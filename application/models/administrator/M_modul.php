<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_modul extends CI_Model {

	private $table="sys_modul";
	private $table_kelompok_modul="sys_kelompok_modul";
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	public function ambil_url_modul($nama){
		$data = $this->db->select("url")
						 ->from($this->table)
						 ->where("nama",$nama)
						 ->get()->result_array();
		$url = "";
		
		if(count($data)==1){
			$url=$data[0]["url"];
		}
		return $url;
	}
	
	public function ambil_nama_modul($id_modul){
		$data = $this->db->select("nama")
						 ->from($this->table)
						 ->where("id",$id_modul)
						 ->get()->result_array();
		$nama = "";
		
		if(count($data)==1){
			$nama=$data=[0]["nama"];
		}
		return $nama;
	}
	
	public function daftar_modul(){
		$data = $this->db->select("k.nama nama_kelompok_modul,m.*",false)
						 ->from($this->table." m")
						 ->join($this->table_kelompok_modul." k","m.id_kelompok_modul=k.id","left")
						 ->order_by("k.nama asc")
						 ->order_by("m.nama asc")
						 ->get()
						 ->result_array();//echo $this->db->last_query();
		return $data;
	}
	
	public function daftar_modul_aktif(){
		$data = $this->db->select("k.nama nama_kelompok_modul,m.*",false)
						 ->from($this->table." m")
						 ->join($this->table_kelompok_modul." k","m.id_kelompok_modul=k.id","left")
						 ->where("m.status",1)
						 ->order_by("k.nama asc")
						 ->order_by("m.nama asc")
						 ->get()
						 ->result_array();
		return $data;
	}
	
	public function data_modul($id_kelompok_modul,$nama){
		$data = $this->db->from($this->table)
						 ->where('nama',$nama)
						 ->where('id_kelompok_modul',$id_kelompok_modul)
						 ->get()
						 ->result_array()[0];
		return $data;
	}
	
	public function cek_hasil_modul($id_kelompok_modul,$nama,$url,$icon,$status){
		$data = $this->db->from($this->table)
						 ->where('id_kelompok_modul',$id_kelompok_modul)
						 ->where('nama',$nama)
						 ->where('url',$url)
						 ->where('icon',$icon)
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
	
	public function cek_tambah_modul($id_kelompok_modul,$nama){
		$data = $this->db->from($this->table)
						 ->where('id_kelompok_modul',$id_kelompok_modul)
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
	
	public function cek_ubah_modul($id_kelompok_modul,$nama,$url,$status,$nama_kelompok_modul,$id_kelompok_modul_ubah,$nama_ubah,$url_ubah,$nama_kelompok_modul_ubah){
		$return = array("status" => false, "error_info" => "");
		$data = $this->db->from($this->table)
						 ->where('id_kelompok_modul',$id_kelompok_modul)
						 ->where('nama',$nama)
						 ->where('status',$status)
						 ->get();
		
		if($data->num_rows()==0){
			$return["status"] = false;
			$return["error_info"] = "Modul dengan nama <b>$nama</b> pada Kelompok Modul <b>$nama_kelompok_modul</b> tidak ada di <i>database</i>.";
		}
		else if($data->num_rows()==1){
			if(strcmp($nama,$nama_ubah)==0 and (int)$id_kelompok_modul==(int)$id_kelompok_modul_ubah){
				$return["status"] = true;
			}
			else{
				$data = $this->db->from($this->table)
								 ->where('id_kelompok_modul',$id_kelompok_modul_ubah)
								 ->where('lower(nama)',strtolower($nama_ubah))
								 ->where_not_in('lower(nama)',strtolower($nama))
								 ->get();

				if($data->num_rows()>0){
					$return["status"] = false;
					$return["error_info"] = "Modul dengan nama <b>$nama_ubah</b> telah digunakan pada Kelompok Modul $nama_kelompok_modul_ubah.";
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
	
	public function ubah($set,$id_kelompok_modul,$nama,$url,$icon,$status){
		$this->db->where('id_kelompok_modul',$id_kelompok_modul)
				 ->where('nama',$nama)
				 ->where('url',$url)
				 ->where('icon',$icon)
				 ->where('status',$status)
				 ->update($this->table,$set);
	}
}

/* End of file m_modul.php */
/* Location: ./application/models/administrator/m_modul.php */
