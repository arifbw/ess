<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_jadwal_operasional extends CI_Model {

	public function __construct(){
		parent::__construct();
	}
    
	function get_order_harian($tgl_ymd, $unit){
        $this->db->select('a.id, a.nomor_pemesanan, a.nama_mst_kendaraan, a.nama_mst_driver, a.nama_mst_bbm, a.jumlah_liter_bbm, a.total_harga_bbm, a.biaya_lainnya, a.biaya_tol, a.biaya_parkir, a.biaya_total, a.nama_unit_pemesan, a.nama_pic, a.no_hp_pic, a.tanggal_berangkat, a.tanggal_awal, a.tanggal_akhir, a.jam, a.lokasi_jemput, a.nama_kota_asal, GROUP_CONCAT(b.keterangan_tujuan SEPARATOR "\n") as tujuannya, a.is_inap, a.is_pp')
            ->where(['a.tanggal_berangkat'=>$tgl_ymd, 'a.status_persetujuan_admin'=>1, 'a.is_canceled_by_admin!='=>'1']);
            //->where('a.id_mst_bbm IS NOT NULL',null,false);
        if($unit!='semua'){
            $this->db->where('a.unit_pemroses', $unit);
        }
        return $this->db->from('ess_pemesanan_kendaraan a')
            ->join('ess_pemesanan_kendaraan_kota b','a.id=b.id_pemesanan_kendaraan','LEFT')
            ->group_by('a.id')
            ->order_by('a.id_mst_kendaraan, a.jam')
            ->get();
    }
    
    function log_harga_bbm($tgl_ymd){
        return $this->db->select("a.nama, CONCAT(a.harga,'|',a.created) as mst, (SELECT CONCAT(b.harga,'|',b.created) FROM mst_bbm_log b WHERE b.id_mst_bbm=a.id AND b.created<'$tgl_ymd' ORDER BY b.created DESC LIMIT 1) as log")->get('mst_bbm a');
    }
	
}

/* End of file M_data_kehadiran.php */
/* Location: ./application/models/kehadiran/M_data_kehadiran.php */