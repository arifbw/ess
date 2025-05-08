<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_hak_akses_grup_pengguna extends CI_Model {

	private $table="sys_hak_akses_grup_pengguna";
	private $table_kelompok_modul="sys_kelompok_modul";
	private $table_modul="sys_modul";
	private $table_aksi_modul="sys_aksi_modul";
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	public function cek_hak_akses_grup_pengguna($id_grup_pengguna,$arr_id_aksi){
		$data = $this->db->from($this->table)
						 ->where('id_grup_pengguna',$id_grup_pengguna)
						 ->where_in('id_aksi_modul',$arr_id_aksi)
						 ->get();

		if($data->num_rows()==count($arr_id_aksi)){
			$return = true;
		}
		else{
			$return = false;
		}

		return $return;
	}
	
	public function daftar_hak_akses(){
		$data = $this->db->select("k.nama nama_kelompok_modul")
						 ->select("m.nama nama_modul")
						 ->select("a.nama nama_aksi")
						 ->select("a.id id_aksi")
						 ->from($this->table_kelompok_modul." k")
						 ->join($this->table_modul." m", "k.id=m.id_kelompok_modul", "left")
						 ->join($this->table_aksi_modul." a", "m.id=a.id_modul", "left")
						 ->where("k.status",1)
						 ->where("m.status",1)
						 ->where("a.status",1)
						 ->where_not_in("m.url",array("","#"))
						 ->order_by("k.nama asc")
						 ->order_by("m.nama asc")
						 ->order_by("a.nama asc")
						 ->get()
						 ->result_array();
		return $data;
	}
	
	public function daftar_hak_akses_grup_pengguna($id_grup_pengguna){
		$arr_data = $this->db->select("id_aksi_modul")
						 ->from($this->table)
						 ->where("id_grup_pengguna",$id_grup_pengguna)
						 ->get()
						 ->result_array();

		$data = array();
		
		for($i=0;$i<count($arr_data);$i++){
			array_push($data,$arr_data[$i]["id_aksi_modul"]);
		}
		return $data;
	}
	
	public function hitung_hak_akses_per_kelompok(){
		$data = $this->db->select("k.nama nama_kelompok_modul")
						 ->select("COUNT(*) banyak")
						 ->from($this->table_kelompok_modul." k")
						 ->join($this->table_modul." m", "k.id=m.id_kelompok_modul", "left")
						 ->join($this->table_aksi_modul." a", "m.id=a.id_modul", "left")
						 ->where("k.status",1)
						 ->where("m.status",1)
						 ->where("a.status",1)
						 ->where_not_in("m.url",array("","#"))
						 ->group_by("k.nama")
						 ->order_by("k.nama asc")
						 ->get()
						 ->result_array();
		return $data;
	}
	
	public function hapus($data){
		$this->db->delete($this->table,$data);
	}
	
	public function tambah($data){
		$this->db->insert($this->table,$data);
	}
}

/* End of file m_hak_akses_grup_pengguna.php */
/* Location: ./application/models/administrator/m_hak_akses_grup_pengguna.php */