<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pamlek_to_ess_get_id extends CI_Controller {
	
	 
	function __construct()
	{
		parent::__construct();
		$this->load->model('pamlek/m_pamlek_to_ess');
		
	}
	
	public function index()
	{
		redirect(base_url('dashboard'));
	}
	
	public function inisialisasi($pa_start,$pa_end,$pa_np_karyawan)
	{
	//	$this->output->enable_profiler(TRUE);
		
		//run program selamanya untuk menghindari maximal execution
		set_time_limit('0');
		
		//jika proses hari ini 
		if(strcmp($pa_start,"today")==0){
			$start = date("Y-m-d");			
		}		
		if(strcmp($pa_end,"today")==0){
			$end = date("Y-m-d");
		}
		
		$tanggal_awal_proses = date('Y-m-d', strtotime($pa_start . ' -2 day'));
		$tanggal_akhir_proses = date('Y-m-d', strtotime($pa_end . ' -2 day'));
		
	//	echo "<br>===========================================================";
	//	echo "<br>Memulai Proses Pamlek_to_ess ".date("Y-m-d H:i:s");
	//	echo "<br>===========================================================";
			
		//looping tanggal
		$tanggal_proses=$tanggal_awal_proses;
		while (strtotime($tanggal_proses) <= strtotime($tanggal_akhir_proses))
		{
			
			
			$tahun_bulan = substr($tanggal_proses,0,7);
			
			/*
			==================
			Hari Ini
			==================
			*/
			$master = $this->m_pamlek_to_ess->select_master_data($tahun_bulan,$tanggal_proses,$pa_np_karyawan);
			
			$arr_master = array();
			foreach ($master->result_array() as $data) 
			{
			
				//jika ada substitution
				$dws_fix 					= null;
				$tanggal_start_dws_fix		= null;
				$start_time_fix				= null;
				$tanggal_end_dws_fix		= null;
				$end_time_fix				= null;
				$start_break_fix			= null;
				$end_break_fix				= null;
				$np_karyawan				= $data['np_karyawan'];
				
				
				$substitution = $this->m_pamlek_to_ess->get_substitution($np_karyawan,$tanggal_proses);
				if($substitution['date'])
				{
					$lintas_hari_masuk = $substitution['lintas_hari_masuk'];
					$lintas_hari_pulang = $substitution['lintas_hari_pulang'];
					
					if($lintas_hari_masuk==1)
					{
						$tanggal_start_dws_fix= date('Y-m-d', strtotime($tanggal_proses . ' +1 day'));
					}else
					{
						$tanggal_start_dws_fix=$tanggal_proses;
					}
					
					if($lintas_hari_pulang==1)
					{
						$tanggal_end_dws_fix= date('Y-m-d', strtotime($tanggal_proses . ' +1 day'));
					}else
					{
						$tanggal_end_dws_fix=$tanggal_proses;
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
				
				if($dws_fix!=null || $dws_fix!='')
				{
					$sekarang_dws=$dws_fix;
				}else
				{
					$sekarang_dws=$dws;
				}
				
				if($tanggal_start_dws_fix!=null || $tanggal_start_dws_fix!='')
				{
					$sekarang_tanggal_start_dws=$tanggal_start_dws_fix;
				}else
				{
					$sekarang_tanggal_start_dws=$tanggal_start_dws;
				}
				
				if($start_time_fix!=null || $start_time_fix!='')
				{
					$sekarang_start_time=$start_time_fix;
				}else
				{
					$sekarang_start_time=$start_time;
				}
				
				if($tanggal_end_dws_fix!=null || $tanggal_end_dws_fix!='')
				{
					$sekarang_tanggal_end_dws=$tanggal_end_dws_fix;
				}else
				{
					$sekarang_tanggal_end_dws=$tanggal_end_dws;
				}
				
				if($end_time_fix!=null || $end_time_fix!='')
				{
					$sekarang_end_time=$end_time_fix;
				}else
				{
					$sekarang_end_time=$end_time;
				}
				
				if($start_break_fix!=null || $start_break_fix!='')
				{
					$sekarang_start_break=$start_break_fix;
				}else
				{
					$sekarang_start_break=$start_break;
				}
				
				if($end_break_fix!=null || $end_break_fix!='')
				{
					$sekarang_end_break=$end_break_fix;
				}else
				{
					$sekarang_end_break=$end_break;
				}
				
				
				
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
				$tanggal_proses = date ("Y-m-d", strtotime("+1 day", strtotime($tanggal_proses)));//looping tambah 1 date
		}
		
		echo "<br>===========================================================";
		echo "<br>Selesai Proses Pamlek_to_ess ".date("Y-m-d H:i:s");
		echo "<br>===========================================================";			
	}
}
