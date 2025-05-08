<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reporting_spbe extends CI_Controller {
	public function __construct(){
		parent::__construct();
		
		$meta = meta_data();
		foreach($meta as $key => $value){
			$this->data[$key] = $value;
		}
		
		$this->folder_view = 'spbe/';
		$this->folder_model = 'spbe/';
		$this->folder_controller = 'spbe/';
		
		$this->akses = array();
		
		$this->load->helper("cutoff_helper");
		$this->load->helper("tanggal_helper");
		$this->load->helper("karyawan_helper");
		$this->load->helper("reference_helper");
		
		$this->load->model($this->folder_model."M_tabel_reporting_spbe");
		
		$this->data["is_with_sidebar"] = true;
		
		$this->data['judul'] = "Reporting SPBE";
		$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		$this->akses = akses_helper($this->data['id_modul']);				
		$this->nama_db = $this->db->database;
		izin($this->akses["akses"]);
	}
	
	public function index(){
		$this->data["akses"] = $this->akses;
		$this->data["navigasi_menu"] = menu_helper();
		$this->data['content'] = $this->folder_view."reporting_spbe";

		$this->load->view('template',$this->data);
	}

	function get_data_reporting(){
		$filter = [];
		$filter['start_date'] = date('Y-m-d', strtotime($this->input->post('start_date')));
		$filter['end_date'] = date('Y-m-d', strtotime($this->input->post('end_date')));
		
		$data = $this->M_tabel_reporting_spbe->get_datatables($filter);
		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->M_tabel_reporting_spbe->count_all($filter),
			"recordsFiltered" => $this->M_tabel_reporting_spbe->count_filtered($filter),
			"data" => $data,
		);
		echo json_encode($output);
	}
}