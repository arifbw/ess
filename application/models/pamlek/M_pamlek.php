<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_pamlek extends CI_Model {
	
	function insert_error($data)
	{
		return  $this->db->insert('ess_error',$data);		
	}
	
	function setting()
	{
		$this->db->select('*');
		$this->db->from('pamlek_setting');		
		$query = $this->db->get();
		
		return $query->row_array();
	}

	public function select_pamlek_files()
	{
		$this->db->select('*');
		$this->db->from('pamlek_files');
		$this->db->order_by("nama_file", "ASC"); 	
		
		$query = $this->db->get();
		return $query;
	}
	
	public function select_pamlek_files_limit($max)
	{
		$this->db->select('*');
		$this->db->from('pamlek_files');
		$this->db->where("proses","0");
		$this->db->limit($max);
		$this->db->order_by("nama_file", "ASC"); 	
		
		$query = $this->db->get();
		return $query;
	}	
	
	function insert_files($data)
	{
		return  $this->db->insert('pamlek_files',$data);	
	}
	
	function insert_data_batch($data)
	{
		$this->db->insert_batch('pamlek_data',$data);
	}

	function update_files($nama_file,$data)
	{
		$this->db->where('nama_file', $nama_file);
		$this->db->update('pamlek_files', $data); 
	}
	
	function select_distinc_tapping_time_pamlek_data()
	{
		$this->db->distinct("tapping_time");
		$this->db->from('pamlek_data');
		
		$query = $this->db->get();
		return $query;		
	}
		
	function create_table_data($name)
	{	
		$this->db->query("CREATE TABLE $name like pamlek_data");
	}
	
	function truncate_table($name)
	{
		$this->db->from($name); 
		$this->db->truncate();
	}
	
	function copy_isi($name,$tahun_bulan)
	{
		$this->db->query("INSERT INTO $name 
		(no_pokok_convert, no_pokok_original, no_pokok, tapping_time, in_out, machine_id, tapping_type, file) 
		SELECT no_pokok_convert, no_pokok_original, no_pokok, tapping_time, in_out, machine_id, tapping_type, file FROM pamlek_data 
		WHERE tapping_time like '$tahun_bulan%'");	
	}
	
	function check_table_exist($name)
	{
		$query = $this->db->query("show tables like '$name'")->row_array();
		
		return $query;
	}
	/* HAPUS
	function insert_ess_tabel_cico($data)
	{
		return  $this->db->insert('ess_tabel_cico',$data);		
	}
	
	function update_ess_tabel_cico($nama_tabel, $data)
	{
		$this->db->where('nama_tabel', $nama_tabel);
		$this->db->update('ess_tabel_cico', $data);
	}
	
	
	public function check_ess_tabel_cico_exist($nama_tabel)
	{
		$this->db->select('*');
		$this->db->from('ess_tabel_cico');	
		$this->db->where('nama_tabel', $nama_tabel);
						
		$query 	= $this->db->get();
		
		return $query->row_array();
	}
	*/
	/*
	public function select_pamlek_data($tahun,$bulan)
	{
		$this->db->select('*');
		$this->db->from("pamlek_data_".$tahun."_".$bulan);
		
		$query = $this->db->get();
		return $query;
	}
	
	public function select_ess_tabel_cico_not_dump()
	{
		$this->db->select("nama_tabel");
		$this->db->from("ess_tabel_cico");
		$this->db->where("dump","0");
		
		$query = $this->db->get();
		return $query;
		
	}
	*/
}
