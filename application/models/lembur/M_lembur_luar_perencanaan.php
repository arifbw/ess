<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_lembur_luar_perencanaan extends CI_Model {

	var $mst_karyawan = 'mst_karyawan';
	var $ess_sto = 'ess_sto';
	var $table = 'ess_lembur_luar_perencanaan';
	var $column_order = array();
	var $column_search = array();
	var $order = array('created_at' => 'desc');
	
	public function __construct(){
		parent::__construct();
		$this->table_schema = $this->db->database;
	}

    private function _get_datatables_query() {
		$this->db->select("lembur.*");
		$this->db->from("{$this->table} lembur");
		$this->db->where('lembur.deleted_at IS NULL', null, false);

		if(@$this->input->post('start_date', true)) $this->db->where('tanggal>=', $this->input->post('start_date', true));
		if(@$this->input->post('end_date', true)) $this->db->where('tanggal<=', $this->input->post('end_date', true));
	
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
		$this->db->where([
            'deleted_at' => null,
        ]);
		if(@$this->input->post('start_date', true)) $this->db->where('tanggal>=', $this->input->post('start_date', true));
		if(@$this->input->post('end_date', true)) $this->db->where('tanggal<=', $this->input->post('end_date', true));
        $this->db->from($this->table);
		return $this->db->count_all_results();
	}

    public function insert_multiple($data) {
		$this->db->insert_batch($this->table, $data);
		return $this->db->affected_rows();
	}

    public function update_detail() {
		return $this->db->query("UPDATE {$this->table} lembur
		INNER JOIN {$this->mst_karyawan} mst_karyawan ON mst_karyawan.no_pokok = lembur.np
		SET lembur.nama = mst_karyawan.nama, lembur.kode_unit = mst_karyawan.kode_unit, lembur.nama_unit = mst_karyawan.nama_unit
		WHERE lembur.nama is null AND lembur.kode_unit is null and lembur.nama_unit is null AND lembur.deleted_at IS NULL");
	}
}