<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_tabel_laporan_bantuan_cubes extends CI_Model {

	var $table = 'ess_cuti';
	var $column_order = array(null, 'np_karyawan', 'absence_type', 'start_date', 'end_date', 'jumlah_hari', 'alasan'); //set column field database for datatable orderable
	var $column_search = array('np_karyawan','absence_type','start_date','end_date','jumlah_hari','alasan'); //set column field database for datatable searchable 
	var $order = array('np_karyawan' => 'asc'); // default order 
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	private function _get_datatables_query($tahun_bulan=null) {
		if ($tahun_bulan != 'all' || $tahun_bulan == null)
			$this->db->where('date_format(bantuan_cuti_besar_tanggal, "%Y-%m")="'.$tahun_bulan.'"');
		$this->db->select('ess_cuti.*, tahun, bantuan_cuti_besar_tanggal');
		$this->db->from($this->table);
		$this->db->join('cuti_cubes_jatah', 'cuti_cubes_jatah.bantuan_cuti_besar_id_cuti = ess_cuti.id');

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

	function get_datatables($tahun_bulan=null) {
		$this->_get_datatables_query($tahun_bulan);
		
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered($tahun_bulan=null) {
		$this->_get_datatables_query($tahun_bulan);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($tahun_bulan=null) {
		if ($tahun_bulan != 'all' || $tahun_bulan == null)
			$this->db->where('date_format(bantuan_cuti_besar_tanggal, "%Y-%m")="'.$tahun_bulan.'"');
		$this->db->select('ess_cuti.*, tahun, bantuan_cuti_besar_tanggal');
		$this->db->from($this->table);
		$this->db->join('cuti_cubes_jatah', 'cuti_cubes_jatah.bantuan_cuti_besar_id_cuti = ess_cuti.id');
		
		return $this->db->count_all_results();
	}
	
	
}

/* End of file m_tabel_persetujuan_cuti_sdm.php */
/* Location: ./application/models/kehadiran/m_tabel_persetujuan_cuti_sdm.php */