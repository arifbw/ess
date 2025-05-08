<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_reimbursement extends CI_Model {

	private $table="ess_cara_reimburse";
	
	public function __construct(){
		parent::__construct();
	}

	public function index()
	{
		$this->db->from($this->table);
		$this->db->where('status', '1');
		$this->db->order_by('no_urut', 'asc');

		return $this->db->get()->result();
	}

}

?>