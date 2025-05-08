<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_outbound_mst_karyawan extends CI_Model
{
	var $table = 'mst_karyawan';

	public function __construct()
	{
		parent::__construct();
	}

	public function all()
	{
		return $this->db->get( $this->table );
	}

	public function insert($data)
	{
		$this->db->insert($this->table, $data);
		return $this->db->affected_rows();
	}

	public function update($data, $where)
	{
		$this->db->where($where)->update($this->table, $data);
		return $this->db->affected_rows();
	}
}