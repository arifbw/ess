<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_poster_tender extends CI_Model {

	var $table = 'ijt_poster';
	var $column_order = array(null, null,null);
	var $column_search = array(); 
	var $order = array('poster.created_at' => 'desc');
	
	public function __construct(){
		parent::__construct();
	}
	
	private function _get_datatables_query()
	{		 
		$this->db->from("{$this->table} poster");		
		$this->db->where('poster.deleted_at', NULL);
		$i = 0;
	
		foreach ($this->column_search as $item) // loop column 
		{
			if($_POST['search']['value']) // if datatable send POST for search
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
		else if(isset($this->order))
		{
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	function get_datatables()
	{
		$this->_get_datatables_query();
		
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered()
	{
		$this->_get_datatables_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($var=null, $month=null)
	{
		$this->_get_datatables_query();	
		return $this->db->count_all_results();
	}

	// crud
	function insert_data($data){
		$this->db->insert($this->table, $data);

		if ($this->db->affected_rows() > 0) {
			return $this->db->insert_id();
		} else {
			return "0";
		}
	}

	function update_data($where, $data){
		if (empty($where) || empty($data)) {
			return false;
		}

		$this->db->where('id', $where);
		$result = $this->db->update($this->table, $data);

		if ($result) {
			return true;
		} else {
			return false;
		}
	}
}
