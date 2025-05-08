<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_administrator extends CI_Model {

	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	private function ambil_id_pengguna($username){
		return $this->db->select("id")
					->from("pengguna")
					->where("username",$username)
					->get()
					->result_array()[0]["id"];
	}
	
	private function cek_hasil_pengguna($username){
		$data = $this->db->from('pengguna')
						 ->where('username',$username)
						 ->get();

		if($data->num_rows()==0){
			$return = false;
		}
		else{
			$return = true;
		}

		return $return;
	}
	
	private function cek_tambah_pengguna($username){
		$data = $this->db->from('pengguna')
						 ->where('username',$username)
						 ->get();
		
		if($data->num_rows()==0){
			$return = true;
		}
		else{
			$return = false;
		}
		return $return;
	}
	
	
	private function simpan_pengguna_grup_pengguna($id_pengguna,$grup_pengguna){
		$return = array("status" => false, "error_info" => "");
		
		$data = array(
					"id_pengguna" => $id_pengguna
				);
		$this->db->delete('pengguna_grup_pengguna',$data);
		
		$arr_grup_pengguna = explode(",",$grup_pengguna);
		
		for($i=0;$i<count($arr_grup_pengguna);$i++){
			$data = array(
						"id_pengguna" => $id_pengguna,
						"id_grup_pengguna" => $arr_grup_pengguna[$i]
					);
			$this->db->insert("pengguna_grup_pengguna",$data);
		}
	}
	
	public function tambah_pengguna($username,$password,$email,$grup_pengguna,$status){
		$return = array("status" => false, "error_info" => "");
		if($this->cek_tambah_pengguna($username)){
			$this->db->set(array(
						"username" => "'$username'",
						"password" => "'".md5($password)."'",
						"email" => "'$email'",
						"waktu_daftar" => "NOW()",
						"aktif" => "'".$status."'"
					),"",FALSE)->insert('pengguna');
			
			if($this->cek_hasil_pengguna($username)){
				$id_pengguna = $this->ambil_id_pengguna($username);
				
				$this->simpan_pengguna_grup_pengguna($id_pengguna,$grup_pengguna);
				
				$return["status"] = true;
			}
			else{
				$return["status"] = false;
				$return["error_info"] = "Penambahan Pengguna <b>Gagal</b> Dilakukan.";
			}
		}
		else{
			$return["status"] = false;
			$return["error_info"] = "Pengguna dengan <i>Username</i> <b>$username</b> sudah ada.";
		}
		return $return;
	}
}

/* End of file m_administrator.php */
/* Location: ./application/models/m_administrator.php */