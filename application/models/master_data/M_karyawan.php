<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_karyawan extends CI_Model {

	private $table="mst_karyawan";
	private $table_pengadministrasi="usr_pengadministrasi";
	private $table_pengguna="usr_pengguna";
	private $table_grup="usr_pengguna_grup_pengguna";
	private $table_satker="mst_satuan_kerja";
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	function check_table_exist($name)
	{
		$name=str_replace("-","_",$name);
		$query = $this->db->query("show tables like '$name'")->row_array();
		
		return $query;
	}
	
	public function daftar_karyawan(){
		$data = $this->db->from($this->table." k")
						 ->order_by("k.no_pokok")
						 ->get()
						 ->result_array();
		return $data;
	}
	
	//belum selese bowo, 4 juni 2020
	/*
	public function daftar_karyawan_dua_bulan(){
		
		$date_now		= $tanggal;
		$date_kemarin1 	= date('Y-m-d', strtotime($tanggal . ' -1 months'));
		$date_kemarin2 	= date('Y-m-d', strtotime($tanggal . ' -2 monts'));
		
		$pisah_date_now		= explode('-',$date_now);
		$tahun				= $pisah_date_now[0];
		$bulan				= $pisah_date_now[1];
		$tahun_bulan		= $tahun1."_".$bulan1;
		
		$pisah_date_kemarin1	= explode('-',$date_kemarin1);
		$tahun1					= $pisah_date_kemarin1[0];
		$bulan1					= $pisah_date_kemarin1[1];
		$tahun_bulan_kemarin1	= $tahun1."_".$bulan1;
		
		$pisah_date_kemarin2	= explode('-',$date_kemarin2);
		$tahun2					= $pisah_date_kemarin2[0];
		$bulan2					= $pisah_date_kemarin2[1];
		$tahun_bulan_kemarin2	= $tahun2."_".$bulan2;
		
		$tabel_cico = 'ess_cico_'.$tahun_bulan;
		if(!$this->check_table_exist($tabel_cico))
		{
			$tabel_cico = 'ess_cico';
		}
		
		$tabel_cico1 = 'ess_cico_'.$tahun_bulan_kemarin1;
		if(!$this->check_table_exist($tabel_cico1))
		{
			$tabel_cico1 = 'ess_cico';
		}
		
		$tabel_cico2 = 'ess_cico_'.$tahun_bulan_kemarin2;
		if(!$this->check_table_exist($tabel_cico2))
		{
			$tabel_cico2 = 'ess_cico';
		}
		
		$data = $this->db->query("SELECT d.no_pokok, d.tapping_time, d.in_out,d.machine_id, e.nama FROM 
									(SELECT a.no_pokok, a.tapping_time, a.in_out,a.machine_id, a.tapping_type FROM $tabel_pamlek a $where
									union all
									SELECT b.no_pokok, b.tapping_time, b.in_out,b.machine_id, b.tapping_type FROM $tabel_pamlek_kemarin b $where_kemarin
									union all
									SELECT c.no_pokok, c.tapping_time, c.in_out,c.machine_id, c.tapping_type FROM $tabel_pamlek_besok c $where_besok) d
									left join mst_perizinan e ON d.tapping_type=e.kode_pamlek AND e.status='1' ORDER BY d.tapping_time ASC");
		
		
		
		
		
	
		
		$data = $this->db->from($this->table." k")
						 ->order_by("k.no_pokok")
						 ->get()
						 ->result_array();
		return $data;
	}
	*/
	
	public function daftar_karyawan_diadministrasikan(){
		$this->db->select("a.no_pokok");
		$this->db->select("a.nama");
		$this->db->select("a.kode_unit");
		$this->db->select("a.kode_jabatan");
		$this->db->from($this->table." a");
		$this->db->join($this->table_pengadministrasi." b","a.kode_unit=b.kode_unit");
		$this->db->join($this->table_pengguna." c","c.id=b.id_pengguna");
		$this->db->where("c.id",$this->session->userdata("id_pengguna"));
		$this->db->get();
		$subquery1 = $this->db->last_query();
		
		$this->db->select("a.no_pokok");
		$this->db->select("a.nama");
		$this->db->select("a.kode_unit");
		$this->db->select("a.kode_jabatan");
		$this->db->from($this->table." a");
		$this->db->where("a.no_pokok",$this->session->userdata("no_pokok"));
		$this->db->get();
		$subquery2 = $this->db->last_query();
		
		$this->db->from("($subquery1 UNION $subquery2) a");
		$this->db->order_by("kode_unit");
		$this->db->order_by("kode_jabatan");
		$data = $this->db->get()->result_array();//echo $this->db->last_query();
		return $data;
	}
	
	public function daftar_kontrak_kerja(){
		$data = $this->db->distinct()
						 ->select("kontrak_kerja")
						 ->from($this->table." k")
						 ->where("kontrak_kerja !=","''",false)
						 ->order_by("kontrak_kerja")
						 ->get()
						 ->result_array();
		return $data;
	}
	
	public function get_atasan($kode_unit){
		$data = $this->db->select("no_pokok")
						 ->from("mst_karyawan a")
						 ->where("a.kode_unit_poh",$kode_unit)
						 ->like("a.kode_jabatan_poh","00","before")
						 ->get();
		//echo $this->db->last_query();
		$atasan["np"] = "";
		$atasan["is_poh"] = false;
		
		if($data->num_rows()==1){
			$atasan["np"] = $data->result_array()[0]["no_pokok"];
			$atasan["is_poh"] = true;
		}
		else if($data->num_rows()==0){
			$data = $this->db->select("no_pokok")
							 ->from("mst_karyawan a")
							 ->where("a.kode_unit",$kode_unit)
							 ->like("a.kode_jabatan","00","before")
							 ->get();
			//echo $this->db->last_query();
			
			if($data->num_rows()==1){
				$atasan["np"] = $data->result_array()[0]["no_pokok"];
				$atasan["is_poh"] = false;
			}
		}
		return $atasan;
	}
	
	public function get_karyawan($np){
		$data = $this->db->select("*")
					 ->from("mst_karyawan a")
					 ->where("no_pokok",$np)
					 ->get()
					 ->result_array();//echo $this->db->last_query();
		if(!empty($data)){
			$data = $data[0];
		}
		return $data;
	}
	
	function get_detail_np($np) 
	{
		$date_now			= date('Y-m-d');
		$date_kemarin 		= date('Y-m-d', strtotime($date_now . ' -1 months'));
		
		$pisah_date_now		= explode('-',$date_now);
		$tahun1				= $pisah_date_now[0];
		$bulan1				= $pisah_date_now[1];
		$tahun_bulan		= $tahun1."_".$bulan1;
		
		$pisah_date_kemarin	= explode('-',$date_kemarin);
		$tahun2				= $pisah_date_kemarin[0];
		$bulan2				= $pisah_date_kemarin[1];
		$tahun_bulan_kemarin= $tahun2."_".$bulan2;
		
		$tabel_master = 'erp_master_data_'.$tahun_bulan;
		if(!$this->check_table_exist($tabel_master))
		{
			$tabel_master = 'erp_master_data';
		}
		
		$tabel_master_kemarin = 'erp_master_data_'.$tahun_bulan_kemarin;
		if(!$this->check_table_exist($tabel_master_kemarin))
		{
			$tabel_master_kemarin = 'erp_master_data';
		}
		
		$data = $this->db->query("SELECT c.np_karyawan as no_pokok, c.nama, c.nama_jabatan, c.nama_unit, c.personnel_number, c.kode_unit, c.grup_jabatan, c.kode_jabatan FROM  
									(SELECT  a.np_karyawan, a.nama, a.nama_jabatan, a.nama_unit, a.personnel_number, a.kode_unit, a.grup_jabatan, a.kode_jabatan FROM $tabel_master a 
									GROUP BY a.np_karyawan
									union all
									SELECT b.np_karyawan, b.nama, b.nama_jabatan, b.nama_unit, b.personnel_number, b.kode_unit, b.grup_jabatan, b.kode_jabatan FROM $tabel_master_kemarin b 
									GROUP BY b.np_karyawan) c where c.np_karyawan='".$np."' group by c.np_karyawan")->result_array();
		if(!empty($data)){
			$data = $data[0];
		}
		
		return $data;
    }
	
	public function get_posisi_karyawan($np){
		$data = $this->db->select("no_pokok")
						 ->select("nama")
						 ->select("nama_unit")
						 ->select("nama_jabatan")
						 ->select("REGEXP_REPLACE(a.kode_unit,'0+$','') kode_unit",false)
						 ->select("CASE WHEN SUBSTR(kode_jabatan,-2) = '00' THEN 'kepala' ELSE 'staf' END jabatan",false)
						 ->select("CASE WHEN LENGTH(REGEXP_REPLACE(a.kode_unit,'0+$','')) = 5 THEN 'unit' WHEN LENGTH(REGEXP_REPLACE(a.kode_unit,'0+$','')) = 4 THEN 'seksi' WHEN LENGTH(REGEXP_REPLACE(a.kode_unit,'0+$','')) = 3 THEN 'departemen' WHEN LENGTH(REGEXP_REPLACE(a.kode_unit,'0+$','')) = 2 THEN 'divisi' WHEN LENGTH(REGEXP_REPLACE(a.kode_unit,'0+$','')) = 1 THEN 'direktorat' END posisi",false)
						 ->from("mst_karyawan a")
						 ->where("no_pokok",$np)
						 ->get()
						 ->result_array()[0];//echo $this->db->last_query();
		return $data;
	}
	
	public function get_posisi_karyawan_periode($np,$periode){
		$data = $this->db->select("np_karyawan no_pokok")
						 ->select("nama")
						 ->select("nama_unit")
						 ->select("nama_jabatan")
						 ->select("nama_unit")
						 ->select("REGEXP_REPLACE(a.kode_unit,'0+$','') kode_unit",false)
						 ->select("CASE SUBSTR(kode_jabatan,-2) WHEN '00' THEN 'kepala' ELSE 'staf' END jabatan",false)
						 ->select("CASE LENGTH(REGEXP_REPLACE(a.kode_unit,'0+$','')) WHEN 5 THEN 'unit' WHEN 4 THEN 'seksi' WHEN 3 THEN 'departemen' when '2' THEN 'divisi' WHEN '1' THEN 'direktorat' END posisi",false)
						 ->from("erp_master_data_".$periode." a")
						 ->where("np_karyawan",$np)
						 ->get()
						 ->result_array()[0];
		return $data;
	}
	
	//20-01-2020, 7648 tambah fungsi untuk menentukan data karyawan pada tanggal tertentu
	public function get_posisi_karyawan_periode_tanggal($np,$periode,$tanggal){
		$data = $this->db->select("np_karyawan no_pokok")
						 ->select("nama")
						 ->select("nama_unit")
						 ->select("nama_jabatan")
						 ->select("nama_unit")
						 ->select("REGEXP_REPLACE(a.kode_unit,'0+$','') kode_unit",false)
						 ->select("CASE SUBSTR(kode_jabatan,-2) WHEN '00' THEN 'kepala' ELSE 'staf' END jabatan",false)
						 ->select("CASE LENGTH(REGEXP_REPLACE(a.kode_unit,'0+$','')) WHEN 5 THEN 'unit' WHEN 4 THEN 'seksi' WHEN 3 THEN 'departemen' when '2' THEN 'divisi' WHEN '1' THEN 'direktorat' END posisi",false)
						 ->from("erp_master_data_".$periode." a")
						 ->where("np_karyawan",$np)
						 ->where("tanggal_dws",$tanggal)
						 ->get()
						 ->result_array()[0];
		return $data;
	}
	
	public function get_karyawan_unit_kerja($kode_unit){
		$induk = rtrim($kode_unit,0);
		$data = $this->db->select("no_pokok")
						 ->select("nama")
						 ->from("mst_karyawan")
						 ->like("kode_unit",$induk,"after")
						 ->order_by("kode_unit")
						 ->order_by("grade_pangkat","DESC")
						 ->get()
						 ->result_array();
		return $data;
	}
	
	public function get_karyawan_beberapa_unit_kerja($arr_kode_unit){
		$data = $this->db->select("no_pokok")
						 ->select("nama")
						 ->from("mst_karyawan")
						 ->where_in("kode_unit",$arr_kode_unit)
						 ->order_by("grade_pangkat","DESC")
						 ->get()
						 ->result_array();
		return $data;
	}
	
	public function filter_karyawan($arr_kontrak_kerja,$arr_kode_unit){
		$data = $this->db->select("no_pokok")
						 ->select("nama")
						 ->from("mst_karyawan")
						 ->where_in("kontrak_kerja",$arr_kontrak_kerja)
						 ->where_in("kode_unit",$arr_kode_unit)
						 ->order_by("kode_unit","ASC")
						 ->order_by("kode_jabatan","ASC")
						 ->order_by("grade_pangkat","DESC")
						 ->order_by("no_pokok","ASC")
						 ->get()
						 ->result_array();//echo $this->db->last_query();
		return $data;
	}
}

/* End of file m_karyawan.php */
/* Location: ./application/models/master_data/m_karyawan.php */