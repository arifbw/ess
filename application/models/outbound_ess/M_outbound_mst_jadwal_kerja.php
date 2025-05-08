<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_outbound_mst_jadwal_kerja extends CI_Model
{
	var $table = 'mst_jadwal_kerja';

	public function __construct()
	{
		parent::__construct();
	}

	public function all()
	{
		return $this->db->get( $this->table );
	}

	public function get_one($id)
	{
		$this->db->where('id', $id)->where('deleted_at IS NULL', null,false);
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