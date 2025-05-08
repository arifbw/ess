<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_pengguna_grup_pengguna extends CI_Model {

	private $table="usr_pengguna_grup_pengguna";
	private $table_grup_pengguna="sys_grup_pengguna";
	private $table_pengguna="usr_pengguna";
	
	public function __construct(){
		parent::__construct();
		$this->load->model("m_keycloak"); //
		$this->load->model("m_login");
	}
	
	public function cek_grup_pengguna($id_pengguna,$arr_grup_pengguna){
		$data = $this->db->from($this->table)
						 ->where('id_pengguna',$id_pengguna)
						 ->where_in('id_grup_pengguna',$arr_grup_pengguna)
						 ->get();

		if($data->num_rows()==count($arr_grup_pengguna)){
			$return = true;
		}
		else{
			$return = false;
		}

		return $return;
	}
	
	public function grup_pengguna_user($username){
		$data = $this->db->select("pgp.id_grup_pengguna")
						 ->select("gp.nama")
						 ->from($this->table." pgp")
						 ->join($this->table_grup_pengguna." gp","pgp.id_grup_pengguna=gp.id","left")
						 ->join($this->table_pengguna." u","pgp.id_pengguna=u.id","left")
						 ->where("gp.status",1)
						 ->where("u.username",$username)
						 ->order_by("nama")
						 ->get()
						 ->result_array();
		return $data;
	}
	
	public function hapus($data){
		$this->db->delete($this->table,$data);
	}

	public function tambah($data)
	{
		$this->db->insert($this->table, $data);
	}

	function syncRoleKeycloak($username)
	{
		$data =	$this->db->select('id,no_pokok')->where('username', $username)->get('usr_pengguna')->row_array();
		$np =	$data['no_pokok'];
		$id_pengguna =	$data['id'];

		$token = $this->m_keycloak->master();
		$roleUser = $this->m_keycloak->getRoleByUser($np, $token);
		$grup = $this->m_login->ambil_grup($id_pengguna);

		$this->m_keycloak->matchRole($grup, $roleUser['role'], $id_pengguna);
	}
}

/* End of file m_pengguna_grup_pengguna.php */
/* Location: ./application/models/administrator/m_pengguna_grup_pengguna.php */
