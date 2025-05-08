<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_tabel_persetujuan_cuti_sdm extends CI_Model {

	var $table = 'ess_cuti';
	var $column_order = array(null, 'np_karyawan', 'absence_type', 'start_date', 'end_date', 'jumlah_hari', 'alasan'); //set column field database for datatable orderable
	var $column_search = array('np_karyawan','absence_type','start_date','end_date','jumlah_hari','alasan'); //set column field database for datatable searchable 
	var $order = array('np_karyawan' => 'asc'); // default order 
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	private function _get_datatables_query($filter)
	{
		$this->db->select('ess_cuti.id');
		$this->db->select('ess_cuti.np_karyawan');
		$this->db->select('ess_cuti.absence_type');
		$this->db->select('ess_cuti.start_date');
		$this->db->select('ess_cuti.end_date');
		$this->db->select('ess_cuti.jumlah_bulan');
		$this->db->select('ess_cuti.jumlah_hari');
		$this->db->select('ess_cuti.alasan');
		$this->db->select('ess_cuti.approval_1');
		$this->db->select('ess_cuti.approval_2');
		$this->db->select('ess_cuti.status_1');
		$this->db->select('ess_cuti.status_2');
		$this->db->select('ess_cuti.approval_1_date');
		$this->db->select('ess_cuti.approval_2_date');
		$this->db->select('ess_cuti.approval_sdm');
		$this->db->select('ess_cuti.approval_sdm_by');
		$this->db->select('ess_cuti.approval_sdm_date');
		$this->db->select('ess_cuti.created_at');
		$this->db->select('ess_cuti.created_by');		
		$this->db->select('nama');
		$this->db->select('mst_cuti.uraian');
		$this->db->from($this->table);
		$this->db->join('mst_cuti', 'mst_cuti.kode_erp = ess_cuti.absence_type', 'left');

		if($filter['filter_belum']=='1')
		{			
			$this->db->where("((ess_cuti.status_1='0' OR ess_cuti.status_1 = '' OR ess_cuti.status_1 is null) AND (ess_cuti.status_2='0' OR ess_cuti.status_2 = '' OR ess_cuti.status_2 is null))");
			   
			
		}
		
		if($filter['filter_atasan_1']==1)
		{			
			$this->db->or_where("(ess_cuti.status_1='1')");	   		
		}
		
		if($filter['filter_atasan_2']==1)
		{			
			$this->db->or_where("(ess_cuti.status_2='1')");	
		}
		
		if($filter['filter_sdm']==1)
		{			
			$this->db->or_where('ess_cuti.approval_sdm', '1');		
		}
		
		if($filter['filter_belum_sdm']==1)
		{			
		
			$this->db->or_where("(ess_cuti.approval_sdm='0' OR ess_cuti.approval_sdm='' OR ess_cuti.approval_sdm is null)");	
		}
		
		if($filter['filter_batal']==1)
		{			
			$this->db->or_where("(ess_cuti.status_1='3' OR ess_cuti.status_2 = '3')");			
		}
		
		if($filter['filter_tolak_atasan']==1)
		{			
			$this->db->or_where("(ess_cuti.status_1='2' OR ess_cuti.status_2 = '2')");				
		}
		
		if($filter['filter_tolak_sdm']==1)
		{			
			$this->db->or_where("(ess_cuti.approval_sdm='2')");				
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

	function get_datatables($filter)
	{
		$this->_get_datatables_query($filter);
		
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered($filter)
	{
		$this->_get_datatables_query($filter);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($filter)
	{
		$this->db->from($this->table);
		$this->db->join('mst_cuti', 'mst_cuti.kode_erp = ess_cuti.absence_type', 'left');
		
			if($filter['filter_belum']==1)
		{
			$this->db->or_where('ess_cuti.status_1', '0');	
			$this->db->or_where('ess_cuti.status_2', '0'); 
			$this->db->or_where('ess_cuti.status_1', '');	
			$this->db->or_where('ess_cuti.status_2', ''); 	
		}
		
		if($filter['filter_atasan_1']==1)
		{			
			$this->db->or_where('ess_cuti.status_1', '1');		
		}
		
		if($filter['filter_atasan_2']==1)
		{			
			$this->db->or_where('ess_cuti.status_2', '1');		
		}
		
		if($filter['filter_sdm']==1)
		{			
			$this->db->or_where('ess_cuti.approval_sdm', '1');		
		}
		
		return $this->db->count_all_results();
	}
	
	
}

/* End of file m_tabel_persetujuan_cuti_sdm.php */
/* Location: ./application/models/kehadiran/m_tabel_persetujuan_cuti_sdm.php */