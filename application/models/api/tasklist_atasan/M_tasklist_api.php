<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_tasklist_api extends CI_Model {
    
    function get_bawahan($params){
        return $this->db->select('np_karyawan, nama')
            ->where('np_atasan',$params['np_atasan'])
            ->where('tanggal',$params['tanggal'])
            ->group_by('np_karyawan,nama')
            ->get('ess_performance_management')->result();
    }
    
    function get_task_by_date($params){
        $this->db->select('id,tanggal,np_karyawan,nama,nama_jabatan,target_pekerjaan,hasil_pekerjaan,created_at,progress,checked,checked_at, created_by, np_atasan, nama_atasan, kode_unit_atasan, nama_jabatan_atasan,evidence,uploaded_at');
        $this->db->where('tanggal',$params['tanggal']);
        $this->db->where('np_karyawan',$params['np']);
        $this->db->where('tipe','task');
        $this->db->where('deleted_at',null);
        return $this->db->get('ess_performance_management')->result_array();
    }
    
}