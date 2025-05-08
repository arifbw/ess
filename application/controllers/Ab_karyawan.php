<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ab_karyawan extends CI_Controller
{
	public function __construct(){
		parent::__construct();
	}

    function cico_tables_to_ab() {
		$keyword = 'ess_cico_';
		$databaseName = $this->db->database;

		$this->db->select('TABLE_NAME')
			->from('INFORMATION_SCHEMA.TABLES')
			->where('TABLE_SCHEMA', $databaseName)
			->like('TABLE_NAME', $keyword, 'AFTER')
			->order_by('TABLE_NAME', 'DESC')
			->limit(2);
		$subquery = $this->db->get_compiled_select();

		$this->db->select('TABLE_NAME')
			->from("($subquery) last_2_month")
			->order_by('TABLE_NAME', 'ASC');

		$query = $this->db->get();

		$tables = $query->result();
		foreach ($tables as $key => $value) {
			$this->ab5hari($value->TABLE_NAME);
		}
		$this->db->truncate('ab_karyawan');
		$this->db->query("INSERT INTO ab_karyawan SELECT * FROM ab_karyawan_temp");
		$this->db->truncate('ab_karyawan_temp');
		return;
	}

	function ab5hari($table){
		$data = [];

		$np = $this->db->select('np_karyawan')->where("IFNULL(dws_name_fix,dws_name)!=",'OFF')->group_by('np_karyawan')->get($table)->result_array();
		$cico = $this->db->select("id, np_karyawan, nama, kode_unit, IFNULL(dws_name_fix,dws_name) AS dws_name, dws_tanggal, IFNULL(tapping_fix_1,tapping_time_1) AS tapping_fix_1, IFNULL(tapping_fix_2,tapping_time_2) AS tapping_fix_2, id_perizinan, id_cuti, id_sppd")
			->where("IFNULL(dws_name_fix,dws_name)!=",'OFF')
			->where("dws_tanggal<",date('Y-m-d'))
			->where("wfh",'0')
			->order_by('np_karyawan, dws_tanggal')
			->get($table)->result_array();

		$data_of_ab = [];
		foreach ($np as $key => $row) {
			$dws = array_filter($cico, function ($val) use ($row) {
				return $val['np_karyawan'] === $row['np_karyawan'];
			});

			$array_of_dws = [];
			$array_tapping_fix_1 = [];
			$array_tapping_fix_2 = [];
			foreach ($dws as $v) {
				if ($v['tapping_fix_1'] == null && $v['tapping_fix_2'] == null && ($v['id_perizinan'] == '' || $v['id_perizinan'] == null) && ($v['id_cuti'] == '' || $v['id_cuti'] == null) && ($v['id_sppd'] == '' || $v['id_sppd'] == null)) {
					if (count($array_of_dws) < 5) {
						$array_of_dws[] = $v['dws_tanggal'];
						$array_tapping_fix_1[] = $v['tapping_fix_1'];
						$array_tapping_fix_2[] = $v['tapping_fix_2'];
						if (count($array_of_dws) == 5) {
							$data_of_ab[] = [
								'np_karyawan' => $row['np_karyawan'],
								'nama' => $v['nama'],
								'kode_unit' => $v['kode_unit'],
								'table_name' => $table,
								'array_of_dws' => json_encode($array_of_dws),
								'array_tapping_fix_1' => json_encode($array_tapping_fix_1),
								'array_tapping_fix_2' => json_encode($array_tapping_fix_2),
								'created_at' => date('Y-m-d H:i:s')
							];
							$array_of_dws = [];
							$array_tapping_fix_1 = [];
							$array_tapping_fix_2 = [];
						}
					} else {
						$data_of_ab[] = [
							'np_karyawan' => $row['np_karyawan'],
							'nama' => $v['nama'],
							'kode_unit' => $v['kode_unit'],
							'table_name' => $table,
							'array_of_dws' => json_encode($array_of_dws),
							'array_tapping_fix_1' => json_encode($array_tapping_fix_1),
							'array_tapping_fix_2' => json_encode($array_tapping_fix_2),
							'created_at' => date('Y-m-d H:i:s')
						];
						$array_of_dws[] = $v['dws_tanggal'];
						$array_tapping_fix_1[] = $v['tapping_fix_1'];
						$array_tapping_fix_2[] = $v['tapping_fix_2'];
					}
				} else {
					$array_of_dws = [];
					$array_tapping_fix_1 = [];
					$array_tapping_fix_2 = [];
				}
			}
		}

		if($data_of_ab!=[]){
			$this->db->insert_batch('ab_karyawan_temp', $data_of_ab);
		}
	}
}