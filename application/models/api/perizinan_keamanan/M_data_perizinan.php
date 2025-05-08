<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_data_perizinan extends CI_Model {
    
    function get_data($params){
        $this->db->select("a.*, (CASE WHEN start_date is not null then start_date else end_date end) as ordere")->from("ess_request_perizinan a");
        $this->db->where('(machine_id_start="ess" OR machine_id_end="ess" OR machine_id_start="" OR machine_id_end="" OR machine_id_start is null OR machine_id_end is null)');
        if(@$params['bulan']){
            $this->db->group_start();
            $this->db->where("DATE_FORMAT(a.start_date, '%Y-%m')=", $params['bulan']);
            $this->db->or_where("DATE_FORMAT(a.end_date, '%Y-%m')=", $params['bulan']);
            $this->db->group_end();
        }

        if(@$params['kode_pamlek'] && @$params['absence_type'] && @$params['info_type']){
            $this->db->where('kode_pamlek',$params['kode_pamlek']);
            $this->db->where('absence_type',$params['absence_type']);
            $this->db->where('info_type',$params['info_type']);
        }

        if(@$params['pos']!=''){
            $this->db->where("(a.pos like '%\"".$params['pos']."\"%')");
        }

        if(@$params['start_date']){
            $this->db->group_start();
            $this->db->where("(a.start_date BETWEEN '".$params['start_date']."' AND '".$params['end_date']."')");
            $this->db->or_where("(a.end_date BETWEEN '".$params['start_date']."' AND '".$params['end_date']."')");
            $this->db->group_end();
        }

        $this->db->order_by('(CASE WHEN a.start_date IS NOT NULL THEN a.start_date ELSE a.end_date END)', 'DESC');
		$this->db->order_by('(CASE WHEN a.start_time IS NOT NULL THEN a.start_time ELSE a.end_time END)', 'DESC');
        $this->db->order_by('a.np_karyawan', 'DESC');
        return $this->db->get();
    }
}