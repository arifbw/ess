<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_outbound_jabatan extends CI_Model {
	
	private $table="mst_jabatan";
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	public function generate_jabatan($jenis){
		//nde
		if($jenis==1){
			$this->db->select("kode_jabatan")
					 ->select("nama_jabatan")
					 ->select("kode_unit");
		}
		
		$this->db->from($this->table);
		$this->db->order_by("kode_unit");
		$this->db->order_by("kode_jabatan");

		$data = $this->db->get()->result_array();echo $this->db->last_query();
		
		return $data;
	}
}

