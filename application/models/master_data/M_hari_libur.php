<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_hari_libur extends CI_Model {

	private $table="mst_hari_libur";
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	public function ambil_tanggal($id_hari_libur){
		$data = $this->db->select("tanggal")
						 ->from($this->table)
						 ->where("id",$id_hari_libur)
						 ->get();
		$tanggal = "";
		
		if($data->num_rows()==1){
			$tanggal=$data->result_array()[0]["tanggal"];
		}
		return $tanggal;
	}
	
	public function daftar_hari_libur(){
		$data = $this->db->from($this->table)
						 ->order_by("tanggal desc")
						 ->get()
						 ->result_array();
		return $data;
	}
	
	public function daftar_hari_libur_periode($awal,$akhir){
		// $data = $this->db->from($this->table)
		// 				 ->where("tanggal >= ",$awal)
		// 				 ->where("tanggal <= ",$akhir)
		// 				 ->order_by("tanggal asc")
		// 				 ->get()
		// 				 ->result_array();
		// return $data;
		$bulan_sekarang=date('Y-m');
		$data = $this->db->from($this->table)
						 ->where("DATE_FORMAT(tanggal,'%Y-%m')",$bulan_sekarang)
						 ->order_by("tanggal asc")
						 ->get()
						 ->result_array();
		return $data;
	}
	
	public function data_hari_libur($tanggal){
		$data = $this->db->from($this->table)
						 ->where('tanggal',$tanggal)
						 ->get()
						 ->result_array()[0];
		return $data;
	}
	
	public function cek_hasil_hari_libur($tanggal,$deskripsi,$hari_raya_keagamaan){
		$data = $this->db->from($this->table)
						 ->where('tanggal',$tanggal)
						 ->where('deskripsi',$deskripsi)
						 ->where('hari_raya_keagamaan',$hari_raya_keagamaan)
						 ->get();

		if($data->num_rows()==0){
			$return = false;
		}
		else{
			$return = true;
		}

		return $return;
	}
	
	public function cek_tambah_hari_libur($tanggal){
		$data = $this->db->from($this->table)
						 ->where('tanggal',$tanggal)
						 ->get();
		
		if($data->num_rows()==0){
			$return = true;
		}
		else{
			$return = false;
		}
		return $return;
	}
	
	public function cek_ubah_hari_libur($tanggal,$tanggal_ubah){
		$return = array("status" => false, "error_info" => "");
		$data = $this->db->from($this->table)
						 ->where('tanggal',$tanggal)
						 ->get();
		
		if($data->num_rows()==0){
			$return["status"] = false;
			$return["error_info"] = "Hari Libur dengan tanggal <b>".tanggal($tanggal)."</b> tidak ada pada <i>database</i>.";
		}
		else if($data->num_rows()==1){
			if(strcmp($tanggal,$tanggal_ubah)==0){
				$return["status"] = true;
			}
			else{
				$data = $this->db->from($this->table)
								 ->where('tanggal',$tanggal_ubah)
								 ->where_not_in('tanggal',$tanggal)
								 ->get();

				if($data->num_rows()>0){
					$return["status"] = false;
					$return["error_info"] = "Hari Libur dengan tanggal <b>".tanggal($tanggal_ubah)."</b> telah digunakan.";
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
	
	public function ubah($set,$tanggal,$deskripsi,$hari_raya_keagamaan){
		$this->db->where('tanggal',$tanggal)
				 ->where('deskripsi',$deskripsi)
				 ->where('hari_raya_keagamaan',$hari_raya_keagamaan)
				 ->update($this->table,$set);
	}
}

/* End of file m_hari_libur.php */
/* Location: ./application/models/master_data/m_hari_libur.php */