<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_inbound_sppd extends CI_Model {


	public function select_sppd($date, $np=null)
	{
		$this->db->select('*');
		$this->db->from("ess_sppd");
		$this->db->where("DATE_FORMAT(tgl_selesai,'%Y-%m-%d')",$date);
        if(@$np){
            $this->db->where('id_user', $np);
        }
			
		$query = $this->db->get();
		return $query;	
	}


}

