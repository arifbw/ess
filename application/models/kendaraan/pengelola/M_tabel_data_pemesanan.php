<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_tabel_data_pemesanan extends CI_Model {

	var $table = 'ess_pemesanan_kendaraan';
	var $column_order = array(); //set column field database for datatable orderable	
	var $column_search = array('np_karyawan','nama','tujuan','nomor_pemesanan'); //set column field database for datatable orderable	
	var $order = array('np_karyawan' => 'asc','tanggal_peminjaman' => 'desc'); // default order 
	
	public function __construct(){
		parent::__construct();
	}
	
	private function _get_datatables_query($var,$tampil_bulan_tahun)
	{			
		if($tampil_bulan_tahun!=''){
            $this->db->where('YEAR(tanggal_berangkat)',explode('_',$tampil_bulan_tahun)[0]);
            $this->db->where('MONTH(tanggal_berangkat)',explode('_',$tampil_bulan_tahun)[1]);
		}				
        $this->db->where("(np_karyawan=$var OR np_karyawan_pic=$var)");
        $this->db->where('insert_as_pengelola',1);
		
		$this->db->from($this->table);		
		
		$this->db->order_by('np_karyawan','ASC');	
		$this->db->order_by('tanggal_berangkat','DESC');
		
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

	function get_datatables($var,$tampil_bulan_tahun)
	{
		$this->_get_datatables_query($var,$tampil_bulan_tahun);
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered($var,$tampil_bulan_tahun)
	{
		$this->_get_datatables_query($var,$tampil_bulan_tahun);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($var,$tampil_bulan_tahun)
	{
        $this->_get_datatables_query($var,$tampil_bulan_tahun);
        return $this->db->count_all_results();
	}
}

/* End of file m_perencanaan_jadwal_kerja.php */
/* Location: ./application/models/kehadiran/m_perencanaan_jadwal_kerja.php */