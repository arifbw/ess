<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_daftar_obat extends CI_Model {
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	public function index($jenis='0',$ktg='0',$tgl='0') {	
		$this->db->select("*");
		$this->db->from("ess_daftar_obat");
		if($jenis!='0')
			$this->db->where("jenis", urldecode($jenis));
		if($ktg!='0')
			$this->db->where("kategori", urldecode($ktg));		
		if($tgl!='0')
			$this->db->where("created_at", urldecode($tgl));		
				
		$query = $this->db->get();
		return $query->result();
	}
}

/* End of file m_skep.php */
/* Location: ./application/models/informasi/m_skep.php */