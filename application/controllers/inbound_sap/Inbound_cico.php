<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inbound_cico extends CI_Controller {
	
	 
	function __construct()
	{
		parent::__construct();
		$this->load->model('inbound_sap/m_inbound_cico');
		$this->load->helper('karyawan_helper');
	}
	
	public function index()
	{
		//$dws_tanggal = '2018-11-30';
		//echo substr($dws_tanggal,8,2);
		
		redirect(base_url('dashboard'));
	}
	
	public function create_file($date, $np=null)
	{
		//include helper file
		$this->load->helper('file');		
		
		//run program selamanya untuk menghindari maximal execution
		//ini_set('MAX_EXECUTION_TIME', -1);
		set_time_limit('0');
		
		// $this->output->enable_profiler(TRUE);
							
				
		echo "Proses Inisialisasi Data dari Tabel ESS CICO untuk dijadikan txt";
		echo "<br>Mulai ".date('Y-m-d H:i:s')."<br><br>";
		$msc = microtime(true);

		//Reference Code
		$clock_in 	= $this->m_inbound_cico->inbound_setting('rs_clock_in')['value'];
		$clock_out 	= $this->m_inbound_cico->inbound_setting('rs_clock_out')['value'];
		
		//membatasi yang di proses data yang sudah siap, yaitu bulan sebelumnya
		// $tahun_bulan = date('Y_m');
        if($date=='today'){
            $month = date("Y-m", strtotime("-1 months"));
        } else{
            $month = $date;
        }
		$tahun_bulan = str_replace('-', '_', $month);
		
		$tabel_cico		= "ess_cico_".$tahun_bulan;
		$tabel_perizinan	= "ess_perizinan_".$tahun_bulan;
		$tabel_inbound_cico	= "ESS_CICO_".$tahun_bulan;

		//CEK dan GENERATE table inbound_absense
		if ($this->m_inbound_cico->check_table_exist($tabel_perizinan)==null)				
		{
			$this->m_inbound_cico->create_table_inbound_perizinan($tabel_perizinan);		
			$this->m_inbound_cico->alter_table($tabel_perizinan);		
		}		

		//ambil data di tabel cico yang belum di proses
		if (empty($np)) {
			$np = 'all';
		}
		
		//tidak lempar jika
		//AND tm_status!='9'
		//AND action NOT IN ('ZI','ZL','ZN')
		$data_tabel_cico = $this->m_inbound_cico->get_data_cico($tabel_cico, $np);
		
		
		
		
		/* tidak perlu
		$arr_cico = array();
		foreach ($data_tabel_cico->result_array() as $data) 
		{	
			$arr = array(
				'id'					=> $data['id'],
				'np_karyawan'			=> $data['np_karyawan'],
				'nama'					=> $data['nama'],
				'nama_jabatan'			=> $data['nama_jabatan'],
				'nama_unit'				=> $data['nama_unit'],
				'personel_number'		=> $data['personel_number'],
				'tapping_time_1'		=> $data['tapping_time_1'],
				'tapping_fix_1'			=> $data['tapping_fix_1'],
				'tapping_terminal_1'	=> $data['tapping_terminal_1'],
				'tapping_time_2'		=> $data['tapping_time_2'],
				'tapping_fix_2'			=> $data['tapping_fix_2'],
				'tapping_terminal_2'	=> $data['tapping_terminal_2'],
				'dws_tanggal' 			=> $data['dws_tanggal'],
				'dws_in_tanggal' 		=> $data['dws_in_tanggal'],
				'dws_in_tanggal_fix' 	=> $data['dws_in_tanggal_fix'],
				'dws_in' 				=> $data['dws_in'],
				'dws_in_fix' 			=> $data['dws_in_fix'],
				'dws_out_tanggal' 		=> $data['dws_out_tanggal'],
				'dws_out_tanggal_fix' 	=> $data['dws_out_tanggal_fix'],
				'dws_out' 				=> $data['dws_out'],
				'dws_out_fix' 			=> $data['dws_out_fix'],
				'dws_name' 				=> $data['dws_name'],
				'dws_name_fix' 			=> $data['dws_name_fix'],
				'id_cuti' 				=> $data['id_cuti'],
				'id_sppd'	 			=> $data['id_sppd'],
				'id_overtime'	 		=> $data['id_overtime'],
				'action'	 			=> $data['action']
			);
								
			array_push($arr_cico,$arr);	
		}
		*/
		
		//inisialisasi check out (tanggal asal asalkan kecil aja :D)
		$check_out_kemarin = "1995-06-17 03:00:00";
		$check_out_np		= '';
		
		//Olah array master data (DWS)
		$counter 				= 1;
		$sudah_awal_bulan		=0;		
		$i = 0; //INDEKS ROW
		foreach ($data_tabel_cico->result_array() as $data)  
		{
			
			
			
			$id					= $data['id'];
			$np_karyawan		= $data['np_karyawan'];
			$nama				= $data['nama'];
			$nama_jabatan		= $data['nama_jabatan'];
			$kode_unit			= $data['kode_unit'];
			$nama_unit			= $data['nama_unit'];
			$personel_number	= $data['personel_number'];
			$tapping_time_1		= $data['tapping_time_1'];			
			$tapping_fix_1		= $data['tapping_fix_1'];
			$tapping_terminal_1	= $data['tapping_terminal_1'];
			$tapping_time_2		= $data['tapping_time_2'];
			$tapping_fix_2		= $data['tapping_fix_2'];
			$tapping_terminal_2	= $data['tapping_terminal_2'];
			$dws_tanggal		= $data['dws_tanggal'];
			$dws_in_tanggal		= $data['dws_in_tanggal'];
			$dws_in_tanggal_fix	= $data['dws_in_tanggal_fix'];
			$dws_in				= $data['dws_in'];
			$dws_in_fix			= $data['dws_in_fix'];
			$dws_out_tanggal	= $data['dws_out_tanggal'];
			$dws_out_tanggal_fix= $data['dws_out_tanggal_fix'];
			$dws_out			= $data['dws_out'];
			$dws_out_fix		= $data['dws_out_fix'];
			$dws_name 			= $data['dws_name'];
			$dws_name_fix		= $data['dws_name_fix'];
			$id_cuti			= $data['id_cuti'];
			$id_sppd			= $data['id_sppd'];
			$id_overtime		= $data['id_overtime'];
			$action				= $data['action'];
			$tm_status			= $data['tm_status'];
			$wfh							= $data['wfh']; //7648 - Tri Wibowo 01 04 2020, work form home
			$tapping_fix_approval_status 	= $data['tapping_fix_approval_status']; //7648 - Tri Wibowo 26 06 2020, work form home
			
			//TIDAK LEMPAR KETIKA			
			$tm_action = tm_status_erp_master_data($np_karyawan,$dws_tanggal);	
			if($tm_action['action']=='ZI' || //skorsing dengan gaji
			$tm_action['action']=='ZL' ||  //sakit berkepanjangan
			($tm_action['action']=='ZN' && $tm_action['tm_status']=='0') || //MPP
			($tm_action['action']=='' || $tm_action['tm_status']==null) || //tidak ada data
			$wfh == '1') //work from home
			{
				//do nothing
				
				//7648 Tri Wibowo, 26 Juni 2020, Update karena jika WFH harus ke reset ketidak hadirannya, jika tidak maka jika running inbound cico 2 kali pada bulan yang sama, maka dikawatirkan data running cico sebelumnya masih ikut dan tidak ke update di running cico ke 2
				//jika wfh maka tidak ada counter ketidak hadiran
				if($wfh=='1' && $tapping_fix_approval_status=='1')
				{
					$tahun_bulan = substr($dws_tanggal,0,7);										
					$tahun_bulan=str_replace("-","_",$tahun_bulan);										
					$tabel_cico = 'ess_cico_'.$tahun_bulan;
					
					if(!$this->m_inbound_cico->check_table_exist($tabel_cico))
					{
						$tabel_cico = 'ess_cico';
					}
					$this->m_inbound_cico->update_tidak_hadir_ke($np_karyawan,$dws_tanggal,null,null,$tabel_cico);
				}
				
				//end update 26 juni 2020
				
			}else			
			{
				$is_tm = false; //jika tidak ada CI
				$is_tk = false; //jika tidak ada CO
				$is_ab = false; //jika tidak ada CI & CO
				$is_off = false; //jika dws_name_fix == OFF
				$tidak_kirim = false; //default pasti kirim cico
				//cuti bersama
				$is_cuti_bersama = $this->m_inbound_cico->is_cuti_bersama($dws_tanggal);
				if($is_cuti_bersama==true) //jika cuti bersama
				{
					$is_cuti_bersama=true;
				}else
				{
					$is_cuti_bersama=false;
				}
				
				//libur
				$is_libur = $this->m_inbound_cico->cek_libur($dws_tanggal);
				if($is_libur==false) //jika libur
				{
					$is_libur=true;
				}else
				{
					$is_libur=false;
				}
				
				
				//lembur
				if($id_overtime!='' && $id_overtime!=null)
				{
					$is_overtime = true;
				}else
				{
					$is_overtime = false;
				}
				
				//lembur
				if($id_sppd!='' && $id_sppd!=null)
				{
					$is_sppd = true;
				}else
				{
					$is_sppd = false;
				}
				
				
				//jika lembur di cuti /cuti bersama/ dinas akan diverikasi manual by sdm berdasarkan tanggal dws nya
				if (!empty($id_overtime) && (!empty($id_cuti) || !empty($id_sppd) || $is_cuti_bersama==true)) {
					$this->m_inbound_cico->update_overtime($id_overtime);
				}
				
				//dipindah keluar IF oleh bowo
					//IN
					if($tapping_fix_1!=null || $tapping_fix_1!='') { //jika terdapat data tapping IN FIX
						$date_in = substr($data['tapping_fix_1'],0,10);
						$time_in = substr($data['tapping_fix_1'],11,8);
					} else { //jika tidak terdapat data tapping IN FIX
						if($tapping_time_1!=null || $tapping_time_1!='') {
							$date_in = substr($data['tapping_time_1'],0,10);
							$time_in = substr($data['tapping_time_1'],11,8);
						} else {
							$is_tm = true;
							$tapping_terminal_1='TM';
							if ($dws_in_tanggal_fix!=null || $dws_in_tanggal_fix!='') {
								$date_in = $data['dws_in_tanggal_fix'];
							} else {
								$date_in = $data['dws_in_tanggal'];
							}
							if ($dws_in_fix!=null || $dws_in_fix!='') {
								$time_in = substr($data['dws_in_fix'],0,8);
							} else {
								$time_in = substr($data['dws_in'],0,8);
							}
						}
					}
					
					//OUT
					if($tapping_fix_2!=null || $tapping_fix_2!='') { //jika terdapat data tapping IN FIX
						$date_out = substr($data['tapping_fix_2'],0,10);
						$time_out = substr($data['tapping_fix_2'],11,8);
					} else { //jika tidak terdapat data tapping IN FIX
						if($tapping_time_2!=null || $tapping_time_2!='') {
							$date_out = substr($data['tapping_time_2'],0,10);
							$time_out = substr($data['tapping_time_2'],11,8);
						} else {
							$is_tk = true;
							$tapping_terminal_2='TK';
							if ($dws_out_tanggal_fix!=null || $dws_out_tanggal_fix!='') {
								$date_out = $data['dws_out_tanggal_fix'];
							} else {
								$date_out = $data['dws_out_tanggal'];
							}
							if ($dws_out_fix!=null || $dws_out_fix!='') {
								$time_out = substr($data['dws_out_fix'],0,8);
							} else {
								$time_out = substr($data['dws_out'],0,8);
							}
						}
					}

					//CEK JIKA Abesence
					if ($is_tk && $is_tm) {
						$is_tm = false;
						$is_tk = false;
						$is_ab = true;
						$tapping_terminal_1='AB';
						$tapping_terminal_2='AB';
					}
					
					if ($dws_name_fix!=null || $dws_name_fix!='') {
						if ($dws_name_fix == 'OFF') {
							if ($is_ab == false) {
								if ($is_tk) {
									$date_out = $date_in;
									$time_out = $time_in; //jika tidak ada izin
									$tidak_kirim = true; //jika tidak ada izin, tidak usah kirim karena pasti lembur tidak diakui
									
									//EDIT DISINI YA MAS BOWO
									//jika ada tapping in tapi tidak ada tapping out
									//untuk keperluan ketika ada karyawan yg lembur di hari libur tapping in tapi tidak tapping out karena ada izin nya
									if ($is_tk) { 
										//CHECK ID PERIZINAN							
										$pakai_penutup_izin = false;
										$tahun_bulan = substr($dws_tanggal,0,7);	
										
										$tahun_bulan=str_replace("-","_",$tahun_bulan);
										
										$tabel_cico = 'ess_cico_'.$tahun_bulan;
										if(!$this->m_inbound_cico->check_table_exist($tabel_cico))
										{
											$tabel_cico = 'ess_cico';
										}
										
										//ambil cico
										$ambil_cico = $this->db->query("SELECT * FROM $tabel_cico WHERE np_karyawan='$np_karyawan' AND dws_tanggal='$dws_tanggal'")->row_array();
										$id_perizinan = $ambil_cico['id_perizinan'];
										
										if($id_perizinan)
										{
											$tabel_perizinan = 'ess_perizinan_'.$tahun_bulan;
											if(!$this->m_inbound_cico->check_table_exist($tabel_perizinan))
											{
												$tabel_perizinan = 'ess_perizinan';
											}
											
											$ambil_perizinan_max_end = $this->db->query("SELECT end_date,end_time FROM $tabel_perizinan WHERE id IN ($id_perizinan) ORDER BY end_date DESC,end_time DESC LIMIT 1")->row_array();
											$perizinan_max_end = $ambil_perizinan_max_end['end_date']." ".$ambil_perizinan_max_end['end_time'];
											
											$ambil_perizinan_max_start = $this->db->query("SELECT start_date,start_time FROM $tabel_perizinan WHERE id IN ($id_perizinan) ORDER BY start_date DESC,start_time DESC LIMIT 1")->row_array();
											$perizinan_max_start =  $ambil_perizinan_max_start['start_date']." ".$ambil_perizinan_max_start['start_time'];
											
											if($perizinan_max_end>=$perizinan_max_start)
											{
												$pakai_penutup_izin = true;
												$penutup_date = $ambil_perizinan_max_end['end_date'];
												$penutup_time = $ambil_perizinan_max_end['end_time'];
											}else
											{
												$pakai_penutup_izin = true;
												$penutup_date = $ambil_perizinan_max_start['start_date'];
												$penutup_time = $ambil_perizinan_max_start['start_time'];
											}
											
											if($pakai_penutup_izin==true)
											{
												$date_out = $penutup_date;
												$time_out = $penutup_time; //jika ada izin
												$tidak_kirim = false; //jika ada izin, kirim ciconya
											}	
											
											
											
										}
																
									}
									
									
									
								}
								if ($is_tm) {
									$date_in = $date_out;
									$time_in = $time_out;
									$tidak_kirim = true; //tidak usah kirim karena pasti lembur tidak diakui
								}
							}else{
								$is_off = true;
							}
							
							//jika OFF tapi tidak lembur tidak usah kirim cico
							if($is_overtime==false)
							{
								$tidak_kirim = true; //tidak usah kirim karena pasti lembur tidak diakui
							}
						}
					}else{
						if ($dws_name == 'OFF') {
							if ($is_ab == false) {
								if ($is_tk) {
									$date_out = $date_in;
									$time_out = $time_in; //jika tidak ada izin
									$tidak_kirim = true; //jika tidak ada izin, tidak usah kirim karena pasti lembur tidak diakui
									
									//EDIT DISINI YA MAS BOWO
									//jika ada tapping in tapi tidak ada tapping out
									//untuk keperluan ketika ada karyawan yg lembur di hari libur tapping in tapi tidak tapping out karena ada izin nya
									if ($is_tk) { 
										//CHECK ID PERIZINAN							
										$pakai_penutup_izin = false;
										$tahun_bulan = substr($dws_tanggal,0,7);	
										
										$tahun_bulan=str_replace("-","_",$tahun_bulan);
										
										$tabel_cico = 'ess_cico_'.$tahun_bulan;
										if(!$this->m_inbound_cico->check_table_exist($tabel_cico))
										{
											$tabel_cico = 'ess_cico';
										}
										
										//ambil cico
										$ambil_cico = $this->db->query("SELECT * FROM $tabel_cico WHERE np_karyawan='$np_karyawan' AND dws_tanggal='$dws_tanggal'")->row_array();
										$id_perizinan = $ambil_cico['id_perizinan'];
										
										if($id_perizinan)
										{
											$tabel_perizinan = 'ess_perizinan_'.$tahun_bulan;
											if(!$this->m_inbound_cico->check_table_exist($tabel_perizinan))
											{
												$tabel_perizinan = 'ess_perizinan';
											}
											

											$ambil_perizinan_max_end = $this->db->query("SELECT end_date,end_time FROM $tabel_perizinan WHERE id IN ($id_perizinan) ORDER BY end_date DESC,end_time DESC LIMIT 1")->row_array();
											$perizinan_max_end = $ambil_perizinan_max_end['end_date']." ".$ambil_perizinan_max_end['end_time'];
											
											$ambil_perizinan_max_start = $this->db->query("SELECT start_date,start_time FROM $tabel_perizinan WHERE id IN ($id_perizinan) ORDER BY start_date DESC,start_time DESC LIMIT 1")->row_array();
											$perizinan_max_start =  $ambil_perizinan_max_start['start_date']." ".$ambil_perizinan_max_start['start_time'];
											
											if($perizinan_max_end>=$perizinan_max_start)
											{
												$pakai_penutup_izin = true;
												$penutup_date = $ambil_perizinan_max_end['end_date'];
												$penutup_time = $ambil_perizinan_max_end['end_time'];
											}else
											{
												$pakai_penutup_izin = true;
												$penutup_date = $ambil_perizinan_max_start['start_date'];
												$penutup_time = $ambil_perizinan_max_start['start_time'];
											}
											
											if($pakai_penutup_izin== true)
											{
												$date_out = $penutup_date;
												$time_out = $penutup_time; //jika ada izin
												$tidak_kirim = false; //jika ada izin, kirim ciconya
											}			
										}
													
									}						
									
									
									
								}
								if ($is_tm) {
									$date_in = $date_out;
									$time_in = $time_out;
									$tidak_kirim = true; //tidak usah kirim karena pasti lembur tidak diakui
								}
							}else{
								$is_off = true;
							}
							
							//jika OFF tapi tidak lembur tidak usah kirim cico
							if($is_overtime==false)
							{
								$tidak_kirim = true; //tidak usah kirim karena pasti lembur tidak diakui
							}
						}
					}
					
				
					
				//End dipindah oleh bowo
				
					//$this->output->enable_profiler(TRUE);	
					//update counter ketika tidak berangkat
										
					if(($is_ab==true || $id_cuti==true || $is_cuti_bersama==true) && $is_off == false && $is_libur == false && $is_sppd==false)
					{
						//ambil data tanggal awal bulan
						if($sudah_awal_bulan==0)
						{
							$awal_bulan 		= $this->m_inbound_cico->ambil_tidak_hadir_awal_bulan($np_karyawan,$dws_tanggal);
							$tanggal_awal_bulan = $awal_bulan['dws_tanggal'];						
						}
						
						//jika awal bulan check apakah data bulan sebelum dia juga tidak hadir
						if($dws_tanggal==$tanggal_awal_bulan) //ketika yang akan d check itu adalah awal bulan
						{
							$sudah_awal_bulan 	= 1;
							$sebelum = $this->m_inbound_cico->ambil_tidak_hadir_ke_sebelum($np_karyawan,$dws_tanggal);
							
							if($sebelum['tidak_hadir_ke']!=null)
							{
								$counter 					= $sebelum['tidak_hadir_ke']+1;
								$tidak_hadir_tanggal_awal 	= $sebelum['tidak_hadir_tanggal_awal'];	
								
								
							}else
							{
								$counter 					= 1;
								$tidak_hadir_tanggal_awal 	= $dws_tanggal;	
							}							
							
						}else
						{
							//counter awal ambil tgl dws sebagai tidak hadir tanggal awal
							if($counter==1)
							{
								$tidak_hadir_tanggal_awal=$dws_tanggal;
							}						
						}

						
						//update 30 Agustus 2019
						//Ketika awal bulan atau ditengah bulan, VALIDASI APAKAH SUDAH MELEWATI BATAS
						//cari batas 1 bulan tanpa minus 1 hari, jika sudah melewati pas satu bulan berarti counter nya jadi 1 lagi
						$tanggal_batas = date('Y-m-d', strtotime($tidak_hadir_tanggal_awal . ' +1 month'));
							
						if($dws_tanggal >= $tanggal_batas)
						{
							$counter 					= 1;
							$tidak_hadir_tanggal_awal 	= $dws_tanggal;									
						}
						

								
						
						$tidak_hadir_ke = $counter;
						
						$this->m_inbound_cico->update_tidak_hadir_ke($np_karyawan,$dws_tanggal,$tidak_hadir_ke,$tidak_hadir_tanggal_awal,$tabel_cico);									
						$counter++;
					}else
					{
						if($is_off == false &&  $is_libur == false) //jika ada tapping saat dia tidak off dan tidak libur
						{
							$counter=1;
						}
						
							
						$this->m_inbound_cico->update_tidak_hadir_ke($np_karyawan,$dws_tanggal,null,null,$tabel_cico);
						
					}
				
					
				//pastikan dia tidak cuti / dinas
				if (empty($id_cuti) && empty($id_sppd)) {
					
					
					
					//check apakah mereka input nya benar +1 -1 DWS
					$check_in = $date_in." ".$time_in;
					$check_out = $date_out." ".$time_out;
					
					//in	
					if ($dws_in_tanggal_fix!=null || $dws_in_tanggal_fix!='') {
						$check_dws_in 	= $dws_in_tanggal_fix." ".$dws_in_fix;
						$check_min_in	 	= date('Y-m-d', strtotime($dws_in_tanggal_fix . ' -1 day'));
						$check_plus_in	 	= date('Y-m-d', strtotime($dws_in_tanggal_fix . ' +1 day'));
						
						if($dws_name_fix=='OFF')
						{
							$check_dws_in_min 	= $check_min_in." ".$dws_in_fix;
							$check_dws_in_plus 	= $check_plus_in." "."12:00:00";//dilebihkan jika ada karyawan yg lembur nya lintas hari di hari libur
						}else
						{
							$check_dws_in_min 	= $check_min_in." ".$dws_in_fix;
							$check_dws_in_plus 	= $check_plus_in." ".$dws_in_fix;
						}
						
						
					} else {
						$check_dws_in =  $dws_in_tanggal." ".$dws_in;
						$check_min_in 	= date('Y-m-d', strtotime($dws_in_tanggal . ' -1 day'));
						$check_plus_in	 	=date('Y-m-d', strtotime($dws_in_tanggal . ' +1 day'));
						
						
						if($dws_name=='OFF')
						{
							$check_dws_in_min 	= $check_min_in." ".$dws_in;
							$check_dws_in_plus 	= $check_plus_in." "."12:00:00";//dilebihkan jika ada karyawan yg lembur nya lintas hari di hari libur
						}else
						{
							$check_dws_in_min 	= $check_min_in." ".$dws_in;
							$check_dws_in_plus 	= $check_plus_in." ".$dws_in;
						}
						
						
					}
					//out
					if ($dws_out_tanggal_fix!=null || $dws_out_tanggal_fix!='') {
						$check_dws_out 	= $dws_out_tanggal_fix." ".$dws_out_fix;
						$check_min_out	 	= date('Y-m-d', strtotime($dws_out_tanggal_fix . ' -1 day'));
						$check_plus_out	 	= date('Y-m-d', strtotime($dws_out_tanggal_fix . ' +1 day'));
						
						if($dws_name_fix=='OFF')
						{
							$check_dws_out_min 	= $check_min_out." ".$dws_out_fix;
							$check_dws_out_plus 	= $check_plus_out." "."12:00:00"; //dilebihkan jika ada karyawan yg lembur nya lintas hari di hari libur
						}else
						{
							$check_dws_out_min 	= $check_min_out." ".$dws_out_fix;
							$check_dws_out_plus 	= $check_plus_out." ".$dws_out_fix;
						}
						
						
					} else {
						$check_dws_out =  $dws_out_tanggal." ".$dws_out;
						$check_min_out 	= date('Y-m-d', strtotime($dws_out_tanggal . ' -1 day'));
						$check_plus_out	 	=date('Y-m-d', strtotime($dws_out_tanggal . ' +1 day'));
						
						if($dws_name=='OFF')
						{
							$check_dws_out_min 	= $check_min_out." ".$dws_out;
							$check_dws_out_plus 	= $check_plus_out." "."12:00:00"; //dilebihkan jika ada karyawan yg lembur nya lintas hari di hari libur
						}else
						{
							$check_dws_out_min 	= $check_min_out." ".$dws_out;
							$check_dws_out_plus 	= $check_plus_out." ".$dws_out;
						}
						
					}
					
					if(($check_in>=$check_dws_in_min) AND ($check_in<=$check_dws_in_plus))
					{
						//do nothing
					}else
					{
						$check_in=$check_dws_in;
					}
					
					if(($check_out>=$check_dws_out_min) AND ($check_out<=$check_dws_out_plus))
					{
						//do nothing
					}else
					{
						$check_out=$check_dws_out;
					}
					
					
					//ketika check in lebih kecil dari pada check out kemarin
					if($check_out_kemarin>=$check_in && $check_out_np==$np_karyawan)
					{
						$check_out_kemarin_min =date('Y-m-d H:i:s',strtotime('+1 minutes',strtotime($check_out_kemarin)));
						$check_in = $check_out_kemarin_min;
					}
					
					
					$check_out_kemarin = $check_out;
					$check_out_np = $np_karyawan;
					
					
					
					//kembalikan ke variabel nya
					$pisah_in 	= explode(" ",$check_in);
					$date_in 	= $pisah_in[0];
					$time_in 	= $pisah_in[1];
					
					$pisah_out 	= explode(" ",$check_out);
					$date_out 	= $pisah_out[0];
					$time_out 	= $pisah_out[1];
					
					
					
					
					
					
					
					//untuk keperluan check rate lembur selanjutnya
					$time_out_ori = $time_out;
					$date_out_ori = $date_out;

					$date_in = str_replace('-', '', $date_in);
					$date_out = str_replace('-', '', $date_out);
					$time_in = str_replace(':', '', $time_in);
					$time_out = str_replace(':', '', $time_out);

					//lempar cico ketika
					if (
						(($is_off == false) || ($is_off == true && $is_overtime == true && (($is_tm == false &&	$is_tk == false) || $pakai_penutup_izin==true))) && //ketika tidak off / ketika off tapi lembur harus dengan (clock in clock out / pakai penutup izin)
						$is_cuti_bersama == false && //ketika tidak cuti bersama
						(($is_libur == false) || ($is_libur == true && $is_overtime == true && $is_tm == false &&
						$is_tk == false)) && //ketika tidak libur / ketika libur tapi lembur harus dengan clock in clock out
						$is_ab == false && //ketika tidak AB
						$tidak_kirim ==false) //tidak ada situasi yg mewajibkan tidak kirim 
					{
						//array inbound cico IN ke sap
						$arr_in[$i] = array(
							'np_karyawan'		=> $np_karyawan,
							'personel_number'	=> $personel_number,
							'date'				=> $date_in,
							'time_type'			=> $clock_in,
							'time'				=> $time_in,
							'terminal_id'		=> $tapping_terminal_1,
							'transaction_type'	=> ''					
						);
						
						//jika lintas hari
						if($date_in<$date_out)
						{
							$arr_out[$i] = array(
							'np_karyawan'		=> $np_karyawan,
							'personel_number'	=> $personel_number,
							'date'				=> $date_in,
							'time_type'			=> $clock_out,
							'time'				=> '240000',
							'terminal_id'		=> 'ESS',
							'transaction_type'	=> ''				
							);	

							$id_lembur_rate_selanjutnya = $this->m_inbound_cico->check_lembur_rate_selanjutnya($np,$date_out_ori,$time_out_ori);
							
							if($id_lembur_rate_selanjutnya!=false)
							{														
								//CHECK apakah tanggal selanjutnya itu SPPD / CUTI / CUTI BERSAMA							
								//cuti bersama
								$date_out_is_cuti_bersama = $this->m_inbound_cico->is_cuti_bersama($date_out_ori);
								if($date_out_is_cuti_bersama==true) //jika cuti bersama
								{
									$date_out_is_cuti_bersama=true;
								}else
								{
									$date_out_is_cuti_bersama=false;
								}
								
								//check apakah sppd dan cuti
								$tahun_bulan = substr($date_out_ori,0,7);
								$date_out_cico = $this->m_inbound_cico->select_cico($tahun_bulan,$date_out_ori,$np);
								
								foreach ($date_out_cico->result_array() as $data) 
								{
									$date_out_id_cuti			= $data['id_cuti'];
									$date_out_id_sppd			= $data['id_sppd'];
								}
								
								//jika lembur dia di saat cuti/sppd/cuti bersama maka dihitung manual by sdm
								if ((!empty($date_out_id_cuti) || !empty($date_out_id_sppd) || $date_out_is_cuti_bersama==true)) {
									$this->m_inbound_cico->update_overtime($id_lembur_rate_selanjutnya); //jadikan lembur manual
									$ESS = "ESS";  //penanda biar jadi - di SAP nya
								}else
								{
									$ESS = "ESSL"; //penanda biar jadi + di SAP nya
								}						
							}else
							{
								$ESS = "ESS";  //penanda biar jadi - di SAP nya
							}
							
							
							$i++;
							$arr_in[$i] = array(
							'np_karyawan'		=> $np_karyawan,
							'personel_number'	=> $personel_number,
							'date'				=> $date_out,
							'time_type'			=> $clock_in,
							'time'				=> '000000',
							'terminal_id'		=> "$ESS",
							'transaction_type'	=> ''					
							);
							
						}
						
						//Kebutuhan SAP, untuk mengantisipasi generate automatis ESS P20 0000, harus dibedakan satu menit agar tidak tabrakan						
						if($time_out=='000000')
						{
							$time_out = '000100';
						}					
					
						//array inbound cico OUT ke sap
						$arr_out[$i] = array(
							'np_karyawan'		=> $np_karyawan,
							'personel_number'	=> $personel_number,
							'date'				=> $date_out,
							'time_type'			=> $clock_out,
							'time'				=> $time_out,
							'terminal_id'		=> $tapping_terminal_2,
							'transaction_type'	=> ''				
						);
						
						//check tk/tm/ab sudah di pindah ke inbound absence saja tidak perlu insert
						/*
						if ($is_tk || $is_tm || $is_ab) {
							//cek jika libur
							$cek = $this->m_inbound_cico->cek_libur($dws_tanggal);

							//jika tidak libur
							if ($cek) {
								if ($is_tm) {
									$arr_tm[$i] = array(
										'np_karyawan'	=> $np_karyawan,
										'nama'			=> $nama,
										'nama_jabatan'	=> $nama_jabatan,
										'nama_unit'		=> $nama_unit,
										'personel_number'	=> $personel_number,
										'info_type'		=> '2010',
										'absence_type'	=> '7020',
										'start_date'	=> $date_in,
										'end_date'		=> $date_in,
										'kode_pamlek'	=> 'TM'
									);
									$where = array(
										'np_karyawan'	=> $np_karyawan,
										'start_date'	=> $date_in,
										'end_date'		=> $date_in
									);
									if ($this->m_inbound_cico->check_absence($where, $tabel_perizinan)) {
										$id_perizinan = $this->m_inbound_cico->insert_inbound($tabel_perizinan, $arr_tm[$i]);
										$this->update_id_perizinan($id_perizinan, $id, $tabel_cico);
									}else{
										$this->m_inbound_cico->update_inbound($tabel_perizinan, $arr_tm[$i], $where);
									}
								}
								if ($is_tk) {
									$arr_tk[$i] = array(
										'np_karyawan'	=> $np_karyawan,
										'nama'			=> $nama,
										'nama_jabatan'	=> $nama_jabatan,
										'nama_unit'		=> $nama_unit,
										'personel_number'	=> $personel_number,
										'info_type'		=> '2010',
										'absence_type'	=> '7021',
										'start_date'	=> $date_out,
										'end_date'		=> $date_out,
										'kode_pamlek'	=> 'TK'
									);
									$where = array(
										'np_karyawan'	=> $np_karyawan,
										'start_date'	=> $date_out,
										'end_date'		=> $date_out
									);
									if ($this->m_inbound_cico->check_absence($where, $tabel_perizinan)) {
										$id_perizinan = $this->m_inbound_cico->insert_inbound($tabel_perizinan, $arr_tk[$i]);
										$this->update_id_perizinan($id_perizinan, $id, $tabel_cico);
									}else{
										$this->m_inbound_cico->update_inbound($tabel_perizinan, $arr_tk[$i], $where);
									}
								}

								if ($is_ab) {
									$arr_ab[$i] = array(
										'np_karyawan'	=> $np_karyawan,
										'nama'			=> $nama,
										'nama_jabatan'	=> $nama_jabatan,
										'nama_unit'		=> $nama_unit,
										'personel_number'	=> $personel_number,
										'info_type'		=> '2001',
										'absence_type'	=> '4000',
										'start_date'	=> $date_in,
										'end_date'		=> $date_out,
										'kode_pamlek'	=> 'AB'
									);
									$where = array(
										'np_karyawan'	=> $np_karyawan,
										'start_date'	=> $date_in,
										'end_date'		=> $date_out
									);
									if ($this->m_inbound_cico->check_absence($where, $tabel_perizinan)) {
										$id_perizinan = $this->m_inbound_cico->insert_inbound($tabel_perizinan, $arr_ab[$i]);
										$this->update_id_perizinan($id_perizinan, $id, $tabel_cico);
									}else{
										$this->m_inbound_cico->update_inbound($tabel_perizinan, $arr_ab[$i], $where);
									}
								}
													
							}else{
								$is_tm = false;
								$is_tk = false;
								$is_ab = false;
							}
						}
						
						if (!$is_tk && !$is_tm && !$is_ab) {
							$where = array(
								'np_karyawan'	=> $np_karyawan,
								'start_date'	=> $date_in,
								'end_date'		=> $date_out
							);
							if (!$this->m_inbound_cico->check_absence($where, $tabel_perizinan)) {
								$id_perizinan = $this->m_inbound_cico->delete_inbound($tabel_perizinan, $where);
								$this->delete_id_perizinan($id_perizinan, $id, $tabel_cico);
							}
						}
						*/
						
						
					
								
						$i++;
					}
				}
			
			} //end of TIDAK LEMPAR
		}
		
		//$hasil = 'NP_KARYAWAN'."\t".'PERSONNEL_NUMBER'."\t".'DATE'."\t".'TIME_TYPE'."\t".'TIME'."\t".'TERMINAL_ID'."\t".'TRANSACTION_TYPE'."\n";
        $hasil = '';
		for ($index=0; $index < $i; $index++) { 

			$hasil = $hasil."".$arr_in[$index]['np_karyawan']."\t".$arr_in[$index]['personel_number']."\t".$arr_in[$index]['date']."\t".$arr_in[$index]['time_type']."\t".$arr_in[$index]['time']."\t".$arr_in[$index]['terminal_id']."\t".$arr_in[$index]['transaction_type']."\t"."\r\n";
			$hasil = $hasil."".$arr_out[$index]['np_karyawan']."\t".$arr_out[$index]['personel_number']."\t".$arr_out[$index]['date']."\t".$arr_out[$index]['time_type']."\t".$arr_out[$index]['time']."\t".$arr_out[$index]['terminal_id']."\t".$arr_out[$index]['transaction_type']."\t"."\r\n";
		}

		$nama_file = "ESS_CICO";
		if ( ! write_file(FCPATH . "inbound_sap/inbound_cico/$nama_file.txt", $hasil)){
			echo 'Gagal membuat file txt';
		} else {
			echo "File $nama_file.txt";
		}
		
		// echo "<br><br>Selesai ".date('Y-m-d H:i:s')."<br>";
		$msc = microtime(true)-$msc;
        // echo "<br><br>Done. ".date('Y-m-d H:i:s').". Execution time: $msc seconds.<br>";
        echo "<br><br>Done. Execution time: $msc seconds.<br>";
		
		//insert ke tabel 'ess_status_proses_output', id proses = 10
        $this->db->insert('ess_status_proses_output', ['id_proses'=>10, 'waktu'=>date('Y-m-d H:i:s')]);
	}

	public function update_id_perizinan($id_perizinan, $id, $table)
	{
		$id_perizinan_now = $this->m_inbound_cico->get_id_perizinan($id, $table);
			
		$id_perizinan_now=explode(",",$id_perizinan_now);
			
		$data_sama='0';
		$array = array();
		foreach($id_perizinan_now as $val)
		{
			array_push($array,$val);
			if($val==$id_perizinan)
			{
				$data_sama='1';
			}
		}
		
		if($data_sama=='0')
		{
			array_push($array,$id_perizinan);
		}
		
		//hapus data kosong
		if (($key = array_search('', $array)) !== false) {
			unset($array[$key]);
		}
		
		$id_perizinan=implode(',',$array);

		$this->m_inbound_cico->update_id_perizinan($id_perizinan, $id, $table);
	}

	public function delete_id_perizinan($id_perizinan, $id, $table)
	{
		$id_perizinan_now = $this->m_inbound_cico->get_id_perizinan($id, $table);
			
		$id_perizinan_now=explode(",",$id_perizinan_now);
			
		$data_sama='0';
		$array = array();
		foreach($id_perizinan_now as $val)
		{
			array_push($array,$val);
			if($val==$id_perizinan)
			{
				$data_sama='1';
			}
		}
		
		if($data_sama=='0')
		{
			array_push($array,$id_perizinan);
		}
		
		//hapus data kosong
		if (($key = array_search('', $array)) !== false) {
			unset($array[$key]);
		}
		
		//hapus id_perizinan
		if (($key = array_search($id_perizinan, $array)) !== false) {
			unset($array[$key]);
		}
		
		$id_perizinan=implode(',',$array);

		$this->m_inbound_cico->update_id_perizinan($id_perizinan, $id, $table);
	}
}

