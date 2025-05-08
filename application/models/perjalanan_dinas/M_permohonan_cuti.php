<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_permohonan_cuti extends CI_Model {

	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	function select_absence_quota_by_np($np)
	{
		$this->db->select('*');
		$this->db->from('erp_absence_quota');	
		$this->db->where('np_karyawan',$np);
		
		$data = $this->db->get();
		
		return $data;
	}
	
	function select_cubes_by_np($np)
	{
		$this->db->select('*');
		$this->db->from('cuti_cubes_jatah');	
		$this->db->where('no_pokok',$np);
		$this->db->where('tanggal_kadaluarsa >=', date("yyyy-dd-mm"));
		
		$data = $this->db->get();
		
		return $data;
	}
	
	function select_mst_cuti()
	{
		$this->db->select('*');
		$this->db->from('mst_cuti');
		
		$data = $this->db->get();
		
		return $data;
	}
	
	function select_np_by_kode_unit($list_kode_unit)
	{
		$this->db->select('*');
		$this->db->from('mst_karyawan');
		$this->db->where_in('kode_unit', $list_kode_unit);
		$this->db->order_by('no_pokok','ASC');
		$data = $this->db->get();
		
		return $data;
	}
	
	function insert_cuti($data)
	{
		$data = array(
				'np_karyawan'		=> $data['np_karyawan'],
				'absence_type'		=> $data['absence_type'],
				'start_date'		=> $data['start_date'],
				'end_date'			=> $data['end_date'],
				'jumlah_bulan'		=> $data['jumlah_bulan'],
				'jumlah_hari'		=> $data['jumlah_hari'],
				'alasan'			=> $data['alasan'],
				'approval_1'		=> $data['approval_1'],
				'approval_2'		=> $data['approval_2'],					
				'created_at'		=> date('Y-m-d H:i:s'),
				'created_by'		=> $this->session->userdata('no_pokok')
			);
		
		$this->db->insert('ess_cuti', $data); 

		if($this->db->affected_rows() > 0)
		{			
			return $this->db->insert_id(); 
		}else
		{
			return "0";
		}
	}
	/*
	function check_cuti_tahunan($np_karyawan)
	{		
		$absence_quota_type='91';
		
		$this->db->select('sum(number)');
		$this->db->select('sum(deduction)');
		$this->db->from('erp_absence_quota');
		$this->db->where('np_karyawan', $np_karyawan);
		$this->db->where('absence_quota_type', $absence_quota_type);
		$this->db->where('deduction_from<=', date('yyyy-dd-mm'));
		$this->db->where('deduction_to<=', date('yyyy-dd-mm'));
	
		$this->db->order_by("start_date", "ASC");	
		$query = $this->db->get();
		
		return $query->row_array();
	}
	*/
	
	function select_cuti_by_id($id)
	{
		$this->db->select('*');
		$this->db->from('ess_cuti');	
		$this->db->where('id',$id);
		
		$query = $this->db->get();
		
		return $query->row_array();
	}
	
	function batal_cuti($id)
	{
		$data = array(				
				'status_1'			=> '3',
				'status_2'			=> '3',
				'approval_1_date'	=> date('Y-m-d H:i:s'),
				'approval_2_date'	=> date('Y-m-d H:i:s'),
				'updated_at'		=> date('Y-m-d H:i:s'),
				'updated_by'		=> $this->session->userdata('no_pokok')
        );
		$this->db->where('id', $id);
		$this->db->update('ess_cuti', $data); 
		
		if($this->db->affected_rows() > 0)
		{			
			return "1"; 
		}else
		{
			return "0";
		}
	}
}

/* End of file m_perencanaan_jadwal_kerja.php */
/* Location: ./application/models/kehadiran/m_perencanaan_jadwal_kerja.php */