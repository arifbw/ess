<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_karyawan_api extends CI_Model {
    function get_profil($np){
        $get = $this->db
					 ->from("mst_karyawan")
					 ->where("no_pokok",$np)
					 ->get();
		if( $get->num_rows()>0 )
			$data = $get->result_array()[0];
		else
            $data = [];
		return $data;
    }
}