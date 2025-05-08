<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_laporan_bantuan_cubes extends CI_Model {

	public function __construct(){
		parent::__construct();
		//Do your magic here
	}

	function getDataCetak($tahun_bulan)
	{
		if ($tahun_bulan != 'all' || $tahun_bulan == null)
			$this->db->where('date_format(bantuan_cuti_besar_tanggal, "%Y-%m")="'.$tahun_bulan.'"');
		$this->db->select('ess_cuti.*, tahun, bantuan_cuti_besar_tanggal');
		$this->db->from('ess_cuti');
		$this->db->join('cuti_cubes_jatah', 'cuti_cubes_jatah.bantuan_cuti_besar_id_cuti = ess_cuti.id');

		return $this->db->get()->result_array();
	}
	
	function get_month_list() {
		return $this->db->query('SELECT DISTINCT DATE_FORMAT(bantuan_cuti_besar_tanggal, "%Y-%m") AS bln FROM cuti_cubes_jatah where bantuan_cuti_besar_tanggal is not null order by bantuan_cuti_besar_tanggal desc')->result_array();
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