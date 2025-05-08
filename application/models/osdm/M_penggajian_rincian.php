<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_penggajian_rincian extends CI_Model {
	var $table_karyawan = 'erp_payslip_karyawan';
	var $table_mst_karyawan = 'mst_karyawan';
	var $column_order = array(null, 'kode_unit', 'kode_jabatan', 'np_karyawan'); //set column field database for datatable orderable	
	var $order = array('length(kode_unit)' => 'DESC', 'kode_unit' => 'ASC', 'kode_jabatan' => 'ASC', 'np_karyawan' => 'ASC'); // default order 
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	private function _get_datatables_query($id_header)
	{
		//dipindah kebawah karena tabel nya dinamis
		$column_search = array("np_karyawan","nama","kode_unit","nama_unit","kode_jabatan","nama_jabatan"); //set column field database for datatable 
		
		$this->db->select("nama_payslip");
		$this->db->select("np_karyawan");
		$this->db->select("nama");
		$this->db->select("kode_unit");
		$this->db->select("nama_unit");
		$this->db->select("kode_jabatan");
		$this->db->select("nama_jabatan");
		$this->db->select("with_payslip");
		$this->db->from($this->table_karyawan);
		$this->db->where("id_payslip_header",$id_header);

		$i = 0;
	
		foreach ($column_search as $item) // loop column 
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

				if(count($column_search) - 1 == $i) //last loop
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
			for($i=0;$i<count($order);$i++){
				$this->db->order_by(key($order), $order[key($order)]);
				next($order);
			}
		}
	}

	function get_datatables($id_header)
	{
		$this->_get_datatables_query($id_header);
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();//echo $this->db->last_query();
		return $query->result();
	}

	function count_filtered($id_header)
	{
		$this->_get_datatables_query($id_header);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($id_header)
	{		
		$this->_get_datatables_query($id_header);
		$query = $this->db->get();
		return $query->num_rows();
	}
}

/* End of file m_penggajian_rincian.php */
/* Location: ./application/models/osdm/m_penggajian_rincian.php */