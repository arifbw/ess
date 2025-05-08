<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_kuesioner_api extends CI_Model {
    
    # cek row exist
	function check_if_exist($params) {
		return $this->db->where($params)->get('ess_self_assesment_covid19');
	}
    
    function get_data($params){
        /*
        $params = ['grup'=>value, 'var'=>value, 'month'=>value]
        */
        
        if(@$params["grup"]==4) {
			$this->db->where_in('kode_unit', $params['var']);								
		} else if(@$params["grup"]==5) {
			$this->db->where('np_karyawan', $params['var']);	
		}

		if(@$params['month']){
            $this->db->where("DATE_FORMAT(tanggal,'%Y-%m')", $params['month']);
        }
        
		$this->db->from('ess_self_assesment_covid19');	
        return $this->db->get();
    }
    
    function edit($id){
        return $this->db->where('id',$id)->get('ess_self_assesment_covid19');
    }
    
}