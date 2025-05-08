<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_regulasi extends CI_Model {

	private $table="mst_regulasi";
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	public function ambil_perizinan_id($id){
		$data = $this->db->from($this->table)
						 ->where("id",$id)
						 ->get()
						 ->result_array()[0];
		return $data;
	}
	
	public function daftar_regulasi(){
		$data = $this->db->select('a.*, b.nama as laporan')
            ->from($this->table.' a')
            ->join('sys_modul b','a.id_laporan = b.id')
            ->order_by('a.id_laporan','asc')
            ->get()
            ->result_array();
		return $data;
	}

    public function daftar_laporan_tambah() {
		$id = $this->db->where('status','1')->group_by('id_laporan')->get('mst_regulasi')->result();
		$laporan = array();
		foreach($id as $row){
			$laporan[] = $row->id_laporan;
		}
        return $this->db
        ->where('id_kelompok_modul',22)
        ->like('nama','Laporan')
        ->where('url !=',"#")
        ->where('status','1')
		->where_not_in('id',$laporan)
        ->get('sys_modul')->result();
    }

	public function daftar_laporan_ubah() {
        return $this->db
        ->where('id_kelompok_modul',22)
        ->like('nama','Laporan')
        ->where('url !=',"#")
        ->where('status','1')
        ->get('sys_modul')->result();
    }
	
	public function data_regulasi($id){
		$data = $this->db->from($this->table)
            ->where("id",$id)
            ->get()
            ->row();
		return $data;
	}
	
	public function cek_hasil($id_laporan,$status){
		$data = $this->db->from($this->table)
            ->where('id_laporan',$id_laporan)
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
	
	public function cek_tambah($id_laporan){
		$data = $this->db->from($this->table)
            ->where('id_laporan',$id_laporan)
			->where('aktif','1')
            ->get();

		if($data->num_rows()==0){
			$return = true;
		}
		else{
			$return = false;
		}
		return $return;
	}
	
	public function cek_ubah_pos($nama,$nama_ubah){
		$return = array("status" => false, "error_info" => "");
		$data = $this->db->from($this->table)
						 ->where('nama',$nama)
						 ->get();
		
		if($data->num_rows()==0){
			$return["status"] = false;
			$return["error_info"] = "Nama pos <b>$nama</b> tidak ada pada <i>database</i>.";
		}
		else if($data->num_rows()==1){
			if(strcmp($nama,$nama_ubah)==0){
				$return["status"] = true;
			}
			else{
				$data = $this->db->from($this->table)
								 ->where('lower(nama)',strtolower($nama_ubah))
								 ->where_not_in('lower(nama)',strtolower($nama))
								 ->get();

				if($data->num_rows()>0){
					$return["status"] = false;
					$return["error_info"] = "Nama pos <b>$nama_ubah</b> telah digunakan.";
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
	
	public function ubah($set,$id){
		$this->db->where('id',$id)->update($this->table,$set);
	}
}
