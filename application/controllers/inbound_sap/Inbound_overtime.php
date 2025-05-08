<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inbound_overtime extends CI_Controller {
	
	 
	function __construct()
	{
		parent::__construct();
		$this->load->helper('karyawan');
		$this->load->model('inbound_sap/m_inbound_overtime');
		
	}
	
	public function index()
	{
		redirect(base_url('dashboard'));
	}
	
	public function create_file($from,$to,$np=null)
	{
		//include helper file
		$this->load->helper('file');

		//run program selamanya untuk menghindari maximal execution
		//ini_set('MAX_EXECUTION_TIME', -1);
		set_time_limit('0');
		
		//$this->output->enable_profiler(TRUE);		
		
		echo "Proses data di tabel Overtime untuk dijadikan txt";
		echo "<br>mulai ".date('Y-m-d H:i:s')."<br><br>";
		
			
		//jika proses hari ini 
		if(strcmp($from,"today")==0){
			$date_start = date("Y-m-01", strtotime("-1 months"));
		} else{
            $date_start = $from;
        }
        
		if(strcmp($to,"today")==0){
            $date_end = date("Y-m-t", strtotime("-1 months"));
		} else{
            $date_end = $to;
        }
        
		$tampil='';
        //$table_name = '';
		while (strtotime($date_start) <= strtotime($date_end)) {
            $date=$date_start;

            //ambil data
            if(@$np){
                $hasil = $this->m_inbound_overtime->select_overtime($date, $np);
            } else{
                $hasil = $this->m_inbound_overtime->select_overtime($date);
            }

            //olah data		
        
            
            if($hasil->num_rows()>0){
                foreach($hasil->result_array() as $data){
					
					//TIDAK LEMPAR KETIKA		
					$this->load->helper('karyawan_helper');
					$tm_action = tm_status_erp_master_data($data['no_pokok'],$data['tgl_dws']);			
					if($tm_action['action']=='ZI' || //skorsing dengan gaji
					$tm_action['action']=='ZL' ||  //sakit berkepanjangan
					($tm_action['action']=='ZN' && $tm_action['tm_status']=='0') || //MPP
					($tm_action['action']=='' || $tm_action['tm_status']==null)) //tidak ada data
					{
						//do nothing
					}else
					{				
						$id					= $data['id'];
						$np_karyawan		= $data['no_pokok'];
						$personnel_number	= $data['personel_number'];

						$waktu_mulai_fix = $data['waktu_mulai_fix'];

						$date				= substr($data['waktu_mulai_fix'],0,10);
						
						//update 7648-Tri Wibowo 6 November 2019, ambil tanggal, untuk mengantisipasi lintas hari
						$date_awal				= substr($data['waktu_mulai_fix'],0,10);
						$date_akhir				= substr($data['waktu_selesai_fix'],0,10);
						
						$start_time			= substr($data['waktu_mulai_fix'],11,5);
						$end_time			= substr($data['waktu_selesai_fix'],11,5);

						$overtime_break		= $data['overtime_break'];	
						$time_type			= $data['time_type'];
						$transaction_type	= $data['transaction_type'];

						$pisah_date			= explode('-',$date);
						$tahun				= $pisah_date[0];
						$bulan				= $pisah_date[1];
						$tanggal			= $pisah_date[2];

						$write_date			= $tahun."".$bulan."".$tanggal;
						$original_start_time	= $start_time;
						$original_end_time		= $end_time;
						$start_time			= str_replace(':','',$start_time);
						$end_time			= str_replace(':','',$end_time);
																		
						//check APAKAH TK TM AB = dia tidak kirim
						$tidak_kirim = false;
						$tahun_bulan = $tahun."-".$bulan;
						$cico = $this->m_inbound_overtime->select_cico($tahun_bulan,$date,$np_karyawan);	
						foreach ($cico->result_array() as $data) 
						{												
							$tapping_time_1		= $data['tapping_time_1'];
							$tapping_fix_1		= $data['tapping_fix_1'];						
							$tapping_time_2		= $data['tapping_time_2'];
							$tapping_fix_2		= $data['tapping_fix_2'];
				
							$is_tm = false; //jika tidak ada CI
							$is_tk = false; //jika tidak ada CO
							$is_ab = false; //jika tidak ada CI & CO
							$is_off = false; //jika dws_name_fix == OFF
							$tidak_kirim = false; //default pasti kirim cico
							
													
							//TK
							if($tapping_fix_1!=null || $tapping_fix_1!='') { //jika terdapat data tapping IN FIX
								
							} else { //jika tidak terdapat data tapping IN FIX
								if($tapping_time_1!=null || $tapping_time_1!='') {
									
								} else {
									$is_tm = true;							
								}
							}
					
							//TM
							if($tapping_fix_2!=null || $tapping_fix_2!='') { //jika terdapat data tapping IN FIX
							
							} else { //jika tidak terdapat data tapping IN FIX
								if($tapping_time_2!=null || $tapping_time_2!='') {
									
								} else {
									$is_tk = true;								
								}
							}
							
							//CEK JIKA Abesence
							if ($is_tk && $is_tm) {
								$is_tm = false;
								$is_tk = false;
								$is_ab = true;
							}
											
						}	
						
						//jika absen tidak lengkap maka tidak kirim
						if($is_tk==true || $is_tm==true || $is_ab==true)
						{
							$tidak_kirim = true;
							$this->m_inbound_overtime->update_overtime($id); //verikasi manual SDM
						}
						
						
						
							
							
							
						/*if($tampil=='')
						{
							$tampil = 'NP_KARYAWAN'."\t".'PERSONNEL_NUMBER'."\t".'DATE'."\t". 'START_TIME'."\t".'END_TIME'."\t".'OVERTIME_BREAK'."\t".'TIME_TYPE'."\t".'TRANSACTION_TYPE'."\n";
						}*/
						if($tidak_kirim==false)
						{
							//update 7648-Tri Wibowo 6 November 2019, ambil tanggal, untuk mengantisipasi lintas hari
							if($date_awal<$date_akhir && $date_awal!=$date_akhir) //kemungkinan lintas hari
							{
								$date_plus = date('Y-m-d', strtotime($date . ' +1 day'));						
								$pisah_date_plus		= explode('-',$date_plus);
								$tahun_plus				= $pisah_date_plus[0];
								$bulan_plus				= $pisah_date_plus[1];
								$tanggal_plus			= $pisah_date_plus[2];
								$write_date_plus		= $tahun_plus."".$bulan_plus."".$tanggal_plus;
						
								$tampil = $tampil."".$np_karyawan."\t".$personnel_number."\t".$write_date."\t". $start_time."\t".'2400'."\t".$overtime_break."\t".$time_type."\t".$transaction_type."\t"."\r\n";
								
								//21 07 2022 Tri Wibowo 7648, jika end time nya 00:00 -> lebihin satu menit biar tidak di kira full time oleh SAP
								if($end_time=='0000')
								{
									$end_time='0001';
								}

								$tampil = $tampil."".$np_karyawan."\t".$personnel_number."\t".$write_date_plus."\t".'0000'."\t".$end_time."\t".$overtime_break."\t".$time_type."\t".$transaction_type."\t"."\r\n";
								
							}else
							{
								 $tampil = $tampil."".$np_karyawan."\t".$personnel_number."\t".$write_date."\t". $start_time."\t".$end_time."\t".$overtime_break."\t".$time_type."\t".$transaction_type."\t"."\r\n";
							}
							
						}
					} //end of TIDAK LEMPAR
                }
				
            }

         
            
           // $table_name = $tabel_inbound_overtime;
            $date_start = date ("Y-m-d", strtotime("+1 day", strtotime($date_start)));
		}
		
		$olah_tanggal		= str_replace("-","",$date_end);

        //$tabel_inbound_overtime = "ESS_OVERTIME_".$olah_tanggal;
		$tabel_inbound_overtime = "ESS_OVERTIME";

        if ( ! write_file(FCPATH . "inbound_sap/inbound_overtime/$tabel_inbound_overtime.txt", $tampil))
        {
            echo 'Gagal membuat file txt';
        }
        else
        {
            echo "File $tabel_inbound_overtime.txt"."<br>";
        }
				
		echo "<br><br>selesai ".date('Y-m-d H:i:s');
		
		//insert ke tabel 'ess_status_proses_output', id proses = 12
        $this->db->insert('ess_status_proses_output', ['id_proses'=>12, 'waktu'=>date('Y-m-d H:i:s')]);
		
	}
}
