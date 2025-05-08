<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pamlek extends CI_Controller {	 
	function __construct()
	{
		parent::__construct();
		$this->load->model('pamlek/M_pamlek');
		
	}
	
	public function index()
	{
		redirect(base_url('dashboard'));
	}
	
	//Fungsi ini merupakan fungsi untuk update data file dari pamlek agar ter-registrasi di sistem peruri
	public function get_files($tanggal_sekarang = "")
	{
		//run program selamanya untuk menghindari maximal execution
		//ini_set('MAX_EXECUTION_TIME', -1);
		set_time_limit('0');
		
		//$this->output->enable_profiler(TRUE);
		
		if(empty($tanggal_sekarang)){
			$tanggal_sekarang = date("Y-m-d");
		}
		
		echo "Proses ambil nama file terbaru di Pamlek";
		echo "<br>mulai ".date('Y-m-d H:i:s')."<br>";
		
		//membatasi yang di proses data yang sudah siap, yaitu dua hari sebelum
		$tanggal_siap_proses = date('Y-m-d', strtotime($tanggal_sekarang . ' -1 day'));
		
		//ambil setting an di dalam database
		$setting	= $this->M_pamlek->setting();
		$pamlek_url	= dirname($_SERVER["SCRIPT_FILENAME"]).$setting['url'];
		
		//ambil data tentang pamlek file yang sudah ada dalam database
		$result 	= $this->M_pamlek->select_pamlek_files();
		
		$arr_registered_pamlek_files = array();
		foreach ($result->result_array() as $data) 
		{
			array_push($arr_registered_pamlek_files,$data['nama_file']);		
		 
		}

		$old = umask(0);
		
		//check server pamlek menyala
		if(is_dir($pamlek_url))
		{
			//scan file .tk dalam server ftp pamlek 
			$data = scandir($pamlek_url);
			$query = "";
			foreach($data as $file){
				
				//tambahkan data file .tk terbaru dalam database
				//7648 Tri Wibowo 13 12 2021, ada file FR yang masuk yg formatnya ada tulisan -FR-I.tk
				//if(!in_array($file,$arr_registered_pamlek_files) and strcmp(substr($file,-3),".tk")==0 and (int)substr($file,0,4)>=2018){
				if(!in_array($file,$arr_registered_pamlek_files) and (int)substr($file,0,4)>=2018){
					
					//yang di proses itu yang dua hari sebelum hari H
					if(substr($file,0,10)>'2018-12-30' && substr($file,0,10)<=$tanggal_siap_proses)
					{					
						$data_db = array(
							'nama_file'			=> $file,
							'size' 				=> filesize($pamlek_url.$file),
							'last_modified'		=> date("Y-m-d H:i:s",filemtime($pamlek_url.$file)) ,					
						);
						
						$insert_data = $this->M_pamlek->insert_files($data_db);
					
						if($insert_data==true)
						{
							echo "<br>status = $file berhasil masuk db";
						}else
						{
							echo "<br>status = gagal masuk db<br>";
						}
					}
				}
			}

				
		}else
		{
			$data_error['modul'] 		= "pamlek/pamlek/get_files/".$tanggal_sekarang; 
			$data_error['error'] 		= "Gagal konek ke Server Pamlek"; 
			$data_error['status'] 		= "0";
			$data_error['created_at'] 	= date("Y-m-d H:i:s");
			$data_error['created_by'] 	= "scheduler";
			
			$this->M_pamlek->insert_error($data_error);
			echo "<br>status = ".$data_error['error'].", ".$data_error['modul'];
		}			
		
		umask($old);
		if ($old != umask()) {
			die('An error occurred while changing back the umask');
		}
		
		echo "<br><br>selesai ".date('Y-m-d H:i:s')."<br>";		
	}
		
	//Fungsi untuk mengambil data dalam file .tk yang ada di Pamlek
	public function get_data()
	{
		//run program selamanya untuk menghindari maximal execution
		//ini_set('MAX_EXECUTION_TIME', -1);
		set_time_limit('0');
		
		//$this->output->enable_profiler(TRUE);
		
		echo "Proses ambil data dari pamlek";
		echo "<br>mulai ".date('Y-m-d H:i:s')."<br>";
		
		//ambil data di database setting
		$setting	= $this->M_pamlek->setting();
		//$pamlek_url	= $setting['url'];
		$pamlek_url	= dirname($_SERVER["SCRIPT_FILENAME"]).$setting['url'];
		$pamlek_max	= $setting['max_files'];
		
		//ambil data mana saja yang belum di proses
		$result = $this->M_pamlek->select_pamlek_files_limit($pamlek_max);
	
		$arr_registered_pamlek_files = array();
		foreach ($result->result_array() as $data) 
		{
			array_push($arr_registered_pamlek_files,$data['nama_file']);	 
		}
		
		//check server pamlek menyala
		if(is_dir($pamlek_url))
		{
			//scan file .tk dalam server ftp pamlek 
			$arr_scan_pamlek_files = scandir($pamlek_url);
			
			$pamlek_files = array();		
			foreach($arr_scan_pamlek_files as $file){
				if(in_array($file,$arr_registered_pamlek_files)){
					array_push($pamlek_files,$file);
				}
			}
					
			foreach($pamlek_files as $file){
				
				echo "<br>".$file."<br><br>";
			
				$rows = explode("\n",trim(file_get_contents($pamlek_url.$file)));
				;
				$i =1;
				$banyak_data=0;			
				
				//parsing data di file .tk
				$array_insert_data = array();
				foreach($rows as $row){
					if(!empty(trim($row))){	
					
						$banyak_data++;					
												
						$pisah = explode(" ",trim($row));	
							
					
						
						
						if(count($pisah)==6 and ((strlen($pisah[2])==19) OR (strlen($pisah[2])==39))){
							$insert_data = array(
									'no_pokok_convert'	=> $pisah[0],
									'no_pokok_original' => $pisah[1],
									'no_pokok' 			=> substr($pisah[1],1,4),
									'tapping_time'		=> $pisah[2],	
									'in_out'			=> $pisah[3],	
									'machine_id'		=> $pisah[4],	
									'tapping_type'		=> $pisah[5],	
									'file'				=> $file
								);	
								
							array_push($array_insert_data,$insert_data);
						}
						
						//insert data ke database dengan sistem batch per 1000
						if(count($rows)>1000 and $i==1000){						
							if(!empty($array_insert_data))
							{
								$this->M_pamlek->insert_data_batch($array_insert_data);
							}
							$array_insert_data = array();
							$i=1;
						}else
						{
							$i++;
						}
					}
					
				}		
				
				//insert sisa data sisa dari yg 1000
				if(!empty($array_insert_data))
				{
					$this->M_pamlek->insert_data_batch($array_insert_data);
				}
				
				//status di pamlek_file proses=1, sehingga tidak di proses lagi 
				//15/12/2020 - 7648 - tri wibowo, Jika di proses hari H maka proses nya tetap 0, untuk mengantisipasi data belum lengkap, jadi saat malam (H+1 jam 3 pagi) bisa ke running lagi file itu
				//12/12/2022 - 7648 - tri wibowo, Proses nya H+1 baru dijadikan 1 
				$tgl1    = date('Y-m-d'); // menentukan tanggal awal
				$tgl2    = date('Y-m-d', strtotime('-1 days', strtotime($tgl1)));
				$tgl3    = date('Y-m-d', strtotime('+1 days', strtotime($tgl1)));
				if(substr($file,0,10)==$tgl1 || substr($file,0,10)==$tgl2 || substr($file,0,10)==$tgl3)
				{
					$update_file = array(
					'proses'			=> '0',
					'baris_data' 		=> $banyak_data,
					'waktu_proses'		=> date('Y-m-d H:i:s')
					);	
				}else
				{
					$update_file = array(
					'proses'			=> '1',
					'baris_data' 		=> $banyak_data,
					'waktu_proses'		=> date('Y-m-d H:i:s')
					);	
				}
				
								
				$this->M_pamlek->update_files($file, $update_file);
				
				//pisah row data di .tk menjadi pertabel per tahun bulan
				$query = $this->M_pamlek->select_distinc_tapping_time_pamlek_data();
				
				$arr_tahun_bulan = array();
				$sudah ='';
				foreach ($query->result_array() as $data) 
				{			
					$tahun_bulan = substr($data['tapping_time'],0,7);
								
					if($tahun_bulan!=$sudah)
					{
						array_push($arr_tahun_bulan,str_replace("-","_",$tahun_bulan));		
					}
					$sudah = $tahun_bulan;
				}
		
				foreach($arr_tahun_bulan as $tahun_bulan){
												
					$table_name 		= "pamlek_data_".$tahun_bulan;
					
						
					if (!$this->M_pamlek->check_table_exist($table_name)) // table does not exist
					{							
						$this->M_pamlek->create_table_data($table_name);							
						$this->M_pamlek->truncate_table($table_name);				
						
					}
					
					/* HAPUS
					$check_ess_tabel_cico = $this->M_pamlek->check_ess_tabel_cico_exist($table_name);

					if($check_ess_tabel_cico['nama_tabel']==null) // masukin data ke ess_tabel_cico
					{						
						$data_tabel = array(
								'nama_tabel'		=> $table_name,
								'last_modified' 	=> date("Y-m-d H:i:s"),
								'proses'			=> '0',							
								'waktu_proses'		=> '0000-00-00 00:00:00'
							);	
						$this->M_pamlek->insert_ess_tabel_cico($data_tabel);
					}else // update
					{					
						
						$data_tabel = array(								
									'last_modified' 	=> date("Y-m-d H:i:s"),
									'proses'			=> '0',	
									'waktu_proses'		=> '0000-00-00 00:00:00'
								);	
						$this->M_pamlek->update_ess_tabel_cico($table_name, $data_tabel);
					}
					*/
						
					
					$copy = $this->M_pamlek->copy_isi($table_name,$tahun_bulan);
				}
				
				$this->M_pamlek->truncate_table('pamlek_data');
				}
		}else
		{
			$data_error['modul'] 		= "pamlek/pamlek/get_data"; 
			$data_error['error'] 		= "Gagal konek ke Server Pamlek"; 
			$data_error['status'] 		= "0";
			$data_error['created_at'] 	= date("Y-m-d H:i:s");
			$data_error['created_by'] 	= "scheduler";
			
			$this->M_pamlek->insert_error($data_error);
			echo "<br>status = ".$data_error['error'].", ".$data_error['modul'];
		}
		
		
		
		echo "<br>selesai ".date('Y-m-d H:i:s');
	}

	
	public function get_pamlek(){
		$this->get_files();
		$this->get_data();
		
		//insert ke tabel 'ess_status_proses_input', id proses = 2
        $this->db->insert('ess_status_proses_input', ['id_proses'=>2, 'waktu'=>date('Y-m-d H:i:s')]);
	}
/*	
	public function dump_file(){
		$setting	= $this->M_pamlek->setting();
		$dump_period	= $setting['dump_period'];
		var_dump(count($this->M_pamlek->select_ess_tabel_cico_not_dump()->result_array()));
	}
*/
/*
	public function dump_file_data($tahun,$bulan){
		$delimiter = ",";
		$newline = "\r\n";
		$enclosure = '"';
		
		$this->load->dbutil();
		$this->load->helper('file');

		$query = $this->M_pamlek->select_pamlek_data($tahun,$bulan);
		$csv_result = $this->dbutil->csv_from_result($query, $delimiter, $newline, $enclosure);
		
		$setting	= $this->M_pamlek->setting();
		$dump_folder	= $setting['dump_folder'];echo base_url()."pamlek_data_".$tahun."_".$bulan.".csv";
		write_file(".".$dump_folder."pamlek_data_".$tahun."_".$bulan.".csv", $csv_result);
	}
*/
}
