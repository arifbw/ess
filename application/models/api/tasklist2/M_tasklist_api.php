<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_tasklist_api extends CI_Model {
    
    function self_assesment($id){
        return $this->db->select('id,np_karyawan,nama,tanggal,is_status,created_at')->where('id',$id)->get('ess_self_assesment_covid19');
    }
    
    # tasklist query
    function get_tasklist($params){
        $this->db->select("tanggal,np_karyawan,nama,nama_jabatan,nama_unit, COUNT(CASE WHEN deleted_at is null then 1 end) as total_task, COUNT(CASE WHEN deleted_at is null AND checked='1' then 1 end) as total_selesai");
        $this->db->where('deleted_at',null);
        
        if(@$params['tahun_bulan']){
            $this->db->where("DATE_FORMAT(tanggal,'%Y-%m')",$params['tahun_bulan']);
        }
        
        $this->db->group_by('tanggal,np_karyawan,nama,nama_jabatan,nama_unit');
        $this->db->order_by('tanggal','DESC');
            
        
        return $this->db->get('ess_performance_management')->result();
    }
    
    function get_task_by_date($params){
        $this->db->select('id,tanggal,np_karyawan,nama,target_pekerjaan,hasil_pekerjaan,created_at,progress,checked,checked_at, created_by, np_atasan, nama_atasan, kode_unit_atasan, nama_jabatan_atasan,evidence,uploaded_at');
        $this->db->where('tanggal',$params['tanggal']);
        $this->db->where('np_karyawan',$params['np']);
        $this->db->where('deleted_at',null);
        return $this->db->get('ess_performance_management');
    }
    
    function get_id_task($id){
        return $this->db->select('id,id_assesment,target_pekerjaan')->where('id',$id)->get('ess_performance_management');
    }
    
}