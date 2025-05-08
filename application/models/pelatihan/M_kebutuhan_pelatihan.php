<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Kebutuhan_pelatihan extends CI_Model {

	public function __construct(){
		parent::__construct();
		//Do your magic here
	}

	function get_pelatihan($id_kategori_pelatihan)
	{	
		$this->db->select('*');
		$this->db->from('mst_diklat_pelatihan');	
		if($id_kategori_pelatihan!=''){
			$this->db->where('id_kategori_pelatihan', $id_kategori_pelatihan);
		}
		$data = $this->db->get();
		
		return $data->result_array();
	}

}