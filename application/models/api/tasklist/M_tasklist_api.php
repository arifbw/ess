<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_tasklist_api extends CI_Model {
    
    function self_assesment($id){
        return $this->db->select('id,np_karyawan,nama,tanggal,is_status,created_at')->where('id',$id)->get('ess_self_assesment_covid19');
    }
    
    # tasklist query
    function get_tasklist($id_assesment){
        return $this->db->select('id,id_assesment,target_pekerjaan,hasil_pekerjaan,created_at,updated_at,checked,progress,checked_at,tipe')->where('id_assesment',$id_assesment)->get('ess_performance_management')->result();
    }
    
    function get_id_task($id){
        return $this->db->select('id,target_pekerjaan')->where('id',$id)->get('ess_performance_management');
    }
    
}