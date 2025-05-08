<?php defined('BASEPATH') or exit('No direct script access allowed');

class History_poin_reward extends MY_Controller
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

		$this->db->select("lpr.*, lp.tipe, lp.poin_awal, lp.poin_hasil, lp.sumber, a.nama, a.gambar, a.konten, a.poin, a.start_date, a.end_date, a.status");
		$this->db->from('log_poin_reward lpr');
		$this->db->join('log_poin lp', "lp.log_poin_reward_id = lpr.id AND lp.created_by_np = '$np'", 'LEFT');
		$this->db->join('manajemen_poin_reward a', 'lpr.poin_reward_id = a.id', 'LEFT');
		$this->db->where('lpr.created_by_np', $np);
		$this->db->order_by("lpr.id desc");

		$status_tukar = $this->get('status_tukar');

		if ($status_tukar == '1') {
			$this->db->where('lpr.status_tukar', '1');
		} else if ($status_tukar == '0') {
			$this->db->group_start();
			$this->db->where('lpr.status_tukar', '0');
			$this->db->or_where('lpr.status_tukar', null, false);
			$this->db->group_end();
		}

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
}
