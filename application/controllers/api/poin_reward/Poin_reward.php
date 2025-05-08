<?php defined('BASEPATH') or exit('No direct script access allowed');

class Poin_reward extends MY_Controller
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
		$id = $this->get('id');
		$np = $this->data_karyawan->np_karyawan;

		if ($id) {
			$this->db->select("*, (select count(*) from log_poin_reward where poin_reward_id=manajemen_poin_reward.id) as jumlah_klaim, (select count(*) from log_poin_reward where poin_reward_id=manajemen_poin_reward.id AND created_by_np = '$np') as jumlah_klaim_anda");
			$this->db->from('manajemen_poin_reward');
			$this->db->where('status', '1');
			$this->db->where('id', $id);
			$this->db->where('end_date >=', $date);
			$data = $this->db->get()->row();

			if ($data) {
				if ($data->gambar) $data->url = base_url() . "uploads/images/poin_reward/" . $data->gambar;
				$data->kode_scan = $this->poin->kode_scan($np, $data->id);
			}
			return $this->response([
				'status' => true,
				'data' => $data
			]);
		}

		$this->db->select("*, (select count(*) from log_poin_reward where poin_reward_id=manajemen_poin_reward.id) as jumlah_klaim, (select count(*) from log_poin_reward where poin_reward_id=manajemen_poin_reward.id AND created_by_np = '$np') as jumlah_klaim_anda");
		$this->db->from('manajemen_poin_reward');
		$this->db->where('status', '1');
		$this->db->where('end_date >=', $date);
		$data = $this->db->get()->result();

		for ($i = 0; $i < count($data); $i++) {
			if (!empty($data[$i]->gambar)) $data[$i]->url = base_url() . "uploads/images/poin_reward/" . $data[$i]->gambar;
			$data[$i]->kode_scan = $this->poin->kode_scan($np, $data[$i]->id);
		}

		$this->response([
			'status' => true,
			'data' => $data
		], MY_Controller::HTTP_OK);
	}

	function index_post()
	{
		$kode_scan = $this->post('kode_scan');
		$result = $this->poin->scan_kode_poin_reward($kode_scan, $this->data_karyawan);

		if ($result['status']) {
			$this->response($result, MY_Controller::HTTP_OK);
		} else {
			$this->response($result, MY_Controller::HTTP_BAD_REQUEST);
		}
	}
}
