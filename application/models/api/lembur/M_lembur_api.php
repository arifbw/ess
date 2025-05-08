<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_lembur_api extends CI_Model {
    
    function get_lembur($params){
        /*
        $params = [
            'table_name'=>value,
            'kode_unit'=>value,
            'np'=>value,
        ]
        */
        
        $this->db->select("a.*")->from('ess_lembur_transaksi a');
				
		if(@$params["kode_unit"]) {
			$this->db->where_in('a.kode_unit', $params["kode_unit"]);								
		} else if(@$params["np"]) {
			$this->db->where_in('a.no_pokok', $params["np"]);	
		}
        
        if(@$params['tahun_bulan']){
            $this->db->where("DATE_FORMAT(a.tgl_dws,'%Y-%m')",$params['tahun_bulan']);
        }
        
		$this->db->order_by('a.tgl_dws', 'ASC');
		$this->db->order_by('a.jam_mulai', 'ASC');
        $this->db->order_by('a.no_pokok', 'ASC');
        
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
    
    function data_persetujuan($params){
        /*
        $params = [
            'table_name'=>value,
            'kode_unit'=>value,
            'np'=>value,
        ]
        */
        
        $this->db->select("a.*")->from('ess_lembur_transaksi a');
                
        if(@$params["np"]) {
            $this->db->where_in('a.approval_pimpinan_np', $params["np"]);   
        }
        
        /*if(@$params['tahun_bulan']){
            $this->db->where("DATE_FORMAT(a.tgl_dws,'%Y-%m')",$params['tahun_bulan']);
        }*/

        if (@$params['approve'] == '5') {
            $this->db->where('((waktu_mulai_fix is null or waktu_mulai_fix = "") OR (waktu_selesai_fix is null or waktu_selesai_fix = ""))');
        }
        else if (@$params['approve'] == '0') {
            $this->db->where('(approval_status="0" or approval_status="" or approval_status is null)');
            $this->db->where('((approval_pimpinan_status="0" or approval_pimpinan_status="" or approval_pimpinan_status is null) AND ((waktu_mulai_fix is not null or waktu_mulai_fix != "") AND (waktu_selesai_fix is not null or waktu_selesai_fix != "")))');
        }
        else if (@$params['approve'] == '1') {
            $this->db->where('(approval_status="0" or approval_status="" or approval_status is null)');
            $this->db->where('((approval_pimpinan_status="1") AND ((waktu_mulai_fix is not null or waktu_mulai_fix != "") AND (waktu_selesai_fix is not null or waktu_selesai_fix != "")))');
        }
        else if (@$params['approve'] == '2') {
            $this->db->where('(approval_status="0" or approval_status="" or approval_status is null)');
            $this->db->where('((approval_pimpinan_status="2") AND ((waktu_mulai_fix is not null or waktu_mulai_fix != "") AND (waktu_selesai_fix is not null or waktu_selesai_fix != "")))');
        }
        else if (@$params['approve'] == '3') {
            $this->db->where('((approval_status="1") AND ((waktu_mulai_fix is not null or waktu_mulai_fix != "") AND (waktu_selesai_fix is not null or waktu_selesai_fix != "")))');
        }
        else if (@$params['approve'] == '4') {
            $this->db->where('((approval_status="2") AND ((waktu_mulai_fix is not null or waktu_mulai_fix != "") AND (waktu_selesai_fix is not null or waktu_selesai_fix != "")))');
        }

        if ($params['tgl'] != '') {
            $this->db->where("DATE_FORMAT(tgl_dws,'%Y-%m')",$params['tgl']);
        }
        /*if ($params['tgl'] != '') {
            $tgl_mulai = date('Y-m-d', strtotime(substr($params['tgl'],0,10))).' 00:00:00';
            $tgl_selesai = date('Y-m-d', strtotime(substr($params['tgl'],-10))).' 23:59:59';
            $this->db->where('(waktu_mulai_fix between "'.$tgl_mulai.'" and "'.$tgl_selesai.'")');
        }*/
        
        $this->db->order_by('a.tgl_dws', 'ASC');
        $this->db->order_by('a.jam_mulai', 'ASC');
        $this->db->order_by('a.no_pokok', 'ASC');
        
        return $this->db->get();
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
    
    # START: heru manambahkan ini 2020-11-14 @15:02
    # Persetujuan lembur oleh atasan
    function save_approval($params, $data) {
		$this->db->where('id', $params['id']);
		$this->db->where('approval_pimpinan_np', $params['approval_pimpinan_np']);
		$this->db->where('(approval_pimpinan_status is null or approval_pimpinan_status = "0" or approval_pimpinan_status = "")');
		$this->db->where('((waktu_mulai_fix is not null or waktu_mulai_fix != "0000-00-00 00:00:00") AND (waktu_selesai_fix is not null or waktu_selesai_fix != "0000-00-00 00:00:00"))');
		$this->db->update('ess_lembur_transaksi', $data);
		
		if($this->db->affected_rows() > 0)
			return true;
		else
			return false;
	}
    # END: heru manambahkan ini 2020-11-14 @15:02
    
}