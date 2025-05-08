<?php defined('BASEPATH') OR exit('No direct script access allowed');

class M_login_history extends CI_Model {

	private $table = "login_history";
	
	public function __construct(){
		parent::__construct();
	}
	
	public function insert_log($data){
		$this->db->insert($this->table,$data);
	}
}