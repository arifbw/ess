<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_laporan_bulanan extends CI_Model {

	public function __construct(){
		parent::__construct();
        $this->params = '(a.status_persetujuan_admin=1 AND a.submit_biaya=1 AND a.id_mst_bbm IS NOT NULL AND a.is_canceled_by_admin!="1")';
        $this->main_table = 'ess_pemesanan_kendaraan';
	}
    
	function get_order_harian($kode, $date, $unit){
        $this->db->select('SUM(total_harga_bbm) as sum_total_harga_bbm, sum(biaya_tol) as sum_biaya_tol, SUM(biaya_parkir) as sum_biaya_parkir, SUM(biaya_lainnya) as sum_biaya_lainnya, SUM(biaya_total) as sum_biaya_total')
            ->where($this->params)
            ->where(['kode_unit_pemesan'=>$kode, 'tanggal_berangkat'=>$date]);
        if($unit!='semua'){
            $this->db->where('a.unit_pemroses', $unit);
        }
        return $this->db->get($this->main_table.' a');
    }
    
    function get_sto(){
        return $this->db->distinct()->select("object_abbreviation as kode_unit, object_name as nama_unit_singkat
            , (case 
                    when SUBSTR(object_abbreviation, -4)='0000' then 1
                    when SUBSTR(object_abbreviation, -3)='000' AND SUBSTR(object_abbreviation, 2, 1)!='0' then 2
                    when SUBSTR(object_abbreviation, -2)='00' AND SUBSTR(object_abbreviation, 3, 1)!='0' then 3
                    END
            ) as levell")
            ->where_not_in('object_abbreviation',['99997','99999'])->where('SUBSTR(object_abbreviation, -2)=','00')
            ->order_by('levell, kode_unit')
            ->get('v_ess_sto_unit');
    }
    
    function get_sto_old($tb_sto){
        return $this->db->distinct()->select("kode_unit, nama_unit_singkat
            , (case 
                    when SUBSTR(kode_unit, -4)='0000' then 1
                    when SUBSTR(kode_unit, -3)='000' AND SUBSTR(kode_unit, 2, 1)!='0' then 2
                    when SUBSTR(kode_unit, -2)='00' AND SUBSTR(kode_unit, 3, 1)!='0' then 3
                    END
            ) as level")
            ->where_not_in('kode_unit',['99997','99999'])->where('SUBSTR(kode_unit, -2)=','00')
            ->order_by('level, kode_unit')
            ->get($tb_sto);
    }
	
}

/* End of file M_data_kehadiran.php */
/* Location: ./application/models/kehadiran/M_data_kehadiran.php */