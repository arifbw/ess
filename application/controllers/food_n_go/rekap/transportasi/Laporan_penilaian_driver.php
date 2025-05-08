<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Laporan_penilaian_driver extends CI_Controller {
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

        $this->load->model($this->folder_model."M_laporan_penilaian");

        $this->data["is_with_sidebar"] = true;

        $this->data['judul'] = "Laporan Penilaian Driver";
        $this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
        $this->akses = akses_helper($this->data['id_modul']);

        izin($this->akses["akses"]);
    }

    public function index() {
        $this->data["akses"] = $this->akses;
        $this->data["navigasi_menu"] = menu_helper();
        $this->data['content'] = $this->folder_view."laporan_penilaian_driver";
        
        $this->load->view('template',$this->data);	
    }

    function proses($_bln, $unit, $regenerate=null){
        $bln = date('m-Y', strtotime($_bln));
        $filename = "Laporan-Penilaian-Driver-Bulan-$bln-$unit.xlsx";
        if(is_file(APPPATH.'../uploads/rekap/transportasi/'.$filename)){
            if($regenerate==1){
                $this->generate_bulanan($_bln, $unit);
            }
        } else{
            $this->generate_bulanan($_bln, $unit);
        }
        $link = base_url('./uploads/rekap/transportasi/'.$filename);

        $data=[
            'message'=>'Laporan Penilaian Driver bulan <b>'.$bln.'</b> telah dibuat. Klik tombol di bawah ini untuk mendownload',
            'download_link'=>$link
        ];

        $this->load->view($this->folder_view.'result', $data);
    }

    private function generate_bulanan($_bln, $unit){
        $bln = date('m-Y', strtotime($_bln));
        $filename = "Laporan-Penilaian-Driver-Bulan-$bln-$unit.xlsx";

        $this->load->library('phpexcel'); 

        $bln_ym = date('Y-m', strtotime($_bln));

        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);

        header("Content-type: application/vnd.ms-excel");
        //nama file
        header("Content-Disposition: attachment; filename=Laporan-Penilaian-Driver-Bulan-$bln-$unit.xlsx");
        header('Cache-Control: max-age=0');

        $excel = PHPExcel_IOFactory::createReader('Excel2007');
        $excel = $excel->load('./asset/excel-templates-bbm/Laporan-Penilaian-Format-Baru-2020-driver.xlsx');
        
        $excel->setActiveSheetIndex(1);
        if($unit!='semua'){
            $excel->getActiveSheet()->setCellValueExplicit('A2', 'PENGEMUDI '.strtoupper($unit).' SIM A DAM SIM B1', PHPExcel_Cell_DataType::TYPE_STRING);
        } else{
            $excel->getActiveSheet()->setCellValueExplicit('A2', 'PENGEMUDI SIM A DAM SIM B1', PHPExcel_Cell_DataType::TYPE_STRING);
        }
        $excel->getActiveSheet()->setCellValue('A3', 'PERIODE: '.date_ym_to_bulan($bln_ym));
        
        $get_nilai = $this->M_laporan_penilaian->get_nilai($bln_ym,$unit);

        $awal = 8;
        $no = 1;

        foreach($get_nilai->result() as $row){
            $excel->getActiveSheet()->insertNewRowBefore($awal,1);
            $excel->getActiveSheet()->setCellValue('A'.$awal, $no);
            $excel->getActiveSheet()->setCellValue('B'.$awal, $row->nama_mst_driver);
            $excel->getActiveSheet()->setCellValue('C'.$awal, $row->jenis_sim);
            $excel->getActiveSheet()->setCellValue('D'.$awal, $row->nilai_akhir);

            $awal++;
            $no++;
        }
        
        if($unit!='semua'){
            $excel->getActiveSheet()->setCellValue('D'.($awal+2), strtoupper($unit).', '.tanggal_format_noday(date('Y-m-d')));
        } else{
            $excel->getActiveSheet()->setCellValue('D'.($awal+2), tanggal_format_noday(date('Y-m-d')));
        }
        

        $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $objWriter->setIncludeCharts(TRUE);
        $objWriter->setPreCalculateFormulas(TRUE);
        PHPExcel_Calculation::getInstance($excel)->clearCalculationCache();

        //$objWriter->save('php://output');
        $objWriter->save(APPPATH.'../uploads/rekap/transportasi/'.$filename, 'F');
    }

}