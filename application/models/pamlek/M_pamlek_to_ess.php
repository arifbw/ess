<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_pamlek_to_ess extends CI_Model {
	
	function check_table_exist($name)
	{
		$name=str_replace("-","_",$name);
		$query = $this->db->query("show tables like '$name'")->row_array();
		
		return $query;
	}
	
	function create_table_cico($name)
	{	
		$name=str_replace("-","_",$name);
		$this->db->query("CREATE TABLE $name like ess_cico");
		
	}
	
	
	public function select_master_data($tahun_bulan,$tanggal_dws,$np_karyawan){
		$tahun_bulan=str_replace("-","_",$tahun_bulan);
		$nama_tabel = 'erp_master_data_'.$tahun_bulan;
		
		if(!$this->check_table_exist($nama_tabel)){
			$nama_tabel = 'erp_master_data';
		}
		
		$this->db->select('*');
		$this->db->from($nama_tabel);
		$this->db->where('tanggal_dws',$tanggal_dws);
		
		if($np_karyawan!='all'){
			$this->db->where('np_karyawan',$np_karyawan);
		}
		
		$this->db->order_by("np_karyawan", "ASC");
		
		$query = $this->db->get();
		return $query;
	}

	public function checking_anomaly_kehadiran($tahun_bulan, $start, $end){
		$tbl1 = 'ess_cico_'.$tahun_bulan;
		$tbl2 = 'pamlek_data_'.$tahun_bulan;
		$tbl3 = 'ess_perizinan_'.$tahun_bulan;

		$f_start = DateTime::createFromFormat('Y-m-d', $start)->format('Y/m/d');
		$f_end = DateTime::createFromFormat('Y-m-d', $end)->format('Y/m/d');

		if(!$this->check_table_exist($tbl1)){
			$tbl1 = 'ess_cico';
		}

		if(!$this->check_table_exist($tbl2)){
			$tbl2 = 'pamlek_data';
		}

		if(!$this->check_table_exist($tbl3)){
			$tbl3 = 'ess_perizinan';
		}

		$sql = '
			SELECT 
				ecd.nama,
				ecd.np_karyawan,
				ecd.dws_tanggal AS "Tertanggal",
				
				COALESCE(ecd.tapping_fix_1, ecd.tapping_time_1) AS "In (Kehadiran)",
				COALESCE(ecd.tapping_fix_2, ecd.tapping_time_2) AS "Out (Kehadiran)",
				fi.first_in_time AS "In (Pamlek)",
				CASE
					WHEN 
						(lo_same_day.last_out_time IS NULL AND fi_next_day.first_in_time > lo_next_day.last_out_time) OR
						(lo_same_day.last_out_time IS NULL AND fi_next_day.first_in_time IS NULL AND lo_next_day.last_out_time IS NOT NULL) OR
						(lo_same_day.last_out_time < fi.first_in_time)
					THEN lo_next_day.last_out_time
					ELSE lo_same_day.last_out_time
				END AS "Out (Pamlek)",
				CASE 
					WHEN 
						TIMESTAMPDIFF(MINUTE, fi.first_in_time, COALESCE(ecd.tapping_fix_1, ecd.tapping_time_1)) <= 60
						AND TIMESTAMPDIFF(MINUTE,
							CASE
								WHEN 
									(lo_same_day.last_out_time IS NULL AND fi_next_day.first_in_time > lo_next_day.last_out_time) OR
									(lo_same_day.last_out_time IS NULL AND fi_next_day.first_in_time IS NULL AND lo_next_day.last_out_time IS NOT NULL) OR
									(lo_same_day.last_out_time < fi.first_in_time)
								THEN lo_next_day.last_out_time
								ELSE lo_same_day.last_out_time
							END, COALESCE(ecd.tapping_fix_2, ecd.tapping_time_2)) <= 60
					THEN "Normal"
					WHEN
						TIME(fi.first_in_time) IS NULL
						OR tapping_fix_approval_status = "1"
						OR mst.dws_start_time = "00:00:00"
						OR mst.lintas_hari_masuk = 1
						OR COALESCE(ecd.dws_in_fix, ecd.dws_in) = "00:00:00"
						OR kode_pamlek IN ("C", "E", "F")
						OR (id_cuti != "" AND id_cuti IS NOT NULL)
						OR (ecd.dws_tanggal = LAST_DAY(ecd.dws_tanggal) && TIMESTAMPDIFF(MINUTE, fi.first_in_time, COALESCE(ecd.tapping_fix_1, ecd.tapping_time_1)) <= 60)
					THEN "Exception"
					WHEN
						TIME(
							CASE
								WHEN 
									(lo_same_day.last_out_time IS NULL AND fi_next_day.first_in_time > lo_next_day.last_out_time) OR
									(lo_same_day.last_out_time IS NULL AND fi_next_day.first_in_time IS NULL AND lo_next_day.last_out_time IS NOT NULL) OR
									(lo_same_day.last_out_time < fi.first_in_time)
								THEN lo_next_day.last_out_time
								ELSE lo_same_day.last_out_time
							END
						) IS NULL AND TIME(COALESCE(ecd.tapping_fix_2, ecd.tapping_time_2)) IS NULL
					THEN "Lupa Slide"
					ELSE "Anomaly"
				END AS status
			FROM 
				'.$tbl1.' ecd
			LEFT JOIN (
				SELECT 
					no_pokok AS np_karyawan,
					DATE(tapping_time) AS dws_tanggal,
					MIN(tapping_time) AS first_in_time
				FROM 
					'.$tbl2.'
				WHERE 
					in_out = 1
				GROUP BY 
					no_pokok, DATE(tapping_time)
			) fi
			ON 
				ecd.np_karyawan = fi.np_karyawan 
				AND ecd.dws_tanggal = fi.dws_tanggal
			LEFT JOIN (
				SELECT 
					no_pokok AS np_karyawan,
					DATE(tapping_time) AS dws_tanggal,
					MIN(tapping_time) AS first_in_time
				FROM 
					'.$tbl2.'
				WHERE 
					in_out = 1
				GROUP BY 
					no_pokok, DATE(tapping_time)
			) fi_next_day
			ON 
				ecd.np_karyawan = fi_next_day.np_karyawan 
				AND DATE_ADD(ecd.dws_tanggal, INTERVAL 1 DAY) = fi_next_day.dws_tanggal
			LEFT JOIN (
				SELECT 
					no_pokok AS np_karyawan,
					DATE(tapping_time) AS dws_tanggal,
					MAX(tapping_time) AS last_out_time
				FROM 
					'.$tbl2.'
				WHERE 
					in_out = 0
				GROUP BY 
					no_pokok, DATE(tapping_time)
			) lo_same_day
			ON 
				ecd.np_karyawan = lo_same_day.np_karyawan 
				AND ecd.dws_tanggal = lo_same_day.dws_tanggal
			LEFT JOIN (
				SELECT 
					no_pokok AS np_karyawan,
					DATE(tapping_time) AS dws_tanggal,
					MIN(tapping_time) AS last_out_time
				FROM 
					'.$tbl2.'
				WHERE 
					in_out = 0
				GROUP BY 
					no_pokok, DATE(tapping_time)
			) lo_next_day
			ON 
				ecd.np_karyawan = lo_next_day.np_karyawan 
				AND DATE_ADD(ecd.dws_tanggal, INTERVAL 1 DAY) = lo_next_day.dws_tanggal
			LEFT JOIN (
				SELECT * FROM mst_jadwal_kerja GROUP BY dws
			) mst
			ON 
				COALESCE(ecd.dws_name_fix, ecd.dws_name) = mst.dws
			LEFT JOIN 
				'.$tbl3.'
			ON 
				ecd.id_perizinan = '.$tbl3.'.id
			WHERE
				DAYOFWEEK(ecd.dws_tanggal) NOT IN (1, 7)
				AND ecd.dws_tanggal between "'.$f_start.'" and "'.$f_end.'"
			HAVING
				status = "Anomaly"
			ORDER BY 
				ecd.dws_tanggal;
		';

		$query = $this->db->query($sql);
		
		return $query;
	}
	
	public function get_substitution($np_karyawan,$dws_tanggal)
	{
		$this->db->select('ess_substitution.np_karyawan');
		$this->db->select('ess_substitution.date');
		$this->db->select('ess_substitution.dws');
		$this->db->select('ess_substitution.dws_variant');
		$this->db->select('mst_jadwal_kerja.lintas_hari_masuk AS lintas_hari_masuk');
		$this->db->select('mst_jadwal_kerja.lintas_hari_pulang AS lintas_hari_pulang');
		$this->db->select('mst_jadwal_kerja.dws_start_time AS start_time');
		$this->db->select('mst_jadwal_kerja.dws_end_time AS end_time');
		$this->db->select('mst_jadwal_kerja.dws_break_start_time');
		$this->db->select('mst_jadwal_kerja.dws_break_end_time');
		$this->db->from('ess_substitution');
		$this->db->join('mst_jadwal_kerja', "mst_jadwal_kerja.dws = ess_substitution.dws AND mst_jadwal_kerja.dws_variant = ess_substitution.dws_variant", 'left');		
	
		$this->db->where('ess_substitution.np_karyawan', $np_karyawan);
		$this->db->where('ess_substitution.date', $dws_tanggal);
		$this->db->where('ess_substitution.deleted', '0');
		$this->db->where('mst_jadwal_kerja.status', '1');
		
		$query 	= $this->db->get();
		
		return $query->row_array();
	}
	
	public function select_cico($tahun_bulan,$tanggal_dws,$np_karyawan)
	{
		$tahun_bulan=str_replace("-","_",$tahun_bulan);
		$nama_tabel = 'ess_cico_'.$tahun_bulan;
		
		if(!$this->check_table_exist($nama_tabel))
		{
			$nama_tabel = 'ess_cico';
		}
		
		$this->db->select('*');
		$this->db->from($nama_tabel);
		$this->db->where('dws_tanggal',$tanggal_dws);
		
		if($np_karyawan!='0')
		{
			$this->db->where('np_karyawan',$np_karyawan);
		}
		
		$this->db->order_by("np_karyawan", "ASC"); 	
		
		$query = $this->db->get();
		return $query;
	}
	
	public function search_tapping_in($tanggal_dws,$np_karyawan,$tabel_sekarang,$tabel_kemarin,$dws_in_besok,$dws_out_kemarin,$tapping_out_kemarin,$dws_out_sekarang)
	{
		$tabel_sekarang=str_replace("-","_",$tabel_sekarang);
		$tabel_kemarin=str_replace("-","_",$tabel_kemarin);
		
		if(!$this->check_table_exist($tabel_sekarang))
		{
			$tabel_sekarang = 'pamlek_data';
		}
		if(!$this->check_table_exist($tabel_kemarin))
		{
			$tabel_kemarin = 'pamlek_data';
		}
		
		$query = $this->db->query("
		/*CARI IN $tanggal_dws*/		
		SELECT
			*
		FROM
			$tabel_sekarang /*Tabel Bulan Sekarang*/
		WHERE
			no_pokok = '$np_karyawan'
		AND in_out = '1'
		AND tapping_type = '0'
		AND tapping_time = (
			SELECT
				min(tapping_time)
			FROM
				$tabel_sekarang /*Tabel Bulan Sekarang*/
			WHERE
				no_pokok = '$np_karyawan'
			AND in_out = '1'
			AND tapping_type = '0'
			AND tapping_time > '$dws_out_kemarin' /*DWS out kemarin*/
			AND SUBSTR(tapping_time, 1, 16) > SUBSTR('$tapping_out_kemarin', 1, 16) /*Tapping out kemarin*/
			AND tapping_time < '$dws_out_sekarang' /*DWS OUT Sekarang*/
			AND tapping_time < '$dws_in_besok' /*EDIT DWS in besok*/
		)
		UNION
			SELECT
				*
			FROM
				$tabel_kemarin /*Tabel Bulan KEMARIN*/
			WHERE
				no_pokok = '$np_karyawan'
			AND in_out = '1'
			AND tapping_type = '0'
			AND tapping_time = (
				SELECT
					min(tapping_time)
				FROM
					$tabel_kemarin /*Tabel Bulan KEMARIN*/
				WHERE
					no_pokok = '$np_karyawan'
				AND in_out = '1'
				AND tapping_type = '0'
				AND tapping_time > '$dws_out_kemarin' /*DWS out kemarin*/
				AND SUBSTR(tapping_time, 1, 16) > SUBSTR('$tapping_out_kemarin', 1, 16) /*Tapping out kemarin*/
				AND tapping_time < '$dws_out_sekarang' /*DWS OUT Sekarang*/
				AND tapping_time < '$dws_in_besok' /*EDIT DWS in besok*/
			) LIMIT 1
		")->row_array();
		
		return $query;
	}
	
	public function search_tapping_out($tanggal_dws, $np_karyawan,$tabel_sekarang,$tabel_besok,$dws_in_sekarang,$dws_in_besok,$tapping_out_kemarin,$tapping_in)
	{
		$tabel_sekarang=str_replace("-","_",$tabel_sekarang);
		$tabel_besok=str_replace("-","_",$tabel_besok);
		
		if(!$this->check_table_exist($tabel_sekarang))
		{
			$tabel_sekarang = 'pamlek_data';
		}
		if(!$this->check_table_exist($tabel_besok))
		{
			$tabel_besok = 'pamlek_data';
		}
		
		$query = $this->db->query("
		/*DWS out Tanggal $tanggal_dws*/
		SELECT
			*
		FROM
			$tabel_sekarang /*Tabel Bulan Sekarang*/
		WHERE
			no_pokok = '$np_karyawan'
		AND in_out = '0'
		AND tapping_type = '0'
		AND tapping_time = (
			SELECT
				max(tapping_time)
			FROM
				$tabel_sekarang /*Tabel Bulan Sekarang*/
			WHERE
				no_pokok = '$np_karyawan'
			AND in_out = '0'
			AND tapping_type = '0'
			AND SUBSTR(tapping_time, 1, 16) > SUBSTR('$tapping_out_kemarin', 1, 16) /*Tapping out kemarin*/
			AND tapping_time > '$dws_in_sekarang' /*DWS in sekarang*/
			AND tapping_time < '$dws_in_besok' /*DWS in besok*/
		) 
		UNION
			SELECT
				*
			FROM
				$tabel_besok /*Tabel Bulan Besok*/
			WHERE
				no_pokok = '$np_karyawan'
			AND in_out = '0'
			AND tapping_type = '0'
			AND tapping_time = (
				SELECT
					max(tapping_time)
				FROM
					$tabel_besok /*Tabel Bulan Besok*/
				WHERE
					no_pokok = '$np_karyawan'
				AND in_out = '0'
				AND tapping_type = '0'
				AND tapping_time > '$tapping_in' /*Tapping IN nya*/
				AND SUBSTR(tapping_time, 1, 16) > SUBSTR('$tapping_out_kemarin', 1, 16) /*Tapping out kemarin*/
				AND tapping_time > '$dws_in_sekarang' /*DWS in sekarang*/
				AND tapping_time < '$dws_in_besok' /*DWS in besok*/
			) LIMIT 1
		")->row_array();
		
		return $query;
	}
	
	public function check_cico($tabel, $np_karyawan,$dws_tanggal)
	{
		$tabel=str_replace("-","_",$tabel);
		
		$this->db->select('*');
		$this->db->from($tabel);	
		$this->db->where('np_karyawan', $np_karyawan);
		$this->db->where('dws_tanggal', $dws_tanggal);
				
		$query 	= $this->db->get();
		
		return $query->row_array();
	}
	
	public function update_cico($tabel, $np_karyawan, $dws_tanggal, $data)
	{
		$tabel=str_replace("-","_",$tabel);
		
		$this->db->where('np_karyawan', $np_karyawan);
		$this->db->where('dws_tanggal', $dws_tanggal);
		$this->db->update($tabel, $data);
				
		
	}
	
	public function insert_cico($tabel, $data)
	{
		$tabel=str_replace("-","_",$tabel);
		
		return  $this->db->insert($tabel,$data);	
				
		
	}

}
