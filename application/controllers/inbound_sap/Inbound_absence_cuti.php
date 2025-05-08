<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inbound_absence_cuti extends CI_Controller {
	 
	function __construct()
	{
		parent::__construct();
        $this->load->helper(['tanggal_helper']);
		$this->load->helper('karyawan_helper');
		$this->load->model('inbound_sap/M_inbound_absence_cuti');
		
	}
	
	public function index()
	{
		redirect(base_url('dashboard'));
	}
    
    public function create_file($today=null, $np=null) {
		
		//jika proses hari ini 
        if(strcmp($today,"today")==0){
			$month = date("Y-m", strtotime("-1 months"));
		} else{
            $month = $today;
        }
        		
//		$today = date('Y-m-d', strtotime($today . ' -1 day'));
//		$tomorrow = date('Y-m-d', strtotime($tomorrow . ' -1 day'));
        
        if(!@$month){
            echo 'Need parameters and must be valid format: Month (Y-m)';
        } else{
            //include helper file
            $this->load->helper('file');

            //run program selamanya untuk menghindari maximal execution
            //ini_set('MAX_EXECUTION_TIME', -1);
            set_time_limit('0');

            //$this->output->enable_profiler(TRUE);		
            $msc = microtime(true);
            //echo '<br>Scanning data...<br>';
            //echo 'Start : '.date('Y-m-d H:i:s').'<br><br>';
            echo "<br>Proses data di tabel ess_cuti untuk dijadikan txt";
            echo "<br>mulai ".date('Y-m-d H:i:s')."<br><br>";
        
//            $today 		= date('Y-m-d', strtotime($today));
//            $tomorrow 	= date('Y-m-d', strtotime($tomorrow));

            $table_name = '';
			
			$pisah_sebelum 		= explode('-',$month);
			$tahun_sebelum 		= $pisah_sebelum[0];
			$bulan_sebelum 		= $pisah_sebelum[1];
			
			$date_start	= $tahun_sebelum."-".$bulan_sebelum."-01";
			
			//$setelah = date('Y-m', strtotime('+1 months', strtotime($month)));
			//$date_end	= $setelah."-10";
			$date_end = date('Y-m-t', strtotime($date_start));
			
			//olah data		
            $tampil='';
		
            while (strtotime($date_start) <= strtotime($date_end)) {
                $date=$date_start;
				
//				echo "<br>--".$date."--<br>"; 	
                //ambil data
                if(@$np){
                    $hasil 				= $this->M_inbound_absence_cuti->get_data_cuti($date, $np);
					$hasil_pembatalan 	= $this->M_inbound_absence_cuti->get_data_pembatalan_cuti($date, $np);
                } else{
                    $hasil				= $this->M_inbound_absence_cuti->get_data_cuti($date);
					$hasil_pembatalan 	= $this->M_inbound_absence_cuti->get_data_pembatalan_cuti($date);
                }

             
                
                if($hasil->num_rows()>0){
                    foreach($hasil->result_array() as $data) {
                       
					  	   
					   $tanggal_proses=$date;
						//looping tanggal cuti
//						$tanggal_proses=$data['start_date'];
//						$tanggal_akhir_proses=$data['end_date'];
						
//						while (strtotime($tanggal_proses) <= strtotime($tanggal_akhir_proses))
//						{
							//TIDAK LEMPAR KETIKA	
					
// echo $data['np_karyawan']." ".$tanggal_proses."<br>";
			
				   
							$tm_action = tm_status_erp_master_data($data['np_karyawan'],$tanggal_proses);		
							if($tm_action['action']=='ZI' || //skorsing dengan gaji
							$tm_action['action']=='ZL' ||  //sakit berkepanjangan
							($tm_action['action']=='ZN' && $tm_action['tm_status']=='0') || //MPP
							($tm_action['action']=='' || $tm_action['tm_status']==null)) //tidak ada data
							{
								//do nothing
							}else
							{
								
								//pastikan ketika melempar attendance dia tidak OFF
								$check_dinas = $this->M_inbound_absence_cuti->select_cico_by_np_dws($data['np_karyawan'],$tanggal_proses);				
								if(
									($check_dinas['dws_name']=='OFF' && ($check_dinas['dws_name_fix']=='' || $check_dinas['dws_name_fix']=='')) //jika dws name off,dws name fix harus kosong
									|| 
									($check_dinas['dws_name_fix']=='OFF') //jika dws_name_fix  OFF
								)
								{ 
									//tidak lempar
								}else //lempar
								{
									//jika hari libur tidak lempar cuti
									$hari_libur = $this->M_inbound_absence_cuti->check_hari_libur($tanggal_proses);								
									if($hari_libur==false)
									{
										$absence_type = explode('|', $data['absence_type']);
							
										$np_karyawan		= $data['np_karyawan'];
										$personnel_number	= $data['personel_number'];
										$info_type          = $absence_type[0];
										$absence_type       = $absence_type[1];
										$start_date         = str_replace('-','',$tanggal_proses);
										$end_date           = str_replace('-','',$tanggal_proses);
										$start_time         = '';
										$end_time           = '';
										$poin           	= '';
										$transaction_type   = '';
										
										/*if($tampil=='')
										{
											$tampil = 'NP_KARYAWAN'."\t".'PERSONNEL_NUMBER'."\t".'INFO_TYPE'."\t". 'ABSENCE_TYPE'."\t".'START_DATE'."\t".'END_DATE'."\t".'START_TIME'."\t".'END_TIME'."\t".'POIN'."\t".'TRANSACTION_TYPE'."\n";
										}*/
										
										if($tanggal_proses==$date) //yg dilempar hanya tgl itu saja
										{
											$tampil .= $np_karyawan."\t".$personnel_number."\t".$info_type."\t".$absence_type."\t".$start_date."\t".$end_date."\t".$start_time."\t".$end_time."\t".$poin."\t".$transaction_type."\t"."\r\n";
										}
									
									} //end of jika hari libur tidak lempar cuti
																
								
								} //end of pastikan ketika melempar attendance dia tidak OFF
								
								
								
								
								$tanggal_proses = date ("Y-m-d", strtotime("+1 day", strtotime($tanggal_proses)));//looping tambah 1 date
						
						
							} //end of TIDAK LEMPAR
							
//						} //end of looping tanggal cuti
						
						
                    }
                }
				
				//CUTI BERSAMA
				$tanggal_sekarang 	= $date;
				//untuk 2020
				//$tanggal_sekarang 	= "2020-01-13";
				$pisah 				= explode('-',$tanggal_sekarang);
				
			
				$tahun_sekarang		= $pisah[0];
				$bulan_sekarang		= $pisah[1];
				$hari_sekarang		= $pisah[2];
				
				//ketika cuti di generate harian
				//$tanggal_cutoff_tahunan = $tahun_sekarang."-01-31";
				//$tanggal_cutoff_bulanan = $tahun_sekarang."-".$bulan_sekarang."-10";
				
				//ketika cuti di generate setiap outbound
				$tanggal_cutoff_tahunan = $tanggal_sekarang;
				$tanggal_cutoff_bulanan = $tanggal_sekarang;
				
				
				if(($tanggal_sekarang==$tanggal_cutoff_tahunan) || ($tanggal_sekarang==$tanggal_cutoff_bulanan && $tanggal_sekarang=!$tanggal_cutoff_tahunan))//jika saat hari cutoff
				{
								
					$get_tanggal_cuti_bersama = $this->M_inbound_absence_cuti->get_tanggal_cuti_bersama($tahun_sekarang);
					
					foreach($get_tanggal_cuti_bersama->result_array() as $cuti_bersama) 
					{
						$tanggal_cuti_bersama = $cuti_bersama['tanggal'];
						
						//Tri Wibowo - 16 02 2021, script ini melihat master data karyawan saat cuti bersama, mengakibatkan untuk bulan yg berlum berjalan, data karyawan tidak akan terambil, maka ganti menggunakan script dimana tanggal berjalan bukan tanggal cuti bersama						
						//$get_data_karyawan = $this->M_inbound_absence_cuti->data_karyawan_by_date($tanggal_cuti_bersama);
						$get_data_karyawan = $this->M_inbound_absence_cuti->data_karyawan_by_date($date);
						
						
						foreach($get_data_karyawan->result_array() as $data_karyawan) 
						{
							
							//$np_karyawan		= $data_karyawan['no_pokok'];
							$np_karyawan		= $data_karyawan['np_karyawan'];
							$personnel_number	= $data_karyawan['personnel_number'];
							
							$data_pembayaran = $this->M_inbound_absence_cuti->get_data_pembayaran($np_karyawan,$tanggal_cuti_bersama);
							
							//0=belum, 1=cuti besar, 2=cuti tahunan, 3=hutang cuti,4=tidak cuti
							if($data_pembayaran['enum']=='1' && $data_pembayaran['submit_erp']!='1') //cuti besar
							{
								$absence_type = explode('|', "2001|1010");
                        
								$np_karyawan		= $np_karyawan;
								$personnel_number	= $personnel_number;
								$info_type          = $absence_type[0];
								$absence_type       = $absence_type[1];
								$start_date         = str_replace('-','',$tanggal_cuti_bersama);
								$end_date           = str_replace('-','',$tanggal_cuti_bersama);
								$start_time         = '';
								$end_time           = '';
								$poin           	= '';
								$transaction_type   = '';

								$tampil .= $np_karyawan."\t".$personnel_number."\t".$info_type."\t".$absence_type."\t".$start_date."\t".$end_date."\t".$start_time."\t".$end_time."\t".$poin."\t".$transaction_type."\t"."\r\n";
								
								$this->M_inbound_absence_cuti->update_submit_cuti_bersama($data_pembayaran['id']);
								
								
								//=============================
								//	UPDATE DATA CUTI BESAR
								//=============================	
								
								$this->load->model('osdm/m_persetujuan_cuti_sdm');
								
								$jumlah_hari	= '1';
									
								$jatah_cubes = $this->m_persetujuan_cuti_sdm->select_jatah_cubes($np_karyawan,$tanggal_cuti_bersama);
								
								$pakai_hari	 = $jatah_cubes['pakai_hari'];								
								$sisa_hari	 = $jatah_cubes['sisa_hari'];
										
							
								$hasil_pakai_hari	= $pakai_hari+$jumlah_hari;							
								$hasil_sisa_hari	= $sisa_hari-$jumlah_hari;
								
								$data_update = array(				
								'id'						=> $jatah_cubes['id'],
								'pakai_hari'				=> $hasil_pakai_hari,	
								'sisa_hari'					=> $hasil_sisa_hari
								);
									
								$this->m_persetujuan_cuti_sdm->update_jatah_cubes($data_update);
								
								/*
								=============================
									END OF UPDATE DATA CUTI BESAR
								=============================	
								*/
								
								
								
							}else
							if($data_pembayaran['enum']=='2' && $data_pembayaran['submit_erp']!='1') //cuti tahunan
							{
								$absence_type = explode('|', "2001|1020");
                        
								$np_karyawan		= $np_karyawan;
								$personnel_number	= $personnel_number;
								$info_type          = $absence_type[0];
								$absence_type       = $absence_type[1];
								$start_date         = str_replace('-','',$tanggal_cuti_bersama);
								$end_date           = str_replace('-','',$tanggal_cuti_bersama);
								$start_time         = '';
								$end_time           = '';
								$poin           	= '';
								$transaction_type   = '';

								$tampil .= $np_karyawan."\t".$personnel_number."\t".$info_type."\t".$absence_type."\t".$start_date."\t".$end_date."\t".$start_time."\t".$end_time."\t".$poin."\t".$transaction_type."\t"."\r\n";
								
								$this->M_inbound_absence_cuti->update_submit_cuti_bersama($data_pembayaran['id']);
								
							}else
							if($data_pembayaran['enum']=='4') //tidak cuti
							{
								
							}else //default langsung lempar cuti bersama
							if($data_pembayaran['submit_erp']!='1')
							{
								$date_now = date('Y-m-d');
								$ambil_jatah = $this->db->query("SELECT sum(number) as number FROM erp_absence_quota WHERE deduction_from<='$date_now' and deduction_to>='$date_now' AND np_karyawan='$np_karyawan'")->row_array();
								$ambil = $ambil_jatah['number'];
								
								if($ambil!=null AND $ambil!='0') //lempar cuti bersama
								{
									$absence_type = explode('|', "2001|1020");
								}else //lempar izin pribadi tanpa potongan
								{
									$absence_type = explode('|', "2001|5010");
								}
								
								
                        
								$np_karyawan		= $np_karyawan;
								$personnel_number	= $personnel_number;
								$info_type          = $absence_type[0];
								$absence_type       = $absence_type[1];
								$start_date         = str_replace('-','',$tanggal_cuti_bersama);
								$end_date           = str_replace('-','',$tanggal_cuti_bersama);
								$start_time         = '';
								$end_time           = '';
								$poin           	= '';
								$transaction_type   = '';

								$tampil .= $np_karyawan."\t".$personnel_number."\t".$info_type."\t".$absence_type."\t".$start_date."\t".$end_date."\t".$start_time."\t".$end_time."\t".$poin."\t".$transaction_type."\t"."\r\n";
							
								$this->M_inbound_absence_cuti->insert_submit_cuti_bersama($np_karyawan,$tanggal_cuti_bersama,'2'); //default lempar pake cuti tahunan
							}
							
							
						}
						
						
					}
				}
				
				
				
				
				
				
				
				
				
				
				if($hasil_pembatalan->num_rows()>0){
                    foreach($hasil_pembatalan->result_array() as $data_pembatalan) {
                        $absence_type = explode('|', $data_pembatalan['absence_type']);
                        
						$np_karyawan 	= $data_pembatalan['np_karyawan'];
						$tanggal_proses	= $data_pembatalan['date'];
						
						
                        $np_karyawan		= $data_pembatalan['np_karyawan'];
                        $personnel_number	= $data_pembatalan['personel_number'];
                        $info_type          = $absence_type[0];
                        $absence_type       = $absence_type[1];
                        $start_date         = str_replace('-','',$data_pembatalan['date']);
                        $end_date           = str_replace('-','',$data_pembatalan['date']);
                        $start_time         = '';
                        $end_time           = '';
						$poin           	= '';
						$transaction_type   = 'D';
						
						
						$tm_action = tm_status_erp_master_data($np_karyawan,$tanggal_proses);		
						if($tm_action['action']=='ZI' || //skorsing dengan gaji
						$tm_action['action']=='ZL' ||  //sakit berkepanjangan
						($tm_action['action']=='ZN' && $tm_action['tm_status']=='0') || //MPP
						($tm_action['action']=='' || $tm_action['tm_status']==null)) //tidak ada data
						{
							//do nothing
						}else
						{
								
							//pastikan ketika melempar attendance dia tidak OFF
							$check_dinas = $this->M_inbound_absence_cuti->select_cico_by_np_dws($np_karyawan,$tanggal_proses);				
							if(
								($check_dinas['dws_name']=='OFF' && ($check_dinas['dws_name_fix']=='' || $check_dinas['dws_name_fix']=='')) //jika dws name off,dws name fix harus kosong
								|| 
								($check_dinas['dws_name_fix']=='OFF') //jika dws_name_fix  OFF
							)
							{ 
								//tidak lempar
							}else //lempar
							{
								//jika hari libur tidak lempar cuti
								$hari_libur = $this->M_inbound_absence_cuti->check_hari_libur($tanggal_proses);								
								if($hari_libur==false)
								{
									$tampil .= $np_karyawan."\t".$personnel_number."\t".$info_type."\t".$absence_type."\t".$start_date."\t".$end_date."\t".$start_time."\t".$end_time."\t".$poin."\t".$transaction_type."\t"."\r\n";			
								}
							}
						}							
						
						
						
						
						
						

                       
                    }
                }
				

                $olah_tanggal		= str_replace("-","",$date);

              

               
                $date_start = date ("Y-m-d", strtotime("+1 day", strtotime($date_start)));
            }
			
				//$tabel_inbound_cuti = "ESS_ABSENCE_CUTI_".$olah_tanggal;
				$tabel_inbound_cuti = "ESS_ABSENCE_CUTI";
				
                if ( ! write_file(FCPATH."inbound_sap/inbound_absence_cuti/$tabel_inbound_cuti.txt", $tampil)){
                    echo 'Gagal membuat file txt';
                } else {
                   
                       echo "File $tabel_inbound_cuti.txt"."<br>";
                    
                }

            //echo "<br><br>selesai ".date('Y-m-d H:i:s');
            $msc = microtime(true)-$msc;
            echo "<br><br>Done. Execution time: $msc seconds.<br>";
			
			//insert ke tabel 'ess_status_proses_output', id proses = 9
			$this->db->insert('ess_status_proses_output', ['id_proses'=>9, 'waktu'=>date('Y-m-d H:i:s')]);
        }
	}
}