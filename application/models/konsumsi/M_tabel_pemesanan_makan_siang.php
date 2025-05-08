<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_tabel_pemesanan_makan_siang extends CI_Model {

	var $table = 'ess_pemesanan_makan_siang';
	var $column_order = array(); //set column field database for datatable orderable	
	var $column_search = array('nama_unit','gilir','diet','jenis_makanan','lokasi','jumlah'); //set column field database for datatable orderable	
	var $order = array('tanggal_awal' => 'desc'); // default order 
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	private function _get_datatables_query($tampil_bulan_tahun=null)
	{			
		if($tampil_bulan_tahun!=null || $tampil_bulan_tahun!=''){
            //$this->db->like('tanggal_berangkat',$tampil_bulan_tahun);
            $this->db->where('YEAR(tanggal_awal)',explode('_',$tampil_bulan_tahun)[0]);
            $this->db->where('MONTH(tanggal_awal)',explode('_',$tampil_bulan_tahun)[1]);
		}				
		
		$this->db->select('a.id, a.nama_unit, a.lokasi, a.tanggal_awal, a.tanggal_akhir, a.gilir, a.jumlah, b.nama as diet, c.nama as jenis_makanan, a.nomor_pemesanan');	
		$this->db->from($this->table.' a');	
		$this->db->join('mst_diet b', 'a.diet=b.id');	
		$this->db->join('mst_makanan c', 'a.jenis_makanan=c.id');	
		
		/*if($_SESSION["grup"]==4) //jika Pengadministrasi Unit Kerja
		{
			$this->db->where_in('kode_unit', $var);								
		}else
		if($_SESSION["grup"]==5) //jika Pengguna
		{
			$this->db->where('np_karyawan', $var);	
		}else
		{
		}*/			
		
		// $this->db->order_by('np_karyawan','ASC');	
				
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
			$this->db->order_by('tanggal_awal','DESC');	
		}
	}

	function get_datatables($tampil_bulan_tahun=null)
	{
		$this->_get_datatables_query($tampil_bulan_tahun);
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered($tampil_bulan_tahun=null)
	{
		$this->_get_datatables_query($tampil_bulan_tahun);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($tampil_bulan_tahun=null)
	{
        $this->_get_datatables_query($tampil_bulan_tahun);
        return $this->db->count_all_results();
	}
	
}

/* End of file m_perencanaan_jadwal_kerja.php */
/* Location: ./application/models/kehadiran/m_perencanaan_jadwal_kerja.php */