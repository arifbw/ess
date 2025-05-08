<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_plafon_lembur extends CI_Model
{

	var $table = 'plafon_lembur';
	var $sto = 'ess_sto';
	var $column_order = array(null, 'kode_unit', 'nominal', null); //set column field database for datatable orderable
	var $column_search = array('plafon.kode_unit', 'sto.object_name', 'sto.object_name_lengkap'); //set column field database for datatable searchable 
	var $order = array('created_at' => 'desc'); // default order 

	public function __construct()
	{
		parent::__construct();
		//Do your magic here
	}

	private function _get_datatables_query()
	{
		$this->db->select('plafon.*, sto.object_name');
		$this->db->from("{$this->table} plafon");
		$this->db->join("{$this->sto} sto", "sto.object_abbreviation = plafon.kode_unit", "INNER");
		$this->db->where('plafon.deleted_at', null);
		
		$tahun = $this->input->post('tahun');
		if ($tahun != null) {
			$this->db->where('plafon.tahun', $tahun);
		}

		$i = 0;

		foreach ($this->column_search as $item) // loop column 
		{
			if ($_POST['search']['value']) // if datatable send POST for search
			{

				if ($i === 0) // first loop
				{
					$this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
					$this->db->like($item, $_POST['search']['value']);
				} else {
					$this->db->or_like($item, $_POST['search']['value']);
				}

				if (count($this->column_search) - 1 == $i) //last loop
					$this->db->group_end(); //close bracket
			}
			$i++;
		}

		if (isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} else if (isset($this->order)) {
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	function get_datatables()
	{
		$this->_get_datatables_query();

		if ($_POST['length'] != -1)
			$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered()
	{
		$this->_get_datatables_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all()
	{
		$this->_get_datatables_query();
		return $this->db->count_all_results();
	}

	function getDataById($id) {
		$this->db->where('id', $id);
		$this->db->where('deleted_at', null);
		$data = $this->db->get('plafon_lembur');

		return $data->row();
	}

	function get_sto_divisi(){
		$this->db->select('object_abbreviation, object_name, object_name_lengkap');
		$this->db->where('object_type','O');
		$this->db->where("SUBSTR(object_abbreviation,2,1)!='0' AND SUBSTR(object_abbreviation,3,3)='000'", null, false);
		return $this->db->get($this->sto)->result();
	}

	function get_by_tahun_sto($tahun, $unit) {
		if(substr($unit, 1, 1)!='0'){
			$this->db->select('nominal');
			$this->db->where('kode_unit', $unit);
		} else if(substr($unit, 1, 1)=='0'){
			$this->db->select('SUM(nominal) AS nominal');
			$this->db->like('kode_unit', substr($unit, 0, 1), 'AFTER');
		} else{
			$this->db->select('nominal');
			$this->db->where('kode_unit', null);
		}
		
		$this->db->where('tahun', $tahun);
		$this->db->where('deleted_at IS NULL', null, false);
		$data = $this->db->get($this->table);
		return $data->row();
	}
}

/* End of file m_perencanaan_jadwal_kerja.php */
/* Location: ./application/models/kehadiran/m_perencanaan_jadwal_kerja.php */