<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_pendaftaran extends CI_Model {

	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
/*	
	function check_table_exist($name)
	{
		$name=str_replace("-","_",$name);
		$query = $this->db->query("show tables like '$name'")->row_array();
		
		return $query;
	}
	
	function create_table_cico($name)
	{	
		$name=str_replace("-","_",$name);
		$this->db->query("CREATE TABLE $name like ess_cico");
		
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
	
	function select_kehadiran_by_id($id,$tahun_bulan)
	{
		$tabel = "ess_cico_".$tahun_bulan;
		
		$this->db->select('*');
		$this->db->from($tabel);	
		$this->db->where('id',$id);
		
		$query = $this->db->get();
		
		return $query->row_array();
	}
	
	function insert_data_kehadiran($data_insert)
	{
		$tahun_bulan 		= $data_insert['tahun_bulan'];
		$tapping_fix_1		= $data_insert['tapping_fix_1'];
		$tapping_fix_2		= $data_insert['tapping_fix_2'];
		$np_karyawan		= $data_insert['np_karyawan'];
		$nama		 		= $data_insert['nama'];
		$personel_number 	= $data_insert['personel_number'];
		$nama_unit			= $data_insert['nama_unit'];
		$kode_unit 			= $data_insert['kode_unit'];
		$nama_jabatan	 	= $data_insert['nama_jabatan'];
		$dws_name			= $data_insert['dws_name'];
		$dws_tanggal		= $data_insert['dws_tanggal'];
		$dws_in				= $data_insert['dws_in'];
		$dws_out			= $data_insert['dws_out'];
		$dws_in_tanggal		= $data_insert['dws_in_tanggal'];
		$dws_out_tanggal	= $data_insert['dws_out_tanggal'];
		$dws_break_start	= $data_insert['dws_break_start'];
		$dws_break_end		= $data_insert['dws_break_end'];		
				
				
				
				
		$tabel = "ess_cico_".$tahun_bulan;
		
		if(!$this->check_table_exist($tabel))
		{
			$this->create_table_cico($tabel);
		}		
				
				
				
		$data = array(               
                'np_karyawan' 			=> $np_karyawan,
				'nama' 					=> $nama,				
				'personel_number' 		=> $personel_number,
				'nama_unit'				=> $nama_unit,
				'kode_unit' 			=> $kode_unit,
				'nama_jabatan' 			=> $nama_jabatan,			
				'dws_name' 				=> $dws_name,
				'dws_tanggal' 			=> $dws_tanggal,
				'tapping_fix_1' 		=> $tapping_fix_1,				
				'tapping_fix_2' 		=> $tapping_fix_2,
				'dws_in'				=> $dws_in,
				'dws_out'				=> $dws_out,
				'dws_in_tanggal'		=> $dws_in_tanggal,
				'dws_out_tanggal'		=> $dws_out_tanggal,
				'dws_break_start'		=> $dws_break_start,
				'dws_break_end'			=> $dws_break_end,
				'created_at'			=> date('Y-m-d H:i:s'),
				'created_by'			=> $this->session->userdata('no_pokok')
            );
			
		$this->db->insert($tabel, $data); 
		
		
		if($this->db->affected_rows() > 0)
		{			
			return $this->db->insert_id();
		}else
		{
			return '0';
		}
		
	}
	
	function update_data_kehadiran($data_update)
	{
		$id 						= $data_update['id'];
		$tahun_bulan 				= $data_update['tahun_bulan'];
		$tapping_1	 				= $data_update['tapping_1'];
		$tapping_2	 				= $data_update['tapping_2'];
		$np_karyawan 				= $data_update['np_karyawan'];
		$nama		 				= $data_update['nama'];
		$dws_tanggal 				= $data_update['dws_tanggal'];
				
		$tapping_fix_approval_status		= $data_update['tapping_fix_approval_status'];
		$tapping_fix_approval_ket 	= $data_update['tapping_fix_approval_ket'];
		$tapping_fix_approval_np			= $data_update['tapping_fix_approval_np'];
		$tapping_fix_approval_nama			= $data_update['tapping_fix_approval_nama'];
		$tapping_fix_approval_nama_jabatan	= $data_update['tapping_fix_approval_nama_jabatan'];
		$tapping_fix_approval_date 			= $data_update['tapping_fix_approval_date'];
				
		$tabel = "ess_cico_".$tahun_bulan;
		
		
		$data = array(               
				'tapping_fix_1_temp' 				=> $tapping_1, //masukan ke temp dulu sebelum di approve
				'tapping_fix_2_temp' 				=> $tapping_2, //masukan ke temp dulu sebelum di approve
				'tapping_fix_approval_status' 		=> $tapping_fix_approval_status,
				'tapping_fix_approval_ket' 			=> $tapping_fix_approval_ket,
				'tapping_fix_approval_np' 			=> $tapping_fix_approval_np,
				'tapping_fix_approval_nama' 		=> $tapping_fix_approval_nama,
				'tapping_fix_approval_nama_jabatan' => $tapping_fix_approval_nama_jabatan,
				'tapping_fix_approval_date'			=> $tapping_fix_approval_date,				
				'updated_at'	=> date('Y-m-d H:i:s'),
				'updated_by'	=> $this->session->userdata('no_pokok')
            );
	
		$this->db->where('id',$id);	
		$this->db->update($tabel, $data); 
		
		
		if($this->db->affected_rows() > 0)
		{			
			return $np_karyawan." | ".$nama.", Pada jadwal kerja tanggal ".$dws_tanggal; 
		}else
		{
			return '0';
		}
		
	}
	
	function update_kode_unit($data_update)
	{
		$id 					= $data_update['id'];
		$np_karyawan 			= $data_update['np_karyawan'];
		$nama 					= $data_update['nama'];
		$dws_tanggal 			= $data_update['dws_tanggal'];
		$kode_unit 				= $data_update['kode_unit'];
		$tahun_bulan 			= $data_update['tahun_bulan'];
		
						
		$tabel = "ess_cico_".$tahun_bulan;
		
		
		$ambil_kd = $this->db->query("SELECT * FROM mst_satuan_kerja WHERE kode_unit='$kode_unit'")->row_array();
		$nama_unit = $ambil_kd['nama_unit'];
	
		$data = array(               
				'kode_unit' 	=> $kode_unit, 
				'nama_unit' 	=> $nama_unit, 					
				'updated_at'	=> date('Y-m-d H:i:s'),
				'updated_by'	=> $this->session->userdata('no_pokok')
            );
	
		$this->db->where('id',$id);	
		$this->db->update($tabel, $data); 
		
		
		if($this->db->affected_rows() > 0)
		{			
			return $np_karyawan." | ".$nama.", Pada jadwal kerja tanggal ".$dws_tanggal; 
		}else
		{
			return '0';
		}
		
	}
	
	
	function select_mst_karyawan_aktif()
	{
		$this->db->select('*');
		$this->db->from('mst_jadwal_kerja');
		$this->db->where('status','1');
		
		$data = $this->db->get();
		
		return $data;
	}
	
	function select_mst_jadwal_kerja_by_id($id)
	{
			
		$this->db->select('*');
		$this->db->from('mst_jadwal_kerja');	
		$this->db->where('id',$id);
		
		$query = $this->db->get();
		
		return $query->row_array();
	}
	
	function check_cico_exist($data)
	{
		$np_karyawan = $data['np_karyawan'];
		$dws_tanggal = $data['dws_tanggal'];
		$tahun_bulan = $data['tahun_bulan'];
			
		$tabel = "ess_cico_".$tahun_bulan;
		
		if(!$this->check_table_exist($tabel))
		{
			$tabel = 'ess_cico';
		}

		
		$this->db->where('np_karyawan',$np_karyawan);
		$this->db->where('dws_tanggal',$dws_tanggal);
		
		$this->db->from($tabel);
		
		return $this->db->count_all_results();

	}
	
	function select_daftar_karyawan()
	{
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
			
		}else
		if($_SESSION["grup"]==5) //jika Pengguna
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
	}
	
	function select_daftar_unit()
	{
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
			
		}else
		if($_SESSION["grup"]==5) //jika Pengguna
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
		
		$this->db->group_by('kode_unit'); 

		
		$data = $this->db->get();
		
		return $data;
	}
	
	function select_cuti_by_id($id)
	{
		$this->db->select('a.*');
		$this->db->select('b.uraian');
		$this->db->from('ess_cuti a');	
		$this->db->join('mst_cuti b', 'a.absence_type = b.kode_erp', 'left');
		$this->db->where('a.id',$id);
		
		$query = $this->db->get();
		
		return $query->row_array();
	}
	
	*/
}

/* End of file M_data_kehadiran.php */
/* Location: ./application/models/kehadiran/M_data_kehadiran.php */