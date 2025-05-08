<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_tabel_listrik_detail extends CI_Model {
    
	var $table = "";
	var $column_order = array('np_karyawan');
	var $column_search = array('np_karyawan','nama_karyawan');
	var $order = array('ess_faskar_listrik_header.pemakaian_bulan', 'desc');
	
	public function __construct(){
		parent::__construct();
	}
	
	private function _get_datatables_query($params) {
        $this->db->select('ess_faskar_listrik_detail.*, ess_faskar_listrik_header.lokasi, ess_faskar_listrik_header.pemakaian_bulan, ess_faskar_listrik_header.pembayaran_bulan, ess_faskar_listrik_header.submit_date, ess_faskar_listrik_header.approval_status, ess_faskar_listrik_header.approval_atasan_np, ess_faskar_listrik_header.approval_atasan_nama, ess_faskar_listrik_header.approval_atasan_at, ess_faskar_listrik_header.approval_atasan_alasan, ess_faskar_listrik_header.alasan_sdm, ess_faskar_listrik_header.approval_sdm_at');
        $this->db->from('ess_faskar_listrik_detail');
        $this->db->join('ess_faskar_listrik_header', 'ess_faskar_listrik_header.id=ess_faskar_listrik_detail.faskar_listrik_header_id');
		$this->db->where('ess_faskar_listrik_detail.np_karyawan', $params['np']);
		$this->db->where('approval_status', '3');

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