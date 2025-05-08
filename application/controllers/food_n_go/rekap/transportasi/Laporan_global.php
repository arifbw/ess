<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Laporan_global extends CI_Controller {
    public function __construct(){
        parent::__construct();

        $meta = meta_data();
        foreach($meta as $key => $value){
            $this->data[$key] = $value;
        }

        $this->folder_view = 'food_n_go/rekap/transportasi/';
        $this->folder_model = 'kendaraan/rekap_transportasi/';
        $this->folder_controller = 'food_n_go/rekap/transportasi/';

        $this->akses = array();

        $this->load->helper("tanggal_helper");
        $this->load->helper('form');
        $this->load->helper('kendaraan');

        $this->load->model($this->folder_model."M_laporan_global");

        $this->data["is_with_sidebar"] = true;

        $this->data['judul'] = "Laporan Global";
        $this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
        $this->akses = akses_helper($this->data['id_modul']);

        izin($this->akses["akses"]);

        //$this->output->enable_profiler(true);
    }

    public function index() {
        $this->data["akses"] = $this->akses;
        $this->data["navigasi_menu"] = menu_helper();
        $this->data['content'] = $this->folder_view."laporan_global";

        //$tahun = $this->M_laporan_global->tahun()->result();

        //$this->data['tahun'] = $tahun;

        $this->load->view('template',$this->data);	
    }

    function proses($tahun, $unit, $regenerate=null){
        $filename = "Rekap-Biaya-Perjalanan-Global-$tahun-$unit.xlsx";
        if(is_file(APPPATH.'../uploads/rekap/transportasi/'.$filename)){
            if($regenerate==1){
                $this->generate_global($tahun, $unit);
            }
        } else{
            $this->generate_global($tahun, $unit);
        }
        $link = base_url('./uploads/rekap/transportasi/'.$filename);
        
        $message = 'Laporan tahun <b>'.$tahun.'</b> ';
        if($unit!='semua'){
            $message .= 'Unit Kendaraan '.strtoupper($unit).' ';
        }
        $message .= 'telah dibuat. Klik tombol di bawah ini untuk mendownload';
        
        $data=[
            'message'=>$message,
            'download_link'=>$link
        ];

        $this->load->view($this->folder_view.'result', $data);
    }

    private function generate_global($tahun, $unit){
        $filename = "Rekap-Biaya-Perjalanan-Global-$tahun-$unit.xlsx";
        $this->load->library('phpexcel'); 

        $excel = PHPExcel_IOFactory::createReader('Excel2007');
        $excel = $excel->load('./asset/excel-templates-bbm/Rekap-Biaya-Perjalanan-Global-D.xlsx');

        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);

        header("Content-type: application/vnd.ms-excel");
        //nama file
        header("Content-Disposition: attachment; filename=Rekap-Biaya-Perjalanan-Global-$tahun-$unit.xlsx");
        header('Cache-Control: max-age=0');

        $excel->setActiveSheetIndex(0);
        if($unit!='semua'){
            $excel->getActiveSheet()->setCellValueExplicit('A2', 'KENDARAAN OPERASIONAL ANGKUTAN PERURI '.strtoupper($unit), PHPExcel_Cell_DataType::TYPE_STRING);
        }
        $excel->getActiveSheet()->setCellValueExplicit('A3', 'PERIODE : '.$tahun, PHPExcel_Cell_DataType::TYPE_STRING);

        $bbm_used = $this->M_laporan_global->bbm_used();


        $column_bbm = 2;
        $column_tol = 3;
        $column_parkir = 4;
        $column_lainnya = 5;
        $column_total = 6;
        if($bbm_used->num_rows()>0){
            $bbm_column = 'B';
            foreach($bbm_used->result() as $row){
                $bbm_column++;

                $column_bbm++;
                $column_tol++;
                $column_parkir++;
                $column_lainnya++;
                $column_total++;

                $excel->getActiveSheet()->insertNewColumnBefore($bbm_column, 1);
                $excel->getActiveSheet()->setCellValueExplicit($bbm_column.'7', $row->nama_mst_bbm, PHPExcel_Cell_DataType::TYPE_STRING);

                $bbm_column++;

                $column_bbm++;
                $column_tol++;
                $column_parkir++;
                $column_lainnya++;
                $column_total++;

                $excel->getActiveSheet()->insertNewColumnBefore($bbm_column, 1);
                $excel->getActiveSheet()->setCellValueExplicit($bbm_column.'7', 'Jumlah (Rp)', PHPExcel_Cell_DataType::TYPE_STRING);
            }
            $excel->getActiveSheet()->mergeCells('C6:'.($bbm_column).'6');
            $excel->getActiveSheet()->setCellValueExplicit('C6', 'BBM', PHPExcel_Cell_DataType::TYPE_STRING);
        }

        $awal 	= 10;
        $no = 1;

        $get_order_global = $this->M_laporan_global->get_order_global($tahun, $unit);
        foreach($get_order_global->result() as $row){
            $excel->getActiveSheet()->setCellValueExplicit('A'.$awal, $no, PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('B'.$awal, $row->tanggal_berangkat, PHPExcel_Cell_DataType::TYPE_STRING);

            # bbm
            if($bbm_used->num_rows()>0){
                $bbm_column = 1;
                $_harga_bbm=0;
                foreach($bbm_used->result() as $r){
                    $get_bbm = $this->M_laporan_global->get_bbm([$row->tanggal_berangkat, $r->id_mst_bbm])->row();

                    $bbm_column++;
                    $excel->getActiveSheet()->setCellValueExplicitByColumnAndRow($bbm_column, $awal, (@$get_bbm->sum_liter!=null?($get_bbm->sum_liter+0):0) );

                    $bbm_column++;
                    $excel->getActiveSheet()->setCellValueExplicitByColumnAndRow($bbm_column, $awal, (@$get_bbm->sum_harga_bbm!=null?($get_bbm->sum_harga_bbm+0):0) );
                    $_harga_bbm+=(@$get_bbm->sum_harga_bbm!=null?($get_bbm->sum_harga_bbm+0):0);
                }
                $bbm_column++;
                $excel->getActiveSheet()->setCellValueByColumnAndRow($bbm_column, $awal, $_harga_bbm);
            }

            $excel->getActiveSheet()->setCellValueByColumnAndRow($column_tol, $awal, $row->total_biaya_tol);
            $excel->getActiveSheet()->setCellValueByColumnAndRow($column_parkir, $awal, $row->total_biaya_parkir);
            $excel->getActiveSheet()->setCellValueByColumnAndRow($column_lainnya, $awal, $row->total_biaya_lainnya);
            $excel->getActiveSheet()->setCellValueByColumnAndRow($column_total, $awal, ($row->total_biaya_tol + $row->total_biaya_parkir + $row->total_biaya_lainnya));

            $no++;
            $awal++;

            $excel->getActiveSheet()->insertNewRowBefore($awal,1);
        }

        $excel->getActiveSheet()->setCellValueByColumnAndRow($column_bbm, ($awal+9), ($unit!='semua'?$unit.', ':'').tanggal_indonesia(date('Y-m-d')) );
		
		//21 01 2022 Tri Wibowo 7648 Tambah ambil dari database terkait penandatangan KANAN
		$ambil_ttd = $this->db->query("SELECT * FROM mst_ttd_kendaraan WHERE type='kanan' AND status='1'")->row_array();
		
		//penandatangan unit
		 $excel->getActiveSheet()->setCellValueExplicit('S'.($awal+10),  $ambil_ttd['nama_unit'], PHPExcel_Cell_DataType::TYPE_STRING);
		//penandatangan nama
		$excel->getActiveSheet()->setCellValueExplicit('P'.($awal+14), $ambil_ttd['nama'], PHPExcel_Cell_DataType::TYPE_STRING);
		//penandatangan jabatan
		$excel->getActiveSheet()->setCellValueExplicit('P'.($awal+15),  $ambil_ttd['jabatan'], PHPExcel_Cell_DataType::TYPE_STRING);
		
		//21 01 2022 Tri Wibowo 7648 Tambah ambil dari database terkait penandatangan KIRI
		$ambil_ttd = $this->db->query("SELECT * FROM mst_ttd_kendaraan WHERE type='kiri' AND status='1'")->row_array();
		
		//penandatangan unit
		 $excel->getActiveSheet()->setCellValueExplicit('B'.($awal+10),  $ambil_ttd['nama_unit'], PHPExcel_Cell_DataType::TYPE_STRING);
		//penandatangan nama
		$excel->getActiveSheet()->setCellValueExplicit('B'.($awal+14), $ambil_ttd['nama'], PHPExcel_Cell_DataType::TYPE_STRING);
		//penandatangan jabatan
		$excel->getActiveSheet()->setCellValueExplicit('B'.($awal+15),  $ambil_ttd['jabatan'], PHPExcel_Cell_DataType::TYPE_STRING);

        $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');

        $objWriter->setIncludeCharts(TRUE);
        $objWriter->setPreCalculateFormulas(TRUE);
        PHPExcel_Calculation::getInstance($excel)->clearCalculationCache();

        //$objWriter->save('php://output');
        $objWriter->save(APPPATH.'../uploads/rekap/transportasi/'.$filename, 'F');
    }

}

/* End of file data_kehadiran.php */
/* Location: ./application/controllers/kehadiran/data_kehadiran.php */