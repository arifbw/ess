<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_pengaturan extends CI_Model {

	private $table="sys_pengaturan";
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	public function daftar_pengaturan(){
		$data = $this->db->from($this->table)
						 ->order_by("nama asc")
						 ->get()
						 ->result_array();
		return $data;
	}
	
	public function data_pengaturan($nama){
		$data = $this->db->from($this->table)
						 ->where('nama',$nama)
						 ->get()
						 ->result_array()[0];
		return $data;
	}
	
	public function cek_hasil_pengaturan($nama,$isi){
		$data = $this->db->from($this->table)
						 ->where('nama',$nama)
						 ->where('isi',$isi)
						 ->get();

		if($data->num_rows()==0){
			$return = false;
		}
		else{
			$return = true;
		}

		return $return;
	}
	
	public function ambil_isi_pengaturan($nama){
	
		$this->db->select('*');
		$this->db->from($this->table);
		$this->db->where('nama',$nama);
		
		$query = $this->db->get();
		
		$ambil = $query->row_array();

		return $ambil['isi'];
	}
	
	public function cek_tambah_pengaturan($nama){
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
	
	public function cek_ubah_pengaturan($nama,$nama_ubah){
		$return = array("status" => false, "error_info" => "");
		$data = $this->db->from($this->table)
						 ->where('nama',$nama)
						 ->get();
		
		if($data->num_rows()==0){
			$return["status"] = false;
			$return["error_info"] = "Pengaturan dengan nama <b>$nama</b> tidak ada pada <i>database</i>.";
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
					$return["error_info"] = "Nama pengaturan <b>$nama_ubah</b> telah digunakan.";
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
	
	public function ubah($set,$nama,$isi){
		$this->db->where('nama',$nama)
				 ->where('isi',$isi)
				 ->update($this->table,$set);
	}
}

/* End of file m_pengaturan.php */
/* Location: ./application/models/administrator/m_pengaturan.php */