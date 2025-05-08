<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_permohonan_spbi extends CI_Model {
    function select_daftar_karyawan($params){
		if($params["grup"]==4) {			
			$ada_data=0;
			$var=array();
			$list_pengadministrasi = $params["list_pengadministrasi"];
			foreach ($list_pengadministrasi as $data) {	
				array_push($var,$data['kode_unit']);
				$ada_data=1;
			}

			if($ada_data==0) $var='';
		} else if($params["grup"]==5) {
			$var = $params["no_pokok"];
		} else {
			$var = '';				
		}	
			
		$this->db->select('*');
		$this->db->from('mst_karyawan');
		
		if($params["grup"]==4) {
			$this->db->where_in('mst_karyawan.kode_unit', $var);								
		} else if($params["grup"]==5) {
			$this->db->where('mst_karyawan.no_pokok', $var);	
		}
		
		$data = $this->db->get();
		return $data;
	}
}