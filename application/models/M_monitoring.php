<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_monitoring extends CI_Model {

	public function __construct() {
		parent::__construct();
	}

	public function getKaryawan($np_karyawan = null) {
		$this->db->order_by('no_pokok', 'ASC');
		if($_SESSION["grup"]==4) { //jika Pengadministrasi Unit Kerja
			$this->db->where_in('kode_unit', $np_karyawan);
		}else if($_SESSION["grup"]==5) { //jika Pengguna
			$this->db->where('no_pokok', $np_karyawan);
		}
		return $this->db->get('mst_karyawan')->result();
	}
    
    function get_mst_proses($interval=null){
        if(@$interval){
            $this->db->where('interval', $interval);
        }
        
        return $this->db->get('mst_status_proses');
    }
    
    function get_from_id_and_date($nama_tabel=null, $id_proses=null, $waktu=null){
        $where = [
            'id_proses'=>$id_proses,
            "DATE_FORMAT(waktu,'%Y-%m-%d')"=>$waktu
        ];
        $get = $this->db->where($where)->get($nama_tabel);
        return $get;
    }
    
    function get_tahun_bulan(){
        $get = $this->db->query("SELECT DATE_FORMAT(waktu,'%Y-%m') as tahun_bulan
                                FROM `ess_status_proses_input` GROUP BY tahun_bulan
                                UNION
                                SELECT DATE_FORMAT(waktu,'%Y-%m') as tahun_bulan
                                FROM `ess_status_proses_output` GROUP BY tahun_bulan
                                ORDER BY tahun_bulan DESC")->result();
        return $get;
    }
    
    function get_monthly_proses($bulan){
        $get = $this->db->select("a.id, a.nama_file, a.in_out
        , (SELECT MAX(b.waktu) as waktu FROM ess_status_proses_input b WHERE b.id_proses=a.id AND DATE_FORMAT(b.waktu,'%Y-%m')='$bulan') as waktu_in
        , (SELECT MAX(c.waktu) as waktu FROM ess_status_proses_output c WHERE c.id_proses=a.id AND DATE_FORMAT(c.waktu,'%Y-%m')='$bulan') as waktu_out")->where('a.interval', 'monthly')->get('mst_status_proses a');
        return $get;
    }

}
