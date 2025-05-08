<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_tabel_kehadiran_cuti_bersama extends CI_Model {

	var $cubes = 'mst_cuti_bersama';
	var $table = 'ess_cico';
	var $cuti = 'ess_cuti';
	var $column_order = array();
	var $column_search = array('cico.np_karyawan', 'cico.nama', 'cico.nama_unit');
	var $order = array('cico.id' => 'asc');
	
	public function __construct(){
		parent::__construct();
	}

    private function _get_datatables_query() {
		$bulan = date('Y_m', strtotime($this->input->post('bulan', true)));
		$table = "ess_cico_{$bulan}";
		$this->db->select("cico.*, cubes.deskripsi");
		$this->db->from("{$table} cico");
		$this->db->join("{$this->cubes} cubes", 'cubes.tanggal = cico.dws_tanggal', 'INNER');

		if(@$this->input->post('status', true)){
			switch ($this->input->post('status', true)) {
				case '1':
					$this->db->group_start();
					$this->db->where('id_cuti!=', '');
					$this->db->where('id_cuti IS NOT NULL', null, false);
					$this->db->group_end();
					break;
				case '2':
					$this->db->group_start();
					$this->db->where('id_cuti', '');
					$this->db->or_where('id_cuti IS NULL', null, false);
					$this->db->group_end();
					break;
				default:
					# code...
					break;
			}
		}
		// $this->db->join("{$this->cuti} cuti", 'cuti.np_karyawan = cico.np_karyawan AND cuti.start_date = cico.dws_tanggal', 'LEFT');
	
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
		if($_POST['length'] != -1) $this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered() {
		$this->_get_datatables_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

    public function count_all() {
		$this->_get_datatables_query();
		return $this->db->count_all_results();
	}

	function bulan_cuti_bersama(){
		$this->db->select("DISTINCT DATE_FORMAT(tanggal,'%Y-%m') AS bulan");
		$this->db->order_by("tanggal", "DESC");
		return $this->db->get($this->cubes)->result();
	}

	function cek_tabel($month){
		$bulan = date('Y_m', strtotime($month));
		$table = "ess_cico_{$bulan}";
		$this->db->where("TABLE_SCHEMA", $this->db->database);
		$this->db->where("TABLE_NAME", $table);
		return $this->db->get('information_schema.TABLES')->row();
	}
}