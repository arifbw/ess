<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inbound_substitution extends CI_Controller {
	
	 
	function __construct()
	{
		parent::__construct();
		$this->load->helper('karyawan');
		$this->load->model('inbound_sap/m_inbound_substitution');
		
	}
	
	public function index()
	{
		redirect(base_url('dashboard'));
	}
	
	public function create_file($today,$tomorrow,$np=null)
	{
		//include helper file
		$this->load->helper('file');
		$this->load->helper('karyawan_helper');

		//run program selamanya untuk menghindari maximal execution
		//ini_set('MAX_EXECUTION_TIME', -1);
		set_time_limit('0');
		
		//$this->output->enable_profiler(TRUE);		
		
		echo "Proses data di tabel Substitution untuk dijadikan txt";
		echo "<br>mulai ".date('Y-m-d H:i:s')."<br><br>";
		
			
		//jika proses hari ini 
		if(strcmp($today,"today")==0){
			$date_start = date("Y-m-01", strtotime("-1 months"));
		} else{
            $date_start = $today;
        }
        
		if(strcmp($tomorrow,"today")==0){
            $date_end = date("Y-m-t", strtotime("-1 months"));
		} else{
            $date_end = $tomorrow;
        }
	
		$tampil='';
		 
		while (strtotime($date_start) <= strtotime($date_end)) {
            $date=$date_start;

            //ambil data
            if(@$np){
                $hasil = $this->m_inbound_substitution->select_substitution($date, $np);
            } else{
                $hasil = $this->m_inbound_substitution->select_substitution($date);
            }
            

            //olah data		           
            if($hasil->num_rows()>0){
                foreach($hasil->result_array() as $data){
					
					//TIDAK LEMPAR KETIKA		
					$tm_action = tm_status_erp_master_data($data['np_karyawan'],$data['date']);			
					if($tm_action['action']=='ZI' || //skorsing dengan gaji
					$tm_action['action']=='ZL' ||  //sakit berkepanjangan
					($tm_action['action']=='ZN' && $tm_action['tm_status']=='0') || //MPP
					($tm_action['action']=='' || $tm_action['tm_status']==null)) //tidak ada data
					{
						//do nothing
					}else
					{
						
						$np_karyawan		= $data['np_karyawan'];
						$personnel_number	= $data['personel_number'];
						$date				= $data['date'];
						$dws				= $data['dws'];
						$dws_variant		= $data['dws_variant'];
						$transaction_type	= '3'; //insert pergantian

						$pisah_date			= explode('-',$date);
						$tahun				= $pisah_date[0];
						$bulan				= $pisah_date[1];
						$tanggal			= $pisah_date[2];

						$write_date			= $tahun."".$bulan."".$tanggal;

						/* SAP tidak memakai Header
						if($tampil=='')
						{						 
							$tampil = 'NP_KARYAWAN'."\t".'PERSONNEL_NUMBER'."\t".'DATE'."\t". 'DWS'."\t".'DWS_VARIANT'."\t".'TRANSACTION_TYPE'."\n";
						}
						*/

						$tampil = $tampil."".$np_karyawan."\t".$personnel_number."\t".$write_date."\t". $dws."\t".$dws_variant."\t".$transaction_type."\t"."\r\n";
					
					} //end of TIDAK LEMPAR
					
                }
            }

            $date_start = date ("Y-m-d", strtotime("+1 day", strtotime($date_start)));
		}
		
		$olah_tanggal		= str_replace("-","",$date_end);

        //$tabel_inbound_substitution = "ESS_SUBSTITUTION_".$olah_tanggal;
		$tabel_inbound_substitution = "ESS_SUBSTITUTION";

        if ( ! write_file(FCPATH . "inbound_sap/inbound_substitution/$tabel_inbound_substitution.txt", $tampil))
        {
            echo 'Gagal membuat file txt';
        }
        else
        {
            echo "File $tabel_inbound_substitution.txt"."<br>";
        }
				
		echo "<br><br>selesai ".date('Y-m-d H:i:s');
        
        //insert ke tabel 'ess_status_proses_output', id proses = 11
        $this->db->insert('ess_status_proses_output', ['id_proses'=>11, 'waktu'=>date('Y-m-d H:i:s')]);
		
	}
	
	

}


