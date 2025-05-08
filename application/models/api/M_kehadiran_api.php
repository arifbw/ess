<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_kehadiran_api extends CI_Model {
    
    function get_kehadiran($param, $bulan){
        /*
        $param => [np] atau
        $param => [kode unit, kode unit]
        */
        // $y_m = date('Y_m');
        $y_m = $bulan;
        $tabel = "ess_cico_$y_m";
        
        $this->db->select("$tabel.id");		
		$this->db->select("$tabel.kode_unit");
		$this->db->select("$tabel.np_karyawan");
		$this->db->select("$tabel.nama");	
		$this->db->select("$tabel.dws_tanggal");	
		$this->db->select("$tabel.dws_name");
		$this->db->select("$tabel.dws_name_fix");
		
		$this->db->select("IFNULL($tabel.dws_in_tanggal_fix,$tabel.dws_in_tanggal) dws_in_tanggal",false);
		$this->db->select("IFNULL($tabel.dws_in_fix,$tabel.dws_in) dws_in",false);
		$this->db->select("IFNULL($tabel.dws_out_tanggal_fix,$tabel.dws_out_tanggal) dws_out_tanggal",false);
		$this->db->select("IFNULL($tabel.dws_out_fix,$tabel.dws_out) dws_out",false);
		
		$this->db->select("$tabel.tapping_time_1");
		$this->db->select("$tabel.tapping_time_2");
		$this->db->select("$tabel.tapping_fix_1");
		$this->db->select("$tabel.tapping_fix_2");
		
		$this->db->select("$tabel.tapping_fix_approval_status");
		$this->db->select("$tabel.tapping_fix_approval_ket");
		$this->db->select("$tabel.tapping_fix_approval_nama");
		$this->db->select("$tabel.tapping_fix_approval_np");
		$this->db->select("$tabel.tapping_fix_1_temp");
		$this->db->select("$tabel.tapping_fix_2_temp");

		$this->db->select("$tabel.tapping_terminal_1");
		$this->db->select("$tabel.tapping_terminal_2");
		
		$this->db->select("$tabel.id_perizinan");
		$this->db->select("$tabel.id_cuti");
		$this->db->select("$tabel.id_sppd");
		
		$this->db->select("$tabel.wfh");
		$this->db->select("$tabel.wfh_foto_1");
		$this->db->select("$tabel.wfh_foto_2");
		$this->db->select("$tabel.is_dinas_luar");
        
        $_Date = date('Y-m-d');
		$date_minus_2 =  date('Y-m-d', strtotime($_Date. ' - 2 days'));
		
		$this->db->select("
		IF($tabel.dws_tanggal > '$date_minus_2','Proses Validasi',
		IF(((($tabel.dws_name!='OFF' && ($tabel.dws_name_fix='' || $tabel.dws_name_fix is null)) || $tabel.dws_name_fix!='OFF' && ($tabel.dws_name_fix!='' && $tabel.dws_name_fix is not null))), 
			IF((($tabel.tapping_time_1 IS null || $tabel.tapping_time_1='0000-00-00 00:00:00') && ($tabel.tapping_fix_1 IS null || $tabel.tapping_fix_1='0000-00-00 00:00:00')) && (($tabel.tapping_time_2 IS null || $tabel.tapping_time_2='0000-00-00 00:00:00') && ($tabel.tapping_fix_2 IS null || $tabel.tapping_fix_2='0000-00-00 00:00:00')) && ($tabel.dws_name='OFF' || $tabel.dws_name!='' || $tabel.dws_name is not null), 'AB',
				IF((($tabel.tapping_time_1 IS null || $tabel.tapping_time_1='0000-00-00 00:00:00') && ($tabel.tapping_fix_1 IS null || $tabel.tapping_fix_1='0000-00-00 00:00:00')) && ($tabel.tapping_time_2 || $tabel.tapping_fix_2) , 'TM',
					IF(($tabel.tapping_time_1 || $tabel.tapping_fix_1)&&(($tabel.tapping_time_2 IS null || $tabel.tapping_time_2='0000-00-00 00:00:00') && ($tabel.tapping_fix_2 IS null || $tabel.tapping_fix_2='0000-00-00 00:00:00')), 'TK' , 
					''))) 
		, '')) AS keterangan");
        $this->db->from($tabel);
        $this->db->where_in('np_karyawan', $param);
        $this->db->order_by('np_karyawan','ASC');		
		$this->db->order_by("$tabel.dws_tanggal",'ASC');
        
        return $this->db->get();
    }

    function persetujuan_kehadiran($param, $bulan){
        /*
        $param => [np] atau
        $param => [kode unit, kode unit]
        */
        // $y_m = date('Y_m');
        $y_m = $bulan;
        $tabel = "ess_cico_$y_m";
        
        $this->db->select("$tabel.id");		
		$this->db->select("$tabel.kode_unit");
		$this->db->select("$tabel.np_karyawan");
		$this->db->select("$tabel.nama");	
		$this->db->select("$tabel.dws_tanggal");	
		$this->db->select("$tabel.dws_name");
		$this->db->select("$tabel.dws_name_fix");
		
		$this->db->select("IFNULL($tabel.dws_in_tanggal_fix,$tabel.dws_in_tanggal) dws_in_tanggal",false);
		$this->db->select("IFNULL($tabel.dws_in_fix,$tabel.dws_in) dws_in",false);
		$this->db->select("IFNULL($tabel.dws_out_tanggal_fix,$tabel.dws_out_tanggal) dws_out_tanggal",false);
		$this->db->select("IFNULL($tabel.dws_out_fix,$tabel.dws_out) dws_out",false);
		
		$this->db->select("$tabel.tapping_time_1");
		$this->db->select("$tabel.tapping_time_2");
		$this->db->select("$tabel.tapping_fix_1");
		$this->db->select("$tabel.tapping_fix_2");
		
		$this->db->select("$tabel.tapping_fix_approval_status");
		$this->db->select("$tabel.tapping_fix_approval_ket");
		$this->db->select("$tabel.tapping_fix_approval_nama");
		$this->db->select("$tabel.tapping_fix_approval_date");
		$this->db->select("$tabel.tapping_fix_approval_np");
		$this->db->select("$tabel.tapping_fix_1_temp");
		$this->db->select("$tabel.tapping_fix_2_temp");
		
		$this->db->select("$tabel.tapping_terminal_1");
		$this->db->select("$tabel.tapping_terminal_2");
		
		$this->db->select("$tabel.id_perizinan");
		$this->db->select("$tabel.id_cuti");
		$this->db->select("$tabel.id_sppd");
		
		$this->db->select("$tabel.wfh");
		$this->db->select("$tabel.wfh_foto_1");
		$this->db->select("$tabel.wfh_foto_2");
		$this->db->select("$tabel.is_dinas_luar");
        
        $_Date = date('Y-m-d');
		$date_minus_2 =  date('Y-m-d', strtotime($_Date. ' - 2 days'));
		
		$this->db->select("
		IF($tabel.dws_tanggal > '$date_minus_2','Proses Validasi',
		IF(((($tabel.dws_name!='OFF' && ($tabel.dws_name_fix='' || $tabel.dws_name_fix is null)) || $tabel.dws_name_fix!='OFF' && ($tabel.dws_name_fix!='' && $tabel.dws_name_fix is not null))), 
			IF((($tabel.tapping_time_1 IS null || $tabel.tapping_time_1='0000-00-00 00:00:00') && ($tabel.tapping_fix_1 IS null || $tabel.tapping_fix_1='0000-00-00 00:00:00')) && (($tabel.tapping_time_2 IS null || $tabel.tapping_time_2='0000-00-00 00:00:00') && ($tabel.tapping_fix_2 IS null || $tabel.tapping_fix_2='0000-00-00 00:00:00')) && ($tabel.dws_name='OFF' || $tabel.dws_name!='' || $tabel.dws_name is not null), 'AB',
				IF((($tabel.tapping_time_1 IS null || $tabel.tapping_time_1='0000-00-00 00:00:00') && ($tabel.tapping_fix_1 IS null || $tabel.tapping_fix_1='0000-00-00 00:00:00')) && ($tabel.tapping_time_2 || $tabel.tapping_fix_2) , 'TM',
					IF(($tabel.tapping_time_1 || $tabel.tapping_fix_1)&&(($tabel.tapping_time_2 IS null || $tabel.tapping_time_2='0000-00-00 00:00:00') && ($tabel.tapping_fix_2 IS null || $tabel.tapping_fix_2='0000-00-00 00:00:00')), 'TK' , 
					''))) 
		, '')) AS keterangan");
        $this->db->from($tabel);
        $this->db->where_in('tapping_fix_approval_np', $param);
        $this->db->where_in('tapping_fix_approval_status', '0');
		$this->db->order_by("$tabel.dws_tanggal",'ASC');
        
        return $this->db->get();
    }
    
    function by_np($id, $tabel){
		// $y_m = date('Y_m', strtotime($tanggal));
        // $tabel = "ess_cico_$y_m";
        
        $this->db->select("$tabel.id");		
		$this->db->select("$tabel.kode_unit");
		$this->db->select("$tabel.np_karyawan");
		$this->db->select("$tabel.nama");	
		$this->db->select("$tabel.dws_tanggal");
		
		$this->db->select("$tabel.tapping_time_1");
		$this->db->select("$tabel.tapping_time_2");
		$this->db->select("$tabel.tapping_fix_1");
		$this->db->select("$tabel.tapping_fix_2");
		
		$this->db->select("$tabel.tapping_fix_approval_date");
		$this->db->select("$tabel.tapping_fix_approval_status");
		$this->db->select("$tabel.tapping_fix_approval_ket");
		$this->db->select("$tabel.tapping_fix_approval_np");
		$this->db->select("$tabel.tapping_fix_1_temp");
		$this->db->select("$tabel.tapping_fix_2_temp");

        $this->db->from($tabel);
        $this->db->where('id', $id);
		$this->db->order_by("$tabel.dws_tanggal",'ASC');
        
        return $this->db->get();
    }
}