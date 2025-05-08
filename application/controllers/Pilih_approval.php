<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Pilih_approval extends CI_Controller {
		public function __construct(){
			parent::__construct();
			
			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->load->helper("cutoff_helper");
			$this->load->helper("tanggal_helper");
			$this->load->helper("karyawan_helper");
			$this->load->helper("reference_helper");
			
			$this->data["is_with_sidebar"] = true;
		}
		
		public function ajax_getListNp()
		{			
			$tampil='';
			
			if($_SESSION["grup"]==4) //jika Pengadministrasi Unit Kerja
			{
				$list_kode_unit=array();
				$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
				foreach ($list_pengadministrasi as $data) //looping list_pengadministrasi
				{	
					array_push($list_kode_unit,$data['kode_unit']);								
				}
				
				$np_list=array();
				$np_list=$this->m_permohonan_cuti->select_np_by_kode_unit($list_kode_unit);						
								
				foreach ($np_list->result_array() as $np) 
				{		
					if($tampil)
					{				
						$tampil=$tampil."".$np['no_pokok']." | ".$np['nama']."\n";
					}else
					{
						$tampil=$np['no_pokok']." | ".$np['nama']."\n";
					}
					
				}
			}else
			if($_SESSION["grup"]==5) //jika Pengguna
			{
				$np 	= $_SESSION["no_pokok"];
				$tampil	= $np." | ".nama_karyawan_by_np($np)."\n";
			}else
			{
				$tampil = "Anda Memiliki Hak untuk semua nomer pokok Karyawan";
			}
			
			
	
			echo $tampil;				
		}
		
		public function ajax_getNama()
		{
			$np_karyawan	= $this->input->post('vnp_karyawan');	
			$x=$this->input->post('vnp_karyawan');	
			if($_SESSION["grup"]==4) //jika Pengadministrasi Unit Kerja
			{
				$temp='';
				$sudah_ketemu=0;
				$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
				foreach ($list_pengadministrasi as $data) //looping list_pengadministrasi
				{					
					if(kode_unit_by_np($np_karyawan)==$data['kode_unit']) //check apakah ada disalah satu unit 
					{
						if($sudah_ketemu==0)
						{
							$np_karyawan=$np_karyawan;
							$sudah_ketemu=1;
						}
						
					}						
				}
				
				if($sudah_ketemu==0)
				{
					$np_karyawan	= '';												
				}
			}
			else if($_SESSION["grup"]==5) //jika Pengguna
			{
				if($np_karyawan==$_SESSION["no_pokok"])
				{
					$np_karyawan	= $this->input->post('vnp_karyawan');						
				}else
				{
					$np_karyawan	= '';					
				}
			}
			
			$nama 			= nama_karyawan_by_np($np_karyawan);	
			
			
			$absence_quota = $this->m_permohonan_cuti->select_absence_quota_by_np($np_karyawan);
	 
			$sisa_cuti = null;
			foreach ($absence_quota->result_array() as $data) 
			{
				$sisa	= $data['number']-$data['deduction'];
				$cuti 	= substr($data['start_date'],0,4)." : ".$sisa." (masa aktif cuti ".tanggal($data['deduction_from'])." s/d ".tanggal($data['deduction_to']).")";
				if($sisa_cuti==null)
				{
					$sisa_cuti=$cuti;
				}else
				{
					$sisa_cuti = $sisa_cuti."\n$cuti";
				}
			}
			
			$cubes = $this->m_permohonan_cuti->select_cubes_by_np($np_karyawan);
	 
			$sisa_cubes = null;
			foreach ($cubes->result_array() as $data) 
			{				
				$cuti 	= $data['tahun']." : ".$data['sisa_bulan']. " bulan ". $data['sisa_hari']." hari (masa aktif cuti ".tanggal($data['tanggal_timbul'])." s/d ".tanggal($data['tanggal_kadaluarsa']).")";
				if($sisa_cubes==null)
				{
					$sisa_cubes=$cuti;
				}else
				{
					$sisa_cubes = $sisa_cubes."\n$cuti";
				}
			}
			
			$hutang = $this->m_permohonan_cuti->select_hutang_by_np($np_karyawan)->result_array();

			if($nama){
				$data= $nama;
			}
			else{
				$data = "\n==================\n"."NP tidak ditemukan"."\n==================\n";
				echo $data; 
				die();
			}
			
			if ($sisa_cuti){
				$data = $data."\n==================\n"."Sisa Cuti Tahunan"."\n==================\n"."$sisa_cuti";	
			
				$cuti_tahunan_menunggu_sdm  = cuti_tahunan_menunggu_sdm($np_karyawan);
					
				if($cuti_tahunan_menunggu_sdm=='')
				{
					$cuti_tahunan_menunggu_sdm=0;
				}
															
				$cuti_tahunan_menunggu_cutoff  = cuti_tahunan_menunggu_cutoff($np_karyawan);
				if($cuti_tahunan_menunggu_cutoff=='')
				{
					$cuti_tahunan_menunggu_cutoff=0;
				}
				
				$data=$data."\n\nCuti Tahunan Menunggu Persetujuan SDM : $cuti_tahunan_menunggu_sdm";
				$data=$data."\nCuti Tahunan Menunggu Cutoff ERP : $cuti_tahunan_menunggu_cutoff\n";
			}
			else{
				$data = $data."\n==================\n"."Belum ada Detail"."\n==================\n";
			}
			
			if($sisa_cubes){
				$data = $data."\n==================\n"."Sisa Cuti Besar"."\n==================\n"."$sisa_cubes";
				
				$cuti_besar_menunggu_sdm  = cuti_besar_menunggu_sdm($np_karyawan);
					
				if($cuti_besar_menunggu_sdm['menunggu_sdm_bulan']=='')
				{
					$menunggu_sdm_bulan=0;
				}else
				{
					$menunggu_sdm_bulan=$cuti_besar_menunggu_sdm['menunggu_sdm_bulan'];
				}
				
				if($cuti_besar_menunggu_sdm['menunggu_sdm_hari']=='')
				{
					$menunggu_sdm_hari=0;
				}else
				{
					$menunggu_sdm_hari=$cuti_besar_menunggu_sdm['menunggu_sdm_hari'];
				}

				$data=$data."\n\nCuti Besar Menunggu Persetujuan SDM : $menunggu_sdm_bulan Bulan $menunggu_sdm_hari Hari";
				
				
			}
			
			if(!empty($hutang)){
				$data = $data."\n\n==================\n"."Hutang Cuti : ".$hutang[0]["hutang"]." hari"."\n==================\n";
			}
			
			echo $data; 	
		}
		
		public function ajax_getNama_approval()
		{			
			$np_karyawan	 		= $this->input->post('vnp_karyawan_request');			
			$np_karyawan_atasan		= $this->input->post('vnp_karyawan');
			
			/* untuk testing 
			if($this->uri->segment(3))
			{
				echo "<br>TEST Start<br>";
				$this->output->enable_profiler(TRUE);
				$np_karyawan 		= $this->uri->segment(3);
				$np_karyawan_atasan = $this->uri->segment(4);

				echo $np_karyawan." atasan ".$np_karyawan_atasan;
				echo "<br>TEST END<br>";
			}
			*/
			
			//07 11 2022, Tri Wibowo 7648 - Check Apakah Karyawan Tersebut Request Atasan Yang Sesuai
			$this->load->model("master_data/m_karyawan");
			
			//ambil data detail atasan
			$karyawan_atasan = $this->m_karyawan->get_posisi_karyawan($np_karyawan_atasan);
			$periode = date("Y_m");
			if(empty($karyawan_atasan)){				
				$karyawan_atasan = $this->m_karyawan->get_posisi_karyawan_periode($np_karyawan_atasan,$periode);
				if(empty($karyawan_atasan)){
					$periode = date('Y_m', strtotime(date("Y-m-d") . " -1 months"));
					$karyawan_atasan = $this->m_karyawan->get_posisi_karyawan_periode($np_karyawan_atasan,$periode);
				}
			}	

			//ambil data detail karyawan
			$karyawan = $this->m_karyawan->get_posisi_karyawan($np_karyawan);
			$periode = date("Y_m");
			if(empty($karyawan["kode_unit"])){				
				$karyawan = $this->m_karyawan->get_posisi_karyawan_periode($np_karyawan,$periode);
				if(empty($karyawan["kode_unit"])){
					$periode = date('Y_m', strtotime(date("Y-m-d") . " -1 months"));
					$karyawan = $this->m_karyawan->get_posisi_karyawan_periode($np_karyawan,$periode);
				}
			}	
			
			$this->load->model("lembur/m_pengajuan_lembur");
            $arr_pilihan = $this->m_pengajuan_lembur->get_apv(array($karyawan["kode_unit"]),$np_karyawan);
			
			$return = [
				'status'=>false,
				'data'=>[
					'nama'=>'',
					'jabatan'=>''
				]
			];

			foreach ($arr_pilihan as $search) 
			{				
				if(strtoupper($search['no_pokok'])==strtoupper($np_karyawan_atasan))
				{	
								
					$return = [
						'status'=>true,
						'data'=>[
							'nama'=>$karyawan_atasan['nama'],
							'jabatan'=>$karyawan_atasan['nama_jabatan']
						]
					];							
				}
			}

			echo json_encode($return);
			//End Of 07 11 2022, Tri Wibowo 7648 
			
		}
		
		
		
		public function ajax_getAtasanCuti(){
			$np_karyawan	 = $this->input->post('vnp_karyawan');
			
			$this->load->model("master_data/m_karyawan");
			$karyawan = $this->m_karyawan->get_posisi_karyawan($np_karyawan);
			//var_dump($karyawan);
			
			if(empty($karyawan) or empty($karyawan["kode_unit"])){
				$periode_kemarin = date('Y_m', strtotime(date('Y-m-d') . ' -1 months'));
				
				$karyawan = $this->m_karyawan->get_posisi_karyawan_periode($np_karyawan,$periode_kemarin);
			}
			
			// unit
			if(strcmp($karyawan["posisi"],"unit")==0){
				// staf unit
				if(strcmp($karyawan["jabatan"],"staf")==0){
					$karyawan["posisi"] = "seksi";
				}
				// kepala unit
				if(strcmp($karyawan["jabatan"],"kepala")==0){
					$karyawan["jabatan"] = "staf";
					$karyawan["posisi"] = "seksi";
				}
				$karyawan["kode_unit"] = substr($karyawan["kode_unit"],0,strlen($karyawan["kode_unit"])-1);
			}
			
			if(strcmp($karyawan["jabatan"],"kepala")==0){
				$kode_unit_atasan_1 = str_pad(substr($karyawan["kode_unit"],0,strlen($karyawan["kode_unit"])-1),5,0);
				$kode_unit_atasan_2 = str_pad(substr($karyawan["kode_unit"],0,strlen($karyawan["kode_unit"])-2),5,0);
			}
			else{
				$kode_unit_atasan_1 = str_pad($karyawan["kode_unit"],5,0);
				if(strlen(preg_replace("/0+$/","",substr($karyawan["kode_unit"],0,strlen($karyawan["kode_unit"])-1)))>1){
					$kode_unit_atasan_2 = str_pad(substr($karyawan["kode_unit"],0,strlen($karyawan["kode_unit"])-1),5,0);
				}
				else{
					$kode_unit_atasan_2 = "";
				}
			}
			
			if(strcmp($kode_unit_atasan_1,"00000")==0){
				$kode_unit_atasan_1 = "10000";
			}
			
			if(strcmp($kode_unit_atasan_2,"00000")==0){
				$kode_unit_atasan_2 = "10000";
			}
			
			if(strcmp($kode_unit_atasan_1,$kode_unit_atasan_2)==0){
				$kode_unit_atasan_2 = "";
			}
			
			if(strcmp(str_pad($karyawan["kode_unit"],5,0),$kode_unit_atasan_1)==0 and strlen($karyawan["kode_unit"])==1){
				$kode_unit_atasan_1 = "";
			}
			
			
			$np_atasan_1 = $this->m_karyawan->get_atasan($kode_unit_atasan_1);
			$np_atasan_2 = $this->m_karyawan->get_atasan($kode_unit_atasan_2);
			
			$return = [
				'np_atasan_1'=>$np_atasan_1,
				'np_atasan_2'=>$np_atasan_2
			];
            echo json_encode($return);
		}
		
		public function action_insert_cuti()
		{			
			$submit = $this->input->post('submit');
					
			if($submit)
			{
                //echo json_encode($this->input->post()); exit();
				$np_karyawan		= $this->input->post('np_karyawan');				
				$absence_type		= $this->input->post('absence_type');
				$start_date			= date('Y-m-d', strtotime($this->input->post('start_date')));
				$end_date			= date('Y-m-d', strtotime($this->input->post('end_date')));
				$jumlah_hari 		= $this->input->post('jumlah_hari');
				$jumlah_bulan 		= $this->input->post('jumlah_bulan');
				$alasan				= $this->input->post('alasan');
				$keterangan			= $this->input->post('keterangan');
				$approval_1 		= $this->input->post('approval_1');
				$approval_1_jabatan = $this->input->post('approval_1_input_jabatan');
				$approval_2 		= $this->input->post('approval_2');
				$approval_2_jabatan = $this->input->post('approval_2_input_jabatan');
								
				$nama_karyawan 		= nama_karyawan_by_np($np_karyawan);
				$nama_approval_1 	= nama_karyawan_by_np($approval_1);
				$nama_approval_2 	= nama_karyawan_by_np($approval_2);
				
				$erp				= erp_master_data_by_np($np_karyawan, $start_date);				
				$kode_unit 			= $erp['kode_unit'];
				$nama_unit 			= $erp['nama_unit'];
				$nama_jabatan		= $erp['nama_jabatan'];
				$personel_number	= $erp['personnel_number'];
					
				//check cuti tahunan
				if($absence_type=='2001|1000') //cuti tahunan
				{
					$cuti_tahunan_menunggu_sdm  = cuti_tahunan_menunggu_sdm($np_karyawan);
					$sisa_cuti			= sisa_cuti_tahunan($np_karyawan);
						
					if($sisa_cuti=='')
					{
						$sisa_cuti=0;
					}
						
					if($cuti_tahunan_menunggu_sdm=='')
					{
						$cuti_tahunan_menunggu_sdm=0;
					}
																
					$cuti_tahunan_menunggu_cutoff  = cuti_tahunan_menunggu_cutoff($np_karyawan);
					if($cuti_tahunan_menunggu_cutoff=='')
					{
						$cuti_tahunan_menunggu_cutoff=0;
					}
					
					$permintaan = $cuti_tahunan_menunggu_sdm+$cuti_tahunan_menunggu_cutoff+$jumlah_hari;
					
					if($sisa_cuti < $permintaan)
					{				
						$tampil = "Sisa Cuti Tahunan Karyawan $np_karyawan tidak mencukupi <br>Permohonan = $jumlah_hari <br>Sisa Cuti Tahunan = $sisa_cuti <br>Cuti menunggu persetujuan SDM = $cuti_tahunan_menunggu_sdm  <br>Cuti menunggu cutoff = $cuti_tahunan_menunggu_cutoff";
							
						$this->session->set_flashdata('warning',$tampil);
						redirect(base_url($this->folder_controller.'permohonan_cuti'));					
					}
				}
				
				//check double input cuti
				$check_double_cuti = $this->m_permohonan_cuti->check_double_cuti($np_karyawan,$start_date,$end_date);
				
				if(@$check_double_cuti['id'])
				{
					$tampil = "Gagal, Terdapat permohonan di tanggal yang sama. Sudah terdapat permohonan cuti dengan tanggal awal ".$check_double_cuti['start_date']." dan tanggal akhir ".$check_double_cuti['end_date']." sudah tersedia ";
							
					$this->session->set_flashdata('warning',$tampil);
					redirect(base_url($this->folder_controller.'permohonan_cuti'));		
				}

		
				//check np pokok				
				if($nama_karyawan=='' || $nama_karyawan==null)
				{
					$this->session->set_flashdata('warning',"NP Karyawan <b>$np_karyawan</b> tidak ditemukan.");
					redirect(base_url($this->folder_controller.'permohonan_cuti'));
				}else
				if($nama_approval_1=='' || $nama_approval_1==null)
				{
					$this->session->set_flashdata('warning',"NP Atasan 1 <b>$approval_1</b> tidak ditemukan.");
					redirect(base_url($this->folder_controller.'permohonan_cuti'));
				}
				
				/* Tidak diwajibkan diisi
				else
				if($nama_approval_2=='' || $nama_approval_2==null)
				{
					$this->session->set_flashdata('warning',"NP Atasan 2 <b>$approval_2</b> tidak ditemukan.");
					redirect(base_url($this->folder_controller.'permohonan_cuti'));
				}
				*/
				
				if($np_karyawan==$approval_1 || $np_karyawan==$approval_2 )
				{
					$this->session->set_flashdata('warning',"NP Atasan harus berbeda dengan NP Pemohon.");
					redirect(base_url($this->folder_controller.'permohonan_cuti'));
				}else
				if($approval_1==$approval_2)
				{
					$this->session->set_flashdata('warning',"NP Atasan tidak boleh sama  <b>$approval_1 | $nama_approval_1</b>.");
					redirect(base_url($this->folder_controller.'permohonan_cuti'));
				}
				
				//check tanggal
				if($start_date>$end_date)
				{
					$this->session->set_flashdata('warning',"Tanggal Akhir Cuti harus lebih besar dari pada Tanggal Mulai Cuti.");
					redirect(base_url($this->folder_controller.'permohonan_cuti'));
				}
				
				//check sisa cuti untuk CUTI TAHUNAN
				if($absence_type=='2001|1000')
				{
					$check_cuti_tahunan=sisa_cuti_tahunan($np_karyawan);	
					
					$sisa_cuti = $check_cuti_tahunan;
					if($jumlah_hari=='0')
					{
						$this->session->set_flashdata('warning',"Sisa cuti tahunan <b>$np_karyawan | $nama_karyawan</b> Permohonan Cuti tidak boleh <= 0.<br>
						<table>
							<tr>
								<td>Sisa Cuti tahunan</td><td>&nbsp;&nbsp;:&nbsp;&nbsp;</td><td> $sisa_cuti </td>
							</tr>
							<tr>
								<td>Permohonan Cuti tahunan</td><td>&nbsp;&nbsp;:&nbsp;&nbsp;</td><td> $jumlah_hari </td>
							</tr>
						</table>");
						redirect(base_url($this->folder_controller.'permohonan_cuti'));
					}else
					if($sisa_cuti<=0 || $sisa_cuti<$jumlah_hari)
					{
						$this->session->set_flashdata('warning',"Sisa cuti tahunan <b>$np_karyawan | $nama_karyawan</b> tidak mencukupi.<br>
						<table>
							<tr>
								<td>Sisa Cuti tahunan</td><td>&nbsp;&nbsp;:&nbsp;&nbsp;</td><td> $sisa_cuti </td>
							</tr>
							<tr>
								<td>Permohonan Cuti tahunan</td><td>&nbsp;&nbsp;:&nbsp;&nbsp;</td><td> $jumlah_hari </td>
							</tr>
						</table>");
						redirect(base_url($this->folder_controller.'permohonan_cuti'));
					}
				}
				
				//check sisa cuti untuk CUTI BESAR
				if($absence_type=='2001|1010')
				{
					
					
					$cuti_besar_menunggu_sdm  = cuti_besar_menunggu_sdm($np_karyawan);
					
					if($cuti_besar_menunggu_sdm['menunggu_sdm_bulan']=='')
					{
						$menunggu_sdm_bulan=0;
					}else
					{
						$menunggu_sdm_bulan=$cuti_besar_menunggu_sdm['menunggu_sdm_bulan'];
					}
					
					if($cuti_besar_menunggu_sdm['menunggu_sdm_hari']=='')
					{
						$menunggu_sdm_hari=0;
					}else
					{
							$menunggu_sdm_hari=$cuti_besar_menunggu_sdm['menunggu_sdm_hari'];
					}
					
					//sisa cuti yang bisa dipakai
					$sisa_cuti	= sisa_cuti_besar($np_karyawan);
					$sisa_bulan	= $sisa_cuti['bulan']-$menunggu_sdm_bulan;
					$sisa_hari	= $sisa_cuti['hari']-$menunggu_sdm_hari;
					
					
					$sisa_cuti_bulan = $sisa_bulan;
					$sisa_cuti_hari = $sisa_hari;
					
					if(($sisa_cuti_bulan<$jumlah_bulan || $sisa_cuti_hari<$jumlah_hari))
					{
						$this->session->set_flashdata('warning',"Sisa cuti Besar <b>$np_karyawan | $nama_karyawan</b> tidak mencukupi.<br>
						<table>
							<tr>
								<td>Sisa Cuti Besar </td><td>&nbsp;&nbsp;:&nbsp;&nbsp;</td><td> $sisa_cuti_bulan bulan dan $sisa_cuti_hari hari </td>
							</tr>
							<tr>
								<td>Permohonan Cuti besar</td><td>&nbsp;&nbsp;:&nbsp;&nbsp;</td><td> $jumlah_bulan bulan dan $jumlah_hari hari </td>
							</tr>
						</table>");
						//$this->output->enable_profiler(TRUE);
						redirect(base_url($this->folder_controller.'permohonan_cuti'));
					}
				}
				
				//check validasi hari cuti
				if($absence_type=='2001|2060' || $absence_type=='2001|2066' || $absence_type=='2001|2061' || $absence_type=='2001|2062' || $absence_type=='2001|2063' || $absence_type=='2001|2068' || $absence_type=='2001|2067' || $absence_type=='2001|2064' || $absence_type=='2001|2065')
				{
					if($absence_type=='2001|2060')
						$hari_cuti = 3;
					else if($absence_type=='2001|2066')
						$hari_cuti = 1;
					else 
						$hari_cuti = 2;
					
					if($jumlah_hari > $hari_cuti)
					{
						$tipe = $this->m_permohonan_cuti->get_absence($absence_type);
						$this->session->set_flashdata('warning',"Jumlah Hari ".$tipe." Maksimal : ".$hari_cuti." Hari.");
						redirect(base_url($this->folder_controller.'permohonan_cuti'));
					}
				}
				
				//insert
				$data_insert['np_karyawan']			= $np_karyawan;
				$data_insert['personel_number']		= $personel_number;
				$data_insert['nama']				= $nama_karyawan;
				$data_insert['kode_unit']			= $kode_unit;					
				$data_insert['nama_unit']			= $nama_unit;	
				$data_insert['nama_jabatan']		= $nama_jabatan;								
				$data_insert['absence_type']		= $absence_type;
				$data_insert['start_date']			= $start_date;
				$data_insert['end_date']			= $end_date;
				$data_insert['jumlah_bulan']	 	= $jumlah_bulan;
				$data_insert['jumlah_hari']	 		= $jumlah_hari;
				$data_insert['alasan']				= $alasan;
				$data_insert['keterangan']			= $keterangan;
				$data_insert['approval_1']	 		= $approval_1;
				$data_insert['approval_1_jabatan']	= $approval_1_jabatan;
				$data_insert['approval_2']	 		= $approval_2;
				$data_insert['approval_2_jabatan']	= $approval_2_jabatan;
				
				$insert_cuti = $this->m_permohonan_cuti->insert_cuti($data_insert);
				
				if($insert_cuti!="0")
				{	
					$this->session->set_flashdata('success',"Permohonan Cuti <b>$np_karyawan | $nama_karyawan </b> berhasil ditambahkan.");
					
					//===== Log Start =====
					$arr_data_baru = $this->m_permohonan_cuti->select_cuti_by_id($insert_cuti);					
					$log_data_baru = "";					
					foreach($arr_data_baru as $key => $value){
						if(strcmp($key,"id")!=0){
							if(!empty($log_data_baru)){
								$log_data_baru .= "<br>";
							}
							$log_data_baru .= "$key = $value";
						}
					}									
					$log = array(
						"id_pengguna" => $this->session->userdata("id_pengguna"),
						"id_modul" => $this->data['id_modul'],
						"deskripsi" => "insert ".strtolower(preg_replace("/_/"," ",__CLASS__)),						
						"kondisi_baru" => $log_data_baru,
						"alamat_ip" => $this->data["ip_address"],
						"waktu" => date("Y-m-d H:i:s")
					);
					$this->m_log->tambah($log);
					//===== Log end =====
					
				}else
				{
					$this->session->set_flashdata('warning',"Permohonan Gagal");
				}	
				
				redirect(base_url($this->folder_controller.'permohonan_cuti'));			
				
			}else
			{					
				$this->session->set_flashdata('warning',"Terjadi Kesalahan");
				redirect(base_url($this->folder_controller.'permohonan_cuti'));	
			}	
		}
		
		public function action_batal_cuti()
		{			
			$submit = $this->input->post('submit');
					
			if($submit)
			{					
				$id 		= $this->input->post('batal_id');
				
				//===== Log Start =====
				$arr_data_lama = $this->m_permohonan_cuti->select_cuti_by_id($id);
				$log_data_lama = "";				
				foreach($arr_data_lama as $key => $value){
					if(strcmp($key,"id")!=0){
						if(!empty($log_data_lama)){
							$log_data_lama .= "<br>";
						}
						$log_data_lama .= "$key = $value";
					}
				}
				//===== Log end =====
					
				$batal_cuti = $this->m_permohonan_cuti->batal_cuti($id);
							
			
				if($batal_cuti!=0)
				{
					$this->session->set_flashdata('success',"Permohonan Cuti berhasil dibatalkan.");
					
					//===== Log Start =====
					$arr_data_baru = $this->m_permohonan_cuti->select_cuti_by_id($id);
					$log_data_baru = "";					
					foreach($arr_data_baru as $key => $value){
						if(strcmp($key,"id")!=0){
							if(!empty($log_data_baru)){
								$log_data_baru .= "<br>";
							}
							$log_data_baru .= "$key = $value";
						}
					}									
					$log = array(
						"id_pengguna" => $this->session->userdata("id_pengguna"),
						"id_modul" => $this->data['id_modul'],
						"deskripsi" => "batal ".strtolower(preg_replace("/_/"," ",__CLASS__)),
						"kondisi_lama" => $log_data_lama,
						"kondisi_baru" => $log_data_baru,
						"alamat_ip" => $this->data["ip_address"],
						"waktu" => date("Y-m-d H:i:s")
					);
					$this->m_log->tambah($log);
					//===== Log end =====
					
				}else
				{
					$this->session->set_flashdata('warning',"Permohonan pembatalan Cuti gagal.");
				}	
				
				redirect(base_url($this->folder_controller.'permohonan_cuti'));				
				
			}else
			{
				$this->session->set_flashdata('warning',"Terjadi Kesalahan");
				
				redirect(base_url($this->folder_controller.'permohonan_cuti'));	
			}	
		}
        
        function ajax_check_validate_date(){
            $response = [];
            try {
                $start_date = date('Y-m-d', strtotime($this->input->post('start_date',true)));
                $end_date = date('Y-m-d', strtotime($this->input->post('end_date',true)));
                $np_karyawan = $this->input->post('np_karyawan',true);
                
                if(validateDate($start_date)==true && validateDate($end_date)==true){
                    $cek = $this->db->where("(np_karyawan='$np_karyawan' AND (('$start_date' BETWEEN start_date AND end_date) OR ('$end_date' BETWEEN start_date AND end_date)) AND (status_1='0' OR status_1='1' OR status_2='1' OR approval_sdm='1'))")->get('ess_cuti');
                    if($cek->num_rows() > 0){
                        $response['status'] = false;
                        $response['message'] = 'Rentang tanggal tersebut sudah mengajukan cuti';
                    } else{
                        $response['status'] = true;
                        $response['message'] = 'OK';
                    }
                } else{
                    //$response['message'] = "Start: $start_date, End: $end_date";
                    $response['status'] = false;
                    $response['message'] = 'Tanggal tidak valid';
                }
                
                echo json_encode($response);
            } catch(Exception $e) {
                $response['status'] = false;
                $response['message'] = 'Error Exception '.$e->getMessage();
                echo json_encode($response);
            }
        }
		

	}
	
	/* End of file perencanaan_jadwal_kerja.php */
	/* Location: ./application/controllers/kehadiran/perencanaan_jadwal_kerja.php */