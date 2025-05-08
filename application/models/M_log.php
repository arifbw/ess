<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_log extends CI_Model {

	private $table = "sys_log";
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	public function tambah($data){
		$id_grup = 0;
		if(isset($_SESSION["grup"])){
			$id_grup = $_SESSION["grup"];
		}
		$data["id_grup_pengguna"] = $id_grup;

		$this->db->insert($this->table,$data);
	}
}

/* End of file m_log.php */
/* Location: ./application/models/m_log.php */