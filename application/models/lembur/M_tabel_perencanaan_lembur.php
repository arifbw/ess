<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_tabel_perencanaan_lembur extends CI_Model {

	var $detail = 'ess_perencanaan_lembur_detail';
	var $ess_sto = 'ess_sto';
	var $table = 'ess_perencanaan_lembur';
	var $column_order = array();
	var $column_search = array('sto.object_name','rencana.kode_unit');
	var $order = array('created_at' => 'asc');
	
	public function __construct(){
		parent::__construct();
		$this->table_schema = $this->db->database;
	}

    private function _get_datatables_query() {
		$this->db->select("rencana.*, sto.object_name");
		$this->db->from("{$this->table} rencana");
		$this->db->join("{$this->ess_sto} sto", 'sto.object_abbreviation = rencana.kode_unit', 'LEFT');
		$this->db->where('rencana.deleted_at IS NULL', null, false);

		if(@$this->input->post('periode', true)!=''){
			$periode = explode('|', $this->input->post('periode', true));
			$tanggal_mulai = $periode[0];
			$tanggal_selesai = $periode[1];
			$this->db->where('tanggal_mulai', $tanggal_mulai);
			$this->db->where('tanggal_selesai', $tanggal_selesai);
		}

		if(@$this->session->userdata('grup')=='5'){
			$np = $this->session->userdata('no_pokok');
			$this->db->where("(rencana.id IN (SELECT detail.perencanaan_lembur_id FROM {$this->detail} detail WHERE FIND_IN_SET('{$np}', detail.list_np) AND detail.deleted_at IS NULL ))", null, false);
		} else if(@$this->session->userdata('grup')=='4'){
			$list_pengadministrasi = $this->session->userdata('list_pengadministrasi');
			$array_unit = array_map(function($item) {
				return $item['kode_unit'];
			}, $list_pengadministrasi);
			if($array_unit!=[]) $this->db->where_in('rencana.kode_unit', $array_unit);
			else $this->db->where('rencana.id', 0);
		} else if(@$this->session->userdata('grup')=='31'){
			$sess_kode_unit = $this->session->userdata('kode_unit');
			$this->db->where("SUBSTR(rencana.kode_unit,1,1)", substr($sess_kode_unit, 0, 1));
		}
	
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
            'rencana.deleted_at' => null,
        ]);
        $this->db->from("{$this->table} rencana");

		if(@$this->session->userdata('grup')=='5'){
			$np = $this->session->userdata('no_pokok');
			$this->db->where("(rencana.id IN (SELECT detail.perencanaan_lembur_id FROM {$this->detail} detail WHERE FIND_IN_SET('{$np}', detail.list_np) AND detail.deleted_at IS NULL ))", null, false);
		} else if(@$this->session->userdata('grup')=='4'){
			$list_pengadministrasi = $this->session->userdata('list_pengadministrasi');
			$array_unit = array_map(function($item) {
				return $item['kode_unit'];
			}, $list_pengadministrasi);
			if($array_unit!=[]) $this->db->where_in('rencana.kode_unit', $array_unit);
			else $this->db->where('rencana.id', 0);
		} else if(@$this->session->userdata('grup')=='31'){
			$sess_kode_unit = $this->session->userdata('kode_unit');
			$this->db->where("SUBSTR(rencana.kode_unit,1,1)", substr($sess_kode_unit, 0, 1));
		}
		return $this->db->count_all_results();
	}
}