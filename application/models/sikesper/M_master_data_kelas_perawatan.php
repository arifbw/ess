<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_master_data_kelas_perawatan extends CI_Model {

	private $table="mst_kelas_perawatan";
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	public function kelas_perawatan($id=null){
		$this->db->from($this->table.' a');
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

	public function kelas_perawatan_pangkat(){
		$this->db->from('mst_karyawan');
		$data = $this->db->group_by('nama_pangkat')
						 ->where('nama_pangkat!=""')
						 ->get()
						 ->result_array();
		return $data;
	}

	public function kelas_perawatan_by_name($name){
		$this->db->from($this->table);
		$data = $this->db->where('kelas', $name)
						 ->get()
						 ->row_array();
		return $data;
	}

	public function kelas_perawatan_pangkat_new(){
		$data = $this->db->select('nama_pangkat')->from('mst_karyawan')
						 ->where('nama_pangkat != ""')
						 ->where("not find_in_set (nama_pangkat, (select group_concat(nama_pangkat) as nama_pangkat from mst_kelas_perawatan))")
						 ->group_by('nama_pangkat')
						 ->get()
						 ->result_array();
		return $data;
	}
	
	public function cek_hasil_kelas_perawatan($nama,$status,$pangkat){
		$data = $this->db->from($this->table)
						 ->where('kelas',$nama)
						 ->where('status',$status)
						 ->where('nama_pangkat',$pangkat)
						 ->get();

		if($data->num_rows()==0){
			$return = false;
		}
		else{
			$return = true;
		}

		return $return;
	}
	
	public function cek_tambah_kelas_perawatan($name){
		$data = $this->db->from($this->table)
						 ->where('kelas',$name)
						 ->get();
		if($data->num_rows()==0){
			$return = true;
		}
		else{
			$return = false;
		}
		return $return;
	}
	
	//public function cek_ubah_kelas_perawatan($nama,$nama_ubah){
	public function cek_ubah_kelas_perawatan($id){
		$return = array("status" => false, "error_info" => "");
		$data = $this->db->from($this->table)
						 ->where('id', $id)
						 ->get();
		
		if($data->num_rows()==0){
			$return["status"] = false;
			$return["error_info"] = "Kelas perawatan tidak ada pada <i>database</i>.";
		}
		else if($data->num_rows()==1){
            $return["status"] = true;
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