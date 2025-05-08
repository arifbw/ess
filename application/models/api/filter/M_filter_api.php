<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_filter_api extends CI_Model {
    
    public function getKaryawan($params) {
        /*
        $params = ['grup'=>value, 'var'=>value, 'fields'=>value]
        */
        if(@$params['fields']){
            $this->db->select($params['fields']);
        }
        
		if($params["grup"]==4) {
            # pengadministrasi unit kerja
			$this->db->where_in('kode_unit', $params['var']);
		} else if($params["grup"]==5) {
            # pengguna
			$this->db->where('no_pokok', $params['var']);
		}
		$this->db->order_by('no_pokok', 'ASC');
        
		return $this->db->get('mst_karyawan')->result();
	}
    
    function jenis_perizinan(){
        $this->db->where_not_in('id',[8,9,10,11]);
        $this->db->where('status',1);
        return $this->db->get('mst_perizinan');
    }
    
    function jenis_cuti(){
        $this->db->where('status',1);
        return $this->db->get('mst_cuti');
    }
    
    function perizinan_bulan(){
        return $this->db->query("SELECT REPLACE(table_name,'ess_perizinan_','') as table_name FROM information_schema.tables WHERE table_schema='".$this->db->database."' AND table_name like 'ess_perizinan_%' group by table_name ORDER BY table_name DESC");
    }
    
    function cuti_bulan(){
        return $this->db->select("DATE_FORMAT(start_date,'%Y-%m') as tahun_bulan")->group_by('tahun_bulan')->order_by('tahun_bulan','DESC')->get('ess_cuti');
    }
    
    function dashboard_bulan(){
        return $this->db->query("SELECT REPLACE(table_name,'ess_cico_','') as table_name FROM information_schema.tables WHERE table_schema='".$this->db->database."' AND table_name like 'ess_cico_%' group by table_name ORDER BY table_name DESC");
    }
    
    function health_passport_bulan(){
        return $query = $this->db->select("DATE_FORMAT(tanggal,'%Y-%m') as tahun_bulan")->group_by('tahun_bulan')->order_by('tahun_bulan','DESC')->get('ess_self_assesment_covid19');
    }
    
    function get_data_karyawan_by_np($np){
        if(is_array($np)){
            $this->db->where_in('no_pokok',$np['np']);
        } else{
            $this->db->where('no_pokok',$np);
        }
        return $this->db->get('mst_karyawan');
    }
    
    function sppd_bulan(){
        return $this->db->select("DATE_FORMAT(tgl_berangkat,'%Y-%m') as tahun_bulan")->where('tgl_berangkat is not null', null, false)->where('YEAR(STR_TO_DATE(tgl_berangkat, "%Y-%m-%d")) <=', date('Y'))->where('YEAR(STR_TO_DATE(tgl_berangkat, "%Y-%m-%d")) >=', date('Y')-1)->group_by('tahun_bulan')->order_by('tahun_bulan','DESC')->get('ess_sppd');
    }
    
    function lembur_bulan(){
        return $this->db->query("SELECT DISTINCT DATE_FORMAT(tgl_dws, '%Y-%m') AS tahun_bulan FROM ess_lembur_transaksi ORDER BY tahun_bulan DESC");
    }

    function mst_jadwal_kerja(){
        return $this->db->get('mst_jadwal_kerja');
    }
    
    function alasan_sipk(){
        $this->db->where('status',1);
        return $this->db->get('mst_sipk_alasan');
    }
    
    function mst_kategori_lembur(){
        $this->db->where('status',1);
        return $this->db->get('mst_kategori_lembur');
    }
    
}