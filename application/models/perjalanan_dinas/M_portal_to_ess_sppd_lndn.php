<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_portal_to_ess_sppd_lndn extends CI_Model {		
	function insert_batch_sto_files($data){
		$this->db->insert_batch('ess_sppd_monitoring_files',$data);
	}
    
    function check_name_then_insert_data($file_name, $data) {
        $cek = $this->db->where('nama_file', $file_name)->get('ess_sppd_monitoring_files')->num_rows();
        if($cek<1){
            $this->db->insert('ess_sppd_monitoring_files',$data);
        }
	}
    
    function get_proses_is_nol(){
        return $this->db->select('nama_file, baris_data')->where('proses',0)->get('ess_sppd_monitoring_files');
    }

	function update_files($nama_file, $data) {
		$this->db->where('nama_file', $nama_file);
		$this->db->update('ess_sppd_monitoring_files', $data); 
	}
	
	function check_id_then_insert_data($data_where, $data) {
        $where = [
            'id_member_sppd'=>$data_where['id_member_sppd'],
            'id_sppd'=>$data_where['id_sppd'],
            'np_karyawan'=>$data_where['np_karyawan'],
            'jenis_fasilitas'=>$data_where['jenis_fasilitas'],
        ];
        $cek = $this->db->where($where)->get('ess_sppd_monitoring')->num_rows();
        if($cek<1){
            $proses = $this->db->insert('ess_sppd_monitoring',$data);
        } else{
            $proses = $this->db->where($where)->update('ess_sppd_monitoring',$data);
        }
        
        if($proses){
            return true;
        } else{
            return false;
        }
	}
}
