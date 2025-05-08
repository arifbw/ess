<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_perencanaan_jadwal_kerja extends CI_Model {

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
	
	function getDataCetak_nama($get)
	{
		$tgl_awal = $get['tgl_awal'];
		$tgl_akhir = $get['tgl_akhir'];
		$np = implode("','", $get['np']);
		$this->db->where("np_karyawan in ('".$np."')");
		$this->db->where("date BETWEEN '".$tgl_awal."' AND '".$tgl_akhir."'");
		$this->db->group_by('nama');
		$this->db->order_by('nama', 'asc');
		$data = $this->db->get('ess_substitution');
		//var_dump($data->result());exit;
		return $data->result();
	}
	
	function getDataCetak($get)
	{
		$tgl_awal = $get['tgl_awal'];
		$tgl_akhir = $get['tgl_akhir'];
		$np = implode("','", $get['np']);
		$this->db->where("np_karyawan in ('".$np."')");
		$this->db->where("date BETWEEN '".$tgl_awal."' AND '".$tgl_akhir."'");
		$this->db->order_by('nama', 'asc');
		$this->db->order_by('date', 'asc');
		$data = $this->db->get('ess_substitution');
		
		return $data->result();
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
	
	function select_substitution_by_id($id)
	{
		$tabel = "ess_substitution";
		
		$this->db->select('*');
		$this->db->from($tabel);	
		$this->db->where('id',$id);
		
		$query = $this->db->get();
		
		return $query->row_array();
	}
	
	//check apakah ada data substitution
	function data_substitution($np_karyawan,$date)
	{
		$this->db->select('*');
		$this->db->from('ess_substitution');
		
		$this->db->where('deleted','0');
		$this->db->where('np_karyawan',$np_karyawan);
		$this->db->where('date',$date);
		
		$data = $this->db->get();
		
		return $data;
	}
	
	//untuk update cico di id_substitution
	function update_cico_substitution($np_karyawan,$date)
	{
		$bulan = substr($date,5,2);
		$tahun = substr($date,0,4);
		
		$tabel_cico = "ess_cico_".$tahun."_".$bulan;
		
		if(!$this->check_table_exist($tabel_cico))
		{
			$tabel_cico = 'ess_cico';
		}
				
		
		$set = $this->db->from($tabel_cico)
	 			->where('dws_tanggal', $date)
	 			->where('np_karyawan', $np_karyawan)
	 			->get();
		$get_all = $this->db->from('ess_substitution')
	 			->where('date', $date)
	 			->where('np_karyawan', $np_karyawan)
				->where('deleted!=', '1')
	 			->get()->result_array();

	 	if ($set->num_rows() == 1) {
	 		$cico = $set->row_array();
	 		$id_substitution = implode(",", array_column($get_all, 'id'));;
	 		$this->db->set('id_substitution', $id_substitution)->where('id', $cico['id'])->update($tabel_cico);
	 	}
		
		if($this->db->affected_rows() > 0)
			return true; 
		else
			return false;
			
	}
	
	//delete field cico di id_cuti
	function delete_cico_substitution($np_karyawan,$date,$delete)
	{
		$bulan = substr($date,5,2);
		$tahun = substr($date,0,4);
		
		$tabel_cico = "ess_cico_".$tahun."_".$bulan;
		
		if(!$this->check_table_exist($tabel_cico))
		{
			$tabel_cico = 'ess_cico';
		}
		
		$ambil_cico = $this->db->query("SELECT id_substitution FROM $tabel_cico WHERE np_karyawan='$np_karyawan' AND dws_tanggal='$date'")->row_array();
		$ambil = $ambil_cico['id_substitution'];
		
		$substitution=explode(",",$ambil);
		
		$data_sama='0';
		$array = array();
		foreach($substitution as $val)
		{
			
			array_push($array,$val);
			
			
			if($val==$delete)
			{
				$data_sama='1';
			}
			
		}
		
		if($data_sama=='1')
		{
			if (($key = array_search($delete, $array)) !== false) {
					unset($array[$key]);
				}
		}
		
		//hapus data kosong
		if (($key = array_search('', $array)) !== false) {
			unset($array[$key]);
		}
		
		$id_subtitution=implode(',',$array);
		
		$data_update = array(               
                'id_substitution' => $id_subtitution,
				);
		
		$this->db->where('np_karyawan',$np_karyawan);
		$this->db->where('dws_tanggal',$date);		
		
		$this->db->update($tabel_cico, $data_update); 
		
	}
	
	
	function insert_perencanaan_jadwal_kerja($data_insert)
	{
		$np_karyawan 		= $data_insert['np_karyawan'];
		$personel_number 	= $data_insert['personel_number'];
		$nama 				= $data_insert['nama'];
		$nama_jabatan 		= $data_insert['nama_jabatan'];
		$kode_unit 			= $data_insert['kode_unit'];
		$nama_unit 			= $data_insert['nama_unit'];
		$date				= $data_insert['date'];
		$dws				= $data_insert['dws'];
		$dws_variant		= $data_insert['dws_variant'];
		$transaction_type	= $data_insert['transaction_type'];
						
		$tabel = "ess_substitution";
				
		$data = array(               
                'np_karyawan' 			=> $np_karyawan,
				 'personel_number' 		=> $personel_number,
				'nama' 					=> $nama,
				'nama_jabatan' 			=> $nama_jabatan,
				'kode_unit' 			=> $kode_unit,
				'nama_unit' 			=> $nama_unit,
				'date' 					=> $date,
				'dws' 					=> $dws,
				'dws_variant' 			=> $dws_variant,
				'transaction_type' 		=> $transaction_type,				
				'created_at'			=> date('Y-m-d H:i:s'),
				'created_by'			=> $this->session->userdata('no_pokok')
            );
			
		$this->db->insert($tabel, $data); 
		
		$insert_id =  $this->db->insert_id();
		
		if($this->db->affected_rows() > 0)
		{			
			$this->update_cico_substitution($np_karyawan,$date,$insert_id);
			
			return $insert_id;
		}else
		{
			return '0';
		}
		
		
	}
	
	function batal_perencanaan_jadwal_kerja($data_batal)
	{
		$id 			= $data_batal['id'];	
		$np_karyawan 	= $data_batal['np_karyawan'];
		$date 			= $data_batal['date'];
				
		$tabel = "ess_substitution";
		
		
		$data = array(               
				'deleted' => '1',				
				'updated_at'	=> date('Y-m-d H:i:s'),
				'updated_by'	=> $this->session->userdata('no_pokok')
            );
	
		$this->db->where('id',$id);	
		$this->db->update($tabel, $data); 
		
		
		if($this->db->affected_rows() > 0)
		{			
			$this->delete_cico_substitution($np_karyawan,$date,$id);
			
			return '1'; 
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
	
	
	function check_substitution_exist($data)
	{
		$np_karyawan = $data['np_karyawan'];
		$date = $data['date'];
					
		$tabel = "ess_substitution";				
				
		$this->db->where('np_karyawan',$np_karyawan);
		$this->db->where('date',$date);
		$this->db->where('deleted','0');
		
		$this->db->from($tabel);
		
		return $this->db->count_all_results();

	}
	
	function select_daftar_karyawan_bk()
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

	function select_daftar_karyawan() 
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
	
	
}

/* End of file M_perencanaan_jadwal_kerja.php */
/* Location: ./application/models/kehadiran/M_perencanaan_jadwal_kerja.php */