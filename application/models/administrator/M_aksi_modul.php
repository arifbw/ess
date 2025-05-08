<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_aksi_modul extends CI_Model {

	private $table="sys_aksi_modul";
	private $table_modul="sys_modul";
	private $table_kelompok_modul="sys_kelompok_modul";
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	public function ambil_nama_modul($id){
		$data = $this->db->select("nama")
						 ->from($this->table_modul)
						 ->where("id",$id)
						 ->get();
		$return = "";
		
		if($data->num_rows()==1){
			$return=$data->result_array()[0]["nama"];
		}
		return $return;
	}
	
	public function cek_hasil_aksi_modul($id_modul,$nama,$status){
		$data = $this->db->from($this->table)
						 ->where('id_modul',$id_modul)
						 ->where('nama',$nama)
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
	
	public function cek_tambah_aksi_modul($id_modul,$nama){
		$data = $this->db->from($this->table)
						 ->where('id_modul',$id_modul)
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
	
	public function cek_ubah_aksi_modul($modul,$nama,$status,$modul_ubah,$nama_ubah,$status_ubah){
		$return = array("status" => false, "error_info" => "");
		$data = $this->db->from($this->table)
						 ->where('id_modul',$modul)
						 ->where('nama',$nama)
						 ->where('status',$status)
						 ->get();
		
		$nama_modul = $this->ambil_nama_modul($modul);
		
		if($data->num_rows()==0){
			$return["status"] = false;
			$return["error_info"] = "Aksi Pengguna <b>$nama</b> pada Modul <b>$nama_modul</b> tidak ada pada <i>database</i>.";
		}
		else if($data->num_rows()==1){
			if(strcmp($nama,$nama_ubah)==0){
				$return["status"] = true;
			}
			else{
				$data = $this->db->from($this->table)
								 ->where('id_modul',$modul_ubah)
								 ->where('lower(nama)',strtolower($nama_ubah))
								 ->get();

				$nama_modul_ubah = $this->ambil_nama_modul($modul_ubah);
				
				if($data->num_rows()>0){
					$return["status"] = false;
					$return["error_info"] = "Aksi Pengguna <b>$nama_ubah</b> pada Modul <b>$nama_modul_ubah</b> telah digunakan.";
				}
				else{
					$return["status"] = true;
				}
			}
		}
		return $return;
	}
	
	public function daftar_aksi_modul(){
		$data = $this->db->select("a.id")
						 ->select("k.nama nama_kelompok_modul")
						 ->select("m.nama nama_modul")
						 ->select("a.nama nama_aksi")
						 ->select("a.status")
						 ->from($this->table_kelompok_modul." k")
						 ->join($this->table_modul." m", "k.id=m.id_kelompok_modul", "left")
						 ->join($this->table." a", "m.id=a.id_modul", "left")
						 ->where("m.status",1)
						 ->where_not_in("m.url",array("","#"))
						 ->order_by("k.nama asc")
						 ->order_by("m.nama asc")
						 ->order_by("a.nama asc")
						 ->get()
						 ->result_array();
		return $data;
	}
	
	public function daftar_modul_aksi(){
		$data = $this->db->select("k.nama nama_kelompok_modul,m.*",false)
						 ->from($this->table_modul." m")
						 ->join($this->table_kelompok_modul." k","m.id_kelompok_modul=k.id","left")
						 ->where_not_in("url",array("","#"))
						 ->order_by("k.nama asc")
						 ->order_by("m.nama asc")
						 ->get()
						 ->result_array();
		return $data;
	}
	
	public function data_aksi_modul($modul,$nama){
		$data = $this->db->select("a.id")
						 ->select("k.nama nama_kelompok_modul")
						 ->select("m.nama nama_modul")
						 ->select("a.nama nama_aksi")
						 ->select("a.status")
						 ->from($this->table_kelompok_modul." k")
						 ->join($this->table_modul." m", "k.id=m.id_kelompok_modul", "left")
						 ->join($this->table." a", "m.id=a.id_modul", "left")
						 ->where("a.id_modul",$modul)
						 ->where("a.nama",$nama)
						 ->where("m.status",1)
						 ->where_not_in("m.url",array("","#"))
						 ->order_by("k.nama asc")
						 ->order_by("m.nama asc")
						 ->order_by("a.nama asc")
						 ->get()
						 ->result_array()[0];
		return $data;
	}
	
	public function tambah($data){
		$this->db->insert($this->table,$data);
	}
	
	public function ubah($set,$modul,$nama,$status){
		$this->db->where('id_modul',$modul)
				 ->where('nama',$nama)
				 ->where('status',$status)
				 ->update($this->table,$set);
	}
}

/* End of file m_aksi_modul.php */
/* Location: ./application/models/administrator/m_aksi_modul.php */