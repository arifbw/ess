<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_rekapitulasi_bulanan extends CI_Model {

	
	public function __construct(){
		parent::__construct();
		//Do your magic here
		$this->tabel_cico = "ess_cico_";
		$this->tabel_mst_jadwal = "mst_jadwal_kerja";
		$this->tabel_perjalanan_dinas = "ess_sppd";
	}
	
	public function jadwal_kerja($np_karyawan,$periode){
		$this->tabel_cico .= $periode;
		
		$data = $this->db->query("SELECT * FROM (SELECT a1.dws_tanggal tertanggal, ifnull(a1.dws_name_fix,a1.dws_name) dws_name, ifnull(a1.dws_in_tanggal_fix, a1.dws_in_tanggal) jadwal_tanggal_masuk, ifnull(a1.dws_in_fix, a1.dws_in) jadwal_jam_masuk, ifnull(a1.dws_out_tanggal_fix, a1.dws_out_tanggal) jadwal_tanggal_pulang, ifnull(a1.dws_out_fix, a1.dws_out) jadwal_jam_pulang, ifnull(a1.dws_break_start_fix,a1.dws_break_start) dws_break_start, ifnull(a1.dws_break_end_fix, a1.dws_break_end) dws_break_end, ifnull(a1.tapping_fix_1,a1.tapping_time_1) datang, ifnull(a1.tapping_fix_2, a1.tapping_time_2) pulang, a1.wfh, a1.id_cuti, a1.id_sppd, a2.perihal perihal_dinas FROM ".$this->tabel_cico." a1 LEFT JOIN ".$this->tabel_perjalanan_dinas." a2 ON a1.id_sppd=a2.id WHERE a1.np_karyawan='$np_karyawan') a join mst_jadwal_kerja b ON a.dws_name=b.dws and a.jadwal_jam_masuk=b.dws_start_time and a.jadwal_jam_pulang=b.dws_end_time and a.dws_break_start=b.dws_break_start_time and a.dws_break_end=b.dws_break_end_time")->result_array();//echo $this->db->last_query();
		return $data; 
	}
}

/* End of file M_rekapitulasi_bulanan.php */
/* Location: ./application/models/informasi/M_rekapitulasi_bulanan.php */