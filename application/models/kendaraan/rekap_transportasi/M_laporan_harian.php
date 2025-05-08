<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_laporan_harian extends CI_Model {

	public function __construct(){
		parent::__construct();
	}
    
	function get_order_harian($tgl_ymd, $unit){
        $this->db->select('a.nama_mst_kendaraan, a.nama_mst_driver, a.nama_mst_bbm, a.jumlah_liter_bbm, a.total_harga_bbm, a.biaya_lainnya, a.biaya_tol, a.biaya_parkir, a.biaya_total, a.nama_unit_pemesan, GROUP_CONCAT(b.keterangan_tujuan SEPARATOR "\n") as tujuannya')
            ->where(['a.tanggal_berangkat'=>$tgl_ymd, 'a.status_persetujuan_admin'=>1, 'a.submit_biaya'=>1, 'a.is_canceled_by_admin!='=>'1'])
            ->where('a.id_mst_bbm IS NOT NULL',null,false);
        if($unit!='semua'){
            $this->db->where('a.unit_pemroses', $unit);
        }
        return $this->db->from('ess_pemesanan_kendaraan a')
            ->join('ess_pemesanan_kendaraan_kota b','a.id=b.id_pemesanan_kendaraan','LEFT')
            ->group_by('a.id')
            ->order_by('a.id_mst_bbm')
            ->get();
    }
    
    function log_harga_bbm($tgl_ymd){
        return $this->db->select("a.nama, CONCAT(a.harga,'|',a.created) as mst, (SELECT CONCAT(b.harga,'|',b.created) FROM mst_bbm_log b WHERE b.status='1' AND b.id_mst_bbm=a.id AND b.created<'$tgl_ymd' ORDER BY b.created DESC LIMIT 1) as log")->get_where('mst_bbm a', array('a.status' => '1'));
    }
	
}

/* End of file M_data_kehadiran.php */
/* Location: ./application/models/kehadiran/M_data_kehadiran.php */