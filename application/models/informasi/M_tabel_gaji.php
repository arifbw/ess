<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_tabel_gaji extends CI_Model {

	var $table_header = 'erp_payslip_header';
	var $table_karyawan = 'erp_payslip_karyawan';
	var $column_order = array(null, 'payment_date'); //set column field database for datatable orderable	
	var $order = array('payment_date' => 'desc'); // default order 
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	private function _get_datatables_query($np)
	{
		//dipindah kebawah karena tabel nya dinamis
		$column_search = array("a.payment_date"); //set column field database for datatable 
		
		$this->db->select("b.nama_payslip");
		$this->db->select("b.id id_payslip_karyawan");
		$this->db->from($this->table_header." a");
		$this->db->join($this->table_karyawan." b","a.id=b.id_payslip_header");
		$this->db->where("b.np_karyawan", $np);
		$this->db->where("a.start_display <= ", "NOW()",false);
		$this->db->order_by("a.payment_date",'desc');
				
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
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	function get_datatables($np)
	{
		$this->_get_datatables_query($np);
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered($np)
	{
		$this->_get_datatables_query($np);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($np)
	{		
		$this->db->select("a.payment_date");	
		$this->db->from($this->table_header." a");
		$this->db->join($this->table_karyawan." b","a.id=b.id_payslip_header");
		$this->db->where("b.np_karyawan", $np);
		$this->db->where("a.start_display <= ", "NOW()",false);
		$this->db->order_by("a.payment_date",'desc');
		
		return $this->db->count_all_results();
	}
	
	
}

/* End of file m_payslip.php */
/* Location: ./application/models/kehadiran/m_payslip.php */