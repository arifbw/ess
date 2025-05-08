<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_persetujuan_lembur extends CI_Model {

	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	function save_approval($id, $data) {
		$this->db->where('id', $id);
		$this->db->where('approval_pimpinan_np', $_SESSION["no_pokok"]);
		$this->db->where('(approval_pimpinan_status is null or approval_pimpinan_status = "0" or approval_pimpinan_status = "")');
		$this->db->where('((waktu_mulai_fix is not null or waktu_mulai_fix != "0000-00-00 00:00:00") AND (waktu_selesai_fix is not null or waktu_selesai_fix != "0000-00-00 00:00:00"))');
		//$this->db->where('is_manual_by_sdm != "1"');
		$this->db->update('ess_lembur_transaksi', $data);
		
		if($this->db->affected_rows() == true) {
			return true; 
		}
		else {
			return false;
		}
	}		
		
	function approve_all($data,$bulan) {
		$this->db->where('approval_pimpinan_np', $_SESSION["no_pokok"]);
		// $this->db->where('is_manual_by_sdm != "1"');
		$this->db->where(' DATE_FORMAT(tgl_dws, "%Y-%m")="'.$bulan.'"');
		$this->db->where('(approval_pimpinan_status is null or approval_pimpinan_status = "0" or approval_pimpinan_status = "")');
		$this->db->where('((waktu_mulai_fix is not null or waktu_mulai_fix != "") AND (waktu_selesai_fix is not null or waktu_selesai_fix != ""))');
		$select = $this->db->select('ess_lembur_transaksi.*')->get('ess_lembur_transaksi');
		
		$this->db->where('approval_pimpinan_np', $_SESSION["no_pokok"]);
		// $this->db->where('is_manual_by_sdm != "1"');
		$this->db->where(' DATE_FORMAT(tgl_dws, "%Y-%m")="'.$bulan.'"');
		$this->db->where('(approval_pimpinan_status is null or approval_pimpinan_status = "0" or approval_pimpinan_status = "")');
		$this->db->where('((waktu_mulai_fix is not null or waktu_mulai_fix != "") AND (waktu_selesai_fix is not null or waktu_selesai_fix != ""))');
		$this->db->set($data)->update('ess_lembur_transaksi');

		if($this->db->affected_rows() > 0) {
			return $select;
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
	
	function getDataCetak($get)
	{
		$np = implode("','", $get['np_karyawan']);
		$this->db->where("no_pokok in ('".$np."')");
		$this->db->where("approval_pimpinan_np", $_SESSION['no_pokok']);
		$this->db->select("no_pokok, nama, sec_to_time(sum(time_to_sec(TIMEDIFF(waktu_selesai_fix, waktu_mulai_fix)))) as waktu");
		$this->db->group_by("no_pokok");
		$data = $this->db->from('ess_lembur_transaksi')
				 ->where("date_format(tgl_dws,'%m-%Y')", $get['bulan'])
				 ->where('((waktu_mulai_fix is not null or waktu_mulai_fix != "0000-00-00 00:00:00") AND (waktu_selesai_fix is not null or waktu_selesai_fix != "0000-00-00 00:00:00"))')
				 ->get()
				 ->result_array();
		return $data;
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