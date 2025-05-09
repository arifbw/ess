<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_tabel_tv_detail extends CI_Model {
    
	var $table = "ess_faskar_tv_detail";
	var $column_order = array('np_karyawan');
	var $column_search = array('np_karyawan','nama_karyawan');
	var $order = array('np_karyawan', 'asc');
	
	public function __construct(){
		parent::__construct();
	}
	
	private function _get_datatables_query($params) {
        $this->db->from($this->table);
        $this->db->where('deleted_at is null');
		
		$this->db->where('faskar_tv_header_id', $params['header_id']);

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

	function get_datatables($params){
		$this->_get_datatables_query($params);
		
		if($_POST['length'] != -1)
		$this->db->limit(@$_POST['length'], @$_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered($params){
		$this->_get_datatables_query($params);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($params){
        $this->_get_datatables_query($params);
		return $this->db->count_all_results();
	}
}