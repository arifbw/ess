<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_pengajuan_lembur extends CI_Model {

	private $table="ess_lembur_transaksi";
	
	public function __construct(){
		parent::__construct();
		$this->table_schema = $this->db->database;
		//$ci =& get_instance();
		// $this->load->helper('tabel_helper');
		//Do your magic here
	}
    
	function check_table_exist($name)
	{
		$name=str_replace("-","_",$name);
		$query = $this->db->query("show tables like '$name'")->row_array();
		
		return $query;
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
			
		}
		else if($_SESSION["grup"]==5) { //jika Pengguna
			$var 	= $_SESSION["no_pokok"];
			
		}
		else {
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
	
	public function ambil_pengajuan_lembur_id($id){
		$data = $this->db->from($this->table)
						 ->where("id",$id)
						 ->get()
						 ->result_array()[0];
		return $data;
	}
	
	public function ambil_pengajuan_lembur_pegawai_id($id){
		$data = $this->db->from($this->table)
				 ->select("ess_lembur_transaksi.*, nama as nama_pegawai")
				 ->where("ess_lembur_transaksi.id",$id)
				 ->get()
				 ->result_array()[0];
		return $data;
	}
	
	public function ambil_pengajuan_lembur_pegawai_tgl($get, $unit){
		$np = implode("','", $get['np_karyawan']);
		$this->db->where("no_pokok in ('".$np."')");
		$data = $this->db->from($this->table)
				 ->select("ess_lembur_transaksi.*, nama as nama_pegawai")
				 ->where("tgl_dws", $get['tgl'])
				 ->where("kode_unit", $unit)
				 ->where('((waktu_mulai_fix is not null or waktu_mulai_fix != "0000-00-00 00:00:00") AND (waktu_selesai_fix is not null or waktu_selesai_fix != "0000-00-00 00:00:00"))')
				 ->get()
				 ->result_array();
		return $data;
	}
	
	public function ambil_unit_pegawai_tgl($get){
		$np = implode("','", $get['np_karyawan']);
		$this->db->where("no_pokok in ('".$np."')");
		$data = $this->db->from($this->table)
				 ->group_by("kode_unit")
				 ->select("kode_unit, nama_unit")
				 ->where("tgl_dws", $get['tgl'])
				 ->where('((waktu_mulai_fix is not null or waktu_mulai_fix != "0000-00-00 00:00:00") AND (waktu_selesai_fix is not null or waktu_selesai_fix != "0000-00-00 00:00:00"))')
				 ->get()
				 ->result_array();
		return $data;
	}
	
	public function daftar_pengajuan_lembur(){
		$data = $this->db->from($this->table)
						 ->order_by("created_at asc")
						 ->get()
						 ->result_array();
		return $data;
	}
	
	function select_pegawai($input_no_pokok) 
	{
		if($_SESSION["grup"]==4) //jika Pengadministrasi Unit Kerja
		{			
			$ada_data=0;
			$var=array();
			$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
			foreach ($list_pengadministrasi as $data) //looping list_pengadministrasi
			{	
				$no_pokok=$input_no_pokok;
				$ada_data=1;
			}
			if($ada_data==0)
			{
				$no_pokok='';
			}
			
		}
		else if($_SESSION["grup"]==5) //jika Pengguna
		{
			if($input_no_pokok==$_SESSION["no_pokok"])
			{
				$no_pokok=$input_no_pokok;
			}
			
		}
		else
		{
			$no_pokok=$input_no_pokok;				
		}

		$this->db->where_in('mst_karyawan.no_pokok', $no_pokok);
		$this->db->select('*');
		$this->db->from('mst_karyawan');	
		
		
		$data = $this->db->get();
		if($no_pokok!='')
		{
			return $data;
		}else
		{
			return '';
		}
		
    }
	
	function get_np_approval_otoritas() 
	{

		$this->db->where('approval_pimpinan_np', $_SESSION["no_pokok"]);
		$this->db->select('no_pokok, nama');
		$this->db->group_by('no_pokok');
		$this->db->from('ess_lembur_transaksi');	
		
		$data = $this->db->get()->result_array();
		return $data;		
    }
	
	function get_np_approval() 
	{

		$this->db->where('approval_pimpinan_np', $_SESSION["no_pokok"]);
		$this->db->select('approval_pimpinan_np as no_pokok, approval_pimpinan_nama as nama');
		$this->db->group_by('approval_pimpinan_np');
		$this->db->from('ess_lembur_transaksi');	
		
		$data = $this->db->get()->result_array();
		return $data;		
    }
	
	function get_np() 
	{

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
			if($ada_data==0)
				$var='';
		}
		else if($_SESSION["grup"]==5) //jika Pengguna
			$var = $_SESSION["no_pokok"];
		else
			$var = 1;

		if($_SESSION["grup"]==4) //jika Pengadministrasi Unit Kerja
			$this->db->where_in('mst_karyawan.kode_unit', $var);
		else if($_SESSION["grup"]==5) //jika Pengguna
			$this->db->where_in('mst_karyawan.no_pokok', $var);

		$this->db->select('no_pokok, nama');
		$this->db->from('mst_karyawan');	
		
		$data = $this->db->get()->result_array();
		return $data;		
    }
	
	function get_apv() 
	{
		$this->db->select('no_pokok, nama');
		$this->db->from('mst_karyawan');
		
		$data = $this->db->get()->result_array();
		return $data;		
    }
	
	public function get_unit_kerja(){
		$this->db->select("kode_unit")
				 ->select("nama_unit")
				 ->from("mst_satuan_kerja");
		
		//jika Pengadministrasi Unit Kerja
		if($_SESSION["grup"]==4) {
			$var=array();

			//looping list_pengadministrasi
			foreach ($_SESSION["list_pengadministrasi"] as $data) {
				array_push($var,$data['kode_unit']);
				$ada_data=1;
			}
		}

		//jika Pengadministrasi Unit Kerja
		if($_SESSION["grup"]==4){
			$this->db->where_in('kode_unit', $var);
		}
			
		$data = $this->db->get()->result_array();
		return $data;		
	}
	
	public function lembur_karyawan_per_bulan($no_pokok,$periode){
		$data = $this->db->select("tgl_dws")
						 ->select("waktu_mulai_fix")
						 ->select("waktu_selesai_fix")
						 ->select("case when time_type='01' then 'lembur akhir' when time_type='02' then 'lembur awal' end jenis_lembur",false) //01=lembur akhir / off | 02=lembur awal
						 ->from($this->table)
						 ->where("no_pokok",$no_pokok)
						 ->where("date_format(tgl_dws,'%Y_%m')",$periode)
						 ->get()->result_array();
		//echo $this->db->last_query();
		return $data;
	}
	
	public function data_lembur($data){
		$data = $this->db->from($this->table)
						 ->where('no_pokok',$data['no_pokok'])
						 ->where('tgl_mulai',$data['tgl_mulai'])
						 ->where('tgl_selesai',$data['tgl_selesai'])
						 ->where('jam_mulai',$data['jam_mulai'])
						 ->where('jam_selesai',$data['jam_selesai'])
						 ->get()
						 ->result_array()[0];
		return $data;
	}

	public function cek_hasil_tambah_lembur($data){
		if(empty($data['tgl_mulai'])){
			$data['tgl_mulai'] = "00:00:00";
		}
		if(empty($data['tgl_selesai'])){
			$data['tgl_selesai'] = "00:00:00";
		}
		if(empty($data['jam_mulai'])){
			$data['jam_mulai'] = "00:00:00";
		}
		if(empty($data['jam_selesai'])){
			$data['jam_selesai'] = "00:00:00";
		}
		
		$data = $this->db->from($this->table)
						 ->where('no_pokok',$data['no_pokok'])
						 ->where('tgl_dws',$data['tgl_dws'])
						 ->where('tgl_mulai',$data['tgl_mulai'])
						 ->where('tgl_selesai',$data['tgl_selesai'])
						 ->where('jam_mulai',$data['jam_mulai'])
						 ->where('jam_selesai',$data['jam_selesai'])
						 ->get();

		if($data->num_rows()==0){
			$return = false;
		}
		else{
			$return = true;
		}

		return $return;
	}

 	public function cek_time_type($data) {
		$data = $this->db->from('ess_lembur_transaksi')
						 ->where('no_pokok',$data['no_pokok'])
						 ->where('tgl_dws',date('Y-m-d', strtotime($data['tgl_dws'])))
						 ->get();
		
		if($data->num_rows()==1){
			$get_data = $data->row_array();
			if(date('Y-m-d H:i:s', strtotime($get_data['tgl_selesai'].' '.$get_data['jam_selesai'])) <= date('Y-m-d H:i:s', strtotime($tgl))) {
				$type = '1';
				$this->db->set('time_type', '0')->where('id', $get_data['id'])->update('ess_lembur_transaksi');
			}
			else {
				$type = '0';
				$this->db->set('time_type', '1')->where('id', $get_data['id'])->update('ess_lembur_transaksi');
			}
		}
		else{
			$type = '0';
		}
		return $type;
	}
	
 	public function cek_dws_lembur($data) {
		$bulan = date('Y_m', strtotime($data['tgl_dws']));
		$table = $this->db->query('SELECT count(*) as htg FROM information_schema.tables WHERE table_schema = "'.$this->table_schema.'" AND table_name = "ess_cico_'.$bulan.'"')->row_array();
		if($table['htg'] == 1) {
			$table_name = "ess_cico_".$bulan;
		}
		else {
			$table_name = "ess_cico";
		}

		$get_dws = $this->db->from($table_name)
				->where('np_karyawan', $data['no_pokok'])
				->where('dws_tanggal = "'.$data['tgl_dws'].'"')
				->count_all_results();
		if($get_dws == 0) {
			return FALSE;
		}
		else {
			return TRUE;
		}
	}

 	public function cek_uniq_lembur($data, $id=null, $mulai_fix, $selesai_fix) {
		$bulan = date('Y_m', strtotime($data['tgl_dws']));
		$table = $this->db->query('SELECT count(*) as htg FROM information_schema.tables WHERE table_schema = "'.$this->table_schema.'" AND table_name = "ess_cico_'.$bulan.'"')->row_array();
		if($table['htg'] == 1) {
			$table_name = "ess_cico_".$bulan;
		}
		else {
			$table_name = "ess_cico";
		}

		$get['start_input'] = date('Y-m-d', strtotime($data['tgl_mulai'])).' '.date('H:i:s', strtotime($data['jam_mulai']));
		$get['end_input'] = date('Y-m-d', strtotime($data['tgl_selesai'])).' '.date('H:i:s', strtotime($data['jam_selesai']));
		
		//pengecekan jika (input_in diantara ess_in dan ess_out) dan (input_out diantara ess_in dan ess_out) dan (input_in = ess_in dan input_out = ess_out)
		if ($id != null) {
			$this->db->where('id != '.$id);
		}
		$uniq_fix = $this->db->from('(select id, "'.$mulai_fix.'" as mulai_fix, "'.$selesai_fix.'" as selesai_fix, (case when waktu_mulai_fix is not null then waktu_mulai_fix else concat(tgl_mulai," ",jam_mulai) end) as start_ess, (case when waktu_selesai_fix is not null then waktu_selesai_fix else concat(tgl_selesai," ",jam_selesai) end) as end_ess, "'.$get['start_input'].'" as start_input, "'.$get['end_input'].'" as end_input from ess_lembur_transaksi where no_pokok="'.$data['no_pokok'].'") as abc')
			->where('((mulai_fix between `start_ess` and end_ess) or (selesai_fix between `start_ess` and end_ess))')
			->count_all_results();

		if ($id != null) {
			$this->db->where('id != '.$id);
		}
		/*$uniq_lembur = $this->db->from('(select id, (case when waktu_mulai_fix is not null then waktu_mulai_fix else concat(tgl_mulai," ",jam_mulai) end) as start_ess, (case when waktu_selesai_fix is not null then waktu_selesai_fix else concat(tgl_selesai," ",jam_selesai) end) as end_ess, "'.$get['start_input'].'" as start_input, "'.$get['end_input'].'" as end_input from ess_lembur_transaksi where no_pokok="'.$data['no_pokok'].'" and tgl_dws="'.$data['tgl_dws'].'") as abc')
			->where('((start_input between `start_ess` and end_ess) and (`end_input` between `start_ess` and end_ess))')
			->get();
			var_dump($uniq_lembur->result_array());exit;*/

		$uniq_lembur = $this->db->from('(select id, concat(tgl_mulai," ",jam_mulai) as start_ess, concat(tgl_selesai," ",jam_selesai) as end_ess, "'.$get['start_input'].'" as start_input, "'.$get['end_input'].'" as end_input from ess_lembur_transaksi where no_pokok="'.$data['no_pokok'].'" and tgl_dws="'.$data['tgl_dws'].'") as abc')
			->where('((start_input between `start_ess` and end_ess) or (`end_input` between `start_ess` and end_ess))')
			->count_all_results();
			//var_dump($uniq_lembur->result_array());exit;

			//var_dump($uniq_fix->result_array());echo '<br><br>';var_dump($uniq_lembur->result_array());echo '<br><br>';echo '(select id, "'.$mulai_fix.'" as mulai_fix, "'.$selesai_fix.'" as selesai_fix, (case when waktu_mulai_fix is not null then waktu_mulai_fix else concat(tgl_mulai," ",jam_mulai) end) as start_ess, (case when waktu_selesai_fix is not null then waktu_selesai_fix else concat(tgl_selesai," ",jam_selesai) end) as end_ess, "'.$get['start_input'].'" as start_input, "'.$get['end_input'].'" as end_input from ess_lembur_transaksi where no_pokok='.$data['no_pokok'].') as abc';exit;
		
		//pengecekan jika (input_in diantara dws_in dan dws_out) dan (input_out diantara dws_in dan dws_out)
		$uniq_tgl_dws = $this->db->from($table_name)
			->where('np_karyawan="'.$data['no_pokok'].'" and dws_tanggal="'.$data['tgl_dws'].'"')
			->count_all_results();
		$uniq_not_valid = $this->db->from('(select (case when tapping_fix_1 is not null then tapping_fix_1 else tapping_time_1 end) as start_cico, (case when tapping_fix_2 is not null then tapping_fix_2 else tapping_time_2 end) as end_cico, "'.$get['start_input'].'" as start_input, "'.$get['end_input'].'" as end_input from '.$table_name.' where np_karyawan="'.$data['no_pokok'].'" and dws_tanggal="'.$data['tgl_dws'].'") as abc')
				->where('((start_input >= end_cico and end_input >= end_cico) OR (start_input <= start_cico and end_input <= start_cico))')
				// ->where('((start_input <= start_cico) and (end_input >= start_cico))')
				// ->where('((start_input <= end_cico) and (end_input >= end_cico))')
				->count_all_results();
		$uniq_dws_null = $this->db->from('(select concat((case when dws_in_tanggal_fix is not null then dws_in_tanggal_fix else dws_in_tanggal end), " ", (case when dws_in_fix is not null then dws_in_fix else dws_in end)) as start_dws, concat((case when dws_out_tanggal_fix is not null then dws_out_tanggal_fix else dws_out_tanggal end), " ", (case when dws_out_fix is not null then dws_out_fix else dws_out end)) as end_dws, "'.$get['start_input'].'" as start_input, "'.$get['end_input'].'" as end_input from '.$table_name.' where np_karyawan="'.$data['no_pokok'].'" and dws_tanggal="'.$data['tgl_dws'].'") as abc')
				->where('((start_dws is null or end_dws is null) OR (start_dws = "0000-00-00 00:00:00" or end_dws = "0000-00-00 00:00:00"))')
				->count_all_results();


		if ($uniq_lembur > 0) {
			$set['status'] = FALSE;
			$set['message'] = 'Rentang waktu lembur sudah ada';
		}
		else if ($uniq_tgl_dws < 1 || $uniq_not_valid > 0 || $uniq_dws_null > 0) {
			$set['status'] = TRUE;
			$set['message'] = 'Not DWS';
		}
		else {
			//echo $data['tgl_dws'];exit;
			//pengecekan jika (input_in diantara dws_in dan dws_out) dan (input_out diantara dws_in dan dws_out)
			$uniq_dws = $this->db->from('(select concat((case when dws_in_tanggal_fix is not null then dws_in_tanggal_fix else dws_in_tanggal end), " ", (case when dws_in_fix is not null then dws_in_fix else dws_in end)) as start_dws, concat((case when dws_out_tanggal_fix is not null then dws_out_tanggal_fix else dws_out_tanggal end), " ", (case when dws_out_fix is not null then dws_out_fix else dws_out end)) as end_dws, "'.$get['start_input'].'" as start_input, "'.$get['end_input'].'" as end_input from '.$table_name.' where np_karyawan="'.$data['no_pokok'].'" and dws_tanggal="'.$data['tgl_dws'].'") as abc')
				->where('((start_input between start_dws and end_dws) and (end_input between start_dws and end_dws))')
				->count_all_results();

				//var_dump($uniq_dws->result_array());exit;
			//pengecekan jika (input_in diantara cico_in dan cico_out) dan (input_out diantara cico_in dan cico_out) dan (input_in = cico_in dan input_out = cico_out)
			$uniq_cico = $this->db->from('(select (case when dws_name_fix is not null then dws_name_fix else dws_name end) as dws_date_name, (case when tapping_fix_1 is not null then tapping_fix_1 else tapping_time_1 end) as start_cico, (case when tapping_fix_2 is not null then tapping_fix_2 else tapping_time_2 end) as end_cico, "'.$get['start_input'].'" as start_input, "'.$get['end_input'].'" as end_input from '.$table_name.' where np_karyawan="'.$data['no_pokok'].'" and dws_tanggal="'.$data['tgl_dws'].'") as abc')
				// ->where('(((start_input between start_cico and end_cico and start_input not in (start_cico, end_cico)) and (end_input between start_cico and end_cico and end_input not in (start_cico, end_cico))) OR (start_cico = start_input and end_cico = end_input))')
				->where('(dws_date_name != "OFF")')
				->where('((start_input < end_cico) and (end_input > end_cico))')
				->where('((start_input = end_cico) and (end_input = end_cico))')
				->count_all_results();
				// ->get()->result_array();

			//pengecekan jika (input_in diantara cico_in dan cico_out) dan (input_out diantara cico_in dan cico_out) dan (input_in = cico_in dan input_out = cico_out)


			if ($uniq_cico > 0 || $uniq_dws > 0 || $uniq_not_valid > 0) {
				// echo $uniq_cico;
				// exit;
				$set['status'] = TRUE;
				$set['message'] = 'Not Valid';
			}
			else {
				$set['status'] = TRUE;
				$set['message'] = 'Success';
			}
		}
		// echo $uniq_cico;exit;
		// echo $uniq_lembur;exit;
		// var_dump($set);exit;
		return $set;
	} 
	
 	public function cek_valid_lembur($data) {
 		$bulan = date('Y_m', strtotime($data['tgl_dws']));
		$table = $this->db->query('SELECT count(*) as htg FROM information_schema.tables WHERE table_schema = "'.$this->table_schema.'" AND table_name = "ess_cico_'.$bulan.'"')->row_array();
		if($table['htg'] == 1) {
			$table_name = "ess_cico_".$bulan;
		}
		else {
			$table_name = "ess_cico";
		}

		$cico = $this->db->from($table_name)
					 ->where('np_karyawan', $data['no_pokok'])
					 ->where('dws_tanggal', $data['tgl_dws'])
					 ->get();

		$set_date['start_input'] = date('Y-m-d', strtotime($data['tgl_mulai'])).' '.date('H:i:s', strtotime($data['jam_mulai']));
		$set_date['end_input'] = date('Y-m-d', strtotime($data['tgl_selesai'])).' '.date('H:i:s', strtotime($data['jam_selesai']));
		if($cico->num_rows() == 1) {
			$get_jadwal = $cico->row_array();
			//DWS DATETIME
			if($get_jadwal['dws_in_tanggal_fix'] != null)
				$start_dws_date = $get_jadwal['dws_in_tanggal_fix'];
			else
				$start_dws_date = $get_jadwal['dws_in_tanggal'];
				
			if($get_jadwal['dws_in_fix'] != null)
				$start_dws_time = $get_jadwal['dws_in_fix'];
			else
				$start_dws_time = $get_jadwal['dws_in'];
			
			if($get_jadwal['dws_out_tanggal_fix'] != null)
				$end_dws_date = $get_jadwal['dws_out_tanggal_fix'];
			else
				$end_dws_date = $get_jadwal['dws_out_tanggal'];
				
			if($get_jadwal['dws_out_fix'] != null)
				$end_dws_time = $get_jadwal['dws_out_fix'];
			else
				$end_dws_time = $get_jadwal['dws_out'];

				//echo $get_jadwal['dws_out_tanggal_fix'];

			//CICO DATETIME
			if($get_jadwal['tapping_fix_1'] != null)
				$set_date['start_cico'] = $get_jadwal['tapping_fix_1'];
			else
				$set_date['start_cico'] = $get_jadwal['tapping_time_1'];
				
			if($get_jadwal['tapping_fix_2'] != null)
				$set_date['end_cico'] = $get_jadwal['tapping_fix_2'];
			else
				$set_date['end_cico'] = $get_jadwal['tapping_time_2'];
			
			//IF nya WINA
			if ($set_date['start_cico'] == null && $set_date['end_cico'] == null) {
				//CHECK ID PERIZINAN
				//TARO SETING START CICO DARI PERIZINAN menggunakan variabel $set_date['start_cico'] dan $set_date['end_cico']
				$set_date['start_cico'] = null; //ubah disini
				$set_date['end_cico'] = null; //ubah disini
			}
			
			//EDIT DISINI YA MAS BOWO
			//jika ada tapping in tapi tidak ada tapping out
			//untuk keperluan ketika ada karyawan yg lembur di hari libur tapping in tapi tidak tapping out karena ada izin nya
			if ($set_date['start_cico'] && $set_date['end_cico'] == null) { 
				//CHECK ID PERIZINAN							
				$np_karyawan = $data['no_pokok'];
				$dws_tanggal =$data['tgl_dws'];
				$tahun_bulan = substr($dws_tanggal,0,7);	
				
				$tahun_bulan=str_replace("-","_",$tahun_bulan);
				
				$tabel_cico = 'ess_cico_'.$tahun_bulan;
				if(!$this->check_table_exist($tabel_cico))
				{
					$tabel_cico = 'ess_cico';
				}
				
				//ambil cico
				$ambil_cico = $this->db->query("SELECT * FROM $tabel_cico WHERE np_karyawan='$np_karyawan' AND dws_tanggal='$dws_tanggal'")->row_array();
				$id_perizinan = $ambil_cico['id_perizinan'];
				
				if($id_perizinan)
				{
					$tabel_perizinan = 'ess_perizinan_'.$tahun_bulan;
					if(!$this->check_table_exist($tabel_perizinan))
					{
						$tabel_perizinan = 'ess_perizinan';
					}
					
					$ambil_perizinan_max_end = $this->db->query("SELECT end_date,end_time FROM $tabel_perizinan WHERE id IN ($id_perizinan) ORDER BY end_date DESC,end_time DESC LIMIT 1")->row_array();
					$perizinan_max_end = $ambil_perizinan_max_end['end_date']." ".$ambil_perizinan_max_end['end_time'];
					
					$ambil_perizinan_max_start = $this->db->query("SELECT start_date,start_time FROM $tabel_perizinan WHERE id IN ($id_perizinan) ORDER BY start_date DESC,start_time DESC LIMIT 1")->row_array();
					$perizinan_max_start =  $ambil_perizinan_max_start['start_date']." ".$ambil_perizinan_max_start['start_time'];
					
					if($perizinan_max_end>=$perizinan_max_start)
					{
						$pakai_penutup_izin = $perizinan_max_end;
					}else
					{
						$pakai_penutup_izin = $perizinan_max_start;
					}
					//echo $set_date['end_cico'];
					//die($pakai_penutup_izin);
					$set_date['end_cico'] = $pakai_penutup_izin; //ubah disini
				}else
				{
					$set_date['end_cico'] = null; //ubah disini
				}
				
				
			}

			//JIKA TIDAK TAPPING CICO
			if((date('H:i:s', strtotime($set_date['start_cico'])) == '00:00:00' || $set_date['start_cico'] == null) && ($set_date['end_cico'] == null || date('H:i:s', strtotime($set_date['end_cico'])) == '00:00:00')) {
				$get_fix = FALSE;
			}
			//JIKA DWS OFF
			else if((date('H:i:s', strtotime($start_dws_time)) == '00:00:00' || $end_dws_time == null) && ($start_dws_time == null || date('H:i:s', strtotime($end_dws_time)) == '00:00:00')) {
				//		echo 'a';exit;
				$get_fix['time_type'] = '01'; //JIKA OFF MAKA TIPE = 1
				//JIKA LEMBUR IN LEBIH DARI TAPPING IN DAN LEMBUR OUT LEBIH DARI TAPPING OUt
				if($set_date['start_input'] < $set_date['start_cico'] && $set_date['start_input'] > $set_date['start_cico'])
					$get_fix = FALSE;
				//JIKA LEMBUR DIANTARA TAPPING
				else {
					//JIKA TAPPING IN KURANG DARI LEMBUR IN
					if($set_date['start_cico'] <= $set_date['start_input']) 
						$get_fix['waktu_mulai_fix'] = $set_date['start_input'];
						//JIKA TAPPING IN LEBIH DARI LEMBUR IN
					else 
						$get_fix['waktu_mulai_fix'] = $set_date['start_cico'];

					//JIKA TAPPING OUT LEBIH DARI LEMBUR OUT
					if($set_date['end_cico'] >= $set_date['end_input']) 
						$get_fix['waktu_selesai_fix'] = $set_date['end_input'];
					//JIKA TAPPING OUT KURANG DARI LEMBUR OUT
					else 
						$get_fix['waktu_selesai_fix'] = $set_date['end_cico'];
				}
 			}
			//JIKA DWS BUKAN OFF
			else {
				$set_date['start_dws'] = $start_dws_date.' '.$start_dws_time;
				$set_date['end_dws'] = $end_dws_date.' '.$end_dws_time;

				//JIKA TIDAK TAPPING IN
				if ($set_date['start_cico'] == null)
					$set_date['start_cico'] = $set_date['start_dws'];

				//JIKA TIDAK TAPPING OUT
				if ($set_date['end_cico'] == null)
					$set_date['end_cico'] = $set_date['end_dws'];

				//JIKA TAPPING IN LEBIH DARI DWS IN dan TAPPING OUT KURANG DARI DWS OUT
				if ($set_date['start_cico'] >= $set_date['start_dws'] && $set_date['end_cico'] <= $set_date['end_dws'])
					$get_fix = FALSE;
				//JIKA TAPPING IN LEBIH DARI DWS IN DAN TAPPING OUT LEBIH DARI DWS OUT
				else if ($set_date['start_cico'] >= $set_date['start_dws']) {
					$get_fix['time_type'] = '01'; //TAPPING IN TIDAK DIAKUI //01=lembur akhir / off | 02=lembur awal
					$get_fix['waktu_mulai_fix'] = $set_date['end_dws']; //LEMBUR IN DIAKUI PADA DWS OUT ATAU TAPPING OUT
					//JIKA LEMBUR OUT LEBIH DARI DWS OUT DAN LEMBUR OUT KURANG DARI TAPPING OUT
					if ($set_date['end_input'] > $set_date['end_dws'] && $set_date['end_input'] <= $set_date['end_cico'])
						$get_fix['waktu_selesai_fix'] = $set_date['end_input'];
					//JIKA LEMBUR OUT LEBIH DARI DWS OUT DAN LEMBUR OUT LEBIH DARI TAPPING OUT
					else if ($set_date['end_input'] > $set_date['end_cico'])
						$get_fix['waktu_selesai_fix'] = $set_date['end_cico'];
					//JIKA LEMBUR OUT KURANG DARI DWS OUT
					else
						$get_fix = FALSE;
				}
				//JIKA TAPPING IN KURANG DARI DWS IN DAN TAPPING OUT KURANG DARI DWS OUT
				else if ($set_date['end_cico'] <= $set_date['end_dws']) {
					$get_fix['time_type'] = '02'; //TAPPING OUT TIDAK DIAKUI //01=lembur akhir / off | 02=lembur awal
					$get_fix['waktu_selesai_fix'] = $set_date['start_dws']; //LEMBUR OUT DIAKUI PADA DWS IN ATAU TAPPING IN
					//JIKA LEMBUR IN KURANG DARI DWS IN DAN LEMBUR IN LEBIH DARI TAPPING IN
					if ($set_date['start_input'] < $set_date['start_dws'] && $set_date['start_input'] >= $set_date['start_cico'])
						$get_fix['waktu_mulai_fix'] = $set_date['start_input'];
					//JIKA LEMBUR IN KURANG DARI TAPPING IN DAN LEMBUR IN LEBIH DARI DWS OUT
					else if ($set_date['start_input'] < $set_date['start_cico'])
						$get_fix['waktu_mulai_fix'] = $set_date['start_cico'];
					//JIKA LEMBUR IN LEBIH DARI DWS IN
					else
						$get_fix = FALSE;
				}
				//JIKA TAPPING IN KURANG DARI DWS IN dan TAPPING OUT LEBIH DARI DWS OUT
				else {
					//JIKA LEMBUR IN KURANG DARI DWS IN
					if ($set_date['start_input'] < $set_date['start_dws']) {
						$get_fix['time_type'] = '02'; //KURANG DARI DWS IN //01=lembur akhir / off | 02=lembur awal
						//JIKA LEMBUR IN KURANG DARI TAPPING IN
						if ($set_date['start_input'] <= $set_date['start_cico'])
							$get_fix['waktu_mulai_fix'] = $set_date['start_cico'];
						//JIKA LEMBUR IN LEBIH DARI TAPPING IN
						else
							$get_fix['waktu_mulai_fix'] = $set_date['start_input'];
					}
					//JIKA LEMBUR IN LEBIH DARI DWS IN
					else {
						$get_fix['time_type'] = '01'; //LEMBUR IN TIDAK DIAKUI //01=lembur akhir / off | 02=lembur awal
						//JIKA LEMBUR IN LEBIH DARI DWS OUT
						if ($set_date['start_input'] > $set_date['end_dws']) {
							//JIKA LEMBUR IN KURANG DARI TAPPING OUT
							if ($set_date['start_input'] <= $set_date['end_cico'])
								$get_fix['waktu_mulai_fix'] = $set_date['start_input'];
							//JIKA LEMBUR IN LEBIH DARI TAPPING OUT
							else
								$get_fix = FALSE;
						}
						//JIKA LEMBUR IN KURANG DARI DWS OUT
						else {
							//JIKA LEMBUR IN KURANG DARI TAPPING OUT
							if ($set_date['start_input'] <= $set_date['end_cico'])
								$get_fix['waktu_mulai_fix'] = $set_date['end_dws'];
							//JIKA LEMBUR IN LEBIH DARI TAPPING OUT
							else
								$get_fix = FALSE;
						}
					}

					//JIKA LEMBUR OUT KURANG DARI DWS IN
					if ($set_date['end_input'] < $set_date['start_dws']) {
						$get_fix['time_type'] = '02'; //LEMBUR OUT KURANG DARI DWS IN //01=lembur akhir / off | 02=lembur awal
						//JIKA LEMBUR OUT LEBIH DARI TAPPING IN
						if ($set_date['end_input'] > $set_date['start_cico'])
							$get_fix['waktu_selesai_fix'] = $set_date['end_input'];
						//JIKA LEMBUR OUT KURANG DARI TAPPING IN
						else
							$get_fix = FALSE;
					}
					//JIKA LEMBUR OUT LEBIH DARI DWS IN
					else {
						//JIKA LEMBUR OUT LEBIH DARI DWS OUT dan LEMBUR IN KURANG DARI DWS IN
						if ($set_date['end_input'] > $set_date['end_dws'] && $set_date['start_input'] < $set_date['start_dws']) {
							$get_fix['time_type'] = '01'; //LEMBUR IN TIDAK DIAKUI
							if ($set_date['end_cico'] <= $set_date['start_dws'])
								$get_fix['waktu_selesai_fix'] = $set_date['end_cico'];
							else if ($set_date['end_input'] <= $set_date['start_cico'])
								$get_fix['waktu_selesai_fix'] = $set_date['end_input'];
							else
								$get_fix['waktu_selesai_fix'] = $set_date['start_dws'];
						}
						//JIKA LEMBUR OUT KURANG DARI DWS OUT dan LEMBUR IN LEBIH DARI DWS IN
						else if (($set_date['end_input'] > $set_date['end_dws']) && ($set_date['start_input'] > $set_date['start_dws'])) {
							if ($set_date['end_input'] <= $set_date['end_cico'])
								$get_fix['waktu_selesai_fix'] = $set_date['end_input'];
							else
								$get_fix['waktu_selesai_fix'] = $set_date['end_cico'];
						}
						else {
							//	echo '1';
							if (($set_date['end_input'] < $set_date['end_dws']) && ($set_date['start_input'] < $set_date['start_dws']))
								$get_fix['waktu_selesai_fix'] = $set_date['start_dws'];
							else if ($set_date['end_input'] <= $set_date['start_dws'] && $set_date['end_input'] > $set_date['start_cico'])
								$get_fix['waktu_selesai_fix'] = $set_date['end_input'];
							else if ($set_date['end_input'] > $set_date['start_dws'] && $set_date['end_input'] > $set_date['start_cico'])
								$get_fix['waktu_selesai_fix'] = $set_date['end_dws'];
							else
								$get_fix = FALSE;
						}
					}
				}
			}
		}
		else {
			$get_fix = FALSE;
		}
		//var_dump($get_fix);exit;
		//var_dump($get_fix);exit;
		return $get_fix;
	}
	
 	public function update_dws($np_karyawan, $tgl) {
 		$get_lembur = $this->db->from('ess_lembur_transaksi')
					 ->where('no_pokok', $np_karyawan)
					 ->where('tgl_dws', $tgl)
					 ->get()
					 ->result_array();
					 
		$this->load->helper("karyawan_helper");
		
		foreach ($get_lembur as $data) {
			
						
			/*BOWO COPAS DARI UBAH LEMBUR*/
	 		$kry = erp_master_data_by_np($data['no_pokok'], $data['tgl_dws']);
	 		$apv = erp_master_data_by_np($data['approval_pimpinan_np'], $data['tgl_dws']);
			$set = array("no_pokok" => $data['no_pokok'], "nama" => $kry['nama'], "nama_jabatan" => $kry['nama_jabatan'], "nama_unit" => $kry['nama_unit'],"kode_unit" => $kry['kode_unit'], "approval_pimpinan_np" => $data['approval_pimpinan_np'], "approval_pimpinan_nama" => $apv['nama'], "approval_pimpinan_nama_jabatan" => $apv['nama_jabatan'], "approval_pimpinan_nama_unit" => $apv['nama_unit'], "approval_pimpinan_kode_unit" => $apv['kode_unit'], "personel_number" => $kry['personnel_number'], "tgl_dws" =>  $data['tgl_dws'], "tgl_mulai" =>  $data['tgl_mulai'], "tgl_selesai" => $data['tgl_selesai'], "jam_mulai" => $data['jam_mulai'], "jam_selesai" => $data['jam_selesai']);
			$where = array("id" => $data['id'], "no_pokok" => $data['no_pokok'], "tgl_mulai" =>  $data['tgl_mulai'], "tgl_selesai" => $data['tgl_selesai'], "jam_mulai" => $data['jam_mulai'], "jam_selesai" => $data['jam_selesai']);
			$set['updated_by']	= $this->session->userdata("no_pokok");
			$set['updated_at']	= date("Y-m-d H:i:s");
			//$where_update = array("id" => $data['id']);

			$get_date['start_input'] = date('Y-m-d', strtotime($set['tgl_mulai'])).' '.date('H:i:s', strtotime($set['jam_mulai']));
			$get_date['end_input'] = date('Y-m-d', strtotime($set['tgl_selesai'])).' '.date('H:i:s', strtotime($set['jam_selesai']));
			$date_dws = date('m/d/Y', strtotime($set['tgl_dws']));
			$plus1 = date('Y-m-d',strtotime($date_dws."+1 days"));
			$minus1 = date('Y-m-d',strtotime($date_dws."-1 days"));

			$get_jadwal = $this->cek_valid_lembur($set);
			$cek_uniq_lembur = $this->cek_uniq_lembur($set, $data['id'], null, null);
			//echo (int)$cek_uniq_lembur['status'];exit;
			if (($get_date['start_input'] < $get_date['end_input'] || (($set['tgl_mulai'] != $set['tgl_dws'] || $set['tgl_mulai'] != $plus1 || $set['tgl_mulai'] != $minus1) && ($set['tgl_selesai'] != $set['tgl_dws'] || $set['tgl_selesai'] != $plus1 || $data[$i]['tgl_selesai'] != $minus1))) && $cek_uniq_lembur['status'] == true) {
				// $get_jadwal = $this->cek_valid_lembur($set);
				
				//if((bool)$this->cek_uniq_lembur($data[$i]) == true) {
				$set['waktu_mulai_fix'] = null;
				$set['waktu_selesai_fix'] = null;
				if ((bool)$get_jadwal != false && (bool)$this->cek_dws_lembur($set) == true) {
					//var_dump($get_jadwal);exit;
					if ($cek_uniq_lembur['message'] == 'Not Valid') {
						$set['waktu_mulai_fix'] = $get_date['start_input'];
						$set['waktu_selesai_fix'] = $get_date['end_input'];
						$set['time_type'] = $get_jadwal['time_type'];
					}
					else if ($cek_uniq_lembur['message'] == 'Not DWS') {
						$set['waktu_mulai_fix'] = $set['waktu_mulai_fix'];
						$set['waktu_selesai_fix'] = $set['waktu_selesai_fix'];
						$set['time_type'] = null;
					}
					else {
						$set['waktu_selesai_fix'] = $get_jadwal['waktu_selesai_fix'];
						$set['waktu_mulai_fix'] = $get_jadwal['waktu_mulai_fix'];
						$set['time_type'] = $get_jadwal['time_type'];
					}
					//echo 'a';exit;
				}
				$where_update = array("id" => $data['id']);
				$this->ubah($set, $where_update);
			}
			else {
				//$return = false;
			}
		}
	}
 	
	
	public function set_cico($data){
		//var_dump($data);exit;
		$bulan = date('Y_m', strtotime($data['tgl_dws']));
		$table = $this->db->query('SELECT count(*) as htg FROM information_schema.tables WHERE table_schema = "'.$this->table_schema.'" AND table_name = "ess_cico_'.$bulan.'"')->row_array();
		if($table['htg'] == 1) {
			$table_name = "ess_cico_".$bulan;
		}
		else {
			$table_name = "ess_cico";
		}

	 	$set = $this->db->from($table_name)
	 			->where('dws_tanggal', $data['tgl_dws'])
	 			->where('np_karyawan', $data['no_pokok'])
	 			->get();
	 	$get_all = $this->db->from('ess_lembur_transaksi')
	 			->where('tgl_dws', $data['tgl_dws'])
	 			->where('no_pokok', $data['no_pokok'])
	 			->where('approval_status', '1')
	 			->where('is_manual_by_sdm != "1"')
	 			->get()->result_array();

	 	if ($set->num_rows() == 1) {
	 		$cico = $set->row_array();
	 		$id_overtime = implode(",", array_column($get_all, 'id'));
	 		//echo $id_overtime;exit;
	 		$this->db->set('id_overtime', $id_overtime)->where('id', $cico['id'])->update($table_name);
	 	}
	 	if($this->db->affected_rows() > 0)
			return true; 
		else
			return false;
	}
	
	public function tambah($data){
		$this->db->insert($this->table,$data);
		$insert_id = $this->db->insert_id();

   		return  $insert_id;
	}
	
	public function ubah($set,$where){
		$this->db->where($where)
				 // ->where('tgl_mulai',$where['tgl_mulai'])
				 // ->where('tgl_selesai',$where['tgl_selesai'])
				 // ->where('jam_mulai',$where['jam_mulai'])
				 ->update($this->table,$set);
	}

	public function hapus($id){
		$this->db->where('id',$id)
				 // ->where('no_pokok',$where['no_pokok'])
				 // ->where('tgl_mulai',$where['tgl_mulai'])
				 // ->where('tgl_selesai',$where['tgl_selesai'])
				 // ->where('jam_mulai',$where['jam_mulai'])
				 ->delete($this->table);
		return $this->db->affected_rows();
	}
	
}

/* End of file m_jadwal_kerja.php */
/* Location: ./application/models/master_data/m_jadwal_kerja.php */