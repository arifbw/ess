<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_sppd_api extends CI_Model {
    
    function get_sppd($params){
        /*
        $params = [
            'tahun_bulan'=>value,
            'kode_unit'=>value,
            'np'=>value,
        ]
        */
        
        $this->db->select("a.*")->from('ess_sppd a');
        	
		if(@$params["kode_unit"]) {
			$this->db->where_in('a.kode_unit', $params["kode_unit"]);								
		} else if(@$params["np"]) {
			$this->db->where_in('a.np_karyawan', $params["np"]);	
		}
        
        if(@$params['tahun_bulan']){
            $this->db->where("DATE_FORMAT(a.tgl_berangkat,'%Y-%m')",$params['tahun_bulan']);
        }
        
		$this->db->order_by('(CASE WHEN a.tgl_berangkat IS NOT NULL THEN a.tgl_berangkat ELSE a.tgl_pulang END)', 'DESC');
        $this->db->order_by('a.np_karyawan', 'DESC');
        
        return $this->db->get();
    }
    
}