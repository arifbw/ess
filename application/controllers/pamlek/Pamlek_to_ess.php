<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pamlek_to_ess extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		$this->load->model('pamlek/m_pamlek_to_ess');
		
	}
	
	public function index()
	{
		redirect(base_url('dashboard'));
	}
	
	public function inisialisasi($pa_start,$pa_end,$pa_np_karyawan,$no_delay=null)
	{
		//run program selamanya untuk menghindari maximal execution
		set_time_limit('0');
		
		$start 	= $pa_start;
		$end	= $pa_end;
		
		//jika proses hari ini 
		if(strcmp($pa_start,"today")==0){
			$now = date("Y-m-d");		
			$start = date('Y-m-d', strtotime($now . ' -2 day'));
		}		
		if(strcmp($pa_end,"today")==0){
			$now = date("Y-m-d");
			$end = $now;
		}
		
		//02 03 2021 Tri Wibowo, Mengantisipasi belum ke running
		/*
		if(strcmp($pa_start,"now")==0)
		{
			$start = date("Y-m-d");			
		}		
		if(strcmp($pa_end,"now")==0)
		{
			$end = date("Y-m-d");
		}
		*/
		if(strcmp($pa_start,"now")==0 && strcmp($pa_end,"now")==0)
		{
			$date_asli 	= date('Y-m-d');
			$time  		= strtotime($date_asli);
			$day   		= date('d',$time);
			$month 		= date('m',$time);
			$year 		= date('Y',$time);
			$start		= $year.'-'.$month.'-01';
			$start 		= strtotime($start);
			$start 		= date('Y-m-d',$start);
			
			$end 		= date('Y-m-d');
			
			
			if($start>$end)
			{
				$start = date('Y-m-d');
			}
		}
		
		//16 03 2020 - Tri Wibowo, WFH Covid19
		
		
		$tanggal_awal_proses 	=$start;
		$tanggal_akhir_proses 	= $end;
		
		echo "===========================================================";
		echo "<br>Memulai Proses Pamlek_to_ess ".date("Y-m-d H:i:s");
		echo "<br>===========================================================<br>";
			
		//looping tanggal
		$tanggal_proses_root=$tanggal_awal_proses;
		while (strtotime($tanggal_proses_root) <= strtotime($tanggal_akhir_proses)){
			$tahun_bulan = substr($tanggal_proses_root,0,7);
			
			/*
				==================
				Hari Ini
				==================
			*/

			// data di chunk
			$all_data = $this->m_pamlek_to_ess->select_master_data($tahun_bulan,$tanggal_proses_root,$pa_np_karyawan);
			$semua_data = $all_data->result_array();

			/* 
				$total_all_data = count($semua_data);
				$jml_chunk = isset($no_delay) ? $total_all_data : 5;
				$master_chunk = array_chunk($semua_data, $jml_chunk);

				foreach ($master_chunk as $master1){
					foreach ($master1 as $data){
						$this->proses_data_employee($data,$tanggal_proses_root,$tahun_bulan);
					}

					if($total_all_data > 1 && !isset($no_delay)){
						sleep(3);
					}
				} 
			*/

			foreach ($semua_data as $data){
				$this->proses_data_employee($data,$tanggal_proses_root,$tahun_bulan);
			}
			
			$tanggal_proses_root = date ("Y-m-d", strtotime("+1 day", strtotime($tanggal_proses_root)));//looping tambah 1 date
		}
		
		echo "<br>===========================================================";
		echo "<br>Selesai Proses Pamlek_to_ess ".date("Y-m-d H:i:s");
		echo "<br>===========================================================";	
		
		/* 	
			////////////////////////////////////
			//-- Hanya untuk di development --//
			///////////////////////////////////
			
			$this->load->database();
			$queries = $this->db->queries;
			$query_times = $this->db->query_times;

			// Prepare an array to hold the query data
			$profiler_data = [];

			foreach ($queries as $key => $query) {
				$profiler_data[] = [
					'query' => $query,
					'execution_time' => $query_times[$key],
				];
			}

			$json_data = json_encode($profiler_data, JSON_PRETTY_PRINT);
			$file_path = APPPATH . 'logs/profiler_log_' . date('Y-m-d_H-i-s') . '.json';
			file_put_contents($file_path, $json_data);

			// Continue with your normal output
			$initial_output = $this->output->get_output();
			echo $initial_output;
		*/
	}

	private function proses_data_employee($data, $tgl, $tahun_bulan){
		//jika ada substitution
		$dws_fix 					= null;
		$tanggal_start_dws_fix		= null;
		$start_time_fix				= null;
		$tanggal_end_dws_fix		= null;
		$end_time_fix				= null;
		$start_break_fix			= null;
		$end_break_fix				= null;
		$np_karyawan				= $data['np_karyawan'];
		$tanggal_proses				= $tgl;
		$tanggal_proses_root		= $tgl;
		$lintas_hari_pulang = null;

		//j971
		// if($np_karyawan == '7245'){
		// if($nama_karyawan == '6814'){
		$substitution = $this->m_pamlek_to_ess->get_substitution($np_karyawan,$tanggal_proses);
		if($substitution['date']){
			$lintas_hari_masuk = $substitution['lintas_hari_masuk'];
			$lintas_hari_pulang = $substitution['lintas_hari_pulang'];
			
			if($lintas_hari_masuk==1){
				$tanggal_start_dws_fix= date('Y-m-d', strtotime($tanggal_proses . ' +1 day'));
			}else{
				$tanggal_start_dws_fix=$tanggal_proses_root;
			}
			
			if($lintas_hari_pulang==1){
				$tanggal_end_dws_fix= date('Y-m-d', strtotime($tanggal_proses . ' +1 day'));
			}else{
				$tanggal_end_dws_fix=$tanggal_proses_root;
			}
			
			
			$dws_fix 					= $substitution['dws'];
			$tanggal_start_dws_fix		= $tanggal_start_dws_fix;
			$start_time_fix				= $substitution['start_time'];
			$tanggal_end_dws_fix		= $tanggal_end_dws_fix;
			$end_time_fix				= $substitution['end_time'];
			$start_break_fix			= $substitution['dws_break_start_time'];
			$end_break_fix				= $substitution['dws_break_end_time'];
		}
					
		$sekarang = array(
			'np_karyawan'			=> $data['np_karyawan'],
			'personel_number'		=> $data['personnel_number'],
			'nama_karyawan'			=> $data['nama'],
			'kode_unit'				=> $data['kode_unit'],
			'nama_unit'				=> $data['nama_unit'],
			'nama_jabatan'			=> $data['nama_jabatan'],
			'tanggal_dws'			=> $data['tanggal_dws'],
			'dws'					=> $data['dws'],
			'tanggal_start_dws'		=> $data['tanggal_start_dws'],
			'start_time'			=> $data['start_time'],
			'tanggal_end_dws'		=> $data['tanggal_end_dws'],
			'end_time'				=> $data['end_time'],
			'start_break'			=> $data['start_break'],
			'end_break'				=> $data['end_break'],
			'dws_fix'				=> $dws_fix,
			'tanggal_start_dws_fix'	=> $tanggal_start_dws_fix,
			'start_time_fix'		=> $start_time_fix,
			'tanggal_end_dws_fix'	=> $tanggal_end_dws_fix,
			'end_time_fix'			=> $end_time_fix,
			'start_break_fix'		=> $start_break_fix,
			'end_break_fix'			=> $end_break_fix,
			'action'				=> $data['action'],
			'tm_status'				=> $data['tm_status']
		);					
			
		$np_karyawan 			= $sekarang['np_karyawan'];
		$personel_number 		= $sekarang['personel_number'];
		$nama_karyawan 			= $sekarang['nama_karyawan'];
		$kode_unit 				= $sekarang['kode_unit'];
		$nama_unit 				= $sekarang['nama_unit'];
		$nama_jabatan 			= $sekarang['nama_jabatan'];
		$tanggal_dws 			= $sekarang['tanggal_dws'];
		$dws 					= $sekarang['dws'];
		$tanggal_start_dws 		= $sekarang['tanggal_start_dws'];
		$start_time 			= $sekarang['start_time'];
		$tanggal_end_dws 		= $sekarang['tanggal_end_dws'];
		$end_time 				= $sekarang['end_time'];
		$start_break 			= $sekarang['start_break'];
		$end_break 				= $sekarang['end_break'];
		$dws_fix 				= $sekarang['dws_fix'];
		$tanggal_start_dws_fix	= $sekarang['tanggal_start_dws_fix'];
		$start_time_fix			= $sekarang['start_time_fix'];
		$tanggal_end_dws_fix	= $sekarang['tanggal_end_dws_fix'];
		$end_time_fix			= $sekarang['end_time_fix'];
		$start_break_fix		= $sekarang['start_break_fix'];
		$end_break_fix			= $sekarang['end_break_fix'];
		$action					= $sekarang['action'];
		$tm_status				= $sekarang['tm_status'];	
		
		
		if($dws_fix!=null || $dws_fix!=''){
			$sekarang_dws=$dws_fix;
		}else{
			$sekarang_dws=$dws;
		}
		
		if($tanggal_start_dws_fix!=null || $tanggal_start_dws_fix!=''){
			$sekarang_tanggal_start_dws=$tanggal_start_dws_fix;
		}else{
			$sekarang_tanggal_start_dws=$tanggal_start_dws;
		}
		
		if($start_time_fix!=null || $start_time_fix!=''){
			$sekarang_start_time=$start_time_fix;
		}else{
			$sekarang_start_time=$start_time;
		}
		
		if($tanggal_end_dws_fix!=null || $tanggal_end_dws_fix!=''){
			$sekarang_tanggal_end_dws=$tanggal_end_dws_fix;
		}else{
			$sekarang_tanggal_end_dws=$tanggal_end_dws;
		}
		
		if($end_time_fix!=null || $end_time_fix!=''){
			$sekarang_end_time=$end_time_fix;
		}else{
			$sekarang_end_time=$end_time;
		}
		
		if($start_break_fix!=null || $start_break_fix!=''){
			$sekarang_start_break=$start_break_fix;
		}else{
			$sekarang_start_break=$start_break;
		}
		
		if($end_break_fix!=null || $end_break_fix!=''){
			$sekarang_end_break=$end_break_fix;
		}else{
			$sekarang_end_break=$end_break;
		}
		
		/*
		==================
		Kemarin
		==================
		*/
		//pakai tanggal start dws karena dimulainya tgl dws in nya
		$tanggal_proses_kemarin = date('Y-m-d', strtotime($tanggal_proses . ' -1 day'));
		$tahun_bulan_kemarin = substr($tanggal_proses_kemarin,0,7);
		
		$cico_kemarin = $this->m_pamlek_to_ess->select_cico($tahun_bulan_kemarin,$tanggal_proses_kemarin,$np_karyawan);
		$arr_cico_kemarin = array();

		foreach ($cico_kemarin->result_array() as $data_cico_kemarin) {
			if($data_cico_kemarin['tapping_fix_1']=='' || $data_cico_kemarin['tapping_fix_1']==null){
				$tapping_in = $data_cico_kemarin['tapping_time_1'] ;
			}else{
				$tapping_in = $data_cico_kemarin['tapping_fix_1'] ;	
			}
			
			if($data_cico_kemarin['tapping_fix_2']=='' || $data_cico_kemarin['tapping_fix_2']==null){
				$tapping_out = $data_cico_kemarin['tapping_time_2'] ;
			}else{
				$tapping_out = $data_cico_kemarin['tapping_fix_2'] ;	
			}		

			if($data_cico_kemarin['dws_in_tanggal_fix']=='' || $data_cico_kemarin['dws_in_tanggal_fix']==null){
				$dws_in_tanggal = $data_cico_kemarin['dws_in_tanggal'] ;
			}else{
				$dws_in_tanggal = $data_cico_kemarin['dws_in_tanggal_fix'] ;	
			}
			
			if($data_cico_kemarin['dws_in_fix']=='' || $data_cico_kemarin['dws_in_fix']==null){
				$dws_in = $data_cico_kemarin['dws_in'] ;
			}else{
				$dws_in = $data_cico_kemarin['dws_in_fix'] ;	
			}
			
			if($data_cico_kemarin['dws_out_tanggal_fix']=='' || $data_cico_kemarin['dws_out_tanggal_fix']==null){
				$dws_out_tanggal = $data_cico_kemarin['dws_out_tanggal'] ;
			}else{
				$dws_out_tanggal = $data_cico_kemarin['dws_out_tanggal_fix'] ;	
			}
			
			if($data_cico_kemarin['dws_out_fix']=='' || $data_cico_kemarin['dws_out_fix']==null){
				$dws_out = $data_cico_kemarin['dws_out'] ;
			}else{
				$dws_out = $data_cico_kemarin['dws_out_fix'] ;	
			}
			
			$arr_cico_kemarin = array(
				'np_karyawan'		=> $data_cico_kemarin['np_karyawan'],					
				'tapping_in'		=> $tapping_in,
				'tapping_out'		=> $tapping_out,
				'dws_name'			=> $data_cico_kemarin['dws_name'],
				'dws_tanggal'		=> $data_cico_kemarin['dws_tanggal'],
				'dws_in_tanggal'	=> $dws_in_tanggal,
				'dws_in'			=> $dws_in,
				'dws_out_tanggal'	=> $dws_out_tanggal,
				'dws_out'			=> $dws_out	
			);	
		}

		$master2 = $this->m_pamlek_to_ess->select_master_data($tahun_bulan_kemarin,$tanggal_proses_kemarin,$np_karyawan);	
		$arr_master_kemarin = array();	

		foreach ($master2->result_array() as $data_master2){
			//jika ada substitution
			$substitution = $this->m_pamlek_to_ess->get_substitution($np_karyawan,$tanggal_proses_kemarin);
			
			if($substitution['date']){					
				$lintas_hari_masuk = $substitution['lintas_hari_masuk'];
				$lintas_hari_pulang = $substitution['lintas_hari_pulang'];
				
				if($lintas_hari_masuk==1){
					$sub_tanggal_start_dws= date('Y-m-d', strtotime($tanggal_proses_kemarin . ' +1 day'));
				}else{
					$sub_tanggal_start_dws=$tanggal_proses_kemarin;
				}
				
				if($lintas_hari_pulang==1){
					$tanggal_end_dws= date('Y-m-d', strtotime($tanggal_proses_kemarin . ' +1 day'));
				}else{
					$tanggal_end_dws=$tanggal_proses_kemarin;
				}
				
					$dws 				= $substitution['dws'];
					$tanggal_start_dws	= $sub_tanggal_start_dws;
					$start_time			= $substitution['start_time'];
					$tanggal_end_dws	= $tanggal_end_dws;
					$end_time			= $substitution['end_time'];
					$start_break		= $substitution['dws_break_start_time'];
					$end_break			= $substitution['dws_break_end_time'];
			}else{
				$dws				= $data_master2['dws'];
				$tanggal_start_dws	= $data_master2['tanggal_end_dws'];
				$start_time			= $data_master2['start_time'];						
				$tanggal_end_dws	= $data_master2['tanggal_end_dws'];
				$end_time			= $data_master2['end_time'];
				$start_break		= $data_master2['start_break'];
				$end_break			= $data_master2['end_break'];
			}
												
			$arr_master_kemarin = array(
				'np_karyawan'		=> $data_master2['np_karyawan'],	
				'tapping_in'		=> $tanggal_proses_kemarin." ".'00:00:00',
				'tapping_out'		=> $tanggal_proses_kemarin." ".'23:59:59',	
				'dws_name'			=> $dws,
				'dws_tanggal'		=> $data_master2['tanggal_dws'],
				'dws_in_tanggal'	=> $tanggal_start_dws,
				'dws_in'			=> $start_time,
				'dws_out_tanggal'	=> $tanggal_end_dws,
				'dws_out'			=> $end_time			
			);				
		}	
		
		
		/*
		==================
		Besok
		==================
		*/
		//pakai tanggal start dws karena dimulainya tgl dws in nya
		$tanggal_proses_besok = date('Y-m-d', strtotime($tanggal_proses . ' +1 day'));
		$tahun_bulan_besok = substr($tanggal_proses_besok,0,7);
						
		$cico = $this->m_pamlek_to_ess->select_cico($tahun_bulan_besok,$tanggal_proses_besok,$np_karyawan);
		$arr_cico_besok = array();

		foreach ($cico->result_array() as $data_cico){					
			if($data_cico['tapping_fix_1']=='' || $data_cico['tapping_fix_1']==null){
				$tapping_in = $data_cico['tapping_time_1'] ;
			}else{
				$tapping_in = $data_cico['tapping_fix_1'] ;	
			}
			
			if($data_cico['tapping_fix_2']=='' || $data_cico['tapping_fix_2']==null){
				$tapping_out = $data_cico['tapping_time_2'] ;
			}else{
				$tapping_out = $data_cico['tapping_fix_2'] ;	
			}		

			if($data_cico['dws_in_tanggal_fix']=='' || $data_cico['dws_in_tanggal_fix']==null){
				$dws_in_tanggal = $data_cico['dws_in_tanggal'] ;
			}else{
				$dws_in_tanggal = $data_cico['dws_in_tanggal_fix'] ;	
			}
			
			if($data_cico['dws_in_fix']=='' || $data_cico['dws_in_fix']==null){
				$dws_in = $data_cico['dws_in'] ;
			}else{
				$dws_in = $data_cico['dws_in_fix'] ;	
			}					
			
			if($data_cico['dws_out_tanggal_fix']=='' || $data_cico['dws_out_tanggal_fix']==null){
				$dws_out_tanggal = $data_cico['dws_out_tanggal'] ;
			}else{
				$dws_out_tanggal = $data_cico['dws_out_tanggal_fix'] ;	
			}
			
			if($data_cico['dws_out_fix']=='' || $data_cico['dws_out_fix']==null){
				$dws_out = $data_cico['dws_out'] ;
			}else{
				$dws_out = $data_cico['dws_out_fix'] ;	
			}
			
			$arr_cico_besok = array(
				'np_karyawan'		=> $data_cico['np_karyawan'],					
				'tapping_in'		=> $tapping_in,
				'tapping_out'		=> $tapping_out,
				'dws_name'			=> $data_cico['dws_name'],
				'dws_tanggal'		=> $data_cico['dws_tanggal'],
				'dws_in_tanggal'	=> $dws_in_tanggal,
				'dws_in'			=> $dws_in,
				'dws_out_tanggal'	=> $dws_out_tanggal,
				'dws_out'			=> $dws_out	
			);	
		}

		$master3 = $this->m_pamlek_to_ess->select_master_data($tahun_bulan_besok,$tanggal_proses_besok,$np_karyawan);	
		$arr_master_besok = array();
		
		foreach ($master3->result_array() as $data_master3){
			//jika ada substitution
			$substitution = $this->m_pamlek_to_ess->get_substitution($np_karyawan,$tanggal_proses_besok);
			
			if($substitution['date']){					
				$lintas_hari_masuk = $substitution['lintas_hari_masuk'];
				$lintas_hari_pulang = $substitution['lintas_hari_pulang'];
				
				//tambahan untuk digunakan
				$asli_lintas_hari_masuk_besok = $lintas_hari_masuk;
				$asli_lintas_hari_pulang_besok = $lintas_hari_pulang;
				
				if($lintas_hari_masuk==1)
				{
					$sub_tanggal_start_dws= date('Y-m-d', strtotime($tanggal_proses_besok . ' +1 day'));
				}else
				{
					$sub_tanggal_start_dws=$tanggal_proses_besok;
				}
				
				if($lintas_hari_pulang==1)
				{
					$tanggal_end_dws= date('Y-m-d', strtotime($tanggal_proses_besok . ' +1 day'));
				}else
				{
					$tanggal_end_dws=$tanggal_proses_besok;
				}
				
					$dws 				= $substitution['dws'];
					$tanggal_start_dws	= $sub_tanggal_start_dws;
					$start_time			= $substitution['start_time'];
					$tanggal_end_dws	= $tanggal_end_dws;
					$end_time			= $substitution['end_time'];
					$start_break		= $substitution['dws_break_start_time'];
					$end_break			= $substitution['dws_break_end_time'];
			}else{
				$dws				= $data_master3['dws'];
				$tanggal_start_dws	= $data_master3['tanggal_end_dws'];
				$start_time			= $data_master3['start_time'];
				$tanggal_end_dws	= $data_master3['tanggal_end_dws'];
				$end_time			= $data_master3['end_time'];
				$start_break		= $data_master3['start_break'];
				$end_break			= $data_master3['end_break'];
			}
			
			$arr_master_besok = array(
				'np_karyawan'		=> $data_master3['np_karyawan'],	
				'tapping_in'		=> $tanggal_proses_besok." ".'00:00:00',
				'tapping_out'		=> $tanggal_proses_besok." ".'23:59:59',	
				'dws_name'			=> $dws,
				'dws_tanggal'		=> $data_master3['tanggal_dws'],
				'dws_in_tanggal'	=> $tanggal_start_dws,
				'dws_in'			=> $start_time,
				'dws_out_tanggal'	=> $tanggal_end_dws,
				'dws_out'			=> $end_time
				
			);											
		}
		/*
		==================
		Search Tapping 
		==================
		*/				
		$tanggal_dws		= $sekarang['tanggal_dws'];			
		$tabel_pamlek 		= "pamlek_data_".$tahun_bulan;
		$tabel_kemarin		= "pamlek_data_".$tahun_bulan_kemarin;
		
		//sekarang							
		$dws_in_sekarang		= $sekarang_tanggal_start_dws." ".$sekarang_start_time;
		$dws_out_sekarang		= $sekarang_tanggal_end_dws." ".$sekarang_end_time;
	
		//Libur				
		if($sekarang_tanggal_start_dws==$sekarang_tanggal_end_dws && $sekarang_start_time==$sekarang_end_time){
			$dws_in_sekarang		= $tanggal_proses." ".'00:00:00';
			//$dws_out_sekarang		= $tanggal_proses." ".'23:59:59';
			$dws_out_sekarang		= $tanggal_proses_besok." ".'00:00:00';
		}
		
		//kemarin			
		
		if($arr_master_kemarin==null || $arr_master_kemarin['dws_name']=='OFF'){
			//GILIR biar ga terlalu jauh
			//die($tanggal_proses_kemarin." x ".$sekarang_tanggal_end_dws);
			if($tanggal_proses_kemarin<$sekarang_tanggal_end_dws){
				$y = date('Y-m-d', strtotime($sekarang_tanggal_end_dws . ' -2 day'));
				$dws_out_kemarin	= $y." ".'23:59:59';
			}else {
				$dws_out_kemarin	= $tanggal_proses_kemarin." ".'12:00:00';	
			}
		} else{
			$tanggal_search_kemarin=$arr_master_kemarin['dws_out_tanggal'];
			$dws_out_kemarin	= $tanggal_search_kemarin." ".$arr_master_kemarin['dws_out'];
		}
		
				
		if($arr_cico_kemarin['tapping_out']){
			$tapping_out_kemarin= $arr_cico_kemarin['tapping_out']; //sudah ada tanggalnya di database
		}else{
			//$tapping_out_kemarin= $tanggal_proses_kemarin." ".'12:00:00';
			$tapping_out_kemarin = date('Y-m-d H:i:s',strtotime('-6 hour',strtotime($dws_in_sekarang)));
		}						
		
		//besok
		if($arr_master_besok['dws_in_tanggal']){
			if( $arr_master_besok['dws_name']!='OFF'){
				$dws_in_besok	= $arr_master_besok['dws_in_tanggal']." ".$arr_master_besok['dws_in'];
				//7648 Tri Wibowo 04-05-2020, menanggapi ketika di tanggal berikutnya ternyata dia tapping in dan tapping out, antisipasi keambil tapping out nya untuk data hari kemarin, seperti kasus 2020-04-28/2020-04-29/7762 yg di laporkan zana
				$dws_in_besok   = date('Y-m-d H:i:s',strtotime('-1 hour',strtotime($dws_in_besok)));
			}else{						
				$dws_in_besok	= $tanggal_proses_besok." ".'03:00:00';
										
				if($dws_out_sekarang>=$dws_in_besok){
					$dws_in_besok =  date('Y-m-d H:i:s',strtotime('+12 hour',strtotime($dws_in_besok)));
					
				}
			}
			
		} else {
				$dws_in_besok	= $tanggal_proses_besok." ".'00:00:00';
				
				if($dws_out_sekarang>=$dws_in_besok){
					$dws_in_besok =  date('Y-m-d H:i:s',strtotime('+12 hour',strtotime($dws_in_besok)));
				}
								
		}

		//18 03 2022, Tri Wibowo - 7648, ada beberapa case dia IN kemudian Out tidak sengaja, maka di buat -> Jika dws merupakan tidak lintas hari dan bukan hari libur, maka batasi ambil in out dari hari itu saja
		if(($sekarang_tanggal_start_dws==$sekarang_tanggal_end_dws) AND $sekarang_dws!='OFF'){
			$dws_out_kemarin =  $tanggal_dws." ".'00:00:00';
			$dws_out_sekarang = $tanggal_proses_besok." ".'00:00:00';
		}

		//08 03 2024, Robi Purnomo - J971, dws P032 dan P038 ada di hari 00:00 yang sama
		if($sekarang_dws == 'P032' || $sekarang_dws == 'P038'){
			$dws_out_kemarin =  date('Y-m-d H:i:s',strtotime('-3 hour',strtotime($tanggal_dws." ".'00:00:00')));
		}
		
		//jika hari libur, kemungkinan WL IN untuk hari berikutnya jadi sisain 3 jam, jadi IN nya cari yg tidak sebelum 3 jam tanggal berikutnya, kaya semisal sabtu WL, pasti dia kerja di minggu
		if($sekarang_dws=='OFF' && $arr_master_besok['dws_name']=='OFF'){
			$dws_out_sekarang =  $tanggal_dws." ".'21:00:00';
		}
		//end of 18 03 2022, Tri Wibowo - 7648
		
		// die();

		//start 30 04 2024, j971 robi purnomo mengantisipasi user tapping in kurang dari 6 jam sebelum dws in besok
		//dws_kemarin
		if ($sekarang_dws == 'OFF' && strtotime($arr_cico_besok['dws_in_tanggal']." ".$arr_cico_besok['dws_in']) == strtotime('+1 day', strtotime($tanggal_dws." ".'00:00:00'))){
			$dws_in_besok = date('Y-m-d H:i:s', strtotime($dws_in_besok . ' -6 hours'));
		}
		// dws hari ini
		if($arr_cico_kemarin['dws_name'] == 'OFF' && $dws_in == '00:00:00'){
			$dws_out_kemarin = date('Y-m-d H:i:s', strtotime($arr_cico_kemarin['dws_out_tanggal'] . ' ' . $arr_cico_kemarin['dws_out'] . ' -6 hours'));
		}
		//end
		
		//search tapping IN
		$search_tapping_in = $this->m_pamlek_to_ess->search_tapping_in($tanggal_dws,$np_karyawan,$tabel_pamlek,$tabel_kemarin,$dws_in_besok,$dws_out_kemarin,$tapping_out_kemarin,$dws_out_sekarang);
		
		if($search_tapping_in['tapping_time']==null){
			$tapping_in = $tapping_out_kemarin;
		}else{
			$tapping_in = $search_tapping_in['tapping_time'];
			
			//jika sudah ada $tapping in dan hari ini OFF dan besok off
			if($arr_master_besok['dws_name']=='OFF' &&  $sekarang_dws=='OFF'){
				$dws_in_besok =  date('Y-m-d H:i:s',strtotime('+18 hour',strtotime($tapping_in)));
			}
			//Tri Wibowo 7648, 05 02 2021 - Enhance ketika dws out hari ini sama dengan dws out besok, tambahkan 2 jam pada dws in besok sehingga "tapping out" dapat terdeteksi 
			else{
				$dws_in_besok =  date('Y-m-d H:i:s',strtotime('+2 hour',strtotime($dws_in_besok)));
			}
		}
		
		$tabel_pamlek_plus		= "pamlek_data_".$tahun_bulan_besok;
		

		//18 03 2022, Tri Wibowo - 7648, ada beberapa case dia IN kemudian Out tidak sengaja, maka di buat -> Jika dws merupakan tidak lintas hari dan bukan hari libur, maka batasi ambil in out dari hari itu saja
		if(($sekarang_tanggal_start_dws==$sekarang_tanggal_end_dws) AND $sekarang_dws!='OFF'){
			if($lintas_hari_pulang=='1')
			{
				$tanggal_proses_besok = date ("Y-m-d", strtotime("+1 day", strtotime($tanggal_proses_besok)));//tambah 1 date
				$dws_in_besok =  $tanggal_proses_besok." ".'00:00:00';	
			}
		}
		
		//jika hari libur
		if($sekarang_dws=='OFF'){
			$dws_in_besok =  $tanggal_proses_besok." ".'01:00:00';	
		}
		
		//jika besok bukan libur, kemungkinan mereka pulang dulu jadi dws in besok nya ga sampe di jam dws in besok, mengantisipasi salah tapping out tapping in
		if($arr_master_besok['dws_name']!='OFF'){		
			$dws_in_besok =  $arr_master_besok['dws_in_tanggal']." ".$arr_master_besok['dws_in'];
			$dws_in_besok =  date('Y-m-d H:i:s',strtotime('-2 hour',strtotime($dws_in_besok)));
		}
		
		//jika ada dws yang lintas hari tapi besok nya libur (jadi irisan)
		if($dws_in_sekarang==$dws_in_besok){
			$cari_lagi_dws_out_sekarang		= $sekarang_tanggal_end_dws." ".$sekarang_end_time;
			$dws_in_besok =  date('Y-m-d H:i:s',strtotime('+2 hour',strtotime($cari_lagi_dws_out_sekarang)));

			//jika OFF lebihin sampe 1 jam
			if($sekarang_dws=='OFF'){
				$dws_in_besok =  $tanggal_proses_besok." ".'01:00:00';	
			}
			
			//jika besok lintas hari juga, kemungkinan WL nya lintas hari juga
			//ini
			if($asli_lintas_hari_masuk_besok==1){
				$asli_dws_in_besok	= $arr_master_besok['dws_in_tanggal']." ".$arr_master_besok['dws_in'];
				
				$dws_in_besok =  date('Y-m-d H:i:s',strtotime('-2 hour',strtotime($asli_dws_in_besok)));
			}
		}
		
		//akhirnyaaaa tak definisikan 1 1 ajaaa soalnya pusing WKWKWKWKWKW
		//Jika dia DWS Biasa maka pake batas dws in besok nya aja
		if(
			$sekarang_dws=='P001' || 
			$sekarang_dws=='P002' || 
			$sekarang_dws=='P003' || 
			$sekarang_dws=='P004' ||
			$sekarang_dws=='P007' ||
			$sekarang_dws=='P010' ||
			$sekarang_dws=='P011' ||
			$sekarang_dws=='P012' ||
			$sekarang_dws=='P013' || 	
			$sekarang_dws=='P016' ||	
			$sekarang_dws=='P017' ||
			$sekarang_dws=='P018' ||	
			$sekarang_dws=='P021' 
		){
			$asli_dws_in_besok	= $arr_master_besok['dws_in_tanggal']." ".$arr_master_besok['dws_in'];
			
			//kurangi 2 jam agar jika salah tapping saat jam tersebut tidak mengganggu cico sebelumnya
			//12 12 2022 Jika dws name nya biasa (jkt dan karawang) maka dws in besok nya bisa di kurangi saja, jadi kalau ada yg salah tapping besok nya tidak kena
			$dws_in_besok =  date('Y-m-d H:i:s',strtotime('-3 hour',strtotime($asli_dws_in_besok)));

			//23 08 2022 Tri WIbowo 7648 - Jika DWS In Besok nya hari libur pasti dws in nya 00.00 jadi tidak usah di kurangi, soalnya ada yg lembur sampe jam 11 malem
			if($arr_master_besok['dws_name']=='OFF')
			{
				$dws_in_besok =  $asli_dws_in_besok;
			}
		}
		
		//end of 18 03 2022, Tri Wibowo - 7648

		// 28 maret 2024 robi purnomo j971, dws out melebihi dws in hari selanjutnya/ besok
		$dws_in_besok_pkb2024	= $arr_master_besok['dws_in_tanggal']." ".$arr_master_besok['dws_in'];
		$dws_out_sekarang_pkb2024 = $sekarang_tanggal_end_dws." ".$sekarang_end_time;

		if($dws_out_sekarang_pkb2024 > $dws_in_besok_pkb2024){
			$dws_in_besok = date('Y-m-d H:i:s',strtotime('3 hour',strtotime($dws_out_sekarang_pkb2024)));

			// echo $dws_in_besok;
			// die();
		}
		// end of 28 maret 2024 robi purnomo j971, dws out melebihi dws in hari selanjutnya/ besok
		
		//search tapping OUT
		$search_tapping_out = $this->m_pamlek_to_ess->search_tapping_out($tanggal_dws,$np_karyawan,$tabel_pamlek,$tabel_pamlek_plus,$dws_in_sekarang,$dws_in_besok,$tapping_out_kemarin,$tapping_in);
		
		//masukan data ke array
		$in_out = array(
			'np_karyawan'		=> $np_karyawan,
			'personel_number'	=> $personel_number,
			'nama'				=> $nama_karyawan,
			'kode_unit'			=> $kode_unit,
			'nama_unit'			=> $nama_unit,
			'nama_jabatan'		=> $nama_jabatan,
			
			'dws_tanggal'		=> $tanggal_dws,
			
			'dws_name'			=> $sekarang['dws'],
			'dws_in_tanggal'	=> $sekarang['tanggal_start_dws'],
			'dws_in'			=> $sekarang['start_time'],
			'dws_out_tanggal'	=> $sekarang['tanggal_end_dws'],
			'dws_out'			=> $sekarang['end_time'],
			'dws_break_start'	=> $sekarang['start_break'],
			'dws_break_end'		=> $sekarang['end_break'],								
			
			'dws_name_fix'			=> $sekarang['dws_fix'],
			'dws_in_tanggal_fix'	=> $sekarang['tanggal_start_dws_fix'],
			'dws_in_fix'			=> $sekarang['start_time_fix'],
			'dws_out_tanggal_fix'	=> $sekarang['tanggal_end_dws_fix'],
			'dws_out_fix'			=> $sekarang['end_time_fix'],
			'dws_break_start_fix'	=> $sekarang['start_break_fix'],
			'dws_break_end_fix'		=> $sekarang['end_break_fix'],
			
			'tapping_type_1'	=> $search_tapping_in['tapping_type'],
			'tapping_time_1'	=> $search_tapping_in['tapping_time'],
			'tapping_terminal_1'=> $search_tapping_in['machine_id'],
			'tapping_type_2'	=> $search_tapping_out['tapping_type'],
			'tapping_time_2'	=> $search_tapping_out['tapping_time'],
			'tapping_terminal_2'=> $search_tapping_out['machine_id'],
			
			'action'			=> $sekarang['action'],
			'tm_status'			=> $sekarang['tm_status'],
			
			'proses'			=> '0',
			'waktu_proses'		=> null
		);
					
		echo "<br>".$np_karyawan;
		echo "<br>".$tanggal_dws."<br>";

		//		var_dump($in_out);
		//		echo "<br><br>";		
		//		var_dump($in_out);
		//		echo "<br><br>";		
	
		//update atau insert tabel
		$tahun_bulan 	= str_replace('-','_',$tahun_bulan);
		$tabel_cico		= "ess_cico_".$tahun_bulan;
		if(!$this->m_pamlek_to_ess->check_table_exist($tabel_cico)){
			$this->m_pamlek_to_ess->create_table_cico($tabel_cico);
		}
		
		$check_cico = $this->m_pamlek_to_ess->check_cico($tabel_cico, $np_karyawan,$tanggal_dws);
		if($check_cico['id']) //jika sudah ada data
		{
			$in_out = array(
				'np_karyawan'		=> $np_karyawan,
				'personel_number'	=> $personel_number,
				'nama'				=> $nama_karyawan,
				'kode_unit'			=> $kode_unit,
				'nama_unit'			=> $nama_unit,
				'nama_jabatan'		=> $nama_jabatan,
				
				'dws_tanggal'		=> $tanggal_dws,
				
				'dws_name'			=> $sekarang['dws'],
				'dws_in_tanggal'	=> $sekarang['tanggal_start_dws'],
				'dws_in'			=> $sekarang['start_time'],
				'dws_out_tanggal'	=> $sekarang['tanggal_end_dws'],
				'dws_out'			=> $sekarang['end_time'],
				'dws_break_start'	=> $sekarang['start_break'],
				'dws_break_end'		=> $sekarang['end_break'],								
				
				'dws_name_fix'			=> $sekarang['dws_fix'],
				'dws_in_tanggal_fix'	=> $sekarang['tanggal_start_dws_fix'],
				'dws_in_fix'			=> $sekarang['start_time_fix'],
				'dws_out_tanggal_fix'	=> $sekarang['tanggal_end_dws_fix'],
				'dws_out_fix'			=> $sekarang['end_time_fix'],
				'dws_break_start_fix'	=> $sekarang['start_break_fix'],
				'dws_break_end_fix'		=> $sekarang['end_break_fix'],
				
				'tapping_type_1'	=> $search_tapping_in['tapping_type'],
				'tapping_time_1'	=> $search_tapping_in['tapping_time'],
				'tapping_terminal_1'=> $search_tapping_in['machine_id'],
				'tapping_type_2'	=> $search_tapping_out['tapping_type'],
				'tapping_time_2'	=> $search_tapping_out['tapping_time'],
				'tapping_terminal_2'=> $search_tapping_out['machine_id'],
				
				'action'			=> $sekarang['action'],
				'tm_status'			=> $sekarang['tm_status'],
											
				'proses'			=> '0',
				'waktu_proses'		=> null
			);
					
			$this->m_pamlek_to_ess->update_cico($tabel_cico, $np_karyawan, $tanggal_dws, $in_out);
		}else //jika belum ada data
		{
			$in_out['created_at'] = date('Y-m-d H:i:s'); # heru menambahkan line ini 2020-11-06 @10:20
			$this->m_pamlek_to_ess->insert_cico($tabel_cico,$in_out);
		}

		//Update log wina
		$job['table'] = $tabel_cico;
		$job['message'] = json_encode($check_cico);
		$job['created_at'] = date('Y-m-d H:i:s');
		$this->db->set($job)->insert('ess_log_job');
		//Update log wina
		
		//update id_substitution
		$this->load->model('kehadiran/m_perencanaan_jadwal_kerja');
		$this->m_perencanaan_jadwal_kerja->update_cico_substitution($np_karyawan,$tanggal_dws);						
		
		//update id_cuti
		$this->load->model('osdm/m_persetujuan_cuti_sdm');
		$this->m_persetujuan_cuti_sdm->update_cico_cuti($np_karyawan,$tanggal_dws);	
		
		//update id_lembur
		$this->load->model('lembur/m_pengajuan_lembur');
		$get_lembur['no_pokok'] = $np_karyawan;
		$get_lembur['tgl_dws'] = $tanggal_dws;
		$this->m_pengajuan_lembur->set_cico($get_lembur);
		//refresh lembur fix
		$check = $this->m_pengajuan_lembur->update_dws($np_karyawan, $tanggal_dws);
		
		
		//update id_perizinan
		$this->load->model('perizinan/m_perizinan');
		//$get_perizinan['np_karyawan'] = $np_karyawan;
		//$get_perizinan['tgl_dws'] = $tanggal_dws;
		//$this->m_perizinan->set_cico($get_perizinan);
		
		//  update id_perizinan script yang baru, perlu dicek dulu
		//START
		$date_in = ($sekarang['tanggal_start_dws_fix']!=NULL ? $sekarang['tanggal_start_dws_fix']:$sekarang['tanggal_start_dws']);
		$time_in = ($sekarang['start_time_fix']!=NULL ? $sekarang['start_time_fix']:$sekarang['start_time']);
		$date_out = ($sekarang['tanggal_end_dws_fix']!=NULL ? $sekarang['tanggal_end_dws_fix']:$sekarang['tanggal_end_dws']);
		$time_out = ($sekarang['end_time_fix']!=NULL ? $sekarang['end_time_fix']:$sekarang['end_time']);
		$get_perizinan = [
			'tahun_bulan'=>str_replace('-','_',substr($date_in,0,7)),
			'np_karyawan'=>$np_karyawan,
			'date_time_in'=>date('Y-m-d H:i:s', strtotime($date_in.' '.$time_in)),
			'date_time_out'=>date('Y-m-d H:i:s', strtotime($date_out.' '.$time_out))
		];
		
		$this->m_perizinan->update_cico($get_perizinan);
				
		//END 
		
		//update id_sppd
		$this->load->model('perjalanan_dinas/m_sppd');
		$get_sppd['np_karyawan'] = $np_karyawan;
		$get_sppd['tgl_dws'] = $tanggal_dws;
		$this->m_sppd->insert_to_cico($get_sppd);
	}

	public function check_anomaly($bulan, $start, $end){
		$date = DateTime::createFromFormat('m-Y', $bulan);
		$tahun_bulan = $date->format('Y_m');

		$data_checking = $this->m_pamlek_to_ess->checking_anomaly_kehadiran($tahun_bulan, $start, $end)->result_array();

		echo json_encode($data_checking);
	}
}
