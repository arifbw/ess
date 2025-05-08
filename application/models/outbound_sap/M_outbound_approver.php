<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_outbound_approver extends CI_Model {		
	function insert_batch_sto_files($data){
		$this->db->insert_batch('ess_approver_files',$data);
	}
    
    function check_name_then_insert_data($file_name, $data) {
        $cek = $this->db->where('nama_file', $file_name)->get('ess_approver_files')->num_rows();
        if($cek<1){
            $this->db->insert('ess_approver_files',$data);
        }
	}
    
    function get_proses_is_nol(){
        return $this->db->select('nama_file, baris_data')->where('proses',0)->order_by('last_modified', 'asc')->get('ess_approver_files');
    }

	function update_files($nama_file, $data) {
		$this->db->where('nama_file', $nama_file);
		$this->db->update('ess_approver_files', $data); 
	}
	
    function check_id_then_insert_data($np_karyawan, $data) {
	// function check_id_then_insert_data($np_karyawan, $np_approver_1, $np_approver_2, $np_approver_3, $data) {
        $where = [
            'np_karyawan'=>$np_karyawan,
            // 'np_approver_1'=>$np_approver_1,
            // 'np_approver_2'=>$np_approver_2,
            // 'np_approver_3'=>$np_approver_3
        ];
        $cek = $this->db->where($where)->get('ess_approver')->num_rows();
        if($cek<1){
            $proses = $this->db->insert('ess_approver',$data);
        } else{
            $proses = $this->db->where($where)->update('ess_approver',$data);
        }
        
        if($proses){
            return true;
        } else{
            return false;
        }
	}
}
