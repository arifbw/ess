<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Export_excel extends CI_Controller {
	public function __construct(){
		parent::__construct();
		$this->load->helper("tanggal_helper");
        $this->load->helper("karyawan_helper");
	}

    function generate(){
        $start_date = @$this->input->get('start_date',true) ? date('Y-m-d', strtotime($this->input->get('start_date',true))) : date('Y-m-01');
        $end_date = @$this->input->get('end_date',true) ? date('Y-m-d', strtotime($this->input->get('end_date',true))) : date('Y-m-t');

        $filename = "{$start_date}-sampai-{$end_date}";

        $this->load->library('phpexcel');
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);

        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=Data-Permohonan-Cuti-$filename.xlsx");
        header('Cache-Control: max-age=0');

        $excel = PHPExcel_IOFactory::createReader('Excel2007');
        $excel = $excel->load('./asset/excel-templates-cuti/template-export-cuti.xlsx');

        # proses isi data
        $this->db->select('ess_cuti.*, mst_cuti.uraian');
        $this->db->where('start_date >=', $start_date);
        $this->db->where('start_date <=', $end_date);

        if($_SESSION["grup"]==4) {
            $var = array();
            $list_pengadministrasi = $_SESSION["list_pengadministrasi"];
            foreach ($list_pengadministrasi as $v) {	
                array_push($var,$v['kode_unit']);
            } 
            if($var!=[]) { 
                $this->db->where_in('kode_unit', $var);
            }		
        } else if($_SESSION["grup"]==5) {
            $var = $_SESSION["no_pokok"];
            $this->db->where('np_karyawan', $var);
        }

        $this->db->join('mst_cuti', 'mst_cuti.kode_erp = ess_cuti.absence_type AND mst_cuti.id = ess_cuti.mst_cuti_id', 'LEFT');
        $data = $this->db->get('ess_cuti')->result();
        
        $excel->setActiveSheetIndex(0);
        $kolom 	= 1;
        $awal 	= 6;
        $no = 1;

        $excel->getActiveSheet()->setCellValueExplicit('A3', 'Tanggal: '.tanggal_indonesia($start_date).' sampai '.tanggal_indonesia($end_date), PHPExcel_Cell_DataType::TYPE_STRING);

        foreach($data as $row){
            switch ($row->keterangan) {
                case '1':
                    $keterangan = 'Dalam Kota';
                    break;
                case '2':
                    $keterangan = 'Luar Kota';
                    break;
                default:
                    $keterangan = '';
                    break;
            }
            $excel->getActiveSheet()->setCellValue('A'.$awal, $no);
            $excel->getActiveSheet()->setCellValueExplicit('B'.$awal, $row->np_karyawan, PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('C'.$awal, $row->nama, PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('D'.$awal, $row->uraian, PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('E'.$awal, tanggal_indonesia(date('Y-m-d', strtotime($row->start_date))), PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('F'.$awal, tanggal_indonesia(date('Y-m-d', strtotime($row->end_date))), PHPExcel_Cell_DataType::TYPE_STRING);

            if($row->jumlah_bulan) {		
                $excel->getActiveSheet()->setCellValueExplicit('G'.$awal, "{$row->jumlah_bulan} Bulan {$row->jumlah_hari} Hari");
            } else {
                $excel->getActiveSheet()->setCellValueExplicit('G'.$awal, "{$row->jumlah_hari} Hari");
            }
            
            $excel->getActiveSheet()->setCellValueExplicit('H'.$awal, $row->alasan);
            $excel->getActiveSheet()->setCellValueExplicit('I'.$awal, $keterangan);

            if($row->status_1=='1') {
                $approval_1_nama 	= $row->approval_1." | ".nama_karyawan_by_np($row->approval_1);
                $approval_1_status 	= "Cuti Telah Disetujui pada {$row->approval_1_date}."; 
            } else if($row->status_1=='2') {
                $approval_1_nama 	= $row->approval_1." | ".nama_karyawan_by_np($row->approval_1);
                $approval_1_status 	= "Cuti TIDAK disetujui pada {$row->approval_1_date}."; 
            } else if($row->status_1=='3') {
                $approval_1_nama 	= $row->approval_1." | ".nama_karyawan_by_np($row->approval_1);
                $approval_1_status 	= "Permohonan Cuti Dibatalkan oleh pemohon pada {$row->approval_1_date}."; 
            } else if($row->status_1=='' || $row->status_1=='0') {
                // $status_1 = '0';
                $approval_1_nama 	= $row->approval_1." | ".nama_karyawan_by_np($row->approval_1);
                $approval_1_status 	= "Cuti BELUM disetujui."; 
            }
            
            if($row->status_2=='1') {
                $approval_2_nama 	= $row->approval_2." | ".nama_karyawan_by_np($row->approval_2);
                $approval_2_status 	= "Cuti Telah Disetujui pada {$row->approval_2_date}."; 
            } else if($row->status_2=='2') {
                $approval_2_nama 	= $row->approval_2." | ".nama_karyawan_by_np($row->approval_2);
                $approval_2_status 	= "Cuti TIDAK disetujui pada {$row->approval_2_date}."; 
            } else if($row->status_2=='3') {
                $approval_2_nama 	= $row->approval_2." | ".nama_karyawan_by_np($row->approval_2);
                $approval_2_status 	= "Permohonan Cuti Dibatalkan oleh pemohon pada {$row->approval_2_date}."; 
            } else if($row->status_2=='' || $row->status_2=='0') {
                // $status_2 = '0';
                $approval_2_nama 	= $row->approval_2." | ".nama_karyawan_by_np($row->approval_2);
                $approval_2_status 	= "Cuti BELUM disetujui."; 
            }
                
            $btn_text		= 'Menunggu Persetujuan';
            
            if(($row->status_1=='' || $row->status_1=='0' || $row->status_1 == null) && ($row->status_2!='2' || $row->status_2!='1')) //menunggu atasan 1
            {
                $btn_text		= 'Menunggu Atasan 1';
            }

            if(($row->status_1=='1') && ($row->status_2!='2' || $row->status_2!='1')) //disetujui atasan 1
            {
                if($row->approval_2==null || $row->approval_2=='') //jika tidak ada atasan 2
                {
                    $btn_text		='Disetujui atasan 1';
                } else //jika ada atasan 2
                {
                    $btn_text		='Menunggu Atasan 2';
                }
                
            }
            
            $alasan_tolak = "";
            
            if(($row->status_1=='2') && ($row->status_2!='2' || $row->status_2!='1')) //ditolak atasan 1
            {
                $btn_text		='Ditolak Atasan 1';
                $alasan_tolak	= $row->approval_alasan_1;
            }

            if($row->status_2=='1') //disetujui atasan  2
            {
                $btn_text		='Disetujui Atasan 2';
            }

            if($row->status_2=='2') //ditolak atasan 2
            {
                $btn_text		='Ditolak Atasan 2';
                $alasan_tolak	= $row->approval_alasan_2;
            }
                
            if($row->status_1=='3' || $row->status_2=='3') //dibatalkan
            {
                $btn_text		='Dibatalkan';
            }
            
            if($row->approval_sdm==1)
            {
                $btn_text		='Disetujui Oleh SDM';
            } else if($row->approval_sdm==2)  {
                $btn_text		='Ditolak Oleh SDM';
                $alasan_tolak	= $row->alasan_sdm;
            }
            
            $status_pembatalan_cuti ='';
            $pembatalan_cuti		='';
            $pembatalan_cuti_header	=false;
            $pembatalan_cuti_tampil	='';
            $pem_cut = $this->db->where('np_karyawan', $row->np_karyawan)->where('id_cuti', $row->id)->get('ess_pembatalan_cuti');
            foreach ($pem_cut->result_array() as $pem) 
            {
                if($pembatalan_cuti_header==false)
                {
                    $pembatalan_cuti_tampil="Terdapat Pembatalan Cuti :";
                    $pembatalan_cuti_header=true;
                }
                $pembatalan_cuti_tampil=$pembatalan_cuti_tampil."\n". $pem['date'];			
            }
            
            if($pembatalan_cuti_header==true)
            {
                $status_pembatalan_cuti="\n\n{$pembatalan_cuti_tampil}";
            }

            $excel->getActiveSheet()->setCellValueExplicit('J'.$awal, "{$btn_text}{$status_pembatalan_cuti}{$alasan_tolak}", PHPExcel_Cell_DataType::TYPE_STRING);

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