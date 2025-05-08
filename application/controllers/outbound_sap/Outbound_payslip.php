<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Outbound_payslip extends CI_Controller {	 
	function __construct(){
		parent::__construct();
		$this->load->model('outbound_sap/m_outbound_payslip');
		$this->load->model('m_setting');
		$this->folder_payslip	= dirname($_SERVER["SCRIPT_FILENAME"])."/outbound_sap/E_PAYSLIP/";
	}
	
	public function index(){
		redirect(base_url('dashboard'));
	}
	
	//Fungsi ini merupakan fungsi untuk update data file dari pamlek agar ter-registrasi di sistem peruri
	public function get_files(){
		//run program selamanya untuk menghindari maximal execution
		//ini_set('MAX_EXECUTION_TIME', -1);
		set_time_limit('0');
		
		//$this->output->enable_profiler(TRUE);
		
		echo "Proses ambil nama file terbaru";
		echo "<br>mulai ".date('Y-m-d H:i:s')."<br>";
		
		//ambil setting an di dalam database
		$payslip_url	= $this->folder_payslip;
		
		//ambil data tentang pamlek file yang sudah ada dalam database
		$result 	= $this->m_outbound_payslip->select_payslip_files();
		
		$arr_registered_payslip_files = array();
		foreach ($result->result_array() as $data){
			array_push($arr_registered_payslip_files,$data['nama_file']);		
		 
		}

		$old = umask(0);
		
		//check folder ada
		if(is_dir($payslip_url)){
			//scan file .tk dalam server ftp pamlek 
			$data = scandir($payslip_url);
			$query = "";
			foreach($data as $file){
				
				//tambahkan data file .tk terbaru dalam database
				if(!in_array($file,$arr_registered_payslip_files) and strcmp(substr($file,-4),".txt")==0){
					$data_db = array(
						'nama_file'			=> $file,
						'size' 				=> filesize($payslip_url.$file),
						'last_modified'		=> date("Y-m-d H:i:s",filemtime($payslip_url.$file)) ,					
					);
					
					$insert_data = $this->m_outbound_payslip->insert_files($data_db);
				
					if($insert_data==true){
						echo "<br>status = $file berhasil masuk db";
					}
					else{
						echo "<br>status = gagal masuk db<br>";
					}
				}
			}
		}
		else{
			$data_error['modul'] 		= "outbound_sap/outbound_payslip/get_files/"; 
			$data_error['error'] 		= "Gagal konek ke Server Pamlek"; 
			$data_error['status'] 		= "0";
			$data_error['created_at'] 	= date("Y-m-d H:i:s");
			$data_error['created_by'] 	= "scheduler";
			
			$this->m_outbound_payslip->insert_error($data_error);
			echo "<br>status = ".$data_error['error'].", ".$data_error['modul'];
		}			
		
		umask($old);
		if ($old != umask()) {
			die('An error occurred while changing back the umask');
		}
		
		echo "<br><br>selesai ".date('Y-m-d H:i:s')."<br>";		
	}
	
	//Fungsi untuk mengambil data dalam file .tk yang ada di Pamlek
	public function get_data(){
		//run program selamanya untuk menghindari maximal execution
		//ini_set('MAX_EXECUTION_TIME', -1);
		set_time_limit('0');
		
		//$this->output->enable_profiler(TRUE);
		
		echo "Proses ambil data dari txt";
		echo "<br>mulai ".date('Y-m-d H:i:s')."<br>";
		
		$payslip_url	= $this->folder_payslip;
		
		/*$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0987654321";*/
		
		//ambil data mana saja yang belum di proses
		$result = $this->m_outbound_payslip->select_payslip_files_unproses();
	
		$arr_registered_payslip_files = array();
		foreach ($result->result_array() as $data) 
		{
			array_push($arr_registered_payslip_files,$data['nama_file']);	 
		}
		
		//check folder ada
		if(is_dir($payslip_url))
		{
			$arr_scan_payslip_files = scandir($payslip_url);
			
			$payslip_files = array();		
			foreach($arr_scan_payslip_files as $file){
				if(in_array($file,$arr_registered_payslip_files)){
					array_push($payslip_files,$file);
				}
			}
			
			$reset_increment = false;
			
			foreach($payslip_files as $file){
				$arr_id_payment_date = array();
				$arr_id_payment_karyawan = array();
				
				$arr_karyawan = array();
				
				//echo "<br>".$file."<br><br>";
			
				$rows = explode("\n",trim(file_get_contents($payslip_url.$file)));
				
				$banyak_data=0;			
				$cek_payment_date = array();
				
				$array_insert_data = array();
				$count = count($rows);
				
				for($i=1;$i<$count and ($i==1 or count($cek_payment_date)>0);$i++){
					//parsing data di file
					
					$pisah = preg_split('/\s+/', $rows[$i]);
					$pisah[0] = substr($pisah[0],0,4)."-".substr($pisah[0],4,2)."-".substr($pisah[0],6,2);
					
					$cek_payment_date = $this->m_outbound_payslip->get_id_payment_header($pisah[0]);
				}
				
				echo "<br><br>$file ".count($cek_payment_date)." ".$pisah[2]." ".$pisah[0].date("Y-m-d");
				
				if(count($cek_payment_date)==0 or (strcmp($pisah[2],"000000")!=0 and strcmp(str_replace("-","",$pisah[0]),substr($file,8,8))>=0)){
					//echo " IN IF";
					$arr_with_payslip = array();
				
					$pesan_1 = $this->m_setting->ambil_pengaturan("pesan gaji baris 1");
					$pesan_2 = $this->m_setting->ambil_pengaturan("pesan gaji baris 2");
				
					for($i=1;$i<$count;$i++){
						$row=$rows[$i];
						$pisah = preg_split('/\s+/', $row);
						
						if((strcmp($pisah[1],"000000")==0 and strcmp($pisah[2],"000000")==0) or (strcmp($pisah[1],"000000")!=0 and strcmp($pisah[2],"000000")!=0)){
							if(strcmp($pisah[1],$pisah[2])!=0 and strcmp($pisah[1],"000000")!=0){
								$pisah[0] = $pisah[2].substr($pisah[0],6,2);
							}
							
							$pisah[0] = substr($pisah[0],0,4)."-".substr($pisah[0],4,2)."-".substr($pisah[0],6,2);
							
							if(!array_key_exists($pisah[0],$arr_id_payment_date)){
								$arr_id_payment_date[$pisah[0]] = $this->payment_header($pisah[0],$pesan_1,$pesan_2);
							}
							//echo "<br>".__LINE__;
							//var_dump(array_key_exists($arr_id_payment_date[$pisah[0]]."_".$pisah[5],$arr_id_payment_karyawan));
							if(!array_key_exists($arr_id_payment_date[$pisah[0]]."_".$pisah[5],$arr_id_payment_karyawan)){
								$arr_id_payment_karyawan[$arr_id_payment_date[$pisah[0]]."_".$pisah[5]] = $this->payment_karyawan($arr_id_payment_date[$pisah[0]]."_".$pisah[5]);
								//echo "<br>".$file." ".$arr_id_payment_date[$pisah[0]]."_".$pisah[5]."<br>";
								$this->m_outbound_payslip->hapus_rincian_payslip($arr_id_payment_karyawan[$arr_id_payment_date[$pisah[0]]."_".$pisah[5]]);
								
								$reset_increment = true;
							}
							
							$parameter = rand(0,999);
							
							$insert_data = array(
									'id_payslip_karyawan'	=> $arr_id_payment_karyawan[$arr_id_payment_date[$pisah[0]]."_".$pisah[5]],
									'payment_date'			=> $pisah[0],
									'for_period'			=> $pisah[1],
									'in_period'				=> $pisah[2],
									'wage_type'				=> $pisah[3],
									'personel_number'	 	=> $pisah[4],
									'np_karyawan'			=> $pisah[5],
									'amount'				=> $pisah[6],
									'parameter'				=> $parameter,
									'proses_encrypt'		=> '0'
								);	
								
							array_push($array_insert_data,$insert_data);
							
							if(in_array($pisah[3],array("/557","/559"))){
								array_push($arr_with_payslip,$arr_id_payment_karyawan[$arr_id_payment_date[$pisah[0]]."_".$pisah[5]]);
							}
						}
						
						//insert data ke database dengan sistem batch per 1000
						
						if($i%1000==0){
							if(!empty($array_insert_data))
							{
								//echo "<hr>";
								//echo "inserting ".count($array_insert_data)." data<br>";
								$this->m_outbound_payslip->insert_data_batch($array_insert_data);
							}
							$array_insert_data = array();
							//$i=1;
						}
						
						$banyak_data++;
					}
					
					//insert sisa data sisa dari yg 1000
					if(!empty($array_insert_data)){
						$this->m_outbound_payslip->insert_data_batch($array_insert_data);
					}
					
					//beri nama
					$nama_pembayaran = "";
					if(strcmp($pisah[1],"000000")==0){
						$nama_offcycle = $this->m_outbound_payslip->get_nama_offcycle($pisah[0]);

						$arr_nama_pembayaran = explode(",",$nama_offcycle);

						if(count($arr_nama_pembayaran)==1){
							$nama_pembayaran = $arr_nama_pembayaran[0];
						}
						else if(count($arr_nama_pembayaran)==2){
							$nama_pembayaran = implode(" dan ",$arr_nama_pembayaran);
						}
						else if(count($arr_nama_pembayaran)>2){
							$arr_nama_pembayaran[count($arr_nama_pembayaran)-1] = " dan ".$arr_nama_pembayaran[count($arr_nama_pembayaran)-1];
							$nama_pembayaran = implode(", ",$arr_nama_pembayaran);
						}
						$nama_pembayaran .= " ".bulan_tahun($pisah[0]);
					}
					else{
						$this->m_outbound_payslip->set_display($arr_id_payment_date[$pisah[0]],$this->m_setting->ambil_pengaturan("Display gaji"));
						
						$nama_pembayaran = "Gaji ".bulan_tahun($pisah[0]);
					}
					$this->m_outbound_payslip->set_nama_payslip($arr_id_payment_date[$pisah[0]],$nama_pembayaran);
					
					//beri nama per karyawan
					if(strcmp($pisah[1],"000000")==0){
						$nama_offcycle_karyawan = $this->m_outbound_payslip->get_nama_offcycle_karyawan($pisah[0]);
						
						foreach($nama_offcycle_karyawan as $offcycle_karyawan){
							$np_karyawan = $offcycle_karyawan["np_karyawan"];
							$nama_offcycle = $offcycle_karyawan["nama_pembayaran"];
							
							$arr_nama_pembayaran = explode(",",$nama_offcycle);
							if(count($arr_nama_pembayaran)==1){
								$nama_pembayaran = $arr_nama_pembayaran[0];
							}
							else if(count($arr_nama_pembayaran)==2){
								$nama_pembayaran = implode(" dan ",$arr_nama_pembayaran);
							}
							else if(count($arr_nama_pembayaran)>2){
								$arr_nama_pembayaran[count($arr_nama_pembayaran)-1] = " dan ".$arr_nama_pembayaran[count($arr_nama_pembayaran)-1];
								$nama_pembayaran = implode(", ",$arr_nama_pembayaran);
							}
							$nama_pembayaran .= " ".bulan_tahun($pisah[0]);
							
							$this->m_outbound_payslip->set_nama_payslip_karyawan_offcycle($arr_id_payment_karyawan[$arr_id_payment_date[$pisah[0]]."_".$np_karyawan],$nama_pembayaran);
						}
					}
					else{					
						$nama_pembayaran = "Gaji ".bulan_tahun($pisah[0]);
						$this->m_outbound_payslip->set_nama_payslip_karyawan_regular($arr_id_payment_date[$pisah[0]],$nama_pembayaran);
					}
					
					
					// WT Negatif
					$wagetype_negatif = $this->m_outbound_payslip->get_wagetype_negatif();
					$this->m_outbound_payslip->update_proses_minus("N",$wagetype_negatif);
					$this->m_outbound_payslip->update_amount_wagetype_minus($pisah[0]);
					$this->m_outbound_payslip->update_proses_minus("Y",$wagetype_negatif);
					
					// encrypt
					$this->m_outbound_payslip->encrypt($pisah[0]);

					$this->m_outbound_payslip->update_with_payslip($arr_with_payslip);
					
					//update data karyawan : beri nama, unit kerja, dan jabatan
					$tahun_bulan = substr($pisah[0],0,4)."_".substr($pisah[0],5,2);
					echo "\n".$tahun_bulan."\n";
					$this->m_outbound_payslip->update_karyawan_payslip($arr_id_payment_date[$pisah[0]],$tahun_bulan);
					
					$tahun_awal = 2019;
					$tahun_akhir = (int)substr($pisah[0],0,4);
					for($i=$tahun_akhir;$i>=$tahun_awal;$i--){
						$bulan_akhir = 12;
						if($i==$tahun_akhir){
							$bulan_akhir = (int)substr($pisah[0],5,2);
						}
						for($j=$bulan_akhir;$j>=1;$j--){
							$tahun_bulan = $i."_".str_pad($j,2,"0",STR_PAD_LEFT);
							echo "\n".$tahun_bulan."\n";
							$this->m_outbound_payslip->update_karyawan_payslip_non_aktif($arr_id_payment_date[$pisah[0]],$tahun_bulan);
						}
					}
				}

				//status di erp_payslip_file proses=1, sehingga tidak di proses lagi 
				$update_file = array(
					'proses'			=> '1',
					'baris_data' 		=> $banyak_data,
					'waktu_proses'		=> date('Y-m-d H:i:s')
				);
				
				$this->m_outbound_payslip->update_files($file,$update_file);
				
			}
		
			if(count($payslip_files)>0){
				$this->peringkat_lembur();
			}
		
			if($reset_increment){
				$this->m_outbound_payslip->reset_increment();
			}
		}
		else{
			$data_error['modul'] 		= "outbound_sap/oubound_payslip/get_data"; 
			$data_error['error'] 		= "Gagal konek ke Server Integrasi SAP"; 
			$data_error['status'] 		= "0";
			$data_error['created_at'] 	= date("Y-m-d H:i:s");
			$data_error['created_by'] 	= "scheduler";
			
			$this->m_outbound_payslip->insert_error($data_error);
			echo "<br>status = ".$data_error['error'].", ".$data_error['modul'];
		}
		
		echo "<br>selesai ".date('Y-m-d H:i:s');
        
        //insert ke tabel 'ess_status_proses_input', id proses = 6
        $this->db->insert('ess_status_proses_input', ['id_proses'=>6, 'waktu'=>date('Y-m-d H:i:s')]);
	}

	public function get_payslip(){
		$this->get_files();
		$this->get_data();
	}
	
	private function payment_header($payment_date,$pesan_1,$pesan_2){
		$payment_header = $this->m_outbound_payslip->get_id_payment_header($payment_date);
		
		if(count($payment_header)==0){
			$this->m_outbound_payslip->insert_payment_header($payment_date,$pesan_1,$pesan_2);
			$payment_header = $this->m_outbound_payslip->get_id_payment_header($payment_date);
		}
		
		return $payment_header[0]["id"];
	}
	
	private function payment_karyawan($header_np){
		list($id_header,$np)=explode("_",$header_np);
		$payment_karyawan = $this->m_outbound_payslip->get_id_payment_karyawan($id_header,$np);
		
		if(count($payment_karyawan)==0){
			$this->m_outbound_payslip->insert_payment_karyawan($id_header,$np);
			$payment_karyawan = $this->m_outbound_payslip->get_id_payment_karyawan($id_header,$np);
		}
		
		return $payment_karyawan[0]["id"];
	}
	
	public function peringkat_lembur(){
		// Report all errors
		//error_reporting(E_ALL);

		// Display errors in output
		//ini_set('display_errors', 1);
			
		$this->m_outbound_payslip->truncate_rank_lembur();
		$this->m_outbound_payslip->generate_rank_lembur();
	}
}
