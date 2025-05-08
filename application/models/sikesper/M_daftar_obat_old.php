<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_daftar_obat extends CI_Model {

	private $table="ess_daftar_obat";
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	public function daftar_obat($id=null,$jenis=null){
		$this->db->from($this->table.' a');
		$this->db->select('a.*, nama_kategori');
		$this->db->join('mst_kategori_obat b', 'b.id=a.id_kategori');
		$this->db->order_by('id_kategori, zat_aktif, merk_obat, created');
		if ($jenis!='0' && $jenis!=null)
			$this->db->where('b.jenis', $jenis);

		if ($_SESSION["grup"]==5)
			$this->db->where('a.status', '1');

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

	public function daftar_obat_parent(){
		$this->db->from('mst_kategori_obat');
		$data = $this->db->where('id_parent != 0 OR id not in (select id_parent from mst_kategori_obat)')
						 ->get()
						 ->result_array();
		return $data;
	}

	public function daftar_obat_new($id){
		$data = $this->db->from($this->table)
						 ->where("id",$id)
						 ->get()
						 ->result_array()[0];
		return $data;
	}
	
	public function cek_hasil_daftar_obat($merk,$zat,$kategori,$status){
		$data = $this->db->from($this->table)
						 ->where('merk_obat',$merk)
						 ->where('zat_aktif',$zat)
						 ->where('id_kategori',$kategori)
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
	
	public function cek_tambah_daftar_obat($merk,$zat,$kategori){
		$data = $this->db->from($this->table)
						 ->where('merk_obat',$merk)
						 ->where('zat_aktif',$zat)
						 ->where('id_kategori',$kategori)
						 ->get();
		
		if($data->num_rows()==0){
			$return = true;
		}
		else{
			$return = false;
		}
		return $return;
	}
	
	//public function cek_ubah_daftar_obat($nama,$nama_ubah){
	public function cek_ubah_daftar_obat($data_update){
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

	public function cekDaftarObat($id)
	{
		$this->db->select('*, (select jenis from mst_kategori_obat where id=a.id_kategori) as jenis');
		$this->db->from($this->table.' a');
		$this->db->where('id', $id);

		return $this->db->get()->row();
	}
}

/* End of file m_jenis_cuti.php */
/* Location: ./application/models/master_data/m_jenis_cuti.php */