<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_pemesanan_konsumsi_rapat extends CI_Model {

	public function __construct(){
		parent::__construct();
		//Do your magic here
	}

	function detail($no){
        $this->db->select('a.*, (select nama from mst_karyawan where no_pokok=a.np_atasan) as nama_atasan, (select nama from mst_karyawan where no_pokok=a.np_verified) as nama_verified, c.nama as nama_gedung, c.id as id_gedung, b.nama as nama_ruangan')
        		->join('mst_ruangan b', 'a.id_ruangan=b.id')
        		->join('mst_gedung c', 'b.id_gedung=c.id')
        		->where('nomor_pemesanan', $no)
        		->from('ess_pemesanan_konsumsi_rapat a');
        return $this->db->get();
    }

	function katalog_pemesanan($find_id){
    	$this->db->select('group_concat(concat("* ", nama, " : ", harga) order by nama SEPARATOR "<br>") as daftar, sum(harga) as total_harga');
		$this->db->from('mst_jenis_katalog a');
		$this->db->where_in('id', $find_id);
		$this->db->order_by('nama', 'asc');
        return $this->db->get();
	}
	
}

/* End of file M_data_kehadiran.php */
/* Location: ./application/models/konsumsi/M_pemesanan_makan_siang.php */