<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_foto_karyawan extends CI_Model {

	private $table="foto_karyawan";
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	public function ambil_nama_file($no_pokok){
		$data = $this->db->select("nama_file")
						 ->from($this->table)
						 ->where('no_pokok',$no_pokok)
						 ->get()
						 ->result_array()[0]["nama_file"];
		return $data;
	}
	
	public function banyak_foto($cari){
		$this->db->select("count(*) as banyak_foto",false)
				 ->from($this->table);
		if(!empty($cari)){
			$this->db->like("no_pokok",$cari)
					 ->or_like("nama",$cari);
		}
		$data = $this->db->get()
						 ->result_array()[0]["banyak_foto"];
		return $data;
	}
	
	public function cek_ada($no_pokok){
		$data = $this->db->from($this->table)
						 ->where('no_pokok',$no_pokok)
						 ->get();
		
		if($data->num_rows()==0){
			$return = false;
		}
		else{
			$return = true;
		}
		return $return;
	}
	
	public function cek($data_simpan){
		$data = $this->db->from($this->table)
						 ->where($data_simpan)
						 ->get();
		if($data->num_rows()==0){
			$return = false;
		}
		else{
			$return = true;
		}
		return $return;
	}
	
	public function data_file($no_pokok){
		$data = $this->db->from($this->table)
						 ->where('no_pokok',$no_pokok)
						 ->get()
						 ->result_array()[0];
		return $data;
	}
	
	public function lihat($halaman,$foto_per_halaman,$cari){
		$this->db->from($this->table);
		if(!empty($cari)){
			$this->db->like("no_pokok",$cari)
					 ->or_like("nama",$cari);
		}
		$this->db->order_by("waktu_ubah desc")
				 ->order_by("no_pokok desc");
		
		$data = $this->db->limit($foto_per_halaman, ($halaman-1)*$foto_per_halaman)
						 ->get()
						 ->result_array();
		return $data;
	}
	
	public function tambah($data){
		$this->db->insert($this->table,$data);
	}
	
	public function ubah($set,$no_pokok){
		$this->db->where('no_pokok',$no_pokok)
				 ->update($this->table,$set);
	}
}

/* End of file m_foto_karyawan.php */
/* Location: ./application/models/osdm/m_foto_karyawan.php */