<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Monitoring_sipk extends CI_Controller {
    public function __construct() {
		parent::__construct();

		$meta = meta_data();
		foreach ($meta as $key => $value) {
			$this->data[$key] = $value;
		}

		$this->folder_view = 'perizinan/';
		$this->folder_model = 'perizinan/';
		$this->folder_ajax_view = $this->folder_view . 'ajax/';

		$this->akses = array();

		$this->load->helper("karyawan_helper");
		$this->load->helper("tanggal_helper");
		$this->load->helper("cutoff_helper");
		$this->load->helper("string");
        
		$this->load->model("perizinan/M_data_perizinan");
		$this->load->model($this->folder_model . "/M_tabel_monitoring_sipk");
		$this->data['success'] = "";
		$this->data['warning'] = "";

		$this->data["is_with_sidebar"] = true;
	}

    public function index() {
		$this->data['judul'] = "Monitoring SIPK";
		$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		$this->akses = akses_helper($this->data['id_modul']);

		izin($this->akses["akses"]);

		$this->data["akses"] = $this->akses;
        $this->data['filter_periode'] = $this->M_data_perizinan->get_tabel_perizinan_from_schema()->result();
		$this->data["navigasi_menu"] = menu_helper();
		$this->data['content'] = $this->folder_view . "monitoring_sipk/index";
        
        $this->load->view('template', $this->data);
	}

    public function get_filter_unit() {
        $bulan = $this->input->post('bulan', true);
        $unit = $this->M_tabel_monitoring_sipk->filter_unit($bulan);

		$response = array(
			"status" => true,
			"data" => $unit
		);
		echo json_encode($response);
	}

    public function get_data() {
		$list 	= $this->M_tabel_monitoring_sipk->get_datatables();
		$no = $_POST['start'];

		$i = 0;
		foreach ($list as $key=>$val) {
			$no++;
            $list[$key]->no = $no;
		}

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->M_tabel_monitoring_sipk->count_all(),
			"recordsFiltered" => $this->M_tabel_monitoring_sipk->count_filtered(),
			"data" => $list,
		);
		echo json_encode($output);
	}
}