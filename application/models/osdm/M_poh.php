<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_poh extends CI_Model {

	private $table="poh";
	private $table_mst_poh="mst_poh";
	private $table_karyawan="mst_karyawan";
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	public function cek_hasil_poh($tanggal_mulai,$tanggal_selesai,$kode_jabatan,$np_definitif,$np_poh,$nomor_nota_dinas,$keterangan){
		$data = $this->db->from($this->table)
						 ->where('tanggal_mulai',$tanggal_mulai)
						 ->where('tanggal_selesai',$tanggal_selesai)
						 ->where('kode_jabatan',$kode_jabatan)
						 ->where('np_definitif',$np_definitif)
						 ->where('np_poh',$np_poh)
						 ->where('nomor_nota_dinas',$nomor_nota_dinas)
						 ->where('keterangan',$keterangan)
						 ->get();

		if($data->num_rows()==0){
			$return = false;
		}
		else{
			$return = true;
		}

		return $return;
	}
	
	public function cek_tambah_poh($kode_jabatan,$tanggal_mulai,$tanggal_selesai){
		$data = $this->db->from($this->table)
						 ->where('kode_jabatan',$kode_jabatan)
						 ->where('tanggal_mulai<=',$tanggal_selesai)
						 ->where('tanggal_selesai>=',$tanggal_mulai)
						 ->get();
		
		if($data->num_rows()==0){
			$return = true;
		}
		else{
			$return = false;
		}
		return $return;
	}
	
	public function cek_ubah_poh($kode_jabatan,$tanggal_mulai,$tanggal_selesai,$kode_jabatan_ubah,$np_definitif_ubah,$nama_definitif_ubah,$sesuai_skep_ubah,$karyawan_ubah,$tanggal_mulai_ubah,$tanggal_selesai_ubah,$nomor_nota_dinas_ubah,$keterangan_ubah){
		$return = array("status" => false, "error_info" => "");
		$data = $this->db->from($this->table)
						 ->where('kode_jabatan',$kode_jabatan)
						 ->where('tanggal_mulai',$tanggal_mulai)
						 ->where('tanggal_selesai',$tanggal_selesai)
						 ->get();
		
		if($data->num_rows()==0){
			$return["status"] = false;
			$return["error_info"] = "Pemegang Operasional Harian (POH) untuk $kode_jabatan pada periode $tanggal_mulai sampai dengan $tanggal_selesai tidak ada.";
		}
		else if($data->num_rows()==1){
			if(strcmp($kode_jabatan,$kode_jabatan_ubah)==0 and strcmp($tanggal_mulai,$tanggal_mulai_ubah)==0 and strcmp($tanggal_selesai,$tanggal_selesai_ubah)==0){
				$return["status"] = true;
			}
			else{
				$data = $this->db->from($this->table)
								 ->where('kode_jabatan',$kode_jabatan_ubah)
								 ->where('tanggal_mulai<=',$tanggal_mulai_ubah)
								 ->where('tanggal_selesai>=',$tanggal_selesai_ubah)
								 ->where('tanggal_mulai!=',$tanggal_mulai)
								 ->where('tanggal_selesai!=',$tanggal_selesai)
								 ->get();
				
				if($data->num_rows()>0){
					$return["status"] = false;
					$return["error_info"] = "Telah ada Pemegang Operasional Harian (POH) untuk jabatan dan periode tersebut. ".$this->db->last_query();
				}
				else{
					$return["status"] = true;
				}
			}
		}
		return $return;
	}
	
	public function daftar_poh(){
		$data = $this->db->select("tanggal_mulai")
						 ->select("tanggal_selesai")
						 ->select("kode_unit")
						 ->select("nama_unit")
						 ->select("kode_jabatan")
						 ->select("nama_jabatan")
						 ->select("np_karyawan")
						 ->select("nama")
						 ->from($this->table)
						 ->order_by("tanggal_selesai desc")
						 ->order_by("mulai_selesai desc")
						 ->get()
						 ->result_array();
						 
		return $data;
	}
	
	/* public function tabel_poh(){
		$data = $this->db->select("*")
						 ->from($this->table)
						 ->get()
						 ->result_array();
						 
		return $data;
	} */
	
	private function _get_datatable_poh_query($display_poh){
		//dipindah kebawah karena tabel nya dinamis
		$column_search = array("kode_unit","nama_unit","kode_jabatan","nama_jabatan","np_definitif","nama_definitif","np_poh","nama_poh","nomor_nota_dinas","keterangan"); //set column field database for datatable_pamlek 
		
		$this->db->select("*");
		$this->db->from($this->table);
		
		if(strcmp($display_poh,"hari ini") == 0){
			$this->db->where("tanggal_mulai <= ","NOW()",false);
			$this->db->where("tanggal_selesai >= ","NOW()",false);
		}
		
		$i = 0;
	
		foreach ($column_search as $item) // loop column 
		{
			if($_POST['search']['value']) // if datatable_pamlek send POST for search
			{
				
				if($i===0) // first loop
				{
					$this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
					$this->db->like($item, $_POST['search']['value']);
				}
				else
				{
					$this->db->or_like($item, $_POST['search']['value']);
				}

				if(count($column_search) - 1 == $i) //last loop
					$this->db->group_end(); //close bracket
			}
			$i++;
		}
		
		if(isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order)){
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
		$this->db->order_by("tanggal_selesai", "desc");
		$this->db->order_by("tanggal_mulai", "desc");
	}

	public function get_datatable_poh($display_poh){
		$this->_get_datatable_poh_query($display_poh);
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}
	
	function count_filtered($display_poh){
		$this->_get_datatable_poh_query($display_poh);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all(){		
		$this->db->select("*");	
		$this->db->from($this->table);
		
		return $this->db->count_all_results();
	}
	
	public function data_poh($kode_jabatan,$tanggal_mulai,$tanggal_selesai){
		$data = $this->db->select("*")
						 ->from($this->table)
						 ->where("kode_jabatan",$kode_jabatan)
						 ->where("tanggal_mulai",$tanggal_mulai)
						 ->where("tanggal_selesai",$tanggal_selesai)
						 ->get()
						 ->result_array()[0];
		return $data;
	}
	
	public function tambah($data){
		$this->db->insert($this->table,$data);
	}
	
	public function ubah($set,$jabatan,$tanggal_mulai,$tanggal_selesai){
		$this->db->where('kode_jabatan',$jabatan)
				 ->where('tanggal_mulai',$tanggal_mulai)
				 ->where('tanggal_selesai',$tanggal_selesai)
				 ->update($this->table,$set);
	}
	
	public function karyawan_calon_poh($kelompok_jabatan,$np_pejabat_definitif,$sesuai_skep){//echo $sesuai_skep;
		$panjang_kode_kelompok_jabatan = -1*strlen($kelompok_jabatan);
		$this->db->distinct();
		$data = $this->db->select("a.no_pokok")
						 ->select("a.nama")
						 ->select("a.kode_unit")
						 ->select("a.nama_unit")
						 ->from($this->table_karyawan." a");
		if(strcmp($sesuai_skep,"sesuai")==0){
			$data = $this->db->join($this->table_mst_poh." b","(a.nama_pangkat=b.nama_sap AND id_pangkat_poh!=0) OR (substr(a.kode_jabatan,$panjang_kode_kelompok_jabatan)=b.kode_kelompok_jabatan_poh AND b.id_kelompok_jabatan_poh!=0)")
							 ->where("b.kode_kelompok_jabatan",$kelompok_jabatan);
		}
		else{
			$data = $this->db->where("substr(a.kode_jabatan,$panjang_kode_kelompok_jabatan)>=",$kelompok_jabatan);
		}
		$data = $this->db->where_not_in("a.no_pokok",array($np_pejabat_definitif))
						 ->order_by("a.kode_unit")
						 ->order_by("a.kode_jabatan")
						 ->order_by("a.no_pokok")
						 ->get()
						 ->result_array();//echo $this->db->last_query();
		return $data;
	}
		
	public function ambil_poh_by_data($tanggal_mulai,$tanggal_selesai,$kode_jabatan,$np_definitif,$np_poh){
		$data = $this->db->from($this->table)
						 ->where("tanggal_mulai",$tanggal_mulai)
						 ->where("tanggal_selesai",$tanggal_selesai)
						 ->where("kode_jabatan",$kode_jabatan)
						 ->where("np_definitif",$np_definitif)
						 ->where("np_poh",$np_poh)
						 ->get()
						 ->result_array()[0];
		return $data;
	}
	
	public function hapus_by_data($tanggal_mulai,$tanggal_selesai,$kode_jabatan,$np_definitif,$np_poh){
		$this->db->where("tanggal_mulai",$tanggal_mulai)
					->where("tanggal_selesai",$tanggal_selesai)
					->where("kode_jabatan",$kode_jabatan)
					->where("np_definitif",$np_definitif)
					->where("np_poh",$np_poh)
					->delete($this->table);
		return $this->db->affected_rows();
	}
}

/* End of file m_poh.php */
/* Location: ./application/models/osdm/m_poh.php */