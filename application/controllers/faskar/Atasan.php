<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Atasan extends CI_Controller {
	public function __construct(){
		parent::__construct();
		$this->load->helper("karyawan_helper");
	}

	public function ajax_getNama_approval() {
		$np_atasan = $this->input->post('np_aprover');
		$np_karyawan = $this->input->post('np_karyawan');
		$kode_unit = array(kode_unit_by_np($np_karyawan));

		$return = [
			'status'=>false,
			'data'=>[],
			'message'=>'Silahkan isi No. Pokok Atasan Dengan Benar',
		];

		if ($np_atasan==$np_karyawan) {
			$return['message'] = 'No. Pokok Approver Tidak Valid';
		} else {
			$this->load->model('m_approval');

			$list = $this->m_approval->list_atasan_minimal_kasek($kode_unit, $np_karyawan);
			$return['message'] = 'Approval Pelaporan Minimal Kasek';

			$list_np = array_column($list, 'no_pokok');
			if (in_array($np_atasan, $list_np)) {
				$key = array_search($np_atasan, $list_np);
				$data['nama'] = $list[$key]['nama'];
				$data['nama_jabatan'] = $list[$key]['nama_jabatan'];
			}


			if (@$data) {
				$return = [
					'status'=>true,
					'data'=>[
						'nama'=>$data['nama'],
						'jabatan'=>$data['nama_jabatan']
					]
				];
			} else {
				$start_date			= date('Y-m-d');
				$end_date			= date('Y-m-d');
				$tahun_bulan     	= $start_date!=null ? str_replace('-','_',substr("$start_date", 0, 7)) : str_replace('-','_',substr("$end_date", 0, 7)) ;
				$nama_karyawan 		= erp_master_data_by_np($np_atasan, $start_date)['nama'];
				$nama_jabatan		= erp_master_data_by_np($np_atasan, $start_date)['nama_jabatan'];
				
				$return = [
					'status'=>true,
					'data'=>[
						'nama'=>$nama_karyawan,
						'jabatan'=>$nama_jabatan
					]
				];
			}
		}

		echo json_encode($return);
	}
}