<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Permohonan_cuti extends CI_Controller {
		public function __construct(){
			parent::__construct();
			
			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'cuti/';
			$this->folder_model = 'cuti/';
			$this->folder_controller = 'cuti/';
			
			$this->akses = array();
			
			$this->load->helper("cutoff_helper");
			$this->load->helper("tanggal_helper");
			$this->load->helper("karyawan_helper");
			$this->load->helper("reference_helper");
			
			$this->load->model($this->folder_model."m_permohonan_cuti");
			$this->load->model('M_approval');
			
			$this->data["is_with_sidebar"] = true;
			
			$this->data['judul'] = "Permohonan Cuti";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);				
			izin($this->akses["akses"]);
		}
		
		public function index()
		{				
			//$this->output->enable_profiler(TRUE);
			//echo __FILE__ . __LINE__;die(var_dump($this->akses));			
            $array_daftar_karyawan	= $this->m_permohonan_cuti->select_daftar_karyawan();
            
			$this->data["akses"] 					= $this->akses;
			$this->data["navigasi_menu"] 			= menu_helper();
			$this->data['content'] 					= $this->folder_view."permohonan_cuti";
			$this->data['select_mst_cuti']= $this->m_permohonan_cuti->select_mst_cuti();
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
				$query = $this->db->select("DATE_FORMAT(start_date,'%Y-%m') as tahun_bulan")->group_by('tahun_bulan')->order_by('tahun_bulan','DESC')->get('ess_cuti');
			}else
			{
				$query = $this->db->select("DATE_FORMAT(start_date,'%Y-%m') as tahun_bulan")->where("kode_unit IN ('".$list_kode_unit."')")->group_by('tahun_bulan')->order_by('tahun_bulan','DESC')->get('ess_cuti');
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
		
		public function tabel_ess_cuti($bulan_tahun=null)
		{	
			if(@$bulan_tahun!=0){
                $month = $bulan_tahun;
            } else{
                $month = 0;
            }
			
			$this->load->model($this->folder_model."M_tabel_permohonan");
			
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
			
			$list 	= $this->M_tabel_permohonan->get_datatables($var,$month);	
			
			
			$data = array();
			$no = $_POST['start'];
			
	
			foreach ($list as $tampil) {
				$no++;
				$row = array();
				$row[] = $no;
				$row[] = $tampil->np_karyawan;
				$row[] = $tampil->nama;

				if($tampil->is_cuti_bersama=='1') {
					if(in_array($tampil->absence_type, ['2001|1000','2001|1010'])) $row[] = "Cuti Bersama (Memotong {$tampil->uraian})";
					else if($tampil->absence_type == '2001|2080') $row[] = "Cuti Bersama (Mengambil {$tampil->uraian})";
					else $row[] = $tampil->uraian;
				} else $row[] = $tampil->uraian;

				$row[] = tanggal_indonesia($tampil->start_date);	
				$row[] = tanggal_indonesia($tampil->end_date);
				
				if($tampil->jumlah_bulan)
				{
					$row[] = $tampil->jumlah_bulan." bulan ".$tampil->jumlah_hari." hari";		
				}else
				{
					$row[] = $tampil->jumlah_hari." hari ";
				}
				
				$row[] = $tampil->alasan;
				
				if($tampil->keterangan=='1')
				{
					$row[] ='Dalam Kota';
				}else
				if($tampil->keterangan=='2')
				{
					$row[] ='Luar Kota';
				}else
				{
					$row[] ='';
				}

				$id				= $tampil->id;
				$np_karyawan	= $tampil->np_karyawan;
				$nama			= $tampil->nama;
				$approval_1		= $tampil->approval_1;
				$approval_2		= $tampil->approval_2;	
				$status_1		= $tampil->status_1;
				$status_2		= $tampil->status_2;	
				$approval_1_date= $tampil->approval_1_date;
				$approval_2_date= $tampil->approval_2_date;	
				$approval_sdm	= $tampil->approval_sdm;
				$created_at		= $tampil->created_at;
				$created_by		= nama_karyawan_by_np($tampil->created_by);
				
				if($status_1=='1')
				{						
					$approval_1_nama 	= $approval_1." | ".nama_karyawan_by_np($approval_1);
					$approval_1_status 	= "Cuti Telah Disetujui pada $approval_1_date."; 
				}else
				if($status_1=='2')
				{					
					$approval_1_nama 	= $approval_1." | ".nama_karyawan_by_np($approval_1);
					$approval_1_status 	= "Cuti TIDAK disetujui pada $approval_1_date."; 
				}else
				if($status_1=='3')
				{					
					$approval_1_nama 	= $approval_1." | ".nama_karyawan_by_np($approval_1);
					$approval_1_status 	= "Permohonan Cuti Dibatalkan oleh pemohon pada $approval_1_date."; 
				}else
				if($status_1==''||$status_1=='0')
				{				
					$status_1 = '0';
					$approval_1_nama 	= $approval_1." | ".nama_karyawan_by_np($approval_1);
					$approval_1_status 	= "Cuti BELUM disetujui."; 
				}
				
				if($status_2=='1')
				{						
					$approval_2_nama 	= $approval_2." | ".nama_karyawan_by_np($approval_2);
					$approval_2_status 	= "Cuti Telah Disetujui pada $approval_2_date."; 
				}else
				if($status_2=='2')
				{					
					$approval_2_nama 	= $approval_2." | ".nama_karyawan_by_np($approval_2);
					$approval_2_status 	= "Cuti TIDAK disetujui pada $approval_2_date."; 
				}else
				if($status_2=='3')
				{					
					$approval_2_nama 	= $approval_2." | ".nama_karyawan_by_np($approval_2);
					$approval_2_status 	= "Permohonan Cuti Dibatalkan oleh pemohon pada $approval_2_date."; 
				}else
				if($status_2==''||$status_2=='0')
				{
					$status_2 = '0';
					$approval_2_nama 	= $approval_2." | ".nama_karyawan_by_np($approval_2);
					$approval_2_status 	= "Cuti BELUM disetujui."; 
				}
					
					
					
				$btn_warna		='btn-default';
				$btn_text		='menunggu persetujuan';
				$btn_disabled 	='';
				
				if(($status_1=='' || $status_1=='0' || $status_1 == null) && ($status_2!='2' || $status_2!='1')) //menunggu atasan 1
				{
					$btn_warna		='btn-warning';
					$btn_text		='Menunggu Atasan 1';
					$btn_disabled 	='';
				}
				if(($status_1=='1') && ($status_2!='2' || $status_2!='1')) //disetujui atasan 1
				{
					if($tampil->approval_2==null || $tampil->approval_2=='') //jika tidak ada atasan 2
					{
						$btn_warna		='btn-success';
						$btn_text		='Disetujui atasan 1';
						$btn_disabled 	='';
					}else //jika ada atasan 2
					{
						$btn_warna		='btn-warning';
						$btn_text		='Menunggu Atasan 2';
						$btn_disabled 	='';
					}
					
				}
				
				$alasan_tolak = "";
				
				if(($status_1=='2') && ($status_2!='2' || $status_2!='1')) //ditolak atasan 1
				{
					$btn_warna		='btn-danger';
					$btn_text		='Ditolak Atasan 1';
					$btn_disabled 	='disabled';
					$alasan_tolak	= $tampil->approval_alasan_1;
				}
				if($status_2=='1') //disetujui atasan  2
				{
					$btn_warna		='btn-success';
					$btn_text		='Disetujui Atasan 2';
					$btn_disabled 	='disabled';
				}
				if($status_2=='2') //ditolak atasan 2
				{
					$btn_warna		='btn-danger';
					$btn_text		='Ditolak Atasan 2';
					$btn_disabled 	='disabled';
					$alasan_tolak	= $tampil->approval_alasan_2;
				}
					
				if($status_1=='3' || $status_2=='3') //dibatalkan
				{
					$btn_warna		='btn-danger';
					$btn_text		='dibatalkan';
					$btn_disabled 	='disabled';
				}
				
				if($approval_sdm==1)
				{
					$btn_warna		='btn-success';
					$btn_text		='Disetujui Oleh SDM';
					$btn_disabled 	='disabled';
				}else
				if($approval_sdm==2)
				{
					$btn_warna		='btn-danger';
					$btn_text		='Ditolak Oleh SDM';
					$btn_disabled 	='disabled';
					$alasan_tolak	= $tampil->alasan_sdm;
				}
				
				$status_pembatalan_cuti ='';
				$pembatalan_cuti		='';
				$pembatalan_cuti_header	=false;
				$pembatalan_cuti_tampil	='';
				$pem_cut = $this->db->query("SELECT * FROM ess_pembatalan_cuti WHERE np_karyawan='$np_karyawan' AND id_cuti='$id'");
				foreach ($pem_cut->result_array() as $pem) 
				{
					if($pembatalan_cuti_header==false)
					{
						$pembatalan_cuti_tampil="Terdapat Pembatalan Cuti :";
						
						$pembatalan_cuti_header=true;
					}
						
						$pembatalan_cuti_tampil=$pembatalan_cuti_tampil."<br>". $pem['date'];			
				}
				
				if($pembatalan_cuti_header==true)
				{
					$status_pembatalan_cuti="<br><br><button class='btn btn-default btn-xs'>$pembatalan_cuti_tampil</button>";
				}
				
				$row[] = "<button class='btn ".$btn_warna." btn-xs status_button' data-toggle='modal' data-target='#modal_status'
					data-np-karyawan='$np_karyawan'
					data-nama='$nama'
					data-created-at='$created_at'
					data-created-by='$created_by'
					data-approval-1-nama='$approval_1_nama'
					data-approval-2-nama='$approval_2_nama'
					data-approval-1-status='$approval_1_status'
					data-approval-2-status='$approval_2_status'					
				>$btn_text</button>".$status_pembatalan_cuti.$alasan_tolak;				
				
				/*if($approval_sdm==1)
				{
					$row[] = "<button class='btn btn-success btn-xs'	
					><i class='fa fa-check'></i></button>";	
					
					$btn_disabled 	='disabled';
				}else
				if($approval_sdm==2)
				{
					$row[] = "<button class='btn btn-danger btn-xs'	
					><i class='fa fa-times  '></i></button>";

					$btn_disabled 	='disabled';					
				}else
				{
					$row[] = "<button class='btn btn-xs'	
					><i class='fa fa-minus'></i></button>";	
				}*/
				
				
				//cutoff ERP
				$sudah_cutoff = sudah_cutoff($tampil->start_date);
				
				if($sudah_cutoff) //jika sudah lewat masa cutoff
				{
					$row[] = "<button class='btn btn-primary btn-xs batal_button'  data-toggle='tooltip' title='Tidak bisa melakukan aksi setelah Cutoff data di ERP pada $sudah_cutoff' disabled>Submit ERP</button>";
				}else
				{					
					$row[] = "<button class='btn btn-primary btn-xs batal_button' data-toggle='modal'  data-target='#modal_batal'
					data-id='$id'
					data-np-karyawan='$np_karyawan'
					data-nama='$nama'
					data-created-at='$created_at'
					data-created-by='$created_by'
					data-approval-1-nama='$approval_1_nama'
					data-approval-2-nama='$approval_2_nama'
					data-approval-1-status='$approval_1_status'
					data-approval-2-status='$approval_2_status'			
					$btn_disabled>Batal</button>";
					
				}
				
				
				
			
				
				$data[] = $row;
			}

			$output = array(
							"draw" => $_POST['draw'],
							"recordsTotal" => $this->M_tabel_permohonan->count_all($var,$month),
							"recordsFiltered" => $this->M_tabel_permohonan->count_filtered($var,$month),
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
					$tipe = $this->m_permohonan_cuti->get_absence($absence_type);
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
					$tipe = $this->m_permohonan_cuti->get_absence($absence_type);
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
						$tampil .= "\nPermohonan = $jumlah_hari ";
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
		
		public function ajax_getNama_approval() {
			$data = $this->input->post('data_array');
			$pisah = explode(",", $data);
			$np_approver = $pisah[0];
			$is_poh = $pisah[1];
			$approval_number = $pisah[2];
			$np_karyawan = $pisah[3];
			$np_poh_approvel = $pisah[4];
			
			$is_true_poh = $this->checkPOH($np_approver, $np_poh_approvel, $np_karyawan);
			// Ambil data karyawan dan approver
			$data_karyawan = mst_karyawan_by_np($np_karyawan);
			$data_approver = mst_karyawan_by_np($np_approver);
		
			if($is_true_poh){
				$jabatan = ($is_poh == "false") ? $data_approver['nama_jabatan'] : $data_approver['nama_jabatan_poh'];
				$return = [
					'status' => true,
					'data' => [
						'nama' => $data_approver['nama'],
						'jabatan' => $jabatan
					]
				];
				
				echo json_encode($return);
				return;
			}
			
			// Jika data approver tidak ditemukan
			if (!$data_approver) {
				$return = [
					'status' => false,
					'data' => [
						'nama' => '',
						'jabatan' => '',
						'message' => 'Data approver tidak ditemukan, silakan isi No. Pokok dengan benar'
					]
				];
				echo json_encode($return);
				return;
			}
		
			// Cek jika np_approver sama dengan np_karyawan
			if ($np_approver === $np_karyawan) {
				$return = [
					'status' => false,
					'data' => [
						'nama' => '',
						'jabatan' => '',
						'message' => 'Approver tidak boleh sama dengan pengaju'
					]
				];
				echo json_encode($return);
				return;
			}
		
			// Jika approver adalah Direksi, langsung valid
			if ($data_approver['kontrak_kerja'] === "Direksi") {
				$jabatan = ($is_poh == "false") ? $data_approver['nama_jabatan'] : $data_approver['nama_jabatan_poh'];
				$return = [
					'status' => true,
					'data' => [
						'nama' => $data_approver['nama'],
						'jabatan' => $jabatan
					]
				];
				echo json_encode($return);
				return;
			}
		
			// Jika karyawan adalah Direksi, pastikan approver juga Direksi
			if ($data_karyawan['kontrak_kerja'] === "Direksi" && $data_approver['kontrak_kerja'] !== "Direksi") {
				$return = [
					'status' => false,
					'data' => [
						'nama' => '',
						'jabatan' => '',
						'message' => 'Approver harus memiliki kontrak kerja Direksi untuk pengaju Direksi'
					]
				];
				echo json_encode($return);
				return;
			}
		
			// Definisikan hierarki jabatan dengan grup setara
			$grupJabatanHierarki = [
				["SEKIV"],
				["AHLIMUDA", "KASEK"],
				["AHLIMDYA", "KADEP"],
				["AHLIUTMA", "KADIV"],
				["Direksi"]
			];
		
			// Cari index grup jabatan karyawan dan approver
			$jabatanKaryawanIndex = $this->findGrupIndex($data_karyawan['grup_jabatan'], $grupJabatanHierarki);
			$jabatanApproverIndex = $this->findGrupIndex($data_approver['grup_jabatan'], $grupJabatanHierarki);
		
			// Jika grup jabatan pengaju tidak valid, kembali ke logika awal approval
			if ($jabatanKaryawanIndex === false) {
				$approvalGrupJabatan = [];
				if ($approval_number == 1) {
					$approvalGrupJabatan = ["SEKIV", "AHLIMUDA", "KASEK", "AHLIMDYA", "KADEP", "AHLIUTMA", "KADIV"];
				} elseif ($approval_number == 2) {
					$approvalGrupJabatan = ["AHLIMDYA", "KADEP", "AHLIUTMA", "KADIV"];
				}
		
				$valid_grup_jabatan = in_array($data_approver['grup_jabatan'], $approvalGrupJabatan);
		
				if (!$valid_grup_jabatan) {
					$return = [
						'status' => false,
						'data' => [
							'nama' => '',
							'jabatan' => '',
							'message' => 'Grup jabatan tidak valid, silakan isi No. Pokok dengan benar'
						]
					];
					echo json_encode($return);
					return;
				}
		
				$jabatan = ($is_poh == "false") ? $data_approver['nama_jabatan'] : $data_approver['nama_jabatan_poh'];
				$return = [
					'status' => true,
					'data' => [
						'nama' => $data_approver['nama'],
						'jabatan' => $jabatan
					]
				];
				echo json_encode($return);
				return;
			}
		
			// Logika pengecekan berdasarkan approval_number
			$minimalLevel = ($approval_number == 1) ? 1 : 2;
		
			if ($jabatanApproverIndex < $jabatanKaryawanIndex + $minimalLevel) {
				$return = [
					'status' => false,
					'data' => [
						'nama' => '',
						'jabatan' => '',
						'message' => "Untuk approval level $approval_number, jabatan approver harus minimal $minimalLevel tingkat lebih tinggi dari pengaju"
					]
				];
				echo json_encode($return);
				return;
			}
		
			$jabatan = ($is_poh == "false") ? $data_approver['nama_jabatan'] : $data_approver['nama_jabatan_poh'];
		
			$return = [
				'status' => true,
				'data' => [
					'nama' => $data_approver['nama'],
					'jabatan' => $jabatan
				]
			];
		
			echo json_encode($return);
		}
		
		// Fungsi untuk menemukan index grup jabatan
		private function findGrupIndex($jabatan, $grupJabatanHierarki) {
			foreach ($grupJabatanHierarki as $index => $grup) {
				if (in_array($jabatan, $grup)) {
					return $index;
				}
			}
			return false;
		}
		
		private function checkPOH($np_karyawan, $np_poh_approvel, $np_pengaju) {
			
			// 	// 1. ambil ke table poh dimana np_definitif == $np_poh_approvel & np_poh == $np_karyawan
			// 	// 2. Check tanggalnya mulai dan berakhir apakah hari ini termasuk pada tanggal tersebut 
			// 	// 3. lalu kembalikan nilai bahwa dia itu benar poh nya dan tanggalnya juga sesuai 
			// Ambil data POH terkait
			$data_poh = get_poh_data($np_karyawan);
			if ($data_poh != null) {
				// Mendapatkan tanggal hari ini
				$tanggal_hari_ini = date('Y-m-d');
		
				// Memeriksa apakah tanggal hari ini berada dalam rentang
				$tanggal_mulai = $data_poh['tanggal_mulai'];
				$tanggal_selesai = $data_poh['tanggal_selesai'];
				// var_dump("kesini gitu", $tanggal_mulai, $tanggal_selesai, $tanggal_hari_ini );exit;
				
				if ($tanggal_hari_ini >= $tanggal_mulai && $tanggal_hari_ini <= $tanggal_selesai) {
					return true ;
				}
			}
		
			return false;
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
		
		public function action_insert_cuti()
		{
			$submit = $this->input->post('submit');
					
			if($submit)
			{
				$np_karyawan = html_escape($this->input->post('np_karyawan', true));
				// $absence_type = html_escape($this->input->post('absence_type', true));
				$mst_cuti = html_escape($this->input->post('absence_type', true));
				$expld = explode('-', $mst_cuti);
				$mst_cuti_id = $expld[0];
				$absence_type = $expld[1];
				$type_cuber = html_escape($this->input->post('type_cuber', true));

				// cek masa jabatan by tanggal masuk
				$data_mst_karyawan = $this->db->select('no_pokok, nama, tanggal_masuk, kontrak_kerja')->where('no_pokok', $np_karyawan)->get('mst_karyawan')->row();
				if($data_mst_karyawan){
					$givenDate = new DateTime($data_mst_karyawan->tanggal_masuk);
					$now = new DateTime();
					$thirtyDaysAgo = $now->sub(new DateInterval('P30D'));
					if($givenDate > $thirtyDaysAgo || $data_mst_karyawan->kontrak_kerja=='PKWT'){
						if($givenDate > $thirtyDaysAgo) {
							$allowed_cuti = ['2001|2040','2001|2065','2001|2064','2001|2068','2001|2030'];
							if(!in_array($absence_type, $allowed_cuti)){
								$this->session->set_flashdata('warning', "Pengajuan cuti bisa dilakukan di bulan berikutnya terhitung tanggal masuk : {$data_mst_karyawan->tanggal_masuk}");
								redirect(base_url($this->folder_controller.'permohonan_cuti'));
							}
						} else if($data_mst_karyawan->kontrak_kerja=='PKWT') {
							$allowed_cuti = ['2001|2040','2001|2065','2001|2064','2001|2068','2001|2030','2001|1000','2001|1020'];
							if(!in_array($absence_type, $allowed_cuti) && ($absence_type=='2001|1020' && $type_cuber!='2001|1000')){
								$this->session->set_flashdata('warning', "Pengajuan cuti bisa dilakukan di bulan berikutnya terhitung tanggal masuk : {$data_mst_karyawan->tanggal_masuk}");
								redirect(base_url($this->folder_controller.'permohonan_cuti'));
							}
						}
					}
				} else{
					$this->session->set_flashdata('warning',"NP Tidak Ditemukan!");
					redirect(base_url($this->folder_controller.'permohonan_cuti'));
				}
				// END: cek masa jabatan by tanggal masuk

				//07 04 2022 Wina, jalankan fungsi pengecekan alasan tidak tersimpan jika tidak jelas
				if (strlen(trim($this->input->post('alasan')))>4) {
	                //echo json_encode($this->input->post()); exit();
					// Update Mozes -> Agar user tidak bisa membuat injection pada form inputan free text
					
					$start_date = date('Y-m-d', strtotime($this->input->post('start_date')));
					$end_date = date('Y-m-d', strtotime($this->input->post('end_date')));
					$jumlah_hari = html_escape($this->input->post('jumlah_hari'));
					$jumlah_bulan = html_escape($this->input->post('jumlah_bulan'));
					$alasan = html_escape($this->input->post('alasan'));
					$keterangan = html_escape($this->input->post('keterangan'));
					$approval_1 = html_escape($this->input->post('approval_1'));
					$approval_1_jabatan = html_escape($this->input->post('approval_1_input_jabatan'));
					$approval_2 = html_escape($this->input->post('approval_2'));
					$approval_2_jabatan = html_escape($this->input->post('approval_2_input_jabatan'));

					if($absence_type=='2001|1020'){
						$absence_type = $type_cuber;
						$is_cuti_bersama = '1';
					} else{
						$is_cuti_bersama = '0';
					}
									
					$nama_karyawan 		= nama_karyawan_by_np($np_karyawan);
					$nama_approval_1 	= nama_karyawan_by_np($approval_1);
					$nama_approval_2 	= nama_karyawan_by_np($approval_2);
					
					$erp				= erp_master_data_by_np($np_karyawan, $start_date);				
					$kode_unit 			= $erp['kode_unit'];
					$nama_unit 			= $erp['nama_unit'];
					$nama_jabatan		= $erp['nama_jabatan'];
					$personel_number	= $erp['personnel_number'];
					
					//Tri WIbowo - 7648 07 08 2023 - Laporan bisa inject via inspect element (dilakukan validasi server side)					
					//Jika Pengguna maka tidak boleh input yg lain lewat inspect
					if($_SESSION["grup"]==5) //jika Pengguna
					{
						if($_SESSION["no_pokok"] != $np_karyawan)
						{
							$this->session->set_flashdata('warning',"Terjadi Kesalahan, Data Login dan Input tidak sama");
							redirect(base_url($this->folder_controller.'permohonan_cuti'));	
						}					
					}

					

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
						
						//06 01 2021, 7648 - Tri Wibowo, Agar bisa backdate input cuti ketika NP nya berubah
						$nama_data_sebelumnya = nama_karyawan_by_np_bulan_sebelumnya($np_karyawan);			
						if($nama_data_sebelumnya)
						{
							$nama_karyawan = $nama_data_sebelumnya;
						}
						else
						{
							$this->session->set_flashdata('warning',"NP Karyawan <b>$np_karyawan</b> tidak ditemukan.");
							redirect(base_url($this->folder_controller.'permohonan_cuti'));
						}
						//end of 06 01 2021, 7648 - Tri Wibowo
						
						
						
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
						else if($absence_type=='2001|2068')
							$hari_cuti = 3;
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
					$data_insert['is_cuti_bersama']		= $is_cuti_bersama;
					$data_insert['mst_cuti_id']		= $mst_cuti_id;
					
					// if ($type_cuber == '2001|1000') {
					// 	$data_insert['ambil_cuti_dari']	= '2001|1000';
					// 	$data_insert['is_cuti_bersama']		= '1';
					// } elseif ($type_cuber == '2001|1010') {
					// 	$data_insert['ambil_cuti_dari']	= '2001|1010';
					// 	$data_insert['is_cuti_bersama']		= '1';
					// } elseif ($type_cuber == '2001|2080') {
					// 	$data_insert['ambil_cuti_dari']	= '2001|2080';
					// 	$data_insert['is_cuti_bersama']		= '1';
					// }
					// var_dump($data_insert);die;
					
					$insert_cuti = $this->m_permohonan_cuti->insert_cuti($data_insert);
					
					if($insert_cuti!="0")
					{	
						$this->session->set_flashdata('success',"Permohonan Cuti <b>$np_karyawan | $nama_karyawan </b> berhasil ditambahkan.");

						if($absence_type=='2001|2080' || $type_cuber=='2001|2080'){
							$cek = $this->db->where(['deleted_at' => null,'no_pokok' => $np_karyawan])->get('cuti_hutang')->row();
							if (@$cek == null) {
								$this->db->insert('cuti_hutang', [
									'no_pokok' => $np_karyawan,
									'hutang' => $jumlah_hari,
									'created_at' => date('Y-m-d H:i:s')
								]);
							} else {
								$this->db->where('no_pokok', $np_karyawan)->update('cuti_hutang', [
									'hutang' => ($cek->hutang + $jumlah_hari),
									'updated_at' => date('Y-m-d H:i:s')
								]);
							}
						}

						
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
				} else {
					$this->session->set_flashdata('warning',"Permohonan Gagal. Alasan Tidak Valid!");
					redirect(base_url($this->folder_controller.'permohonan_cuti'));
				}
				
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

		public function ajax_getPilihanAtasanCuti($jenis='approval_1'){
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
	}
	
	/* End of file perencanaan_jadwal_kerja.php */
	/* Location: ./application/controllers/kehadiran/perencanaan_jadwal_kerja.php */
