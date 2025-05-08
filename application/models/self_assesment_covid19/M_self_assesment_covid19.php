<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_self_assesment_covid19 extends CI_Model {

	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
    
		
	function insert($data)
	{
		/*$data_insert['np_karyawan']				= $data_karyawan['np_karyawan'];
		$data_insert['personel_number']			= $data_karyawan['personel_number'];
		$data_insert['nama']					= $data_karyawan['nama'];
		$data_insert['nama_jabatan']			= $data_karyawan['nama_jabatan'];
		$data_insert['kode_unit']				= $data_karyawan['kode_unit'];
		$data_insert['nama_unit']				= $data_karyawan['nama_unit'];
		$data_insert['pernah_keluar']			= $this->input->post('pernah_keluar');
		$data_insert['transportasi_umum']		= $this->input->post('transportasi_umum');
		$data_insert['luar_kota']				= $this->input->post('luar_kota');
		$data_insert['kegiatan_orang_banyak']	= $this->input->post('kegiatan_orang_banyak');
		$data_insert['tanggal']					= $this->input->post('tanggal');*/
				
		$data['updated_at']		= date('Y-m-d H:i:s');
		
		$this->db->insert('ess_self_assesment_covid19', $data); 

		if($this->db->affected_rows() > 0)
		{			
			return $this->db->insert_id(); 
		}else
		{
			return "0";
		}
	}
	
	function update($data, $where)
	{
				
		$data['updated_at']		= date('Y-m-d H:i:s');
		$data['updated_by']		= $this->session->userdata('no_pokok');
		
		$this->db->set($data)->where($where)->update('ess_self_assesment_covid19'); 
		
		if($this->db->affected_rows() > 0)
		{			
			return 'edit'; 
		}else
		{
			return "0";
		}
	}
	
	function select_data_by_id($id)
	{
		$this->db->select('*');
		$this->db->from('ess_self_assesment_covid19');	
		$this->db->where('id',$id);
		
		$query = $this->db->get();
		
		return $query->row_array();
	}
	
	function last_assesment($np, $tgl)
	{
		$this->db->select('*');
		$this->db->from('ess_self_assesment_covid19');	
		$this->db->where('np_karyawan',$np);
		$this->db->where('date(tanggal)', date('Y-m-d', strtotime($tgl)));
		$this->db->order_by('created_at','DESC');
		$this->db->limit(1);
		
		$query = $this->db->get();
		
		return $query->row_array();
	}
	
		
		

	
}

/* End of file m_perencanaan_jadwal_kerja.php */
/* Location: ./application/models/kehadiran/m_perencanaan_jadwal_kerja.php */