<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_jenis_alasan_lembur extends CI_Model {

	private $table="tb_kategori_lembur";
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}

	public function daftar_jenis_alasan_lembur(){
		$data = $this->db->from($this->table)
						 ->order_by("id asc")
						 ->get()
						 ->result_array();
		return $data;
	}

	public function tambah($data){
		$this->db->insert($this->table,$data);
	}

	public function cek_tambah_jenis_alasan_lembur($kategori_lembur){
		$data = $this->db->from($this->table)
						 ->where('kategori_lembur',$kategori_lembur)
						 ->get();
		
		if($data->num_rows()==0){
			$return = true;
		}
		else{
			$return = false;
		}
		return $return;
	}

	public function cek_hasil_jenis_alasan_lembur($kategori_lembur,$status){
		$data = $this->db->from($this->table)
						 ->where('kategori_lembur',$kategori_lembur)
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

	public function data_jenis_alasan_lembur($kategori_lembur){
		$data = $this->db->from($this->table)
						 ->where("kategori_lembur",$kategori_lembur)
						 ->get()
						 ->result_array()[0];
		return $data;
	}

	public function cek_ubah_jenis_alasan_lembur($kategori_lembur,$kategori_lembur_ubah){
		$return = array("status" => false, "error_info" => "");
		$data = $this->db->from($this->table)
						 ->where('kategori_lembur',$kategori_lembur)
						 ->get();
		
		if($data->num_rows()==0){
			$return["status"] = false;
			$return["error_info"] = "Jenis Alasan Lembur dengan <b>$kategori_lembur</b> tidak ada pada <i>database</i>.";
		}
		else if($data->num_rows()==1){
			if(strcmp($kategori_lembur,$kategori_lembur_ubah)==0){
				$return["status"] = true;
			}
			else{
				$data = $this->db->from($this->table)
								 ->where('lower(kategori_lembur)',strtolower($kategori_lembur_ubah))
								 ->where_not_in('lower(kategori_lembur)',strtolower($kategori_lembur))
								 ->get();

				if($data->num_rows()>0){
					$return["status"] = false;
					$return["error_info"] = "Categori Lembur pada Jenis Alasan Lembur <b>$kategori_lembur_ubah</b> telah digunakan.";
				}
				else{
					$return["status"] = true;
				}
			}
		}
		return $return;
	}

	public function ubah($set,$kategori_lembur){
		$this->db->where('kategori_lembur',$kategori_lembur)
				 ->update($this->table,$set);
	}
}

/* End of file m_jenis_cuti.php */
/* Location: ./application/models/master_data/m_jenis_cuti.php */