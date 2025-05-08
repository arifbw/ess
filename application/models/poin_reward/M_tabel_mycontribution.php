<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_tabel_mycontribution extends CI_Model
{

	//var $table = "pamlek_data_2018_11";
	var $column_order = array(
		null,
		'nama_karyawan',
		'np_karyawan',
		'perihal',
		'dokumen',
		'poin',
		'status_verifikasi',
		'tanggal_submit',
		'tanggal_dokumen',
	); //set column field database for datatable orderable
	var $column_search = array(
		'np_karyawan',
		'nama_karyawan',
		'perihal',
		'status_verifikasi'

	); //set column field database for datatable searchable 
	var $order = array('a.np_karyawan', 'a.id'); // default order 

	public function __construct()
	{
		parent::__construct();
		//Do your magic here
	}

	private function _get_datatables_query($var = null, $get_tbl = null)
	{
		$awal_bulan = date('Y-m-01');
		$akhir_bulan = date('Y-m-t');

		$this->db->select("*")->from("my_contribution a");
		$this->db->where('deleted_at is null');
		if ($get_tbl == 'persetujuan') {
			$this->db->where('approval_np', $_SESSION['no_pokok']);
		} else if ($get_tbl == 'verifikasi') {
			if ($this->input->post('karyawan') != 'all') {
				$this->db->where('np_karyawan', $this->input->post('karyawan'));
			}
			if ($this->input->post('satuan_kerja') != 'all') {
				$this->db->where('kode_unit', $this->input->post('satuan_kerja'));
			}
		} else {
			if ($_SESSION["grup"] == 4) {
				$this->db->where_in('a.kode_unit', $var);
			} else if ($_SESSION["grup"] == 5) {
				$this->db->where_in('a.np_karyawan', $var);
			} elseif ($_SESSION['grup'] == 30) {

				if (!empty($this->input->post('karyawan')) && $this->input->post('karyawan') != 'all') {
					$this->db->where('np_karyawan', $this->input->post('karyawan'));
				}
				if (!empty($this->input->post('satuan_kerja')) && $this->input->post('satuan_kerja') != 'all') {
					$this->db->where('kode_unit', $this->input->post('satuan_kerja'));
				}
			}
		}
		
		if ($this->input->post('status') != 'all') {
			$this->db->where('status_verifikasi', $this->input->post('status'));
		}
		if ($this->input->post('start_date')) {
			$this->db->where('tanggal_submit >=', $this->input->post('start_date'));
		} else {
			$this->db->where('tanggal_submit >=', $awal_bulan);
		}
		if ($this->input->post('end_date')) {
			$this->db->where('tanggal_submit <=', $this->input->post('end_date'));
		} else {

			$this->db->where('tanggal_submit <=', $akhir_bulan);
		}

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
		} else {
			$this->db->order_by('a.created_at', 'DESC');
		}
	}

	function get_datatables($var = null, $get_tbl = null)
	{
		$this->_get_datatables_query($var, $get_tbl);

		if ($_POST['length'] != -1)
			$this->db->limit(@$_POST['length'], @$_POST['start']);

		$data = $this->db->get()->result();

		for ($i = 0; $i < count($data); $i++) {
			if (!empty($data[$i]->dokumen)) $data[$i]->url = base_url() . "uploads/mycontribution/dokumen/" . $data[$i]->dokumen;
		}
		return $data;
	}

	function count_filtered($var = null, $get_tbl = null)
	{
		$this->_get_datatables_query($var, $get_tbl);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($var = null, $get_tbl = null)
	{
		$this->_get_datatables_query($var, $get_tbl);
		return $this->db->count_all_results();
	}

	public function _get_excel($var, $table_name, $get, $jenis = null)
	{
		if ($get['np_karyawan'] != '') {
			$np = implode("','", $get['np_karyawan']);
			$this->db->where("np_karyawan in ('" . $np . "')");
		}

		$this->db->select("a.*, (CASE WHEN start_date is not null then start_date else end_date end) as ordere")->from("$table_name a");
		if (@$jenis != null)
			$this->db->where_in("a.kode_pamlek", $jenis);

		if ($_SESSION["grup"] == 4) //jika Pengadministrasi Unit Kerja
			$this->db->where_in('a.kode_unit', $var);
		else if ($_SESSION["grup"] == 5) //jika Pengguna
			$this->db->where_in('a.np_karyawan', $var);

		$this->db->order_by('(CASE WHEN a.start_date IS NOT NULL THEN a.start_date ELSE a.end_date END)', 'DESC');
		$this->db->order_by('(CASE WHEN a.start_time IS NOT NULL THEN a.start_time ELSE a.end_time END)', 'DESC');
		$this->db->order_by('a.np_karyawan', 'DESC');
		$data = $this->db->get()->result();
		return $data;
	}
}
