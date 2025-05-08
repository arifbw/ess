<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_jadwal_kerja extends CI_Model {

	private $table="mst_jadwal_kerja";
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	public function ambil_jadwal_kerja_id($id){
		$data = $this->db->from($this->table)
						 ->where("id",$id)
						 ->get()
						 ->result_array()[0];
		return $data;
	}
	
	public function daftar_jadwal_kerja(){
		$data = $this->db->from($this->table)
						 ->order_by("dws asc")
						 ->order_by("dws_variant asc")
						 ->get()
						 ->result_array();
		return $data;
	}
	
	public function data_jadwal_kerja($kode_erp,$varian){
		$data = $this->db->from($this->table)
						 ->where("dws",$kode_erp)
						 ->where("dws_variant",$varian)
						 ->get()
						 ->result_array()[0];
		return $data;
	}
	
	public function cek_hasil_jadwal_kerja($nama,$hari,$lintas_hari_masuk,$jam_masuk,$istirahat,$lintas_hari_mulai_istirahat,$jam_mulai_istirahat,$lintas_hari_akhir_istirahat,$jam_akhir_istirahat,$lintas_hari_pulang,$jam_pulang,$kode_erp,$varian,$status){
		if(empty($jam_masuk)){
			$jam_masuk = "00:00:00";
		}
		if(empty($jam_mulai_istirahat)){
			$jam_mulai_istirahat = "00:00:00";
		}
		if(empty($jam_akhir_istirahat)){
			$jam_akhir_istirahat = "00:00:00";
		}
		if(empty($jam_pulang)){
			$jam_pulang = "00:00:00";
		}
		
		$data = $this->db->from($this->table)
						 ->where('description',$nama)
						 ->where('libur',$hari)
						 ->where('lintas_hari_masuk',$lintas_hari_masuk)
						 ->where('dws_start_time',$jam_masuk)
						 ->where('istirahat',$istirahat)
						 ->where('lintas_hari_mulai_istirahat',$lintas_hari_mulai_istirahat)
						 ->where('dws_break_start_time',$jam_mulai_istirahat)
						 ->where('lintas_hari_akhir_istirahat',$lintas_hari_akhir_istirahat)
						 ->where('dws_break_end_time',$jam_akhir_istirahat)
						 ->where('lintas_hari_pulang',$lintas_hari_pulang)
						 ->where('dws_end_time',$jam_pulang)
						 ->where('dws',$kode_erp)
						 ->where('dws_variant',$varian)
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
	
	public function cek_tambah_jadwal_kerja($kode_erp,$varian,$nama){
		$return = array("status" => false, "error_info" => "");
		$data = $this->db->from($this->table)
						 ->where('description',$nama)
						 ->get();
		
		if($data->num_rows()==0){
			$return["status"] = true;
			
			$data = $this->db->from($this->table)
							 ->where('dws',$kode_erp)
							 ->where('dws_variant',$varian)
							 ->get();
							 
			if($data->num_rows()==0){
				$return["status"] = true;
			}
			else{
				$return["status"] = false;
				$return["error_info"] = "Jadwal Kerja dengan kode ERP <b>$kode_erp</b>";
				if(empty($varian)){
					$return["error_info"] .= " tanpa ";
				}
				else{
					$return["error_info"] .= " dengan ";
				}
				$return["error_info"] .= "varian sudah ada pada <i>database</i>.";
			}
		}
		else{
			$return["status"] = false;
			$return["error_info"] = "Jadwal Kerja dengan nama <b>$nama</b> sudah ada pada <i>database</i>.";
		}
		return $return;
	}
	
	public function cek_ubah_jadwal_kerja($nama,$nama_ubah,$kode_erp,$kode_erp_ubah,$varian,$varian_ubah){
		$return = array("status" => false, "error_info" => "");
		$data = $this->db->from($this->table)
						 ->where('description',$nama)
						 ->get();
		
		if($data->num_rows()==0){
			$return["status"] = false;
			$return["error_info"] = "Jadwal Kerja dengan nama <b>$nama</b> tidak ada pada <i>database</i>.";
		}
		else if($data->num_rows()>0){
			if(strcmp($nama,$nama_ubah)==0){
				$return["status"] = true;
			}
			else{
				$data = $this->db->from($this->table)
								 ->where('lower(description)',strtolower($nama_ubah))
								 ->where_not_in('lower(description)',strtolower($nama))
								 ->get();

				if($data->num_rows()>0){
					$return["status"] = false;
					$return["error_info"] = "Nama Jadwal Kerja <b>$nama_ubah</b> telah digunakan.";
				}
				else{
					$return["status"] = true;
				}
			}
		}
		
		if($return["status"] and strcmp($nama,$nama_ubah)!=0){
			$data = $this->db->from($this->table)
							 ->where('dws',$kode_erp_ubah)
							 ->where('dws_variant',$varian_ubah)
							 ->where_not_in('dws',$kode_erp_ubah)
							 ->where_not_in('dws_variant',$varian)
							 ->get();
							 
			if($data->num_rows()>0){
				$return["status"] = false;
				$return["error_info"] = "Jadwal Kerja dengan kode ERP <b>$kode_erp_ubah</b>";
				if(empty($varian_ubah)){
					$return["error_info"] .= " tanpa ";
				}
				else{
					$return["error_info"] .= " dengan ";
				}
				$return["error_info"] .= "varian sudah ada pada <i>database</i>.";
			}
			else{
				$return["status"] = true;
			}
		}
		
		return $return;
	}
	
	public function tambah($data){
		$this->db->insert($this->table,$data);
	}
	
	public function ubah($set,$id){
		$this->db->where('id',$id)
				 ->update($this->table,$set);
	}
}

/* End of file m_jadwal_kerja.php */
/* Location: ./application/models/master_data/m_jadwal_kerja.php */