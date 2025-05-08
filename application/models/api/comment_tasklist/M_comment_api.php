<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_comment_api extends CI_Model {
    
    function self_assesment($id){
        return $this->db->select('id,np_karyawan,nama,tanggal,is_status,created_at')->where('id',$id)->get('ess_self_assesment_covid19');
    }
    
    # tasklist query
    function get_tasklist($tasklist_id){
        return $this->db->select('id,ess_performance_management_id as tasklist_id,np_karyawan,comment,created_at,updated_at')->where('ess_performance_management_id',$tasklist_id)->get('ess_performance_comment')->result();
    }
    
    function get_id_task($id){
        return $this->db->select('id,ess_performance_management_id as tasklist_id,np_karyawan,comment,created_at,updated_at')->where('id',$id)->get('ess_performance_comment');
    }
    
}