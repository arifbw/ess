<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_perizinan extends CI_Model {

	private $table="mst_perizinan";
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	public function ambil_perizinan_id($id){
		$data = $this->db->from($this->table)
						 ->where("id",$id)
						 ->get()
						 ->result_array()[0];
		return $data;
	}
	
	public function daftar_perizinan(){
		$data = $this->db->from($this->table)
						 ->order_by("nama asc")
						 ->get()
						 ->result_array();
		return $data;
	}
	
	public function data_perizinan($nama){
		$data = $this->db->from($this->table)
						 ->where("nama",$nama)
						 ->get()
						 ->result_array()[0];
		return $data;
	}
	
	public function cek_hasil_perizinan($nama,$keterangan,$kode_pamlek,$kode_erp,$status){
		$data = $this->db->from($this->table)
						 ->where('nama',$nama)
						 ->where('keterangan',$keterangan)
						 ->where('kode_pamlek',$kode_pamlek)
						 ->where('kode_erp',$kode_erp)
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
	
	public function cek_tambah_perizinan($nama){
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
	
	public function cek_ubah_perizinan($nama,$nama_ubah){
		$return = array("status" => false, "error_info" => "");
		$data = $this->db->from($this->table)
						 ->where('nama',$nama)
						 ->get();
		
		if($data->num_rows()==0){
			$return["status"] = false;
			$return["error_info"] = "Perizinan dengan nama <b>$nama</b> tidak ada pada <i>database</i>.";
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
					$return["error_info"] = "Nama Perizinan <b>$nama_ubah</b> telah digunakan.";
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
	
	public function ubah($set,$nama){
		$this->db->where('nama',$nama)
				 ->update($this->table,$set);
	}
}

/* End of file m_perizinan.php */
/* Location: ./application/models/master_data/m_perizinan.php */