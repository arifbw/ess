<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_persetujuan_lembur_sdm extends CI_Model {

	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	function save_approval($id, $data) {
		$this->db->where('id', $id);
		$this->db->where('(approval_status is null or approval_status = "0" or approval_status = "")');
		$this->db->where('((waktu_mulai_fix is not null or waktu_mulai_fix != "0000-00-00 00:00:00") AND (waktu_selesai_fix is not null or waktu_selesai_fix != "0000-00-00 00:00:00"))');
		$this->db->where('is_manual_by_sdm != "1"');
		$this->db->update('ess_lembur_transaksi', $data);
		
		if($this->db->affected_rows() == true) {
			return true; 
		}
		else {
			return false;
		}
	}		
		
	function approve_all($data,$set) {
		//jika Pengadministrasi Unit Kerja
		if($_SESSION["grup"]==4) {
			$ada_data=0;
			$var=array();
			$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
			//looping list_pengadministrasi
			foreach ($list_pengadministrasi as $data) {
				array_push($var,$data['kode_unit']);
				$ada_data=1;
			}
			if($ada_data==0)
				$var='';
		}
		else if($_SESSION["grup"]==5) //jika Pengguna
			$var = $_SESSION["no_pokok"];
		else
			$var = 1;

		if($_SESSION["grup"]==4) //jika Pengadministrasi Unit Kerja
			$this->db->where_in('kode_unit', $var);
		else if($_SESSION["grup"]==5) //jika Pengguna
			$this->db->where_in('no_pokok', $var);
		
		if($set['jenis']=='pimpinan') 
			$this->db->where('approval_pimpinan_status = "1"');
		else 
			$this->db->where('approval_pimpinan_status != "2"');
		
		$this->db->where('is_manual_by_sdm != "1"');
		$this->db->where('(month(tgl_dws)="'.substr($set['bulan'],-2).'" and year(tgl_dws)="'.substr($set['bulan'],0,4).'")');
		$this->db->where('(approval_status is null or approval_status = "0" or approval_status = "")');
		$this->db->where('((waktu_mulai_fix is not null or waktu_mulai_fix != "") AND (waktu_selesai_fix is not null or waktu_selesai_fix != ""))');
		$select = $this->db->select('ess_lembur_transaksi.*')->get('ess_lembur_transaksi');
		
		//jika Pengadministrasi Unit Kerja
		if($_SESSION["grup"]==4) {
			$ada_data=0;
			$var=array();
			$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
			//looping list_pengadministrasi
			foreach ($list_pengadministrasi as $data) {
				array_push($var,$data['kode_unit']);
				$ada_data=1;
			}
			if($ada_data==0)
				$var='';
		}
		else if($_SESSION["grup"]==5) //jika Pengguna
			$var = $_SESSION["no_pokok"];
		else
			$var = 1;

		if($_SESSION["grup"]==4) //jika Pengadministrasi Unit Kerja
			$this->db->where_in('kode_unit', $var);
		else if($_SESSION["grup"]==5) //jika Pengguna
			$this->db->where_in('no_pokok', $var);

		if($set['jenis']=='pimpinan') 
			$this->db->where('approval_pimpinan_status = "1"');
		else 
			$this->db->where('approval_pimpinan_status != "2"');

		$this->db->where('is_manual_by_sdm != "1"');
		$this->db->where('(month(tgl_dws)="'.substr($set['bulan'],-2).'" and year(tgl_dws)="'.substr($set['bulan'],0,4).'")');
		$this->db->where('(approval_status is null or approval_status = "0" or approval_status = "")');
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