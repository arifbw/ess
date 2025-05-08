<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_ab_karyawan extends CI_Model
{
	var $table_cico = "ess_cico_";

	public function __construct()
	{

		parent::__construct();
	}

	private function _get_datatable_ab_query($kode_unit = null, $np_karyawan, $periode_start, $periode_end)
	{
		$kode_unit = rtrim($kode_unit, "0");
		$this->table_cico = "ess_cico_";

		$this->db->select('a.*, COUNT(a.np_karyawan) AS jumlah_ab')->from('ab_karyawan a');

		if ($kode_unit != null)
			$this->db->like("a.kode_unit", $kode_unit, "after");

		if (!empty($np_karyawan)) {
			$this->db->where("a.np_karyawan", $np_karyawan);
		}

		if (!empty($periode_start) && !empty($periode_end)) {
			$periode_start = $this->table_cico . str_replace("-", "_", $periode_start);
			$periode_end = $this->table_cico . str_replace("-", "_", $periode_end);
			$this->db->where("a.table_name BETWEEN '$periode_start' AND '$periode_end'");
		}

		$this->db->group_by('a.np_karyawan');
	}

	function get_datatable_ab($kode_unit, $np_karyawan, $periode_start, $periode_end)
	{
		//dipindah kebawah karena tabel nya dinamis
		$column_search = array("a.np_karyawan", "a.nama"); //set column field database for datatable_pamlek 

		$this->_get_datatable_ab_query($kode_unit, $np_karyawan, $periode_start, $periode_end);
		if ($_POST['search']['value'] != "") {
			$i = 0;
			foreach ($column_search as $item) // loop column 
			{
				if ($_POST['search']['value']) // if datatable_pamlek send POST for search
				{

					if ($i === 0) // first loop
					{
						$this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
						$this->db->like($item, $_POST['search']['value']);
					} else {
						$this->db->or_like($item, $_POST['search']['value']);
					}

					if (count($column_search) - 1 == $i) //last loop
						$this->db->group_end(); //close bracket
				}
				$i++;
			}
		}
		$sql = $this->db->get_compiled_select();


		$sql = str_replace("::limit::", "", $sql);
		$query = $this->db->query('select * from (' . $sql . ') a');
		// print_r($sql);
		// die;
		return $query->result();
	}

	function count_filtered($kode_unit, $np_karyawan, $periode_start, $periode_end)
	{
		//dipindah kebawah karena tabel nya dinamis
		$column_search = array("a.np_karyawan", "a.nama"); //set column field database for datatable_pamlek 

		$this->_get_datatable_ab_query($kode_unit, $np_karyawan, $periode_start, $periode_end);
		if ($_POST['search']['value'] != "") {
			$i = 0;
			foreach ($column_search as $item) // loop column 
			{
				if ($_POST['search']['value']) // if datatable_pamlek send POST for search
				{

					if ($i === 0) // first loop
					{
						$this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
						$this->db->like($item, $_POST['search']['value']);
					} else {
						$this->db->or_like($item, $_POST['search']['value']);
					}

					if (count($column_search) - 1 == $i) //last loop
						$this->db->group_end(); //close bracket
				}
				$i++;
			}
		}

		$sql = $this->db->get_compiled_select();
		$sql = str_replace("::limit::", "", $sql);

		$count_filtered = count($this->db->query('select * from (' . $sql . ') a')->result_array());

		return $count_filtered;
	}

	public function count_all($kode_unit, $np_karyawan, $periode_start, $periode_end)
	{
		$this->_get_datatable_ab_query($kode_unit, $np_karyawan, $periode_start, $periode_end);

		$sql = $this->db->get_compiled_select();
		$sql = str_replace("::limit::", "", $sql);
		$count_all = count($this->db->query('select * from (' . $sql . ') a')->result_array());

		return $count_all;
	}

	function export_data_ab_karyawan($kode_unit, $np_karyawan, $periode_start, $periode_end)
	{
		$this->_get_datatable_ab_query($kode_unit, $np_karyawan, $periode_start, $periode_end);
		$sql = $this->db->get_compiled_select();
		$sql = str_replace("::limit::", "", $sql);

		$query = $this->db->query('select * from (' . $sql . ') a');

		return $query->result();
	}
}
