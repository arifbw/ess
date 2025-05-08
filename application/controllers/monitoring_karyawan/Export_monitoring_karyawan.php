<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Export_monitoring_karyawan extends CI_Controller {
	public function __construct(){
		parent::__construct();
		$this->load->helper("tanggal_helper");
	}

    function generate(){
        $start_date = @$this->input->get('start_date') ? date('Y-m-d', strtotime($this->input->get('start_date'))) : date('Y-m-01');
        $end_date = @$this->input->get('end_date') ? date('Y-m-d', strtotime($this->input->get('end_date'))) : date('Y-m-t');
        $np_input = $this->input->get('np_input') ?: '-';

        $filename = "{$start_date}-sampai-{$end_date}";

        $this->load->library('phpexcel');
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);

        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=Data-Monitoring-Karyawan-$filename.xlsx");
        header('Cache-Control: max-age=0');

        $excel = PHPExcel_IOFactory::createReader('Excel2007');
        $excel = $excel->load('./asset/excel-templates-monitoring-karyawan/template-export.xlsx');

        # proses isi data
        $filter = [];
        switch ($this->session->userdata('grup')) {
			case '4':
				$unit=array();
				$list_pengadministrasi = $this->session->userdata('list_pengadministrasi');
				foreach ($list_pengadministrasi as $i) {	
					array_push($unit,$i['kode_unit']);
				}
				$filter['kode_unit'] = $unit;
				break;
			case '5':
				$filter['np'] = $this->session->userdata('no_pokok');
				break;
			default:
				# code...
				break;
		}

        $this->db->select("a.id, a.np_karyawan, a.nama, a.info_type, a.absence_type, a.kode_pamlek, a.start_date, a.start_time, a.start_date_input, a.end_date, a.end_time, a.end_date_input, a.approval_1_np, a.approval_1_nama, a.approval_1_status, a.approval_1_updated_at, a.approval_2_np, a.approval_2_nama, a.approval_2_status, a.approval_2_updated_at, b.nama AS jenis_izin, a.pos, a.approval_pengamanan_posisi")
            ->from('ess_request_perizinan a')
            ->join('mst_perizinan b',"b.kode_erp=CONCAT(a.info_type,'|',a.absence_type) AND b.kode_pamlek=a.kode_pamlek",'LEFT')
            ->where("DATE_FORMAT(a.created_at,'%Y-%m-%d') >=",$start_date)
            ->where("DATE_FORMAT(a.created_at,'%Y-%m-%d') <=",$end_date)
            ->where("a.date_batal IS NULL",null,false);
        if( $np_input=='-' ){
            if(@$filter['np']){
                $this->db->where('a.np_karyawan',$filter['np']);
            } else if(@$filter['kode_unit']){
                $this->db->where_in('a.kode_unit',$filter['kode_unit']);
            }
		} else{
			$this->db->where('a.np_karyawan',$np_input);
		}
        $this->db->order_by('a.created_at', 'DESC');
        $data = $this->db->get()->result();
        
        $excel->setActiveSheetIndex(0);
        $kolom 	= 1;
        $awal 	= 6;
        $no = 1;

        $excel->getActiveSheet()->setCellValueExplicit('A3', 'Tanggal: '.tanggal_indonesia($start_date).' sampai '.tanggal_indonesia($end_date), PHPExcel_Cell_DataType::TYPE_STRING);

        foreach($data as $row){
            $excel->getActiveSheet()->setCellValue('A'.$awal, $no);
            $excel->getActiveSheet()->setCellValueExplicit('B'.$awal, "{$row->np_karyawan}\n{$row->nama}", PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('C'.$awal, $row->jenis_izin, PHPExcel_Cell_DataType::TYPE_STRING);

            $tanggal = '';
            if( "{$row->info_type}|{$row->absence_type}|{$row->kode_pamlek}"!="2001|5000|0" ){
                if( $row->start_date!=null ){
                    $tanggal .= "{$row->start_date} {$row->start_time} \n s/d \n";
                } else if( $row->start_date_input!=null ){
                    $tanggal .= "{$row->start_date_input} \n s/d \n";
                }
            }
            $tanggal .= ($row->end_date!=null ? "{$row->end_date} {$row->end_time}" : $row->end_date_input);
            $excel->getActiveSheet()->setCellValueExplicit('D'.$awal, $tanggal, PHPExcel_Cell_DataType::TYPE_STRING);

            # atasan 1
            $excel->getActiveSheet()->setCellValueExplicit('E'.$awal, "{$row->approval_1_np}\n{$row->approval_1_nama}", PHPExcel_Cell_DataType::TYPE_STRING);
            switch ($row->approval_1_status) {
                case '1':
                    $approval_1_status = 'Disetujui Atasan 1';
                    break;
                case '2':
                    $approval_1_status = 'Ditolak Atasan 1';
                    break;
                default:
                    $approval_1_status = 'Menunggu Persetujuan';
                    break;
            }
            $excel->getActiveSheet()->setCellValueExplicit('F'.$awal, $approval_1_status, PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('G'.$awal, $row->approval_1_updated_at, PHPExcel_Cell_DataType::TYPE_STRING);

            # atasan 2
            if( $row->approval_2_np!=null ){
                $excel->getActiveSheet()->setCellValueExplicit('H'.$awal, "{$row->approval_2_np}\n{$row->approval_2_nama}", PHPExcel_Cell_DataType::TYPE_STRING);
                switch ($row->approval_2_status) {
                    case '1':
                        $approval_2_status = 'Disetujui Atasan 2';
                        break;
                    case '2':
                        $approval_2_status = 'Ditolak Atasan 2';
                        break;
                    default:
                        $approval_2_status = 'Menunggu Persetujuan';
                        break;
                }
                $excel->getActiveSheet()->setCellValueExplicit('I'.$awal, $approval_2_status, PHPExcel_Cell_DataType::TYPE_STRING);
                $excel->getActiveSheet()->setCellValueExplicit('J'.$awal, $row->approval_2_updated_at, PHPExcel_Cell_DataType::TYPE_STRING);
            }

            # pos
            if( !in_array($row->pos,[null,'null']) ){
                $pos = '';
                $array_pos = json_decode($row->pos);
                $filter = $this->db->select('nama')->where_in('id',$array_pos)->get('mst_pos')->result();
                foreach ($filter as $key) {
                    $pos .= "{$key->nama};\n";
                }
                $excel->getActiveSheet()->setCellValueExplicit('K'.$awal, $pos, PHPExcel_Cell_DataType::TYPE_STRING);
            }

            # pengamanan
            if( !in_array($row->approval_pengamanan_posisi,[null,'null']) ){
                $posisi = '';
                $array_posisi = json_decode($row->approval_pengamanan_posisi);
                foreach ($array_posisi as $key) {
                    if( $key->status=='1' ){
                        $a = $key->nama_approver?:$key->np_approver;
                        $posisi .= "{$key->nama_pos} | Oleh {$a} | {$key->posisi} pada {$key->waktu};\n\n";
                    }
                }
                $excel->getActiveSheet()->setCellValueExplicit('L'.$awal, $posisi, PHPExcel_Cell_DataType::TYPE_STRING);
            }

            $no++;
            $awal++;
        }
        # END: proses isi data

        $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $objWriter->setIncludeCharts(TRUE);
        $objWriter->setPreCalculateFormulas(TRUE);
        PHPExcel_Calculation::getInstance($excel)->clearCalculationCache();

        $objWriter->save('php://output');
	}
}