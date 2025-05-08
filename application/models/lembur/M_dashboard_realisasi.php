<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_dashboard_realisasi extends CI_Model
{
	var $table = 'sap_hcm_rekap_lembur';
	var $column_order = array(null, 'rekap.np_karyawan', 'rekap.nama', null, 'total_uang_lembur', 'total_jam');
	var $column_search = array('rekap.np_karyawan', 'rekap.nama', 'rekap.abbr_unit_kerja', 'rekap.unit_kerja');
	var $order = array();

	public function __construct()
	{
		parent::__construct();
        $this->db = $this->load->database('dwh', true);
	}

	// datatable
	private function _get_datatables_query() {
		$tahun = $this->input->post('tahun', true);
		$unit = $this->input->post('unit', true);
		$this->db->select("rekap.np_karyawan, rekap.nama, rekap.abbr_unit_kerja, rekap.unit_kerja, SUM(rekap.total_jam_lembur) as total_jam,
			SUM(CASE
				WHEN RIGHT(rekap.3001_uang_lembur_15, 1) = '-' THEN
					CAST(CONCAT('-', LEFT(rekap.3001_uang_lembur_15, LENGTH(rekap.3001_uang_lembur_15) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3001_uang_lembur_15 AS SIGNED)
			END) AS total_3001_uang_lembur_15,
			SUM(CASE
				WHEN RIGHT(rekap.3002_uang_lembur_2, 1) = '-' THEN
					CAST(CONCAT('-', LEFT(rekap.3002_uang_lembur_2, LENGTH(rekap.3002_uang_lembur_2) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3002_uang_lembur_2 AS SIGNED)
			END) AS total_3002_uang_lembur_2,
			SUM(CASE
				WHEN RIGHT(rekap.3003_uang_lembur_3, 1) = '-' THEN
					CAST(CONCAT('-', LEFT(rekap.3003_uang_lembur_3, LENGTH(rekap.3003_uang_lembur_3) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3003_uang_lembur_3 AS SIGNED)
			END) AS total_3003_uang_lembur_3,
			SUM(CASE
				WHEN RIGHT(rekap.3004_uang_lembur_4, 1) = '-' THEN
					CAST(CONCAT('-', LEFT(rekap.3004_uang_lembur_4, LENGTH(rekap.3004_uang_lembur_4) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3004_uang_lembur_4 AS SIGNED)
			END) AS total_3004_uang_lembur_4,
			SUM(CASE
				WHEN RIGHT(rekap.3005_uang_lembur_5, 1) = '-' THEN
					CAST(CONCAT('-', LEFT(rekap.3005_uang_lembur_5, LENGTH(rekap.3005_uang_lembur_5) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3005_uang_lembur_5 AS SIGNED)
			END) AS total_3005_uang_lembur_5,
			SUM(CASE
				WHEN RIGHT(rekap.3006_uang_lembur_6, 1) = '-' THEN
					CAST(CONCAT('-', LEFT(rekap.3006_uang_lembur_6, LENGTH(rekap.3006_uang_lembur_6) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3006_uang_lembur_6 AS SIGNED)
			END) AS total_3006_uang_lembur_6,
			SUM(CASE
				WHEN RIGHT(rekap.3007_uang_lembur_7, 1) = '-' THEN
					CAST(CONCAT('-', LEFT(rekap.3007_uang_lembur_7, LENGTH(rekap.3007_uang_lembur_7) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3007_uang_lembur_7 AS SIGNED)
			END) AS total_3007_uang_lembur_7,
			SUM(CASE
				WHEN RIGHT(rekap.3100_uang_lembur_manual, 1) = '-' THEN
					CAST(CONCAT('-', LEFT(rekap.3100_uang_lembur_manual, LENGTH(rekap.3100_uang_lembur_manual) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3100_uang_lembur_manual AS SIGNED)
			END) AS total_3100_uang_lembur_manual,
			SUM(CASE
				WHEN RIGHT(rekap.3110_insentif_lembur, 1) = '-' THEN
					CAST(CONCAT('-', LEFT(rekap.3110_insentif_lembur, LENGTH(rekap.3110_insentif_lembur) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3110_insentif_lembur AS SIGNED)
			END) AS total_3110_insentif_lembur,
			SUM(CASE
				WHEN RIGHT(rekap.3400_uang_lembur_susulan, 1) = '-' THEN
					CAST(CONCAT('-', LEFT(rekap.3400_uang_lembur_susulan, LENGTH(rekap.3400_uang_lembur_susulan) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3400_uang_lembur_susulan AS SIGNED)
			END) AS total_3400_uang_lembur_susulan
		
		, (
			SUM(CASE
				WHEN RIGHT(rekap.3001_uang_lembur_15, 1) = '-' THEN
					CAST(CONCAT('-', LEFT(rekap.3001_uang_lembur_15, LENGTH(rekap.3001_uang_lembur_15) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3001_uang_lembur_15 AS SIGNED)
			END) + 
			SUM(CASE
				WHEN RIGHT(rekap.3002_uang_lembur_2, 1) = '-' THEN
					CAST(CONCAT('-', LEFT(rekap.3002_uang_lembur_2, LENGTH(rekap.3002_uang_lembur_2) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3002_uang_lembur_2 AS SIGNED)
			END) + 
			SUM(CASE
				WHEN RIGHT(rekap.3003_uang_lembur_3, 1) = '-' THEN
					CAST(CONCAT('-', LEFT(rekap.3003_uang_lembur_3, LENGTH(rekap.3003_uang_lembur_3) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3003_uang_lembur_3 AS SIGNED)
			END) + 
			SUM(CASE
				WHEN RIGHT(rekap.3004_uang_lembur_4, 1) = '-' THEN
					CAST(CONCAT('-', LEFT(rekap.3004_uang_lembur_4, LENGTH(rekap.3004_uang_lembur_4) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3004_uang_lembur_4 AS SIGNED)
			END) + 
			SUM(CASE
				WHEN RIGHT(rekap.3005_uang_lembur_5, 1) = '-' THEN
					CAST(CONCAT('-', LEFT(rekap.3005_uang_lembur_5, LENGTH(rekap.3005_uang_lembur_5) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3005_uang_lembur_5 AS SIGNED)
			END) + 
			SUM(CASE
				WHEN RIGHT(rekap.3006_uang_lembur_6, 1) = '-' THEN
					CAST(CONCAT('-', LEFT(rekap.3006_uang_lembur_6, LENGTH(rekap.3006_uang_lembur_6) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3006_uang_lembur_6 AS SIGNED)
			END) + 
			SUM(CASE
				WHEN RIGHT(rekap.3007_uang_lembur_7, 1) = '-' THEN
					CAST(CONCAT('-', LEFT(rekap.3007_uang_lembur_7, LENGTH(rekap.3007_uang_lembur_7) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3007_uang_lembur_7 AS SIGNED)
			END) + 
			SUM(CASE
				WHEN RIGHT(rekap.3100_uang_lembur_manual, 1) = '-' THEN
					CAST(CONCAT('-', LEFT(rekap.3100_uang_lembur_manual, LENGTH(rekap.3100_uang_lembur_manual) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3100_uang_lembur_manual AS SIGNED)
			END) + 
			SUM(CASE
				WHEN RIGHT(rekap.3110_insentif_lembur, 1) = '-' THEN
					CAST(CONCAT('-', LEFT(rekap.3110_insentif_lembur, LENGTH(rekap.3110_insentif_lembur) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3110_insentif_lembur AS SIGNED)
			END) + 
			SUM(CASE
				WHEN RIGHT(rekap.3400_uang_lembur_susulan, 1) = '-' THEN
					CAST(CONCAT('-', LEFT(rekap.3400_uang_lembur_susulan, LENGTH(rekap.3400_uang_lembur_susulan) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3400_uang_lembur_susulan AS SIGNED)
			END)
		) AS total_uang_lembur");
		$this->db->from("{$this->table} rekap");

		// $this->db->join("(SELECT x.np_karyawan, MAX(x.id) as max_id
		// FROM {$this->table} x WHERE x.periode_tahun='{$tahun}' AND x.abbr_unit_kerja LIKE '".rtrim($unit, '0')."%'
		// GROUP BY x.np_karyawan) t2", 'rekap.np_karyawan = t2.np_karyawan AND rekap.id = t2.max_id','LEFT');

		$this->db->join("(SELECT x.np_karyawan, x.periode_tahun, x.periode_bulan, MAX(x.id) as max_id
		FROM {$this->table} x WHERE x.periode_tahun='{$tahun}' AND x.abbr_unit_kerja LIKE '".rtrim($unit, '0')."%'
		GROUP BY x.np_karyawan, x.periode_tahun, x.periode_bulan) t2", 'rekap.np_karyawan = t2.np_karyawan AND rekap.periode_tahun = t2.periode_tahun AND rekap.periode_bulan = t2.periode_bulan AND rekap.id = t2.max_id','INNER');

		$this->db->where('rekap.periode_tahun', $tahun);
        $this->db->where('rekap.rate_lembur!=', '');
		if($unit!='00000'){
			$this->db->like('rekap.abbr_unit_kerja', rtrim($unit, '0'), 'AFTER');
		}
        $this->db->having("SUM(rekap.total_jam_lembur) >", 0);
		$this->db->group_by('rekap.np_karyawan');
		// $this->db->order_by('rekap.np_karyawan, rekap.nama, rekap.abbr_unit_kerja, rekap.unit_kerja');
	
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
		$tahun = $this->input->post('tahun', true);
		$unit = $this->input->post('unit', true);
		$this->db->select("rekap.np_karyawan, rekap.nama, rekap.abbr_unit_kerja, rekap.unit_kerja, SUM(rekap.total_jam_lembur) as total_jam,
			SUM(CASE
				WHEN RIGHT(rekap.3001_uang_lembur_15, 1) = '-' THEN
					CAST(CONCAT('-', LEFT(rekap.3001_uang_lembur_15, LENGTH(rekap.3001_uang_lembur_15) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3001_uang_lembur_15 AS SIGNED)
			END) AS total_3001_uang_lembur_15,
			SUM(CASE
				WHEN RIGHT(rekap.3002_uang_lembur_2, 1) = '-' THEN
					CAST(CONCAT('-', LEFT(rekap.3002_uang_lembur_2, LENGTH(rekap.3002_uang_lembur_2) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3002_uang_lembur_2 AS SIGNED)
			END) AS total_3002_uang_lembur_2,
			SUM(CASE
				WHEN RIGHT(rekap.3003_uang_lembur_3, 1) = '-' THEN
					CAST(CONCAT('-', LEFT(rekap.3003_uang_lembur_3, LENGTH(rekap.3003_uang_lembur_3) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3003_uang_lembur_3 AS SIGNED)
			END) AS total_3003_uang_lembur_3,
			SUM(CASE
				WHEN RIGHT(rekap.3004_uang_lembur_4, 1) = '-' THEN
					CAST(CONCAT('-', LEFT(rekap.3004_uang_lembur_4, LENGTH(rekap.3004_uang_lembur_4) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3004_uang_lembur_4 AS SIGNED)
			END) AS total_3004_uang_lembur_4,
			SUM(CASE
				WHEN RIGHT(rekap.3005_uang_lembur_5, 1) = '-' THEN
					CAST(CONCAT('-', LEFT(rekap.3005_uang_lembur_5, LENGTH(rekap.3005_uang_lembur_5) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3005_uang_lembur_5 AS SIGNED)
			END) AS total_3005_uang_lembur_5,
			SUM(CASE
				WHEN RIGHT(rekap.3006_uang_lembur_6, 1) = '-' THEN
					CAST(CONCAT('-', LEFT(rekap.3006_uang_lembur_6, LENGTH(rekap.3006_uang_lembur_6) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3006_uang_lembur_6 AS SIGNED)
			END) AS total_3006_uang_lembur_6,
			SUM(CASE
				WHEN RIGHT(rekap.3007_uang_lembur_7, 1) = '-' THEN
					CAST(CONCAT('-', LEFT(rekap.3007_uang_lembur_7, LENGTH(rekap.3007_uang_lembur_7) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3007_uang_lembur_7 AS SIGNED)
			END) AS total_3007_uang_lembur_7,
			SUM(CASE
				WHEN RIGHT(rekap.3100_uang_lembur_manual, 1) = '-' THEN
					CAST(CONCAT('-', LEFT(rekap.3100_uang_lembur_manual, LENGTH(rekap.3100_uang_lembur_manual) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3100_uang_lembur_manual AS SIGNED)
			END) AS total_3100_uang_lembur_manual,
			SUM(CASE
				WHEN RIGHT(rekap.3110_insentif_lembur, 1) = '-' THEN
					CAST(CONCAT('-', LEFT(rekap.3110_insentif_lembur, LENGTH(rekap.3110_insentif_lembur) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3110_insentif_lembur AS SIGNED)
			END) AS total_3110_insentif_lembur,
			SUM(CASE
				WHEN RIGHT(rekap.3400_uang_lembur_susulan, 1) = '-' THEN
					CAST(CONCAT('-', LEFT(rekap.3400_uang_lembur_susulan, LENGTH(rekap.3400_uang_lembur_susulan) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3400_uang_lembur_susulan AS SIGNED)
			END) AS total_3400_uang_lembur_susulan");
		$this->db->from("{$this->table} rekap");

		// $this->db->join("(SELECT x.np_karyawan, MAX(x.id) as max_id
		// FROM {$this->table} x WHERE x.periode_tahun='{$tahun}' AND x.abbr_unit_kerja LIKE '".rtrim($unit, '0')."%'
		// GROUP BY x.np_karyawan) t2", 'rekap.np_karyawan = t2.np_karyawan AND rekap.id = t2.max_id','LEFT');

		$this->db->join("(SELECT x.np_karyawan, x.periode_tahun, x.periode_bulan, MAX(x.id) as max_id
		FROM {$this->table} x WHERE x.periode_tahun='{$tahun}' AND x.abbr_unit_kerja LIKE '".rtrim($unit, '0')."%'
		GROUP BY x.np_karyawan, x.periode_tahun, x.periode_bulan) t2", 'rekap.np_karyawan = t2.np_karyawan AND rekap.periode_tahun = t2.periode_tahun AND rekap.periode_bulan = t2.periode_bulan AND rekap.id = t2.max_id','INNER');

		$this->db->where('rekap.periode_tahun', $tahun);
        $this->db->where('rekap.rate_lembur!=', '');
		if($unit!='00000'){
			$this->db->like('rekap.abbr_unit_kerja', rtrim($unit, '0'), 'AFTER');
		}
		$this->db->having("SUM(rekap.total_jam_lembur) >", 0);
		$this->db->group_by('rekap.np_karyawan');
		// $this->db->order_by('rekap.np_karyawan, rekap.nama, rekap.abbr_unit_kerja, rekap.unit_kerja');
		return $this->db->count_all_results();
	}
	// END datatable

    function get_realisasi_tahunan($tahun, $unit){
        $this->db->select('periode_bulan, SUM(total_jam_lembur) as total_jam,
			SUM(CASE
				WHEN RIGHT(rekap.3001_uang_lembur_15, 1) = "-" THEN
					CAST(CONCAT("-", LEFT(rekap.3001_uang_lembur_15, LENGTH(rekap.3001_uang_lembur_15) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3001_uang_lembur_15 AS SIGNED)
			END) AS total_3001_uang_lembur_15,
			SUM(CASE
				WHEN RIGHT(rekap.3002_uang_lembur_2, 1) = "-" THEN
					CAST(CONCAT("-", LEFT(rekap.3002_uang_lembur_2, LENGTH(rekap.3002_uang_lembur_2) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3002_uang_lembur_2 AS SIGNED)
			END) AS total_3002_uang_lembur_2,
			SUM(CASE
				WHEN RIGHT(rekap.3003_uang_lembur_3, 1) = "-" THEN
					CAST(CONCAT("-", LEFT(rekap.3003_uang_lembur_3, LENGTH(rekap.3003_uang_lembur_3) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3003_uang_lembur_3 AS SIGNED)
			END) AS total_3003_uang_lembur_3,
			SUM(CASE
				WHEN RIGHT(rekap.3004_uang_lembur_4, 1) = "-" THEN
					CAST(CONCAT("-", LEFT(rekap.3004_uang_lembur_4, LENGTH(rekap.3004_uang_lembur_4) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3004_uang_lembur_4 AS SIGNED)
			END) AS total_3004_uang_lembur_4,
			SUM(CASE
				WHEN RIGHT(rekap.3005_uang_lembur_5, 1) = "-" THEN
					CAST(CONCAT("-", LEFT(rekap.3005_uang_lembur_5, LENGTH(rekap.3005_uang_lembur_5) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3005_uang_lembur_5 AS SIGNED)
			END) AS total_3005_uang_lembur_5,
			SUM(CASE
				WHEN RIGHT(rekap.3006_uang_lembur_6, 1) = "-" THEN
					CAST(CONCAT("-", LEFT(rekap.3006_uang_lembur_6, LENGTH(rekap.3006_uang_lembur_6) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3006_uang_lembur_6 AS SIGNED)
			END) AS total_3006_uang_lembur_6,
			SUM(CASE
				WHEN RIGHT(rekap.3007_uang_lembur_7, 1) = "-" THEN
					CAST(CONCAT("-", LEFT(rekap.3007_uang_lembur_7, LENGTH(rekap.3007_uang_lembur_7) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3007_uang_lembur_7 AS SIGNED)
			END) AS total_3007_uang_lembur_7,
			SUM(CASE
				WHEN RIGHT(rekap.3100_uang_lembur_manual, 1) = "-" THEN
					CAST(CONCAT("-", LEFT(rekap.3100_uang_lembur_manual, LENGTH(rekap.3100_uang_lembur_manual) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3100_uang_lembur_manual AS SIGNED)
			END) AS total_3100_uang_lembur_manual,
			SUM(CASE
				WHEN RIGHT(rekap.3110_insentif_lembur, 1) = "-" THEN
					CAST(CONCAT("-", LEFT(rekap.3110_insentif_lembur, LENGTH(rekap.3110_insentif_lembur) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3110_insentif_lembur AS SIGNED)
			END) AS total_3110_insentif_lembur,
			SUM(CASE
				WHEN RIGHT(rekap.3400_uang_lembur_susulan, 1) = "-" THEN
					CAST(CONCAT("-", LEFT(rekap.3400_uang_lembur_susulan, LENGTH(rekap.3400_uang_lembur_susulan) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3400_uang_lembur_susulan AS SIGNED)
			END) AS total_3400_uang_lembur_susulan');
        $this->db->where('periode_tahun', $tahun);
        $this->db->where('rate_lembur!=', '');
		if($unit!='00000'){
			$this->db->like('abbr_unit_kerja', rtrim($unit, '0'), 'AFTER');
		}
        $this->db->group_by('periode_bulan');
        $this->db->order_by('periode_bulan','ASC');
        return $this->db->get($this->table)->result();
    }

    function get_realisasi_tahunan_divisi($tahun, $unit){
        $this->db->select('rekap.periode_bulan, SUM(rekap.total_jam_lembur) as total_jam,
			SUM(CASE
				WHEN RIGHT(rekap.3001_uang_lembur_15, 1) = "-" THEN
					CAST(CONCAT("-", LEFT(rekap.3001_uang_lembur_15, LENGTH(rekap.3001_uang_lembur_15) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3001_uang_lembur_15 AS SIGNED)
			END) AS total_3001_uang_lembur_15,
			SUM(CASE
				WHEN RIGHT(rekap.3002_uang_lembur_2, 1) = "-" THEN
					CAST(CONCAT("-", LEFT(rekap.3002_uang_lembur_2, LENGTH(rekap.3002_uang_lembur_2) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3002_uang_lembur_2 AS SIGNED)
			END) AS total_3002_uang_lembur_2,
			SUM(CASE
				WHEN RIGHT(rekap.3003_uang_lembur_3, 1) = "-" THEN
					CAST(CONCAT("-", LEFT(rekap.3003_uang_lembur_3, LENGTH(rekap.3003_uang_lembur_3) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3003_uang_lembur_3 AS SIGNED)
			END) AS total_3003_uang_lembur_3,
			SUM(CASE
				WHEN RIGHT(rekap.3004_uang_lembur_4, 1) = "-" THEN
					CAST(CONCAT("-", LEFT(rekap.3004_uang_lembur_4, LENGTH(rekap.3004_uang_lembur_4) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3004_uang_lembur_4 AS SIGNED)
			END) AS total_3004_uang_lembur_4,
			SUM(CASE
				WHEN RIGHT(rekap.3005_uang_lembur_5, 1) = "-" THEN
					CAST(CONCAT("-", LEFT(rekap.3005_uang_lembur_5, LENGTH(rekap.3005_uang_lembur_5) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3005_uang_lembur_5 AS SIGNED)
			END) AS total_3005_uang_lembur_5,
			SUM(CASE
				WHEN RIGHT(rekap.3006_uang_lembur_6, 1) = "-" THEN
					CAST(CONCAT("-", LEFT(rekap.3006_uang_lembur_6, LENGTH(rekap.3006_uang_lembur_6) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3006_uang_lembur_6 AS SIGNED)
			END) AS total_3006_uang_lembur_6,
			SUM(CASE
				WHEN RIGHT(rekap.3007_uang_lembur_7, 1) = "-" THEN
					CAST(CONCAT("-", LEFT(rekap.3007_uang_lembur_7, LENGTH(rekap.3007_uang_lembur_7) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3007_uang_lembur_7 AS SIGNED)
			END) AS total_3007_uang_lembur_7,
			SUM(CASE
				WHEN RIGHT(rekap.3100_uang_lembur_manual, 1) = "-" THEN
					CAST(CONCAT("-", LEFT(rekap.3100_uang_lembur_manual, LENGTH(rekap.3100_uang_lembur_manual) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3100_uang_lembur_manual AS SIGNED)
			END) AS total_3100_uang_lembur_manual,
			SUM(CASE
				WHEN RIGHT(rekap.3110_insentif_lembur, 1) = "-" THEN
					CAST(CONCAT("-", LEFT(rekap.3110_insentif_lembur, LENGTH(rekap.3110_insentif_lembur) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3110_insentif_lembur AS SIGNED)
			END) AS total_3110_insentif_lembur,
			SUM(CASE
				WHEN RIGHT(rekap.3400_uang_lembur_susulan, 1) = "-" THEN
					CAST(CONCAT("-", LEFT(rekap.3400_uang_lembur_susulan, LENGTH(rekap.3400_uang_lembur_susulan) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3400_uang_lembur_susulan AS SIGNED)
			END) AS total_3400_uang_lembur_susulan');
		
		$this->db->join("(SELECT x.np_karyawan, x.periode_tahun, x.periode_bulan, MAX(x.id) as max_id
		FROM {$this->table} x WHERE x.periode_tahun='{$tahun}' AND x.abbr_unit_kerja LIKE '".rtrim($unit, '0')."%'
		GROUP BY x.np_karyawan, x.periode_tahun, x.periode_bulan) t2", 'rekap.np_karyawan = t2.np_karyawan AND rekap.periode_tahun = t2.periode_tahun AND rekap.periode_bulan = t2.periode_bulan AND rekap.id = t2.max_id','INNER');

        $this->db->where('rekap.periode_tahun', $tahun);
        $this->db->where('rekap.rate_lembur!=', '');
		if($unit!='00000'){
			$this->db->like('rekap.abbr_unit_kerja', rtrim($unit, '0'), 'AFTER');
		}
        $this->db->group_by('rekap.periode_bulan');
        $this->db->order_by('rekap.periode_bulan','ASC');
        return $this->db->get("{$this->table} rekap")->result();
    }
}