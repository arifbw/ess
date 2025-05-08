<?php defined('BASEPATH') or exit('No direct script access allowed');

class Data extends MY_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->load->helper("karyawan_helper");
	}

	function index_get()
	{
		$get = $this->db->select("gd.*")
			->from("gambar_dinamis gd")
			->where('gd.status', 1)
			->where('gd.nama =', 'Logo App Bar ESS Mobile')
			->get()->row();

		if ($get) {
			$get->url = base_url()."uploads/images/gambar_dinamis/".$get->gambar;
		}

		$this->response([
			'status' => true,
			'data' => $get
		], MY_Controller::HTTP_OK);
	}
}
