<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_alasan_sipk extends CI_Model {
    
	function alasan(){
		return $this->db->get('mst_sipk_alasan')->result_array();
	}
}