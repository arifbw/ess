<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_cdtp extends CI_Model {

	private $table="cuti_cdtp";
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	public function ambil_nama($id_cdtp){
		$data = $this->db->select("nama")
						 ->from($this->table)
						 ->where("id",$id_cdtp)
						 ->get();
		$nama = "";
		
		if($data->num_rows()==1){
			$nama=$data->result_array()[0]["nama"];
		}
		return $nama;
	}
	
	public function daftar_cdtp(){
		$data = $this->db->from($this->table)
						 ->order_by("nama asc")
						 ->get()
						 ->result_array();
		return $data;
	}
	
	public function data_cdtp($no_pokok){
		$data = $this->db->from($this->table)
						 ->where('no_pokok',$no_pokok)
						 ->get()
						 ->result_array()[0];
		return $data;
	}
	
	public function cek_hasil_cdtp($no_pokok,$nama,$tanggal_mulai,$tanggal_selesai,$skep){
		$data = $this->db->from($this->table)
						 ->where('no_pokok',$no_pokok)
						 ->where('nama',$nama)
						 ->where('tanggal_mulai',$tanggal_mulai)
						 ->where('tanggal_selesai',$tanggal_selesai)
						 ->where('skep',$skep)
						 ->get();

		if($data->num_rows()==0){
			$return = false;
		}
		else{
			$return = true;
		}

		return $return;
	}
	
	public function cek_tambah_cdtp($no_pokok){
		$data = $this->db->from($this->table)
						 ->where('no_pokok',$no_pokok)
						 ->get();
		
		if($data->num_rows()==0){
			$return = true;
		}
		else{
			$return = false;
		}
		return $return;
	}
	
	public function cek_ubah_cdtp($no_pokok,$no_pokok_ubah){
		$return = array("status" => false, "error_info" => "");
		$data = $this->db->from($this->table)
						 ->where('no_pokok',$no_pokok)
						 ->get();
		
		if($data->num_rows()==0){
			$return["status"] = false;
			$return["error_info"] = "Cuti di Luar Tanggungan Perusahaan (CDTP) untuk No. Pokok <b>$no_pokok</b> tidak ada pada <i>database</i>.";
		}
		else if($data->num_rows()==1){
			if(strcmp($no_pokok,$no_pokok_ubah)==0){
				$return["status"] = true;
			}
			else{
				$data = $this->db->from($this->table)
								 ->where('no_pokok',$no_pokok_ubah)
								 ->where_not_in('no_pokok',$no_pokok)
								 ->get();

				if($data->num_rows()>0){
					$return["status"] = false;
					$return["error_info"] = "Cuti di Luar Tanggungan Perusahaan (CDTP) untuk No. Pokok <b>$no_pokok_ubah</b> telah digunakan.";
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
	
	public function ubah($set,$no_pokok,$nama,$tanggal_mulai,$tanggal_selesai,$skep){
		$this->db->where('no_pokok',$no_pokok)
				 ->where('nama',$nama)
				 ->where('tanggal_mulai',$tanggal_mulai)
				 ->where('tanggal_selesai',$tanggal_selesai)
				 ->where('skep',$skep)
				 ->update($this->table,$set);
	}
}

/* End of file m_cdtp.php */
/* Location: ./application/models/osdm/m_cdtp.php */