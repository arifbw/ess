<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_dashboard_pengguna_api extends CI_Model {
    
    public function getTotalWhere($field, $value, $checkDateKehadiran) {
		$where = '';
		if ($field == 'np') {
			$query['where'] = "(np_karyawan='".$value."')";
		}else if ($field == 'date') {
			$query['where'] = "(date_format(dws_tanggal,'%Y-%m')='".$value."')";
		}else{
			$query['where'] = "(np_karyawan='".$value[0]."' AND date_format(dws_tanggal,'%Y-%m')='".$value[1]."')";
		}
        
        if(check_table_exist("ess_cico_".$checkDateKehadiran)=='ada'){
            $query['table'] = "ess_cico_".$checkDateKehadiran;
        } else{
            $query['table'] = "ess_cico";
        }

		$query['from'] = '(select id_cuti, a.np_karyawan, dws_in_tanggal, dws_tanggal, id_sppd, id_overtime, id_perizinan, (case when (dws_name_fix is not null or dws_name_fix != "") then dws_name_fix else dws_name end) as dws_name_tap, (case when (tapping_fix_1 is not null or tapping_fix_1 != "0000-00-00 00:00:00") then tapping_fix_1 else tapping_time_1 end) as tapping_1, (case when (tapping_fix_2 is not null or tapping_fix_2 != "0000-00-00 00:00:00") then tapping_fix_2 else tapping_time_2 end) as tapping_2, a.wfh from '.$query['table'].' a LEFT JOIN 
		ess_cuti_bersama b ON CONCAT(a.np_karyawan,a.dws_in_tanggal) = CONCAT(b.np_karyawan,b.tanggal_cuti_bersama) WHERE b.id is null) as abc';
        
		return $query;
	}
    
    # cuti
	public function getTotalCuti_where($field, $value, $checkDateKehadiran) {
		$query = $this->getTotalWhere($field, $value, $checkDateKehadiran);
		return $this->db->from($query['from'])->where($query['where'])->where('(id_cuti is not null and id_cuti != "")')->count_all_results();
	}
    
    # lembur 
	public function getTotalLembur_where($field, $value, $checkDateKehadiran) {
		$query = $this->getTotalWhere($field, $value, $checkDateKehadiran);
		return $this->db->from($query['table'])->where($query['where'])->where('(id_overtime is not null and id_overtime != "")')->count_all_results();
	}
    
    # izin
	public function getTotalIzin_where($field, $value, $checkDateKehadiran) {
        $query = $this->getTotalWhere($field, $value, $checkDateKehadiran);
		return $this->db->from($query['from'])->where($query['where'])->where('(id_perizinan is not null and id_perizinan != "")')->count_all_results();
	}
    
    # dinas
	public function getTotalDinas_where($field, $value, $checkDateKehadiran) {
		$query = $this->getTotalWhere($field, $value, $checkDateKehadiran);
		return $this->db->from($query['from'])->where($query['where'])->where('(id_sppd is not null and id_sppd != "")')->count_all_results();
	}
    
    # grafik
	public function getGrafikKehadiran_where($field, $value, $checkDateKehadiran){
		//16 03 2020, Tri Wibowo. WFH COVID19
		$_Date = date('Y-m-d');
		$date_minus_2 =  date('Y-m-d', strtotime($_Date. ' - 2 days'));
		
		$query = $this->getTotalWhere($field, $value, $checkDateKehadiran);
		
		//kehadiran
		$query_kehadiran = $this->db->select("'Kehadiran' as jenis, count(*) as jml")->where($query['where'])->from($query['from'])->where("(((tapping_1 is not null AND tapping_1 != '0000-00-00 00:00:00') AND (tapping_2 is not null AND tapping_2 != '0000-00-00 00:00:00')) OR ((id_perizinan != '' AND id_perizinan is not null) AND ((tapping_1 is not null AND tapping_1 != '0000-00-00 00:00:00') OR (tapping_2 is not null AND tapping_2 != '0000-00-00 00:00:00'))) )")->where('(dws_tanggal not in (select tanggal from mst_hari_libur))')->where('(dws_name_tap != "OFF" and dws_name_tap is not null and dws_name_tap != "")')->where('(id_cuti is null or id_cuti="")')->where('(id_sppd is null or id_sppd="")')->where('wfh!=',"1")->get()->result();
		
		//wfh
		$query_wfh = $this->db->select("'WFH' as jenis, count(*) as jml")->where($query['where'])->from($query['from'])->where("(((tapping_1 is not null AND tapping_1 != '0000-00-00 00:00:00') AND (tapping_2 is not null AND tapping_2 != '0000-00-00 00:00:00')) OR ((id_perizinan != '' AND id_perizinan is not null) AND ((tapping_1 is not null AND tapping_1 != '0000-00-00 00:00:00') OR (tapping_2 is not null AND tapping_2 != '0000-00-00 00:00:00'))) )")->where('(dws_tanggal not in (select tanggal from mst_hari_libur))')->where('(dws_name_tap != "OFF" and dws_name_tap is not null and dws_name_tap != "")')->where('(id_cuti is null or id_cuti="")')->where('(id_sppd is null or id_sppd="")')->where('wfh',"1")->get()->result();
		
		//TM
		$query_tm = $this->db->select("'TM' as jenis, count(*) as jml")->where($query['where'])->from($query['from'])->where("((tapping_1 is null OR tapping_1 = '0000-00-00 00:00:00') AND (tapping_2 is not null AND tapping_2 != '0000-00-00 00:00:00'))")->where('(dws_tanggal not in (select tanggal from mst_hari_libur))')->where('(dws_name_tap != "OFF" and dws_name_tap is not null and dws_name_tap != "")')->where('(id_cuti is null or id_cuti="")')->where('(id_sppd is null or id_sppd="")')->where('(id_perizinan is null or id_perizinan="")')->where("(dws_tanggal <= '$date_minus_2' )")->get()->result();
		
		//TK
		$query_tk = $this->db->select("'TK' as jenis, count(*) as jml")->where($query['where'])->from($query['from'])->where("((tapping_1 is not null OR tapping_1 != '0000-00-00 00:00:00') AND (tapping_2 is null OR tapping_2 = '0000-00-00 00:00:00'))")->where('(dws_tanggal not in (select tanggal from mst_hari_libur))')->where('(dws_name_tap != "OFF" and dws_name_tap is not null and dws_name_tap != "")')->where('(id_cuti is null or id_cuti="")')->where('(id_sppd is null or id_sppd="")')->where('(id_perizinan is null or id_perizinan="")')->where("(dws_tanggal <= '$date_minus_2' )")->get()->result();
		
		//AB
		$query_ab = $this->db->select("'AB' as jenis, count(*) as jml")->where($query['where'])->from($query['from'])->where("((tapping_1 is null OR tapping_1 = '0000-00-00 00:00:00') AND (tapping_2 is null OR tapping_2 = '0000-00-00 00:00:00'))")->where('(dws_tanggal not in (select tanggal from mst_hari_libur))')->where('(dws_name_tap != "OFF" and dws_name_tap is not null and dws_name_tap != "")')->where('(id_cuti is null or id_cuti="")')->where('(id_sppd is null or id_sppd="")')->where('(id_perizinan is null or id_perizinan="")')->where("(dws_tanggal <= '$date_minus_2' )")->get()->result();
		
		//Perjalanan Dinas
		$query_dinas = $this->db->select("'Dinas' as jenis, count(*) as jml")->from($query['from'])->where($query['where'])->where('(id_sppd is not null and id_sppd != "")')->get()->result();
		
		//Cuti
		$query_cuti = $this->db->select("'Cuti' as jenis, count(*) as jml")->from($query['from'])->where($query['where'])->where('(id_cuti is not null and id_cuti != "")')->get()->result();

		return array_merge($query_kehadiran, $query_dinas, $query_tm, $query_ab, $query_wfh, $query_cuti, $query_tk);
	}
    
}