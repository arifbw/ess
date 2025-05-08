<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_pengguna extends CI_Model {

	private $table="usr_pengguna";
	private $table_karyawan="mst_karyawan";
	private $table_unitkerja="mst_satuan_kerja";
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	public function ambil_data_pengguna($id_pengguna){
		$data = $this->db->select("p.id")
						 ->select("k.no_pokok")
						 ->select("p.username")
						 ->select("k.nama")
						 ->select("k.kode_unit")
						 ->select("k.nama_unit")
						 ->from($this->table." p")
						 ->join($this->table_karyawan." k","p.no_pokok=k.no_pokok","left")
						 ->where("p.id",$id_pengguna)
						 ->order_by("k.no_pokok")
						 ->get()
						 ->result_array();//echo $this->db->last_query();
		return $data;
	}
	
	public function cek_hasil_pengguna($karyawan,$username,$status){
		$data = $this->db->from($this->table)
						 ->where('no_pokok',$karyawan)
						 ->where('username',$username)
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
	
	public function cek_tambah_pengguna($username){
		$data = $this->db->from($this->table)
						 ->where('username',$username)
						 ->get();
		
		if($data->num_rows()==0){
			$return = true;
		}
		else{
			$return = false;
		}
		return $return;
	}
	
	public function cek_ubah_pengguna($username,$username_ubah){
		$return = array("status" => false, "error_info" => "");
		$data = $this->db->from($this->table)
						 ->where('username',$username)
						 ->get();
		
		if($data->num_rows()==0){
			$return["status"] = false;
			$return["error_info"] = "Pengguna dengan username <b>$username</b> tidak ada pada <i>database</i>.";
		}
		else if($data->num_rows()==1){
			if(strcmp($username,$username_ubah)==0){
				$return["status"] = true;
			}
			else{
				$data = $this->db->from($this->table)
								 ->where('lower(username)',strtolower($username_ubah))
								 ->get();

				if($data->num_rows()>0){
					$return["status"] = false;
					$return["error_info"] = "Pengguna dengan username <b>$username_ubah</b> telah digunakan.";
				}
				else{
					$return["status"] = true;
				}
			}
		}
		return $return;
	}
	
	public function daftar_pengguna($arr_id_grup_pengguna=""){
		$this->db->select("p.id")
			 ->select("p.no_pokok")
			 ->select("p.username")
			 ->select("k.nama")
			 ->select("k.kode_unit")
			 ->select("k.nama_unit")
			 ->select("p.status")
			 ->from($this->table." p")
			 ->join($this->table_karyawan." k","p.no_pokok=k.no_pokok","left");
			 
		if(!empty($grup)){
			$this->db->where_in("p.id_grup_pengguna",$arr_id_grup_pengguna);
		}

		 $data = $this->db->order_by("k.no_pokok")
						  ->get()
						  ->result_array();
		return $data;
	}
	
	public function data_pengguna($username){
		$data = $this->db->select("p.id")
						 ->select("k.no_pokok")
						 ->select("p.username")
						 ->select("k.nama")
						 ->select("k.kode_unit")
						 ->select("u.nama_unit")
						 ->select("p.status")
						 ->from($this->table." p")
						 ->join($this->table_karyawan." k","p.no_pokok=k.no_pokok","left")
						 ->join($this->table_unitkerja." u","k.kode_unit=u.kode_unit","left")
						 ->where("p.username",$username)
						 ->order_by("k.no_pokok")
						 ->get()
						 ->result_array()[0];
		return $data;
	}
	
	public function tambah($data){
		$this->db->insert($this->table,$data);
	}
	
	public function ubah($set,$karyawan,$username,$status){
		$this->db->where('no_pokok',$karyawan)
				 ->where('username',$username)
				 ->where('status',$status)
				 ->update($this->table,$set);//echo $this->db->last_query();die();
	}
}

/* End of file m_pengguna.php */
/* Location: ./application/models/administrator/m_pengguna.php */