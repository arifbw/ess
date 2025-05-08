<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_cuti_bersama extends CI_Model {

	private $table="mst_cuti_bersama";
	private $table_hari_libur="mst_hari_libur";
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	public function ambil_tanggal($id_cuti_bersama){
		$data = $this->db->select("tanggal")
						 ->from($this->table)
						 ->where("id",$id_cuti_bersama)
						 ->get();
		$tanggal = "";
		
		if($data->num_rows()==1){
			$tanggal=$data->result_array()[0]["tanggal"];
		}
		return $tanggal;
	}

	public function ambil_tahun(){
		return $this->db->select('YEAR(tanggal) as tahun')
							->group_by('YEAR(tanggal)')
							->order_by('YEAR(tanggal)','ASC')
							->get('mst_cuti_bersama')
							->result_array();
	}
	
	public function daftar_cuti_bersama(){
		$data = $this->db->from($this->table)
						 ->order_by("tanggal desc")
						 ->get()
						 ->result_array();
		return $data;
	}

	public function daftar_cuti_bersama_tahun($tahun){
		return $this->db->from($this->table)
							->where('YEAR(tanggal)', $tahun)
							->order_by("tanggal")
							->get()
							->result_array();
	}
	
	public function data_cuti_bersama($tanggal){
		return $this->db->from($this->table)
						 ->where('tanggal',$tanggal)
						 ->get()
						 ->result_array()[0];
	}
	
	public function cek_hasil_cuti_bersama($tanggal,$deskripsi){
		$data = $this->db->from($this->table)
						 ->where('tanggal',$tanggal)
						 ->where('deskripsi',$deskripsi)
						 ->get();

		if($data->num_rows()==0){
			$return = false;
		}
		else{
			$return = true;
		}

		return $return;
	}
	
	public function cek_tambah_cuti_bersama($tanggal){
		$data = $this->db->from($this->table)
						 ->where('tanggal',$tanggal)
						 ->get();
		
		if($data->num_rows()==0){
			$return = true;
			
			$data = $this->db->from($this->table_hari_libur)
							 ->where('tanggal',$tanggal)
							 ->get();
							 
			if($data->num_rows()==0){
				$return = true;
				}
			else{
				$return = false;
			}
		}
		else{
			$return = false;
		}
		return $return;
	}
	
	public function cek_ubah_cuti_bersama($tanggal,$tanggal_ubah){
		$return = array("status" => false, "error_info" => "");
		$data = $this->db->from($this->table)
						 ->where('tanggal',$tanggal)
						 ->get();
		
		if($data->num_rows()==0){
			$return["status"] = false;
			$return["error_info"] = "Cuti Bersama dengan tanggal <b>".tanggal($tanggal)."</b> tidak ada pada <i>database</i>.";
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
					$return["error_info"] = "Cuti Bersama dengan tanggal <b>".tanggal($tanggal_ubah)."</b> telah digunakan.";
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
	
	public function ubah($set,$tanggal,$deskripsi){
		$this->db->where('tanggal',$tanggal)
				 ->where('deskripsi',$deskripsi)
				 ->update($this->table,$set);
	}
}

/* End of file m_cuti_bersama.php */
/* Location: ./application/models/master_data/m_cuti_bersama.php */