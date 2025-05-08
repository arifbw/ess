<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_menu_grup_pengguna extends CI_Model {

	private $table="sys_menu_grup_pengguna";
	private $table_posisi_menu="sys_posisi_menu";
	private $table_master_menu="sys_master_menu";
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	public function ambil_nama_posisi_menu($id_posisi_menu){
		$data = $this->db->select("nama")
						 ->from($this->table_posisi_menu)
						 ->where("id",$id_posisi_menu)
						 ->get();
		$return = "";
		
		if($data->num_rows()==1){
			$return=$data->result_array()[0]["nama"];
		}
		return $return;
	}
	
	public function cek_posisi_menu_master_menu_grup_pengguna($id_grup_pengguna,$id_posisi_menu,$id_master_menu){
		$data = $this->db->from($this->table)
						 ->where('id_grup_pengguna',$id_grup_pengguna)
						 ->where('id_posisi_menu',$id_posisi_menu)
						 ->where('id_master_menu',$id_master_menu)
						 ->get();
		
		if($data->num_rows()==1){
			$return = true;
		}
		else{
			$return = false;
		}
		return $return;
	}
	
	public function cek_posisi_menu_grup_pengguna($id_grup_pengguna,$id_posisi_menu){
		$data = $this->db->from($this->table)
					 ->where('id_grup_pengguna',$id_grup_pengguna)
					 ->where('id_posisi_menu',$id_posisi_menu)
					 ->get();
		
		if($data->num_rows()==1){
			$return = true;
		}
		else{
			$return = false;
		}
		return $return;
	}
	
	public function daftar_menu_grup_pengguna($id_grup_pengguna){
		$data = $this->db->select("mgp.id")
						 ->select("pm.id id_posisi_menu")
						 ->select("pm.nama nama_posisi_menu")
						 ->select("pm.shortcode shortcode_posisi_menu")
						 ->select("mm.id id_master_menu")
						 ->select("mm.nama nama_master_menu")
						 ->from($this->table_posisi_menu." pm")
						 ->join($this->table." mgp","pm.id=mgp.id_posisi_menu AND mgp.id_grup_pengguna='$id_grup_pengguna'","left")
						 ->join($this->table_master_menu." mm","mgp.id_master_menu=mm.id AND mgp.id_grup_pengguna='$id_grup_pengguna'","left")
						 ->order_by("pm.nama")
						 ->get()
						 ->result_array();
		return $data;
	}
	
	public function data_menu_grup_pengguna($id_grup_pengguna,$id_posisi_menu){
		$data = $this->db->select("pm.id id_posisi_menu")
						 ->select("pm.nama nama_posisi_menu")
						 ->select("mm.nama nama_master_menu")
						 ->from($this->table_posisi_menu." pm")
						 ->join($this->table." mgp","pm.id=mgp.id_posisi_menu AND mgp.id_grup_pengguna='$id_grup_pengguna'","left")
						 ->join($this->table_master_menu." mm","mgp.id_master_menu=mm.id AND mgp.id_grup_pengguna='$id_grup_pengguna'","left")
						 ->where("pm.id",$id_posisi_menu)
						 ->get()
						 ->result_array()[0];//echo $this->db->last_query();
		return $data;
	}
	
	public function ubah_menu_grup_pengguna($set,$id_grup_pengguna,$id_posisi_menu){
		$this->db->where("id_grup_pengguna",$id_grup_pengguna)
				 ->where("id_posisi_menu",$id_posisi_menu)
				 ->update($this->table,$set);
	}
	
	public function tambah($data){
		$this->db->insert($this->table,$data);
	}
}

/* End of file m_menu_grup_pengguna.php */
/* Location: ./application/models/administrator/m_menu_grup_pengguna.php */