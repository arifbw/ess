<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Biodata extends MY_Controller
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

		$list = $this->m_keluarga_tertanggung->get_datatable($np, $unit);

		$data = array();
		foreach ($list as $tampil) {

			// START OF ENCRYPT NAMA FILE*/
			$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0987654321";
			$rand_chars = "";
			$length_rand_chars = rand(8, 16);

			while (strlen($rand_chars) < $length_rand_chars) {
				$rand_chars .= substr($chars, rand(0, strlen($chars)), 1);
			}

			$id_txt = rand(1, 10000) . "|" . "get_id" . "|" . $tampil->id . "|" . date('Y-m-d H:i:s') . "|" . $rand_chars;
			$encrypted_txt_id = $this->encrypt_decrypt('encrypt', $id_txt);
			// END OF ENCRYPT NAMA FILE*/

			$no++;
			$row = array();
			$row[] = $no;
			$row[] = $tampil->no_pokok . ' - ' . $tampil->nama . '(' . $tampil->nama_unit_singkat . ')';
			$row[] = $tampil->tempat_lahir . '' . tanggal_indonesia($tampil->tanggal_lahir) . '';
			$row[] = $tampil->usia;
			//$row[] = tanggal_indonesia($tampil->start_date);
			$row[] = $tampil->jenis_kelamin;
			//$row[] = $tampil->bpjs_id;
			$row[] = $tampil->bpjs_kesehatan;
			$row[] = $tampil->class_bpjs != null ? $tampil->class_bpjs : 'I';
			$row[] = $tampil->kelas;
			$row[] = $tampil->jumlah . " Keluarga";

			$data[] = $row;
		}

		$this->response([
			'status' => true,
			'message' => 'Data Biodata ' . $nama,
			'data' => $data
		], MY_Controller::HTTP_OK);
	}

	private function encrypt_decrypt($action, $string)
	{
		$output = false;
		$encrypt_method = "AES-256-CBC";
		$secret_key = 'zxfajaSsd1fjDwASjA12SAGSHga3yus' . date('Ymd');
		$secret_iv = 'zxASsadkmjku4jLOIh2jfGda5' . date('Ymd');
		// hash
		$key = hash('sha256', $secret_key);

		// iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
		$iv = substr(hash('sha256', $secret_iv), 0, 16);
		if ($action == 'encrypt') {
			$output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
			$output = base64_encode($output);
		} else if ($action == 'decrypt') {
			$output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);

			$pisah 				= explode('|', $output);
			$datetime_request 	= $pisah[3];
			/*
				$datetime_expired 	= date('Y-m-d H:i:s',strtotime('+10 seconds',strtotime($datetime_request))); 

				$datetime_now		= date('Y-m-d H:i:s');
				
				if($datetime_now > $datetime_expired || !$datetime_request){
					$output = false;
				}	
				*/
		}
		return $output;
	}
}
