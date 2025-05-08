<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_pengadministrasi_unit_kerja extends CI_Model {

	private $table="usr_pengadministrasi";
	private $table_karyawan="mst_karyawan";
	private $table_pengguna="usr_pengguna";
	private $table_grup="usr_pengguna_grup_pengguna";
	private $table_satker="mst_satuan_kerja";
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	public function daftar_pengguna_pengadministrasi($arr_id_grup_pengguna){
		$data = $this->db->select("u.id")
						 ->select("u.username")
						 ->select("k.no_pokok")
						 ->select("k.nama")
						 ->select("GROUP_CONCAT(DISTINCT CONCAT(s.kode_unit ,' - ', s.nama_unit) ORDER BY s.kode_unit SEPARATOR '|') nama_unit")
						 ->from($this->table_pengguna." u")
						 ->join($this->table." p","p.id_pengguna=u.id","left")
						 ->join($this->table_karyawan." k","u.no_pokok=k.no_pokok","left")
						 ->join($this->table_grup." g","u.id=g.id_pengguna","left")
						 ->join($this->table_satker." s","p.kode_unit=s.kode_unit","left")
						 ->where_in("g.id_grup_pengguna",$arr_id_grup_pengguna)
						 ->group_by("u.username")
						 ->order_by("u.username")
						 ->get()
						 ->result_array();//echo $this->db->last_query();
		return $data;
	}
}

/* End of file m_pengadministrasi_unit_kerja.php */
/* Location: ./application/models/osdm/m_pengadministrasi_unit_kerja.php */