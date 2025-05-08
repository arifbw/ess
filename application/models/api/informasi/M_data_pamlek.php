<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_data_pamlek extends CI_Model {

	var $table_pamlek = 'pamlek_data_';
	var $table_izin = 'mst_perizinan';
	var $column_order = array(null, 'payment_date'); //set column field database for datatable_pamlek orderable	
	var $order = array('tapping_time' => 'asc'); // default order 
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	public function get_all($periode,$np,$mesin_perizinan){
        $is_table_exist = $this->db->query("SELECT * FROM information_schema.tables WHERE table_schema = 'ess' AND table_name = '".$this->table_pamlek.$periode."' LIMIT 1");
        if ($is_table_exist->num_rows()==0) {
            $this->table_ = 'pamlek_data';
        } else {
            $this->table_ = $this->table_pamlek.$periode;
        }
		$this->db->select("distinct case when a.machine_id in ($mesin_perizinan) then b.nama else 'Kehadiran' end jenis");
		$this->db->select("case when a.in_out=0 then 'keluar' when a.in_out='1' then 'masuk' end tipe",false);
		$this->db->select("a.machine_id");
		$this->db->select("a.tapping_time");
		$this->db->from($this->table_." a");
		$this->db->join($this->table_izin." b","a.tapping_type=b.kode_pamlek","left");
		$this->db->where("no_pokok", $np);
		$query = $this->db->get();
		return $query->result();
	}
}

/* End of file m_data_pamlek.php */
/* Location: ./application/models/informasi/m_data_pamlek.php */