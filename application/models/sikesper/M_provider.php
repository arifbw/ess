<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_provider extends CI_Model {

	private $table="ess_provider_kesehatan";
	
	public function __construct(){
		parent::__construct();
	}

	public function daftar_provider($kode=null)
	{
		$this->db->from($this->table);
		$this->db->where('aktif', '1');
        
        if(@$kode){
            if($kode!='all'){
                $this->db->where('id_kabupaten', $kode);
            }
        }

		return $this->db->get()->result();
	}

	public function insert($data)
	{
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}

	public function cek_daftar_provider($id)
	{
		$this->db->select('a.*, b.nama as provinsi, c.nama as kabupaten');
		$this->db->from($this->table.' a');
		$this->db->join('provinsi b', 'a.id_provinsi = b.kode_wilayah');
		$this->db->join('kabupaten c', 'a.id_kabupaten = c.kode_wilayah');
		$this->db->where('a.id', $id);		

		return $this->db->get()->row();
	}

	public function update($data, $id)
	{
		$this->db->where('id', $id);		

		return $this->db->update($this->table, $data);
	}

	public function daftar_provinsi()
	{
		$this->db->select('kode_wilayah as id, nama as text');
		$this->db->from('provinsi');

		$search = $this->input->get('search');
		if(!empty($search)){
			$this->db->like('nama', $search);
		}

		return $this->db->get()->result_array();
	}

	public function daftar_kabupaten($provinsi = null)
	{
		$this->db->select('kode_wilayah as id, nama as text');
		$this->db->from('kabupaten');

		if($provinsi != null){
			$this->db->where('kode_prop', $provinsi);
		}

		$search = $this->input->get('search');
		if(!empty($search)){
			$this->db->like('nama', $search);
		}

		return $this->db->get()->result_array();
	}
    
    function filter_kabupaten(){
        return $this->db->select('a.id_kabupaten, b.nama')
            ->where('a.aktif','1')
            ->from('ess_provider_kesehatan a')
            ->join('kabupaten b','a.id_kabupaten=b.kode_wilayah')
            ->group_by('a.id_kabupaten, b.nama')
            ->order_by('a.id_kabupaten')
            ->get()
            ->result();
    }
}

?>