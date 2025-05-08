<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_pegawai extends CI_Model {

	public function __construct(){
		parent::__construct();
		//Do your magic here
	}

	private function cek_hasil_pegawai($data){
		$data = $this->db->from('pegawai')
						 ->where('no_pokok',$data['no_pokok'])
						 ->where('nomor_ktp',$data['nomor_ktp'])
						 ->where('nama_lengkap',$data['nama'])
						 ->where('jenis_kelamin',$data['jenis_kelamin'])
						 ->where('tanggal_lahir',$data['tanggal_lahir'])
						 ->where('tanggal_masuk',$data['tanggal_masuk'])
						 ->get();

		if($data->num_rows()==0){
			$return = false;
		}
		else{
			$return = true;
		}

		return $return;
	}
	
	private function cek_tambah_pegawai($data){
		$data = $this->db->from('pegawai')
						 ->where('nomor_ktp',$data['nomor_ktp'])
						 ->get();
		
		if($data->num_rows()==0){
			$return = true;
		}
		else{
			$return = false;
		}
		return $return;
	}
	
	public function daftar_pegawai(){
		$data = $this->db->from("pegawai")
						 ->order_by("nama_lengkap asc")
						 ->get()
						 ->result_array();
		return $data;
	}
	
	public function tambah_pegawai($data){
		$return = array("status" => false, "error_info" => "");
		if($this->cek_tambah_pegawai($data)){
			$data = array(
						"no_pokok" => $data['no_pokok'],
						"nomor_ktp" => $data['nomor_ktp'],
						"nama_lengkap" => $data['nama'],
						"jenis_kelamin" => $data['jenis_kelamin'],
						"tanggal_lahir" => $data['tanggal_lahir'],
						"tanggal_masuk" => $data['tanggal_masuk']
					);
			$this->db->insert('pegawai',$data);
			
			if($this->cek_hasil_pegawai($data)){
				$return["status"] = true;
			}
			else{
				$return["status"] = false;
				$return["error_info"] = "Penambahan Pegawai <b>Gagal</b> Dilakukan.";
			}
		}
	}
}

/* End of file m_pegawai.php */
/* Location: ./application/models/m_pegawai.php */