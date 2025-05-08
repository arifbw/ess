<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_outbound_satuan_kerja extends CI_Model {
	
	private $table="mst_satuan_kerja";
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	public function generate_satuan_kerja($jenis){
		//kalender
		if($jenis==1){
			$this->db->select("kode_unit")
					 ->select("nama_unit")
					 ->select("now()",false);
		}
		//nde
		else if($jenis==2){
			$this->db->select("kode_unit")
					 ->select("case when substr(kode_unit,1,1)='9' then '00000' else rpad(substr(kode_unit,1,length(regexp_replace(kode_unit,'(0+$)',''))-1),5,'0') end",false)
					 ->select("nama_unit");
		}
		
		$this->db->from($this->table);
		$this->db->order_by("kode_unit");

		$data = $this->db->get()->result_array();echo $this->db->last_query();
		
		return $data;
	}
}

