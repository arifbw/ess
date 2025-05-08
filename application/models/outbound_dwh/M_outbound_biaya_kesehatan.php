<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_outbound_biaya_kesehatan extends CI_Model {
    
    function check_name_then_insert_data($file_name, $data) {
        $cek = $this->db->where('nama_file', $file_name)->get('dwh_biaya_kesehatan_files')->num_rows();
        if($cek<1){
            $this->db->insert('dwh_biaya_kesehatan_files',$data);
        }
	}
    
    function get_proses_is_nol(){
        return $this->db->select('nama_file, baris_data')->where('proses',0)->get('dwh_biaya_kesehatan_files');
    }

	function update_files($nama_file, $data) {
		$this->db->where('nama_file', $nama_file);
		$this->db->update('dwh_biaya_kesehatan_files', $data); 
	}
}
