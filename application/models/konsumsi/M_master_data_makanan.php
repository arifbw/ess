<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_master_data_makanan extends CI_Model {

	private $table="mst_makanan";
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	public function daftar_makanan($id=null){
		$this->db->select('a.id, a.nama, a.status, a.harga, a.lokasi as id_lokasi, b.nama as lokasi');
		$this->db->from($this->table.' a');
		$this->db->join('mst_lokasi b', 'a.lokasi = b.id', 'LEFT');
		if ($id!=null) {
			$data = $this->db->where("a.id",$id)
						 ->get()
						 ->result_array()[0];
		}
		else {
			$data = $this->db->get()
						 ->result_array();
		}
		return $data;
	}

	public function daftar_makanan_new($id){
		$data = $this->db->from($this->table)
						 ->where("id",$id)
						 ->get()
						 ->result_array()[0];
		return $data;
	}
	
	public function cek_hasil_daftar_makanan($data){
		$data = $this->db->from($this->table)
						 ->where('nama',$data['nama'])
						 ->where('lokasi',$data['lokasi'])
						 ->where('status',$data['status'])
						 ->get();

		if($data->num_rows()==0){
			$return = false;
		}
		else{
			$return = true;
		}

		return $return;
	}
	
	public function cek_tambah_daftar_makanan($data){
		$data = $this->db->from($this->table)
						 ->where('nama',$data['nama'])
						 ->where('lokasi',$data['lokasi'])
						 ->get();
		
		if($data->num_rows()==0){
			$return = true;
		}
		else{
			$return = false;
		}
		return $return;
	}
	
	//public function cek_ubah_daftar_makanan($nama,$nama_ubah){
	public function cek_ubah_daftar_makanan($data_update){
		$return = array("status" => false, "error_info" => "");
		$data = $this->db->from($this->table)
						 ->where('id',$data_update['id'])
						 ->get();
		
		if($data->num_rows()==0){
			$return["status"] = false;
			$return["error_info"] = "Jenis Makanan tidak ada pada <i>database</i>.";
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
