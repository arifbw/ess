<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Jadwal_operasional extends CI_Controller {
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

        $this->load->model($this->folder_model."M_jadwal_operasional");

        $this->data["is_with_sidebar"] = true;

        $this->data['judul'] = "Jadwal Operasional Kendaraan";
        $this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
        $this->akses = akses_helper($this->data['id_modul']);
        izin($this->akses["akses"]);
    }

    public function index() {
        $this->data["akses"] = $this->akses;
        $this->data["navigasi_menu"] = menu_helper();
        $this->data['content'] = $this->folder_view."jadwal_operasional";

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
        $filename = "Jadwal-Kendaraan-Operasional-Harian-$tgl-$unit.xlsx";
        if(is_file(APPPATH.'../uploads/rekap/transportasi/'.$filename)){
            if($regenerate==1){
                $this->generate_harian($tgl, $unit);
            }
        } else{
            $this->generate_harian($tgl, $unit);
        }
        $link = base_url('./uploads/rekap/transportasi/'.$filename);
        
        $message = 'Jadwal Kendaraan Operasional Harian tanggal <b>'.$tgl.'</b> ';
        if($unit!='semua'){
            $message .= 'Unit Kendaraan '.strtoupper($unit).' ';
        }
        $message .= 'telah dibuat. Klik tombol di bawah ini untuk mendownload';
        //$message = 'Dalam pengembangan...';
        
        $data=[
            'message'=>$message,
            'download_link'=>$link
        ];

        $this->load->view($this->folder_view.'result', $data);
    }

    private function generate_harian($tgl, $unit){
        $filename = "Jadwal-Kendaraan-Operasional-Harian-$tgl-$unit.xlsx";
        $this->load->library('phpexcel'); 

        $tgl_ymd = date('Y-m-d', strtotime($tgl));

        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);

        header("Content-type: application/vnd.ms-excel");
        //nama file
        header("Content-Disposition: attachment; filename=Jadwal-Kendaraan-Operasional-Harian-$tgl-$unit.xlsx");
        header('Cache-Control: max-age=0');

        $excel = PHPExcel_IOFactory::createReader('Excel2007');
        $excel = $excel->load('./asset/excel-templates-bbm/Rekap-Harian-Ploting-Driver.xlsx');

        $get_order = $this->M_jadwal_operasional->get_order_harian($tgl_ymd, $unit);

        $get_log_harga = $this->M_jadwal_operasional->log_harga_bbm($tgl_ymd);

        $excel->setActiveSheetIndex(0);
        $kolom 	= 1;
        $awal 	= 6;
        $no = 1;
        
        if($unit!='semua'){
            $excel->getActiveSheet()->setCellValueExplicit('A1', 'JADWAL KENDARAAN OPERASIONAL HARIAN ANGKUTAN '.strtoupper($unit), PHPExcel_Cell_DataType::TYPE_STRING);
        } else{
            $excel->getActiveSheet()->setCellValueExplicit('A1', 'JADWAL KENDARAAN OPERASIONAL HARIAN', PHPExcel_Cell_DataType::TYPE_STRING);
        }
        $excel->getActiveSheet()->setCellValueExplicit('A2', 'TANGGAL : '.tanggal_format($tgl_ymd), PHPExcel_Cell_DataType::TYPE_STRING);

        foreach($get_order->result() as $row){
            
            # waktu berangkat
            $waktu_berangkat=hari_tanggal($row->tanggal_berangkat).' @'.$row->jam;
            if($row->is_inap==1 || $row->is_pp==1){
                $waktu_berangkat .= ($row->is_inap==1?"\n(Menginap)":"\n(PP");
                if($row->tanggal_awal!=null){
                    $waktu_berangkat .= ': '.hari_tanggal($row->tanggal_awal);
                }
                if($row->tanggal_akhir!=null){
                    $waktu_berangkat .= ' s/d '.hari_tanggal($row->tanggal_akhir);
                }
                $waktu_berangkat .= ')';
            } else{
                $waktu_berangkat .= "\n(Sekali Jalan)";
            }

            $excel->getActiveSheet()->setCellValueExplicit('A'.$awal, $no, PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('B'.$awal, $row->nomor_pemesanan, PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('C'.$awal, explode(' | ',$row->nama_mst_kendaraan)[1], PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('D'.$awal, explode(' | ',$row->nama_mst_kendaraan)[0], PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('E'.$awal, $row->nama_mst_bbm, PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('F'.$awal, $row->nama_mst_driver, PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('G'.$awal, $row->nama_unit_pemesan, PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('H'.$awal, $row->lokasi_jemput."\n(".$row->nama_kota_asal.")", PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('I'.$awal, $row->tujuannya, PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('J'.$awal, $row->nama_pic, PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('K'.$awal, $waktu_berangkat, PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('L'.$awal, $row->jam, PHPExcel_Cell_DataType::TYPE_STRING);

            $no++;
            $awal++;

            $excel->getActiveSheet()->insertNewRowBefore($awal,1);
        }

        $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $objWriter->setIncludeCharts(TRUE);
        $objWriter->setPreCalculateFormulas(TRUE);
        PHPExcel_Calculation::getInstance($excel)->clearCalculationCache();

        //$objWriter->save('php://output');
        $objWriter->save(APPPATH.'../uploads/rekap/transportasi/'.$filename, 'F');
    }
    
    public function tabel_data_pemesanan($tampil_tanggal, $unit) {
			$this->load->model($this->folder_model."/M_tabel_jadwal_operasional");
							
			$list = $this->M_tabel_jadwal_operasional->get_datatables(date('Y-m-d', strtotime($tampil_tanggal)), $unit);
			$data = array();
			$no = $_POST['start'];
			foreach ($list as $tampil) {
				$no++;
                
                # waktu berangkat
                $waktu_berangkat=hari_tanggal($tampil->tanggal_berangkat).' @'.$tampil->jam;
                if($tampil->is_inap==1 || $tampil->is_pp==1){
                    $waktu_berangkat .= ($tampil->is_inap==1?'<br><small>(Menginap)</small>':'<br><small>(PP');
                    if($tampil->tanggal_awal!=null){
                        $waktu_berangkat .= ': '.hari_tanggal($tampil->tanggal_awal);
                    }
                    if($tampil->tanggal_akhir!=null){
                        $waktu_berangkat .= ' s/d '.hari_tanggal($tampil->tanggal_akhir);
                    }
                    $waktu_berangkat .= ')</small>';
                } else{
                    $waktu_berangkat .= '<br><small>(Sekali Jalan)</small>';
                }
                
				$row = array();
				$row[] = $no;
				$row[] = $tampil->nomor_pemesanan;
				$row[] = explode(' | ',$tampil->nama_mst_kendaraan)[1];
				$row[] = explode(' | ',$tampil->nama_mst_kendaraan)[0];
				$row[] = $tampil->nama_mst_bbm;
				$row[] = $tampil->nama_mst_driver;
				$row[] = $tampil->nama_unit_pemesan;
				$row[] = $tampil->lokasi_jemput.'<br><small>('.$tampil->nama_kota_asal.')</small>';
				$row[] = get_pemesanan_tujuan_small($tampil->id);
                $row[] = $waktu_berangkat;
				$row[] = $tampil->nama_pic;
				$row[] = $tampil->jam;
								
				$data[] = $row;
			}

			$output = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->M_tabel_jadwal_operasional->count_all(date('Y-m-d', strtotime($tampil_tanggal)), $unit),
                "recordsFiltered" => $this->M_tabel_jadwal_operasional->count_filtered(date('Y-m-d', strtotime($tampil_tanggal)), $unit),
                "data" => $data,
            );
			//output to json format
			echo json_encode($output);
		}

}

/* End of file data_kehadiran.php */
/* Location: ./application/controllers/kehadiran/data_kehadiran.php */