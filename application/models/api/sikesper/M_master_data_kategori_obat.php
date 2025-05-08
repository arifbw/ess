<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_master_data_kategori_obat extends CI_Model {

	private $table="mst_kategori_obat";
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	public function kategori_obat($id=null){
		$this->db->from($this->table.' a');
		$this->db->select('a.*, (select nama_kategori from mst_kategori_obat where id=a.id_parent) as parent_kategori');
		if ($id!=null) {
			$data = $this->db->where("id",$id)
						 ->get()
						 ->result_array()[0];
		}
		else {
			$data = $this->db->get()
						 ->result_array();
		}
		return $data;
	}

	public function kategori_obat_parent(){
		$this->db->from($this->table);
		$data = $this->db->where('id_parent', 0)
						 ->get()
						 ->result_array();
		return $data;
	}

	public function kategori_obat_new($id){
		$data = $this->db->from($this->table)
						 ->where("id",$id)
						 ->get()
						 ->result_array()[0];
		return $data;
	}
	
	public function cek_hasil_kategori_obat($nama,$status,$jenis){
		$data = $this->db->from($this->table)
						 ->where('nama_kategori',$nama)
						 ->where('jenis',$jenis)
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
	
	public function cek_tambah_kategori_obat($nama,$jenis){
		$data = $this->db->from($this->table)
						 ->where('nama_kategori',$nama)
						 ->where('jenis',$jenis)
						 ->get();
		
		if($data->num_rows()==0){
			$return = true;
		}
		else{
			$return = false;
		}
		return $return;
	}
	
	//public function cek_ubah_kategori_obat($nama,$nama_ubah){
	public function cek_ubah_kategori_obat($data_update){
		$return = array("status" => false, "error_info" => "");
		$data = $this->db->from($this->table)
						 ->where('id',$data_update['id_ubah'])
						 ->get();
		
		if($data->num_rows()==0){
			$return["status"] = false;
			$return["error_info"] = "Kategori obat tidak ada pada <i>database</i>.";
		}
		else if($data->num_rows()==1){
            $return["status"] = true;
			/*if(strcmp($nama,$nama_ubah)==0){
			}
			else{
				$data = $this->db->from($this->table)
								 ->where('lower(nama)',strtolower($nama_ubah))
								 ->where_not_in('lower(nama)',strtolower($nama))
								 ->get();

				if($data->num_rows()>0){
					$return["status"] = false;
					$return["error_info"] = "Nama Jenis Kendaraan <b>$nama_ubah</b> telah digunakan.";
				}
				else{
					$return["status"] = true;
				}
			}*/
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

/* End of file m_jenis_cuti.php */
/* Location: ./application/models/master_data/m_jenis_cuti.php */