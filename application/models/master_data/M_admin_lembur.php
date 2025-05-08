<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_admin_lembur extends CI_Model {
	
    private $db;
    
	public function __construct(){
		parent::__construct();
        $this->db= $this->load->database('metadata',TRUE);
		//Do your magic here
	}
	
    public function get_realisasi_target_divisi(){
        $this->db->select('a.realisasi, SUM(b.total_asumsi_i) as target, a.divisi');
        $this->db->from('(SELECT (SUM(ifnull(a.3001_uang_lembur_15,0)) +
            SUM(ifnull(a.3002_uang_lembur_2,0)) +
            SUM(ifnull(a.3003_uang_lembur_3,0)) +
            SUM(ifnull(a.3004_uang_lembur_4,0)) +
            SUM(ifnull(a.3005_uang_lembur_5,0)) +
            SUM(ifnull(a.3006_uang_lembur_6,0)) +
            SUM(ifnull(a.3007_uang_lembur_7,0)) +
            SUM(ifnull(a.3100_uang_lembur_manual,0))  +
            SUM(ifnull(a.3110_insentif_lembur,0)) +
            SUM(ifnull(a.3400_uang_lembur_susulan,0))) as realisasi, a.divisi, a.abbr_divisi FROM sap_hcm_rekap_lembur a 
            WHERE a.abbr_divisi IS NOT NULL GROUP BY a.abbr_divisi) a');
        $this->db->join('manual_target_lembur b', 'b.abbr_div = a.abbr_divisi');
        $this->db->group_by('a.abbr_divisi');
        $this->db->limit(10);
        $query = $this->db->get();
        $result=$query->result();
        return $result;
    }

    public function get_realisasi_target_departemen($start_date = null, $end_date = null){
        $this->db->select('a.realisasi, SUM(b.total_asumsi_i) as target, a.departemen');
        $this->db->from('(SELECT (SUM(ifnull(a.3001_uang_lembur_15,0)) +
            SUM(ifnull(a.3002_uang_lembur_2,0)) +
            SUM(ifnull(a.3003_uang_lembur_3,0)) +
            SUM(ifnull(a.3004_uang_lembur_4,0)) +
            SUM(ifnull(a.3005_uang_lembur_5,0)) +
            SUM(ifnull(a.3006_uang_lembur_6,0)) +
            SUM(ifnull(a.3007_uang_lembur_7,0)) +
            SUM(ifnull(a.3100_uang_lembur_manual,0)) +
            SUM(ifnull(a.3110_insentif_lembur,0)) +
            SUM(ifnull(a.3400_uang_lembur_susulan,0))) as realisasi, a.departemen, a.abbr_departemen FROM sap_hcm_rekap_lembur a
            WHERE a.abbr_departemen IS NOT NULL
            '. ($start_date != null ? 'AND (CONCAT(YEAR("'.$start_date.'"),"-",(a.periode_bulan),"-01")) >= "'.$start_date.'" ' : '') .'
            '. ($end_date != null ? 'AND (CONCAT(YEAR("'.$end_date.'"),"-",(a.periode_bulan),"-01")) <= "'.$end_date.'" ' : '') .'
            GROUP BY a.abbr_departemen)a');
        $this->db->join('manual_target_lembur b', 'b.abbr_dep = a.abbr_departemen');
        $this->db->group_by('a.abbr_departemen');
        $query = $this->db->get();
        $result=$query->result();
        return $result;
    }

    public function get_top_divisi(){
        $this->db->select('(SUM(ifnull(a.3001_uang_lembur_15,0)) +
            SUM(ifnull(a.3002_uang_lembur_2,0)) +
            SUM(ifnull(a.3003_uang_lembur_3,0)) +
            SUM(ifnull(a.3004_uang_lembur_4,0)) +
            SUM(ifnull(a.3005_uang_lembur_5,0)) +
            SUM(ifnull(a.3006_uang_lembur_6,0)) +
            SUM(ifnull(a.3007_uang_lembur_7,0)) +
            SUM(ifnull(a.3100_uang_lembur_manual,0))  +
            SUM(ifnull(a.3110_insentif_lembur,0)) +
            SUM(ifnull(a.3400_uang_lembur_susulan,0))) as uang_lembur, a.divisi, SUM(a.total_jam_lembur) as total_jam_lembur');
        $this->db->from('sap_hcm_rekap_lembur a');
        $this->db->where('a.abbr_divisi IS NOT NULL');
        $this->db->where('a.abbr_divisi IS NOT NULL');
        $this->db->group_by('a.abbr_divisi');
        $this->db->order_by('uang_lembur', 'DESC');
        $this->db->limit(10);
        $query = $this->db->get();
        $result=$query->result();
        return $result;
    }

    public function get_top_departemen(){
        $this->db->select('(SUM(ifnull(a.3001_uang_lembur_15,0)) +
            SUM(ifnull(a.3002_uang_lembur_2,0)) +
            SUM(ifnull(a.3003_uang_lembur_3,0)) +
            SUM(ifnull(a.3004_uang_lembur_4,0)) +
            SUM(ifnull(a.3005_uang_lembur_5,0)) +
            SUM(ifnull(a.3006_uang_lembur_6,0)) +
            SUM(ifnull(a.3007_uang_lembur_7,0)) +
            SUM(ifnull(a.3100_uang_lembur_manual,0))  +
            SUM(ifnull(a.3110_insentif_lembur,0)) +
            SUM(ifnull(a.3400_uang_lembur_susulan,0))) as uang_lembur, a.departemen, SUM(a.total_jam_lembur) as total_jam_lembur');
        $this->db->from('sap_hcm_rekap_lembur a');
        $this->db->where('a.abbr_divisi IS NOT NULL');
        $this->db->group_by('a.departemen');
        $this->db->order_by('uang_lembur', 'DESC');
        $this->db->limit(10);
        $query = $this->db->get();
        $result=$query->result();
        return $result;
    }

    public function get_rekap_lembur_bulan(){
        $this->db->select('(SUM(ifnull(a.3001_uang_lembur_15,0)) +
            SUM(ifnull(a.3002_uang_lembur_2,0)) +
            SUM(ifnull(a.3003_uang_lembur_3,0)) +
            SUM(ifnull(a.3004_uang_lembur_4,0)) +
            SUM(ifnull(a.3005_uang_lembur_5,0)) +
            SUM(ifnull(a.3006_uang_lembur_6,0)) +
            SUM(ifnull(a.3007_uang_lembur_7,0)) +
            SUM(ifnull(a.3100_uang_lembur_manual,0))  +
            SUM(ifnull(a.3110_insentif_lembur,0)) +
            SUM(ifnull(a.3400_uang_lembur_susulan,0))) as Jumlah');
        $this->db->from('sap_hcm_rekap_lembur a');
        $this->db->where('a.abbr_divisi IS NOT NULL');
        $this->db->group_by('a.periode_bulan, a.periode_tahun');
        $query = $this->db->get();
        $result=$query->result();
        return $result;
    }

}