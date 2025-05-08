<?php defined('BASEPATH') or exit('No direct script access allowed');

class Data extends MY_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->load->helper("karyawan_helper");
		$this->load->model("poin_reward/m_manajemen_poin", "poin");
	}

	function index_get()
	{
		$date = date("Y-m-d");
		$np = $this->data_karyawan->np_karyawan;

		$get = $this->db->select("mp.*")
			->from("manajemen_popup mp")
			->where('mp.status', 1)
			->where('mp.start_date <=', $date)
			->where('mp.end_date >=', $date)
			->get()->row();

		if ($get) {
			if ($get->gambar) $get->url = base_url() . "uploads/images/popup/" . $get->gambar;

			$cek_data = $this->db->select('*')
				->from('log_popup')
				->where('popup_id', $get->id)
				->where('created_by_np', $np)
				->get()->row();

			if (empty($cek_data)) {
				$get->status_baca = '0';
			} else {
				$get->status_baca = '1';
			}
		}

		$this->response([
			'status' => true,
			'data' => $get
		], MY_Controller::HTTP_OK);
	}

	function index_post()
	{
		$popup_id = $this->post('id');
		$np_karyawan = $this->data_karyawan->np_karyawan;

		$data_insert = [
			'popup_id' => $popup_id,
			'created_at' => date('Y-m-d H:i:s'),
			'created_by_np' => $np_karyawan,
			'created_by_nama' => $this->data_karyawan->nama,
		];

		$cek_data = $this->db->select('*')
			->from('log_popup')
			->where('popup_id', $data_insert['popup_id'])
			->where('created_by_np', $data_insert['created_by_np'])
			->get()->row();

		if (empty($cek_data)) {
			$this->db->insert("log_popup", $data_insert);
		}

		$get = $this->db->where('id', $popup_id)->where('status', 1)->get('manajemen_popup')->row();

		$data_insert = [
			'tipe' => 'Debit',
			'poin' => $get->poin,
			'sumber' => 'Popup',
			'popup_id' => $popup_id,
			'created_at' => date('Y-m-d H:i:s'),
			'created_by_np' => $this->data_karyawan->np_karyawan,
			'created_by_nama' => $this->data_karyawan->nama,
			'created_by_kode_unit' => $this->data_karyawan->kode_unit,
		];

		$cek_data = $this->db->select('*')
			->from('log_poin')
			->where('popup_id', $data_insert['popup_id'])
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
					'message' => "$get->poin Poin berhasil ditambahkan",
					'data' => []
				], MY_Controller::HTTP_OK);
			} else {
				$this->response([
					'status' => false,
					'message' => "Gagal. $get->poin Poin gagal ditambahkan",
					'data' => []
				], MY_Controller::HTTP_BAD_REQUEST);
			}
		} else {
			$this->response([
				'status' => false,
				'message' => "Popup sudah pernah dibuka",
				'data' => []
			], MY_Controller::HTTP_BAD_REQUEST);
		}
	}
}
