<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {
	public function __construct(){
		parent::__construct();
		
		$meta = meta_data();
		foreach($meta as $key => $value){
			$this->data[$key] = $value;
		}
		
		$this->folder_view = 'monitoring_karyawan/';
		$this->folder_model = 'monitoring_karyawan/';
		$this->folder_controller = 'monitoring_karyawan/';
		
		$this->akses = array();
		
		$this->load->helper("cutoff_helper");
		$this->load->helper("tanggal_helper");
		$this->load->helper("karyawan_helper");
		$this->load->helper("reference_helper");
		
		$this->load->model($this->folder_model."M_table_monitoring_karyawan");
		
		$this->data["is_with_sidebar"] = true;
		
		$this->data['judul'] = "Monitoring Karyawan";
		$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		$this->akses = akses_helper($this->data['id_modul']);				
		$this->nama_db = $this->db->database;
		izin($this->akses["akses"]);
	}
	
	public function index(){
		$this->data["akses"] = $this->akses;
		$this->data["navigasi_menu"] = menu_helper();
		$this->data['content'] = $this->folder_view."home";

		$this->data['mst_perizinan'] = $this->db->get('mst_perizinan')->result();
		$this->data['mst_pos'] = $this->db->get('mst_pos')->result();

		# karyawan
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
		$this->db->select('no_pokok,nama');
		if(@$filter['np']){
            $this->db->where('no_pokok',$filter['np']);
        } else if(@$filter['kode_unit']){
            $this->db->where_in('kode_unit',$filter['kode_unit']);
        }
		$this->data['mst_karyawan'] = $this->db->get('mst_karyawan')->result();
		# END: karyawan

		$this->load->view('template',$this->data);
	}

	function get_data(){
		$filter = [];
		$filter['start_date'] = date('Y-m-d', strtotime($this->input->post('start_date')));
		$filter['end_date'] = date('Y-m-d', strtotime($this->input->post('end_date')));
		$filter['np_input'] = $this->input->post('np');
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
		
		$data = $this->M_table_monitoring_karyawan->get_datatables($filter);
		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->M_table_monitoring_karyawan->count_all($filter),
			"recordsFiltered" => $this->M_table_monitoring_karyawan->count_filtered($filter),
			"data" => $data,
		);
		echo json_encode($output);
	}
}