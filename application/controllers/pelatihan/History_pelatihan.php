<?php
defined('BASEPATH') OR exit('No direct script access allowed');

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	class History_pelatihan extends CI_Controller {
		public function __construct(){
			parent::__construct();
			
			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'pelatihan/';
			$this->folder_model = 'pelatihan/';
			$this->folder_controller = 'pelatihan/';
			
			$this->akses = array();
			
			$this->load->helper("cutoff_helper");
			$this->load->helper("tanggal_helper");
			$this->load->helper("karyawan_helper");
			$this->load->helper("reference_helper");
			
			$this->load->model($this->folder_model."m_pelatihan");
			$this->load->model('M_approval');
			
			$this->data["is_with_sidebar"] = true;
			
			$this->data['judul'] = "Input Pelatihan";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);				
			izin($this->akses["akses"]);
		}
		
		public function index()
		{				
			//$this->output->enable_profiler(TRUE);
			//echo __FILE__ . __LINE__;die(var_dump($this->akses));			
            $array_daftar_karyawan	= $this->m_pelatihan->select_daftar_karyawan();
            
			$this->data["akses"] 					= $this->akses;
			$this->data["navigasi_menu"] 			= menu_helper();
			$this->data['content'] 					= $this->folder_view."history_pelatihan";
			$this->data['select_mst_cuti']= $this->m_pelatihan->select_mst_cuti();
            $this->data['array_daftar_karyawan'] 	= $array_daftar_karyawan;				
			
			if($_SESSION["grup"]==4) //jika Pengadministrasi Unit Kerja
			{
				$list_kode_unit=array();
				$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
				foreach ($list_pengadministrasi as $data) //looping list_pengadministrasi
				{	
					array_push($list_kode_unit,$data['kode_unit']);								
				}	
				
				$list_kode_unit = implode("','",$list_kode_unit);
			}else
			if($_SESSION["grup"]==5) //jika Pengguna
			{
				$list_kode_unit=array();
				array_push($list_kode_unit,$_SESSION["kode_unit"]);	
				
				$list_kode_unit = implode("','",$list_kode_unit);
			}else
			{
				$list_kode_unit=false;
			}
				
			$array_tahun_bulan = array();
			if($list_kode_unit==false)
			{				
				$query = $this->db->select("DATE_FORMAT(tgl_berangkat,'%Y-%m') as tahun_bulan")->group_by('tahun_bulan')->order_by('tahun_bulan','DESC')->get('ess_sppd');
			}else
			{
				$query = $this->db->select("DATE_FORMAT(tgl_berangkat,'%Y-%m') as tahun_bulan")->where("kode_unit IN ('".$list_kode_unit."')")->group_by('tahun_bulan')->order_by('tahun_bulan','DESC')->get('ess_sppd');
			}				
				
			
			foreach ($query->result_array() as $data) 
			{					
				$bulan = substr($data['tahun_bulan'],-2);
				$tahun = substr($data['tahun_bulan'],0,4);
				
				$bulan_tahun = $bulan."-".$tahun;				
				
				$array_tahun_bulan[] = $bulan_tahun; 
			}
            
            $this->data['array_tahun_bulan'] 	= $array_tahun_bulan;
			
			$this->load->view('template',$this->data);
		}	
		
		public function tabel_ess_history_pelatihan($bulan_tahun=null)
		{	
			if(@$bulan_tahun!=0){
                $month = $bulan_tahun;
            } else{
                $month = 0;
            }
			
			$this->load->model($this->folder_model."M_tabel_history");
			
			if($_SESSION["grup"]==4) //jika Pengadministrasi Unit Kerja
			{
				$ada_data=0;
				$var=array();
				$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
				foreach ($list_pengadministrasi as $data) //looping list_pengadministrasi
				{	
					array_push($var,$data['kode_unit']);
					$ada_data=1;
				}
				if($ada_data==0)
				{
					$var='';
				}		
			}else
			if($_SESSION["grup"]==5) //jika Pengguna
			{
				$var 	= $_SESSION["no_pokok"];							
			}else
			{
				$var = 1;				
			}
			
			$list 	= $this->M_tabel_history->get_datatables($var,$month);	
			
			$data = array();
			$no = 0;
			
			foreach ($list as $tampil) {
				$no++;
				$row = array();
				$row[] = $no;
				$row[] = $tampil->np_karyawan;
				$row[] = $tampil->nama;

				$id							= $tampil->id;
				$np_karyawan				= $tampil->np_karyawan;
				$nama						= $tampil->nama;
				$nama_jabatan				= $tampil->nama_jabatan;
				$perihal					= $tampil->perihal;
				$tgl_berangkat				= $tampil->tgl_berangkat;	

				$row[]				= $nama_jabatan;
				$row[]				= $perihal;
				$row[]				= $tgl_berangkat;

				$data[] = $row;
			}

			$records_total = count($data);
			$data = array_slice($data, $_POST['start'], $_POST['length']);

			$output = array(
							"draw" => $_POST['draw'],
							"recordsTotal" => $records_total,
							"recordsFiltered" => $this->M_tabel_history->count_filtered($var,$month),
							"data" => $data,
					);
			//output to json format
			echo json_encode($output);
		}
		
		public function ajax_checkJumlahCuti()
		{			
			$data	= $this->input->post('data_array');	
			
			$pisah = explode(",",$data);
			$absence_type 	=$pisah[0];
			$np_karyawan 	=$pisah[1];
			$jumlah_hari 	=$pisah[2];				
			$jumlah_bulan 	=$pisah[3];		
			$type_cuber 	=$pisah[6];
			if($pisah[4])
			{
				$start_date 	=explode('-', $pisah[4]);				
			}else
			{
				$start_date 	=explode('-', "00-00-0000");		
			}
			
			if($pisah[5])
			{
				$end_date 		=explode('-', $pisah[5]);				
			}else
			{
				$end_date 	=explode('-', "00-00-0000");		
			}
			
			$d1 =$start_date[2].'-'.$start_date[1].'-'.$start_date[0];
			$d2 =$end_date[2].'-'.$end_date[1].'-'.$end_date[0];
				
			
			//check validasi hari cuti
			if($absence_type=='2001|2060' || $absence_type=='2001|2066' || $absence_type=='2001|2061' || $absence_type=='2001|2062' || $absence_type=='2001|2063' || $absence_type=='2001|2068' || $absence_type=='2001|2067' || $absence_type=='2001|2064' || $absence_type=='2001|2065')
			{
				if($absence_type=='2001|2060')
					$hari_cuti = 3;
				else if($absence_type=='2001|2066')
					$hari_cuti = 1;
				else if($absence_type=='2001|2067')
					$hari_cuti = 3;
				else if($absence_type=='2001|2068')
					$hari_cuti = 3;
				else 
					$hari_cuti = 2;
				
				if($jumlah_hari > $hari_cuti) {
					$tipe = $this->m_pelatihan->get_absence($absence_type);
					$tampil = "Jumlah Hari ".$tipe." Maksimal : ".$hari_cuti." Hari.";
				}
				else {
					$tampil = '';
				}
			}
			else if($absence_type=='2001|2000' || $absence_type=='2001|2010')
			{
				$hari_cuti = date('Y-m-d', strtotime($d1 . ' +3 months -1 days'));
				$akhir_cuti = date('Y-m-d', strtotime($d2));

				if($akhir_cuti > $hari_cuti) {
					$tipe = $this->m_pelatihan->get_absence($absence_type);
					$tampil = "Batas Maksimum Untuk Cuti Bersalin/Gugur Kandungan Pada Tanggal : ".$hari_cuti;
				}
				else {
					$tampil = '';
				}
			}
			else if($absence_type=='2001|1000') //cuti tahunan
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
						$sisa_cuti_tahunan_expired = sisa_cuti_tahunan_expired($np_karyawan);
						$tampil = "Sisa Cuti Tahunan Karyawan $np_karyawan tidak mencukupi ";
						$tampil .= "\nUsulan = $jumlah_hari ";
						$tampil .= "\nSisa Cuti Tahunan = $sisa_cuti ";
						if($sisa_cuti_tahunan_expired && $sisa_cuti_tahunan_expired['sisa'] > 0) $tampil .= "\nSisa Cuti Tahunan Sudah Kedaluarsa = {$sisa_cuti_tahunan_expired['sisa']}";
						$tampil .= "\nCuti menunggu persetujuan SDM = $cuti_tahunan_menunggu_sdm  \nCuti menunggu cutoff = $cuti_tahunan_menunggu_cutoff";
					}else
					{
						$tampil = '';
					}
			}
			else if($absence_type=='2001|1020') //cuti bersama
			{
				if ($type_cuber == '2001|1000') { //ambil dari cuti tahunan
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
						$sisa_cuti_tahunan_expired = sisa_cuti_tahunan_expired($np_karyawan);
						$tampil = "Sisa Cuti Tahunan Karyawan $np_karyawan tidak mencukupi ";
						$tampil .= "\nPermohonan = $jumlah_hari ";
						$tampil .= "\nSisa Cuti Tahunan = $sisa_cuti ";
						if($sisa_cuti_tahunan_expired && $sisa_cuti_tahunan_expired['sisa'] > 0) $tampil .= "\nSisa Cuti Tahunan Sudah Kedaluarsa = {$sisa_cuti_tahunan_expired['sisa']}";
						$tampil .= "\nCuti menunggu persetujuan SDM = $cuti_tahunan_menunggu_sdm  \nCuti menunggu cutoff = $cuti_tahunan_menunggu_cutoff";
					}else
					{
						$tampil = '';
					}
				} else $tampil = '';
			}
			else if($absence_type=='2001|1010') //cuti cubes
			{
				//pindah khusus
				$tampil='';
				
				
							
			}else
			{
				$tampil = '';
			}
			
			
		
	
			echo $tampil;				
		}
		
		
		public function ajax_checkValidateHutangCuti()
		{
			$data	= $this->input->post('data_array', true);
			
			// $pisah = explode(",", $data);
			// $np_karyawan 	=$pisah[0];
			$np_karyawan 	=$data;

			$data_mst_karyawan = $this->db->select('no_pokok, nama, tanggal_masuk, kontrak_kerja')->where('no_pokok', $np_karyawan)->get('mst_karyawan')->row();
			$givenDate = new DateTime($data_mst_karyawan->tanggal_masuk);
			$now = new DateTime();
			$thirtyDaysAgo = $now->sub(new DateInterval('P30D'));
			if($givenDate > $thirtyDaysAgo || $data_mst_karyawan->kontrak_kerja=='PKWT'){
				$data_mst_karyawan->is_pkwt = true;
			} else{
				$data_mst_karyawan->is_pkwt = false;
			}
			
			// cek sisa cuti tahunan tahun sebelumnya
			$cuti_tahunan_menunggu_sdm  = cuti_tahunan_menunggu_sdm($np_karyawan);
			$sisa_cuti			= sisa_cuti_tahunan_untuk_hutang($np_karyawan);
			
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

			if (($sisa_cuti - ($cuti_tahunan_menunggu_cutoff + $cuti_tahunan_menunggu_sdm)) > 0) {
				// echo 'gagal sisa cuti tahunan tahun sebelumnya';
				return $this->output
					->set_content_type('application/json')
					->set_status_header(200)
					->set_output(json_encode([
						'status' => false,
						'data' => $data_mst_karyawan,
						'message' => 'gagal sisa cuti tahunan tahun sebelumnya'
					]));
				exit;
			}
			
			// cek sisa cuti besar
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

			if ($sisa_bulan > 0 && $sisa_hari > 0) {
				// echo 'gagal sisa cubes';
				return $this->output
					->set_content_type('application/json')
					->set_status_header(200)
					->set_output(json_encode([
						'status' => false,
						'data' => $data_mst_karyawan,
						'message' => 'gagal sisa cubes'
					]));
				exit;
			}
			
			//cek sisa hutang cuti
			$cek_np_hutang_cuti = $this->db->where(['no_pokok' => $np_karyawan, 'deleted_at' => null])->get('cuti_hutang')->row();
			$sisa_hutang_cuti = @$cek_np_hutang_cuti->hutang ?: 0;
			
			if ($sisa_hutang_cuti > 32) {
				// echo 'gagal hutang cuti melebihi';
				return $this->output
					->set_content_type('application/json')
					->set_status_header(200)
					->set_output(json_encode([
						'status' => false,
						'data' => $data_mst_karyawan,
						'message' => 'gagal hutang cuti melebihi'
					]));
				exit;
			}

			// echo 'berhasil';
			return $this->output
				->set_content_type('application/json')
				->set_status_header(200)
				->set_output(json_encode([
					'status' => true,
					'data' => $data_mst_karyawan,
					'message' => 'berhasil'
				]));
		}
		
		public function ajax_checkJumlahCutiBesar()
		{		
			$data	= $this->input->post('data_array');	
			
			$pisah 					= explode(",",$data);
			$np_karyawan 			=$pisah[0];
			
			if($pisah[1])
			{
				$start_date 			=explode('-', $pisah[1]);
			}else
			{
				$start_date = '00-00-0000';
			}
			
			$cuti_besar_start_date 	=$start_date[2].'-'.$start_date[1].'-'.$start_date[0];
			$cuti_besar_pilih 		=$pisah[2];				
			$jumlah_hari 			=$pisah[3];
			$jumlah_bulan 			=$pisah[4];
			$type_cuber 			=$pisah[5];
			
			
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
		
			if($cuti_besar_pilih=='bulan') //jika pilih bulan
			{
				$plus_satu_bulan = date('Y-m-d', strtotime($cuti_besar_start_date . " +$jumlah_bulan month"));
				$end = date('Y-m-d', strtotime($plus_satu_bulan . " -1 days"));
				
				$pisah_end 			=explode('-', $end);
				$cuti_besar_end_date 	=$pisah_end[2].'-'.$pisah_end[1].'-'.$pisah_end[0];
				
				if($jumlah_bulan<=$sisa_bulan) //jika masih cukup
				{
					  $return = $cuti_besar_end_date;		
					
				}else
				{
					 $return = '';		
				}
			}else
			if($cuti_besar_pilih=='hari') //jika pilih hari
			{
				$jumlah_hari_original 	= $jumlah_hari;
				$jumlah_hari 			= $jumlah_hari-1;
				
				if($jumlah_hari_original==1) //jika cuti nya satu hari, maka pakai hari itu 
				{
					$end					= $cuti_besar_start_date;
					$pisah_end 				= explode('-', $end);
					$cuti_besar_end_date 	= $pisah_end[2].'-'.$pisah_end[1].'-'.$pisah_end[0];					
				}else
				{
					$end = date('Y-m-d', strtotime($cuti_besar_start_date . " +$jumlah_hari day"));
					$pisah_end 				= explode('-', $end);
					$cuti_besar_end_date 	= $pisah_end[2].'-'.$pisah_end[1].'-'.$pisah_end[0];
				}
								
				// if($jumlah_hari<=$sisa_hari) //jika masih cukup
				if($jumlah_hari_original<=$sisa_hari) //jika masih cukup
				{
					 $return = $cuti_besar_end_date;						
						
				}else
				{
					 $return = '';			
				}
			} else if ($type_cuber == '2001|1010') {
				if($cuti_besar_pilih=='bulan') //jika pilih bulan
				{
					$plus_satu_bulan = date('Y-m-d', strtotime($cuti_besar_start_date . " +$jumlah_bulan month"));
					$end = date('Y-m-d', strtotime($plus_satu_bulan . " -1 days"));
					
					$pisah_end 			=explode('-', $end);
					$cuti_besar_end_date 	=$pisah_end[2].'-'.$pisah_end[1].'-'.$pisah_end[0];
					
					if($jumlah_bulan<=$sisa_bulan) //jika masih cukup
					{
						  $return = $cuti_besar_end_date;
						
					}else
					{
						 $return = '';		
					}
				}else
				if($cuti_besar_pilih=='hari') //jika pilih hari
				{
					$jumlah_hari_original 	= $jumlah_hari;
					$jumlah_hari 			= $jumlah_hari-1;
					
					if($jumlah_hari_original==1) //jika cuti nya satu hari, maka pakai hari itu 
					{
						$end					= $cuti_besar_start_date;
						$pisah_end 				= explode('-', $end);
						$cuti_besar_end_date 	= $pisah_end[2].'-'.$pisah_end[1].'-'.$pisah_end[0];					
					}else
					{
						$end = date('Y-m-d', strtotime($cuti_besar_start_date . " +$jumlah_hari day"));
						$pisah_end 				= explode('-', $end);
						$cuti_besar_end_date 	= $pisah_end[2].'-'.$pisah_end[1].'-'.$pisah_end[0];
					}
									
					// if($jumlah_hari<=$sisa_hari) //jika masih cukup
					if($jumlah_hari_original<=$sisa_hari) //jika masih cukup
					{
						 $return = $cuti_besar_end_date;						
							
					}else
					{
						 $return = '';			
					}
				} else {
					$return = 'kosong';
				}
			}
			else
			{
				 $return = '';
			}				
				
			
			if($jumlah_hari_original=='0' && $jumlah_bulan=='0') //jika jumlah hari nya 0, maka tanggal akhir nya tidak terisi
			{
				echo 'kosong';	
			}
			else
			{
				echo $return;
			}       		

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
				$np_list=$this->m_pelatihan->select_np_by_kode_unit($list_kode_unit);						
								
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
					//06 01 2021, 7648 - Tri Wibowo, Agar bisa backdate input cuti ketika NP nya berubah
					else
					{
						if(kode_unit_by_np_sebelumnya($np_karyawan)==$data['kode_unit']) //check apakah ada disalah satu unit 
						{
							if($sudah_ketemu==0)
							{
								$np_karyawan=$np_karyawan;
								$sudah_ketemu=1;
							}
							
						}
					}
					//end of 06 01 2021
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

			// get data karyawan
			$data_mst_karyawan = $this->db->select('no_pokok, nama, tanggal_masuk, kontrak_kerja')->where('no_pokok', $np_karyawan)->get('mst_karyawan')->row();
			$allowed_cuti = [];
			
			$nama 			= nama_karyawan_by_np($np_karyawan);	
			
			
			$absence_quota = $this->m_pelatihan->select_absence_quota_by_np($np_karyawan);
	 
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
			
			$cubes = $this->m_pelatihan->select_cubes_by_np($np_karyawan);
	 
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
			
			$hutang = $this->m_pelatihan->select_hutang_by_np($np_karyawan)->result_array();

			$status = false;
			$raw = null;
			$message = '';
			if($nama){
				$status = true;
				$raw = $data_mst_karyawan;
				$data= $nama;

				$givenDate = new DateTime($data_mst_karyawan->tanggal_masuk);
				$now = new DateTime();
				$thirtyDaysAgo = $now->sub(new DateInterval('P30D'));
				if($givenDate > $thirtyDaysAgo || $data_mst_karyawan->kontrak_kerja=='PKWT'){
					$raw->is_pkwt = true;
					$data = $data. "\n==================\n"."Pengajuan cuti bisa dilakukan di bulan berikutnya terhitung tanggal masuk : {$data_mst_karyawan->tanggal_masuk}";
					if($givenDate > $thirtyDaysAgo) $allowed_cuti = ['2001|2040','2001|2065','2001|2064','2001|2068','2001|2030'];
					else if($data_mst_karyawan->kontrak_kerja=='PKWT') $allowed_cuti = ['2001|2040','2001|2065','2001|2064','2001|2068','2001|2030','2001|1000','2001|1020'];
				} else{
					$raw->is_pkwt = false;
				}
			} else{
				//06 01 2021, 7648 - Tri Wibowo, Agar bisa backdate input cuti ketika NP nya berubah
				$nama_data_sebelumnya = nama_karyawan_by_np_bulan_sebelumnya($np_karyawan);

				// get data karyawan
				$data_mst_karyawan = raw_karyawan_by_np_bulan_sebelumnya($np_karyawan);
								
				if($nama_data_sebelumnya)
				{
					$data= $nama_data_sebelumnya;	
					$status = true;
					$raw = $data_mst_karyawan;

					$givenDate = new DateTime($data_mst_karyawan->tanggal_masuk);
					$now = new DateTime();
					$thirtyDaysAgo = $now->sub(new DateInterval('P30D'));
					if($givenDate > $thirtyDaysAgo || $data_mst_karyawan->kontrak_kerja=='PKWT'){
						$raw->is_pkwt = true;
						$data = $data. "\n==================\n"."Pengajuan cuti bisa dilakukan di bulan berikutnya terhitung tanggal masuk : {$data_mst_karyawan->tanggal_masuk}";
						if($givenDate > $thirtyDaysAgo) $allowed_cuti = ['2001|2040','2001|2065','2001|2064','2001|2068','2001|2030'];
						else if($data_mst_karyawan->kontrak_kerja=='PKWT') $allowed_cuti = ['2001|2040','2001|2065','2001|2064','2001|2068','2001|2030','2001|1000','2001|1020'];
					} else{
						$raw->is_pkwt = false;
					}
				}else
				{
					$data = "\n==================\n"."NP tidak ditemukan"."\n==================\n";
					// echo $data; 
					return $this->output
						->set_content_type('application/json')
						->set_status_header(200)
						->set_output(json_encode([
							'status' => $status,
							'data' => $data_mst_karyawan,
							'allowed_cuti' => $allowed_cuti,
							'message' => $data
						]));
					die();
				}
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
			
			// echo $data; 	
			return $this->output
				->set_content_type('application/json')
				->set_status_header(200)
				->set_output(json_encode([
					'status' => $status,
					'data' => $data_mst_karyawan,
					'allowed_cuti' => $allowed_cuti,
					'message' => $data
				]));
		}
					
		public function ajax_getNama_approval(){
			$data	= $this->input->post('data_array');	
			
			$pisah = explode(",",$data);
			$np_karyawan 	=$pisah[0];
			$is_poh 	=$pisah[1];	
						
			$data = mst_karyawan_by_np($np_karyawan);		

			if ($data) {
				if(strcmp($is_poh,"false")==0){
					$return = [
						'status'=>true,
						'data'=>[
							'nama'=>$data['nama'],
							'jabatan'=>$data['nama_jabatan']
						]
					];
				}
				else if(strcmp($is_poh,"true")==0){
					$return = [
						'status'=>true,
						'data'=>[
							'nama'=>$data['nama'],
							'jabatan'=>$data['nama_jabatan_poh']
						]
					];
				}
			}
			else{				
				$return = [
                    'status'=>false,
                    'data'=>[
                        'nama'=>'',
                        'jabatan'=>''
                    ]
                ];
			}	
            echo json_encode($return);
		}
		
		public function ajax_getCategory(){
			$this->load->model("pelatihan/m_pelatihan");
			$data	= $this->m_pelatihan->get_category();	
			
			if ($data) {
				$return = [
					'status'=>true,
					'data'=> $data
				];
			}
			else{				
				$return = [
                    'status'=>false,
                    'data'=> $data
                ];
			}	
            echo json_encode($return);
		}

		public function ajax_getPelatihan(){
			$val_kategori	= $this->input->post('val_kategori');	

			$this->load->model("pelatihan/m_pelatihan");
			$data	= $this->m_pelatihan->get_pelatihan($val_kategori);	

			if ($data) {
				$return = [
					'status'=>true,
					'data'=> $data
				];
			}
			else{				
				$return = [
                    'status'=>false,
                    'data'=> $data
                ];
			}	
            echo json_encode($return);
		}

		public function ajax_getSkalaPrioritas(){	
			$this->load->model("pelatihan/m_pelatihan");
			$data	= $this->m_pelatihan->get_skala_prioritas($_SESSION["no_pokok"]);	

			if ($data) {
				$return = [
					'status'=>true,
					'data'=> $data
				];
			}
			else{				
				$return = [
                    'status'=>false,
                    'data'=> $data
                ];
			}	
            echo json_encode($return);
		}
		
		public function ajax_getAtasanPelatihan(){
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
			
			
			$atasan_1 = $this->m_karyawan->get_atasan($kode_unit_atasan_1);
			$atasan_2 = $this->m_karyawan->get_atasan($kode_unit_atasan_2);
			
			$return = [
				'np_atasan_1'=>$atasan_1["np"],
				'is_poh_atasan_1'=>$atasan_1["is_poh"],
				'np_atasan_2'=>$atasan_2["np"],
				'is_poh_atasan_2'=>$atasan_2["is_poh"]
			];
            echo json_encode($return);
		}
		
		public function action_insert_pelatihan()
		{
			$submit = $this->input->post('submit');
			
			if($submit)
			{
				$np_karyawan = html_escape($this->input->post('np_karyawan', true));
				$id_kategori_pelatihan = html_escape($this->input->post('kategori', true));
				$id_pelatihan = html_escape($this->input->post('nama_pelatihan', true));
				// $tgl_pelatihan = html_escape($this->input->post('tgl_pelatihan', true));
				$vendor = html_escape($this->input->post('vendor', true));
				$skala_prioritas = html_escape($this->input->post('skala_prioritas', true));
				$approval_1 = html_escape($this->input->post('approval_1'));
				$approval_1_jabatan = html_escape($this->input->post('approval_1_input_jabatan'));
				$approval_2 = html_escape($this->input->post('approval_2'));
				$approval_2_jabatan = html_escape($this->input->post('approval_2_input_jabatan'));
				
				// get nama pelatihan dan nama karyawan
				$pelatihan = $this->m_pelatihan->mst_pelatihan_by_id($id_pelatihan);
				$last_kode_pelatihan_lainnya = $this->m_pelatihan->get_last_kode_pelatihan_kategori_lainnya($np_karyawan);
				$number = (int)substr($last_kode_pelatihan_lainnya, -3) + 1;
				$new_kode = str_pad($number, 3, "0", STR_PAD_LEFT);
				$kategori = $this->m_pelatihan->mst_kategori_pelatihan_by_id($id_kategori_pelatihan);
				$karyawan = mst_karyawan_by_np($np_karyawan);

				if ($kategori['nama_kategori_pelatihan'] == 'Lainnya'){				
					$data_insert['kode_pelatihan']				= $kategori['kode_kategori_pelatihan'] . $new_kode;						
					$data_insert['pelatihan']					= $id_pelatihan;	
				} else {
					$data_insert['id_pelatihan']				= $id_pelatihan;					
					$data_insert['kode_pelatihan']				= $pelatihan['kode_pelatihan'];						
					$data_insert['pelatihan']					= $pelatihan['nama_pelatihan'];	
				}

				//insert
				$data_insert['np_karyawan']					= $np_karyawan;
				$data_insert['nama']						= $karyawan['nama'];
				$data_insert['nama_jabatan']				= $karyawan['nama_jabatan'];
				$data_insert['kode_unit']					= $karyawan['kode_unit'];
				$data_insert['nama_unit']					= $karyawan['nama_unit'];
				$data_insert['id_kategori_pelatihan']		= $id_kategori_pelatihan;
				$data_insert['kode_kategori_pelatihan']		= $kategori['kode_kategori_pelatihan'];
				$data_insert['nama_kategori_pelatihan']		= $kategori['nama_kategori_pelatihan'];
				// $data_insert['tanggal_pelatihan']			= $tgl_pelatihan;								
				$data_insert['skala_prioritas']				= $skala_prioritas;
				$data_insert['vendor']						= $vendor;
				$data_insert['approval_1']					= $approval_1;
				$data_insert['approval_1_jabatan']			= $approval_1_jabatan;
				$data_insert['status_1']					= '0';
				$data_insert['approval_2']	 				= $approval_2;
				$data_insert['approval_2_jabatan']			= $approval_2_jabatan;
				$data_insert['status_2']					= '0';

				$insert_cuti = $this->m_pelatihan->insert_pelatihan($data_insert);

				redirect(base_url($this->folder_controller.'pelatihan'));	
			}		
		}

		public function action_update_data_pelatihan()
		{
			$submit = $this->input->post('submit');
			
			if($submit)
			{
				$id = html_escape($this->input->post('edit_id', true));
				$skala_prioritas = html_escape($this->input->post('edit_skala_prioritas', true));
				
				//update
				$data_update['id']					= $id;
				$data_update['skala_prioritas']		= $skala_prioritas;
				
				$update = $this->m_pelatihan->update_pelatihan($data_update);
				
				if ($update=='0'){
					$this->session->set_flashdata('warning',"Update Gagal");
				} else {
					$this->session->set_flashdata('success',"Update Berhasil");
				}	

				redirect(base_url($this->folder_controller.'pelatihan'));
			}		
		}
		
		public function action_batal_pelatihan()
		{			
			$submit = $this->input->post('submit');
					
			if($submit)
			{					
				$id 		= $this->input->post('batal_id');
					
				$batal_pelatihan = $this->m_pelatihan->batal_pelatihan($id);
							
			
				if($batal_pelatihan!=0)
				{
					$this->session->set_flashdata('success',"Usulan Pelatihan berhasil dibatalkan.");
				}else
				{
					$this->session->set_flashdata('warning',"Permohonan pembatalan Usulan Pelatihan gagal.");
				}	
				
				redirect(base_url($this->folder_controller.'pelatihan'));				
				
			}else
			{
				$this->session->set_flashdata('warning',"Terjadi Kesalahan");
				
				redirect(base_url($this->folder_controller.'pelatihan'));	
			}	
		}
        
        function ajax_check_validate_date(){
            $response = [];
            try {
                $start_date = date('Y-m-d', strtotime($this->input->post('start_date',true)));
                $end_date = date('Y-m-d', strtotime($this->input->post('end_date',true)));
                $np_karyawan = $this->input->post('np_karyawan',true);
                
                if(validateDate($start_date)==true && validateDate($end_date)==true){
                    $cek = $this->db->where("(np_karyawan='$np_karyawan' AND (('$start_date' BETWEEN start_date AND end_date) OR ('$end_date' BETWEEN start_date AND end_date)) AND (status_1!='2' AND status_2!='2' AND approval_sdm!='2') AND (status_1!='3' AND status_2!='3'))")->get('ess_cuti');
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

		public function ajax_getPilihanAtasanPelatihan($jenis='approval_1'){
			//2-12-2020 - 7648 Tri Wibowo menambah periode sehingga atasanya pas saat periode tersebut
			$periode = null;

			$np_karyawan = $this->input->post('vnp_karyawan');
			//10-07-2021 - Wina mengganti function atasan menjadi minimal kasek, kadep, kadiv sesuai jumlah jam lembur
			$no_pokok	 = $this->input->post('vno_pokok');
			$tgl_mulai	 = date('Y-m-d', strtotime($this->input->post('tgl_mulai')));
			$tgl_selesai = date('Y-m-d', strtotime($this->input->post('tgl_selesai')));
			
			//2-12-2020 - 7648 Tri Wibowo menambah periode sehingga atasanya pas saat periode tersebut
			$pisah = explode('#',$np_karyawan);
			$np_karyawan = $pisah[0];
			$periode     = date('Y-m-d', strtotime($pisah[1]));
			
			$pisah_periode = explode('-',$periode);
			$d = $pisah_periode[2];
			$m = $pisah_periode[1];
			$y = $pisah_periode[0];
			
			$periode_tanggal 	= $periode;
			$periode 			= $y.'_'.$m;
			
			//jika tidak ada tanggal terpilih maka pake tanggal sekarang
			if(!$periode_tanggal)
			{
				$periode_tanggal=date('Y-m-d');
			}
			
			//$np_karyawan = $vnp_karyawan;
			$this->load->model("master_data/m_karyawan");
			//2-12-2020 - 7648 Tri Wibowo menambah periode sehingga atasanya pas saat periode tersebut
			//$karyawan = $this->m_karyawan->get_posisi_karyawan_periode($np_karyawan,$periode);
			//20-01-2020 - 7648 Tri Wibowo menambah periode tanggal per tanggal dws karyawan tersebut
			$karyawan = $this->m_karyawan->get_posisi_karyawan_periode_tanggal($np_karyawan,$periode,$periode_tanggal);
				
			// echo $this->db->last_query();exit;
			if(empty($karyawan)){
				//2-12-2020 - 7648 Tri Wibowo menambah periode sehingga atasanya pas saat periode tersebut
				if($periode=='')
				{
					$periode = date("Y_m");
				}
				$karyawan = $this->m_karyawan->get_posisi_karyawan_periode($np_karyawan,$periode);
				
				if(empty($karyawan)){
					//2-12-2020 - 7648 Tri Wibowo menambah periode sehingga atasanya pas saat periode tersebut
					if($periode=='')
					{
						$periode = date('Y_m', strtotime(date("Y-m-d") . " -1 months"));
					}
					$karyawan = $this->m_karyawan->get_posisi_karyawan_periode($np_karyawan,$periode);
				}
			}
			$kode = $karyawan["kode_unit"];

			if(strcmp(substr($karyawan["kode_unit"],1,1),"0")==0){
				$karyawan["kode_unit"] = substr($karyawan["kode_unit"],0,3);
			}
			else{
				$karyawan["kode_unit"] = substr($karyawan["kode_unit"],0,2);
			}

			
			if ($jenis=='approval_1') {
				$send_data['kode_unit'] = substr($kode, 0, 4);
				$send_data['atasan'] = $this->M_approval->list_atasan_minimal_kasek(array($karyawan["kode_unit"]),$np_karyawan);
			} else if ($jenis=='approval_2') {
				$send_data['kode_unit'] = substr($kode, 0, 3);
				$send_data['atasan'] = $this->M_approval->list_atasan_minimal_kadep(array($karyawan["kode_unit"]),$np_karyawan);
				/*$send_data['kode_unit'] = substr($kode, 0, 2);
				$send_data['atasan'] = $this->M_approval->list_atasan_minimal_kadiv(array($karyawan["kode_unit"]),$np_karyawan);*/
			} else {
				$send_data['kode_unit'] = substr($kode, 0, 2);
				$send_data['atasan'] = $this->M_approval->list_atasan_minimal_kadiv(array($karyawan["kode_unit"]),$np_karyawan);
			}

			echo json_encode($send_data);
		}

		function get_atasan_cuti_new(){
			$np = $this->input->post('np',true);
			$tanggal_mulai = date('Y-m-d', strtotime($this->input->post('tanggal_mulai',true)));

			$data_karyawan = $this->M_approval->get_jabatan_karyawan($np, $tanggal_mulai);
			$data_atasan1 = [];
			$data_atasan2 = [];

			if(in_array(substr($data_karyawan->kode_jabatan, -3), ['500','600']) || $data_karyawan->grup_jabatan=='KASEK'){
				$data_atasan1 = $this->M_approval->list_atasan_minimal_kadep(array($data_karyawan->kode_unit),$data_karyawan->no_pokok);
				$data_atasan2 = $this->M_approval->list_atasan_minimal_kadiv(array($data_karyawan->kode_unit),$data_karyawan->no_pokok);
			} else if(substr($data_karyawan->kode_jabatan, -3)=='400' || $data_karyawan->grup_jabatan=='KADEP'){
				$data_atasan1 = $this->M_approval->list_atasan_minimal_kadiv(array($data_karyawan->kode_unit),$data_karyawan->no_pokok);
				$data_atasan2 = $this->M_approval->list_atasan_minimal_dir(array($data_karyawan->kode_unit),$data_karyawan->no_pokok);
			} else if(in_array(substr($data_karyawan->kode_jabatan, -3), ['100','200','300']) || in_array($data_karyawan->grup_jabatan, ['KADIV'])){
				$data_atasan1 = $this->M_approval->list_atasan_minimal_dir(array($data_karyawan->kode_unit),$data_karyawan->no_pokok);
				$data_atasan2 = $data_atasan1;
			} else{
				$data_atasan1 = $this->M_approval->list_atasan_minimal_kasek(array($data_karyawan->kode_unit),$data_karyawan->no_pokok);
				$data_atasan2 = $this->M_approval->list_atasan_minimal_kadep(array($data_karyawan->kode_unit),$data_karyawan->no_pokok);
			}

			echo json_encode([
				'status'=>true,
				'data_karyawan'=>$data_karyawan,
				'data_atasan1'=>$data_atasan1,
				'data_atasan2'=>$data_atasan2
			]);
		}

		function ajax_checkSisaHutangCuti() {
			$data	= $this->input->post('data_array');	
			
			$pisah = explode(",",$data);
			$np_karyawan 	=$pisah[0];
			
			$karyawan = $this->db->select('SUM(hutang) AS total_hutang')->where(['no_pokok' => $np_karyawan])->get('cuti_hutang')->row();

			echo $karyawan->total_hutang;
		}

		public function create_template()
		{
			//require_once __DIR__ . '/../../../vendor/autoload.php';

			$spreadsheet = new Spreadsheet();
			
			$newSheet = $spreadsheet->createSheet();
			$sheet->setTitle('Template import contribution');
			
			$headers = [
				["NP Karyawan", ""],
				["Perihal", ""],
				["Nama Jenis Dokumen", "(* Ambil dari sheet referensi jenis dokumen"],
				["Tanggal Dokumen", "(* Gunakan format YYYY-MM-DD"],
				["Url Dokumen", ""]
			];

			$sheet->getStyle('D:D')
				->getNumberFormat()
				->setFormatCode(NumberFormat::FORMAT_DATE_YYYYMMDD);

			$headers_new_sheet = ['No', 'Nama Jenis Dokumen'];

			for ($i = 1; $i <= count($headers); $i++) {
				$columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
				$sheet->getColumnDimension($columnLetter)->setWidth(40);

				$sheet->getStyle($columnLetter . '1')->getFont()->setBold(true);
				$sheet->getStyle($columnLetter . '1')->getAlignment()->setWrapText(true);


				$richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();

				$headerText = $richText->createTextRun($headers[$i - 1][0]);
				$headerText->getFont()->setBold(true);

				if (!empty($headers[$i - 1][1])) {
					$richText->createText("\n");
					$noteText = $richText->createTextRun($headers[$i - 1][1]);
					$noteText->getFont()->setBold(false);
				}

				$sheet->getStyle($columnLetter . '1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
				$sheet->getStyle($columnLetter . '1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
				$sheet->getCell($columnLetter . '1')->setValue($richText);
			}

			$newSheet->fromArray($headers_new_sheet, null, 'A1');
			$newSheet->getColumnDimension('A')->setAutoSize(true);
			$newSheet->getColumnDimension('B')->setAutoSize(true);
			$newSheet->fromArray($ref_jenis_dokumen, NULL, 'A2');
			$newSheet->getStyle('A1')->getFont()->setBold(true);
			$newSheet->getStyle('B1')->getFont()->setBold(true);
			$spreadsheet->setActiveSheetIndex(0);

			// Menulis file dengan writer Xlsx
			$writer = new Xlsx($spreadsheet);

			// Mengirimkan file ke browser
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="template.xlsx"');
			header('Cache-Control: max-age=0');

			$writer->save('php://output');
		}
	}
	
	/* End of file perencanaan_jadwal_kerja.php */
	/* Location: ./application/controllers/kehadiran/perencanaan_jadwal_kerja.php */
