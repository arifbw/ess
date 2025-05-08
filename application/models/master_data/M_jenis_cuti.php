<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_jenis_cuti extends CI_Model {

	private $table="mst_cuti";
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	public function ambil_perizinan_id($id){
		$data = $this->db->from($this->table)
						 ->where("id",$id)
						 ->get()
						 ->result_array()[0];
		return $data;
	}
	
	public function daftar_jenis_cuti(){
		$data = $this->db->from($this->table)
						 ->order_by("kode_erp asc")
						 ->get()
						 ->result_array();
		return $data;
	}
	
	public function data_jenis_cuti($uraian){
		$data = $this->db->from($this->table)
						 ->where("uraian",$uraian)
						 ->get()
						 ->result_array()[0];
		return $data;
	}
	
	public function cek_hasil_jenis_cuti($uraian,$kode_erp,$status){
		$data = $this->db->from($this->table)
						 ->where('uraian',$uraian)
						 ->where('kode_erp',$kode_erp)
						 ->where('status',$status)
						 ->get();

		if($data->num_rows()==0){
			$return = false;
		}
		else{
			$return = true;
		}

		return $return;
	}
	
	public function cek_tambah_jenis_cuti($uraian){
		$data = $this->db->from($this->table)
						 ->where('uraian',$uraian)
						 ->get();
		
		if($data->num_rows()==0){
			$return = true;
		}
		else{
			$return = false;
		}
		return $return;
	}
	
	public function cek_ubah_jenis_cuti($uraian,$uraian_ubah){
		$return = array("status" => false, "error_info" => "");
		$data = $this->db->from($this->table)
						 ->where('uraian',$uraian)
						 ->get();
		
		if($data->num_rows()==0){
			$return["status"] = false;
			$return["error_info"] = "Jenis Cuti dengan uraian <b>$uraian</b> tidak ada pada <i>database</i>.";
		}
		else if($data->num_rows()==1){
			if(strcmp($uraian,$uraian_ubah)==0){
				$return["status"] = true;
			}
			else{
				$data = $this->db->from($this->table)
								 ->where('lower(uraian)',strtolower($uraian_ubah))
								 ->where_not_in('lower(uraian)',strtolower($uraian))
								 ->get();

				if($data->num_rows()>0){
					$return["status"] = false;
					$return["error_info"] = "uraian Jenis Cuti <b>$uraian_ubah</b> telah digunakan.";
				}
				else{
					$return["status"] = true;
				}
			}
		}
		return $return;
	}
	
	public function tambah($data){
		$this->db->insert($this->table,$data);
	}
	
	public function ubah($set,$uraian){
		$this->db->where('uraian',$uraian)
				 ->update($this->table,$set);
	}
}

/* End of file m_jenis_cuti.php */
/* Location: ./application/models/master_data/m_jenis_cuti.php */