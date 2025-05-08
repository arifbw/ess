<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_perizinan_api extends CI_Model {
    
    function get_perizinan($params){
        /*
        $params = [
            'table_name'=>value,
            'kode_unit'=>value,
            'np'=>value,
            'jenis_izin'=>value,
        ]
        */
        
        $this->db->select("a.*, (CASE WHEN b.start_date is not null then b.start_date else b.end_date end) as ordere, b.pos, b.approval_pengamanan_np, b.approval_pengamanan_posisi, b.start_date_input, b.end_date_input, b.approval_1_np as request_approval_1_np, b.approval_1_nama as request_approval_1_nama, b.approval_2_np as request_approval_2_np, b.approval_2_nama as request_approval_2_nama")->from($params['table_name'].' a');

        $this->db->join('ess_request_perizinan b', "a.id=b.id_perizinan AND (DATE_FORMAT(b.start_date,'%Y-%m')='{$params['bulan']}' OR (DATE_FORMAT(b.end_date,'%Y-%m')='{$params['bulan']}' AND b.start_date IS NULL)) AND a.np_karyawan=b.np_karyawan AND b.date_batal IS NULL");
        
        if(@$params['jenis_izin']){
            $this->db->where_in("a.kode_pamlek", $params['jenis_izin']);
        }
				
		if(@$params["kode_unit"]) {
			$this->db->where_in('a.kode_unit', $params["kode_unit"]);								
		} else if(@$params["np"]) {
			$this->db->where_in('a.np_karyawan', $params["np"]);	
		}
        
		$this->db->order_by('(CASE WHEN b.start_date IS NOT NULL THEN b.start_date ELSE b.end_date END)', 'DESC');
		$this->db->order_by('(CASE WHEN b.start_time IS NOT NULL THEN b.start_time ELSE b.end_time END)', 'DESC');
        $this->db->order_by('a.np_karyawan', 'DESC');
        
        return $this->db->get();
    }

    function get_permohonan($params){
        /*
        $params = [
            'table_name'=>value,
            'kode_unit'=>value,
            'np'=>value,
            'jenis_izin'=>value,
        ]
        */
        
        $this->db->select("a.*, (CASE WHEN start_date is not null then start_date else end_date end) as ordere")->from('ess_request_perizinan a');
        
        if(@$params['jenis_izin']){
            $this->db->where_in("a.kode_pamlek", $params['jenis_izin']);
        }
        
        if(@$params["kode_unit"]) {
            $this->db->where_in('a.kode_unit', $params["kode_unit"]);                               
        } else if(@$params["np"]) {
            $this->db->where_in('a.np_karyawan', $params["np"]);    
        }

        if(@$params['bulan']){
            $this->db->group_start();
            $this->db->where("DATE_FORMAT(a.start_date, '%Y-%m')=", $params['bulan']);
            $this->db->or_where("DATE_FORMAT(a.end_date, '%Y-%m')=", $params['bulan']);
            $this->db->group_end();
        }
        
        $this->db->order_by('(CASE WHEN a.start_date IS NOT NULL THEN a.start_date ELSE a.end_date END)', 'DESC');
        $this->db->order_by('(CASE WHEN a.start_time IS NOT NULL THEN a.start_time ELSE a.end_time END)', 'DESC');
        $this->db->order_by('a.np_karyawan', 'DESC');
        
        return $this->db->get();
    }
    
    # perizinan menunggu persetujuan (sbg pengguna)
    function menunggu_persetujuan($params){
        /*
        $params = [
            'table_name'=>value,
            'kode_unit'=>value,
            'np'=>value,
            'jenis_izin'=>value,
        ]
        */
        $where = "a.is_machine='0' AND a.pengguna_status!='3'";
        if(@$params['jenis_izin']){
            $where .= " AND a.kode_pamlek IN ('".implode("','",$params['jenis_izin'])."')";
        }
				
		if(@$params["kode_unit"]) {
            $where .= " AND a.kode_unit IN ('".implode("','",$params["kode_unit"])."')";				
		} else if(@$params["np"]) {
            $where .= " AND a.np_karyawan IN ('".implode("','",$params["np"])."')";
		}
        
        $where .= " AND (CASE WHEN a.kode_pamlek IN ('G','0') THEN a.approval_1_status IS NULL ELSE (approval_1_status IS NULL OR approval_2_status IS NULL) END)";
        
        return $this->db->query("SELECT a.*, (CASE WHEN start_date IS NOT NULL THEN start_date ELSE end_date END) AS ordere FROM ".$params['table_name']." a WHERE $where ORDER BY (CASE WHEN a.start_date IS NOT NULL THEN a.start_date ELSE a.end_date END) DESC, (CASE WHEN a.start_time IS NOT NULL THEN a.start_time ELSE a.end_time END) DESC, a.np_karyawan DESC");
    }
    
    function get_jenis_by_id($id){
        return $this->db->where('id',$id)->get('mst_perizinan');
    }
    
    # get data perizinan yg menunggu persetujuan (sbg atasan)
    function get_persetujuan($params){
        /*
        $params = [
            'table_name'=>value,
            'np'=>value,
            'jenis_izin'=>value,
        ]
        */
        
        $this->db->select("a.*, (CASE WHEN approval_1_np='".$params['np']."' THEN '1' ELSE '2' END) AS field_approval, (CASE WHEN start_date IS NOT NULL THEN start_date ELSE end_date END) AS ordere")->from($params['table_name'].' a');
        
        $this->db->where('a.is_machine','0');
        $this->db->where('a.pengguna_status!=','3');
        
        if(@$params['jenis_izin']){
            $this->db->where_in("a.kode_pamlek", $params['jenis_izin']);
        }
        
        if(@$params['np']) {
			$this->db->where("(a.approval_1_np='".$params['np']."' OR a.approval_2_np='".$params['np']."')");	
		}
        
		$this->db->order_by('(CASE WHEN a.start_date IS NOT NULL THEN a.start_date ELSE a.end_date END)', 'ASC');
		$this->db->order_by('(CASE WHEN a.start_time IS NOT NULL THEN a.start_time ELSE a.end_time END)', 'ASC');
        $this->db->order_by('a.np_karyawan', 'DESC');
        
        return $this->db->get();
    }
    
}