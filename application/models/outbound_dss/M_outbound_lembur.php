<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_outbound_lembur extends CI_Model {
	
	private $table="erp_master_data_";
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	public function generate_lembur($tanggal){
		//kalender, portal
		$this->table = substr();
		if($jenis==1){
			$this->db->select("no_pokok")
					 ->select("nama")
					 ->select("tempat_lahir")
					 ->select("tanggal_lahir")
					 ->select("kode_unit")
					 ->select("jenis_kelamin")
					 ->select("agama")
					 ->select("kontrak_kerja")
					 ->select("nama_pangkat")
					 ->select("kode_jabatan")
					 ->select("nama_jabatan")
					 ->select("now()",false);
			
		}
		//sppd
		if($jenis==2){
			$this->db->query("SET @baris=0");
			$this->db->select("@baris:=@baris+1",false);
			$this->db->select("no_pokok")
					 ->select("nama")
					 ->select("nama_jabatan")
					 ->select("kode_unit")
					 ->select("nama_unit")
					 ->select("nama_pangkat");
		}
			
		$this->db->from($this->table);
		$this->db->order_by("personnel_number");

		$data = $this->db->get()->result_array();echo $this->db->last_query();
		
		return $data;
	}
}

