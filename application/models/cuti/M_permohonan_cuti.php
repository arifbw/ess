<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_permohonan_cuti extends CI_Model {

	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
    
	function check_double_cuti($np_karyawan,$start_date,$end_date)
	{
		
		$query = $this->db->query("SELECT * 
									FROM 
										ess_cuti 
									WHERE 
										np_karyawan='$np_karyawan' AND 
										(
											(status_1!=2 AND status_1!=3) OR 
											(status_2!=2 AND status_2!=3)
										) AND
										(
											(start_date<='$start_date' AND end_date>='$start_date') OR 
											(start_date<='$end_date' AND end_date>='$end_date')
										)AND
										((status_1=0 OR status_1=1) AND (status_2=0 OR status_2=1)) /*yang tidak ditolak/dibatalkan*/
										AND
										(approval_sdm=0 OR approval_sdm=1) /*yang tidak ditolak sdm*/
										")->row_array();
		
		
		return $query;
			
	}
	
	//06 01 2021, 7648 - Tri Wibowo, Agar bisa backdate input cuti ketika NP nya berubah
	function check_table_exist($name)
	{
		$name=str_replace("-","_",$name);
		$query = $this->db->query("show tables like '$name'")->row_array();
		
		return $query;
	}
	
	//06 01 2021, 7648 - Tri Wibowo, Agar bisa backdate input cuti ketika NP nya berubah
    function select_daftar_karyawan()
	{
		/*
		if($_SESSION["grup"]==4) //jika Pengadministrasi Unit Kerja
		{			
			$ada_data=0;
			$var=array();
			$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
			foreach ($list_pengadministrasi as $data) //looping list_pengadministrasi
			{	
				array_push($var,$data['kode_unit']);
				$ada_data=1;
			}
			if($ada_data==0)
			{
				$var='';
			}
			
		}
		else if($_SESSION["grup"]==5) //jika Pengguna
		{
			$var 	= $_SESSION["no_pokok"];
			
		}else
		{
			$var = '';				
		}	
			
		$this->db->select('*');
		$this->db->from('mst_karyawan');
		
		if($_SESSION["grup"]==4) //jika Pengadministrasi Unit Kerja
		{
			$this->db->where_in('mst_karyawan.kode_unit', $var);								
		}else
		if($_SESSION["grup"]==5) //jika Pengguna
		{
			$this->db->where_in('mst_karyawan.no_pokok', $var);	
		}else
		{
		}			
		
		$data = $this->db->get();
		
		return $data;
		*/
		
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
					
		//jika Pengadministrasi Unit Kerja
		if($_SESSION["grup"]==4) {
			$ada_data=0;
			$var=array();
			$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
			//looping list_pengadministrasi
			foreach ($list_pengadministrasi as $data) {
				array_push($var,$data['kode_unit']);
				$ada_data=1;
			}
			$var = "'".implode("','",$var)."'";
			if($ada_data==0)
				$var='';
		}
		else if($_SESSION["grup"]==5) //jika Pengguna
			$var = $_SESSION["no_pokok"];
		else
			$var = 1;
		
		$where='';
		if($_SESSION["grup"]==4) //jika Pengadministrasi Unit Kerja
		{
			$where = "kode_unit IN ($var)";
		}
		else if($_SESSION["grup"]==5) //jika Pengguna
		{
			$where = "np_karyawan IN ('$var')";
		}
		
		if($where!='')
		{
			$where = "WHERE ".$where;
		}
		
		$data = $this->db->query("SELECT c.np_karyawan as no_pokok, c.nama FROM 
									(SELECT a.np_karyawan, a.nama FROM $tabel_master a $where
									GROUP BY a.np_karyawan
									union all
									SELECT b.np_karyawan, b.nama FROM $tabel_master_kemarin b $where
									GROUP BY b.np_karyawan) c where (c.np_karyawan!='' OR c.np_karyawan is not null) group by c.np_karyawan");
		
		return $data;
		
		
		
		
	}
	
	function select_absence_quota_by_np($np)
	{
		$this->db->select('*');
		$this->db->from('erp_absence_quota');	
		$this->db->where('np_karyawan',$np);
		
		$data = $this->db->get();
		
		return $data;
	}
	
	function select_absence_quota_by_array_of_np($array_of_np)
	{
		if($array_of_np!=[]){
			$this->db->select('np_karyawan, start_date, deduction_from, deduction_to, number, deduction');
			$this->db->from('erp_absence_quota');	
			$this->db->where_in('np_karyawan',$array_of_np);
			$data = $this->db->get()->result();
			return $data;
		} else return [];
	}
	
	function select_cubes_by_np($np)
	{
		$this->db->select('*');
		$this->db->from('cuti_cubes_jatah');	
		$this->db->where('np_karyawan',$np);
		$this->db->where('tanggal_kadaluarsa >=', date("Y-m-d"));
		
		$data = $this->db->get();
		
		return $data;
	}
	
	function select_hutang_by_np($np){
		$this->db->select('*');
		$this->db->from('cuti_hutang');	
		$this->db->where('no_pokok',$np);

		$data = $this->db->get();
		return $data;
	}
	
	function select_mst_cuti()
	{
		$this->db->select('*');
		$this->db->from('mst_cuti');
		
		$data = $this->db->get();
		
		return $data;
	}
	
	function select_np_by_kode_unit($list_kode_unit)
	{
		$this->db->select('*');
		$this->db->from('mst_karyawan');
		$this->db->where_in('kode_unit', $list_kode_unit);
		$this->db->order_by('no_pokok','ASC');
		$data = $this->db->get();
		
		return $data;
	}
	
	function insert_cuti($data)
	{
		$data = array(
				'np_karyawan'		=> $data['np_karyawan'],								
				'personel_number'	=> $data['personel_number'],
				'nama'				=> $data['nama'],	
				'kode_unit'			=> $data['kode_unit'],				
				'nama_unit'			=> $data['nama_unit'],	
				'nama_jabatan'		=> $data['nama_jabatan'],				
				'absence_type'		=> $data['absence_type'],
				'start_date'		=> $data['start_date'],
				'end_date'			=> $data['end_date'],
				'jumlah_bulan'		=> $data['jumlah_bulan'],
				'jumlah_hari'		=> $data['jumlah_hari'],
				'alasan'			=> $data['alasan'],
				'keterangan'		=> $data['keterangan'],
				'approval_1'		=> $data['approval_1'],
				'approval_1_jabatan'=> $data['approval_1_jabatan'],
				'approval_2'		=> $data['approval_2'],					
				'approval_2_jabatan'=> $data['approval_2_jabatan'],					
				'ambil_cuti_dari'   => @$data['ambil_cuti_dari'],					
				'is_cuti_bersama'   => $data['is_cuti_bersama'],					
				'mst_cuti_id'   => $data['mst_cuti_id'],					
				'created_at'		=> date('Y-m-d H:i:s'),
				'created_by'		=> $this->session->userdata('no_pokok')
			);
		
		$this->db->insert('ess_cuti', $data); 

		if($this->db->affected_rows() > 0)
		{			
			return $this->db->insert_id(); 
		}else
		{
			return "0";
		}
	}
	/*
	function check_cuti_tahunan($np_karyawan)
	{		
		$absence_quota_type='91';
		
		$this->db->select('sum(number)');
		$this->db->select('sum(deduction)');
		$this->db->from('erp_absence_quota');
		$this->db->where('np_karyawan', $np_karyawan);
		$this->db->where('absence_quota_type', $absence_quota_type);
		$this->db->where('deduction_from<=', date('yyyy-dd-mm'));
		$this->db->where('deduction_to<=', date('yyyy-dd-mm'));
	
		$this->db->order_by("start_date", "ASC");	
		$query = $this->db->get();
		
		return $query->row_array();
	}
	*/
	
	function select_cuti_by_id($id)
	{
		$this->db->select('*');
		$this->db->from('ess_cuti');	
		$this->db->where('id',$id);
		
		$query = $this->db->get();
		
		return $query->row_array();
	}
	
	function batal_cuti($id)
	{
		$data = array(				
				'status_1'			=> '3',
				'status_2'			=> '3',
				'approval_1_date'	=> date('Y-m-d H:i:s'),
				'approval_2_date'	=> date('Y-m-d H:i:s'),
				'updated_at'		=> date('Y-m-d H:i:s'),
				'updated_by'		=> $this->session->userdata('no_pokok')
        );
		$this->db->where('id', $id);
		$this->db->update('ess_cuti', $data); 
		
		if($this->db->affected_rows() > 0)
		{			
			return "1"; 
		}else
		{
			return "0";
		}
	}

	function get_absence($id)
	{
		$this->db->where('kode_erp', $id);
		$tipe = $this->db->get('mst_cuti')->row(); 
		
		return $tipe->uraian;
	}
}

/* End of file m_perencanaan_jadwal_kerja.php */
/* Location: ./application/models/kehadiran/m_perencanaan_jadwal_kerja.php */