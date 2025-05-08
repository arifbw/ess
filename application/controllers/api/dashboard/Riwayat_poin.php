<?php defined('BASEPATH') or exit('No direct script access allowed');

class Riwayat_poin extends MY_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->load->model("poin_reward/m_manajemen_poin", "poin");
	}

	function index_get()
	{
		try {
			$_get_np = $this->get('np');
			$tahun = $this->get('tahun') ?: date('Y');
			$np = @$_get_np!='' ? $_get_np : $this->data_karyawan->np_karyawan;
			$get = $this->poin->daftar_riwayat_poin($np, $tahun);

			$this->response([
				'status' => true,
				'data' => $get
			], MY_Controller::HTTP_OK);
		} catch (Exception $e) {
			$this->response([
				'status' => true,
				'message' => $e
			], MY_Controller::HTTP_BAD_REQUEST);
		}
	}
}
