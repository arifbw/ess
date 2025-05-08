<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Monitoring extends CI_Controller
{

	function __construct()
	{
		parent::__construct();

		$meta = meta_data();

		foreach ($meta as $key => $value) {
			$this->data[$key] = $value;
		}

		$this->folder_view = 'ijt/monitoring/';
		$this->folder_model = 'ijt/';
		$this->folder_ajax_view = $this->folder_view . 'ajax/';
		$this->akses = array(/* 'scan'=>true,'ubah'=>true,'tambah'=>true,'lihat'=>true */);

		$this->load->helper("tanggal_helper");

		$this->load->model($this->folder_model . "/M_monitoring");
		$this->load->model("poin_reward/m_manajemen_poin", "poin");

		$this->data['success'] = "";
		$this->data['warning'] = "";

		$this->data["is_with_sidebar"] = true;

		$this->data['judul'] = "Monitoring";
		$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		$this->akses = akses_helper($this->data['id_modul']);
		izin($this->akses["akses"]);
	}

	public function index()
	{
		if (@$this->input->get('kegiatan')) {
			$this->data['kegiatan_name'] = urldecode($this->input->get('kegiatan'));
		}
		if (@$this->input->get('kegiatan_id')) {
			$this->data['kegiatan_id'] = $this->input->get('kegiatan_id');
		}

		$this->data["akses"] = $this->akses;
		$this->data["navigasi_menu"] = menu_helper();
		$this->data['content'] = $this->folder_view . "index";
		$this->data['np'] = $this->session->userdata("no_pokok");

		if($this->session->userdata('grup')=='5'){
			$kode_jabatan = $this->session->userdata("kode_jabatan");
			if(substr($kode_jabatan, -3)!='300'){
				redirect(base_url());
				exit;
			}
		}

		if ($this->akses["lihat"]) {
			$js_header_script = "
							<script>
								var config = {
									route:'" . base_url() . "'
								}
							</script>
							<script src='" . base_url('asset/js/agenda') . "/datatable.serverside.js'></script>
							<script src='" . base_url('asset/datatables/js') . "/reload.js'></script>
							<script src='" . base_url('asset/select2') . "/select2.min.js'></script>
							<script src='" . base_url('asset/sweetalert2') . "/sweetalert2.js'></script>
							<script>
								$(document).ready(function() {
									$('.select2').select2();
								});
							</script>";

			array_push($this->data["js_header_script"], $js_header_script);

			$this->data["daftar_lokasi"] = $this->M_agenda->daftar_lokasi();
			$this->data['akses'] = $this->akses['persetujuan'];
			
			$log = array(
				"id_pengguna" => $this->session->userdata("id_pengguna"),
				"id_modul" => $this->data['id_modul'],
				"deskripsi" => "lihat " . strtolower(preg_replace("/_/", " ", __CLASS__)),
				"alamat_ip" => $this->data["ip_address"],
				"waktu" => date("Y-m-d H:i:s")
			);
			$this->m_log->tambah($log);
		}

		$this->load->view('template', $this->data);
	}

	public function data()
	{
		$data = $this->M_monitoring->get_datatables();
		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->M_monitoring->count_all(),
			"recordsFiltered" => $this->M_monitoring->count_filtered(),
			"data" => $data,
		);

		header('Content-Type: application/json');
		header("Access-Control-Allow-Origin: *");
		//output dalam format JSON
		echo json_encode($output);
	}

	function action_verval()
	{
		$this->load->library('form_validation');

		if (!$this->input->post('jenis_verval') == 'keterangan') {
			$this->form_validation->set_rules('is_verval', 'Status Verifikasi', 'required');
		}
		$this->form_validation->set_rules('keterangan', 'Keterangan', 'required');

		if ($this->form_validation->run() == FALSE) {
			echo json_encode([
				'status' => 'error',
				'message' => validation_errors()
			]);
			return;
		}

		$apply_id = $this->input->post('apply_id');
		$is_verval = $this->input->post('is_verval');
		$keterangan = $this->input->post('keterangan');
		$jenis_verval = $this->input->post('jenis_verval');

		$this->db->trans_begin();

		try {
			$data = [
				'apply_id' => $apply_id,
				'is_verval' => $is_verval,
				'keterangan' => $keterangan,
				'jenis_verval' => $jenis_verval,
				'verif_by' => $this->session->userdata('no_pokok')
			];

			$existing = $this->db->get_where('ijt_verval', ['apply_id' => $apply_id, 'jenis_verval' => 'keterangan'])->row_array();

			if($existing) {
				$this->db->where(['apply_id' => $apply_id, 'jenis_verval' => 'keterangan']);
				$update = $this->db->update('ijt_verval', $data);
			} else {				
				$update = $this->M_monitoring->insertVerval($data);
			}

			if ($update) {
				$this->db->trans_commit();
				echo json_encode(['status' => 'success', 'message' => 'Berhasil melakukan verifikasi']);
			} else {
				throw new Exception('Gagal memperbarui status data');
			}
		} catch (Exception $e) {
			$this->db->trans_rollback();
			echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
		}
	}

	public function get_keterangan($id)
	{		
		$query = $this->db->get_where('ijt_verval', ['apply_id' => $id, 'jenis_verval' => 'keterangan']);
		$data = $query->row_array();

		if ($data) {
			echo json_encode($data);
		} else {
			echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan.']);
		}
	}

	public function get_files()
	{
		$apply_id = $this->input->post('apply_id');		

		if (!$apply_id) {
			echo json_encode(['status' => false, 'message' => 'Data tidak ditemukan']);
		}

		$this->load->model('ijt/m_internal_job_tender');
		$files = $this->m_internal_job_tender->get_dokumen_by_apply($apply_id);

		if (!empty($files)) {
			echo json_encode(['status' => true, 'files' => $files]);
		} else {
			echo json_encode(['status' => false, 'message' => 'Tidak ada file ditemukan']);
		}
	}
}
