<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_mst_kategori_lembur extends CI_Model
{
	var $table = 'mst_kategori_lembur';

	public function __construct(){
		parent::__construct();
	}

	public function get_all(){
		$this->db->where('status', 1);
		return $this->db->get($this->table)->result();
	}
}