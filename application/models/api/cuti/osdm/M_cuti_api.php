<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_cuti_api extends CI_Model {
    
    function get_cuti($params){
        /*
        $params = [
            'tahun_bulan'=>value,
        ]
        */
        
        $this->db->select("a.*, b.uraian")->from('ess_cuti a');
        $this->db->join('mst_cuti b', 'b.kode_erp = a.absence_type', 'left');
        
        if(@$params['tahun_bulan']){
            $this->db->where("DATE_FORMAT(start_date,'%Y-%m')",$params['tahun_bulan']);
        }
        
        $this->db->where("(a.approval_sdm='0' OR a.approval_sdm='' OR a.approval_sdm is null) AND (a.status_1!='3' AND a.status_2!='3')");
        
		$this->db->order_by('(CASE WHEN a.start_date IS NOT NULL THEN a.start_date ELSE a.end_date END)', 'DESC');
        $this->db->order_by('a.np_karyawan', 'DESC');
        
        return $this->db->get();
    }
    
    # get data cuti yg menunggu persetujuan (sbg atasan)
    function get_persetujuan($params){
        /*
        $params = [
            'tahun_bulan'=>value,
            'np'=>value,
        ]
        */
        
        $this->db->select("a.*, b.uraian, (CASE WHEN approval_1='".$params['np']."' THEN '1' ELSE '2' END) AS field_approval")->from('ess_cuti a');
        $this->db->join('mst_cuti b', 'b.kode_erp = a.absence_type', 'left');
        
        if(@$params['np']) {
			$this->db->where("(a.approval_1='".$params['np']."' OR a.approval_2='".$params['np']."')");	
		}
        
        if(@$params['tahun_bulan']){
            $this->db->where("DATE_FORMAT(start_date,'%Y-%m')",$params['tahun_bulan']);
        }
        
		$this->db->order_by('(CASE WHEN a.start_date IS NOT NULL THEN a.start_date ELSE a.end_date END)', 'ASC');
        $this->db->order_by('a.np_karyawan', 'DESC');
        
        return $this->db->get();
    }
    
}