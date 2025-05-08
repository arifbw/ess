<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_posisi_menu extends CI_Model {

	private $table="sys_posisi_menu";
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	public function cek_hasil_posisi_menu($nama,$shortcode){
		$data = $this->db->from($this->table)
						 ->where('nama',$nama)
						 ->where('shortcode',$shortcode)
						 ->get();

		if($data->num_rows()==0){
			$return = false;
		}
		else{
			$return = true;
		}

		return $return;
	}
	
	public function cek_tambah_posisi_menu($nama,$shortcode){
		$data = $this->db->from($this->table)
						 ->where('nama',$nama)
						 ->or_where('shortcode',$shortcode)
						 ->get();
		
		if($data->num_rows()==0){
			$return = true;
		}
		else{
			$return = false;
		}
		return $return;
	}
	
	public function cek_ubah_posisi_menu($nama,$shortcode,$nama_ubah,$shortcode_ubah){
		$return = array("status" => false, "error_info" => "");
		$data = $this->db->from($this->table)
						 ->where('nama',$nama)
						 ->where('shortcode',$shortcode)
						 ->get();
		
		if($data->num_rows()==0){
			$return["status"] = false;
			$return["error_info"] = "Posisi Menu dengan nama <b>$nama</b> dan <i>shortcode</i> <b>$shortcode</b> tidak ada pada <i>database</i>.";
		}
		else if($data->num_rows()==1){
			if(strcmp($nama,$nama_ubah)==0 and strcmp($shortcode,$shortcode_ubah)==0){
				$return["status"] = true;
			}
			else{
				$id_posisi_menu = $data->result_array()[0]["id"];
				$data = $this->db->from($this->table)
								 ->where_not_in('id',$id_posisi_menu)
								 ->group_start()
									 ->where('lower(nama)',strtolower($nama_ubah))
									 ->or_where('lower(shortcode)',strtolower($shortcode_ubah))
								 ->group_end()
								 ->get();
				
				if($data->num_rows()>0){
					$return["status"] = false;
					$return["error_info"] = "Posisi Menu dengan nama <b>$nama_ubah</b> atau <i>shortcode</i> <b>$shortcode_ubah</b> telah digunakan.";
				}
				else{
					$return["status"] = true;
				}
			}
		}
		return $return;
	}
	
	public function daftar_posisi_menu(){
		$data = $this->db->from($this->table)
						 ->order_by("nama")
						 ->get()
						 ->result_array();
		return $data;
	}
	
	public function data_posisi_menu($nama){
		$data = $this->db->from($this->table)
						 ->where('nama',$nama)
						 ->get()
						 ->result_array()[0];
		return $data;
	}
	
	public function tambah($data){
		$this->db->insert($this->table,$data);
	}
	
	public function ubah($set,$nama,$shortcode){		
		$this->db->where('nama',$nama)
			 ->where('shortcode',$shortcode)
			 ->update($this->table,$set);
	}
}

/* End of file m_posisi_menu.php */
/* Location: ./application/models/administrator/m_posisi_menu.php */