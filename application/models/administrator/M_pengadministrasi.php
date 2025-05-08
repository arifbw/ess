<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_pengadministrasi extends CI_Model {

	private $table="usr_pengadministrasi";
	private $table_pengguna="usr_pengguna";
	private $table_unit_kerja="mst_satuan_kerja";
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	public function cek_pengadministrasi($id_pengguna,$arr_unit_kerja){
		$list_unit_kerja = implode("','",$arr_unit_kerja);
		$data_cocok = $this->db->select("COUNT(*) banyak")
							 ->from($this->table)
							 ->where("id_pengguna",$id_pengguna)
							 ->where_in("kode_unit",$arr_unit_kerja)
							 ->get()
							 ->result_array();
							 
		$data_tidak_cocok = $this->db->select("COUNT(*) banyak")
							 ->from($this->table)
							 ->where("id_pengguna",$id_pengguna)
							 ->where_not_in("kode_unit",$arr_unit_kerja)
							 ->get()
							 ->result_array();

		if((int)$data_cocok[0]["banyak"]==count($arr_unit_kerja) and (int)$data_tidak_cocok[0]["banyak"]==0){
			$hasil=true;
		}
		else{
			$hasil=false;
		}
		
		return $hasil;
	}
	
	public function data_pengadministrasi($username){
		$data = $this->db->select("a.kode_unit")
						 ->select("u.nama_unit")
						 ->from($this->table." a")
						 ->join($this->table_pengguna." p","a.id_pengguna=p.id","left")
						 ->join($this->table_unit_kerja." u","a.kode_unit=u.kode_unit","inner")
						 ->where("username",$username)
						 ->get()
						 ->result_array();
		return $data;
	}
	
	public function hapus($data){
		$this->db->delete($this->table,$data);//echo $this->db->last_query()."<br>";
	}
	
	public function tambah($data){
		$this->db->insert_batch($this->table,$data);//echo $this->db->last_query()."<br>";
	}
	
	
}

/* End of file m_pengadministrasi.php */

/* Location: ./application/models/administrator/m_pengadministrasi.php */