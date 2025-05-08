<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_proses_delete extends CI_Model {
	
	function drop_table($data){
        $table_name = $data['table_name'];
        if($this->db->query("DROP TABLE $table_name")){
            $ket = "Tabel $table_name BERHASIL dihapus.";
        } else{
            $ket = "Tabel $table_name GAGAL dihapus.";
        }
		return ['ket'=>$ket];
	}
    
    function delete_data_lembur($data){
        $table_name = $data['table_name'];
        $count_rows = $this->db->query("SELECT COUNT(id) as jumlah FROM $table_name WHERE DATE_FORMAT(tgl_dws, '%Y-%m')='".$data['month']."'")->row()->jumlah;
        if($this->db->query("DELETE FROM $table_name WHERE DATE_FORMAT(tgl_dws, '%Y-%m')='".$data['month']."'")){
            $ket = "Data $table_name bulan ".$data['month']." BERHASIL dihapus. Jumlah baris: $count_rows.";
        } else{
            $ket = "Data $table_name bulan ".$data['month']." GAGAL dihapus.";
        }
		return ['ket'=>$ket];
    }
    
    function delete_data_sppd($data){
        $table_name = $data['table_name'];
        $count_rows = $this->db->query("SELECT COUNT(id) as jumlah FROM $table_name WHERE DATE_FORMAT(tgl_selesai, '%Y-%m')='".$data['month']."'")->row()->jumlah;
        if($this->db->query("DELETE FROM $table_name WHERE DATE_FORMAT(tgl_selesai, '%Y-%m')='".$data['month']."'")){
            $ket = "Data $table_name bulan ".$data['month']." BERHASIL dihapus. Jumlah baris: $count_rows.";
        } else{
            $ket = "Data $table_name bulan ".$data['month']." GAGAL dihapus.";
        }
		return ['ket'=>$ket];
    }
}
