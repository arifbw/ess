<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_lokasi extends CI_Model {

	private $table="mst_lokasi";
	
	public function __construct(){
		parent::__construct();
	}

	public function daftar_lokasi()
	{
		$this->db->from($this->table);
		$this->db->where('status', '1');

		return $this->db->get()->result();
	}

	public function insert($data)
	{
		$this->db->insert($this->table, $data);
	}

	public function cek_daftar_lokasi($id)
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