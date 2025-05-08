<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_inbound_time_event extends CI_Model {


	public function select_time_event($date)
	{
		$this->db->select('*');
		$this->db->from("ess_time_event");		
		$this->db->where('deleted','0');
		$this->db->where('date',$date);
			
		$query = $this->db->get();
		return $query;	
	}


}

