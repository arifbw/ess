<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kehadiran_cuti_bersama extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		$meta = meta_data();
		foreach ($meta as $key => $value) {
			$this->data[$key] = $value;
		}

		$this->dwh = $this->load->database('dwh', true);
		$this->folder_view = 'kehadiran/kehadiran_cuti_bersama/';
		$this->folder_model = 'kehadiran/';
		$this->folder_controller = 'kehadiran/';

		$this->akses = array();

		$this->load->helper("cutoff_helper");
		$this->load->helper("tanggal_helper");
		$this->load->helper("karyawan_helper");
		$this->load->helper("reference_helper");
		$this->load->helper("string");

		$this->data["is_with_sidebar"] = true;

		$this->data['judul'] = "Kehadiran Cuti Bersama";
		$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		$this->akses = akses_helper($this->data['id_modul']);
		izin($this->akses["akses"]);

		$this->load->model('kehadiran/M_tabel_kehadiran_cuti_bersama', 'kehadiran');
	}

	public function index()
	{
		$this->data["akses"] = $this->akses;
		$this->data["navigasi_menu"] = menu_helper();
		$this->data['content'] = $this->folder_view . "index";

		$bulan = $this->kehadiran->bulan_cuti_bersama();
		$this->data['bulan'] = $bulan;

		$this->load->view('template', $this->data);
	}

	function get_data_kehadiran()
	{
		$cek_tabel = $this->kehadiran->cek_tabel($this->input->post('bulan', true));
		if($cek_tabel){
			$list 	= $this->kehadiran->get_datatables();
			$no = $_POST['start'];
	
			foreach ($list as $key => $val) {
				$no++;
				$list[$key]->no = $no;
			}
		} else{
			$list = [];
		}

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $cek_tabel ? $this->kehadiran->count_all() : 0,
			"recordsFiltered" => $cek_tabel ? $this->kehadiran->count_filtered() : 0,
			"data" => $list
		);
		echo json_encode($output);
	}
}
