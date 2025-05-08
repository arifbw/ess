<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_inbound_attendance extends CI_Model {


	public function select_sppd($date, $np=null)
	{
		$this->db->select('*');
		$this->db->from("ess_sppd");
		$this->db->where("DATE_FORMAT(tgl_selesai,'%Y-%m-%d')",$date);
        if(@$np){
            $this->db->where('id_user', $np);
        }
		$this->db->group_by('id_sppd'); 
		$this->db->group_by('np_karyawan'); 
			
		$this->db->order_by('np_karyawan', 'ASC');	
		$query = $this->db->get();
		return $query;	
	}


}

