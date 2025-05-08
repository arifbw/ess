<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_monitoring_sppd extends CI_Model
{

	var $table = 'ess_sppd_monitoring';
	var $column_order = array(null, 'np_karyawan', 'nama', 'perihal', 'tipe_perjalanan', 'tujuan', 'tgl_berangkat', 'tgl_pulang', 'tgl_selesai', 'no_surat', 'hotel', 'jenis_transportasi', 'jenis_fasilitas', 'biaya', 'biayaus', 'nama_jabatan', 'pangkat', 'unit', 'kode_unit'); //set column field database for datatable orderable
	var $column_search = array('np_karyawan', 'nama', 'perihal', 'tipe_perjalanan', 'tujuan', 'tgl_berangkat', 'tgl_pulang', 'tgl_selesai', 'no_surat', 'hotel', 'jenis_transportasi', 'jenis_fasilitas', 'biaya', 'biayaus', 'nama_jabatan', 'pangkat', 'unit', 'kode_unit'); //set column field database for datatable searchable 
	var $order = array('tgl_berangkat' => 'asc'); // default order 

	public function __construct()
	{
		parent::__construct();
		//Do your magic here
	}

	public function getKaryawan($np_karyawan = null)
	{
		$this->db->order_by('no_pokok', 'ASC');
		if ($_SESSION["grup"] == 4) { //jika Pengadministrasi Unit Kerja
			$this->db->where_in('kode_unit', $np_karyawan);
		} else if ($_SESSION["grup"] == 5) { //jika Pengguna
			$this->db->where('no_pokok', $np_karyawan);
		}
		return $this->db->get('mst_karyawan')->result();
	}

	private function _get_datatables_query($var = null, $start_date = null, $end_date = null, $np_karyawan = null, $tipe_perjalanan = null)
	{
		$this->db->select('ess_sppd_monitoring.id');
		$this->db->select('ess_sppd_monitoring.np_karyawan');
		$this->db->select('ess_sppd_monitoring.nama');
		$this->db->select('ess_sppd_monitoring.perihal');
		$this->db->select('ess_sppd_monitoring.tipe_perjalanan');
		$this->db->select('ess_sppd_monitoring.tujuan');
		$this->db->select('ess_sppd_monitoring.tgl_berangkat');
		$this->db->select('ess_sppd_monitoring.tgl_pulang');
		$this->db->select('ess_sppd_monitoring.tgl_selesai');
		$this->db->select('ess_sppd_monitoring.no_surat');
		$this->db->select('ess_sppd_monitoring.hotel');
		$this->db->select('ess_sppd_monitoring.jenis_transportasi');
		$this->db->select('ess_sppd_monitoring.jenis_fasilitas');
		$this->db->select('ess_sppd_monitoring.biaya');
		$this->db->select('ess_sppd_monitoring.biayaus');
		$this->db->select('ess_sppd_monitoring.nama_jabatan');
		$this->db->select('ess_sppd_monitoring.pangkat');
		$this->db->select('ess_sppd_monitoring.unit');
		$this->db->select('b.kode_unit');
		$this->db->from($this->table);
		$this->db->join('mst_karyawan b', 'ess_sppd_monitoring.np_karyawan=b.no_pokok', 'LEFT');

		if ($start_date != null && $end_date != null) {
			$this->db->where("tgl_berangkat BETWEEN '$start_date' AND '$end_date'");
		}

		if ($_SESSION["grup"] == 4) //jika Pengadministrasi Unit Kerja
		{
			$this->db->where_in('kode_unit', $var);
		} else if ($_SESSION["grup"] == 5) //jika Pengguna
		{
			$this->db->where_in('np_karyawan', $var);
		}

		if (!empty($tipe_perjalanan)) {
			$this->db->where('tipe_perjalanan', $tipe_perjalanan);
		}

		if (!empty($np_karyawan) && $_SESSION["grup"] != 5) {
			$this->db->where('np_karyawan', $np_karyawan);
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

	function get_datatables($var = null, $start_date = null, $end_date = null, $np_karyawan = null, $tipe_perjalanan = null)
	{
		$this->_get_datatables_query($var, $start_date, $end_date, $np_karyawan, $tipe_perjalanan);

		if ($_POST['length'] != -1)
			$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered($var = null, $start_date = null, $end_date = null, $np_karyawan = null, $tipe_perjalanan = null)
	{
		$this->_get_datatables_query($var, $start_date, $end_date, $np_karyawan, $tipe_perjalanan);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($var = null, $start_date = null, $end_date = null, $np_karyawan = null, $tipe_perjalanan = null)
	{
		$this->db->from($this->table);

		if ($start_date != null && $end_date != null) {
			$this->db->where("tgl_berangkat BETWEEN '$start_date' AND '$end_date'");
		}

		if ($_SESSION["grup"] == 4) //jika Pengadministrasi Unit Kerja
		{
			$this->db->where_in('kode_unit', $var);
		} else
		if ($_SESSION["grup"] == 5) //jika Pengguna
		{
			$this->db->where_in('np_karyawan', $var);
		} else {
		}

		return $this->db->count_all_results();
	}
}
