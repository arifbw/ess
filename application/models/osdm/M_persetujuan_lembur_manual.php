<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_persetujuan_lembur_manual extends CI_Model {

	public function __construct(){
		parent::__construct();
		//Do your magic here
	}

	function getDataCetak($approve, $tahun_bulan)
	{
		if ($approve != 'all') {
			$this->db->where('approval_status', $approve);
		}
		if ($tahun_bulan != 'all') {
			$this->db->where("DATE_FORMAT(tgl_dws, '%Y-%m') = '$tahun_bulan'");
		}
		
		$query = $this->db->where('is_manual_by_sdm', '1')->get('ess_lembur_transaksi');

		return $query->result_array();
	}
	
	function get_month_list() {
		return $this->db->query('SELECT DISTINCT DATE_FORMAT(tgl_dws, "%Y-%m") AS bln FROM ess_lembur_transaksi')->result();
	}
	
	function save_approval($id, $data) {
		$this->db->where('id', $id);
		$this->db->update('ess_lembur_transaksi', $data); 
		
		if($this->db->affected_rows() > 0) {
			return true; 
		}
		else {
			return false;
		}
	}		
		
	function select_lembur_by_id($id)
	{
		$this->db->select('*');
		$this->db->from('ess_lembur_transaksi');	
		$this->db->where('id',$id);
		
		$query = $this->db->get();
		
		return $query->row_array();
	}
	
	function select_lembur_siap_approve_all()
	{
		$this->db->select('*');
		$this->db->from('ess_cuti');
		$this->db->where('status_1','1');
		$this->db->where('status_2','1');
		$this->db->where("(approval_sdm='0' OR approval_sdm='' OR approval_sdm is null )");	

		$query = $this->db->get();
		
		return $query;
	}
	
}

/* End of file m_persetujuan_cuti_sdm.php */
/* Location: ./application/models/osdm/m_persetujuan_cuti_sdm.php */