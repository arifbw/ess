<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_master_data_katering extends CI_Model {

	private $table="mst_penyedia_makanan";
	
	public function __construct(){
		parent::__construct();
	}

	public function daftar_penyedia()
	{
		$this->db->select('a.*, b.nama as nama_lokasi');
		$this->db->from($this->table.' a');
		$this->db->join('mst_lokasi b', 'a.lokasi = b.id', 'LEFT');
		$this->db->order_by('a.lokasi', 'ASC');

		return $this->db->get()->result();
	}

	public function daftar_lokasi()
	{
		$this->db->select('id, nama');
		$this->db->from('mst_lokasi');
		$this->db->where('status', '1');

		return $this->db->get()->result();
	}

	public function insert($data)
	{
		$this->db->insert($this->table, $data);
	}

	public function cek_daftar_penyedia($id)
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
