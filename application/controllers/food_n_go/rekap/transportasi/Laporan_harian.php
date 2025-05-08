<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Laporan_harian extends CI_Controller {
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

        $this->load->model($this->folder_model."M_laporan_harian");

        $this->data["is_with_sidebar"] = true;

        $this->data['judul'] = "Laporan Harian";
        $this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
        $this->akses = akses_helper($this->data['id_modul']);

        izin($this->akses["akses"]);
    }

    public function index() {
        $this->data["akses"] = $this->akses;
        $this->data["navigasi_menu"] = menu_helper();
        $this->data['content'] = $this->folder_view."laporan_harian";

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

    function proses($tgl, $unit, $regenerate=null){
        $filename = "Rekap-Biaya-Perjalanan-$tgl-$unit.xlsx";
        if(is_file(APPPATH.'../uploads/rekap/transportasi/'.$filename)){
            if($regenerate==1){
                $this->generate_harian($tgl, $unit);
            }
        } else{
            $this->generate_harian($tgl, $unit);
        }
        $link = base_url('./uploads/rekap/transportasi/'.$filename);
        
        $message = 'Laporan tanggal <b>'.$tgl.'</b> ';
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

    private function generate_harian($tgl, $unit){
        $filename = "Rekap-Biaya-Perjalanan-$tgl-$unit.xlsx";
        $this->load->library('phpexcel'); 

        $tgl_ymd = date('Y-m-d', strtotime($tgl));

        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);

        header("Content-type: application/vnd.ms-excel");
        //nama file
        header("Content-Disposition: attachment; filename=Rekap-Biaya-Perjalanan-$tgl-$unit.xlsx");
        header('Cache-Control: max-age=0');

        $excel = PHPExcel_IOFactory::createReader('Excel2007');
        $excel = $excel->load('./asset/excel-templates-bbm/Rekap-Biaya-Perjalanan-D.xlsx');

        $get_order = $this->M_laporan_harian->get_order_harian($tgl_ymd, $unit);

        $get_log_harga = $this->M_laporan_harian->log_harga_bbm($tgl_ymd);

        $excel->setActiveSheetIndex(0);
        $kolom 	= 1;
        $awal 	= 8;
        $no = 1;
        $first = $awal;

        # biaya
        $total_harga_bbm=0;
        $total_biaya_tol_parkir=0;
        $total_biaya_lainnya=0;
        $total_akhir=0;
        
        if($unit!='semua'){
            $excel->getActiveSheet()->setCellValueExplicit('A1', 'RINCIAN PEMAKAIAN BBM KENDARAAN DINAS PERUM PERURI '.strtoupper($unit), PHPExcel_Cell_DataType::TYPE_STRING);
        }
        $excel->getActiveSheet()->setCellValueExplicit('A2', 'TANGGAL : '.tanggal_format($tgl_ymd), PHPExcel_Cell_DataType::TYPE_STRING);

        foreach($get_order->result() as $row){
            $total_harga_bbm += ($row->total_harga_bbm!=null ? $row->total_harga_bbm:0);
            $total_biaya_tol_parkir += ( ($row->biaya_tol!=null ? $row->biaya_tol:0) + ($row->biaya_parkir!=null ? $row->biaya_parkir:0) );
            $total_biaya_lainnya += ($row->biaya_lainnya!=null ? $row->biaya_lainnya:0);

            $total_akhir += ($row->biaya_total!=null ? $row->biaya_total:0);

            $excel->getActiveSheet()->setCellValueExplicit('A'.$awal, $no, PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('B'.$awal, explode(' | ',$row->nama_mst_kendaraan)[0], PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('C'.$awal, explode(' - ',$row->nama_mst_driver)[1], PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('D'.$awal, $row->nama_mst_bbm, PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValue('E'.$awal, (($row->jumlah_liter_bbm!=null ? $row->jumlah_liter_bbm:0) + 0));
            $excel->getActiveSheet()->setCellValue('F'.$awal, ($row->total_harga_bbm!=null ? $row->total_harga_bbm:0));
            $excel->getActiveSheet()->setCellValue('G'.$awal, ($row->biaya_tol!=null ? $row->biaya_tol:0));
            $excel->getActiveSheet()->setCellValue('H'.$awal, ($row->biaya_parkir!=null ? $row->biaya_parkir:0));
            $excel->getActiveSheet()->setCellValue('I'.$awal, ($row->biaya_lainnya!=null ? $row->biaya_lainnya:0));
            //$excel->getActiveSheet()->setCellValue('J'.$awal, ($row->biaya_total!=null ? $row->biaya_total:0));
            $excel->getActiveSheet()->setCellValue('J'.$awal, '=SUM(F'.$awal.':I'.$awal.')');
            $excel->getActiveSheet()->setCellValueExplicit('K'.$awal, $row->nama_unit_pemesan, PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('L'.$awal, $row->tujuannya, PHPExcel_Cell_DataType::TYPE_STRING);

            $no++;
            $awal++;

            $excel->getActiveSheet()->insertNewRowBefore($awal,1);
        }
        
        //$excel->getActiveSheet()->setCellValue('F'.($awal+1), $total_harga_bbm);
        $excel->getActiveSheet()->setCellValue('F'.($awal+1), '=SUM(F'.$first.':F'.$awal.')');
        //$excel->getActiveSheet()->setCellValue('F'.($awal+2), $total_biaya_tol_parkir);
        $excel->getActiveSheet()->setCellValue('F'.($awal+2), '=SUM(G'.$first.':H'.$awal.')');
        //$excel->getActiveSheet()->setCellValue('F'.($awal+3), $total_biaya_lainnya);
        $excel->getActiveSheet()->setCellValue('F'.($awal+3), '=SUM(I'.$first.':I'.$awal.')');
        //$excel->getActiveSheet()->setCellValue('F'.($awal+4), $total_akhir);
        $excel->getActiveSheet()->setCellValue('F'.($awal+4), '=SUM(F'.($awal+1).':F'.($awal+3).')');

        $excel->getActiveSheet()->setCellValueExplicit('J'.($awal+6), tanggal_format($tgl_ymd), PHPExcel_Cell_DataType::TYPE_STRING);
		
		
		//21 01 2022 Tri Wibowo 7648 Tambah ambil dari database terkait penandatangan
		$ambil_ttd = $this->db->query("SELECT * FROM mst_ttd_kendaraan WHERE type='kanan' AND status='1'")->row_array();
		
		//penandatangan unit
		 $excel->getActiveSheet()->setCellValueExplicit('J'.($awal+7),  $ambil_ttd['nama_unit'], PHPExcel_Cell_DataType::TYPE_STRING);
		//penandatangan nama
		$excel->getActiveSheet()->setCellValueExplicit('J'.($awal+11), $ambil_ttd['nama'], PHPExcel_Cell_DataType::TYPE_STRING);
		//penandatangan jabatan
		$excel->getActiveSheet()->setCellValueExplicit('J'.($awal+12),  $ambil_ttd['jabatan'], PHPExcel_Cell_DataType::TYPE_STRING);
		
        # catatan harga bbm
        $last_date = '';
        $date_fix = '2020-04-01';
        $row_first_harga=$awal+7;
        foreach($get_log_harga->result() as $row){
            $date_result = ($row->log!=null ? explode('|',$row->log)[1] : explode('|',$row->mst)[1]);
            $excel->getActiveSheet()->setCellValueExplicit('A'.$row_first_harga, $row->nama, PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValue('C'.$row_first_harga, ($row->log!=null ? explode('|',$row->log)[0] : explode('|',$row->mst)[0]));
            $row_first_harga++;

            if($date_result > $date_fix){
                $date_fix = date('Y-m-d', strtotime($date_result));
            }
        }

        $excel->getActiveSheet()->setCellValueExplicit('A'.($row_first_harga+2), '# Perubahan harga berlaku mulai '.tanggal_format($date_fix), PHPExcel_Cell_DataType::TYPE_STRING);

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