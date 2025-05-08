<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_tabel_pembatalan_cuti extends CI_Model {

	var $table = 'ess_pembatalan_cuti';
	var $column_order = array(null, "np_karyawan", "nama", "uraian", "date", "date_submit"); //set column field database for datatable orderable	
	var $order = array('np_karyawan' => 'asc','date' => 'asc'); // default order 
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	private function _get_datatables_query($tampil_bulan_tahun)
	{				
		$tabel = $this->table;
			
		//dipindah kebawah karena tabel nya dinamis
		$column_search = array("$tabel.np_karyawan","$tabel.nama","$tabel.date","b.uraian","$tabel.date_submit"); //set column field database for datatable 
				
		$this->db->select("$tabel.id");		
		$this->db->select("$tabel.np_karyawan");
		$this->db->select("$tabel.nama");	
		$this->db->select("b.uraian");	
		$this->db->select("$tabel.date");
		$this->db->select("$tabel.date_submit");		
		
		$this->db->from($tabel);	
		$this->db->join('mst_cuti b', "$tabel.absence_type = b.kode_erp", 'left');
		
		$this->db->where("MONTH($tabel.date) ", $tampil_bulan_tahun['bulan']);
		$this->db->where("YEAR($tabel.date) ", $tampil_bulan_tahun['tahun']);	
	
		$this->db->order_by("$tabel.np_karyawan",'ASC');		
		$this->db->order_by("$tabel.date",'ASC');	
		
				
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

	function get_datatables($tampil_bulan_tahun)
	{
		$this->_get_datatables_query($tampil_bulan_tahun);
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered($tampil_bulan_tahun)
	{
		$this->_get_datatables_query($tampil_bulan_tahun);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($tampil_bulan_tahun)
	{
		$tabel = $this->table;
			
				
		$this->db->select("$tabel.id");		
		$this->db->select("$tabel.np_karyawan");
		$this->db->select("$tabel.nama");	
		$this->db->select("b.uraian");	
		$this->db->select("$tabel.date");
		$this->db->select("$tabel.date_submit");
		
		$this->db->from($tabel);	
		$this->db->join('mst_cuti b', "$tabel.absence_type = b.kode_erp", 'left');
		
		$this->db->where("MONTH($tabel.date) ", $tampil_bulan_tahun['bulan']);
		$this->db->where("YEAR($tabel.date) ", $tampil_bulan_tahun['tahun']);	
	
		$this->db->order_by("$tabel.np_karyawan",'ASC');		
		$this->db->order_by("$tabel.date",'ASC');	
		
		
		
		return $this->db->count_all_results();
	}
	
	
}

/* End of file m_perencanaan_jadwal_kerja.php */
/* Location: ./application/models/kehadiran/m_perencanaan_jadwal_kerja.php */