<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Get_data extends CI_Controller {
	public function __construct(){
		parent::__construct();
		$this->load->helper("tanggal_helper");
		$this->load->helper("karyawan_helper");
	}

	# match NP
	private function cek_np($np_karyawan) {
		$start_date			= date('Y-m-d');
        $tahun_bulan     	= str_replace('-','_',substr("$start_date", 0, 7));
		$nama_karyawan 		= erp_master_data_by_np($np_karyawan, $start_date)['nama'];
		$nama_jabatan		= erp_master_data_by_np($np_karyawan, $start_date)['nama_jabatan'];
		if ($nama_karyawan=='' || $nama_karyawan==null) {
			$start_date			= date("Y-m-d", strtotime( date( "Y-m-d", strtotime( date("Y-m-d") ) ) . "-1 month" ) );
	        $tahun_bulan     	= str_replace('-','_',substr("$start_date", 0, 7));
			$nama_karyawan 		= erp_master_data_by_np($np_karyawan, $start_date)['nama'];
			$nama_jabatan		= erp_master_data_by_np($np_karyawan, $start_date)['nama_jabatan'];
			if ($nama_karyawan=='' || $nama_karyawan==null) {
				$start_date			= date("Y-m-d", strtotime( date( "Y-m-d", strtotime( date("Y-m-d") ) ) . "-2 month" ) );
		        $tahun_bulan     	= str_replace('-','_',substr("$start_date", 0, 7));
				$nama_karyawan 		= erp_master_data_by_np($np_karyawan, $start_date)['nama'];
				$nama_jabatan		= erp_master_data_by_np($np_karyawan, $start_date)['nama_jabatan'];
			}
		}

		$data['np_karyawan'] = $np_karyawan;
		$data['nama_karyawan'] = $nama_karyawan;
		$data['nama_jabatan'] = $nama_jabatan;
		$data['alamat'] = null;
		$data['plafon'] = 0;

        return $data;
	}

	# saat ini msh berfungsi untuk inputan listrik dan pam
	function ajax_getNama(){
		$np_karyawan = $this->input->post('np',true);
		$tabel_cari = $this->input->post('tabel_cari',true);
		$getLastInput = $this->db->select('np_karyawan, nama_karyawan, "" as nama_jabatan, alamat, plafon')->where('np_karyawan', $np_karyawan)->order_by('created_at','DESC')->limit(1)->get($tabel_cari)->row_array();
		if( $getLastInput['np_karyawan']!=null )
			$data = $getLastInput;
		else
			$data = $this->cek_np($np_karyawan);

		$return = [
            'status'=>true,
            'data'=>$data
        ];

        echo json_encode($return);
	}
	# END: match NP

	# ambil plafon
	# listrik
	function mst_plafon_listrik(){
		$np = $this->input->post('np',true);
		$get = $this->db->where('deleted_at IS NULL',null,false)->where('np_karyawan',$np)->get('mst_plafon_listrik')->row();
		echo json_encode([
			'status'=>true,
			'data'=>$get
		]);
	}
	
	function mst_plafon_listrik_by_kontrol(){
		$np = $this->input->post('np',true);
		$kontrol = $this->input->post('kontrol',true);
		$get = $this->db->where('deleted_at IS NULL',null,false)->where('np_karyawan',$np)->where('no_kontrol',$kontrol)->get('mst_plafon_listrik')->row();
		echo json_encode([
			'status'=>true,
			'data'=>$get
		]);
	}
	
	# pam
	function mst_plafon_pam(){
		$np = $this->input->post('np',true);
		$get = $this->db->where('deleted_at IS NULL',null,false)->where('np_karyawan',$np)->get('mst_plafon_pam')->row();
		echo json_encode([
			'status'=>true,
			'data'=>$get
		]);
	}
	
	# pam
	function mst_plafon_tv(){
		$np = $this->input->post('np',true);
		$get = $this->db->where('deleted_at IS NULL',null,false)->where('np_karyawan',$np)->get('mst_plafon_tv')->row();
		echo json_encode([
			'status'=>true,
			'data'=>$get
		]);
	}
	
	# internet
	function mst_plafon_internet(){
		$np = $this->input->post('np',true);
		$get = $this->db->where('deleted_at IS NULL',null,false)->where('np_karyawan',$np)->get('mst_plafon_internet')->row();
		echo json_encode([
			'status'=>true,
			'data'=>$get
		]);
	}
	
	# pulsa
	function mst_plafon_ponsel(){
		$np = $this->input->post('np',true);
		$get = $this->db->where('deleted_at IS NULL',null,false)->where('np_karyawan',$np)->limit(1)->get('mst_plafon_ponsel')->row();
		echo json_encode([
			'status'=>true,
			'data'=>$get
		]);
	}
	
	function mst_plafon_ponsel_by_hp(){
		$np = $this->input->post('np',true);
		$hp = $this->input->post('hp',true);
		$get = $this->db->where('deleted_at IS NULL',null,false)->where('np_karyawan',$np)->where('no_hp',$hp)->limit(1)->get('mst_plafon_ponsel')->row();
		echo json_encode([
			'status'=>true,
			'data'=>$get
		]);
	}
	# END: ambil plafon

	# get data mst karyawan
	function get_mst_karyawan(){
		$get = $this->db->select('no_pokok, nama, nama_jabatan')->get('mst_karyawan')->result();
		echo json_encode($get);
	}
	# END: get data mst karyawan
	
	# get master plafon
	function get_mst_plafon(){
		$table_name = $this->input->post('table_name',true);
		$get = $this->db->where('deleted_at IS NULL',null,false)->get($table_name)->result();
		echo json_encode($get);
	}
	# END: get master plafon
}