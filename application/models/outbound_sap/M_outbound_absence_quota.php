<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_outbound_absence_quota extends CI_Model {		
	function insert_batch_sto_files($data){
		$this->db->insert_batch('erp_absence_quota_files',$data);
	}
    
    function check_name_then_insert_data($file_name, $data) {
        $cek = $this->db->where('nama_file', $file_name)->get('erp_absence_quota_files')->num_rows();
        if($cek<1){
            $this->db->insert('erp_absence_quota_files',$data);
        }
	}
    
    function get_proses_is_nol(){
        return $this->db->select('nama_file, baris_data')->where('proses',0)->get('erp_absence_quota_files');
    }

	function update_files($nama_file, $data) {
		$this->db->where('nama_file', $nama_file);
		$this->db->update('erp_absence_quota_files', $data); 
	}
	
	function check_id_then_insert_data($id, $start_date, $end_date, $deduction_from, $deduction_to, $data) {
        $where = [
            'np_karyawan'=>$id,
            'start_date'=>"$start_date",
            'end_date'=>"$end_date",
            'deduction_from'=>"$deduction_from",
            'deduction_to'=>"$deduction_to"
        ];
        $cek = $this->db->where($where)->get('erp_absence_quota')->num_rows();
        if($cek<1){
            $proses = $this->db->insert('erp_absence_quota',$data);
        } else{
            $proses = $this->db->where($where)->update('erp_absence_quota',$data);
        }
        
        if($proses){
            return true;
        } else{
            return false;
        }
	}
}
