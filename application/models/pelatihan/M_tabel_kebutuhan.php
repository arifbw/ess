<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_tabel_kebutuhan extends CI_Model {

	var $table = 'ess_diklat_kebutuhan_pelatihan';
	var $column_order = array(null, 'np_karyawan','nama','nama_kategori_pelatihan','kode_pelatihan','pelatihan','skala_prioritas','vendor','status_1','status_2'); //set column field database for datatable orderable
	var $column_search = array('np_karyawan','nama','nama_kategori_pelatihan','kode_pelatihan','pelatihan','skala_prioritas','vendor','status_1','status_2'); //set column field database for datatable searchable 
	var $order = array('created_at' => 'desc'); // default order 
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	private function _get_datatables_query($var=null, $month=null)
	{		 
		$this->db->select('ess_diklat_kebutuhan_pelatihan.id');
		$this->db->select('ess_diklat_kebutuhan_pelatihan.np_karyawan');
		$this->db->select('ess_diklat_kebutuhan_pelatihan.nama');
		$this->db->select('ess_diklat_kebutuhan_pelatihan.nama_kategori_pelatihan');
		$this->db->select('ess_diklat_kebutuhan_pelatihan.kode_pelatihan');
		$this->db->select('ess_diklat_kebutuhan_pelatihan.pelatihan');
		$this->db->select('ess_diklat_kebutuhan_pelatihan.skala_prioritas');
		$this->db->select('ess_diklat_kebutuhan_pelatihan.vendor');
		$this->db->select('ess_diklat_kebutuhan_pelatihan.approval_1');
		$this->db->select('ess_diklat_kebutuhan_pelatihan.approval_2');
		$this->db->select('ess_diklat_kebutuhan_pelatihan.status_1');
		$this->db->select('ess_diklat_kebutuhan_pelatihan.status_2');
		$this->db->select('ess_diklat_kebutuhan_pelatihan.approval_1_date');
		$this->db->select('ess_diklat_kebutuhan_pelatihan.approval_2_date');
		$this->db->select('ess_diklat_kebutuhan_pelatihan.created_at');
		$this->db->select('ess_diklat_kebutuhan_pelatihan.updated_by');
		$this->db->from($this->table);	
				
		if($_SESSION["grup"]==4) //jika Pengadministrasi Unit Kerja
		{
			$this->db->where_in('kode_unit', $var);								
		}else
		if($_SESSION["grup"]==5) //jika Pengguna
		{
			$this->db->where_in('np_karyawan', $var);	
		}else
		{
		}

		if(@$month!=0){
            $this->db->where("DATE_FORMAT(created_at,'%Y-%m')", $month);
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

	function get_datatables($var=null, $month=null)
	{
		$this->_get_datatables_query($var,$month);
		
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered($var=null, $month=null)
	{
		$this->_get_datatables_query($var,$month);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($var=null, $month=null)
	{
		$this->db->from($this->table);
		
		if($_SESSION["grup"]==4) //jika Pengadministrasi Unit Kerja
		{
			$this->db->where_in('kode_unit', $var);								
		}else
		if($_SESSION["grup"]==5) //jika Pengguna
		{
			$this->db->where_in('np_karyawan', $var);	
		}else
		{
		}	

		if(@$month!=0){
            $this->db->where("DATE_FORMAT(created_at,'%Y-%m')", $month);
        }
			
		return $this->db->count_all_results();
	}
	
	
}

/* End of file m_perencanaan_jadwal_kerja.php */
/* Location: ./application/models/kehadiran/m_perencanaan_jadwal_kerja.php */