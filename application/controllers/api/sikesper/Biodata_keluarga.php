<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Biodata_keluarga extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->folder_model = 'sikesper/';
		$this->load->model($this->folder_model . "m_keluarga_tertanggung");
	}

	function index_get()
	{
		if (@$this->get('np')) {
			$data_karyawan = $this->db->where('no_pokok', $this->get('np'))->get('mst_karyawan');
			if ($data_karyawan->num_rows() != 1) {
				$this->response([
					'status' => false,
					'message' => "NP tidak ditemukan",
					'data' => []
				], MY_Controller::HTTP_BAD_REQUEST);
			}
			$np = $this->get('np');
			$nama = $data_karyawan->row()->nama;
		} else {
			$np = $this->data_karyawan->np_karyawan;
			$nama = $this->data_karyawan->nama;
		}

		$karyawan = $this->m_keluarga_tertanggung->detailKaryawan($np);
		$keluarga = $this->m_keluarga_tertanggung->detail_keluarga($np);

		$data = [
			'karyawan' => $karyawan,
			'keluarga' => $keluarga
		];

		$this->response([
			'status' => true,
			'message' => 'Data Biodata Detail Keluarga' . $nama,
			'data' => $data
		], MY_Controller::HTTP_OK);
	}
}
