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
	
	private function _get_datatable_pamleks_query($periode,$np,$mesin_perizinan){
		//dipindah kebawah karena tabel nya dinamis
		$column_search = array("a.payment_date"); //set column field database for datatable_pamlek 
		
		$this->db->select("distinct case when a.machine_id in ($mesin_perizinan) then b.nama else 'Kehadiran' end jenis");
		$this->db->select("case when a.in_out=0 then 'keluar' when a.in_out='1' then 'masuk' end tipe",false);
		$this->db->select("a.machine_id");
		$this->db->select("a.tapping_time");
		$this->db->from($this->table_pamlek.$periode." a");
		$this->db->join($this->table_izin." b","a.tapping_type=b.kode_pamlek","left");
		$this->db->where("no_pokok", $np);
				
		$i = 0;
	
		foreach ($column_search as $item) // loop column 
		{
			if($_POST['search']['value']) // if datatable_pamlek send POST for search
			{
				
				if($i===0) // first loop
				{
					$this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
					$this->db->like($item, $_POST['search']['value']);
				}
				else
				{
					$this->db->or_like($item, $_POST['search']['value']);
				}

				if(count($column_search) - 1 == $i) //last loop
					$this->db->group_end(); //close bracket
			}
			$i++;
		}
		
		if(isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order)){
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	function get_datatable_pamlek($periode,$np,$mesin_perizinan){
		$this->_get_datatable_pamleks_query($periode,$np,$mesin_perizinan);
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered($periode,$np,$mesin_perizinan){
		$this->_get_datatable_pamleks_query($periode,$np,$mesin_perizinan);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($periode){		
		$this->db->select("a.payment_date");	
		$this->db->from($this->table_pamlek.$periode." a");
		
		return $this->db->count_all_results();
	}
	
	
}

/* End of file m_data_pamlek.php */
/* Location: ./application/models/informasi/m_data_pamlek.php */