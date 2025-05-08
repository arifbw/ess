<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_pembatalan_cuti extends CI_Model {

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
/*	
	function getDataCetak_nama($tgl_awal, $tgl_akhir)
	{
		if($_SESSION["grup"]==4) {
			$ada_data=0;
			$var=array();
			$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
			//looping list_pengadministrasi
			foreach ($list_pengadministrasi as $data) {
				array_push($var,$data['kode_unit']);
				$ada_data=1;
			}
			if($ada_data==0)
				$var='';
		}
		else if($_SESSION["grup"]==5) //jika Pengguna
			$var = $_SESSION["no_pokok"];
		else
			$var = 1;

		//echo $var[0];exit;
		if($_SESSION["grup"]==4) //jika Pengadministrasi Unit Kerja
			$this->db->where('kode_unit', $var[0]);
		else if($_SESSION["grup"]==5) //jika Pengguna
			$this->db->where('np_karyawan', $var);
		else
			$this->db->where("np_karyawan='7648'");

		$this->db->where("date BETWEEN '$tgl_awal' AND '$tgl_akhir'");
		$this->db->group_by('nama');
		$this->db->order_by('nama', 'asc');
		$data = $this->db->get('ess_substitution');
		
		return $data->result();
	}
*/
/*	
	function getDataCetak($tgl_awal, $tgl_akhir)
	{
		if($_SESSION["grup"]==4) {
			$ada_data=0;
			$var=array();
			$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
			//looping list_pengadministrasi
			foreach ($list_pengadministrasi as $data) {
				array_push($var,$data['kode_unit']);
				$ada_data=1;
			}
			if($ada_data==0)
				$var='';
		}
		else if($_SESSION["grup"]==5) //jika Pengguna
			$var = $_SESSION["no_pokok"];
		else
			$var = 1;

		if($_SESSION["grup"]==4) //jika Pengadministrasi Unit Kerja
			$this->db->where('kode_unit', $var[0]);
		else if($_SESSION["grup"]==5) //jika Pengguna
			$this->db->where('np_karyawan', $var[0]);
		else
			$this->db->where("np_karyawan='7648'");

		//$this->db->where("np_karyawan='7648'");
		$this->db->where("date BETWEEN '$tgl_awal' AND '$tgl_akhir'");
		$this->db->order_by('nama', 'asc');
		$this->db->order_by('date', 'asc');
		$data = $this->db->get('ess_substitution');
		
		return $data->result();
	}
*/
/*	
	function select_np_by_kode_unit($list_kode_unit)
	{
		$this->db->select('*');
		$this->db->from('mst_karyawan');
		$this->db->where_in('kode_unit', $list_kode_unit);
		$this->db->order_by('no_pokok','ASC');
		$data = $this->db->get();
		
		return $data;
	}
*/	
	
	function select_pembatalan_cuti_by_id($id)
	{
		$tabel = "ess_pembatalan_cuti";
		
		$this->db->select('*');
		$this->db->from($tabel);	
		$this->db->where('id',$id);
		
		$query = $this->db->get();
		
		return $query->row_array();
	}

/*	
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
*/
/*	
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
*/
/*	
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
*/	
	//insert pembatalan cuti
	function insert_pembatalan_cuti($data_insert)
	{
		$np_karyawan 		= $data_insert['np_karyawan'];
		$personel_number 	= $data_insert['personel_number'];
		$nama 				= $data_insert['nama'];
		$nama_jabatan 		= $data_insert['nama_jabatan'];
		$kode_unit 			= $data_insert['kode_unit'];
		$nama_unit 			= $data_insert['nama_unit'];
		$absence_type		= $data_insert['absence_type'];
		$id_cuti			= $data_insert['id_cuti'];
		$is_cuti_bersama	= $data_insert['is_cuti_bersama'];
		$date				= $data_insert['date'];
		$date_submit		= $data_insert['date_submit'];
				
				
		$tabel = "ess_pembatalan_cuti";
				
		$data = array(               
                'np_karyawan' 			=> $np_karyawan,
				'personel_number' 		=> $personel_number,
				'nama' 					=> $nama,
				'nama_jabatan' 			=> $nama_jabatan,
				'kode_unit' 			=> $kode_unit,
				'nama_unit' 			=> $nama_unit,
				'absence_type' 			=> $absence_type,
				'id_cuti' 				=> $id_cuti,
				'is_cuti_bersama'		=> $is_cuti_bersama,
				'date' 					=> $date,
				'date_submit' 			=> $date_submit,				
				'created_at'			=> date('Y-m-d H:i:s'),
				'created_by'			=> $this->session->userdata('no_pokok')
            );
			
		$this->db->insert($tabel, $data); 
		
		$insert_id =  $this->db->insert_id();
		
		if($this->db->affected_rows() > 0)
		{
				$this->load->model('osdm/m_persetujuan_cuti_sdm');
				$this->m_persetujuan_cuti_sdm->update_cico_cuti($np_karyawan,$date);					
			
			return $insert_id;
		}else
		{
			return '0';
		}
		
		
	}
	
	//action pembatalan cuti
	function batal_pembatalan_cuti($data_batal)
	{
		$id 			= $data_batal['id'];	
					
		$tabel = "ess_pembatalan_cuti";
		
		$this->db->delete($tabel, array('id' => $id));
			
		if($this->db->affected_rows() > 0)
		{						
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
	
	//check apakah sudah ada pembatalan sebelumnya
	function check_pembatalan_exist($data)
	{
		$id_cuti 		= $data['id_cuti'];
		$is_cuti_bersama= $data['is_cuti_bersama'];
		$date 			= $data['date'];
					
		$tabel = "ess_pembatalan_cuti";				
				
		$this->db->where('id_cuti',$id_cuti);
		$this->db->where('is_cuti_bersama',$is_cuti_bersama);
		$this->db->where('date',$date);
		
		$this->db->from($tabel);
		
		return $this->db->count_all_results();

	}
	
	//select daftar cuti yang sudah di approve dan tanggal selesainya lebih besar sama dengan hari ini
	function select_daftar_cuti()
	{
			
		$this->db->select('a.*');
		$this->db->select('b.uraian');
		$this->db->from('ess_cuti a');
		$this->db->join('mst_cuti b', 'a.absence_type = b.kode_erp', 'left');
		
		$this->db->where('a.approval_sdm', '1');
		$this->db->where('a.end_date>=', date('Y-m-d'));			
		
		$data = $this->db->get();
		
		return $data;
	}
	
	//select daftar cuti bersama yang sudah di submit ke ERP yang memilih cuti tahunan sebagai pembayar
	function select_daftar_cuti_bersama()
	{
			
		$this->db->select('*');
		$this->db->from('ess_cuti_bersama');	
		$this->db->where('submit_erp', '1');
		
		$data = $this->db->get();
		
		return $data;
	}
	
	//select cuti by id
	function select_cuti_by_id($id)
	{			
		$this->db->select('a.*');
		$this->db->select('b.uraian');
		$this->db->from('ess_cuti a');	
		$this->db->join('mst_cuti b', 'a.absence_type = b.kode_erp', 'left');
		
		$this->db->where('a.id', $id);			
		
		$data = $this->db->get();
				
		return $data->row_array();
		
	}
	
	//select cuti bersama by id
	function select_cuti_bersama_by_id($id)
	{			
		$this->db->select('a.*');
		$this->db->from('ess_cuti_bersama a');	
		
		$this->db->where('a.id', $id);			
		
		$data = $this->db->get();
				
		return $data->row_array();
		
	}
	
	function select_pembatalan_cuti_by_month($tampil_bulan_tahun)
	{		
		$tabel ='ess_pembatalan_cuti';
						
		$this->db->select("$tabel.id");		
		$this->db->select("$tabel.np_karyawan");
		$this->db->select("$tabel.nama");	
		$this->db->select("b.uraian");	
		$this->db->select("$tabel.date");
		$this->db->select("$tabel.date_submit");
		$this->db->select("$tabel.is_cuti_bersama");
		
		$this->db->from($tabel);	
		$this->db->join('mst_cuti b', "$tabel.absence_type = b.kode_erp", 'left');
		
		$this->db->where("MONTH($tabel.date) ", $tampil_bulan_tahun['bulan']);
		$this->db->where("YEAR($tabel.date) ", $tampil_bulan_tahun['tahun']);	
	
		$this->db->order_by("$tabel.np_karyawan",'ASC');		
		$this->db->order_by("$tabel.date",'ASC');	
		
		$data = $this->db->get();
		
		
		return $data->result();
		
	}
	
	
	
}

/* End of file M_perencanaan_jadwal_kerja.php */
/* Location: ./application/models/kehadiran/M_perencanaan_jadwal_kerja.php */