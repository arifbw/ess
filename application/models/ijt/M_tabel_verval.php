<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_tabel_verval extends CI_Model
{

	var $table = 'ijt_apply a';
	var $column_order = array(null, 'posisi', 'deskripsi', 'start_date', 'end_date'); //set column field database for datatable orderable
	var $column_search = array('posisi', 'start_date', 'end_date'); //set column field database for datatable searchable 
	var $order = array('a.created_at' => 'asc'); // default order 

	public function __construct()
	{
		parent::__construct();
		//Do your magic here
	}

	private function _get_datatables_query()
	{

		$this->db->select('MAX(a.id) as id, a.np, b.id as user_id, c.nama, f.file, e.is_verval, e.keterangan, a.motivasi, d.nama_jabatan');
		$this->db->from($this->table);
		$this->db->join('usr_pengguna b', 'b.no_pokok = a.np', 'left');
		$this->db->join('mst_karyawan c', 'b.no_pokok = c.no_pokok', 'left');
		$this->db->join('ijt_data d', 'd.id = a.job_id', 'left');
		$this->db->join('ijt_verval e', 'e.apply_id = a.id', 'left');
		$this->db->join('ijt_apply_dokumen f', 'f.ijt_apply_id = a.id', 'left');
		$this->db->where('a.deleted_at', NULL);

		$this->db->group_by('a.id');

		$i = 0;

		foreach ($this->column_search as $item) // loop column 
		{
			if (@$_POST['search']['value']) // if datatable send POST for search
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

		if (@$_POST['length'] != -1)
			$this->db->limit(@$_POST['length'], @$_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered()
	{
		$this->_get_datatables_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($var = null, $month = null)
	{
		$this->db->from($this->table);

		return $this->db->count_all_results();
	}
}

/* End of file m_perencanaan_jadwal_kerja.php */
/* Location: ./application/models/kehadiran/m_perencanaan_jadwal_kerja.php */
