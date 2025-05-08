<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Laporan_bulanan extends CI_Controller {
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

        $this->load->model($this->folder_model."M_laporan_bulanan");

        $this->data["is_with_sidebar"] = true;

        $this->data['judul'] = "Laporan Bulanan";
        $this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
        $this->akses = akses_helper($this->data['id_modul']);

        izin($this->akses["akses"]);
    }

    public function index() {
        $this->data["akses"] = $this->akses;
        $this->data["navigasi_menu"] = menu_helper();
        $this->data['content'] = $this->folder_view."laporan_bulanan";

        //ambil tahun bulan tabel yang tersedia
        $array_tahun_bulan = array();

        $nama_db = $this->db->database;

        $get_month = $this->db->select('tanggal_berangkat')->group_by('YEAR(tanggal_berangkat), MONTH(tanggal_berangkat)')->order_by('YEAR(tanggal_berangkat) DESC, MONTH(tanggal_berangkat) DESC')->get($nama_db.'.ess_pemesanan_kendaraan')->result();

        foreach($get_month as $row){
            $array_tahun_bulan[] = date('m-Y', strtotime($row->tanggal_berangkat));
        }

        $this->data['array_tahun_bulan'] 		= $array_tahun_bulan;
        //echo json_encode($this->data['array_tahun_bulan']);exit();
        $this->load->view('template',$this->data);	
    }

    function proses($_bln, $unit, $regenerate=null){
        $bln = date('m-Y', strtotime($_bln));
        $filename = "Rekap-Biaya-Perjalanan-Bulan-$bln-$unit.xlsx";
        if(is_file(APPPATH.'../uploads/rekap/transportasi/'.$filename)){
            if($regenerate==1){
                $this->generate_bulanan($_bln, $unit);
            }
        } else{
            $this->generate_bulanan($_bln, $unit);
        }
        $link = base_url('./uploads/rekap/transportasi/'.$filename);

        $data=[
            'message'=>'Laporan bulan <b>'.$bln.'</b> telah dibuat. Klik tombol di bawah ini untuk mendownload',
            'download_link'=>$link
        ];

        $this->load->view($this->folder_view.'result', $data);
    }

    private function generate_bulanan($_bln, $unit){
        $bln = date('m-Y', strtotime($_bln));
        $filename = "Rekap-Biaya-Perjalanan-Bulan-$bln-$unit.xlsx";

        $this->load->library('phpexcel'); 

        $bln_ym = date('Y-m', strtotime($_bln));

        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);

        header("Content-type: application/vnd.ms-excel");
        //nama file
        header("Content-Disposition: attachment; filename=Rekap-Biaya-Perjalanan-Bulan-$bln-$unit.xlsx");
        header('Cache-Control: max-age=0');

        $excel = PHPExcel_IOFactory::createReader('Excel2007');
        $excel = $excel->load('./asset/excel-templates-bbm/Rekap-Biaya-Perjalanan-Bulan-D.xlsx');

        $excel->setActiveSheetIndex(0);
        if($unit!='semua'){
            $excel->getActiveSheet()->setCellValueExplicit('A1', 'REKAP PEMAKAIAN KENDARAAN DEPARTEMENARTMENT (UNIT KENDARAAN '.strtoupper($unit).')', PHPExcel_Cell_DataType::TYPE_STRING);
        }
        $excel->getActiveSheet()->setCellValue('C3', 'Bulan: '.date_ym_to_bulan($bln_ym));
        //$excel->getActiveSheet()->setCellValue('S19', '=SUM(S5:S15)');

        $tb_sto = check_tb_tahun('erp_master_data', date('Y_m', strtotime($_bln)));
        $get_sto = $this->M_laporan_bulanan->get_sto();

        $awal_dir = 5;
        $awal_div = 8;
        $awal_dep = 11;
        $no = 1;

        foreach($get_sto->result() as $row){
            switch($row->levell){
                case "1":
                    $awal_fix = $awal_dir;
                    break;
                case "2":
                    $awal_fix = $awal_div;
                    break;
                case "3":
                    $awal_fix = $awal_dep;
                    break;
            }
            $excel->getActiveSheet()->setCellValue('A'.$awal_fix, $no);
            $excel->getActiveSheet()->setCellValue('B'.$awal_fix, $row->nama_unit_singkat);

            $date_awal = date('Y-m-d', strtotime($_bln));
            $date_akhir = date('Y-m-t', strtotime($_bln));

            $awal_tgl = 2;
            while($date_awal <= $date_akhir){
                $get_harian = $this->M_laporan_bulanan->get_order_harian($row->kode_unit, $date_awal, $unit)->row();
                $excel->getActiveSheet()->setCellValueByColumnAndRow($awal_tgl, $awal_fix, @$get_harian->sum_biaya_total);

                $date_awal = date('Y-m-d',strtotime($date_awal . "+1 days"));
                $awal_tgl++;
            }

            $excel->getActiveSheet()->insertNewRowBefore(($awal_fix+1),1);
            $excel->getActiveSheet()->setCellValue('AH'.($awal_fix+1), '=SUM(C'.($awal_fix+1).':AG'.($awal_fix+1).')');

            $awal_dir++;
            $awal_div++;
            $awal_dep++;
            $no++;
        }

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