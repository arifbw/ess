<?php defined('BASEPATH') or exit('No direct script access allowed');

class Ref_tahun extends MY_Controller
{

	function __construct()
	{
		parent::__construct();
	}

	function index_get()
	{
		try {
			for ($i = 2022; $i <= date('Y'); $i++) {
				$data[] = [
					'id' => "$i",
					'nama' => "$i"
				];
			}

			$this->response([
				'status' => true,
				'data' => $data
			], MY_Controller::HTTP_OK);
		} catch (Exception $e) {
			$this->response([
				'status' => true,
				'message' => $e
			], MY_Controller::HTTP_BAD_REQUEST);
		}
	}
}
