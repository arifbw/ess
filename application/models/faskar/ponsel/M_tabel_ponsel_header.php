<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_tabel_ponsel_header extends CI_Model {
    
	var $table = "ess_faskar_ponsel_header";
	var $column_order = array('pemakaian_bulan');
	var $column_search = array();
	var $order = array('pemakaian_bulan', 'desc');
	
	public function __construct(){
		parent::__construct();
	}
	
	private function _get_datatables_query() {
        $this->db->select("{$this->table}.*, (SELECT count(kode) FROM ess_faskar_ponsel_detail WHERE faskar_ponsel_header_id={$this->table}.id AND deleted_at IS NULL) as jumlah_data");
        $this->db->from($this->table);
        $this->db->order_by('created_at','DESC');

		$i = 0;
	
		foreach ($this->column_search as $item) // loop column 
		{
			if(@$_POST['search']['value']) // if datatable send POST for search
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

				if(count($this->column_search) - 1 == $i) //last loop
					$this->db->group_end(); //close bracket
			}
			$i++;
		}
		
		if(isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else 
		{
			if(isset($this->order))
				$order = $this->order;
		}
	}

	function get_datatables(){
		$this->_get_datatables_query();
		
		if($_POST['length'] != -1)
		$this->db->limit(@$_POST['length'], @$_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered(){
		$this->_get_datatables_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all(){
        $this->_get_datatables_query();
		return $this->db->count_all_results();
	}
}