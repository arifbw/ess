<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_laporan_penilaian extends CI_Model {

	public function __construct(){
		parent::__construct();
        $this->params = '(a.status_persetujuan_admin=1 AND a.is_canceled_by_admin!="1" AND a.rating_driver IS NOT NULL)';
        $this->main_table = 'ess_pemesanan_kendaraan';
	}
    
    function get_nilai($date, $unit){
        $this->db->select('a.nama_mst_driver, (select x.jenis_sim from mst_driver x where x.id=a.id_mst_driver) as jenis_sim, AVG(a.rating_driver) as nilai_akhir')
            ->where($this->params)
            ->where("DATE_FORMAT(a.tanggal_berangkat,'%Y-%m')='$date'");
        if($unit!='semua'){
            $this->db->where('a.unit_pemroses', $unit);
        }
        $this->db->group_by('a.id_mst_driver');
        return $this->db->get($this->main_table.' a');
    }
	
}