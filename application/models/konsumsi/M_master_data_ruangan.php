<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_master_data_ruangan extends CI_Model {

	private $table="mst_ruangan";
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	public function daftar_ruangan($nama=null){
		$this->db->select('a.*, b.nama as gedung');
		$this->db->from($this->table.' a');
		$this->db->join('mst_gedung b', 'a.id_gedung=b.id');
		if ($nama!=null) {
			$data = $this->db->where("a.nama",$nama)
						 ->get()
						 ->result_array()[0];
		}
		else {
			$data = $this->db->get()
						 ->result_array();
		}
		return $data;
	}

	public function daftar_ruangan_new($id){
		$data = $this->db->from($this->table)
						 ->where("id",$id)
						 ->get()
						 ->result_array()[0];
		return $data;
	}
	
	public function cek_hasil_daftar_ruangan($nama,$status){
		$data = $this->db->from($this->table)
						 ->where('nama',$nama)
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
	
	public function cek_tambah_daftar_ruangan($nama){
		$data = $this->db->from($this->table)
						 ->where('nama',$nama)
						 ->get();
		
		if($data->num_rows()==0){
			$return = true;
		}
		else{
			$return = false;
		}
		return $return;
	}
	
	//public function cek_ubah_daftar_ruangan($nama,$nama_ubah){
	public function cek_ubah_daftar_ruangan($data_update){
		$return = array("status" => false, "error_info" => "");
		$data = $this->db->from($this->table)
						 ->where('id',$data_update['id'])
						 ->get();
		
		if($data->num_rows()==0){
			$return["status"] = false;
			$return["error_info"] = "Jenis ruangan tidak ada pada <i>database</i>.";
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

	public function cek_daftar_ruangan($id)
	{
		$this->db->select('a.id, a.nama, a.kapasitas, a.id_gedung as gedung, b.nama as nama_gedung, c.id as lokasi, c.nama as nama_lokasi, a.status'); # heru menambahkan "kapasitas" 2020-11-25 @09:00
		$this->db->from($this->table.' a');
		$this->db->join('mst_gedung b', 'a.id_gedung = b.id');
		$this->db->join('mst_lokasi c', 'b.lokasi = c.id');
		$this->db->where('a.id', $id);

		return $this->db->get()->row();
	}

	public function daftar_lokasi()
	{
		$this->db->select('id, nama as text');
		$this->db->from('mst_lokasi');
		$this->db->where('status', '1');

		$search = $this->input->get('search');
		if(!empty($search)){
			$this->db->like('nama', $search);
		}

		return $this->db->get()->result_array();
	}

	public function daftar_gedung($lokasi = null)
	{
		$this->db->select('id, nama as text');
		$this->db->from('mst_gedung');
		$this->db->where('status', '1');

		if($lokasi != null){
			$this->db->where('lokasi', $lokasi);
		}

		$search = $this->input->get('search');
		if(!empty($search)){
			$this->db->like('nama', $search);
		}

		return $this->db->get()->result_array();
	}
}

/* End of file m_jenis_cuti.php */
/* Location: ./application/models/master_data/m_jenis_cuti.php */