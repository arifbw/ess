<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_portal_to_ess_sppd extends CI_Model {		
	function insert_batch_sto_files($data){
		$this->db->insert_batch('ess_sppd_files',$data);
	}
    
    function check_name_then_insert_data($file_name, $data) {
        $cek = $this->db->where('nama_file', $file_name)->get('ess_sppd_files')->num_rows();
        if($cek<1){
            $this->db->insert('ess_sppd_files',$data);
        }
	}
    
    function get_proses_is_nol(){
        return $this->db->select('nama_file, baris_data')->where('proses',0)->get('ess_sppd_files');
    }

	function update_files($nama_file, $data) {
		$this->db->where('nama_file', $nama_file);
		$this->db->update('ess_sppd_files', $data); 
	}
	
	function check_id_then_insert_data($data_where, $data) {
        $where = [
            'np_karyawan'=>$data_where['np_karyawan'],
            'id_sppd'=>$data_where['id_sppd']
        ];
        $cek = $this->db->where($where)->get('ess_sppd')->num_rows();
        if($cek<1){
            $proses = $this->db->insert('ess_sppd',$data);
        } else{
            $proses = $this->db->where($where)->update('ess_sppd',$data);
        }
        
        if($proses){
            return true;
        } else{
            return false;
        }
	}
}
