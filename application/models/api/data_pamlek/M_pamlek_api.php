<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_pamlek_api extends CI_Model {
    
    function get_pamlek($params){
        /*
        $params = [
            'tahun_bulan'=>value,
            'np'=>value,
            'mesin_perizinan'=>value,
        ]
        */
        
        $this->db->select("distinct case when a.machine_id in (".$params['mesin_perizinan'].") then b.nama else 'Kehadiran' end jenis");
		$this->db->select("case when a.in_out=0 then 'keluar' when a.in_out='1' then 'masuk' end tipe",false);
		$this->db->select("a.machine_id");
		$this->db->select("a.tapping_time");
		$this->db->from("pamlek_data_".$params['tahun_bulan']." a");
		$this->db->join("mst_perizinan b","a.tapping_type=b.kode_pamlek","left");
		$this->db->where("no_pokok", $params['np']);
        $this->db->order_by('tapping_time', 'ASC');
        
        return $this->db->get();
    }
}