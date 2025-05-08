<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_master_data_ttd extends CI_Model {

	private $table="mst_driver";
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
    
    function get_mst_karyawan(){
        return $this->db->select('no_pokok,nama')->get('mst_karyawan');
    }
	
	public function ambil_perizinan_id($id){
		$data = $this->db->from($this->table)
						 ->where("id",$id)
						 ->get()
						 ->result_array()[0];
		return $data;
	}
	
	public function daftar_driver(){
		$data = $this->db->select('a.*, b.nopol, b.nama as kendaraan')
						 ->from($this->table.' a')
						 ->join('mst_kendaraan b','a.id_mst_kendaraan_default=b.id','LEFT')
						 ->get()
						 ->result_array();
		return $data;
	}
	
	public function data_driver($np){
		$data = $this->db->from($this->table)
						 ->where("np_karyawan",$np)
						 ->get()
						 ->result_array()[0];
		return $data;
	}
	
	public function cek_hasil_driver($np){
		$data = $this->db->from($this->table)
						 ->where('np_karyawan',$np)
						 ->get();

		if($data->num_rows()==0){
			$return = false;
		}
		else{
			$return = true;
		}

		return $return;
	}
	
	public function cek_tambah_driver($np){
		$data = $this->db->from($this->table)
						 ->where('np_karyawan',$np)
						 ->get();
		
		if($data->num_rows()==0){
			$return = true;
		}
		else{
			$return = false;
		}
		return $return;
	}
	
	public function cek_ubah_driver($data_update){
		$return = array("status" => false, "error_info" => "");
		$data = $this->db->from($this->table)
						 ->where('id',$data_update['id'])
						 ->get();
		
		if($data->num_rows()==0){
			$return["status"] = false;
			$return["error_info"] = "Driver tidak ada pada <i>database</i>.";
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