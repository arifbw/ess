<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Persetujuan_cuti extends CI_Model {

	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	function persetujuan_cuti($data)
	{
		if($data['status_1']==null)
		{
			$setuju = array(						
				'status_2'			=> $data['status_2'],
				'approval_2_date'	=> date('Y-m-d H:i:s'),
				'updated_at'		=> date('Y-m-d H:i:s'),
				'updated_by'		=> $this->session->userdata('no_pokok')
			);
		}else
		if($data['status_2']==null)
		{
			$setuju = array(				
				'status_1'			=> $data['status_1'],	
				'approval_1_date'	=> date('Y-m-d H:i:s'),
				'updated_at'		=> date('Y-m-d H:i:s'),
				'updated_by'		=> $this->session->userdata('no_pokok')
			);
		}else
		{
			$setuju = array(				
				'status_1'		=> $data['status_1'],
				'status_2'		=> $data['status_2'],
				'approval_1_date'	=> date('Y-m-d H:i:s'),
				'approval_2_date'	=> date('Y-m-d H:i:s'),
				'updated_at'	=> date('Y-m-d H:i:s'),
				'updated_by'	=> $this->session->userdata('no_pokok')
			);
		}
		
		$this->db->where('id', $data['id']);
		$this->db->update('ess_cuti', $setuju); 
		
		if($this->db->affected_rows() > 0)
		{			
			return $data['id']; 
		}else
		{
			return "0";
		}
	}		
			
	function select_cuti_by_id($id)
	{
		$this->db->select('*');
		$this->db->from('ess_cuti');	
		$this->db->where('id',$id);
		
		$query = $this->db->get();
		
		return $query->row_array();
	}

}

/* End of file m_perencanaan_jadwal_kerja.php */
/* Location: ./application/models/kehadiran/m_perencanaan_jadwal_kerja.php */