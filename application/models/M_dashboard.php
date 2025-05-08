<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class M_dashboard extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
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

	public function checkDateIzin($tahun_bulan)
	{
		$this->db->query("SELECT table_name FROM information_schema.tables WHERE table_schema='ess' AND table_name like 'ess_perizinan_$tahun_bulan%'")->result();
		if ($this->db->affected_rows() > 0) {
			return false;
		} else {
			return true;
		}
	}

	public function checkDateKehadiran($tahun_bulan)
	{
		$this->db->query("SELECT table_name FROM information_schema.tables WHERE table_schema='ess' AND table_name like 'ess_cico_$tahun_bulan%'")->result();
		if ($this->db->affected_rows() > 0) {
			return false;
		} else {
			return true;
		}
	}

	public function getTotalCuti()
	{
		return $this->db->where('MONTH(created_at) = MONTH(CURRENT_DATE())')->count_all('ess_cuti');
	}

	public function getTotalLembur()
	{
		return $this->db->count_all_results('ess_lembur_transaksi');
	}

	public function getTotalIzin($table)
	{
		return $this->db->count_all($table);
		// return $this->db->count_all('ess_perizinan_'.date("Y_m"));
	}

	public function getTotalAB()
	{
		return $this->db->count_all_results('ab_karyawan');
	}

	public function getTotalDinas()
	{
		return $this->db->where('id_sppd !=', null)->count_all_results('ess_sppd');
	}

	public function getGrafikKehadiran($date, $checkDateIzin)
	{
		$table = "ess_cico_$date";
		if ($checkDateIzin != 'true') {
			$table = 'ess_cico';
		}

		$from = '(select id_cuti, np_karyawan, dws_in_tanggal, dws_tanggal, dws_name_fix, dws_name, id_sppd, (case when tapping_fix_1 is not null then tapping_fix_1 else tapping_time_1 end) as tapping_1, (case when tapping_fix_2 is not null then tapping_fix_2 else tapping_time_2 end) as tapping_2, count(*) as jml from ' . $table . ') as abc';
		$query1 = $this->db->select("*, 'Kehadiran' as nama")->from($from)->where("(tapping_1 is not null OR tapping_1 = '') AND (tapping_2 is not null OR tapping_2 = '')")->where('id_cuti', null)->where('id_sppd', null)->get()->result();
		$query2 = $this->db->select("*, 'TK' as nama")->from($from)->where("(tapping_1 is not null OR tapping_1 = '') AND (tapping_2 is not null OR tapping_2 = '')")->where('id_cuti', null)->where('id_sppd', null)->get()->result();
		$query3 = $this->db->select("*, 'TM' as nama")->from($from)->where("(tapping_1 is not null OR tapping_1 = '') AND (tapping_2 is not null OR tapping_2 = '')")->where('id_cuti', null)->where('id_sppd', null)->get()->result();
		$query4 = $this->db->select("*, 'AB' as nama")->from($from)->where("(tapping_1 is not null OR tapping_1 = '') AND (tapping_2 is not null OR tapping_2 = '')")->where('id_cuti', null)->where('id_sppd', null)->get()->result();
		$query5 = $this->db->select("'Cuti' as nama, COUNT(*) as jml")->where('approval_sdm =', '1')->where('id_cuti !=', null)->get($table)->result();
		$query6 = $this->db->select("'Dinas' as nama, COUNT(*) as jml")->where('id_sppd !=', null)->get($table)->result();

		return array_merge($query1, $query2, $query3, $query4, $query5, $query6);
	}

	public function getCutiNeedApproval()
	{
		return $this->db->select('ess_cuti.np_karyawan, start_date, end_date, jumlah_hari, alasan, approval_1, approval_2, approval_sdm_by, uraian')
			->join('mst_cuti', 'ess_cuti.absence_type = mst_cuti.kode_erp', 'left')
			//->where('MONTH(created_at) = MONTH(CURRENT_DATE())')
			->group_start()
			// ->where('approval_1',$np)
			->group_start()
			->where('status_1', '0')
			->or_where('status_1 is null')
			->group_end()
			// ->or_where('approval_2',$np)
			->group_start()
			->where('status_2', '0')
			->or_where('status_2 is null')
			->group_end()
			->group_end()
			->get('ess_cuti');
	}

	public function getCutiNeedApproval_where($np)
	{
		$current_date_before 	= date('Y-m-d', strtotime(date('Y-m-d') . ' -1 month'));
		$pisah 					= explode("-", $current_date_before);
		$month_before 			=  $pisah[1];

		return $this->db->select('ess_cuti.np_karyawan, ess_cuti.nama, start_date, end_date, jumlah_hari, jumlah_bulan, alasan, approval_1, approval_2, approval_sdm_by, uraian')
			->join('mst_cuti', 'ess_cuti.absence_type = mst_cuti.kode_erp', 'left')
			->where("(MONTH(created_at) = MONTH(CURRENT_DATE()) OR MONTH(created_at) ='$month_before')")
			->where("((approval_1='" . $np . "' and (status_1='0' or status_1=null)) OR (approval_2='" . $np . "' and (status_2='0' or status_2=null))) and approval_sdm='0'")
			->get('ess_cuti');
	}

	public function getKehadiranNeedApproval_where($np, $checkDateKehadiran)
	{
		$tahunbulan 		= str_replace("_", "-", $checkDateKehadiran);
		$tahunbulantanggal 	= $tahunbulan . "-01";

		$checkDateKehadiranBefore = date('Y-m-d', strtotime($tahunbulantanggal . ' -1 month'));
		$checkDateKehadiranBefore = str_replace("-", "_", $checkDateKehadiranBefore);
		$checkDateKehadiranBefore = substr($checkDateKehadiranBefore, 0, 7);

		if (check_table_exist("ess_cico_" . $checkDateKehadiran) == 'ada') {
			$tabel 	= "ess_cico_" . $checkDateKehadiran;
		} else {
			$tabel	= "ess_cico";
		}

		if (check_table_exist("ess_cico_" . $checkDateKehadiranBefore) == 'ada') {
			$tabel_before 	= "ess_cico_" . $checkDateKehadiranBefore;
		} else {
			$tabel_before	= "ess_cico";
		}

		return $this->db->query("SELECT np_karyawan, nama, dws_tanggal, tapping_fix_approval_np, tapping_fix_approval_ket FROM $tabel WHERE tapping_fix_approval_np = '$np' AND tapping_fix_approval_status = '0'
									UNION 
								 SELECT np_karyawan, nama, dws_tanggal, tapping_fix_approval_np, tapping_fix_approval_ket FROM $tabel_before WHERE tapping_fix_approval_np = '$np' AND tapping_fix_approval_status = '0'");
	}

	public function getPelatihanNeedApproval_wheredate($np, $tahun_bulan)
	{
		return $this->db->select('np_karyawan, nama, approval_1, approval_2, created_at, pelatihan')
			->from('ess_diklat_kebutuhan_pelatihan')
			->where("DATE_FORMAT(created_at, '%Y-%m') =", $tahun_bulan)
			->where("(
					(approval_1 = '$np' AND (status_1 = '0' OR status_1 IS NULL) AND (status_2 != '2' OR status_2 IS NULL)) 
					OR 
					(approval_2 = '$np' AND (status_2 = '0' OR status_2 IS NULL) AND (status_1 != '2' OR status_1 IS NULL))
				)")
			->order_by('created_at', 'ASC')
			->get();
	}

	public function getPelatihanNeedApproval_where($np)
	{
		$current_date_before 	= date('Y-m-d', strtotime(date('Y-m-d') . ' -1 month'));
		$pisah 					= explode("-", $current_date_before);
		$month_before 			=  $pisah[1];

		return $this->db->select('np_karyawan, nama, approval_1, approval_2, created_at, pelatihan')
			->from('ess_diklat_kebutuhan_pelatihan')
			->where("(MONTH(created_at) = MONTH(CURRENT_DATE()) OR MONTH(created_at) ='$month_before')")
			->where("(
				(approval_1 = '$np' AND (status_1 = '0' OR status_1 IS NULL) AND (status_2 != '2' OR status_2 IS NULL)) 
				OR 
				(approval_2 = '$np' AND (status_2 = '0' OR status_2 IS NULL) AND (status_1 != '2' OR status_1 IS NULL))
			)")
			->order_by('created_at', 'ASC')
			->get();
	}

	public function getPelatihanNeedApproval()
	{
		return $this->db->select('np_karyawan, nama, approval_1, approval_2, created_at, pelatihan')
			->from('ess_diklat_kebutuhan_pelatihan')
			//->where('MONTH(created_at) = MONTH(CURRENT_DATE())')
			->group_start()
			// ->where('approval_1',$np)
			->group_start()
			->where('status_1', '0')
			->or_where('status_1 is null')
			->group_end()
			// ->or_where('approval_2',$np)
			->group_start()
			->where('status_2', '0')
			->or_where('status_2 is null')
			->group_end()
			->group_end()
			->order_by('created_at', 'ASC')
			->get('');
	}

	public function getKehadiranNotYetApproved($np, $checkDateKehadiran)
	{
		
		$tahunbulan 		= str_replace("_", "-", $checkDateKehadiran);
		$tahunbulantanggal 	= $tahunbulan . "-01";

		$checkDateKehadiranBefore = date('Y-m-d', strtotime($tahunbulantanggal . ' -1 month'));
		$checkDateKehadiranBefore = str_replace("-", "_", $checkDateKehadiranBefore);
		$checkDateKehadiranBefore = substr($checkDateKehadiranBefore, 0, 7);

		if (check_table_exist("ess_cico_" . $checkDateKehadiran) == 'ada') {
			$tabel 	= "ess_cico_" . $checkDateKehadiran;
		} else {
			$tabel	= "ess_cico";
		}

		if (check_table_exist("ess_cico_" . $checkDateKehadiranBefore) == 'ada') {
			$tabel_before 	= "ess_cico_" . $checkDateKehadiranBefore;
		} else {
			$tabel_before	= "ess_cico";
		}

		return $this->db->query("
    SELECT np_karyawan, nama, dws_tanggal, tapping_fix_approval_np, tapping_fix_approval_ket
    FROM $tabel
    WHERE np_karyawan = '$np' AND tapping_fix_approval_status = '0' AND tapping_fix_approval_np IS NOT NULL
    UNION 
    SELECT np_karyawan, nama, dws_tanggal, tapping_fix_approval_np, tapping_fix_approval_ket
    FROM $tabel_before
    WHERE np_karyawan = '$np' AND tapping_fix_approval_status = '0' AND tapping_fix_approval_np IS NOT NULL
");

	}

	public function getLemburNeedApproval_where($np)
	{
		$current_date_before 	= date('Y-m-d', strtotime(date('Y-m-d') . ' -1 month'));
		$pisah 					= explode("-", $current_date_before);
		$month_before 			=  $pisah[1];

		return $this->db->select('a.no_pokok, a.nama, a.tgl_dws, a.waktu_mulai_fix, a.waktu_selesai_fix')
			->where("(MONTH(created_at) = MONTH(CURRENT_DATE()) OR MONTH(created_at) ='$month_before')")
			->where('a.approval_pimpinan_status', '0')
			->where('a.approval_status', '0')
			->where('a.approval_pimpinan_np', $np)
			->where('a.waktu_mulai_fix IS NOT NULL', NULL, FALSE)
			->where('a.waktu_selesai_fix IS NOT NULL', NULL, FALSE)
			->where('TRIM(a.waktu_mulai_fix)!=', '')
			->where('TRIM(a.waktu_selesai_fix)!=', '')
			->get('ess_lembur_transaksi a');
	}

	//robi J971 menampilkan pengajuan belum di approve atasan
	public function getLemburNotYetApproved($np)
	{

		return $this->db->select('a.no_pokok, a.approval_pimpinan_nama, a.approval_pimpinan_np, a.nama, a.tgl_dws, a.waktu_mulai_fix, a.waktu_selesai_fix')
			->where('a.approval_pimpinan_status', '0')
			->where('a.approval_status', '0')
			->where('a.no_pokok', $np)
			->where('a.waktu_mulai_fix IS NOT NULL', NULL, FALSE)
			->where('a.waktu_selesai_fix IS NOT NULL', NULL, FALSE)
			->where('TRIM(a.waktu_mulai_fix)!=', '')
			->where('TRIM(a.waktu_selesai_fix)!=', '')
			->get('ess_lembur_transaksi a');
	}
	

	public function getTotalWhere($field, $value, $checkDateKehadiran)
	{
		$date = date("Y_m");
		$where = '';
		if ($field == 'np') {
			$query['where'] = "(np_karyawan='" . $value . "')";
		} else if ($field == 'date') {
			$query['where'] = "(date_format(dws_tanggal,'%Y-%m')='" . $value . "')";
		} else {
			$query['where'] = "(np_karyawan='" . $value[0] . "' AND date_format(dws_tanggal,'%Y-%m')='" . $value[1] . "')";
		}

		if (check_table_exist("ess_cico_" . $checkDateKehadiran) == 'ada') {
			$query['table'] = "ess_cico_" . $checkDateKehadiran;
		} else {
			$query['table'] = "ess_cico";
		}

		$query['from'] = '(select id_cuti, a.np_karyawan, a.kode_unit, a.nama_unit, dws_in_tanggal, dws_tanggal, id_sppd, id_overtime, id_perizinan, (case when (dws_name_fix is not null or dws_name_fix != "") then dws_name_fix else dws_name end) as dws_name_tap, (case when (tapping_fix_1 is not null or tapping_fix_1 != "0000-00-00 00:00:00") then tapping_fix_1 else tapping_time_1 end) as tapping_1, (case when (tapping_fix_2 is not null or tapping_fix_2 != "0000-00-00 00:00:00") then tapping_fix_2 else tapping_time_2 end) as tapping_2, a.wfh from ' . $query['table'] . ' a LEFT JOIN 
		ess_cuti_bersama b ON b.np_karyawan = a.np_karyawan AND b.tanggal_cuti_bersama = a.dws_in_tanggal WHERE b.id is null) as abc';
		//yang di hitung yang tidak cuti bersama
		return $query;
	}

	public function getTotalCuti_where($field, $value, $checkDateKehadiran)
	{
		$query = $this->getTotalWhere($field, $value, $checkDateKehadiran);
		return $this->db->from($query['from'])->where($query['where'])->where('(id_cuti is not null and id_cuti != "")')->count_all_results();
	}

	public function getTotalLembur_where($field, $value, $checkDateKehadiran)
	{
		$query = $this->getTotalWhere($field, $value, $checkDateKehadiran);
		return $this->db->from($query['table'])->where($query['where'])->where('(id_overtime is not null and id_overtime != "")')->count_all_results();
	}

	public function getTotalIzin_where($field, $value, $checkDateKehadiran)
	{
		$query = $this->getTotalWhere($field, $value, $checkDateKehadiran);
		return $this->db->from($query['from'])->where($query['where'])->where('(id_perizinan is not null and id_perizinan != "")')->count_all_results();
	}

	public function getTotalDinas_where($field, $value, $checkDateKehadiran)
	{
		$query = $this->getTotalWhere($field, $value, $checkDateKehadiran);
		return $this->db->from($query['from'])->where($query['where'])->where('(id_sppd is not null and id_sppd != "")')->count_all_results();
	}

	public function getABKaryawan_where($np, $date)
	{


		$query['where'] = "(np_karyawan='" . $np . "')";
		$query['where'] = "(np_karyawan='" . $np . "' AND table_name='ess_cico_" . $date . "')";
		$query['table'] = "ab_karyawan";

		$query['from'] = '(select a.* from ' . $query['table'] . ' a';
		//yang di hitung yang tidak cuti bersama
		return $this->db->from($query['table'])->where($query['where'])->count_all_results();
	}

	public function getGrafikKehadiran_where($field, $value, $checkDateKehadiran)
	{
		//16 03 2020, Tri Wibowo. WFH COVID19
		$Date = date('Y-m-d');
		$date_minus_2 =  date('Y-m-d', strtotime($Date . ' - 2 days'));

		$date = date("Y_m");
		$query = $this->getTotalWhere($field, $value, $checkDateKehadiran);

		$table_name = $query['table'];
		// $table_name = $field;

		//$from = '(select id_cuti, np_karyawan, dws_in_tanggal, dws_tanggal, id_sppd, (case when (dws_name_fix is not null or dws_name_fix != "") then dws_name_fix else dws_name end) as dws_name_tap, (case when (tapping_fix_1 is not null or tapping_fix_1 != "0000-00-00 00:00:00") then tapping_fix_1 else tapping_time_1 end) as tapping_1, (case when (tapping_fix_2 is not null or tapping_fix_2 != "0000-00-00 00:00:00") then tapping_fix_2 else tapping_time_2 end) as tapping_2 from '.$query['table'].') as abc';

		//kehadiran
		$query_kehadiran = $this->db->select("count(*) as jml, 'Kehadiran' as nama")->where($query['where'])->from($query['from'])->where("(((tapping_1 is not null AND tapping_1 != '0000-00-00 00:00:00') AND (tapping_2 is not null AND tapping_2 != '0000-00-00 00:00:00')) OR ((id_perizinan != '' AND id_perizinan is not null) AND ((tapping_1 is not null AND tapping_1 != '0000-00-00 00:00:00') OR (tapping_2 is not null AND tapping_2 != '0000-00-00 00:00:00'))) )")->where('(dws_tanggal not in (select tanggal from mst_hari_libur))')->where('(dws_name_tap != "OFF" and dws_name_tap is not null and dws_name_tap != "")')->where('(id_cuti is null or id_cuti="")')->where('(id_sppd is null or id_sppd="")')->where('wfh!=', "1")->get()->result();

		//wfh
		$query_wfh = $this->db->select("count(*) as jml, 'WFH' as nama")->where($query['where'])->from($query['from'])->where("(((tapping_1 is not null AND tapping_1 != '0000-00-00 00:00:00') AND (tapping_2 is not null AND tapping_2 != '0000-00-00 00:00:00')) OR ((id_perizinan != '' AND id_perizinan is not null) AND ((tapping_1 is not null AND tapping_1 != '0000-00-00 00:00:00') OR (tapping_2 is not null AND tapping_2 != '0000-00-00 00:00:00'))) )")->where('(dws_tanggal not in (select tanggal from mst_hari_libur))')->where('(dws_name_tap != "OFF" and dws_name_tap is not null and dws_name_tap != "")')->where('(id_cuti is null or id_cuti="")')->where('(id_sppd is null or id_sppd="")')->where('wfh', "1")->get()->result();

		//TM
		$query_tm = $this->db->select("count(*) as jml, 'TM' as nama")->where($query['where'])->from($query['from'])->where("((tapping_1 is null OR tapping_1 = '0000-00-00 00:00:00') AND (tapping_2 is not null AND tapping_2 != '0000-00-00 00:00:00'))")->where('(dws_tanggal not in (select tanggal from mst_hari_libur))')->where('(dws_name_tap != "OFF" and dws_name_tap is not null and dws_name_tap != "")')->where('(id_cuti is null or id_cuti="")')->where('(id_sppd is null or id_sppd="")')->where('(id_perizinan is null or id_perizinan="")')->where("(dws_tanggal <= '$date_minus_2' )")->get()->result();

		//TK
		$query_tk = $this->db->select("count(*) as jml, 'TK' as nama")->where($query['where'])->from($query['from'])->where("((tapping_1 is not null OR tapping_1 != '0000-00-00 00:00:00') AND (tapping_2 is null OR tapping_2 = '0000-00-00 00:00:00'))")->where('(dws_tanggal not in (select tanggal from mst_hari_libur))')->where('(dws_name_tap != "OFF" and dws_name_tap is not null and dws_name_tap != "")')->where('(id_cuti is null or id_cuti="")')->where('(id_sppd is null or id_sppd="")')->where('(id_perizinan is null or id_perizinan="")')->where("(dws_tanggal <= '$date_minus_2' )")->get()->result();

		//AB
		$query_ab = $this->db->select("count(*) as jml, 'AB' as nama")->where($query['where'])->from($query['from'])->where("((tapping_1 is null OR tapping_1 = '0000-00-00 00:00:00') AND (tapping_2 is null OR tapping_2 = '0000-00-00 00:00:00'))")->where('(dws_tanggal not in (select tanggal from mst_hari_libur))')->where('(dws_name_tap != "OFF" and dws_name_tap is not null and dws_name_tap != "")')->where('(id_cuti is null or id_cuti="")')->where('(id_sppd is null or id_sppd="")')->where('(id_perizinan is null or id_perizinan="")')->where("(dws_tanggal <= '$date_minus_2' )")->get()->result();

		//Perjalanan Dinas
		$query_dinas = $this->db->select("count(*) as jml, 'Dinas' as nama")->from($query['from'])->where($query['where'])->where('(id_sppd is not null and id_sppd != "")')->get()->result();

		//Cuti
		$query_cuti = $this->db->select("count(*) as jml, 'Cuti' as nama")->from($query['from'])->where($query['where'])->where('(id_cuti is not null and id_cuti != "")')->get()->result();

		return array_merge($query_kehadiran, $query_dinas, $query_tm, $query_ab, $query_wfh, $query_cuti, $query_tk);
	}

	function filter_kehadiran_unit($field, $value, $checkDateKehadiran){
		$query = $this->getTotalWhere($field, $value, $checkDateKehadiran);
		$from = $query['from'];
		return $this->db->select('kode_unit, nama_unit')
			->from($from)
			->group_by('kode_unit, nama_unit')
			->get()->result();
	}

	public function getGrafikKehadiran_where_by_unit($field, $value, $checkDateKehadiran, $unit){
		$Date = date('Y-m-d');
		$date_minus_2 =  date('Y-m-d', strtotime($Date . ' - 2 days'));

		$date = date("Y_m");
		$query = $this->getTotalWhere($field, $value, $checkDateKehadiran);

		$table_name = $query['table'];

		//kehadiran
		if($unit!='') $this->db->where('kode_unit', $unit);
		$query_kehadiran = $this->db->select("count(DISTINCT np_karyawan) as jml, 'Kehadiran' as nama")
			->from($query['from'])
			->where("(((tapping_1 is not null AND tapping_1 != '0000-00-00 00:00:00') AND (tapping_2 is not null AND tapping_2 != '0000-00-00 00:00:00')) OR ((id_perizinan != '' AND id_perizinan is not null) AND ((tapping_1 is not null AND tapping_1 != '0000-00-00 00:00:00') OR (tapping_2 is not null AND tapping_2 != '0000-00-00 00:00:00'))) )")
			->where('(dws_tanggal not in (select tanggal from mst_hari_libur))')
			->where('(dws_name_tap != "OFF" and dws_name_tap is not null and dws_name_tap != "")')
			->where('(id_cuti is null or id_cuti="")')
			->where('(id_sppd is null or id_sppd="")')
			->where('wfh!=', "1")
			->get()->result();

		//wfh
		if($unit!='') $this->db->where('kode_unit', $unit);
		$query_wfh = $this->db->select("count(DISTINCT np_karyawan) as jml, 'WFH' as nama")
			->from($query['from'])
			->where("(((tapping_1 is not null AND tapping_1 != '0000-00-00 00:00:00') AND (tapping_2 is not null AND tapping_2 != '0000-00-00 00:00:00')) OR ((id_perizinan != '' AND id_perizinan is not null) AND ((tapping_1 is not null AND tapping_1 != '0000-00-00 00:00:00') OR (tapping_2 is not null AND tapping_2 != '0000-00-00 00:00:00'))) )")
			->where('(dws_tanggal not in (select tanggal from mst_hari_libur))')
			->where('(dws_name_tap != "OFF" and dws_name_tap is not null and dws_name_tap != "")')
			->where('(id_cuti is null or id_cuti="")')
			->where('(id_sppd is null or id_sppd="")')
			->where('wfh', "1")
			->get()->result();

		//TM
		if($unit!='') $this->db->where('kode_unit', $unit);
		$query_tm = $this->db->select("count(DISTINCT np_karyawan) as jml, 'TM' as nama")
			->from($query['from'])
			->where("((tapping_1 is null OR tapping_1 = '0000-00-00 00:00:00') AND (tapping_2 is not null AND tapping_2 != '0000-00-00 00:00:00'))")
			->where('(dws_tanggal not in (select tanggal from mst_hari_libur))')
			->where('(dws_name_tap != "OFF" and dws_name_tap is not null and dws_name_tap != "")')
			->where('(id_cuti is null or id_cuti="")')
			->where('(id_sppd is null or id_sppd="")')
			->where('(id_perizinan is null or id_perizinan="")')
			// ->where("(dws_tanggal <= '$date_minus_2' )")
			->get()->result();

		//TK
		if($unit!='') $this->db->where('kode_unit', $unit);
		$query_tk = $this->db->select("count(DISTINCT np_karyawan) as jml, 'TK' as nama")
			->from($query['from'])
			->where("((tapping_1 is not null OR tapping_1 != '0000-00-00 00:00:00') AND (tapping_2 is null OR tapping_2 = '0000-00-00 00:00:00'))")
			->where('(dws_tanggal not in (select tanggal from mst_hari_libur))')
			->where('(dws_name_tap != "OFF" and dws_name_tap is not null and dws_name_tap != "")')
			->where('(id_cuti is null or id_cuti="")')
			->where('(id_sppd is null or id_sppd="")')
			->where('(id_perizinan is null or id_perizinan="")')
			// ->where("(dws_tanggal <= '$date_minus_2' )")
			->get()->result();

		//AB
		if($unit!='') $this->db->where('kode_unit', $unit);
		$query_ab = $this->db->select("count(DISTINCT np_karyawan) as jml, 'AB' as nama")
			->from($query['from'])
			->where("((tapping_1 is null OR tapping_1 = '0000-00-00 00:00:00') AND (tapping_2 is null OR tapping_2 = '0000-00-00 00:00:00'))")
			->where('(dws_tanggal not in (select tanggal from mst_hari_libur))')
			->where('(dws_name_tap != "OFF" and dws_name_tap is not null and dws_name_tap != "")')
			->where('(id_cuti is null or id_cuti="")')
			->where('(id_sppd is null or id_sppd="")')
			->where('(id_perizinan is null or id_perizinan="")')
			// ->where("(dws_tanggal <= '$date_minus_2' )")
			->get()->result();

		//Perjalanan Dinas
		if($unit!='') $this->db->where('kode_unit', $unit);
		$query_dinas = $this->db->select("count(DISTINCT np_karyawan) as jml, 'Dinas' as nama")
			->from($query['from'])
			->where('(id_sppd is not null and id_sppd != "")')
			->get()->result();

		//Cuti
		if($unit!='') $this->db->where('kode_unit', $unit);
		$query_cuti = $this->db->select("count(DISTINCT np_karyawan) as jml, 'Cuti' as nama")
			->from($query['from'])
			->where('(id_cuti is not null and id_cuti != "")')
			->get()->result();

		return array_merge($query_kehadiran, $query_dinas, $query_tm, $query_ab, $query_wfh, $query_cuti, $query_tk);
	}
}
