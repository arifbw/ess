<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_tabel_data extends CI_Model {

	var $table = 'ijt_data a';
	var $column_order = array(null, null,'deskripsi','start_date','end_date');
	var $column_search = array('a.nama_jabatan','a.start_date','a.end_date'); 
	var $order = array('a.created_at' => 'desc');
	
	public function __construct(){
		parent::__construct();
	}
	
	private function _get_datatables_query()
	{		 
		$this->db->select('a.id, a.kode_unit, a.kode_jabatan, a.nama_jabatan, a.deskripsi, a.gambar, a.start_date, a.end_date, COUNT(b.job_id) as jumlah_pendaftar');		
		$this->db->from($this->table);
		$this->db->join('ijt_apply b', 'b.job_id = a.id', 'left');		
		$this->db->where('a.deleted_at', NULL);
		$this->db->group_by('a.id, a.kode_unit, a.kode_jabatan, a.nama_jabatan, a.deskripsi, a.gambar, a.start_date, a.end_date');
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
	
	
}

/* End of file m_perencanaan_jadwal_kerja.php */
/* Location: ./application/models/kehadiran/m_perencanaan_jadwal_kerja.php */
