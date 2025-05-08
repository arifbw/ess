<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_master_menu extends CI_Model {

	private $table="sys_master_menu";
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	public function ambil_id_menu($nama_menu){
		$data = $this->db->select("id")
						 ->from($this->table)
						 ->where("nama",$nama_menu)
						 ->get();

		$return = array();
		
		if($data->num_rows()==1){
			$return["hasil"] = true;
			$return["id"]=$data->result_array()[0]["id"];
		}
		else{
			$return["hasil"] = false;
		}
		return $return;
	}

	public function ambil_nama_menu($id_menu){
		$data = $this->db->select("nama")
						 ->from($this->table)
						 ->where("id",$id_menu)
						 ->get()->result_array();

		$nama = "";
		
		if(count($data)==1){
			$nama=$data[0]["nama"];
		}
		return $nama;
	}
	
	public function cek_hasil_master_menu($nama,$status){
		$data = $this->db->from($this->table)
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
	
	public function cek_ubah_master_menu($nama,$status,$nama_ubah){
		$return = array("status" => false, "error_info" => "");
		$data = $this->db->from($this->table)
						 ->where('nama',$nama)
						 ->where('status',$status)
						 ->get();
		
		if($data->num_rows()==0){
			$return["status"] = false;
			$return["error_info"] = "Nama Master Menu <b>$nama</b> tidak ada pada <i>database</i>.";
		}
		else if($data->num_rows()==1){
			if(strcmp($nama,$nama_ubah)==0){
				$return["status"] = true;
			}
			else{
				$data = $this->db->from($this->table)
								 ->where('lower(nama)',strtolower($nama_ubah))
								 ->get();

				if($data->num_rows()>0){
					$return["status"] = false;
					$return["error_info"] = "Nama Master Menu <b>$nama_ubah</b> telah digunakan.";
				}
				else{
					$return["status"] = true;
				}
			}
		}
		return $return;
	}
	
	public function cek_tambah_master_menu($nama){
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
	
	public function daftar_master_menu(){
		$data = $this->db->from($this->table)
						 ->order_by("nama")
						 ->get()
						 ->result_array();
		return $data;
	}
	
	public function data_master_menu($nama){
		$data = $this->db->from($this->table)
						 ->where('nama',$nama)
						 ->get()
						 ->result_array()[0];
		return $data;
	}
	
	public function tambah($data){
		$this->db->insert($this->table,$data);
	}
	
	public function ubah($set,$nama,$status){
		$this->db->where('nama',$nama)
				 ->where('status',$status)
				 ->update($this->table,$set);
	}
}

/* End of file m_master_menu.php */
/* Location: ./application/models/administrator/m_master_menu.php */
