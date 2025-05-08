<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Data extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->folder_model = 'faskar/ponsel/';
	}

	function index_get()
	{
		$params=[];
		$params['header_id'] = @$this->get('header_id');
		
		$this->load->model($this->folder_model."M_tabel_ponsel_detail");
		$list = $this->M_tabel_ponsel_detail->get_datatables($params);

		$this->response([
			'status' => true,
			'message' => 'Sukses ambil data',
			'data' => $list
		], MY_Controller::HTTP_OK);
	}
}
