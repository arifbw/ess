<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_laporan_global extends CI_Model {

	public function __construct(){
		parent::__construct();
        $this->params = '(a.status_persetujuan_admin=1 AND a.submit_biaya=1 AND a.id_mst_bbm IS NOT NULL AND a.is_canceled_by_admin!="1")';
        $this->main_table = 'ess_pemesanan_kendaraan';
	}
    
    function tahun(){
        return $this->db->select('YEAR(a.tanggal_berangkat) as tahun')->where($this->params)->group_by('tahun')->order_by('tahun','DESC')->get($this->main_table.' a');
    }
    
    function bbm_used($tahun=null){
        //return $this->db->select('a.id_mst_bbm, a.nama_mst_bbm')->where($this->params)->where("(YEAR(a.tanggal_berangkat)=$tahun)")->group_by('a.id_mst_bbm')->order_by('a.id_mst_bbm','ASC')->get($this->main_table.' a');
        return $this->db->select('id as id_mst_bbm, nama as nama_mst_bbm')->where('status',1)->get('mst_bbm');
    }
    
    function get_order_global($tahun, $unit){
        $this->db->select('a.tanggal_berangkat, SUM(a.biaya_tol) as total_biaya_tol, SUM(a.biaya_parkir) as total_biaya_parkir, SUM(a.biaya_lainnya) as total_biaya_lainnya, SUM(a.biaya_total) as total_biaya_total')
            ->where($this->params)
            ->where("(YEAR(a.tanggal_berangkat)=$tahun)");
        if($unit!='semua'){
            $this->db->where('a.unit_pemroses', $unit);
        }
        return $this->db->group_by('a.tanggal_berangkat')
            ->order_by('a.tanggal_berangkat','ASC')
            ->get($this->main_table.' a');
    }
    
    function get_bbm($param){
        # $param = [tanggal, id_mst_bbm]
        return $this->db->select('SUM(a.jumlah_liter_bbm) as sum_liter, SUM(a.total_harga_bbm) as sum_harga_bbm')
            ->where($this->params)
            ->where(['a.tanggal_berangkat'=>$param[0], 'a.id_mst_bbm'=>$param[1]])
            ->get($this->main_table.' a');
    }
	
}

/* End of file M_data_kehadiran.php */
/* Location: ./application/models/kehadiran/M_data_kehadiran.php */