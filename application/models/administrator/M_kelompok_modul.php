<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_kelompok_modul extends CI_Model {

	private $table="sys_kelompok_modul";
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	public function ambil_nama($id_kelompok_modul){
		$data = $this->db->select("nama")
						 ->from($this->table)
						 ->where("id",$id_kelompok_modul)
						 ->get();
		$nama = "";
		
		if($data->num_rows()==1){
			$nama=$data->result_array()[0]["nama"];
		}
		return $nama;
	}
	
	public function daftar_kelompok_modul(){
		$data = $this->db->from($this->table)
						 ->order_by("nama asc")
						 ->get()
						 ->result_array();
		return $data;
	}
	
	public function data_kelompok_modul($nama){
		$data = $this->db->from($this->table)
						 ->where('nama',$nama)
						 ->get()
						 ->result_array()[0];
		return $data;
	}
	
	public function cek_hasil_kelompok_modul($nama,$keterangan,$status){
		$data = $this->db->from($this->table)
						 ->where('nama',$nama)
						 ->where('keterangan',$keterangan)
						 ->where('status',$status)
						 ->get();

		if($data->num_rows()==0){
			$return = false;
		}
		else{
			$return = true;
		}

		return $return;
	}
	
	public function cek_tambah_kelompok_modul($nama){
		$data = $this->db->from($this->table)
						 ->where('nama',$nama)
						 ->get();
		
		if($data->num_rows()==0){
			$return = true;
		}
		else{
			$return = false;
		}
		return $return;
	}
	
	public function cek_ubah_kelompok_modul($nama,$nama_ubah){
		$return = array("status" => false, "error_info" => "");
		$data = $this->db->from($this->table)
						 ->where('nama',$nama)
						 ->get();
		
		if($data->num_rows()==0){
			$return["status"] = false;
			$return["error_info"] = "Kelompok Modul dengan nama <b>$nama</b> tidak ada pada <i>database</i>.";
		}
		else if($data->num_rows()==1){
			if(strcmp($nama,$nama_ubah)==0){
				$return["status"] = true;
			}
			else{
				$data = $this->db->from($this->table)
								 ->where('lower(nama)',strtolower($nama_ubah))
								 ->where_not_in('lower(nama)',strtolower($nama))
								 ->get();

				if($data->num_rows()>0){
					$return["status"] = false;
					$return["error_info"] = "Kelompok Modul dengan nama <b>$nama_ubah</b> telah digunakan.";
				}
				else{
					$return["status"] = true;
				}
			}
		}
		return $return;
	}
	
	public function tambah($data){
		$this->db->insert($this->table,$data);
	}
	
	public function ubah($set,$nama,$keterangan,$status){
		$this->db->where('nama',$nama)
				 ->where('keterangan',$keterangan)
				 ->where('status',$status)
				 ->update($this->table,$set);
	}
}

/* End of file m_kelompok_modul.php */
/* Location: ./application/models/administrator/m_kelompok_modul.php */