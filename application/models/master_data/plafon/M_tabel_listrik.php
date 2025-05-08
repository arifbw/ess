<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_tabel_listrik extends CI_Model {
    
	//var $table = "pamlek_data_2018_11";
	var $column_order = array(null, 'np_karyawan', 'nama_anak', 'tanggal_lahir_anak', 'status_pekerjaan','nama_instansi', 'keterangan', 'tempat_lahir_anak', 'anak_ke', null, null); //set column field database for datatable orderable
	var $column_search = array('np_karyawan','nama_karyawan','nama_anak','status_pekerjaan','nama_instansi','tanggal_lahir_anak','keterangan', 'tempat_lahir_anak', 'anak_ke'); //set column field database for datatable searchable 
	var $order = array('a.np_karyawan', 'a.id'); // default order 
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	private function _get_datatables_query() {
        $this->db->select("*")->from("mst_plafon_listrik a");
        $this->db->where('deleted_at is null');
        
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
		else if(isset($this->order))
		{
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	function get_datatables($var=null, $get_tbl=null)
	{
		$this->_get_datatables_query($var, $get_tbl);
		
		if($_POST['length'] != -1)
		$this->db->limit(@$_POST['length'], @$_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered($var=null, $get_tbl=null)
	{
		$this->_get_datatables_query($var, $get_tbl);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($var=null, $get_tbl=null)
	{
        $this->_get_datatables_query($var, $get_tbl);
		return $this->db->count_all_results();
	}
}