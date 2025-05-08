<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_jabatan extends CI_Model {

	private $table="mst_jabatan";
	private $table_satuan_kerja="mst_satuan_kerja";
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	public function data_jabatan($kode_jabatan){
		$this->db->select("a.kode_unit")
				 ->select("b.nama_unit")
				 ->select("a.kode_jabatan")
				 ->select("a.nama_jabatan")
				 ->from($this->table." a")
				 ->join($this->table_satuan_kerja." b","a.kode_unit=b.kode_unit")
				 ->where("kode_jabatan",$kode_jabatan)
				 ->order_by("a.kode_unit")
				 ->order_by("a.kode_jabatan");
		//echo $this->db->get_compiled_select();
		$data = $this->db->get()->result_array();
		return $data;
	}
	
	public function daftar_jabatan($struktural=false){
		$this->db->select("a.kode_unit")
				 ->select("b.nama_unit")
				 ->select("a.kode_jabatan")
				 ->select("a.nama_jabatan")
				 ->from($this->table." a")
				 ->join($this->table_satuan_kerja." b","a.kode_unit=b.kode_unit");
		if($struktural){
			$this->db->like("a.kode_jabatan","00","before");
		}
		$this->db->order_by("a.kode_unit")
				 ->order_by("a.kode_jabatan");
		//echo $this->db->get_compiled_select();
		$data = $this->db->get()->result_array();
		return $data;
	}
	
	public function daftar_jabatan_struktural(){
		$struktural = true;
		return $this->daftar_jabatan($struktural);
	}
	
	public function daftar_jabatan_bawah($induk){
		$induk = rtrim($kode_unit,0);
		$data = $this->db->from($this->table)
						 ->like("kode_unit",$induk,"after")
						 ->order_by("kode_unit")
						 ->get()
						 ->result_array();
		return $data;
	}
	
	public function get_pejabat($kode_jabatan){
		$data = $this->db->select("no_pokok")
						 ->select("nama")
						 ->from("mst_karyawan")
						 ->where("kode_jabatan",$kode_jabatan)
						 ->order_by("grade_pangkat","DESC")
						 ->order_by("no_pokok","ASC")
						 ->get()
						 ->result_array();//echo $this->db->last_query();
		return $data;
	}
}

/* End of file m_karyawan.php */
/* Location: ./application/models/master_data/m_satuan_kerja.php */