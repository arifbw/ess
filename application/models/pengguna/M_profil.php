<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_profil extends CI_Model {

	private $table="usr_pengguna";
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	public function data_profil($username){
		$data = $this->db->select("a.*")
						 ->select("datediff(now(),a.last_change_password) usia_password")
						 ->from($this->table." a")
						 ->where('username',$username)
						 ->get()
						 ->result_array()[0];
		return $data;
	}
	
	public function tambah($data){
		$this->db->insert($this->table,$data);
	}
	
	public function ubah($set,$nama,$status){
		$this->db->where('nama',$nama)
				 ->where('status',$status)
				 ->update($this->table,$set);
	}
}

/* End of file m_profil.php */
/* Location: ./application/models/pengguna/m_profil.php */