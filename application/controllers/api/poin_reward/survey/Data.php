<?php defined('BASEPATH') or exit('No direct script access allowed');

class Data extends MY_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->load->model("poin_reward/m_manajemen_poin", "poin");
	}

	function index_get()
	{
		$np = $this->data_karyawan->np_karyawan;
		$date = date("Y-m-d");
		$id = $this->get('id');
		if (!empty($id)) {
			$this->db->select("a.*, (CASE WHEN lp.id IS NOT NULL THEN '1' ELSE '0' END) as status_baca");
			$this->db->from('manajemen_survey a');
			$this->db->join('log_poin lp', 'lp.survey_id = a.id AND lp.created_by_np = ' . $np, 'LEFT');
			$this->db->where('a.status', '1');
			$this->db->where('a.id', $id);
			$this->db->where('a.end_date >=', $date);
			$get = $this->db->get()->row();
			if ($get->gambar) $get->url = base_url() . "uploads/images/popup/" . $get->gambar;

			$this->response([
				'status' => true,
				'data' => $get
			], MY_Controller::HTTP_OK);
		} else {
			$this->db->select("a.*, (CASE WHEN lp.id IS NOT NULL THEN '1' ELSE '0' END) as status_baca");
			$this->db->from('manajemen_survey a');
			$this->db->join('log_poin lp', 'lp.survey_id = a.id AND lp.created_by_np = ' . $np, 'LEFT');
			$this->db->where('a.status', '1');
			$this->db->where('a.end_date >=', $date);
			$get = $this->db->get()->result();
			for ($i = 0; $i < count($get); $i++) {
				if (!empty($get[$i]->gambar)) $get[$i]->url = base_url() . "uploads/images/survey/" . $get[$i]->gambar;
			}
			$this->response([
				'status' => true,
				'data' => $get
			], MY_Controller::HTTP_OK);
		}
	}

	function index_post()
	{
		$survey_id = $this->post('id');

		$get = $this->db->where('id', $survey_id)->where('status', 1)->get('manajemen_survey')->row();

		if (empty($get)) {
			$this->response([
				'status' => false,
				'message' => "Survey tidak ditemukan",
			], MY_Controller::HTTP_BAD_REQUEST);
			exit;
		} else {
			$date = date("Y-m-d");

			if ($date > $get->end_date) {
				$this->response([
					'status' => false,
					'message' => "Survey telah berakhir",
				], MY_Controller::HTTP_BAD_REQUEST);
				exit;
			}
		}

		$data_insert = [
			'tipe' => 'Debit',
			'poin' => $get->poin,
			'sumber' => 'Survey',
			'survey_id' => $survey_id,
			'created_at' => date('Y-m-d H:i:s'),
			'created_by_np' => $this->data_karyawan->np_karyawan,
			'created_by_nama' => $this->data_karyawan->nama,
			'created_by_kode_unit' => $this->data_karyawan->kode_unit,
		];

		$cek_data = $this->db->select('*')
			->from('log_poin')
			->where('survey_id', $data_insert['survey_id'])
			->where('created_by_np', $data_insert['created_by_np'])
			->get()->row();

		if (empty($cek_data)) {
			$poin_sekarang = $this->poin->poin_sekarang($data_insert['created_by_np']);
			$data_insert['poin_awal'] = $poin_sekarang;
			$data_insert['poin_hasil'] = $poin_sekarang + (int)$data_insert['poin'];
			$this->db->insert("log_poin", $data_insert);
			$params = [
				'np' => $data_insert['created_by_np'],
				'nama' => $data_insert['created_by_nama'],
				'poin' => $data_insert['poin_hasil'],
			];
			$result = $this->poin->tambah_poin($params);
			if ($result) {
				$this->response([
					'status' => true,
					'message' => "Survey berhasil ditambahkan. $get->poin Poin ditambahkan",
					'data' => []
				], MY_Controller::HTTP_OK);
			} else {
				$this->response([
					'status' => false,
					'message' => "Survey gagal ditambahkan. $get->poin Poin gagal ditambahkan",
					'data' => []
				], MY_Controller::HTTP_BAD_REQUEST);
			}
		} else {
			$this->response([
				'status' => false,
				'message' => "Anda sudah menjalankan survey ini",
				'data' => []
			], MY_Controller::HTTP_BAD_REQUEST);
		}
	}
}
