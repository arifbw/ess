<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_katalog extends CI_Model {

	private $table="mst_jenis_katalog";
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}

	public function daftar_katalog($jenis=null, $aktif=null)
	{
		$this->db->select('a.id, a.nama, a.jenis, a.harga, b.nama_penyedia, c.nama as lokasi, a.status');
		$this->db->from($this->table.' a');
		$this->db->join('mst_penyedia_makanan b', 'a.id_penyedia = b.id');
		$this->db->join('mst_lokasi c', 'b.lokasi = c.id');
		if($jenis!=null) {
			$this->db->where('a.jenis', $jenis);
		}
		if($jenis!=null) {
			$this->db->where('a.status', '1');
		}

		return $this->db->get()->result();
	}

	public function daftar_penyedia($lokasi = null)
	{
		$this->db->select('id, nama_penyedia as text');
		$this->db->from('mst_penyedia_makanan');
		$this->db->where('status', '1');

		if($lokasi != null){
			$this->db->where('lokasi', $lokasi);
		}

		$search = $this->input->get('search');
		if(!empty($search)){
			$this->db->like('nama_penyedia', $search);
		}

		return $this->db->get()->result_array();
	}

	public function daftar_lokasi()
	{
		$this->db->select('id, nama as text');
		$this->db->from('mst_lokasi');
		$this->db->where('status', '1');

		$search = $this->input->get('search');
		if(!empty($search)){
			$this->db->like('nama', $search);
		}

		return $this->db->get()->result_array();
	}

	public function tambah($data)
	{
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}

	public function ubah($table, $data, $id)
	{
		$this->db->where('id', $id);
		return $this->db->update($table, $data);
	}

	public function detail_katalog($id)
	{
		$this->db->select('a.id, a.id_penyedia, b.nama_penyedia, a.nama, a.jenis, a.harga, b.nama_penyedia as penyedia, c.id as lokasi, c.nama as nama_lokasi, a.status');
		$this->db->from($this->table.' a');
		$this->db->join('mst_penyedia_makanan b', 'a.id_penyedia = b.id');
		$this->db->join('mst_lokasi c', 'b.lokasi = c.id');
		$this->db->where('a.id', $id);

		return $this->db->get()->row();
	}
}