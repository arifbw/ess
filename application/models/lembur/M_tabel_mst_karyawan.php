<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_tabel_mst_karyawan extends CI_Model {

	var $table = 'mst_karyawan';
	var $column_order = array(null, 'ess_lembur_transaksi.no_pokok',); //set column field database for datatable orderable
	var $column_search = array('mst_karyawan.no_pokok', 'mst_karyawan.nama'); //set column field database for datatable searchable 
	var $order = array('no_pokok' => 'asc'); // default order 
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	private function _get_datatables_query()
	{
		$this->db->from($this->table);
	
		//jika Pengadministrasi Unit Kerja
		if($_SESSION["grup"]==4) {
			$ada_data=0;
			$var=array();
			$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
			//looping list_pengadministrasi
			foreach ($list_pengadministrasi as $data) {
				array_push($var,$data['kode_unit']);
				$ada_data=1;
			}
			if($ada_data==0)
				$var='';
		}
		else if($_SESSION["grup"]==5) //jika Pengguna
			$var 	= $_SESSION["no_pokok"];
		else
			$var = 1;

		if($_SESSION["grup"]==4) //jika Pengadministrasi Unit Kerja
			$this->db->where_in('mst_karyawan.kode_unit', $var);
		else if($_SESSION["grup"]==5) //jika Pengguna
			$this->db->where_in('mst_karyawan.no_pokok', $var);
	
	
		$i = 0;
		foreach ($this->column_search as $item) { // loop column 
			if($_POST['search']['value']) { // if datatable send POST for search
				if($i===0) { // first loop
					$this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
					$this->db->like($item, $_POST['search']['value']);
				}
				else {
					$this->db->or_like($item, $_POST['search']['value']);
				}

				if(count($this->column_search) - 1 == $i) //last loop
					$this->db->group_end(); //close bracket
			}
			$i++;
		}
		
		if(isset($_POST['order'])) { // here order processing
			$this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order)) {
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	function get_datatables() {
		$this->_get_datatables_query();
		
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered() {
		$this->_get_datatables_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all() {
		$this->db->from($this->table);
	
		//jika Pengadministrasi Unit Kerja
		if($_SESSION["grup"]==4) {
			$ada_data=0;
			$var=array();
			$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
			//looping list_pengadministrasi
			foreach ($list_pengadministrasi as $data) {
				array_push($var,$data['kode_unit']);
				$ada_data=1;
			}
			if($ada_data==0)
				$var='';
		}
		else if($_SESSION["grup"]==5) //jika Pengguna
			$var 	= $_SESSION["no_pokok"];
		else
			$var = 1;

		if($_SESSION["grup"]==4) //jika Pengadministrasi Unit Kerja
			$this->db->where_in('mst_karyawan.kode_unit', $var);
		else if($_SESSION["grup"]==5) //jika Pengguna
			$this->db->where_in('mst_karyawan.no_pokok', $var);
		
		return $this->db->count_all_results();
	}
	
	
}

/* End of file m_tabel_persetujuan_cuti_sdm.php */
/* Location: ./application/models/kehadiran/m_tabel_persetujuan_cuti_sdm.php */