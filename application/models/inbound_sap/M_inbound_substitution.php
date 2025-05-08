<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_inbound_substitution extends CI_Model {


	public function select_substitution($date, $np=null)
	{
		$this->db->select('*');
		$this->db->from("ess_substitution");		
		$this->db->where('deleted','0');
		$this->db->where('date',$date);
        if(@$np){
            $this->db->where('np_karyawan', $np);
        }
			
		$query = $this->db->get();
		return $query;	
	}


}

