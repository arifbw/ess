<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_grup_pengguna extends CI_Model {

	private $table="sys_grup_pengguna";
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	public function ambil_id_grup_pengguna($nama_grup_pengguna){
		$data = $this->db->select("id")
						 ->from($this->table)
						 ->where("nama","$nama_grup_pengguna")
						 ->get()
						 ->result_array();
		return $data[0]["id"];
	}
	
	public function daftar_grup_pengguna(){
		$data = $this->db->from($this->table)
						 ->order_by("nama")
						 ->get()
						 ->result_array();
		return $data;
	}
	
	public function daftar_grup_pengguna_aktif(){
		$data = $this->db->from($this->table)
						 ->where("status",1)
						 ->order_by("nama")
						 ->get()
						 ->result_array();
		return $data;
	}
	
	public function data_grup_pengguna($nama){
		$data = $this->db->from($this->table)
						 ->where('nama',$nama)
						 ->get()
						 ->result_array()[0];
		return $data;
	}
	
	public function daftar_pengguna($id_grup_pengguna){
		$data = $this->db->select("c.no_pokok")
						 ->select("c.nama")
						 ->select("c.kode_unit")
						 ->select("c.nama_unit")
						 ->from("usr_pengguna_grup_pengguna a")
						 ->join("usr_pengguna b","a.id_pengguna = b.id","left")
						 ->join("mst_karyawan c","b.no_pokok = c.no_pokok","left")
						 ->where("a.id_grup_pengguna",$id_grup_pengguna)
						 ->where("c.no_pokok IS NOT NULL")
						 ->order_by("c.kode_unit")
						 ->order_by("c.grade_jabatan","desc")
						 ->order_by("c.no_pokok")
						 ->get()
						 ->result_array();
		return $data;
	}
	
	public function cek_hasil_grup_pengguna($nama,$status){
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
	
	public function cek_tambah_grup_pengguna($nama){
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
	
	public function cek_ubah_grup_pengguna($nama,$status,$nama_ubah){
		$return = array("status" => false, "error_info" => "");
		$data = $this->db->from($this->table)
						 ->where('nama',$nama)
						 ->where('status',$status)
						 ->get();
		
		if($data->num_rows()==0){
			$return["status"] = false;
			$return["error_info"] = "Grup Pengguna dengan nama <b>$nama</b> tidak ada pada <i>database</i>.";
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
					$return["error_info"] = "Nama Grup Pengguna <b>$nama_ubah</b> telah digunakan.";
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
	
	public function ubah($set,$nama,$status){
		$this->db->where('nama',$nama)
				 ->where('status',$status)
				 ->update($this->table,$set);
	}
	
	public function ambil_kode_unit($np_karyawan)
	{
		$this->db->select('kode_unit');
		$this->db->from('mst_karyawan');
		
		$this->db->where('no_pokok', $np_karyawan);
		
		$query 	= $this->db->get();
		$ambil 	= $query->row_array();
		
		return $ambil['kode_unit'];
	}
}

/* End of file m_grup_pengguna.php */
/* Location: ./application/models/administrator/m_grup_pengguna.php */