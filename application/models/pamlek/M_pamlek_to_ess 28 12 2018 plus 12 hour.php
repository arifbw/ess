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
		$this->db->query("CREATE TABLE $name AS SELECT * FROM ess_cico");
		
		$this->alter_table($name);
	}
	
	function alter_table($name)
	{
		$this->db->query("ALTER TABLE $name MODIFY id INT AUTO_INCREMENT PRIMARY KEY");
	}
	
	public function select_master_data($tahun_bulan,$tanggal_dws,$np_karyawan)
	{
		$tahun_bulan=str_replace("-","_",$tahun_bulan);
		$nama_tabel = 'erp_master_data_'.$tahun_bulan;
		
		if(!$this->check_table_exist($nama_tabel))
		{
			$nama_tabel = 'erp_master_data';
		}
		
		$this->db->select('*');
		$this->db->from($nama_tabel);
		$this->db->where('tanggal_dws',$tanggal_dws);
		
		if($np_karyawan!='all')
		{
			$this->db->where('np_karyawan',$np_karyawan);
		}
		
		$this->db->order_by("np_karyawan", "ASC"); 	
		
		$query = $this->db->get();
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
		
		if($np_karyawan!=0)
		{
			$this->db->where('np_karyawan',$np_karyawan);
		}
		
		$this->db->order_by("np_karyawan", "ASC"); 	
		
		$query = $this->db->get();
		return $query;
	}
	
	public function search_tapping_in($tanggal_dws,$np_karyawan,$tabel_pamlek,$tabel_pamlek_kemarin,$batas_awal,$batas_akhir,$tapping_out_kemarin)
	{
		$tabel_pamlek=str_replace("-","_",$tabel_pamlek);
		$tabel_pamlek_kemarin=str_replace("-","_",$tabel_pamlek_kemarin);
		
		if(!$this->check_table_exist($tabel_pamlek))
		{
			$tabel_pamlek = 'pamlek_data';
		}
		if(!$this->check_table_exist($tabel_pamlek_kemarin))
		{
			$tabel_pamlek_kemarin = 'pamlek_data';
		}
		
		$query = $this->db->query("
		/*DWS IN Tanggal $tanggal_dws*/
		SELECT
			*
		FROM
			$tabel_pamlek /*Tabel Bulan Sekarang*/
		WHERE
			no_pokok = '$np_karyawan'
		AND in_out = '1'
		AND tapping_type = '0'
		AND tapping_time = (
			SELECT
				IFNULL(
					(
						SELECT
							min(tapping_time)
						FROM
							$tabel_pamlek /*Tabel Bulan Sekarang*/
						WHERE
							no_pokok = '$np_karyawan'
						AND in_out = '1'
						AND tapping_type = '0'
						AND tapping_time > '$tapping_out_kemarin' /*tapping_out_kemarin*/
						AND tapping_time >= '$batas_awal' /*batas awal*/
						AND tapping_time <= '$batas_akhir' /*batas akhir*/
					),
					(
						SELECT
							min(tapping_time)
						FROM
							$tabel_pamlek_kemarin /*Tabel Bulan KEMARIN*/
						WHERE
							no_pokok = '$np_karyawan'
						AND in_out = '1'
						AND tapping_type = '0'
						AND tapping_time > '$tapping_out_kemarin' /*tapping_out_kemarin*/
						AND tapping_time >= '$batas_awal' /*batas awal*/
						AND tapping_time <= '$batas_akhir' /*batas akhir*/
					)
				)
		)
		LIMIT 1
		")->row_array();
		
		return $query;
	}
	
	public function search_tapping_out($tanggal_dws,$np_karyawan,$tabel_pamlek,$tabel_pamlek_besok,$batas_awal,$batas_akhir,$tapping_out_kemarin)
	{
		$tabel_pamlek=str_replace("-","_",$tabel_pamlek);
		$tabel_pamlek_besok=str_replace("-","_",$tabel_pamlek_besok);
		
		if(!$this->check_table_exist($tabel_pamlek))
		{
			$tabel_pamlek = 'pamlek_data';
		}
		if(!$this->check_table_exist($tabel_pamlek_besok))
		{
			$tabel_pamlek_besok = 'pamlek_data';
		}
		
		$query = $this->db->query("
		/*DWS out Tanggal $tanggal_dws*/
		SELECT
			*
		FROM
			$tabel_pamlek /*Tabel Bulan Sekarang*/
		WHERE
			no_pokok = '$np_karyawan'
		AND in_out = '0'
		AND tapping_type = '0'
		AND tapping_time = (
			SELECT
				IFNULL(
					(
						SELECT
							max(tapping_time)
						FROM
							$tabel_pamlek /*Tabel Bulan Sekarang*/
						WHERE
							no_pokok = '$np_karyawan'
						AND in_out = '0'
						AND tapping_type = '0'
						AND tapping_time > '$tapping_out_kemarin' /*tapping_out_kemarin*/
						AND tapping_time >= '$batas_awal' /*batas awal*/
						AND tapping_time <= '$batas_akhir' /*batas akhir*/
					),
					(
						SELECT
							max(tapping_time)
						FROM
							$tabel_pamlek_besok /*Tabel Bulan Besok*/
						WHERE
							no_pokok = '$np_karyawan'
						AND in_out = '0'
						AND tapping_type = '0'
						AND tapping_time > '$tapping_out_kemarin' /*tapping_out_kemarin*/
						AND tapping_time >= '$batas_awal' /*batas awal*/
						AND tapping_time <= '$batas_akhir' /*batas akhir*/
					)
				)
		)
		LIMIT 1
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
