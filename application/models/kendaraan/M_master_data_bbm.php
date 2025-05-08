<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_master_data_bbm extends CI_Model {

	private $table="mst_bbm";
	
	public function __construct(){
		parent::__construct();
	}
	
	public function daftar_jenis_bbm(){
		$data = $this->db->from($this->table)
						 ->get()
						 ->result_array();
		return $data;
	}
	
	public function data_jenis_bbm($nama){
		$data = $this->db->from($this->table)
						 ->where("nama",$nama)
						 ->get()
						 ->result_array()[0];
		return $data;
	}
	
	public function data_jenis_bbm_new($id){
		$data = $this->db->from($this->table)
						 ->where("id",$id)
						 ->get()
						 ->result_array()[0];
		return $data;
	}
	
	public function cek_hasil_jenis_bbm($nama,$harga,$status){
		$data = $this->db->from($this->table)
						 ->where('nama',$nama)
						 ->where('harga',$harga)
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
	
	public function cek_tambah_jenis_bbm($nama){
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
	
	public function cek_ubah_jenis_bbm($data_update){
		$return = array("status" => false, "error_info" => "");
		$data = $this->db->from($this->table)
						 ->where('id',$data_update['id'])
						 ->get();
		
		if($data->num_rows()==0){
			$return["status"] = false;
			$return["error_info"] = "Jenis BBM tidak ada pada <i>database</i>.";
		}
		else if($data->num_rows()==1){
            $return["status"] = true;
		}
		return $return;
	}
	
	public function tambah($data){
		$this->db->insert($this->table,$data);
	}
	
	public function ubah($set,$id){
		$this->db->where('id',$id)
				 ->update($this->table,$set);
	}
}

/* End of file m_jenis_cuti.php */
/* Location: ./application/models/master_data/m_jenis_cuti.php */