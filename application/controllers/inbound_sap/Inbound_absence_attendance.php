<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inbound_absence_attendance extends CI_Controller {
	
	 
	function __construct()
	{
		parent::__construct();
		$this->load->helper(['fungsi_helper','tanggal_helper','karyawan_helper','perizinan_helper']);
		$this->load->model('inbound_sap/m_inbound_absence_attendance');
		
	}
	
	public function index()
	{
		redirect(base_url('dashboard'));
	}
	
	public function create_file($date=null, $np=null){
        if($date==null){
            echo 'Need parameters: Month (Y-m)';
        } else{
            //include helper file
            $this->load->helper('file');

            //run program selamanya untuk menghindari maximal execution
            //ini_set('MAX_EXECUTION_TIME', -1);
            set_time_limit('0');

           // $this->output->enable_profiler(TRUE);
            
            $msc = microtime(true);
            echo "<br>Proses Inisialisasi Data dari Tabel ESS PERIZINAN untuk dijadikan txt";
            echo "<br>Mulai ".date('Y-m-d H:i:s')."<br><br>";

            //membatasi yang di proses data yang sudah siap, yaitu bulan sebelumnya
            if($date=='today'){
                $date = date("Y-m", strtotime("-1 months"));
            }
            $tahun_bulan = str_replace('-', '_', $date);

            $tabel_cuti				= "ess_cuti";
            $tabel_perizinan		= "ess_perizinan_".$tahun_bulan;
            $tabel_cico     		= "ess_cico_".$tahun_bulan;
            $tabel_inbound_absence	= "ESS_ABSENCE_ATTENDANCE_".$tahun_bulan;
            
            if(@$np){
				//cuti dipindah daily ke inbound_absence_daily
                //ambil data di tabel ess cuti sesuai parameter
                //$data_tabel_cuti = $this->m_inbound_absence_attendance->get_data_cuti($tabel_cuti, $date, $np);

                //ambil data di tabel ess perizinan sesuai parameter
                $data_tabel_perizinan = $this->m_inbound_absence_attendance->get_data_perizinan($tabel_perizinan, $np);

                //ambil data di tabel ess cico sesuai parameter
                $data_tabel_cico = $this->m_inbound_absence_attendance->get_data_cico($tabel_cico, $np);
				
				//ambil data di tabel ess cico wfh sesuai parameter
                $data_tabel_cico_wfh = $this->m_inbound_absence_attendance->get_data_cico_wfh($tabel_cico, $np);

                //ambil data di tabel ess cico group by np
                //$data_tabel_cuti_bersama = $this->m_inbound_absence_attendance->get_data_cuti_bersama($tabel_cico, $np);
				
				//ambil data di tabel ess cico max dws dari 'tidak hadir ke tanggal awal'
                $get_data_max_dws_tanggal_awal = $this->m_inbound_absence_attendance->get_data_max_dws_tanggal_awal($tabel_cico, $np);
				
				
				$get_tabel_sppd = $this->m_inbound_absence_attendance->select_sppd($date, $np);

				$data_terlambat_tanpa_izin = $this->m_inbound_absence_attendance->get_data_cico_tanpa_izin($tabel_cico, $tabel_perizinan, $np);
             				
            } else{
				//cuti dipindah daily ke inbound_absence_daily
			    //ambil data di tabel ess cuti sesuai parameter
                //$data_tabel_cuti = $this->m_inbound_absence_attendance->get_data_cuti($tabel_cuti, $date);

                //ambil data di tabel ess perizinan sesuai parameter
                $data_tabel_perizinan = $this->m_inbound_absence_attendance->get_data_perizinan($tabel_perizinan);

                //ambil data di tabel ess cico sesuai parameter
                $data_tabel_cico = $this->m_inbound_absence_attendance->get_data_cico($tabel_cico);
				
				//ambil data di tabel ess cico wfh sesuai parameter
                $data_tabel_cico_wfh = $this->m_inbound_absence_attendance->get_data_cico_wfh($tabel_cico);

                //ambil data di tabel ess cico group by np
                //$data_tabel_cuti_bersama = $this->m_inbound_absence_attendance->get_data_cuti_bersama($tabel_cico);
				
				//ambil data di tabel ess cico max dws dari 'tidak hadir ke tanggal awal'
                $get_data_max_dws_tanggal_awal = $this->m_inbound_absence_attendance->get_data_max_dws_tanggal_awal($tabel_cico);
				
				$get_tabel_sppd = $this->m_inbound_absence_attendance->select_sppd($date);

				$data_terlambat_tanpa_izin = $this->m_inbound_absence_attendance->get_data_cico_tanpa_izin($tabel_cico, $tabel_perizinan);
			
            }
			
		
			
			
			
			
				/*
				$arr_cuti = array();
				foreach ($data_tabel_cuti->result_array() as $data) 
				{	
					$absence_type = explode('|', $data['absence_type']);
					$arr = array(
						'np_karyawan'		=> $data['np_karyawan'],
						'personel_number'	=> $data['personel_number'],
						'info_type'			=> $absence_type[0],
						'absence_type'		=> $absence_type[1],
						'start_date' 		=> $data['start_date'],
						'end_date' 			=> $data['end_date'],
						'start_time' 		=> '',
						'end_time' 			=> ''
					);

					array_push($arr_cuti,$arr);	
				}
				*/

				$arr_perizinan = array();
				foreach ($data_tabel_perizinan->result_array() as $data) 
				{
					$arr = array(
						'np_karyawan'		=> $data['np_karyawan'],
						'personel_number'	=> $data['personel_number'],
						'info_type'			=> $data['info_type'],
						'absence_type'		=> $data['absence_type'],
						// 'start_date' 		=> $data['start_date'],
						// 'end_date' 			=> $data['end_date'],
						// 'start_time' 		=> $data['start_time'],
						// 'end_time' 			=> $data['end_time']
					);
					
					//TIDAK LEMPAR KETIKA		
					
					$check_tanggal='';
					if(@$data['start_date'])
					{
						$check_tanggal=$data['start_date'];
					}else
					{
						$check_tanggal=$data['end_date'];
					}
					
					$tm_action = tm_status_erp_master_data($data['np_karyawan'],$check_tanggal);	
					if($tm_action['action']=='ZI' || //skorsing dengan gaji
					$tm_action['action']=='ZL' ||  //sakit berkepanjangan
					($tm_action['action']=='ZN' && $tm_action['tm_status']=='0') || //MPP
					($tm_action['action']=='' || $tm_action['tm_status']==null) || //tidak ada data
					$tm_action['wfh'] == '1') //7648 Tri Wibowo, 01 04 2020 work from home
					{
						//do nothing
					}else
					{
					
					
					
						if (empty($data['start_date'])) {
							$end_date_time  = $data['end_date'].' '.$data['end_time'];
							$data_date      = $this->m_inbound_absence_attendance->check_date_absence($end_date_time, $data['np_karyawan']);
							if (empty($data_date)) {
								continue;
							}else{
								if (!empty($data_date->start_date_fix)) {
									$arr['start_date']  = substr($data_date->start_date_fix, 0, 10);
									$arr['start_time']  = substr($data_date->start_date_fix, 11);
								}else{
									$arr['start_date']  = substr($data_date->start_date, 0, 10);
									$arr['start_time']  = substr($data_date->start_date, 11);
								}
								$arr['end_date'] = $data['end_date'];
								$arr['end_time'] = $data['end_time'];
							}
						}else if (empty($data['end_date'])) {
							$start_date_time    = $data['start_date'].' '.$data['start_time']; 
							$data_date          = $this->m_inbound_absence_attendance->check_date_absence($start_date_time, $data['np_karyawan']);
							if (empty($data_date)) {
								continue;
							}else{
								
								$arr['start_date'] = $data['start_date'];
								$arr['start_time'] = $data['start_time'];
								$date_time_start = $data['start_date']." ".$data['start_time'];
								
								
								if (!empty($data_date->end_date_fix)) {
									
									//kurangi satu menit lagi, biar SAP bisa menerima ketika full day absen ada jeda satu menit
									$end_date_fix = $data_date->end_date_fix;									
									$time_end_date_fix = strtotime($end_date_fix);
									$time_end_date_fix = $time_end_date_fix - (1 * 60);
													
									$end_date_fix = date("Y-m-d H:i:s", $time_end_date_fix);

									$arr['end_date']  = substr($end_date_fix, 0, 10);
									$arr['end_time']  = substr($end_date_fix, 11);
								}else{
									
									//kurangi satu menit lagi, biar SAP bisa menerima ketika full day absen ada jeda satu menit
									$end_date = $data_date->end_date;									
																								
									$time_end_date = strtotime($end_date);
									$time_end_date = $time_end_date - (1 * 60);
									
									$end_date = date("Y-m-d H:i:s", $time_end_date);
																
									$arr['end_date']  = substr($end_date, 0, 10);
									$arr['end_time']  = substr($end_date, 11);
								}
								
							}
						}else{
							//check apakah DWS out <= izin out nya
							$start_date_time    = $data['start_date'].' '.$data['start_time']; 
							$data_date          = $this->m_inbound_absence_attendance->check_date_absence($start_date_time, $data['np_karyawan']);						
							
							$end_date_dipakai = $data['end_date'];
							$end_time_dipakai = $data['end_time'];
							
							if (empty($data_date)) {
								continue;
							}else{												
								if (!empty($data_date->end_date_fix)) {								
									//kurangi satu menit lagi, biar SAP bisa menerima ketika full day absen ada jeda satu menit
									$end_date_fix = $data_date->end_date_fix;									
									$time_end_date_fix = strtotime($end_date_fix);
									$time_end_date_fix = $time_end_date_fix - (1 * 60);
													
									$end_date_fix = date("Y-m-d H:i:s", $time_end_date_fix);

									$arr['dws_end_date']  = substr($end_date_fix, 0, 10);
									$arr['dws_end_time']  = substr($end_date_fix, 11);
								}else{
									
									//kurangi satu menit lagi, biar SAP bisa menerima ketika full day absen ada jeda satu menit
									$end_date = $data_date->end_date;									
																								
									$time_end_date = strtotime($end_date);
									$time_end_date = $time_end_date - (1 * 60);
									
									$end_date = date("Y-m-d H:i:s", $time_end_date);
																
									$arr['dws_end_date']  = substr($end_date, 0, 10);
									$arr['dws_end_time']  = substr($end_date, 11);
								}
								
								$dws_end 	= $arr['dws_end_date']." ".$arr['dws_end_time']; //dari dws
								$izin_end 	= $data['end_date']." ".$data['end_time']; //dari izin
								if($izin_end>=$dws_end) //jika izin out melebihi dws out
								{					
									$end_date_dipakai  = substr($dws_end, 0, 10);
									$end_time_dipakai  = substr($dws_end, 11);
								}	
														
							}
							
										
							
							$arr['start_date'] = $data['start_date'];
							$arr['start_time'] = $data['start_time'];
							$arr['end_date'] = $end_date_dipakai;
							$arr['end_time'] = $end_time_dipakai;	
									
						}
						
						
						
						
						
						$arr['poin'] 				= '';
						$arr['transaction_type']	= '';				
						
						//PASTIKAN HASILNYA TIDAK ADA YANG
						
						if(
							((substr($arr['start_time'],0,5)>=substr($arr['end_time'],0,5)) && ($arr['start_date']>=$arr['end_date'])) //nilai hh:mm from >= hh:mm date
						)
						{
							//tidak melakukan apa apa
						}else
						{
							
							
							/*CHECK KALI ADA YG KEPOTONG DIA GA KEVALIDASI DIBAWAH*/
							//libur
							$is_libur = $this->m_inbound_absence_attendance->cek_libur($arr['start_date']);
							if($is_libur==false) //jika libur
							{
								$is_libur=true;
							}else
							{
								$is_libur=false;
							}
							
							//cuti bersama
							$is_cuti_bersama = $this->m_inbound_absence_attendance->is_cuti_bersama($arr['start_date']);
							if($is_cuti_bersama==true) //jika cuti bersama
							{
								$is_cuti_bersama=true;
							}else
							{
								$is_cuti_bersama=false;
							}
					
							//pastikan ketika melempar attendance dia tidak dinas
							$check_dinas = $this->m_inbound_absence_attendance->select_cico_by_np_dws($data['np_karyawan'],$arr['start_date']);				
							if(
								($check_dinas['id_sppd']==null||$check_dinas['id_sppd']=='') && //Check tidak sppd 
								($check_dinas['id_cuti']==null||$check_dinas['id_cuti']=='') && //check tidak cuti
								(($check_dinas['dws_name_fix']!='OFF' && $check_dinas['dws_name_fix']!='' && $check_dinas['dws_name_fix']!=null) || ($check_dinas['dws_name']!='OFF' && $check_dinas['dws_name_fix']=='' && $check_dinas['dws_name_fix']==null)) && //Check DWS tidak OFF
								($is_libur==false) && //tidak di libur
								($is_cuti_bersama==false) //tidak di cuti bersama
							)
							{
								
								
								
								
								if($arr['start_date']<$arr['end_date']) //jika lintas hari
								{
									$arr_lintas_in = array(
										'np_karyawan'		=> $data['np_karyawan'],
										'personel_number'	=> $data['personel_number'],
										'info_type'			=> $data['info_type'],
										'absence_type'		=> $data['absence_type'],
										'start_date' 		=> $arr['start_date'],
										'end_date' 			=> $arr['start_date'],
										'start_time' 		=> $arr['start_time'],
										'end_time' 			=> '24:00:00'
									);
									//var_dump($arr_lintas_in);
									//echo '<br><br>';
									array_push($arr_perizinan,$arr_lintas_in);
									
									$arr_lintas_out = array(
										'np_karyawan'		=> $data['np_karyawan'],
										'personel_number'	=> $data['personel_number'],
										'info_type'			=> $data['info_type'],
										'absence_type'		=> $data['absence_type'],
										'start_date' 		=> $arr['end_date'],
										'end_date' 			=> $arr['end_date'],
										'start_time' 		=> '00:00:00',
										'end_time' 			=> $arr['end_time']
									);
									//var_dump($arr_lintas_out);
									array_push($arr_perizinan,$arr_lintas_out);
									
									
								}else
								{
									array_push($arr_perizinan,$arr);
								}
								
								
								
								
							}
							
							
							
							
							
							
							
							
							
							
							
							
							
							
						
							
						}
						
						
					}//end of TIDAK LEMPAR
				}
				
				//tidak lempar jika
				//AND tm_status!='9'
				//AND action NOT IN ('ZI','ZL','ZN')
				$arr_cico = array();
				foreach ($data_tabel_cico->result_array() as $data) {
					$arr_datang_telat_dan_pulang_awal = array();
					$start_time = '';
					$end_time = '';
					$info_type = '';
					$tanggal_in_dws = str_replace('-','',explode(' ', $data['tanggal_in_dws'])[0]);
					//AB
					if($data['tapping_masuk']==NULL && $data['tapping_keluar']==NULL){
						$info_type = '2001';
						$absence_type = '4000';
						$poin	= '';
					} else 
					//TM
					if($data['tapping_masuk']==NULL && $data['tapping_keluar']!=NULL){
						$info_type = '2010';
						$absence_type = '3140';
						$poin	= '4';
					} else 
					//TK
					if($data['tapping_keluar']==NULL && $data['tapping_masuk']!=NULL){
						$info_type = '2010';
						$absence_type = '3150';
						$poin	= '4';
					} else
					// ATU
					if(date('Y-m-d H:i', strtotime($data['tapping_masuk'])) > date('Y-m-d H:i', strtotime($data['jadwal_masuk'])) || date('Y-m-d H:i', strtotime($data['tapping_keluar'])) < date('Y-m-d H:i', strtotime($data['jadwal_keluar']))){
						/*
						$info_type = '2001';
						$absence_type = '4040';
						$poin	= '';
						$start_time = date('Hi', strtotime($data['tapping_masuk']));
						$end_time = date('Hi', strtotime($data['tapping_keluar']));
						*/

						// datang telat
						if(date('Y-m-d H:i', strtotime($data['tapping_masuk'])) > date('Y-m-d H:i', strtotime($data['jadwal_masuk']))){
							$info_type = '2001';
							$absence_type = '4040';
							$poin	= '';
							$start_time = date('Hi', strtotime($data['jadwal_masuk']));
							$end_time = date('Hi', strtotime($data['tapping_masuk']));

							$arr_datang_telat_dan_pulang_awal[] = array(
								'np_karyawan'		=> $data['np_karyawan'],
								'personel_number'	=> $data['personel_number'],
								'info_type'			=> $info_type,
								'absence_type'		=> $absence_type,
								'start_date' 		=> $tanggal_in_dws,
								'end_date' 			=> $tanggal_in_dws,
								'start_time' 		=> $start_time,
								'end_time' 			=> $end_time,
								'poin' 				=> $poin,
								'transaction_type' 	=> ''
							);
						}

						// pulang awal
						if(date('Y-m-d H:i', strtotime($data['tapping_keluar'])) < date('Y-m-d H:i', strtotime($data['jadwal_keluar']))){
							$info_type = '2001';
							$absence_type = '4040';
							$poin	= '';
							$start_time = date('Hi', strtotime($data['tapping_keluar']));
							$end_time = date('Hi', strtotime($data['jadwal_keluar']));

							$arr_datang_telat_dan_pulang_awal[] = array(
								'np_karyawan'		=> $data['np_karyawan'],
								'personel_number'	=> $data['personel_number'],
								'info_type'			=> $info_type,
								'absence_type'		=> $absence_type,
								'start_date' 		=> $tanggal_in_dws,
								'end_date' 			=> $tanggal_in_dws,
								'start_time' 		=> $start_time,
								'end_time' 			=> $end_time,
								'poin' 				=> $poin,
								'transaction_type' 	=> ''
							);
						}
					}
					$arr = array(
						'np_karyawan'		=> $data['np_karyawan'],
						'personel_number'	=> $data['personel_number'],
						'info_type'			=> $info_type,
						'absence_type'		=> $absence_type,
						'start_date' 		=> $tanggal_in_dws,
						'end_date' 			=> $tanggal_in_dws,
						'start_time' 		=> $start_time,
						'end_time' 			=> $end_time,
						'poin' 				=> $poin,
						'transaction_type' 	=> ''
					);
					
					//TIDAK LEMPAR KETIKA		
					$tm_action = tm_status_erp_master_data($data['np_karyawan'],$tanggal_in_dws);		
					if($tm_action['action']=='ZI' || //skorsing dengan gaji
					$tm_action['action']=='ZL' ||  //sakit berkepanjangan
					($tm_action['action']=='ZN' && $tm_action['tm_status']=='0') || //MPP
					($tm_action['action']=='' || $tm_action['tm_status']==null) || //tidak ada data
					$tm_action['wfh'] == '1') //7648 Tri Wibowo, 01 04 2020 work from home
					{
						//do nothing
					}else
					{
						if($arr_datang_telat_dan_pulang_awal!=[]) $arr_cico = array_merge($arr_cico, $arr_datang_telat_dan_pulang_awal);
						// else if($start_time!='' && $end_time!='') $arr_cico[] = $arr;
						else if($info_type!='') $arr_cico[] = $arr;
					} //end of TIDAK LEMPAR
				}
				
				// foreach ($data_terlambat_tanpa_izin->result_array() as $key => $data) {
				// 	$arr_datang_telat_dan_pulang_awal = array();
				// 	$start_time = '';
				// 	$end_time = '';
				// 	$tanggal_in_dws = str_replace('-','',explode(' ', $data['tanggal_in_dws'])[0]);
				// 	// ATU
				// 	if($data['tapping_masuk'] > $data['jadwal_masuk'] || $data['tapping_keluar'] < $data['jadwal_keluar']){
				// 		/*
				// 		$info_type = '2001';
				// 		$absence_type = '4040';
				// 		$poin	= '';
				// 		$start_time = date('Hi', strtotime($data['tapping_masuk']));
				// 		$end_time = date('Hi', strtotime($data['tapping_keluar']));
				// 		*/
	
				// 		// datang telat
				// 		if($data['tapping_masuk'] > $data['jadwal_masuk']){
				// 			$info_type = '2001';
				// 			$absence_type = '4040';
				// 			$poin	= '';
				// 			$start_time = date('Hi', strtotime($data['jadwal_masuk']));
				// 			$end_time = date('Hi', strtotime($data['tapping_masuk']));
	
				// 			$arr_datang_telat_dan_pulang_awal[] = array(
				// 				'np_karyawan'		=> $data['np_karyawan'],
				// 				'personel_number'	=> $data['personel_number'],
				// 				'info_type'			=> $info_type,
				// 				'absence_type'		=> $absence_type,
				// 				'start_date' 		=> $tanggal_in_dws,
				// 				'end_date' 			=> $tanggal_in_dws,
				// 				'start_time' 		=> $start_time,
				// 				'end_time' 			=> $end_time,
				// 				'poin' 				=> $poin,
				// 				'transaction_type' 	=> ''
				// 			);
				// 		}
	
				// 		// pulang awal
				// 		if($data['tapping_keluar'] < $data['jadwal_keluar']){
				// 			$info_type = '2001';
				// 			$absence_type = '4040';
				// 			$poin	= '';
				// 			$start_time = date('Hi', strtotime($data['tapping_keluar']));
				// 			$end_time = date('Hi', strtotime($data['jadwal_keluar']));
	
				// 			$arr_datang_telat_dan_pulang_awal[] = array(
				// 				'np_karyawan'		=> $data['np_karyawan'],
				// 				'personel_number'	=> $data['personel_number'],
				// 				'info_type'			=> $info_type,
				// 				'absence_type'		=> $absence_type,
				// 				'start_date' 		=> $tanggal_in_dws,
				// 				'end_date' 			=> $tanggal_in_dws,
				// 				'start_time' 		=> $start_time,
				// 				'end_time' 			=> $end_time,
				// 				'poin' 				=> $poin,
				// 				'transaction_type' 	=> ''
				// 			);
				// 		}
				// 	}
					
				// 	//TIDAK LEMPAR KETIKA		
				// 	$tm_action = tm_status_erp_master_data($data['np_karyawan'],$tanggal_in_dws);		
				// 	if($tm_action['action']=='ZI' || //skorsing dengan gaji
				// 	$tm_action['action']=='ZL' ||  //sakit berkepanjangan
				// 	($tm_action['action']=='ZN' && $tm_action['tm_status']=='0') || //MPP
				// 	($tm_action['action']=='' || $tm_action['tm_status']==null) /*|| //tidak ada data
				// 	$tm_action['wfh'] == '1'*/) //7648 Tri Wibowo, 01 04 2020 work from home
				// 	{
				// 		//do nothing
				// 	}else
				// 	{
				// 		if($arr_datang_telat_dan_pulang_awal!=[]) $arr_cico = array_merge($arr_cico, $arr_datang_telat_dan_pulang_awal);						
				// 	} //end of TIDAK LEMPAR
				// }
				
				/*7648 Tri Wibowo, 01 04 2020 - Ambil Data WFH*/
				//tidak lempar jika
				//AND tm_status!='9'
				//AND action NOT IN ('ZI','ZL','ZN')
				$arr_cico_wfh = array();
				foreach ($data_tabel_cico_wfh->result_array() as $data) {
					
					$tanggal_in_dws = str_replace('-','',explode(' ', $data['tanggal_in_dws'])[0]);
										
					if($data['is_dinas_luar']=='1')
					{
						$info_type = '2002';
						$absence_type = '6100';
						
						$arr_cico_wfh = array(
						'np_karyawan'		=> $data['np_karyawan'],
						'personel_number'	=> $data['personel_number'],
						'info_type'			=> $info_type,
						'absence_type'		=> $absence_type,
						'start_date' 		=> $tanggal_in_dws,
						'end_date' 			=> $tanggal_in_dws,
						'start_time' 		=> '',
						'end_time' 			=> '',
						'poin' 				=> '',
						'transaction_type' 	=> ''
						);
					}else
					{
						$info_type = '2001';
						$absence_type = '7000';
					
						$arr_cico_wfh = array(
						'np_karyawan'		=> $data['np_karyawan'],
						'personel_number'	=> $data['personel_number'],
						'info_type'			=> $info_type,
						'absence_type'		=> $absence_type,
						'start_date' 		=> $tanggal_in_dws,
						'end_date' 			=> $tanggal_in_dws,
						'start_time' 		=> '',
						'end_time' 			=> '',
						'poin' 				=> '',
						'transaction_type' 	=> ''
						);
					}
					
					
					//gabungin ke arr cico
					array_push($arr_cico,$arr_cico_wfh);						
									
				}
				
				/*$arr_cuti_bersama = array();
				foreach ($data_tabel_cuti_bersama->result_array() as $data) {
					//get tanggal from mst_cuti_bersama
					$get_tanggal_cuti_bersama = $this->m_inbound_absence_attendance->get_data_tanggal_cuti_bersama()->result_array();
					foreach($get_tanggal_cuti_bersama as $row){
						$arr = array(
							'np_karyawan'		=> $data['np_karyawan'],
							'personel_number'	=> $data['personel_number'],
							'info_type'			=> '2001',
							'absence_type'		=> '1020',
							'start_date' 		=> $row['tanggal'],
							'end_date' 			=> $row['tanggal'],
							'start_time' 		=> '',
							'end_time' 			=> ''
						);

						array_push($arr_cuti_bersama,$arr);
					}
				}*/
				
				
				
				//Olah array master data 
				$i = 0; //INDEKS ROW
				/*
				foreach ($arr_cuti as $data) 
				{
					$np_karyawan		= $data['np_karyawan'];
					$personel_number	= $data['personel_number'];
					$info_type			= $data['info_type'];
					$absence_type		= $data['absence_type'];
					$start_date			= $data['start_date'];
					$end_date			= $data['end_date'];
					$start_time			= $data['start_time'];
					$end_time			= $data['end_time'];

					$start_date = str_replace('-', '', $start_date);
					$end_date = str_replace('-', '', $end_date);

					$arr_data[$i] = array(
						'np_karyawan'		=> $np_karyawan,
						'personel_number'	=> $personel_number,
						'info_type'			=> $info_type,
						'absence_type'		=> $absence_type,
						'start_date'		=> $start_date,
						'end_date'			=> $end_date,
						'start_time'		=> $start_time,
						'end_time'			=> $end_time
					);

					$i++;
				}
				*/
				
				$np_karyawan_sebelum 	= '';
				$end_time_sebelum 		= '';
				
				
				foreach ($arr_perizinan as $data) 
				{		
					//libur
					$is_libur = $this->m_inbound_absence_attendance->cek_libur($data['start_date']);
					if($is_libur==false) //jika libur
					{
						$is_libur=true;
					}else
					{
						$is_libur=false;
					}
					
					//cuti bersama
					$is_cuti_bersama = $this->m_inbound_absence_attendance->is_cuti_bersama($data['start_date']);
					if($is_cuti_bersama==true) //jika cuti bersama
					{
						$is_cuti_bersama=true;
					}else
					{
						$is_cuti_bersama=false;
					}
			
					//pastikan ketika melempar attendance dia tidak dinas
					$check_dinas = $this->m_inbound_absence_attendance->select_cico_by_np_dws($data['np_karyawan'],$data['start_date']);				
					if(
						($check_dinas['id_sppd']==null||$check_dinas['id_sppd']=='') && //Check tidak sppd 
						($check_dinas['id_cuti']==null||$check_dinas['id_cuti']=='') && //check tidak cuti
						(($check_dinas['dws_name_fix']!='OFF' && $check_dinas['dws_name_fix']!='' && $check_dinas['dws_name_fix']!=null) || ($check_dinas['dws_name']!='OFF' && $check_dinas['dws_name_fix']=='' && $check_dinas['dws_name_fix']==null)) && //Check DWS tidak OFF
						($is_libur==false) && //tidak di libur
						($is_cuti_bersama==false) //tidak di cuti bersama
					)
					{
						
						
						
						$np_karyawan		= $data['np_karyawan'];
						$personel_number	= $data['personel_number'];
						$info_type			= $data['info_type'];
						$absence_type		= $data['absence_type'];
						$start_date			= $data['start_date'];
						$end_date			= $data['end_date'];
						$start_time			= $data['start_time'];
						$end_time			= $data['end_time'];
						
						$start_date_ori		=$start_date;
						$start_time_ori		=$start_time;
						$end_date_ori		=$end_date;
						$end_time_ori		=$end_time;

						$start_date = str_replace('-', '', $start_date);
						$end_date = str_replace('-', '', $end_date);
						$start_time = str_replace(':', '', $start_time);
						$end_time = str_replace(':', '', $end_time);
						
						//izin datang terlambat
						if ($info_type == '2001' && $absence_type == '5000') {
							$start_date2 = new DateTime($start_date.' '.$start_time);
							$end_date2 = new DateTime($end_date.' '.$end_time);
							$since_start = $start_date2->diff($end_date2);

							if ($since_start->h > 1 && $since_start->i > 0) {
								$start_date2->add(new DateInterval("PT2H"));
								$new_time = $start_date2->format('Y-m-d H:i:s');
								$new_time = str_replace(':', '', substr($new_time, 11));
								$arr_data[$i] = array(
									'np_karyawan'		=> $np_karyawan,
									'personel_number'	=> $personel_number,
									'info_type'			=> $info_type,
									'absence_type'		=> $absence_type,
									'start_date'		=> $start_date,
									'end_date'			=> $end_date, 
									'start_time'		=> substr($start_time,0,4), //hanya hhmm
									'end_time'			=> substr($new_time,0,4), //hanya hhmm
									'poin' 				=> '',
									'transaction_type' 	=> ''
								);
								$i++;
								$arr_data[$i] = array(
									'np_karyawan'		=> $np_karyawan,
									'personel_number'	=> $personel_number,
									'info_type'			=> '2001',
									'absence_type'		=> '4040',
									'start_date'		=> $start_date,
									'end_date'			=> $end_date,
									'start_time'		=> substr($new_time,0,4), //hanya hhmm
									'end_time'			=> substr($end_time,0,4), //hanya hhmm
									'poin' 				=> '',
									'transaction_type' 	=> ''
								);
							}else{
								$arr_data[$i] = array(
									'np_karyawan'		=> $np_karyawan,
									'personel_number'	=> $personel_number,
									'info_type'			=> $info_type,
									'absence_type'		=> $absence_type,
									'start_date'		=> $start_date,
									'end_date'			=> $end_date,
									'start_time'		=> substr($start_time,0,4), //hanya hhmm
									'end_time'			=> substr($end_time,0,4), //hanya hhmm
									'poin' 				=> '',
									'transaction_type' 	=> ''
								);
							}
						}else{
							
							//ketika jam akhir nya 00:00:00 maka dibuat 24:00:00 tanggal sebelumnya, untuk menghindari jam 00:00:00 ada cuti / cuti bersama
							if($end_time=="000000")
							{
								$end_date = $start_date;
								$end_time='240000';
							}
							
							
							//memastikan tidak tubrukan dengan perizinan setelahnya
							$np_karyawan_sekarang 	= $np_karyawan;
							$start_time_sekarang 	= $start_date_ori.' '.$start_time_ori;
							$end_time_sekarang 		= $end_date_ori.' '.$end_time_ori;
							
							if($end_time_sebelum!='' && //pastikan bukan data awal
								($np_karyawan_sekarang==$np_karyawan_sebelum) && //pastikan np nya sama
								($start_time_sekarang<=$end_time_sebelum) //berarti tubrukan							
							)
							{
								$start_time_sekarang_atur = date('Y-m-d H:i:s',strtotime('+1 minutes',strtotime($end_time_sebelum))); //pakai end time nya kemarin tambah 1 menit
								
								$pisah_start = explode(' ',$start_time_sekarang_atur);										
								$start_date = str_replace('-', '', $pisah_start[0]);								
								$start_time = str_replace(':', '', $pisah_start[1]);
															
								if($end_time_sekarang<=$end_time_sebelum)
								{
									$end_time_sekarang_atur = date('Y-m-d H:i:s',strtotime('+1 minutes',strtotime($start_time_sekarang_atur))); //pakai start time nya sekarang tambah 1 menit
									
									$pisah_end = explode(' ',$end_time_sekarang_atur);
									$end_date = str_replace('-', '', $pisah_end[0]);
									$end_time = str_replace(':', '', $pisah_end[1]);
								}
								
							}
							
							
							$arr_data[$i] = array(
								'np_karyawan'		=> $np_karyawan,
								'personel_number'	=> $personel_number,
								'info_type'			=> $info_type,
								'absence_type'		=> $absence_type,
								'start_date'		=> $start_date,
								'end_date'			=> $end_date,
								'start_time'		=> substr($start_time,0,4), //hanya hhmm
								'end_time'			=> substr($end_time,0,4), //hanya hhmm
								'poin' 				=> '',
								'transaction_type' 	=> ''
							);
							
					
							//buat nge check data setelahnya apakah tubrukan
							$np_karyawan_sebelum 	= $np_karyawan;
							$end_time_sebelum		= $end_date_ori.' '.$end_time_ori;
							
							
						}

						$i++;
						
					} //end of pastikan dia tidak dinas
					
				}
				
				foreach ($arr_cico as $data) 
				{
					
					//pastikan ketika melempar attendance dia tidak dinas
					$check_dinas = $this->m_inbound_absence_attendance->select_cico_by_np_dws($data['np_karyawan'],$data['start_date']);				
					if(($check_dinas['id_sppd']==null||$check_dinas['id_sppd']=='') && ($check_dinas['id_cuti']==null||$check_dinas['id_cuti']==''))
					{				
					
						$np_karyawan		= $data['np_karyawan'];
						$personel_number	= $data['personel_number'];
						$info_type			= $data['info_type'];
						$absence_type		= $data['absence_type'];
						$start_date			= $data['start_date'];
						$end_date			= $data['end_date'];
						$start_time			= $data['start_time'];
						$end_time			= $data['end_time'];
						$poin				= $data['poin'];
						
						//ketika jam akhir nya 00:00:00 maka dibuat 24:00:00 tanggal sebelumnya, untuk menghindari jam 00:00:00 ada cuti / cuti bersama
						if($end_time=="000000")
						{
							$end_date = $start_date;
							$end_time='240000';
						}
							
						$arr_data[$i] = array(
							'np_karyawan'		=> $np_karyawan,
							'personel_number'	=> $personel_number,
							'info_type'			=> $info_type,
							'absence_type'		=> $absence_type,
							'start_date'		=> $start_date,
							'end_date'			=> $end_date,
							'start_time'		=> substr($start_time,0,4), //hanya hhmm
							'end_time'			=> substr($end_time,0,4), //hanya hhmm
							'poin' 				=> $poin,
							'transaction_type' 	=> ''
						);

						$i++;
					
					} //end of pastikan dia tidak dinas
				}
				
				
				//Memotong Potongan 50 % dan 100 %
				//tidak lempar jika
				//AND tm_status!='9'
				//AND action NOT IN ('ZI','ZL','ZN')
				$potong 					= 0;
				$potong_akumulasi 			= 0;
				$potong_akumulasi_sebelum	= 0;
				$np_karyawan 				= false;
				$personel_number 			= false;
				$np_karyawan_sebelum 		= false; 
				$personel_number_sebelum 	= false; 
				$cetak						= 0;
				
				foreach ($get_data_max_dws_tanggal_awal->result_array() as $data) 
				{
					
					//04 05 2023, Tri Wibowo 7648, case cubes di bulan februari dibuat pake perhitungan php karena hasil dari myql dan php + 1 month itu berbeda
					$batas_plus_1_month = date('Y-m-d', strtotime($data['tidak_hadir_tanggal_awal'] . ' +1 month'));
					$data['batas'] = date('Y-m-d', strtotime($batas_plus_1_month . ' -1 day'));


					//TIDAK LEMPAR KETIKA
					$np_karyawan 			= $data['np_karyawan'];
					$personel_number 		= $data['personel_number'];
					$potong 				= 0;
					
					$tanggal_in_dws = $data['dws_tanggal'];
					$tm_action = tm_status_erp_master_data($data['np_karyawan'],$tanggal_in_dws);	
					if($tm_action['action']=='ZI' || //skorsing dengan gaji
					$tm_action['action']=='ZL' ||  //sakit berkepanjangan
					($tm_action['action']=='ZN' && $tm_action['tm_status']=='0') || //MPP
					($tm_action['action']=='' || $tm_action['tm_status']==null)) //tidak ada data
					{
						//do nothing
					}else
					{
						
						if($data['tidak_hadir_ke']>=10) //jika lebih dari 10 hari ada kemungkinan potong 100 ada kemungkinan potong 50
						{
							
							if($data['dws_tanggal']>=$data['batas']) // jika langsung ketemu
							{
								if(substr($data['dws_tanggal'],5,2)==substr($data['batas'],5,2)) //jika ketemu dalam satu bulan, maka 50% nya sudah terpotong bulan lalu
								{
									$potong = 50;
									
									//7648 - Tri Wibowo - 24 02 2021 - Check Apakah bulan itu dia tidak berangkat full
									$check_full_tidak_berangkat = $this->m_inbound_absence_attendance->check_full_tidak_berangkat($np_karyawan,$data['dws_tanggal']);
										
									if($check_full_tidak_berangkat=='1')
									{
										$potong = 100;
									}
									//end of 7648 - Tri Wibowo - 24 02 2021
									
								}else
								{
									$potong = 100;
								}
							}else //jika hari libur
							{
								$sudah_potong = false;
								$dws_tanggal = $data['dws_tanggal'];
								do //looping ketika besoknya off
								{
									$dws_besok 	= date('Y-m-d', strtotime($dws_tanggal.' +1 day'));
									$cico_besok = $this->m_inbound_absence_attendance->select_cico_by_np_dws($data['np_karyawan'],$dws_besok);
									
									if($cico_besok['dws_tanggal']>=$data['batas'] && $sudah_potong==false && (($cico_besok['dws_name']=='OFF' && ($cico_besok['dws_name_fix']=='' || $cico_besok['dws_name_fix']==null)) || $cico_besok['dws_name_fix']=='OFF'))
									{
																			
										if(substr($cico_besok['dws_tanggal'],5,2)==substr($data['batas'],5,2))//jika ketemu dalam satu bulan, maka 50% nya sudah terpotong bulan lalu
										{
											$potong = 50;
											
											//7648 - Tri Wibowo - 24 02 2021 - Check Apakah bulan itu dia tidak berangkat full
											$check_full_tidak_berangkat = $this->m_inbound_absence_attendance->check_full_tidak_berangkat($np_karyawan,$data['dws_tanggal']);
												
											if($check_full_tidak_berangkat=='1')
											{
												$potong = 100;
											}
											//end of 7648 - Tri Wibowo - 24 02 2021
										}else
										{
											$potong = 100;
										}
											
									
										$sudah_potong	= true;
									}
									
									$dws_tanggal 	= $dws_besok;
									
								}				
								while(($cico_besok['dws_name']=='OFF' && ($cico_besok['dws_name_fix']=='' || $cico_besok['dws_name_fix']==null)) || $cico_besok['dws_name_fix']=='OFF');
								
								//update 25 july 2019
								$tidak_hadir_max 	= $data['tidak_hadir_ke'];
								$tidak_hadir_min	= $data['tidak_hadir_ke_min'];
																								
								if($tidak_hadir_min<=10 && $sudah_potong==false) //pastikan bulan sebelumnya tidak terpotong "10 hari"
								{
									$potong = 50;
								}
																
							}
							
							
						}
						
							if($np_karyawan_sebelum == false) //jika data np pertama pertama
							{								
								$potong_akumulasi 		= $potong;							
							}else
							if(($np_karyawan_sebelum == $np_karyawan) && $np_karyawan_sebelum != false) //jika ada dua potongan dengan np sama
							{
								$potong_akumulasi 	= $potong_akumulasi+$potong;							
							}else
							if(($np_karyawan_sebelum != $np_karyawan) && $np_karyawan_sebelum != false) //jika sudah ganti np, berarti data np sebelumnya di cetak
							{
								$potong_akumulasi_sebelum 	= $potong_akumulasi; //potongan np yg akan di cetak	
								$potong_akumulasi 			= $potong;	 //potongan np yg baru
								$cetak						= 1;
							}
													
							if($cetak==1)
							{
								
								if($potong_akumulasi_sebelum>=100) //yg di cetak np karyawan sebelum
								{
									//yg dilempar karyawan sebelumnya
									$i_np_karyawan		= $np_karyawan_sebelum;
									$i_personel_number		= $personel_number_sebelum;
									$i_info_type				= '0015';
									$i_absence_type			= '7110';
									$i_start_date				= date('Y-m-t', strtotime($data['dws_tanggal']));
									$i_end_date				= date('Y-m-t', strtotime($data['dws_tanggal']));
									
									$i_start_date = str_replace('-', '', $i_start_date);
									$i_end_date 	= str_replace('-', '', $i_end_date);
									
									$i_start_time			= '';
									$i_end_time			= '';
									$i_poin				= '2';

									$arr_data[$i] = array(
										'np_karyawan'		=> $i_np_karyawan,
										'personel_number'	=> $i_personel_number,
										'info_type'			=> $i_info_type,
										'absence_type'		=> $i_absence_type,
										'start_date'		=> $i_start_date,
										'end_date'			=> $i_end_date,
										'start_time'		=> substr($i_start_time,0,4), //hanya hhmm
										'end_time'			=> substr($i_end_time,0,4), //hanya hhmm
										'poin'				=> $i_poin,							
										'transaction_type' 	=> ''
									);
									
								

									$i++;
								}else
								if($potong_akumulasi_sebelum==50) //yg di cetak np karyawan sebelum
								{
									//yg dilempar karyawan sebelumnya
									$i_np_karyawan		= $np_karyawan_sebelum;
									$i_personel_number	=  $personel_number_sebelum;
									$i_info_type			= '0015';
									$i_absence_type		= '7110';
									$i_start_date			= date('Y-m-t', strtotime($data['dws_tanggal']));
									$i_end_date			= date('Y-m-t', strtotime($data['dws_tanggal']));
									
									$i_start_date = str_replace('-', '', $i_start_date);
									$i_end_date 	= str_replace('-', '', $i_end_date);						
									
									$i_start_time			= '';
									$i_end_time			= '';
									$i_poin				= '1';

									$arr_data[$i] = array(
										'np_karyawan'		=> $i_np_karyawan,
										'personel_number'	=> $i_personel_number,
										'info_type'			=> $i_info_type,
										'absence_type'		=> $i_absence_type,
										'start_date'		=> $i_start_date,
										'end_date'			=> $i_end_date,
										'start_time'		=> substr($i_start_time,0,4), //hanya hhmm
										'end_time'			=> substr($i_end_time,0,4), //hanya hhmm
										'poin'				=> $i_poin,							
										'transaction_type' 	=> ''
									);
									
									
									
									$i++;
								}
								
								
								$cetak						=0;						
								$potong_akumulasi_sebelum 	=0;
								
															
								
							}
						
							$np_karyawan_sebelum		= $np_karyawan;
							$personel_number_sebelum	= $personel_number;
					
					} //end of TIDAK LEMPAR
				
				}
				
				//sisa jika sudah tidak ada lagi looping data dari fungsi looping diatas, cetak data terakhir
				if($np_karyawan!=false)
				{
							if($potong_akumulasi>=100) //yg di cetak np karyawan ini
								{
									//yg dilempar karyawan sisa nya
									$i_np_karyawan		= $np_karyawan;
									$i_personel_number	= $personel_number;
									$i_info_type			= '0015';
									$i_absence_type		= '7110';
									$i_start_date			= date('Y-m-t', strtotime($data['dws_tanggal']));
									$i_end_date			= date('Y-m-t', strtotime($data['dws_tanggal']));
									
									$i_start_date = str_replace('-', '', $i_start_date);
									$i_end_date 	= str_replace('-', '', $i_end_date);
									
									$i_start_time			= '';
									$i_end_time			= '';
									$i_poin				= '2';

									$arr_data[$i] = array(
										'np_karyawan'		=> $i_np_karyawan,
										'personel_number'	=> $i_personel_number,
										'info_type'			=> $i_info_type,
										'absence_type'		=> $i_absence_type,
										'start_date'		=> $i_start_date,
										'end_date'			=> $i_end_date,
										'start_time'		=> substr($i_start_time,0,4), //hanya hhmm
										'end_time'			=> substr($i_end_time,0,4), //hanya hhmm
										'poin'				=> $i_poin,							
										'transaction_type' 	=> ''
									);
									
									$cetak				=0;
									$np_karyawan 		= false;
									$personel_number 	= false;
									$potong 			=0;
									$potong_akumulasi 	=0;

									$i++;
								}else
								if($potong_akumulasi==50) //yg di cetak np karyawan ini
								{									
									//yg dilempar karyawan sisa nya
									$i_np_karyawan		= $np_karyawan;
									$i_personel_number	=  $personel_number;
									$i_info_type			= '0015';
									$i_absence_type		= '7110';
									$i_start_date			= date('Y-m-t', strtotime($data['dws_tanggal']));
									$i_end_date			= date('Y-m-t', strtotime($data['dws_tanggal']));
									
									$i_start_date = str_replace('-', '', $i_start_date);
									$i_end_date 	= str_replace('-', '', $i_end_date);						
									
									$i_start_time			= '';
									$i_end_time			= '';
									$i_poin				= '1';

									$arr_data[$i] = array(
										'np_karyawan'		=> $i_np_karyawan,
										'personel_number'	=> $i_personel_number,
										'info_type'			=> $i_info_type,
										'absence_type'		=> $i_absence_type,
										'start_date'		=> $i_start_date,
										'end_date'			=> $i_end_date,
										'start_time'		=> substr($i_start_time,0,4), //hanya hhmm
										'end_time'			=> substr($i_end_time,0,4), //hanya hhmm
										'poin'				=> $i_poin,							
										'transaction_type' 	=> ''
									);
									
									$cetak				=0;
									$np_karyawan 		= false;
									$personel_number 	= false;
									$potong 			=0;
									$potong_akumulasi 	=0;

									$i++;
								}
				}
				
				
				
				
				
				
				
				
				   //ATTENDANCE
				  

					//olah data		
					$tampil='';

					if($get_tabel_sppd->num_rows()>0){
						foreach(@$get_tabel_sppd->result_array() as $data) {
							
							//7648 Tri Wibowo 8 Juni 2020, SPPD Online
							//Ketika SPPD tujuan online tidak ada absen = wfh
							//Ketika SPPD tujuan online ada absen = wfo
							$tujuan = $data['tujuan'];
							$tujuan = strtoupper($tujuan);
							
							$wfo = $this->m_inbound_absence_attendance->select_cico_by_np_dws($data['np_karyawan'],$data['dws_tanggal']);
							
							$tapping_time_1 			= $wfo['tapping_time_1'];
							$tapping_time_2 			= $wfo['tapping_time_2'];
							$tapping_fix_1 				= $wfo['tapping_fix_1'];
							$tapping_fix_2 				= $wfo['tapping_fix_2'];
							$id_sppd 					= $wfo['id_sppd'];
							$tapping_fix_approval_status= $wfo['tapping_fix_approval_status'];
															
							
							//TIDAK LEMPAR KETIKA		
							$tm_action = tm_status_erp_master_data($data['np_karyawan'],$data['dws_tanggal']);			
							if($tm_action['action']=='ZI' || //skorsing dengan gaji
							$tm_action['action']=='ZL' ||  //sakit berkepanjangan
							($tm_action['action']=='ZN' && $tm_action['tm_status']=='0') || //MPP
							($tm_action['action']=='' || $tm_action['tm_status']==null) || //tidak ada data
							($wfo['wfh'] == '1')) //7648 Tri Wibowo, 01 04 2020 work from home
							{								
								//sppd online tapi dia input absen WFH
								if($wfo['wfh'] == '1' && $wfo['tapping_fix_approval_status']=='1')
								{
									$tanggal_in_dws = str_replace('-','',explode(' ', $data['dws_tanggal'])[0]);
									$info_type = '2001';
									$absence_type = '7000';
									
									$arr_data[$i] = array(
									'np_karyawan'		=> $data['np_karyawan'],
									'personel_number'	=> $data['personel_number'],
									'info_type'			=> $info_type,
									'absence_type'		=> $absence_type,
									'start_date' 		=> $tanggal_in_dws,
									'end_date' 			=> $tanggal_in_dws,
									'start_time' 		=> '',
									'end_time' 			=> '',
									'poin' 				=> '',
									'transaction_type' 	=> ''
									);
								
									$i++;
								}							
							}else
							{				
						
								//SPPD online tapi tidak absen, maka dia wfh
								if(
									$tujuan == "ONLINE" && 
									$id_sppd=='1' &&
									(
										( //tidak ada presensi slide
											($tapping_time_1=='' || $tapping_time_1==null || $tapping_time_1=="0000-00-00 00:00:00")
											||
											($tapping_time_2=='' || $tapping_time_2==null || $tapping_time_2=="0000-00-00 00:00:00")
										)
										||
										( //tidak ada presensi perubahan kehadiran
											($tapping_fix_approval_status=='1' && ($tapping_fix_1=='' || $tapping_fix_1==null || $tapping_fix_1=="0000-00-00 00:00:00"))
											||
											($tapping_fix_approval_status=='1' && ($tapping_fix_2=='' || $tapping_fix_2==null || $tapping_fix_2=="0000-00-00 00:00:00"))
										)
									) 
								)
								{
									$tanggal_in_dws = str_replace('-','',explode(' ', $data['dws_tanggal'])[0]);
									$info_type = '2001';
									$absence_type = '7000';
									
									//cetak sppd online
									$arr_data[$i] = array(
										'np_karyawan'		=> $data['np_karyawan'],
										'personel_number'	=> $data['personel_number'],
										'info_type'			=> $info_type,
										'absence_type'		=> $absence_type,
										'start_date' 		=> $tanggal_in_dws,
										'end_date' 			=> $tanggal_in_dws,
										'start_time' 		=> '',
										'end_time' 			=> '',
										'poin' 				=> '',
										'transaction_type' 	=> ''
									);
									
									$i++;
										
								}else //sppd biasa, maka dia dinas
								{
									$biaya=0;	

									$arr_data[$i] = array(
										'np_karyawan'		=> $data['np_karyawan'],
										'personel_number'	=> $data['personel_number'],
										'info_type'			=> get_mst_attendance($data['tipe_perjalanan'], $biaya)['info_type'],
										'absence_type'		=> get_mst_attendance($data['tipe_perjalanan'], $biaya)['absence_type'],
										'start_date'		=> str_replace('-','',$data['dws_tanggal']),
										'end_date'			=> str_replace('-','',$data['dws_tanggal']),
										'start_time'		=> '',
										'end_time'			=> '',
										'poin'				=> '',							
										'transaction_type' 	=> ''
									);

									$i++;	
								}									
			
															
									/*		
										//looping tanggal dinas
										$tanggal_proses=$data['tgl_berangkat'];
										$tanggal_akhir_proses=$data['tgl_pulang'];
									
										while (strtotime($tanggal_proses) <= strtotime($tanggal_akhir_proses))
										{
											
											//pastikan ketika melempar DINAS dia tidak CUTI
											$check_dinas = $this->m_inbound_absence_attendance->select_cico_by_np_dws($data['np_karyawan'],$tanggal_proses);				
											if($check_dinas['id_cuti']==null||$check_dinas['id_cuti']=='')
											{
												
												$arr_data[$i] = array(
													'np_karyawan'		=> $data['np_karyawan'],
													'personel_number'	=> $data['personel_number'],
													'info_type'			=> get_mst_attendance($data['tipe_perjalanan'], $biaya)['info_type'],
													'absence_type'		=> get_mst_attendance($data['tipe_perjalanan'], $biaya)['absence_type'],
													'start_date'		=> str_replace('-','',$tanggal_proses),
													'end_date'			=> str_replace('-','',$tanggal_proses),
													'start_time'		=> '',
													'end_time'			=> '',
													'poin'				=> '',							
													'transaction_type' 	=> ''
												);

												$i++;
										
											}//end offpastikan ketika melempar DINAS dia tidak CUTI
											
											
											$tanggal_proses = date ("Y-m-d", strtotime("+1 day", strtotime($tanggal_proses)));//looping tambah 1 date
										}//end looping dinas						
									*/		
							} //end of TIDAK LEMPAR
						}
						
					}
					
					//cuti bersama dilempar saat nglempar pembayaran nya pake apa
					/*foreach ($arr_cuti_bersama as $data) {
						$np_karyawan		= $data['np_karyawan'];
						$personel_number	= $data['personel_number'];
						$info_type			= $data['info_type'];
						$absence_type		= $data['absence_type'];
						$start_date			= $data['start_date'];
						$end_date			= $data['end_date'];
						$start_time			= $data['start_time'];
						$end_time			= $data['end_time'];

						$arr_data[$i] = array(
							'np_karyawan'		=> $np_karyawan,
							'personel_number'	=> $personel_number,
							'info_type'			=> $info_type,
							'absence_type'		=> $absence_type,
							'start_date'		=> str_replace('-','',$start_date),
							'end_date'			=> str_replace('-','',$end_date),
							'start_time'		=> $start_time,
							'end_time'			=> $end_time
						);

						$i++;
					}*/
			
			
               
						
			
			
			

            //$hasil = 'NP_KARYAWAN'."\t".'PERSONNEL_NUMBER'."\t".'INFO_TYPE'."\t". 'ABSENCE_TYPE'."\t".'START_DATE'."\t".'END_DATE'."\t".'START_TIME'."\t".'END_TIME'."\t".'POIN'."\t".'TRANSACTION_TYPE'."\n";
            $hasil = '';
            for ($index=0; $index < $i; $index++) { 
                $hasil = $hasil."".$arr_data[$index]['np_karyawan']."\t".$arr_data[$index]['personel_number']."\t".$arr_data[$index]['info_type']."\t".$arr_data[$index]['absence_type']."\t".$arr_data[$index]['start_date']."\t".$arr_data[$index]['end_date']."\t".$arr_data[$index]['start_time']."\t".$arr_data[$index]['end_time']."\t".$arr_data[$index]['poin']."\t".$arr_data[$index]['transaction_type']."\t"."\r\n";
            }
			
			$tabel_name	= "ESS_ABSENCE_ATTENDANCE";
			 
            if ( ! write_file(FCPATH . "inbound_sap/inbound_absence_attendance/$tabel_name.txt", $hasil)){
                echo 'Gagal membuat file txt';
            } else {
                echo "File $tabel_name.txt";
            }

            //echo "<br><br>Selesai ".date('Y-m-d H:i:s')."<br>";
            $msc = microtime(true)-$msc;
            echo "<br><br>Done. Execution time: $msc seconds.<br>";
            
            //insert ke tabel 'ess_status_proses_output', id proses = 13
            $this->db->insert('ess_status_proses_output', ['id_proses'=>13, 'waktu'=>date('Y-m-d H:i:s')]);
        }
	}
}