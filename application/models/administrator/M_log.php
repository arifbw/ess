<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_log extends CI_Model {

	private $table = "sys_log";
	private $table_pengguna = "usr_pengguna";
	private $table_karyawan = "mst_karyawan";
	private $table_modul = "sys_modul";
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	public function tambah($data){
		$this->db->insert($this->table,$data);
	}
	
	public function lihat($id_pengguna="",$id_modul="",$id_target=""){
		$this->db->select("p.username")
				 ->select("p.no_pokok")
				 ->select("k.nama")
				 ->select("m.nama nama_modul")
				 ->select("l.deskripsi")
				 ->select("l.kondisi_lama")
				 ->select("l.kondisi_baru")
				 ->select("l.alamat_ip")
				 ->select("l.waktu")
				 ->from($this->table." l")
				 ->join($this->table_pengguna." p","l.id_pengguna=p.id","left")
				 ->join($this->table_karyawan." k","p.no_pokok=k.no_pokok","left")
				 ->join($this->table_modul." m","l.id_modul=m.id","left");
		
		if(!empty($id_pengguna)){
			$this->db->where("l.id_pengguna",$id_pengguna);
		}
		
		if(!empty($id_modul)){
			$this->db->where("l.id_modul",$id_modul);
		}
		
		if(!empty($id_target)){
			$this->db->where("l.id_target",$id_target);
		}
		
		$data = $this->db->order_by("l.id desc")
						 ->get()
						 ->result_array();
		//echo $this->db->last_query();
		return $data;
	}
}

/* End of file m_log.php */
/* Location: ./application/models/administrator/m_log.php */