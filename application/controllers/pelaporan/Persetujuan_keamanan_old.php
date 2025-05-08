<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Persetujuan_keamanan extends CI_Controller {
		public function __construct(){
			parent::__construct();
			
			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'perizinan/';
			$this->folder_model = 'perizinan/';
			$this->folder_controller = 'perizinan/';
			
			$this->akses = array();
			
			$this->load->helper("cutoff_helper");
			$this->load->helper("tanggal_helper");
			$this->load->helper("karyawan_helper");
			$this->load->helper("reference_helper");
			$this->load->helper("perizinan_helper");
			
			#$this->load->model($this->folder_model."m_permohonan_cuti");
			
			$this->data["is_with_sidebar"] = true;
			
			$this->data['judul'] = "Persetujuan Keamanan";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);				
            $this->nama_db = $this->db->database;
			izin($this->akses["akses"]);
		}
		
		public function index()
		{
            //$this->output->enable_profiler(true);
            $this->load->model($this->folder_model."M_persetujuan_keamanan");
			//echo __FILE__ . __LINE__;die(var_dump($this->akses));
            $izin = $this->M_persetujuan_keamanan->get_mst_perizinan()->result();
            $array_daftar_karyawan	= $this->M_persetujuan_keamanan->select_daftar_karyawan();
            
			$this->data["akses"] 					= $this->akses;
			$this->data["navigasi_menu"] 			= menu_helper();
			$this->data['content'] 					= $this->folder_view."persetujuan_keamanan";
            $this->data['jenis_izin']               = $izin;
			//$this->data['select_mst_cuti']= $this->m_permohonan_cuti->select_mst_cuti();
            $this->data['array_daftar_karyawan'] 	= $array_daftar_karyawan;				
                        
            $this->data['array_tahun_bulan'] 		= $this->M_persetujuan_keamanan->get_tabel_perizinan_from_schema()->result();
            if ($this->akses['lihat semua pos'])
            	$this->data['array_pos'] 			= $this->db->where('status', '1')->get('mst_pos')->result();
            else
            	$this->data['array_pos'] 			= $this->db->where('status', '1')->where("find_in_set('".$_SESSION['no_pokok']."', no_pokok)")->get('mst_pos')->result();

            // echo $this->session->flashdata('date_range');exit;
            // echo $this->session->flashdata('bulan_tahun');exit;
			$this->load->view('template',$this->data);
		}	
		
		public function tabel_persetujuan_keamanan() {
            $jenis = array();
            
            $filter_bulan   = $this->uri->segment(4);
            $filter_pos   = $this->uri->segment(11);
            
            if($this->uri->segment(5)==1){
                $jenis[] = '0';
            }
            if($this->uri->segment(6)==1){ //izin dinas pendidikan / non pendidikan
                $jenis[] = 'C';
            }
			/*
            if($this->uri->segment(5)==1){
                $jenis[] = 'D';
            }
			*/
            if($this->uri->segment(7)==1){
                $jenis[] = 'E';
            }
            if($this->uri->segment(8)==1){
                $jenis[] = 'F';
            }
            if($this->uri->segment(9)==1){
                $jenis[] = 'G';
            }
            if($this->uri->segment(10)==1){
                $jenis[] = 'H';
            }
			/*
            if($this->uri->segment(10)==1){
                $jenis[] = 'TM';
            }
            if($this->uri->segment(11)==1){
                $jenis[] = 'TK';
            }
			*/
            
            /*if($this->uri->segment(14)==1){
                $jenis[] = 'AB';
            }
            if($this->uri->segment(15)==1){
                $jenis[] = 'ATU';
            }*/
            
            $target_table   = $filter_bulan;
            
			$this->load->model($this->folder_model."M_tabel_persetujuan_keamanan");
            
//            if($this->akses["ubah"]){ //jika pengguna
//				$disabled_ubah = '';
//			} else{
//				$disabled_ubah = 'disabled';
//			}
			
			if($_SESSION["grup"]==4) //jika Pengadministrasi Unit Kerja
			{
				$var=array();
				$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
				foreach ($list_pengadministrasi as $data) //looping list_pengadministrasi
				{	
					array_push($var,$data['kode_unit']);							
				}				
			} else if($_SESSION["grup"]==5) //jika Pengguna
			{
				$var 	= $_SESSION["no_pokok"];							
			} else
			{
				$var = 1;				
			}
            
            # tambahan untuk filter date range, 2021-02-24
            $date_range = $this->input->post('date_range',true);
            $explode_date_range = explode(' - ', $date_range);
            $startDate = date('Y-m-d', strtotime($explode_date_range[0]));
            $endDate = date('Y-m-d', strtotime($explode_date_range[1]));
			$params = [];
            $params['startDate'] = $startDate;
            $params['endDate'] = $endDate;
            $params['table_name'] = $target_table;
            # END tambahan untuk filter date range, 2021-02-24
			
			$list 	= $this->M_tabel_persetujuan_keamanan->get_datatables($var,$params,@$jenis,$filter_pos);	
			
			
			$data = array();
			$no = $_POST['start'];
			
			foreach ($list as $tampil) {
				$no++;
                $absence_type = $tampil->kode_pamlek.'|'.$tampil->info_type.'|'.$tampil->absence_type;
				$row = array();
				$row[] = $no;
				$row[] = $tampil->np_karyawan.'<br>'.$tampil->nama;
				//$row[] = $tampil->absence_type;	
				$row[] = get_perizinan_name($tampil->kode_pamlek)->nama;	
				
				if($tampil->start_date) {
					$row[] = tanggal_indonesia($tampil->start_date).'<br>'.$tampil->start_time.'<br><span class="text-primary"><b>machine : '.$tampil->machine_id_start.'</b></span>';
				} else {
					$row[] = '';
				}
				
				if($tampil->end_date) {
					$row[] = tanggal_indonesia($tampil->end_date).'<br>'.$tampil->end_time.'<br><span class="text-primary"><b>machine : '.$tampil->machine_id_end.'</b></span>';
				} else {
					$row[] = '';
				}
				
                $row[] = implode("<br>", array_column($this->db->where("id in ('".(implode("','", json_decode($tampil->pos)))."')")->get('mst_pos')->result_array(), 'nama'));

				$np_karyawan	= trim($tampil->np_karyawan);
				$nama			= trim($tampil->nama);
				$kode_pamlek	= trim($tampil->kode_pamlek);
				$created_at		= trim($tampil->created_at);
				$start_date		= trim(tanggal_indonesia($tampil->start_date).' '.$tampil->start_time);
				$end_date		= trim(tanggal_indonesia($tampil->end_date).' '.$tampil->end_time);
				$approval_1		= trim($tampil->approval_1_np);
				$approval_2		= trim($tampil->approval_2_np);	
				$status_1		= trim($tampil->approval_1_status);
				$status_2		= trim($tampil->approval_2_status);
				$approval_1_date= trim($tampil->approval_1_updated_at);
				$approval_2_date= trim($tampil->approval_2_updated_at);

//				if($status_1=='1')
//				{						
//					$approval_1_nama 	= $approval_1." | ".nama_karyawan_by_np($approval_1);
//					$approval_1_status 	= "Izin Telah Disetujui pada $approval_1_date."; 
//				}else
//				if($status_1=='2')
//				{					
//					$approval_1_nama 	= $approval_1." | ".nama_karyawan_by_np($approval_1);
//					$approval_1_status 	= "Izin TIDAK disetujui pada $approval_1_date."; 
//				}else
//				if($status_1=='3')
//				{					
//					$approval_1_nama 	= $approval_1." | ".nama_karyawan_by_np($approval_1);
//					$approval_1_status 	= "Pengajuan Izin Dibatalkan oleh pemohon pada $approval_1_date."; 
//				}else
//				if($status_1==''||$status_1=='0')
//				{				
//					$status_1 = '0';
//					$approval_1_nama 	= $approval_1." | ".nama_karyawan_by_np($approval_1);
//					$approval_1_status 	= "Izin BELUM disetujui."; 
//				}
//				
//				if($status_2=='1')
//				{						
//					$approval_2_nama 	= $approval_2." | ".nama_karyawan_by_np($approval_2);
//					$approval_2_status 	= "Izin Telah Disetujui pada $approval_2_date."; 
//				}else
//				if($status_2=='2')
//				{					
//					$approval_2_nama 	= $approval_2." | ".nama_karyawan_by_np($approval_2);
//					$approval_2_status 	= "Izin TIDAK disetujui pada $approval_2_date."; 
//				}else
//				if($status_2=='3')
//				{					
//					$approval_2_nama 	= $approval_2." | ".nama_karyawan_by_np($approval_2);
//					$approval_2_status 	= "Pengajuan Izin Dibatalkan oleh pemohon pada $approval_2_date."; 
//				}else
//				if($status_2==''||$status_2=='0')
//				{
//					$status_2 = '0';
//					$approval_2_nama 	= $approval_2." | ".nama_karyawan_by_np($approval_2);
//					$approval_2_status 	= "Izin BELUM disetujui."; 
//				}
                
                # ada perizinan yg membutuhkan atasan 2, dan ada perizinan yg TIDAK perlu atasan 2
                # 2021-03-04, heru/bowo
                
                $tombol_action=1;
                # (jika NP Atasan 1 terisi dan Atasan 1 status is (null OR !=1 OR empty)) atau (jika NP Atasan 2 terisi dan Atasan 2 status is (null OR 1 OR empty)) maka tombol kuning, izin belum disetujui atasan
                if( ($approval_1!=null && ($status_1==null || $status_1=='0' || empty($status_1))) && ($approval_2!=null && ($status_2==null || $status_2=='0' || empty($status_2))) ){
                    $btn_warna		='btn-warning';
                    $btn_text		='Izin BELUM disetujui Atasan';
                    $btn_disabled 	='';
                    $tombol_action=0;
                } else if(($approval_1!=null && ($status_1==null || $status_1=='0' || empty($status_1))) && ($approval_2==null)){
                    $btn_warna		='btn-warning';
                    $btn_text		='Izin BELUM disetujui Atasan';
                    $btn_disabled 	='';
                    $tombol_action=0;
                } else if(($status_1=='1') && ($status_2!='2' && $status_2!='1')) { //disetujui atasan 1
					if($tampil->approval_2_np==null || $tampil->approval_2_np=='') { //jika tidak ada atasan 2
						$btn_warna		='btn-success';
						$btn_text		='Disetujui Atasan 1';
						$btn_disabled 	='';
					}else { //jika ada atasan 2
						$btn_warna		='btn-warning';
						$btn_text		='Disetujui Atasan 1, Menunggu Atasan 2';
						$btn_disabled 	='disabled';
					}
				} else if(($status_1=='2') && ($status_2!='2' && $status_2!='1')) { //ditolak atasan 1
					$btn_warna		='btn-danger';
					$btn_text		='Ditolak Atasan 1';
					$btn_disabled 	='disabled';
				} else if($status_2=='2') { //ditolak atasan 1
					$btn_warna		='btn-danger';
					$btn_text		='Ditolak Atasan 2';
					$btn_disabled 	='disabled';
				} else if($status_2=='1') { //disetujui atasan  2
					$btn_warna		='btn-success';
					$btn_text		='Disetujui Atasan 2';
					$btn_disabled 	='disabled';
					
					if($status_1=='0' || $status_1==null) { //jika paralel atasan 2 belum approve
						$btn_warna		='btn-warning';
						$btn_text		='Disetujui Atasan 2, Menunggu Atasan 1';
						$btn_disabled 	='disabled';
					}
                } else{
                    $btn_warna		='';
                    $btn_text		='';
                    $btn_disabled 	='';
                }

                if($tampil->date_batal!=null) { //dibatalkan
					$btn_warna		='btn-danger';
					$btn_text		='Dibatalkan keamanan';
					$btn_disabled 	='disabled';
				}

                $btn_disabled 	='';
				$row[] = "<button class='btn $btn_warna btn-xs'>$btn_text</button>";

				$pengamanan_posisi = json_decode($tampil->approval_pengamanan_posisi);
				$last_pengamanan = $pengamanan_posisi[(count($pengamanan_posisi)-1)];


				if($tampil->approval_pengamanan_posisi!=null && $tampil->approval_pengamanan_posisi!='') //sudah approval
				{
					$btn_warna		='btn-warning';
					$btn_text		=ucwords($last_pengamanan->posisi);
					$btn_disabled 	='';
				} else {
					$btn_warna		='btn-danger';
					$btn_text		='Belum Keluar/Masuk';
					$btn_disabled 	='';
				}
				
				/*$row[] = "<button class='btn ".$btn_warna." btn-xs status_button' data-toggle='modal' data-target='#modal_status'
					data-np-karyawan='$np_karyawan'
					data-nama='$nama'
					data-pamlek='$kode_pamlek'
					data-created-at='$created_at'
					data-start-date='$start_date'
					data-end-date='$end_date'
					data-approval-1-nama='$approval_1_nama'
					data-approval-2-nama='$approval_2_nama'
					data-approval-1-status='$approval_1_status'
					data-approval-2-status='$approval_2_status'						
				>$btn_text</button>";*/

                $btn_disabled 	='';
				$row[] = "<button class='btn ".$btn_warna." btn-xs detail_button' data-toggle='modal' data-target='#modal_detail' data-id=".$tampil->id." data-tgl=".($tampil->start_date!=null ? $tampil->start_date: $tampil->end_date).">$btn_text</button>";
					// data-created-by='$created_by'
				
                if ($this->akses["persetujuan"]) {
					
					//cutoff ERP
					if($tampil->start_date) {
						$tanggal_check = $tampil->start_date;
					} else {
						$tanggal_check = $tampil->end_date;
					}
					
					$sudah_cutoff = sudah_cutoff($tanggal_check);
					
					if($sudah_cutoff) { //jika sudah lewat masa cutoff
						$aksi = "<button class='btn btn-primary btn-xs' data-toggle='tooltip' title='Tidak bisa melakukan aksi setelah Cutoff data di ERP pada $sudah_cutoff' disabled>Submit ERP</button>";
						
					} else {					
						if ($tampil->np_batal==null || $tampil->np_batal=='') {
							$aksi = "<button class='btn btn-primary btn-xs persetujuan_button' data-toggle='modal' data-target='#modal_persetujuan' data-id='".$tampil->id."' data-tgl='".($tampil->start_date!=null ? $tampil->start_date: $tampil->end_date)."' >Persetujuan</button>";
						} else {
							$aksi = "<button class='btn btn-primary btn-xs detail_button' data-toggle='modal' data-target='#modal_detail' data-id='".$tampil->id."' data-tgl='".($tampil->start_date!=null ? $tampil->start_date: $tampil->end_date)."' >Detail</button>";
						}
						/*$row[] = "<button class='btn btn-primary btn-xs persetujuan_button' data-toggle='modal'  data-target='#modal_persetujuan'
							data-np-karyawan='$np_karyawan'
							data-nama='$nama'
							data-created-at='$created_at'
							data-start-date='$start_date'
							data-end-date='$end_date'
							data-created-by='$created_by'
							data-approval-1-nama='$approval_1_nama'
							data-approval-2-nama='$approval_2_nama'
							data-approval-1-status='$approval_1_status'
							data-approval-2-status='$approval_2_status'	
							data-approval-1='$approval_1'
							data-approval-2='$approval_2'
							data-status-1='$status_1'
							data-status-2='$status_2'
							$btn_disabled>Persetujuan</button>";	*/					
					}
                   
                
				}
                else {
                    $aksi = "";
                }
                
				$row[] = $aksi;
				
				$data[] = $row;
			}

			$output = array(
							"draw" => $_POST['draw'],
							"recordsTotal" => $this->M_tabel_persetujuan_keamanan->count_all($var, $params, @$jenis),
							"recordsFiltered" => $this->M_tabel_persetujuan_keamanan->count_filtered($var, $params, @$jenis),
							"data" => $data,
					);
			//output to json format
			echo json_encode($output);
		}
	
        //Fungsi untuk mengambil data dalam file .txt yang ada di outbound_portal/sppd
        public function get_data($last_date=null) {
            //run program selamanya untuk menghindari maximal execution
            //ini_set('MAX_EXECUTION_TIME', -1);
            set_time_limit('0');

            //$this->output->enable_profiler(TRUE);

            echo "Proses ambil data dari pamlek";
            echo "<br>mulai ".date('Y-m-d H:i:s')."<br>";

            //ambil data di database setting
            $this->load->model($this->folder_model."M_sppd");
            $setting	= $this->M_sppd->setting();
            
            $pamlek_url	= dirname($_SERVER["SCRIPT_FILENAME"]).$setting['url'];
            $pamlek_max	= $setting['max_files_hapus'];
            // $pamlek_max	= $setting['max_files'];
            
            if(@$last_date){
                $file = 'biaya-sppd-'.$last_date.'.txt';
                if($last_date==date('Y-m-d') && is_file($pamlek_url.$file)){
                    $this->read_process($pamlek_url, $file);
                    
                } else{
                    $data_error['modul'] 		= "perizinan/sppd/get_data"; 
                    $data_error['error'] 		= "Gagal konek ke Server Pamlek"; 
                    $data_error['status'] 		= "0";
                    $data_error['created_at'] 	= date("Y-m-d H:i:s");
                    $data_error['created_by'] 	= "scheduler";

                    $this->M_sppd->insert_error($data_error);
                    echo "<br>status = ".$data_error['error'].", ".$data_error['modul'];
                }
            } else{
                //ambil data mana saja yang belum di proses
                $result = $this->M_sppd->select_pamlek_files_limit($pamlek_max);

                $arr_registered_pamlek_files = array();
                foreach ($result->result_array() as $data) {
                    array_push($arr_registered_pamlek_files,$data['nama_file']);	 
                }
                
                //check server pamlek menyala
                if(is_dir($pamlek_url)) {
                    //scan file .txt dalam server ftp pamlek 
                    $arr_scan_pamlek_files = scandir($pamlek_url);

                    $pamlek_files = array();		
                    foreach($arr_scan_pamlek_files as $file){
                        if(in_array($file,$arr_registered_pamlek_files)){
                            array_push($pamlek_files,$file);
                        }
                    }

                    foreach($pamlek_files as $file){
                        $this->read_process($pamlek_url, $file);
                    }
                } else {
                    $data_error['modul'] 		= "perizinan/sppd/get_data"; 
                    $data_error['error'] 		= "Gagal konek ke Server Pamlek"; 
                    $data_error['status'] 		= "0";
                    $data_error['created_at'] 	= date("Y-m-d H:i:s");
                    $data_error['created_by'] 	= "scheduler";

                    $this->M_sppd->insert_error($data_error);
                    echo "<br>status = ".$data_error['error'].", ".$data_error['modul'];
                }
            }
            
            echo "<br>selesai ".date('Y-m-d H:i:s');
        }
        
        function read_process($pamlek_url, $file){
            echo "<br>".$file."<br><br>";

            $rows = explode("\n",trim(file_get_contents($pamlek_url.$file)));

            $i =1;
            $banyak_data=0;			
            
            //parsing data di file .txt
            $array_insert_data = array();
            foreach($rows as $row){
                if(!empty(trim($row))){

                    $banyak_data++;					
                    $pisah = explode("\t",trim($row));
                    
                    $insert_data = array(
                        'id_sppd'       	=> $pisah[0],
                        'id_user'           => $pisah[1],
                        'kode_sto' 			=> $pisah[2],
                        'jenis_fasilitas'	=> $pisah[3],	
                        'biaya'			    => $pisah[4],	
                        'catatan'   		=> @$pisah[6],	
                        'tgl_pulang'		=> @$pisah[7]
                    );

                    $this->M_sppd->cek_id_then_insert_data($pisah[0], $insert_data);
                }
            }
            
            $update_file = array(
                'proses'			=> '1',
                'baris_data' 		=> $banyak_data,
                'waktu_proses'		=> date('Y-m-d H:i:s')
            );

            $this->M_sppd->update_files($file, $update_file);
        }
        
        public function ajax_getListNp()
		{			
            $this->load->model($this->folder_model."M_persetujuan_keamanan");
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
				$np_list=$this->M_persetujuan_keamanan->select_np_by_kode_unit($list_kode_unit);						
								
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
			
			if($_SESSION["grup"]==4) //jika Pengadministrasi Unit Kerja
			{
				$temp='';
				$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
				foreach ($list_pengadministrasi as $data) //looping list_pengadministrasi
				{					
					if(kode_unit_by_np($np_karyawan)==$data['kode_unit']) //check apakah ada disalah satu unit 
					{
						$temp=$np_karyawan;
					}				
				}
				
				$np_karyawan	=$temp;				
				
			}else
			if($_SESSION["grup"]==5) //jika Pengguna
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
			
			echo $nama; 	
		}
        
        function action_insert_perizinan(){
            $data_insert = [];
            $submit = $this->input->post('submit');
            if($submit){
				$absence_type		= $this->input->post('absence_type');
                $explode = explode('|', $absence_type);
                $info_type = $explode[1];
                $absence_type = $explode[2];
                $kode_pamlek = $explode[0];
                
                if($kode_pamlek=='0'){
                    $this->action_insert_perizinan_sidt($this->input->post()); exit();
                }
                
                $np_karyawan		= $this->input->post('np_karyawan');				
				$start_date			= date('Y-m-d',strtotime($this->input->post('start_date')));
				$start_time			= $this->input->post('start_time');
				$end_date			= date('Y-m-d',strtotime($this->input->post('end_date')));
				$end_time			= $this->input->post('end_time');
                
                $start_date_time = date('Y-m-d H:i', strtotime($start_date.' '.$start_time));
                $end_date_time = date('Y-m-d H:i', strtotime($end_date.' '.$end_time));
                $tahun_bulan     = str_replace('-','_',substr("$start_date", 0, 7));
                
				$nama_karyawan 		= erp_master_data_by_np($np_karyawan, $start_date)['nama'];
				$personel_number	= erp_master_data_by_np($np_karyawan, $start_date)['personnel_number'];
				$nama_jabatan		= erp_master_data_by_np($np_karyawan, $start_date)['nama_jabatan'];
				$kode_unit 			= erp_master_data_by_np($np_karyawan, $start_date)['kode_unit'];
				$nama_unit 			= erp_master_data_by_np($np_karyawan, $start_date)['nama_unit'];
                
                
                $data_insert = [
                    'np_karyawan'=>$np_karyawan,
                    'nama'=>$nama_karyawan,
                    'personel_number'=>$personel_number,
                    'nama_jabatan'=>$nama_jabatan,
                    'kode_unit'=>$kode_unit,
                    'nama_unit'=>$nama_unit,
                    'info_type'=>$info_type,
                    'absence_type'=>$absence_type,
                    'kode_pamlek'=>$kode_pamlek,
                    'start_date'=>$start_date,
                    'start_time'=>$start_time,
                    'end_date'=>$end_date,
                    'end_time'=>$end_time
                ];
                
                //echo '<br>under maintenance. <br>';
                //echo json_encode($data_insert); exit();
                
                if($np_karyawan=='' || $np_karyawan==null){
					$this->session->set_flashdata('warning',"NP Karyawan <b>$np_karyawan</b> tidak ditemukan.");
					redirect(base_url($this->folder_controller.'persetujuan_keamanan'));
				} else if($start_date=='' || $start_date==null){
					$this->session->set_flashdata('warning',"Start date tidak boleh kosong.");
					redirect(base_url($this->folder_controller.'persetujuan_keamanan'));
				} else if($end_date=='' || $end_date==null){
					$this->session->set_flashdata('warning',"End date tidak boleh kosong.");
					redirect(base_url($this->folder_controller.'persetujuan_keamanan'));
				} else if($start_date_time >= $end_date_time){
					$this->session->set_flashdata('warning',"Tanggal Akhir Perizinan harus lebih besar dari Tanggal Mulai Perizinan.");
					redirect(base_url($this->folder_controller.'persetujuan_keamanan'));
				}
                
                $this->db->query("CREATE TABLE IF NOT EXISTS ess_perizinan_$tahun_bulan LIKE ess_perizinan");
                
                //old $cek = $this->db->where(['np_karyawan'=>$nama_karyawan, 'start_date'=>$start_date, 'end_date'=>$end_date, 'start_time'=>$start_time, 'end_time'=>$end_time, 'kode_pamlek'=>$kode_pamlek])->get("ess_perizinan_$tahun_bulan");
                $cek = $this->db->query("SELECT * 
                FROM ess_perizinan_$tahun_bulan
                WHERE np_karyawan='$np_karyawan' 
                AND (
                    ('$start_date_time' BETWEEN DATE_FORMAT(CONCAT(start_date,' ',start_time),'%Y-%m-%d %H:%i') AND DATE_FORMAT(CONCAT(end_date,' ',end_time),'%Y-%m-%d %H:%i'))
                    OR ('$end_date_time' BETWEEN DATE_FORMAT(CONCAT(start_date,' ',start_time),'%Y-%m-%d %H:%i') AND DATE_FORMAT(CONCAT(end_date,' ',end_time),'%Y-%m-%d %H:%i'))
                    OR (DATE_FORMAT(CONCAT(start_date,' ',start_time),'%Y-%m-%d %H:%i') BETWEEN '$start_date_time' AND '$end_date_time')
                    OR (DATE_FORMAT(CONCAT(end_date,' ',end_time),'%Y-%m-%d %H:%i') BETWEEN '$start_date_time' AND '$end_date_time')
                ) ");

                if($cek->num_rows()>0){
                    $this->session->set_flashdata('warning',"Data perizinan dengan nama $nama_karyawan, pada rentang tanggal $start_date $start_time sampai $end_date $end_time sudah ada.");
					redirect(base_url($this->folder_controller.'persetujuan_keamanan'));
                } else{
                    $this->db->insert("ess_perizinan_$tahun_bulan", $data_insert);
                    
                    $parameter_perizinan = [
                        'id_row_baru'=>$this->db->insert_id(),
                        'np_karyawan'=>$np_karyawan,
                        'date_start'=>$start_date,
                        'date_end'=>$end_date
                    ];
                    $this->update_cico($parameter_perizinan);
                    
                    $this->session->set_flashdata('success',"Data perizinan dengan nama $nama_karyawan, pada rentang tanggal $start_date $start_time sampai $end_date $end_time berhasil ditambahkan.");
					redirect(base_url($this->folder_controller.'persetujuan_keamanan'));
                }
                
                echo json_encode($data_insert);
                
            } else{
                $this->session->set_flashdata('warning',"Terjadi Kesalahan");
				redirect(base_url($this->folder_controller.'persetujuan_keamanan'));	
            }
        }
        
        function action_insert_perizinan_sidt($data){
            $data_insert = [];
            $submit = $data['submit'];
            if($submit){
				$absence_type		= $data['absence_type'];
                $explode = explode('|', $absence_type);
                $info_type = $explode[1];
                $absence_type = $explode[2];
                $kode_pamlek = $explode[0];
                
                $np_karyawan		= $data['np_karyawan'];				
                
                if($np_karyawan=='' || $np_karyawan==null){
					$this->session->set_flashdata('warning',"NP Karyawan <b>$np_karyawan</b> tidak ditemukan.");
					redirect(base_url($this->folder_controller.'persetujuan_keamanan'));
				} else if($data['end_date']=='' || $data['end_date']==null){
					$this->session->set_flashdata('warning',"End date tidak boleh kosong.");
					redirect(base_url($this->folder_controller.'persetujuan_keamanan'));
				} else if($data['absence_type']=='' || $data['absence_type']==null){
					$this->session->set_flashdata('warning',"Jenis perizinan tidak boleh kosong.");
					redirect(base_url($this->folder_controller.'persetujuan_keamanan'));
				}
				//$start_date			= date('Y-m-d',strtotime($this->input->post('start_date')));
				//$start_time			= $this->input->post('start_time');
				$end_date			= date('Y-m-d',strtotime($data['end_date']));
				$end_time			= $data['end_time'];
                
                $end_date_time = date('Y-m-d H:i', strtotime($end_date.' '.$end_time));
                $tahun_bulan     = str_replace('-','_',substr("$end_date", 0, 7));
                
				$nama_karyawan 		= erp_master_data_by_np($np_karyawan, $end_date)['nama'];
				$personel_number	= erp_master_data_by_np($np_karyawan, $end_date)['personnel_number'];
				$nama_jabatan		= erp_master_data_by_np($np_karyawan, $end_date)['nama_jabatan'];
				$kode_unit 			= erp_master_data_by_np($np_karyawan, $end_date)['kode_unit'];
				$nama_unit 			= erp_master_data_by_np($np_karyawan, $end_date)['nama_unit'];
                
                
                $data_insert = [
                    'np_karyawan'=>$np_karyawan,
                    'nama'=>$nama_karyawan,
                    'personel_number'=>$personel_number,
                    'nama_jabatan'=>$nama_jabatan,
                    'kode_unit'=>$kode_unit,
                    'nama_unit'=>$nama_unit,
                    'info_type'=>$info_type,
                    'absence_type'=>$absence_type,
                    'kode_pamlek'=>$kode_pamlek,
                    //'start_date'=>$start_date,
                    //'start_time'=>$start_time,
                    'end_date'=>$end_date,
                    'end_time'=>$end_time
                ];
                
//                echo '<br>Khusus SIDT masih dalam perbaikan. <br>';
//                echo json_encode($data_insert); exit();
                
                $this->db->query("CREATE TABLE IF NOT EXISTS ess_perizinan_$tahun_bulan LIKE ess_perizinan");
                
                //cek table exist
                $get_table = $this->db->select('TABLE_NAME')->where('table_schema', $this->nama_db)->where('TABLE_NAME', 'ess_cico_'.$tahun_bulan)->get('information_schema.`TABLES`');
                if($get_table->num_rows()==0){
                    $this->session->set_flashdata('warning',"Data kehadiran belum tersedia.");
					redirect(base_url($this->folder_controller.'persetujuan_keamanan'));
                }
                
                $cek_perizinan = $this->db->where(['np_karyawan'=>$np_karyawan, 'end_date'=>$end_date, 'kode_pamlek'=>$kode_pamlek])->get("ess_perizinan_$tahun_bulan");
                
                $cek_cico = $this->db->select("(CASE WHEN dws_in_fix is not null then dws_in_fix ELSE dws_in END) as dws_in_time,(CASE WHEN dws_out_fix is not null then dws_out_fix ELSE dws_out END) as dws_out_time")->where(['np_karyawan'=>$np_karyawan, 'dws_tanggal'=>$end_date])->get("ess_cico_$tahun_bulan");
                
                if($cek_perizinan->num_rows()>0){
                    $this->session->set_flashdata('warning',"Data perizinan SIDT dengan nama $nama_karyawan, pada tanggal $end_date sudah ada.");
					redirect(base_url($this->folder_controller.'persetujuan_keamanan'));
                } else if($cek_cico->num_rows()==0){
                    $this->session->set_flashdata('warning',"Data kehadiran belum tersedia.");
					redirect(base_url($this->folder_controller.'persetujuan_keamanan'));
                } else{
                    $cek_jam = $this->db->select("MIN(start_time) AS min_start_time")->where(['np_karyawan'=>$np_karyawan, 'kode_pamlek!='=>'0', 'start_date'=>$end_date])->get("ess_perizinan_$tahun_bulan")->row();
                    
                    if(@$cek_jam->min_start_time!=NULL){
                        if(date('H:i:s', strtotime($end_time)) > $cek_jam->min_start_time){
                            $this->session->set_flashdata('warning',"Waktu SIDT tidak boleh melebihi waktu perizinan lain.");
                            redirect(base_url($this->folder_controller.'persetujuan_keamanan'));
                        }
                    } else if($cek_cico->num_rows()>0){
                        if((date('H:i:s', strtotime($end_time)) < $cek_cico->row()->dws_in_time) || (date('H:i:s', strtotime($end_time)) > $cek_cico->row()->dws_out_time)){
                            $this->session->set_flashdata('warning',"Waktu SIDT harus di antara DWS IN dan DWS OUT.");
                            redirect(base_url($this->folder_controller.'persetujuan_keamanan'));
                        }
                    }
                    
                    $this->db->insert("ess_perizinan_$tahun_bulan", $data_insert);
                    
                    $parameter_perizinan = [
                        'id_row_baru'=>$this->db->insert_id(),
                        'np_karyawan'=>$np_karyawan,
                        //'date_start'=>$start_date,
                        'date_end'=>$end_date
                    ];
                    $this->update_cico_sidt($parameter_perizinan);
                    
                    $this->session->set_flashdata('success',"Data perizinan SIDT dengan nama $nama_karyawan, pada tanggal $end_date $end_time berhasil ditambahkan.");
					redirect(base_url($this->folder_controller.'persetujuan_keamanan'));
                }
                
                echo json_encode($data_insert);
                
            } else{
                $this->session->set_flashdata('warning',"Terjadi Kesalahan");
				redirect(base_url($this->folder_controller.'persetujuan_keamanan'));	
            }
        }
        
        public function hapus($id=null, $np=null, $tanggal_end=null, $tanggal_start=null) {
            $this->load->model($this->folder_model."M_persetujuan_keamanan");
            //echo 'under maintenance.'; exit();
			if(@$id != null && @$np != null && @$tanggal_start != null && @$tanggal_end != null) {
                $tanggal_proses = $tanggal_start;
                while($tanggal_proses <= $tanggal_end){
                    $tahun_bulan = str_replace('-','_',substr("$tanggal_proses", 0, 7));
                    $cek = $this->db->query("SELECT TABLE_NAME FROM information_schema.`TABLES` WHERE table_schema='$this->nama_db' AND TABLE_NAME='ess_cico_$tahun_bulan'");
                    
                    if($cek->num_rows()>0){
                        $get_cico = $this->db->select('id, np_karyawan, id_perizinan, dws_tanggal')->where('np_karyawan', $np)->where('dws_tanggal', $tanggal_proses)->get('ess_cico_'.$tahun_bulan);
                        
                        if($get_cico->num_rows()>0){
                            $str_fix = '';
                            $row = $get_cico->row_array();
                            
                            //str awal diambil dari id_perizinan di cico
                            $str_awal = $row['id_perizinan'];
                            //convert str_awal to array_awal
                            $arr_awal = explode(',', $str_awal);

                            //concat dari id tabel perizinan
                            $str_datang = $id;

                            if (($key = array_search($str_datang, $arr_awal)) !== false) {
                                unset($arr_awal[$key]);
                                $arr_awal = array_values($arr_awal);
                            }

                            //convert arr_awal to str
                            $str_awal = implode(',', $arr_awal);
                            $str_fix = trim($str_awal,',');

                            $this->db->where('id', $row['id'])->update('ess_cico_'.$tahun_bulan, ['id_perizinan'=>$str_fix]);
                        }
                    }
                    
                    $tanggal_proses = date ("Y-m-d", strtotime("+1 day", strtotime($tanggal_proses)));
                }
                
                $tahun_bulan_perizinan = str_replace('-','_',substr("$tanggal_start", 0, 7));
                $get = $this->M_persetujuan_keamanan->ambil_perizinan_id($id, 'ess_perizinan_'.$tahun_bulan_perizinan);
                if($this->db->where('id', $id)->delete("ess_perizinan_$tahun_bulan_perizinan")){
                    $return["status"] = true;

                    $log_data_lama = "";
                    foreach($get as $key => $value){
                        if(strcmp($key,"id")!=0){
                            if(!empty($log_data_lama)){
                                $log_data_lama .= "<br>";
                            }
                            $log_data_lama .= "$key = $value";
                        }
                    }

                    $log = array(
                        "id_pengguna" => $this->session->userdata("id_pengguna"),
                        "id_modul" => $this->data['id_modul'],
                        "id_target" => $get["id"],
                        "deskripsi" => "hapus ".strtolower(preg_replace("/_/"," ",__CLASS__)),
                        "kondisi_lama" => $log_data_lama,
                        "kondisi_baru" => '',
                        "alamat_ip" => $this->data["ip_address"],
                        "waktu" => date("Y-m-d H:i:s")
                    );
                    $this->m_log->tambah($log);
                    $this->session->set_flashdata('success', 'Perizinan berhasil dihapus.');
                }
                
			} else if(@$id != null && @$np != null && @$tanggal_end != null && (@$tanggal_start==NULL || @$tangal_start=='')) {
                $tanggal_proses = $tanggal_end;
                //while($tanggal_proses <= $tanggal_end){
                $tahun_bulan = str_replace('-','_',substr("$tanggal_proses", 0, 7));
                $cek = $this->db->query("SELECT TABLE_NAME FROM information_schema.`TABLES` WHERE table_schema='$this->nama_db' AND TABLE_NAME='ess_cico_$tahun_bulan'");

                if($cek->num_rows()>0){
                    $get_cico = $this->db->select('id, np_karyawan, id_perizinan, dws_tanggal')->where('np_karyawan', $np)->where('dws_tanggal', $tanggal_proses)->get('ess_cico_'.$tahun_bulan);

                    if($get_cico->num_rows()>0){
                        $str_fix = '';
                        $row = $get_cico->row_array();

                        //str awal diambil dari id_perizinan di cico
                        $str_awal = $row['id_perizinan'];
                        //convert str_awal to array_awal
                        $arr_awal = explode(',', $str_awal);

                        //concat dari id tabel perizinan
                        $str_datang = $id;

                        if (($key = array_search($str_datang, $arr_awal)) !== false) {
                            unset($arr_awal[$key]);
                            $arr_awal = array_values($arr_awal);
                        }

                        //convert arr_awal to str
                        $str_awal = implode(',', $arr_awal);
                        $str_fix = trim($str_awal,',');

                        $this->db->where('id', $row['id'])->update('ess_cico_'.$tahun_bulan, ['id_perizinan'=>$str_fix]);
                    }
                }
                    
                    //$tanggal_proses = date ("Y-m-d", strtotime("+1 day", strtotime($tanggal_proses)));
                //}
                
                $tahun_bulan_perizinan = str_replace('-','_',substr("$tanggal_end", 0, 7));
                $get = $this->M_persetujuan_keamanan->ambil_perizinan_id($id, 'ess_perizinan_'.$tahun_bulan_perizinan);
                if($this->db->where('id', $id)->delete("ess_perizinan_$tahun_bulan_perizinan")){
                    $return["status"] = true;

                    $log_data_lama = "";
                    foreach($get as $key => $value){
                        if(strcmp($key,"id")!=0){
                            if(!empty($log_data_lama)){
                                $log_data_lama .= "<br>";
                            }
                            $log_data_lama .= "$key = $value";
                        }
                    }

                    $log = array(
                        "id_pengguna" => $this->session->userdata("id_pengguna"),
                        "id_modul" => $this->data['id_modul'],
                        "id_target" => $get["id"],
                        "deskripsi" => "hapus ".strtolower(preg_replace("/_/"," ",__CLASS__)),
                        "kondisi_lama" => $log_data_lama,
                        "kondisi_baru" => '',
                        "alamat_ip" => $this->data["ip_address"],
                        "waktu" => date("Y-m-d H:i:s")
                    );
                    $this->m_log->tambah($log);
                    $this->session->set_flashdata('success', 'Perizinan berhasil dihapus.');
                }
                
            } else {
				$this->session->set_flashdata('warning', 'Data Perizinan <b>Gagal</b> Dihapus.');
			}
			redirect(base_url('perizinan/persetujuan_keamanan'));
		}
        
        function update_cico($data_lempar){
            $tanggal_proses = $data_lempar['date_start'];
            
            while($tanggal_proses <= $data_lempar['date_end']){
                $tahun_bulan = str_replace('-','_',substr("$tanggal_proses", 0, 7));
                
                //cek table exist
                $get_table = $this->db->select('TABLE_NAME')->where('table_schema', $this->nama_db)->where('TABLE_NAME', 'ess_cico_'.$tahun_bulan)->get('information_schema.`TABLES`');
                if($get_table->num_rows()>0){
                    //get cico
                    $get_cico = $this->db->select('id, np_karyawan, id_perizinan, dws_tanggal')->where('np_karyawan', $data_lempar['np_karyawan'])->where('dws_tanggal', $tanggal_proses)->get('ess_cico_'.$tahun_bulan);
                    
                    if($get_cico->num_rows()>0){
                        $data_to_process = array();
                        $row = $get_cico->row_array();
                        
                        $data_to_process = [
                            'id'=>$row['id'],
                            'id_perizinan'=>$row['id_perizinan'],
                            'tahun_bulan'=>str_replace('-','_',substr($row['dws_tanggal'], 0, 7)),
                            'id_row_baru'=>$data_lempar['id_row_baru']
                        ];
                        $this->process_update_cico($data_to_process);
                        
                    }
                }
                
                $tanggal_proses = date ("Y-m-d", strtotime("+1 day", strtotime($tanggal_proses)));
            }
        }
        
        function update_cico_sidt($data_lempar){
            $tanggal_proses = $data_lempar['date_end'];
            
            //while($tanggal_proses <= $data_lempar['date_end']){
                $tahun_bulan = str_replace('-','_',substr("$tanggal_proses", 0, 7));
                
                //cek table exist
                $get_table = $this->db->select('TABLE_NAME')->where('table_schema', $this->nama_db)->where('TABLE_NAME', 'ess_cico_'.$tahun_bulan)->get('information_schema.`TABLES`');
                if($get_table->num_rows()>0){
                    //get cico
                    $get_cico = $this->db->select('id, np_karyawan, id_perizinan, dws_tanggal')->where('np_karyawan', $data_lempar['np_karyawan'])->where('dws_tanggal', $tanggal_proses)->get('ess_cico_'.$tahun_bulan);
                    
                    if($get_cico->num_rows()>0){
                        $data_to_process = array();
                        $row = $get_cico->row_array();
                        
                        $data_to_process = [
                            'id'=>$row['id'],
                            'id_perizinan'=>$row['id_perizinan'],
                            'tahun_bulan'=>str_replace('-','_',substr($row['dws_tanggal'], 0, 7)),
                            'id_row_baru'=>$data_lempar['id_row_baru']
                        ];
                        $this->process_update_cico($data_to_process);
                        
                    }
                }
                
                //$tanggal_proses = date ("Y-m-d", strtotime("+1 day", strtotime($tanggal_proses)));
            //}
        }
        
		public function view_detail()
		{			
			$id_ = $this->input->post('id_perizinan');
			$tgl = $this->input->post('tgl');
			$bulan = substr($tgl, 0, 4).'_'.substr($tgl, 5, 2);
			// $tabel = 'ess_perizinan_'.$bulan;
			$tabel = 'ess_request_perizinan';
			$izin = $this->db->select("a.*, (CASE WHEN start_date is not null then start_date else end_date end) as ordere")->where('id', $id_)->get($tabel.' a')->row_array();

			$data["id_"] = $izin["id"];
			$data["no_pokok"] = $izin["np_karyawan"];
			$data["nama_pegawai"] = $izin["nama"];

            if($izin["start_date"]!=null)
                $data["start_date"] = tanggal_indonesia($izin["start_date"]).' '.$izin["start_time"];
            else
                $data["start_date"] = '';
            
            if($izin["start_date_input"]!=null)
                $data["start_date_input"] = tanggal_indonesia(date('Y-m-d', strtotime($izin["start_date_input"]))).' '.date('H:i:s', strtotime($izin["start_date_input"]));
            else
                $data["start_date_input"] = '';
            
			$data["end_date"] = tanggal_indonesia($izin["end_date"]).' '.$izin["end_time"];
			$data["end_date_input"] = tanggal_indonesia(date('Y-m-d', strtotime($izin["end_date_input"]))).' '.date('H:i:s', strtotime($izin["end_date_input"]));
			$data["tgl"] = $izin["created_at"];
			$data["date"] = $izin["ordere"];
			$data["kode_pamlek"] = $izin["kode_pamlek"];
            
            # tambahan untuk alasan, 2021-03-10
            $data["alasan"] = $izin['alasan'];

			$approval_1		= trim($izin['approval_1_np']);
			$approval_2		= trim($izin['approval_2_np']);	
			$status_1		= trim($izin['approval_1_status']);
			$status_2		= trim($izin['approval_2_status']);
			$approval_1_date= trim($izin['approval_1_updated_at']);
			$approval_2_date= trim($izin['approval_2_updated_at']);

			if($status_1=='1') {						
				$approval_1_nama 	= $approval_1." | ".nama_karyawan_by_np($approval_1);
				$approval_1_status 	= "Izin Telah Disetujui pada $approval_1_date."; 
			} else if($status_1=='2') {				
				$approval_1_nama 	= $approval_1." | ".nama_karyawan_by_np($approval_1);
				$approval_1_status 	= "Izin TIDAK disetujui pada $approval_1_date."; 
			} else if($status_1=='3') {				
				$approval_1_nama 	= $approval_1." | ".nama_karyawan_by_np($approval_1);
				$approval_1_status 	= "Pengajuan Izin Dibatalkan oleh pemohon pada $approval_1_date."; 
			} else if($status_1==''||$status_1=='0'||$status_1==null) {
				$status_1 = '0';
				$approval_1_nama 	= $approval_1." | ".nama_karyawan_by_np($approval_1);
				$approval_1_status 	= "Izin BELUM disetujui."; 
			}
			
			if($status_2=='1') {
				$approval_2_nama 	= $approval_2." | ".nama_karyawan_by_np($approval_2);
				$approval_2_status 	= "Izin Telah Disetujui pada $approval_2_date."; 
			}else if($status_2=='2') {
				$approval_2_nama 	= $approval_2." | ".nama_karyawan_by_np($approval_2);
				$approval_2_status 	= "Izin TIDAK disetujui pada $approval_2_date."; 
			}else if($status_2==''||$status_2=='0'||$status_2==null) {
				$status_2 = '0';
				$approval_2_nama 	= $approval_2." | ".nama_karyawan_by_np($approval_2);
				$approval_2_status 	= "Izin BELUM disetujui."; 
			}

			$np_batal_apr = trim($izin['np_batal']);
			if ($np_batal_apr!='' && $np_batal_apr!='0' && $np_batal_apr!=null) {
				$data["waktu_batal"] = 'Izin telah dibatalkan keamanan pada '.trim($izin['date_batal']);
				$data["alasan_batal"] = trim($izin['alasan_batal']);
				$data["np_batal"] = $np_batal_apr." | ".nama_karyawan_by_np($np_batal_apr);
			} else {
				$data["waktu_batal"] = '';
				$data["alasan_batal"] = '';
				$data["np_batal"] = '';
			}
			$data["np_batal_apr"] = $np_batal_apr;

			$arr_pos = json_decode($izin["pos"]);
			$pos = $this->db->where_in('id', $arr_pos)->get('mst_pos')->result();
			$data["pos"] = $pos;

			$data["status_1"] = $status_1;
			$data["status_2"] = $status_2;
			$data["approval_1"] = $approval_1;
			$data["approval_2"] = $approval_2;
			$data["status_approval_1_nama"] = $approval_1_nama;
			$data["status_approval_1_status"] = $approval_1_status;
			$data["status_approval_1_keterangan"] = $izin['approval_1_keterangan'];
			$data["status_approval_2_nama"] = $approval_2_nama;
			$data["status_approval_2_status"] = $approval_2_status;
			$data["status_approval_2_keterangan"] = $izin['approval_2_keterangan'];
			$data["pengamanan"] = ($izin["approval_pengamanan_posisi"]==null) ? array() : json_decode($izin["approval_pengamanan_posisi"]);
			$data["judul"] = ucwords(preg_replace("/_/"," ",__CLASS__));
			$this->load->view($this->folder_view."detail",$data);
		}
		
		public function view_approve()
		{			
			$id_ = $this->input->post('id_perizinan');
			$tgl = $this->input->post('tgl');
			$bulan = substr($tgl, 0, 4).'_'.substr($tgl, 5, 2);
			// $tabel = 'ess_perizinan_'.$bulan;
			$tabel = 'ess_request_perizinan';
			$izin = $this->db->select("a.*, (CASE WHEN start_date is not null then start_date else end_date end) as ordere")->where('id', $id_)->get($tabel.' a')->row_array();

			$data["id_"] = $izin["id"];
			$data["no_pokok"] = $izin["np_karyawan"];
			$data["nama_pegawai"] = $izin["nama"];
            
            # tambahan untuk alasan, 2021-03-10
            $data["alasan"] = $izin['alasan'];
            
            if($izin["start_date"]!=null)
                $data["start_date"] = tanggal_indonesia($izin["start_date"]).' '.$izin["start_time"];
            else
                $data["start_date"] = '';
            
            if($izin["start_date_input"]!=null)
                $data["start_date_input"] = tanggal_indonesia(date('Y-m-d', strtotime($izin["start_date_input"]))).' '.date('H:i:s', strtotime($izin["start_date_input"]));
            else
                $data["start_date_input"] = '';
            
			$data["end_date"] = tanggal_indonesia($izin["end_date"]).' '.$izin["end_time"];
			$data["end_date_input"] = tanggal_indonesia(date('Y-m-d', strtotime($izin["end_date_input"]))).' '.date('H:i:s', strtotime($izin["end_date_input"]));

			$data["tgl"] = $izin["created_at"];
			$data["date"] = $izin["ordere"];
			$data["kode_pamlek"] = $izin["kode_pamlek"];

			$approval_1		= trim($izin['approval_1_np']);
			$approval_2		= trim($izin['approval_2_np']);	
			$status_1		= trim($izin['approval_1_status']);
			$status_2		= trim($izin['approval_2_status']);
			$approval_1_date= trim($izin['approval_1_updated_at']);
			$approval_2_date= trim($izin['approval_2_updated_at']);

			if($status_1=='1') {						
				$approval_1_nama 	= $approval_1." | ".nama_karyawan_by_np($approval_1);
				$approval_1_status 	= "Izin Telah Disetujui pada $approval_1_date."; 
			}else if($status_1=='2') {				
				$approval_1_nama 	= $approval_1." | ".nama_karyawan_by_np($approval_1);
				$approval_1_status 	= "Izin TIDAK disetujui pada $approval_1_date."; 
			}else if($status_1=='3') {				
				$approval_1_nama 	= $approval_1." | ".nama_karyawan_by_np($approval_1);
				$approval_1_status 	= "Pengajuan Izin Dibatalkan oleh pemohon pada $approval_1_date."; 
			}else if($status_1==''||$status_1=='0'||$status_1==null) {
				$status_1 = '0';
				$approval_1_nama 	= $approval_1." | ".nama_karyawan_by_np($approval_1);
				$approval_1_status 	= "Izin BELUM disetujui."; 
			}
			
			if($status_2=='1') {
				$approval_2_nama 	= $approval_2." | ".nama_karyawan_by_np($approval_2);
				$approval_2_status 	= "Izin Telah Disetujui pada $approval_2_date."; 
			}else if($status_2=='2') {
				$approval_2_nama 	= $approval_2." | ".nama_karyawan_by_np($approval_2);
				$approval_2_status 	= "Izin TIDAK disetujui pada $approval_2_date."; 
			}else if($status_2==''||$status_2=='0'||$status_2==null) {
				$status_2 = '0';
				$approval_2_nama 	= $approval_2." | ".nama_karyawan_by_np($approval_2);
				$approval_2_status 	= "Izin BELUM disetujui."; 
			}

			$np_batal_apr = trim($izin['np_batal']);
			if ($np_batal_apr!='' && $np_batal_apr!='0' && $np_batal_apr!=null) {
				$data["waktu_batal"] = 'Izin telah dibatalkan keamanan pada '.trim($izin['date_batal']);
				$data["alasan_batal"] = trim($izin['alasan_batal']);
				$data["np_batal"] = $np_batal_apr." | ".nama_karyawan_by_np($np_batal_apr);
			} else {
				$data["waktu_batal"] = '';
				$data["alasan_batal"] = '';
				$data["np_batal"] = '';
			}
			$data["np_batal_apr"] = $np_batal_apr;

			$arr_pos = json_decode($izin["pos"]);
			$pos = $this->db->where('status', '1')->where("find_in_set('".$_SESSION['no_pokok']."', no_pokok)")->get('mst_pos')->result();
			$data["pos"] = $pos;
			// ->where_in('id', $arr_pos)

			$data["status_1"] = $status_1;
			$data["status_2"] = $status_2;
			$data["approval_1"] = $approval_1;
			$data["approval_2"] = $approval_2;
			$data["status_approval_1_nama"] = $approval_1_nama;
			$data["status_approval_1_status"] = $approval_1_status;
			$data["status_approval_1_keterangan"] = $izin['approval_1_keterangan'];
			$data["status_approval_2_nama"] = $approval_2_nama;
			$data["status_approval_2_status"] = $approval_2_status;
			$data["status_approval_2_keterangan"] = $izin['approval_2_keterangan'];
			$data["pengamanan"] = ($izin["approval_pengamanan_posisi"]==null) ? array() : json_decode($izin["approval_pengamanan_posisi"]);
			$data["judul"] = ucwords(preg_replace("/_/"," ",__CLASS__));

			$data["date_range"] = $this->input->post("date_range");
			//$data["bulan_tahun"] = $this->input->post("bulan_tahun");
			$data["bulan_tahun"] = date('m_Y', strtotime($tgl));
			$data["get_pos"] = $this->input->post("get_pos");
			$data["izin_0"] = $this->input->post("izin_0");
			$data["izin_C"] = $this->input->post("izin_C");
			$data["izin_E"] = $this->input->post("izin_E");
			$data["izin_F"] = $this->input->post("izin_F");
			$data["izin_G"] = $this->input->post("izin_G");
			$data["izin_H"] = $this->input->post("izin_H");
			$data["izin_TM"] = $this->input->post("izin_TM");
			$data["izin_TK"] = $this->input->post("izin_TK");

			$this->load->view($this->folder_view."approve_keamanan",$data);
		}
		
		public function save_approve($jenis=null) {
			$simpan = array();
			$set_date_real = array();
			$id_ = $this->input->post('id_perizinan');
			$tgl = $this->input->post('tgl');
			$bulan = substr($tgl, 0, 4).'_'.substr($tgl, 5, 2);
			$tabel_bulan = 'ess_perizinan_'.$bulan;
			$tabel = 'ess_request_perizinan';

			$izin = $this->db->where('id', $id_)->get($tabel);
			if ($jenis=='batal') {
				if ($izin->num_rows() == 1) {

					$set['alasan_batal'] = $this->input->post('alasan_batal', true);
					$set['np_batal'] = $_SESSION['no_pokok'];
					$set['date_batal'] = date('Y-m-d H:i:s');
					$this->db->set($set)->where('id', $id_)->update('ess_request_perizinan');
					if ($izin->row()->id_perizinan!=null) {
						$this->db->where('id', $izin->row()->id_perizinan)->delete($tabel_bulan);
						$cek_is_cico = $this->db->where('find_in_set('.$izin->row()->id_perizinan.', id_perizinan)')->get($tabel_cico);
						if ($cek_is_cico->num_rows()) {
							$get_cico = $cek_is_cico->row();
							$array_id_cico = explode(',', $get_cico->id_perizinan);
							$index = array_search($izin->row()->id_perizinan, $array_id_cico);
							unset($array_id_cico[$index]);
							$set_id_cico = implode(',', $array_id_cico);
							$this->db->where('id', $get_cico->id)->set('id_perizinan', $set_id_cico)->update($tabel_cico);
						}
					}

					$this->session->set_flashdata('success', 'Berhasil Membatalkan Perizinan');
				} else {
					$this->session->set_flashdata('warning', 'Data perizinan tidak valid!');
				}
			} else {
				if ($izin->num_rows() == 1) {
					$pengamanan = $izin->row()->approval_pengamanan_posisi;
					if ($pengamanan!=null) {
						$get_posisi =  json_decode($pengamanan);
						foreach ($get_posisi as $val) {
							$simpan[] = $val;
							if ($val->status=="1")
								$set_date_real[] = $val;
						}
					}

					$set['pos'] = $this->input->post('pos');
					$set['nama_pos'] = $this->db->where('id', $this->input->post('pos'))->get('mst_pos')->row()->nama;
					$set['waktu'] = $this->input->post('waktu');
					$set['posisi'] = $this->input->post('posisi');
					// $set['np_approver'] = $this->input->post('np_approver');
					$set['np_approver'] = $_SESSION['no_pokok'];
					$set['nama_approver'] = nama_karyawan_by_np($set['np_approver']);
                    
                    # tambahan atribut untuk keperluan edit/hapus, 2021-03-08
					$set['created'] = date('Ymd-His');
					$set['status'] = '1';
                    # tambahan atribut untuk keperluan edit/hapus, 2021-03-08
                    
					$simpan[] = $set;
					$save['approval_pengamanan_posisi'] = json_encode($simpan);
					$save['approval_pengamanan_np'] = $set['np_approver'];
					$set_date_real[] = $set;
					$set_date_realisasi = json_encode($set_date_real);

					$get_date = array_column(json_decode($set_date_realisasi, true), 'waktu');
					sort($get_date);
					$jml_date = count($get_date);
                    
                    # untuk sidt start date=null and start time=null, 2021-03-04 bowo/heru
                    if( $izin->row()->kode_pamlek=='0' ){
                        $start_date = null;
                        $start_time = null;
                        
                        $end_date = date('Y-m-d', strtotime($get_date[($jml_date-1)]));
                        $end_time = date('H:i:s', strtotime($get_date[($jml_date-1)]));
                        
                        $save['start_date'] = $start_date;
                        $save['start_time'] = $start_time;
                        $save['end_date'] = $end_date;
                        $save['end_time'] = $end_time;

                        $save_bln['start_date'] = $start_date;
                        $save_bln['start_time'] = $start_time;
                        $save_bln['end_date'] = $end_date;
                        $save_bln['end_time'] = $end_time;
                    } else{
                        if ($jml_date>0) {
			                $start_date = date('Y-m-d', strtotime($get_date[0]));
			                $start_time = date('H:i:s', strtotime($get_date[0]));
			                $end_date = date('Y-m-d', strtotime($get_date[($jml_date-1)]));
			                $end_time = date('H:i:s', strtotime($get_date[($jml_date-1)]));
			            } else {
			            	$start_date = date('Y-m-d', strtotime($izin->row()->start_date_input));
			                $start_time = date('H:i:s', strtotime($izin->row()->start_date_input));
			                $end_date = date('Y-m-d', strtotime($izin->row()->end_date_input));
			                $end_time = date('H:i:s', strtotime($izin->row()->end_date_input));
			            }

		                if ($jml_date>1 || $jml_date==0) {
		                    $save['start_date'] = $start_date;
		                    $save['start_time'] = $start_time;
		                    $save['end_date'] = $end_date;
		                    $save['end_time'] = $end_time;

		                    $save_bln['start_date'] = $start_date;
		                    $save_bln['start_time'] = $start_time;
		                    $save_bln['end_date'] = $end_date;
		                    $save_bln['end_time'] = $end_time;
		                } else if ($jml_date==1) {
		                    $save['start_date'] = $start_date;
		                    $save['start_time'] = $start_time;
		                    $save_bln['start_date'] = $start_date;
		                    $save_bln['start_time'] = $start_time;

                            $end_date_realisasi = date('Y-m-d H:i:s', strtotime($get_date[($jml_date-1)]));
		                    if ($end_date_realisasi > $izin->row()->end_date_input) {
		                        $save['end_date'] = null;
		                        $save['end_time'] = null;
		                        $save_bln['end_date'] = null;
		                        $save_bln['end_time'] = null;
		                    } else {
			                	$end_date = date('Y-m-d', strtotime($izin->row()->end_date_input));
			                	$end_time = date('H:i:s', strtotime($izin->row()->end_date_input));

		                    	$save['end_date'] = $end_date;
		                    	$save['end_time'] = $end_time;
		                    	$save_bln['end_date'] = $end_date;
		                    	$save_bln['end_time'] = $end_time;
		                    }
		                }
                    }
                    
					// $this->db->where('id', $id_)->set($save)->update($tabel);
                    if( $izin->row()->id_perizinan!=null ){
						$this->db->where('id', $izin->row()->id_perizinan)->set($save_bln)->update($tabel_bulan);
                    }
					$this->db->where('id', $id_)->set($save)->update('ess_request_perizinan');

					if ($this->db->affected_rows() > 0) {
						$this->session->set_flashdata('success', 'Berhasil Memberikan Approval');
					}
					else {
						$this->session->set_flashdata('warning', 'Gagal Memberikan Approval! Cek Koneksi Anda.');
					}
				} else {
					$this->session->set_flashdata('warning', 'Data perizinan tidak valid!');
				}
			}
			
			$this->session->set_flashdata("date_range", $this->input->post("date_range"));
			$this->session->set_flashdata("bulan_tahun", $tabel_bulan);
			$this->session->set_flashdata("get_pos", $this->input->post("get_pos"));
			$this->session->set_flashdata("izin_0", $this->input->post("izin_0"));
			$this->session->set_flashdata("izin_C", $this->input->post("izin_C"));
			$this->session->set_flashdata("izin_E", $this->input->post("izin_E"));
			$this->session->set_flashdata("izin_F", $this->input->post("izin_F"));
			$this->session->set_flashdata("izin_G", $this->input->post("izin_G"));
			$this->session->set_flashdata("izin_H", $this->input->post("izin_H"));
			$this->session->set_flashdata("izin_TM", $this->input->post("izin_TM"));
			$this->session->set_flashdata("izin_TK", $this->input->post("izin_TK"));

			// var_dump($this->input->post());exit;
			
			redirect(site_url($this->folder_controller.'persetujuan_keamanan'));
		}
		
        function process_update_cico($data_lempar){
            //$data_cico = $get_cico->row();
            $str_fix = '';
            $new_element = [];

            //str awal diambil dari id_perizinan di cico
            $str_awal = $data_lempar['id_perizinan'];
            //convert str_awal to array_awal
            $arr_awal = explode(',', $str_awal);

            //concat dari id tabel perizinan
            $str_datang = $data_lempar['id_row_baru'];
            //convert str_datang to array_datang
            $arr_datang = explode(',', $str_datang);

            //found elements of arr_datang where not in arr_awal
            $new_elements=array_diff($arr_datang, $arr_awal);

            foreach($new_elements as $value){
                //push new element to arr_awal
                $arr_awal[] = $value;
            }

            //convert arr_awal to str
            $str_awal = implode(',', $arr_awal);
            $str_fix = trim($str_awal,',');
            
            $this->db->where('id', $data_lempar['id'])->update('ess_cico_'.$data_lempar['tahun_bulan'], ['id_perizinan'=>$str_fix]);
        }

        public function cetak() {
			$this->load->library('phpexcel'); 
			$this->load->model($this->folder_model."M_tabel_persetujuan_keamanan");
			$set['np_karyawan'] = $this->input->post('np_karyawan');
			$jenis = array();
			$filter_bulan   = $this->input->post('bulan');
            if($this->input->post('izin_D')==1)
                $jenis[] = 'D';
            if($this->input->post('izin_E')==1)
                $jenis[] = 'E';
            if($this->input->post('izin_F')==1)
                $jenis[] = 'F';
            if($this->input->post('izin_G')==1)
                $jenis[] = 'G';
            if($this->input->post('izin_H')==1)
                $jenis[] = 'H';
            if($this->input->post('izin_TM')==1)
                $jenis[] = 'TM';
            if($this->input->post('izin_TK')==1)
                $jenis[] = 'TK';
            if($this->input->post('izin_0')==1)
                $jenis[] = '0';

            $target_table   = $filter_bulan;
			if($_SESSION["grup"]==4) { //jika Pengadministrasi Unit Kerja
				$var = array();
				$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
				foreach ($list_pengadministrasi as $data) { //looping list_pengadministrasi
					array_push($var,$data['kode_unit']);							
				}
			} 
			else if($_SESSION["grup"]==5) //jika Pengguna
				$var = $_SESSION["no_pokok"];							
			else
				$var = 1;
			$get_data = $this->M_tabel_persetujuan_keamanan->_get_excel($var,$target_table,$set,@$jenis);

	        error_reporting(E_ALL);
	        ini_set('display_errors', TRUE);
	        ini_set('display_startup_errors', TRUE);

	        header("Content-type: application/vnd.ms-excel");
	        header("Content-Disposition: attachment; filename=persetujuan_keamanan.xlsx");
	        header('Cache-Control: max-age=0');

	        $excel = PHPExcel_IOFactory::createReader('Excel2007');
	        $excel = $excel->load('./asset/Template_persetujuan_keamanan.xlsx');

	        $excel->setActiveSheetIndex(0);
	        $kolom 	= 2;
	        $awal 	= 4;
	        $no = 1;
			
			foreach ($get_data as $tampil) {
                $absence_type = $tampil->kode_pamlek.'|'.$tampil->info_type.'|'.$tampil->absence_type;
				$excel->getActiveSheet()->setCellValueExplicit('A'.$awal, $no++, PHPExcel_Cell_DataType::TYPE_STRING);
	            $excel->getActiveSheet()->setCellValueExplicit('B'.$awal, strtoupper($tampil->np_karyawan), PHPExcel_Cell_DataType::TYPE_STRING);
	            $excel->getActiveSheet()->setCellValueExplicit('C'.$awal, ucwords($tampil->nama), PHPExcel_Cell_DataType::TYPE_STRING);
	            $excel->getActiveSheet()->setCellValueExplicit('D'.$awal, get_perizinan_name($tampil->kode_pamlek)->nama, PHPExcel_Cell_DataType::TYPE_STRING);
				
				if($tampil->start_date)
					$excel->getActiveSheet()->setCellValueExplicit('E'.$awal, tanggal_indonesia($tampil->start_date).' '.$tampil->start_time, PHPExcel_Cell_DataType::TYPE_STRING);
				else
					$excel->getActiveSheet()->setCellValueExplicit('E'.$awal, '', PHPExcel_Cell_DataType::TYPE_STRING);
				
				if($tampil->end_date)
					$excel->getActiveSheet()->setCellValueExplicit('F'.$awal, tanggal_indonesia($tampil->end_date).' '.$tampil->end_time, PHPExcel_Cell_DataType::TYPE_STRING);
				else
					$excel->getActiveSheet()->setCellValueExplicit('F'.$awal, '', PHPExcel_Cell_DataType::TYPE_STRING);
				$awal += 1;	
			}

	        $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
	        $objWriter->setIncludeCharts(TRUE);
	        $objWriter->setPreCalculateFormulas(TRUE);
	        PHPExcel_Calculation::getInstance($excel)->clearCalculationCache();
	        $objWriter->save('php://output');
	        exit();
		}
        
        function update_approval() {
			$response = [];
            $id_perizinan = $this->input->post('id_perizinan',true);
            $tgl = $this->input->post('tgl',true);
            $bulan = date('Y_m', strtotime($tgl));
            $tabel_bulan = 'ess_perizinan_'.$bulan;
            $tabel = 'ess_request_perizinan';
            
            $created = $this->input->post('created',true);
            $pos = $this->input->post('pos',true);
            $nama_pos = $this->input->post('nama_pos',true);
            $posisi = $this->input->post('posisi',true);
            $waktu = $this->input->post('waktu',true);
            
            $izin = $this->db->where('id', $id_perizinan)->get($tabel)->row();
            $kode_pamlek = $izin->kode_pamlek;
            $pengamanan_posisi = json_decode($izin->approval_pengamanan_posisi);
            $response['status'] = true;
            $response['message'] = 'Telah diupdate';
            //$response['pengamanan_posisi'] = $pengamanan_posisi;
            
            $new_pengamanan_posisi = [];
            $new_text = '';
            $no=1;
            $start_date_real = $pengamanan_posisi[0]->waktu;
            $end_date_real = $pengamanan_posisi[0]->waktu;
            foreach( $pengamanan_posisi as $row ){
                if( $row->created==$created ){
                    $row->pos = $pos;
                    $row->nama_pos = $nama_pos;
                    $row->posisi = $posisi;
                    $row->waktu = $waktu;
                }
                
                $new_text .= '<strong>';
                if( $row->status=='1' )
                    $new_text .= '<a>'.$no.'. '.$row->nama_pos.' | '.$row->nama_approver.' ('.$row->np_approver.') | '.ucwords($row->posisi).' Pada '.$row->waktu.'</a>';
                else
                    $new_text .= '<a><strike>'.$no.'. '.$row->nama_pos.' | '.$row->nama_approver.' ('.$row->np_approver.') | '.ucwords($row->posisi).' Pada '.$row->waktu.'</strike></a>';
                $new_text .= '
                            &nbsp;&nbsp;<a title="Edit" data-no="'.$no.'" data-created="'.$row->created.'" data-pos="'.$row->pos.'" data-waktu="'.$row->waktu.'" data-posisi="'.$row->posisi.'" onclick="edit_approval(this)"><i class="fa fa-pencil"></i></a>';
                if($row->status=='1'){
                $new_text .= '&nbsp;&nbsp;<a title="Hapus" data-created="'.$row->created.'" onclick="hapus_approval(this)"><i class="fa fa-trash"></i></a>';
                }
                        $new_text .= '</strong>
                        <br>';
                
                if($row->status=='1'){
                    if( date('Y-m-d H:i', strtotime($row->waktu)) < date('Y-m-d H:i', strtotime($start_date_real)) )
                        $start_date_real = $row->waktu;
                    if( date('Y-m-d H:i', strtotime($row->waktu)) > date('Y-m-d H:i', strtotime($end_date_real)) )
                        $end_date_real = $row->waktu;
                }
                
                $new_pengamanan_posisi[] = $row;
                $no++;
            }
            $response['new_pengamanan_posisi'] = $new_pengamanan_posisi;
            $response['new_text'] = $new_text;
            $response['kode_pamlek'] = $kode_pamlek;
            $response['start_date_real'] = $start_date_real;
            $response['end_date_real'] = $end_date_real;
            $this->db->where('id', $id_perizinan)->update($tabel, ['approval_pengamanan_posisi'=>json_encode($new_pengamanan_posisi)]);
            
            $set_date_real = array();
            $izin = $this->db->where('id', $id_perizinan)->get($tabel)->row();
            $kode_pamlek = $izin->kode_pamlek;
            $pengamanan_posisi = json_decode($izin->approval_pengamanan_posisi);
			foreach ($pengamanan_posisi as $val) {
				if ($val->status=="1")
					$set_date_real[] = $val;
			}

			$set_date_realisasi = json_encode($set_date_real);

			$get_date = array_column(json_decode($set_date_realisasi, true), 'waktu');
			sort($get_date);
			$jml_date = count($get_date);
            
            # untuk sidt start date=null and start time=null, 2021-03-04 bowo/heru
            if( $kode_pamlek=='0' ) {
                $start_date = null;
                $start_time = null;
                
                $end_date = date('Y-m-d', strtotime($get_date[($jml_date-1)]));
                $end_time = date('H:i:s', strtotime($get_date[($jml_date-1)]));
                
                $save['start_date'] = $start_date;
                $save['start_time'] = $start_time;
                $save['end_date'] = $end_date;
                $save['end_time'] = $end_time;

                $save_bln['start_date'] = $start_date;
                $save_bln['start_time'] = $start_time;
                $save_bln['end_date'] = $end_date;
                $save_bln['end_time'] = $end_time;
            } else {
                if ($jml_date>0) {
	                $start_date = date('Y-m-d', strtotime($get_date[0]));
	                $start_time = date('H:i:s', strtotime($get_date[0]));
	                $end_date = date('Y-m-d', strtotime($get_date[($jml_date-1)]));
	                $end_time = date('H:i:s', strtotime($get_date[($jml_date-1)]));
	            } else {
	            	$start_date = date('Y-m-d', strtotime($izin->start_date_input));
	                $start_time = date('H:i:s', strtotime($izin->start_date_input));
	                $end_date = date('Y-m-d', strtotime($izin->end_date_input));
	                $end_time = date('H:i:s', strtotime($izin->end_date_input));
	            }

                if ($jml_date>1 || $jml_date==0) {
                    $save['start_date'] = $start_date;
                    $save['start_time'] = $start_time;
                    $save['end_date'] = $end_date;
                    $save['end_time'] = $end_time;

                    $save_bln['start_date'] = $start_date;
                    $save_bln['start_time'] = $start_time;
                    $save_bln['end_date'] = $end_date;
                    $save_bln['end_time'] = $end_time;
                } else if ($jml_date==1) {
                    $save['start_date'] = $start_date;
                    $save['start_time'] = $start_time;
                    $save_bln['start_date'] = $start_date;
                    $save_bln['start_time'] = $start_time;
					
					$end_date_realisasi = date('Y-m-d H:i:s', strtotime($get_date[($jml_date-1)]));
                    if ($end_date_realisasi > $izin->end_date_input) {
                        $save['end_date'] = null;
                        $save['end_time'] = null;
                        $save_bln['end_date'] = null;
                        $save_bln['end_time'] = null;
                    } else {
	                	$end_date = date('Y-m-d', strtotime($izin->end_date_input));
	                	$end_time = date('H:i:s', strtotime($izin->end_date_input));

                    	$save['end_date'] = $end_date;
                    	$save['end_time'] = $end_time;
                    	$save_bln['end_date'] = $end_date;
                    	$save_bln['end_time'] = $end_time;
                    }
                }
            }

            if( $izin->id_perizinan!=null ){
				$this->db->where('id', $izin->id_perizinan)->set($save_bln)->update($tabel_bulan);
            }
			$this->db->where('id', $izin->id)->set($save)->update('ess_request_perizinan');
            
            $response['start_date_realisasi'] = tanggal_indonesia($save_bln["start_date"]).' '.$save_bln["start_time"];
            $response['end_date_realisasi'] = tanggal_indonesia($save_bln["end_date"]).' '.$save_bln["end_time"];
            echo json_encode($response);
		}
        
        function hapus_approval(){
            $response = [];
            $id_perizinan = $this->input->post('id_perizinan',true);
            $tgl = $this->input->post('tgl',true);
            $created = $this->input->post('created',true);
            $bulan = date('Y_m', strtotime($tgl));
            $tabel_bulan = 'ess_perizinan_'.$bulan;
            $tabel = 'ess_request_perizinan';
            
            $izin = $this->db->where('id', $id_perizinan)->get($tabel)->row();
            $kode_pamlek = $izin->kode_pamlek;
            $pengamanan_posisi = json_decode($izin->approval_pengamanan_posisi);
            $response['status'] = true;
            $response['message'] = 'Telah dihapus';
            
            $new_pengamanan_posisi = [];
            $new_text = '';
            $no=1;
            $start_date_real = $pengamanan_posisi[0]->waktu;
            $end_date_real = $pengamanan_posisi[0]->waktu;
            foreach( $pengamanan_posisi as $row ){
                if( $row->created==$created ){
                    $row->status = '0';
                }
                
                $new_text .= '<strong>';
                if( $row->status=='1' )
                    $new_text .= '<a>'.$no.'. '.$row->nama_pos.' | '.$row->nama_approver.' ('.$row->np_approver.') | '.ucwords($row->posisi).' Pada '.$row->waktu.'</a>';
                else
                    $new_text .= '<a><strike>'.$no.'. '.$row->nama_pos.' | '.$row->nama_approver.' ('.$row->np_approver.') | '.ucwords($row->posisi).' Pada '.$row->waktu.'</strike></a>';
                if($row->status=='1'){
	                $new_text .= '&nbsp;&nbsp;<a title="Edit" data-no="'.$no.'" data-created="'.$row->created.'" data-pos="'.$row->pos.'" data-waktu="'.$row->waktu.'" data-posisi="'.$row->posisi.'" onclick="edit_approval(this)"><i class="fa fa-pencil"></i></a>';
	                $new_text .= '&nbsp;&nbsp;<a title="Hapus" data-created="'.$row->created.'" onclick="hapus_approval(this)"><i class="fa fa-trash"></i></a>';
                }
                        $new_text .= '</strong>
                        <br>';
                if($row->status=='1'){
                    if( date('Y-m-d H:i', strtotime($row->waktu)) < date('Y-m-d H:i', strtotime($start_date_real)) )
                        $start_date_real = $row->waktu;
                    if( date('Y-m-d H:i', strtotime($row->waktu)) > date('Y-m-d H:i', strtotime($end_date_real)) )
                        $end_date_real = $row->waktu;
                }
                
                $new_pengamanan_posisi[] = $row;
                $no++;
            }
            $response['new_pengamanan_posisi'] = $new_pengamanan_posisi;
            $response['new_text'] = $new_text;
            $response['kode_pamlek'] = $kode_pamlek;
            $response['start_date_real'] = $start_date_real;
            $response['end_date_real'] = $end_date_real;
            $this->db->where('id', $id_perizinan)->update($tabel, ['approval_pengamanan_posisi'=>json_encode($new_pengamanan_posisi)]);

            $set_date_real = array();
            $izin = $this->db->where('id', $id_perizinan)->get($tabel)->row();
            $kode_pamlek = $izin->kode_pamlek;
            $pengamanan_posisi = json_decode($izin->approval_pengamanan_posisi);
			foreach ($pengamanan_posisi as $val) {
				if ($val->status=="1")
					$set_date_real[] = $val;
			}

			$set_date_realisasi = json_encode($set_date_real);

			$get_date = array_column(json_decode($set_date_realisasi, true), 'waktu');
			sort($get_date);
			$jml_date = count($get_date);
            
            # untuk sidt start date=null and start time=null, 2021-03-04 bowo/heru
            if( $kode_pamlek=='0' ){
                $start_date = null;
                $start_time = null;
                
                $end_date = date('Y-m-d', strtotime($get_date[($jml_date-1)]));
                $end_time = date('H:i:s', strtotime($get_date[($jml_date-1)]));
                
                $save['start_date'] = $start_date;
                $save['start_time'] = $start_time;
                $save['end_date'] = $end_date;
                $save['end_time'] = $end_time;

                $save_bln['start_date'] = $start_date;
                $save_bln['start_time'] = $start_time;
                $save_bln['end_date'] = $end_date;
                $save_bln['end_time'] = $end_time;
            } else{
                if ($jml_date>0) {
	                $start_date = date('Y-m-d', strtotime($get_date[0]));
	                $start_time = date('H:i:s', strtotime($get_date[0]));
	                $end_date = date('Y-m-d', strtotime($get_date[($jml_date-1)]));
	                $end_time = date('H:i:s', strtotime($get_date[($jml_date-1)]));
	            } else {
	            	$start_date = date('Y-m-d', strtotime($izin->start_date_input));
	                $start_time = date('H:i:s', strtotime($izin->start_date_input));
	                $end_date = date('Y-m-d', strtotime($izin->end_date_input));
	                $end_time = date('H:i:s', strtotime($izin->end_date_input));
	            }

                if ($jml_date>1 || $jml_date==0) {
                    $save['start_date'] = $start_date;
                    $save['start_time'] = $start_time;
                    $save['end_date'] = $end_date;
                    $save['end_time'] = $end_time;

                    $save_bln['start_date'] = $start_date;
                    $save_bln['start_time'] = $start_time;
                    $save_bln['end_date'] = $end_date;
                    $save_bln['end_time'] = $end_time;
                } else if ($jml_date==1) {
                    $save['start_date'] = $start_date;
                    $save['start_time'] = $start_time;
                    $save_bln['start_date'] = $start_date;
                    $save_bln['start_time'] = $start_time;

                    $end_date_realisasi = date('Y-m-d H:i:s', strtotime($get_date[($jml_date-1)]));
                    if ($end_date_realisasi > $izin->end_date_input) {
                        $save['end_date'] = null;
                        $save['end_time'] = null;
                        $save_bln['end_date'] = null;
                        $save_bln['end_time'] = null;
                    } else {
	                	$end_date = date('Y-m-d', strtotime($izin->end_date_input));
	                	$end_time = date('H:i:s', strtotime($izin->end_date_input));

                    	$save['end_date'] = $end_date;
                    	$save['end_time'] = $end_time;
                    	$save_bln['end_date'] = $end_date;
                    	$save_bln['end_time'] = $end_time;
                    }
                }
            }

            if( $izin->id_perizinan!=null ){
				$this->db->where('id', $izin->id_perizinan)->set($save_bln)->update($tabel_bulan);
            }
			$this->db->where('id', $izin->id)->set($save)->update('ess_request_perizinan');
            
            $response['start_date_realisasi'] = tanggal_indonesia($save_bln["start_date"]).' '.$save_bln["start_time"];
            $response['end_date_realisasi'] = tanggal_indonesia($save_bln["end_date"]).' '.$save_bln["end_time"];
            echo json_encode($response);
        }
	}
	
	/* End of file perencanaan_jadwal_kerja.php */
	/* Location: ./application/controllers/kehadiran/perencanaan_jadwal_kerja.php */