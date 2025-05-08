<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_tabel_approver extends CI_Model {
    
	var $table = "ess_approver";
	var $column_order = array(null, 'a.np_karyawan'); //set column field database for datatable orderable
	var $column_search = array('a.np_karyawan','a.nama_karyawan'); //set column field database for datatable searchable 
	var $order = array('id' => 'asc'); // default order 
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	private function _get_datatables_query($var)
	{        
        $this->db->select("a.*, b.kode_unit")->from("$this->table a");
        $this->db->join('mst_karyawan b', 'a.np_karyawan=b.no_pokok', 'left');
				
		if($_SESSION["grup"]==4) //jika Pengadministrasi Unit Kerja
		{
			$this->db->where_in('b.kode_unit', $var);								
		} else if($_SESSION["grup"]==5) //jika Pengguna
		{
			$this->db->where_in('b.no_pokok', $var);	
		} else
		{
		}
				
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

	function get_datatables($var)
	{
		$this->_get_datatables_query($var);
		
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered($var)
	{
		$this->_get_datatables_query($var);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($var)
	{
        $this->_get_datatables_query($var);	
			
		return $this->db->count_all_results();
	}
	
	
}

/* End of file m_perencanaan_jadwal_kerja.php */
/* Location: ./application/models/kehadiran/m_perencanaan_jadwal_kerja.php */