<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Pengaturan_poh extends CI_Model {

	private $table="mst_poh";
	private $table_mst_pangkat="mst_pangkat";
	private $table_mst_kelompok_jabatan="mst_kelompok_jabatan";
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	
	
	public function daftar_pengaturan_poh(){
		$data = $this->db->query("SELECT a.kode_kelompok_jabatan, a.nama_kelompok_jabatan, regexp_replace(GROUP_CONCAT(b.nama_kelompok_jabatan_poh SEPARATOR '<br>'), '^(<br>)|(<br>)(<br>)+|(<br>)$', '') kelompok_jabatan_poh, regexp_replace(GROUP_CONCAT(b.nama_pangkat_poh SEPARATOR '<br>'), '^(<br>)|(<br>)(<br>)+|(<br>)$', '') pangkat_poh FROM mst_kelompok_jabatan a LEFT JOIN mst_poh b ON a.id=b.id_kelompok_jabatan GROUP BY a.kode_kelompok_jabatan ORDER BY a.kode_kelompok_jabatan")->result_array();
		
		/* $data = $this->db->select("a.kode_kelompok_jabatan")
						 ->select("a.nama_kelompok_jabatan")
						 ->select("regexp_replace(GROUP_CONCAT(b.nama_kelompok_jabatan_poh),'(^,)|(,,+)|(,$)','') kelompok_jabatan_poh",false)
						 ->select("regexp_replace(GROUP_CONCAT(b.nama_pangkat_poh),'(^,)|(,,+)|(,$)','') pangkat_poh",false)
						 ->from($this->table_mst_kelompok_jabatan." a")
						 ->join($this->table." b","a.id=b.id_kelompok_jabatan","left")
						 ->group_by("a.kode_kelompok_jabatan")
						 ->order_by("a.kode_kelompok_jabatan")
						 ->get()
						 ->result_array(); */
		//echo $this->db->last_query();
		return $data;
	}
	
	public function daftar_pangkat(){
		$data = $this->db->from($this->table_mst_pangkat)
						 ->order_by("grade_pangkat", "desc")
						 ->order_by("id", "asc")
						 ->get()
						 ->result_array();
		return $data;
	}
	
	public function daftar_kelompok_jabatan(){
		$data = $this->db->from($this->table_mst_kelompok_jabatan)
						 ->order_by("kode_kelompok_jabatan")
						 ->get()
						 ->result_array();
		return $data;
	}
	
	public function data_poh($kode_kelompok_jabatan){
		$data = $this->db->query("SELECT a.kode_kelompok_jabatan, a.nama_kelompok_jabatan, regexp_replace(GROUP_CONCAT(b.nama_kelompok_jabatan_poh SEPARATOR '<br>'), '^(<br>)|(<br>)(<br>)+|(<br>)$', '') kelompok_jabatan_poh, regexp_replace(GROUP_CONCAT(b.nama_pangkat_poh SEPARATOR '<br>'), '^(<br>)|(<br>)(<br>)+|(<br>)$', '') pangkat_poh FROM mst_kelompok_jabatan a LEFT JOIN mst_poh b ON a.id=b.id_kelompok_jabatan WHERE a.kode_kelompok_jabatan='$kode_kelompok_jabatan' GROUP BY a.kode_kelompok_jabatan ORDER BY a.kode_kelompok_jabatan")->result_array()[0];
		
		/* $data = $this->db->select("a.kode_kelompok_jabatan")
						 ->select("a.nama_kelompok_jabatan")
						 ->select("GROUP_CONCAT(b.nama_kelompok_jabatan_poh) kelompok_jabatan_poh")
						 ->select("GROUP_CONCAT(b.nama_pangkat_poh) pangkat_poh")
						 ->from($this->table_mst_kelompok_jabatan." a")
						 ->join($this->table." b","a.id=b.id_kelompok_jabatan","left")
						 ->where("a.kode_kelompok_jabatan",$kode_kelompok_jabatan)
						 ->group_by("a.kode_kelompok_jabatan")
						 ->order_by("a.kode_kelompok_jabatan")
						 ->get()
						 ->result_array()[0];//echo $this->db->last_query(); */
		return $data;
	}
	
	
	
	public function cek_hasil_poh($kode_kelompok_jabatan,$kelompok_jabatan,$pangkat){
		$return = false;
		if ($this->cek_hasil_poh_jabatan($kode_kelompok_jabatan,$kelompok_jabatan) and $this->cek_hasil_poh_pangkat($kode_kelompok_jabatan,$pangkat)){
			$return = true;
		}

		return $return;
	}
	
	public function cek_hasil_poh_jabatan($kode_kelompok_jabatan,$kelompok_jabatan){
		$return = false;
		
		if(count($kelompok_jabatan)>0){			
			$data = $this->db->from($this->table)
							 ->where("kode_kelompok_jabatan",$kode_kelompok_jabatan)
							 ->where_in("id_kelompok_jabatan_poh",$kelompok_jabatan)
							 ->get();//echo $this->db->last_query();

			if($data->num_rows()==count($kelompok_jabatan)){
				$return = true;
			}
		}
		else{
			$data = $this->db->from($this->table)
							 ->where("kode_kelompok_jabatan",$kode_kelompok_jabatan)
							 ->where("id_kelompok_jabatan_poh != ","0")
							 ->get();//echo $this->db->last_query();

			if($data->num_rows()==0){
				$return = true;
			}
		}

		return $return;
	}
	
	public function cek_hasil_poh_pangkat($kode_kelompok_jabatan,$pangkat){
		$return = false;

		if(count($pangkat)>0){
			$data = $this->db->from($this->table)
							 ->where("kode_kelompok_jabatan",$kode_kelompok_jabatan)
							 ->where_in("id_pangkat_poh",$pangkat)
							 ->get();//echo $this->db->last_query();
	//echo $data->num_rows()." = ".count($pangkat);
			if($data->num_rows()==count($pangkat)){
				$return = true;
			}
		}
		else{
			$data = $this->db->from($this->table)
							 ->where("kode_kelompok_jabatan",$kode_kelompok_jabatan)
							 ->where("id_pangkat_poh != ","0")
							 ->get();
			if($data->num_rows()==0){
				$return = true;
			}
		}

		return $return;
	}
	
	public function tambah($data){
		$this->db->insert($this->table,$data);//echo $this->db->last_query();
	}
	
	public function hapus($kode_kelompok_jabatan){
		$this->db->where("kode_kelompok_jabatan",$kode_kelompok_jabatan)
				 ->delete($this->table);
	}
	
	public function update_id_jabatan_kelompok($kode_kelompok_jabatan){
		$this->db->query("UPDATE $this->table a LEFT JOIN $this->table_mst_kelompok_jabatan b ON a.kode_kelompok_jabatan=b.kode_kelompok_jabatan SET a.id_kelompok_jabatan = b.id WHERE a.kode_kelompok_jabatan='$kode_kelompok_jabatan'");
	}
	
	public function update_jabatan_poh($kode_kelompok_jabatan){
		$this->db->query("UPDATE $this->table a LEFT JOIN $this->table_mst_kelompok_jabatan b ON a.id_kelompok_jabatan_poh=b.id SET a.kode_kelompok_jabatan_poh = b.kode_kelompok_jabatan, a.nama_kelompok_jabatan_poh = b.nama_kelompok_jabatan WHERE a.kode_kelompok_jabatan='$kode_kelompok_jabatan'");
	}
	
	public function update_pangkat_poh($kode_kelompok_jabatan){
		$this->db->query("UPDATE $this->table a LEFT JOIN $this->table_mst_pangkat b ON a.id_pangkat_poh=b.id SET a.nama_pangkat_poh = b.nama_pangkat, a.nama_sap = b.nama_sap WHERE a.kode_kelompok_jabatan='$kode_kelompok_jabatan'");
	}
	
	/* public function ubah($set,$nama,$status){
		$this->db->where('nama',$nama)
				 ->where('status',$status)
				 ->update($this->table,$set);
	} */
}

/* End of file m_pengaturan_poh.php */
/* Location: ./application/models/master_data/m_pengaturan_poh.php */