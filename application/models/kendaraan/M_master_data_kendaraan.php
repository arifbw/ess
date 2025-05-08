<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_master_data_kendaraan extends CI_Model {

	private $table="mst_kendaraan";
	
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
	
	public function daftar_jenis_kendaraan(){
		$data = $this->db->from($this->table)
						 ->get()
						 ->result_array();
		return $data;
	}
	
	public function data_jenis_kendaraan($nama){
		$data = $this->db->from($this->table)
						 ->where("nama",$nama)
						 ->get()
						 ->result_array()[0];
		return $data;
	}
	
	public function data_jenis_kendaraan_new($id){
		$data = $this->db->from($this->table)
						 ->where("id",$id)
						 ->get()
						 ->result_array()[0];
		return $data;
	}
	
	public function cek_hasil_jenis_kendaraan($nama,$nopol,$status){
		$data = $this->db->from($this->table)
						 ->where('nama',$nama)
						 ->where('nopol',$nopol)
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
	
	public function cek_tambah_jenis_kendaraan($nama){
		$data = $this->db->from($this->table)
						 ->where('nama',$nama)
						 ->get();
		
		if($data->num_rows()==0){
			$return = true;
		}
		else{
			$return = false;
		}
		return true;
	}
	
	//public function cek_ubah_jenis_kendaraan($nama,$nama_ubah){
	public function cek_ubah_jenis_kendaraan($data_update){
		$return = array("status" => false, "error_info" => "");
		$data = $this->db->from($this->table)
						 ->where('id',$data_update['id'])
						 ->get();
		
		if($data->num_rows()==0){
			$return["status"] = false;
			$return["error_info"] = "Jenis Kendaraan tidak ada pada <i>database</i>.";
		}
		else if($data->num_rows()==1){
            $return["status"] = true;
			/*if(strcmp($nama,$nama_ubah)==0){
			}
			else{
				$data = $this->db->from($this->table)
								 ->where('lower(nama)',strtolower($nama_ubah))
								 ->where_not_in('lower(nama)',strtolower($nama))
								 ->get();

				if($data->num_rows()>0){
					$return["status"] = false;
					$return["error_info"] = "Nama Jenis Kendaraan <b>$nama_ubah</b> telah digunakan.";
				}
				else{
					$return["status"] = true;
				}
			}*/
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