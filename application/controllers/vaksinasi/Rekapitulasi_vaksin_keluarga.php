<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rekapitulasi_vaksin_keluarga extends CI_Controller {
	public function __construct(){
		parent::__construct();
		
		$meta = meta_data();
		foreach($meta as $key => $value){
			$this->data[$key] = $value;
		}
		
		$this->folder_view = 'vaksinasi/rekapitulasi_vaksin_keluarga/';
		$this->folder_model = 'vaksinasi/rekapitulasi_vaksin_keluarga/';
		$this->folder_controller = 'vaksinasi/';
		
		$this->akses = array();
		
		// $this->load->helper("cutoff_helper");
		// $this->load->helper("tanggal_helper");
		// $this->load->helper("karyawan_helper");
		// $this->load->helper("reference_helper");
					
		// $this->load->model($this->folder_model."M_data_vaksin_keluarga");
		
		$this->data["is_with_sidebar"] = true;
		
		$this->data['judul'] = "Rekapitulasi Vaksin Keluarga";
		$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		$this->akses = akses_helper($this->data['id_modul']);
		$this->nama_db = $this->db->database;
		izin($this->akses["akses"]);
	}
	
	public function index() {
		$this->data["akses"] = $this->akses;
		$this->data["navigasi_menu"] = menu_helper();
		$this->data['content'] = $this->folder_view."index";
		$this->load->view('template',$this->data);
	}

	function get_all_data(){
		$all_data = $this->db->select('ess_kesehatan_keluarga_tertanggung.id, ess_kesehatan_keluarga_tertanggung.np_karyawan, ess_kesehatan_keluarga_tertanggung.nama_karyawan, ess_kesehatan_keluarga_tertanggung.tipe_keluarga, ess_kesehatan_keluarga_tertanggung.tempat_lahir_keluarga, ess_kesehatan_keluarga_tertanggung.tanggal_lahir, ess_kesehatan_keluarga_tertanggung.nama_lengkap, ess_kesehatan_keluarga_tertanggung.	jenis_kelamin, mst_karyawan.kode_unit, mst_karyawan.nama_unit, data_vaksin_keluarga.usia, data_vaksin_keluarga.status_vaksin, data_vaksin_keluarga.created_at, data_vaksin_keluarga.dibatalkan_admin, data_vaksin_keluarga.tanggal_vaksin_1, data_vaksin_keluarga.lokasi_vaksin_1, data_vaksin_keluarga.tanggal_vaksin_2, data_vaksin_keluarga.lokasi_vaksin_2, data_vaksin_keluarga.alasan')
			//zanna 09-08-2021
			//->where('ess_kesehatan_keluarga_tertanggung.status_tanggungan', 'Ditanggung')
			->where("2021 - YEAR(ess_kesehatan_keluarga_tertanggung.tanggal_lahir) >=",12)
			->from('ess_kesehatan_keluarga_tertanggung')
			->join('mst_karyawan', 'mst_karyawan.no_pokok=ess_kesehatan_keluarga_tertanggung.np_karyawan')
			->join('data_vaksin_keluarga', 'data_vaksin_keluarga.np_karyawan=ess_kesehatan_keluarga_tertanggung.np_karyawan AND data_vaksin_keluarga.tipe_keluarga=ess_kesehatan_keluarga_tertanggung.tipe_keluarga AND data_vaksin_keluarga.nama_lengkap=ess_kesehatan_keluarga_tertanggung.nama_lengkap AND data_vaksin_keluarga.tanggal_lahir=ess_kesehatan_keluarga_tertanggung.tanggal_lahir', 'LEFT')
			->order_by('ess_kesehatan_keluarga_tertanggung.np_karyawan')
			->get()->result();
		echo json_encode([
			'status'=>true,
			'message'=>'Data updated at: '. date('Y-m-d H:i:s'),
			'data'=>$all_data
		]);
	}
}