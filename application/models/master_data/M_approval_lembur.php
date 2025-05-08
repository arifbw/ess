<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_approval_lembur extends CI_Model {

	private $table="mst_approval_lembur";
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	public function daftar_divisi(){
		$data = $this->db->from('mst_satuan_kerja')
						 ->where('right(kode_unit, 3)="000"')
						 ->where('kode_unit not in (select divisi from mst_approval_lembur)')
						 ->get()
						 ->result_array();
		return $data;
	}
	
	public function data_approval($divisi){
		$data = $this->db->from($this->table)
						 ->where("divisi",$divisi)
						 ->get()
						 ->result_array()[0];
		return $data;
	}
	
	public function daftar_approval_lembur(){
		$data = $this->db->from($this->table)
						 ->select('mst_approval_lembur.*, nama_unit')
						 ->join('mst_satuan_kerja b', 'mst_approval_lembur.divisi=b.kode_unit')
						 ->get()
						 ->result_array();
		return $data;
	}
	
	public function cek_hasil_approval($divisi,$approval){
		$data = $this->db->from($this->table)
						 ->where('divisi',$divisi)
						 ->get();

		if($data->num_rows()==0){
			$return = false;
		}
		else{
			$return = true;
		}

		return $return;
	}
	
	public function cek_tambah_approval($divisi){
		$data = $this->db->from($this->table)
						 ->where('divisi',$divisi)
						 ->get();
		
		if($data->num_rows()==0){
			$return = true;
		}
		else{
			$return = false;
		}
		return $return;
	}
}

/* End of file m_pos.php */
/* Location: ./application/models/master_data/m_pos.php */