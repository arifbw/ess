<?php
defined('BASEPATH') OR exit('No direct script access allowed');

Class M_pembayaran_hutang_cuti extends CI_Model {

	private $table="cuti_hutang";
	private $table_karyawan="mst_karyawan";
	var $column_order = array(null, 'no_pokok'); //set column field database for datatable orderable
	var $column_search = array('a.no_pokok',"b.nama"); //set column field database for datatable searchable 
	var $order = array('b.no_pokok' => 'asc'); // default order 
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	private function _get_datatables_query(){
		$this->db->select("a.*");
		$this->db->select("b.nama");
		$this->db->from($this->table." a");	
		$this->db->join($this->table_karyawan." b","a.no_pokok=b.no_pokok","left");
		$this->db->order_by("b.no_pokok IS NOT NULL",'DESC',false);
		$this->db->order_by("a.no_pokok",'asc');
				
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

	public function count_all()
	{
		$this->db->from($this->table);
		
		return $this->db->count_all_results();
	}
	
	/* public function data_pembayaran_hutang_cuti($no_pokok,$tahun){
		$data = $this->db->from($this->table)
						 ->where('no_pokok',$no_pokok)
						 ->where('tahun',$tahun)
						 ->get()
						 ->result_array()[0];
		return $data;
	} */
}

/* End of file m_pembayaran_hutang_cuti.php */
/* Location: ./application/models/osdm/m_pembayaran_hutang_cuti.php */