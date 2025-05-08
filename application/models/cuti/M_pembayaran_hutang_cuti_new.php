<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_pembayaran_hutang_cuti_new extends CI_Model { 

	var $jatah_cubes = 'cuti_cubes_jatah';
	var $pembayaran = 'cuti_hutang_pembayaran';
	var $mst_karyawan = 'mst_karyawan';
	var $table = 'cuti_hutang';
	var $column_order = array(null, 'hutang.no_pokok', 'karyawan.nama_unit', 'hutang.hutang', null);
	var $column_search = array('karyawan.no_pokok', 'karyawan.nama');
	var $order = array();
	
	public function __construct(){
		parent::__construct();
	}

    private function _get_datatables_query() {
		$this->db->select("hutang.*, karyawan.nama, karyawan.nama_unit, a.total_bulan, a.total_hari, a.sisa_bulan, a.sisa_hari");
		$this->db->from("{$this->table} hutang");
		$this->db->join("{$this->mst_karyawan} karyawan", 'karyawan.no_pokok = hutang.no_pokok', 'LEFT');
		$this->db->join("(SELECT jatah.* 
		FROM cuti_cubes_jatah jatah
		INNER JOIN (
			SELECT x.np_karyawan, MAX(x.tanggal_kadaluarsa) AS tanggal_kadaluarsa
			FROM cuti_cubes_jatah x
			WHERE x.tanggal_kadaluarsa > DATE(NOW())
			GROUP BY x.np_karyawan
		) jatah_cubes ON jatah.np_karyawan = jatah_cubes.np_karyawan AND jatah.tanggal_kadaluarsa = jatah_cubes.tanggal_kadaluarsa
		WHERE jatah.tanggal_kadaluarsa > DATE(NOW())) a", 'a.np_karyawan = hutang.no_pokok');
		$this->db->where('hutang.deleted_at IS NULL', null, false);

		if(@$this->input->post('unit', true)!='00000') $this->db->where('karyawan.kode_unit', $this->input->post('unit', true));
	
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

	// crud
    public function get_by_np($np) {
		$this->db->where([
            'deleted_at' => null,
            'no_pokok' => $np
        ]);
        return $this->db->get($this->table)->row();
	}

    public function update_hutang($data, $where) {
		$this->db->where($where);
		return $this->db->update($this->table, $data);
	}

	public function get_histori($np) {
		$this->db->where([
            'no_pokok' => $np
        ]);
		$this->db->order_by('created_at', 'DESC');
        return $this->db->get($this->pembayaran)->result();
	}

    public function insert_bayar($data) {
		return $this->db->insert($this->pembayaran, $data);
	}

    public function update_bayar($data, $where) {
		$this->db->where($where);
		return $this->db->update($this->pembayaran, $data);
	}
}