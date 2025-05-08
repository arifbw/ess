<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_reimbursement extends CI_Model {

	private $table="ess_cara_reimburse";
	
	public function __construct(){
		parent::__construct();
	}

	public function daftar_reimburse()
	{
		$this->db->from($this->table);
		//$this->db->where('status', '1'); # heru comment ini, 2020-12-23, 13:58
		$this->db->order_by('no_urut', 'asc');

		return $this->db->get()->result();
	}

	public function insert($data)
	{
		$this->db->insert($this->table, $data);
	}

	public function cek_daftar_reimburse($id)
	{
		$this->db->from($this->table);
		$this->db->where('id', $id);		

		return $this->db->get()->row();
	}

	public function update($data, $id)
	{
		$this->db->where('id', $id);		

		return $this->db->update($this->table, $data);
	}
}

?>