<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_satuan_kerja extends CI_Model {

	private $table="mst_satuan_kerja";
	private $table_administrasi="usr_pengadministrasi";
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	public function daftar_satuan_kerja(){
		$data = $this->db->from($this->table)
						 ->order_by("kode_unit")
						 ->get()
						 ->result_array();
		return $data;
	}
	
	public function daftar_satuan_kerja_diadministrasikan(){
		$data = $this->db->from($this->table." a")
						 ->join($this->table_administrasi." b","a.kode_unit=b.kode_unit")
						 ->where("b.id_pengguna",$this->session->userdata("id_pengguna"))
						 ->order_by("a.kode_unit")
						 ->get()
						 ->result_array();//echo $this->db->last_query();
		return $data;
	}
	
	public function daftar_satuan_kerja_bawah($induk){
		$induk = rtrim($induk,0);
		$data = $this->db->from($this->table)
						 ->like("kode_unit",$induk,"after")
						 ->order_by("kode_unit")
						 ->get()
						 ->result_array();
		return $data;
	}
	
	public function max_level_satuan_kerja_bawah($induk){
		$induk = rtrim($induk,0);
		$data = $this->db->query("SELECT MAX(LENGTH(REGEXP_REPLACE(a.kode_unit,'0+$',''))) level FROM mst_satuan_kerja a WHERE a.kode_unit LIKE '$induk%'")->result_array()[0]["level"];
		return $data;
	}
}

/* End of file m_karyawan.php */
/* Location: ./application/models/master_data/m_satuan_kerja.php */