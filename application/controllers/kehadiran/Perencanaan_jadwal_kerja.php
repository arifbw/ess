<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class perencanaan_jadwal_kerja extends CI_Controller {
		public function __construct(){
			parent::__construct();
			
			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'kehadiran/';
			$this->folder_model = 'kehadiran/';
			$this->folder_controller = 'kehadiran/';
			
			$this->akses = array();
			
			$this->load->helper("cutoff_helper");
			$this->load->helper("tanggal_helper");
			$this->load->helper("karyawan_helper");
			$this->load->helper("reference_helper");
						
			$this->load->model($this->folder_model."m_perencanaan_jadwal_kerja");
			$this->load->model("lembur/m_pengajuan_lembur");
			$this->load->model("pamlek/m_pamlek_to_ess");
		
        	$this->load->library('phpexcel'); 
		
			$this->data["is_with_sidebar"] = true;
								
			$this->data['judul'] = "Perencanaan Jadwal Kerja";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);
		}
		
		public function index()
		{			
		//	echo __FILE__ . __LINE__;die(var_dump($this->akses));	
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."perencanaan_jadwal_kerja";
			
			//ambil tahun bulan tabel yang tersedia
			$array_tahun_bulan = array();
			$array_mst_jadwal = $this->db->select("dws, dws_variant, description")->from("mst_jadwal_kerja")->order_by("dws")->get()->result_array();
			$query = $this->db->query("select DISTINCT(substr(date,1,7)) AS tahun_bulan from ess_substitution ORDER BY date DESC");
			foreach ($query->result_array() as $data) 
			{					
				if(strlen($data['tahun_bulan'])==7) {
					$pisah = explode('-',$data['tahun_bulan']);
					$tahun = $pisah[0];
					$bulan = $pisah[1];
					$bulan_tahun = $bulan."-".$tahun;
					$array_tahun_bulan[] = $bulan_tahun; 
				}
			}
				
			//ambil mst dws	

			$array_jadwal_kerja 	= $this->m_perencanaan_jadwal_kerja->select_mst_karyawan_aktif();
			$array_daftar_karyawan	= $this->m_perencanaan_jadwal_kerja->select_daftar_karyawan();
			
			$this->data['array_tahun_bulan'] 	= $array_tahun_bulan;	
			$this->data['array_jadwal_kerja'] 	= $array_jadwal_kerja;	
			$this->data['array_mst_jadwal'] 	= $array_mst_jadwal;	
			$this->data['array_daftar_karyawan']= $array_daftar_karyawan;	
			
			$this->load->view('template',$this->data);
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
				$np_list=$this->m_perencanaan_jadwal_kerja->select_np_by_kode_unit($list_kode_unit);						
								
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
			$nama			= nama_karyawan_by_np($np_karyawan);
			
			if ($nama) 
			{				
				echo $nama; 			
			}else			
			{						 
				echo '';
			}			
		}
		
		public function tabel_perencanaan_jadwal_kerja($dws_filter, $tampil_bulan_tahun = null)
		{		
			$this->load->model($this->folder_model."/M_tabel_perencanaan_jadwal_kerja");
			
			//akses ke menu ubah				
			if($this->akses["batal"]) //jika pengguna
			{
				$disabled_batal = '';
			}else
			{
				$disabled_batal = 'disabled';
			}
						
			$bulan = substr($tampil_bulan_tahun,0,2);
			$tahun = substr($tampil_bulan_tahun,3,4);
			
			$arr_bulan_tahun = array();
			$arr_bulan_tahun['bulan'] = $bulan;
			$arr_bulan_tahun['tahun'] = $tahun;
			
			
			
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
				
			$list = $this->M_tabel_perencanaan_jadwal_kerja->get_datatables($var,$arr_bulan_tahun,$dws_filter);
			$data = array();
			$no = $_POST['start'];
			foreach ($list as $tampil) {
				$no++;
				$row = array();
				$row[] = $no;
				$row[] = $tampil->np_karyawan;
				$row[] = $tampil->nama;		
				$row[] = tanggal_indonesia($tampil->date);	
				
				if($tampil->dws_variant=='A')
				{
					$dws_variant='Jumat';
				}else
				{
					$dws_variant='';
				}
				
				
				$row[] = nama_dws_by_kode($tampil->dws)." ". $dws_variant;
				//$row[] = $tampil->dws_variant;
				
				//cutoff ERP
				$sudah_cutoff = sudah_cutoff($tampil->date);
				
				if($sudah_cutoff) //jika sudah lewat masa cutoff
				{
					$row[] = "<button class='btn btn-primary btn-xs batal_button' data-toggle='tooltip' title='Tidak bisa melakukan aksi setelah Cutoff data di ERP pada $sudah_cutoff' disabled>Submit ERP</button>";
				}else
				{
					$dws = get_jadwal_from_dws($tampil->dws);
										
					$tanggal_indonesia = tanggal_indonesia($tampil->date);
					
					$row[] = "<button class='btn btn-primary btn-xs batal_button' data-toggle='modal' data-target='#modal_batal'
						data-id='$tampil->id'
						data-np-karyawan='$tampil->np_karyawan'
						data-nama='$tampil->nama'
						data-date='$tanggal_indonesia'
						data-dws='$dws $dws_variant'
						data-dws-variant='$tampil->dws_variant'					
					$disabled_batal>Batal</button>";
				}
				
				
				
			
				
				$data[] = $row;
			}

			$output = array(
							"draw" => $_POST['draw'],
							"recordsTotal" => $this->M_tabel_perencanaan_jadwal_kerja->count_all($var,$arr_bulan_tahun,$dws_filter),
							"recordsFiltered" => $this->M_tabel_perencanaan_jadwal_kerja->count_filtered($var,$arr_bulan_tahun,$dws_filter),
							"data" => $data,
					);
			//output to json format
			echo json_encode($output);
		}
		
		public function action_insert_perencanaan_jadwal_kerja()
		{			
			$submit = $this->input->post('submit');
										
			if($submit)
			{
				$np_karyawan 		= $this->input->post('insert_np_karyawan');
                
                //echo json_encode($np_karyawan); exit();
				$nama 				= $this->input->post('insert_nama');
				$date 				= date('Y-m-d',strtotime($this->input->post('insert_date_awal')));
				$date_akhir 		= date('Y-m-d',strtotime($this->input->post('insert_date_akhir')));
				$id_dws 			= $this->input->post('insert_dws_id');			
				$tampil_bulan_tahun	= $this->input->post('insert_tampil_bulan_tahun');
				
				$select_dws	= $this->m_perencanaan_jadwal_kerja->select_mst_jadwal_kerja_by_id($id_dws);
				
				$dws			= $select_dws['dws'];
				$dws_variant	= $select_dws['dws_variant'];
				
				$error_exist 	= '';
				$error 			= '';
				$success 		= '';
				
                while (strtotime($date) <= strtotime($date_akhir)) 
                {
                    for($i=0; $i<count($np_karyawan); $i++){
                    $data_insert = [];
                        //validasi data sudah ada
                        $data_exist['np_karyawan'] 		= $np_karyawan[$i];
                        $data_exist['date'] 			= $date;

                        $exist	= $this->m_perencanaan_jadwal_kerja->check_substitution_exist($data_exist);

                        //check data di erp master data / mst_karyawan
                        $erp =  erp_master_data_by_np($np_karyawan[$i], $date);

                        if($exist!=0 || $erp==null)
                        {

                            if($erp==null)
                            {
                                $error_exist = $error_exist."<b>Gagal validasi</b>, Pengajuan Perencanaan Kerja <b>".$np_karyawan[$i]. "|" .$erp['nama']."</b> pada <b>$date</b> belum terdapat di master data karyawan.<br>";	
                            }else
                            {
                                $error_exist = $error_exist."<b>Gagal validasi</b>, Pengajuan Perencanaan Kerja <b>".$np_karyawan[$i]. "|" .$erp['nama']."</b> pada <b>$date</b> sudah tersedia.<br>";
                            }							


                        }else
                        {					
                            //insert
                            $data_insert['np_karyawan'] 	= $np_karyawan[$i];
                            $data_insert['personel_number']	= $erp['personnel_number'];
                            $data_insert['nama'] 			= $erp['nama'];
                            $data_insert['nama_jabatan'] 	= $erp['nama_jabatan'];
                            $data_insert['kode_unit'] 		= $erp['kode_unit'];
                            $data_insert['nama_unit'] 		= $erp['nama_unit'];
                            $data_insert['date'] 			= $date;
                            $data_insert['dws'] 			= $dws;
                            $data_insert['dws_variant'] 	= $dws_variant;
                            $data_insert['transaction_type']= '1';	//perencanaan			
                            
                            $insert = $this->m_perencanaan_jadwal_kerja->insert_perencanaan_jadwal_kerja($data_insert);

                            if(@$insert==0)
                            {
                                $error = $error."<b>Gagal input</b>, Pengajuan Perencanaan Kerja <b>".$np_karyawan[$i] ."|". $erp['nama']."</b> pada <b>$date</b> gagal masuk database.<br>";				
                            }else
                            {
                                $success = $success."<b>Berhasil input</b>, data Perencanaan Jadwal Kerja <b>".$np_karyawan[$i] ."|". $erp['nama']."</b> pada <b>$date</b> telah masuk database.<br>";

                                //===== Log Start =====
                                $arr_data_baru = $this->m_perencanaan_jadwal_kerja->select_substitution_by_id($insert);					
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

                                //refresh ess cico berdasarkan dws yang baru
                                $this->refresh_ess_cico_by_np_date($np_karyawan[$i],$date);
                            }

                        }
                    }
                    
                    $date = date ("Y-m-d", strtotime("+1 day", strtotime($date)));
                }
                //echo json_encode($data_insert); exit();

				if($error_exist!='')
				{
					$this->session->set_flashdata('warning',$error_exist);
				}
				
				if($error!='')
				{
					$this->session->set_flashdata('warning',$error);
				}
				
				if($success!='')
				{
					
					$this->session->set_flashdata('success',$success);
				}

				$this->session->set_flashdata('tampil_bulan_tahun',$tampil_bulan_tahun);
				redirect(base_url($this->folder_controller.'perencanaan_jadwal_kerja/'));				
				
			}else
			{
				$this->session->set_flashdata('warning',"Terjadi Kesalahan");
				
				redirect(base_url($this->folder_controller.'perencanaan_jadwal_kerja/'));	
			}	
		}
		
		public function action_batal_perencanaan_jadwal_kerja()
		{			
			$submit = $this->input->post('submit');
					
					
			if($submit)
			{					
				$id 				= $this->input->post('batal_id');				
				$tampil_bulan_tahun	= $this->input->post('batal_tampil_bulan_tahun');
								
				$bulan	= substr($tampil_bulan_tahun,0,2);
				$tahun 	= substr($tampil_bulan_tahun,3,4);
				$tahun_bulan		= $tahun."_".$bulan;
				
				//===== Log Start =====
				$arr_data_lama = $this->m_perencanaan_jadwal_kerja->select_substitution_by_id($id);
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
				
				$np_karyawan 	= $arr_data_lama['np_karyawan'];
				$nama		 	= nama_karyawan_by_np($np_karyawan);
				$date 			= $arr_data_lama['date'];
				
				$data_batal['id'] 			= $id;
				$data_batal['np_karyawan'] 	= $np_karyawan;
				$data_batal['nama'] 		= $nama;
				$data_batal['date'] 		= $date;
									
				$batal = $this->m_perencanaan_jadwal_kerja->batal_perencanaan_jadwal_kerja($data_batal);
					
				if($batal==0)
				{
					$this->session->set_flashdata('warning',"Update Gagal");
				}else
				{
					$this->session->set_flashdata('success',"<b>Pembatalan Berhasil</b>, <br><b>$np_karyawan | $nama</b> Pada Perencanaan jadwal kerja tanggal <b>$date</b>");
					
					//===== Log Start =====
					$arr_data_baru = $this->m_perencanaan_jadwal_kerja->select_substitution_by_id($id);
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
						"deskripsi" => "update ".strtolower(preg_replace("/_/"," ",__CLASS__)),
						"kondisi_lama" => $log_data_lama,
						"kondisi_baru" => $log_data_baru,
						"alamat_ip" => $this->data["ip_address"],
						"waktu" => date("Y-m-d H:i:s")
					);
					$this->m_log->tambah($log);
					//===== Log end =====
					
					//refresh ess cico berdasarkan dws yang baru
					$this->refresh_ess_cico_by_np_date($np_karyawan,$date);
					
				}	
				
				$this->session->set_flashdata('tampil_bulan_tahun',$tampil_bulan_tahun);
				redirect(base_url($this->folder_controller.'perencanaan_jadwal_kerja/'));				
				
			}else
			{
				$this->session->set_flashdata('warning',"Terjadi Kesalahan");
				
				redirect(base_url($this->folder_controller.'perencanaan_jadwal_kerja/'));	
			}	
		}
		
		public function refresh_ess_cico_by_np_date($np_karyawan,$date)
		{	
			$date_sebelum = date('Y-m-d', strtotime($date . ' -1 day'));
			$date_sesudah = date('Y-m-d', strtotime($date . ' +1 day'));
			
			
			$bisa_proses = true;
			$date_now = date('Y-m-d');
			if($date_sesudah>$date_now)
			{
				$bisa_proses = false;
			}
			
			
			//refresh lembur fix
			$check_sebelum 	= $this->m_pengajuan_lembur->update_dws($np_karyawan, $date_sebelum);
			$check 			= $this->m_pengajuan_lembur->update_dws($np_karyawan, $date);
						
			
			//refresh cico
			$date_sebelum = date('Y-m-d', strtotime($date_sebelum . ' +2 day'));
			$this->inisialisasi($date_sebelum,$date_sebelum,$np_karyawan);
			
			$date = date('Y-m-d', strtotime($date . ' +2 day'));
			$this->inisialisasi($date,$date,$np_karyawan);
			
			if($bisa_proses==true)
			{
				$date_sesudah = date('Y-m-d', strtotime($date_sesudah . ' +2 day'));
				$this->inisialisasi($date_sesudah,$date_sesudah,$np_karyawan);
			
				$check_sesudah 	= $this->m_pengajuan_lembur->update_dws($np_karyawan, $date_sesudah);
			}
			 
			
			
		}
		
		/*
		==================
		Copas dari controller pamlek_to_ess
		==================
		*/
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
				
				/*
				==================
				Kemarin
				==================
				*/
				//pakai tanggal start dws karena dimulainya tgl dws in nya
				$tanggal_proses_kemarin = date('Y-m-d', strtotime($tanggal_proses . ' -1 day'));
				$tahun_bulan_kemarin = substr($tanggal_proses_kemarin,0,7);
				
				$cico_kemarin = $this->m_pamlek_to_ess->select_cico($tahun_bulan_kemarin,$tanggal_proses_kemarin,$np_karyawan);
				$arr_cico_kemarin = array();
				foreach ($cico_kemarin->result_array() as $data) 
				{					
			
					if($data['tapping_fix_1']=='' || $data['tapping_fix_1']==null)
					{
						$tapping_in = $data['tapping_time_1'] ;
					}else						
					{
						$tapping_in = $data['tapping_fix_1'] ;	
					}
					
					if($data['tapping_fix_2']=='' || $data['tapping_fix_2']==null)
					{
						$tapping_out = $data['tapping_time_2'] ;
					}else						
					{
						$tapping_out = $data['tapping_fix_2'] ;	
					}		

					if($data['dws_in_tanggal_fix']=='' || $data['dws_in_tanggal_fix']==null)
					{
						$dws_in_tanggal = $data['dws_in_tanggal'] ;
					}else						
					{
						$dws_in_tanggal = $data['dws_in_tanggal_fix'] ;	
					}
					
					if($data['dws_in_fix']=='' || $data['dws_in_fix']==null)
					{
						$dws_in = $data['dws_in'] ;
					}else						
					{
						$dws_in = $data['dws_in_fix'] ;	
					}
					
					if($data['dws_out_tanggal_fix']=='' || $data['dws_out_tanggal_fix']==null)
					{
						$dws_out_tanggal = $data['dws_out_tanggal'] ;
					}else						
					{
						$dws_out_tanggal = $data['dws_out_tanggal_fix'] ;	
					}
					
					if($data['dws_out_fix']=='' || $data['dws_out_fix']==null)
					{
						$dws_out = $data['dws_out'] ;
					}else						
					{
						$dws_out = $data['dws_out_fix'] ;	
					}
					
					$arr_cico_kemarin = array(
									'np_karyawan'		=> $data['np_karyawan'],					
									'tapping_in'		=> $tapping_in,
									'tapping_out'		=> $tapping_out,
									'dws_name'			=> $data['dws_name'],
									'dws_tanggal'		=> $data['dws_tanggal'],
									'dws_in_tanggal'	=> $dws_in_tanggal,
									'dws_in'			=> $dws_in,
									'dws_out_tanggal'	=> $dws_out_tanggal,
									'dws_out'			=> $dws_out	
								);	
				}
				
				
					$master = $this->m_pamlek_to_ess->select_master_data($tahun_bulan_kemarin,$tanggal_proses_kemarin,$np_karyawan);	
					$arr_master_kemarin = array();					
					foreach ($master->result_array() as $data) 
					{
						//jika ada substitution
						$substitution = $this->m_pamlek_to_ess->get_substitution($np_karyawan,$tanggal_proses_kemarin);
						if($substitution['date'])
						{					
							$lintas_hari_masuk = $substitution['lintas_hari_masuk'];
							$lintas_hari_pulang = $substitution['lintas_hari_pulang'];
							
							if($lintas_hari_masuk==1)
							{
								$sub_tanggal_start_dws= date('Y-m-d', strtotime($tanggal_proses_kemarin . ' +1 day'));
							}else
							{
								$sub_tanggal_start_dws=$tanggal_proses_kemarin;
							}
							
							if($lintas_hari_pulang==1)
							{
								$tanggal_end_dws= date('Y-m-d', strtotime($tanggal_proses_kemarin . ' +1 day'));
							}else
							{
								$tanggal_end_dws=$tanggal_proses_kemarin;
							}
							
								$dws 				= $substitution['dws'];
								$tanggal_start_dws	= $sub_tanggal_start_dws;
								$start_time			= $substitution['start_time'];
								$tanggal_end_dws	= $tanggal_end_dws;
								$end_time			= $substitution['end_time'];
								$start_break		= $substitution['dws_break_start_time'];
								$end_break			= $substitution['dws_break_end_time'];
						}else
						{
							$dws				= $data['dws'];
							$tanggal_start_dws	= $data['tanggal_end_dws'];
							$start_time			= $data['start_time'];						
							$tanggal_end_dws	= $data['tanggal_end_dws'];
							$end_time			= $data['end_time'];
							$start_break		= $data['start_break'];
							$end_break			= $data['end_break'];
						}
						
															
						$arr_master_kemarin = array(
										'np_karyawan'		=> $data['np_karyawan'],	
										'tapping_in'		=> $tanggal_proses_kemarin." ".'00:00:00',
										'tapping_out'		=> $tanggal_proses_kemarin." ".'23:59:59',	
										'dws_name'			=> $dws,
										'dws_tanggal'		=> $data['tanggal_dws'],
										'dws_in_tanggal'	=> $tanggal_start_dws,
										'dws_in'			=> $start_time,
										'dws_out_tanggal'	=> $tanggal_end_dws,
										'dws_out'			=> $end_time
																			
									);											
					}				
					
				
				
				
				/*
				==================
				Besok
				==================
				*/
				//pakai tanggal start dws karena dimulainya tgl dws in nya
				$tanggal_proses_besok = date('Y-m-d', strtotime($tanggal_proses . ' +1 day'));
				$tahun_bulan_besok = substr($tanggal_proses_besok,0,7);
								
				$cico = $this->m_pamlek_to_ess->select_cico($tahun_bulan_besok,$tanggal_proses_besok,$np_karyawan);
				$arr_cico_besok = array();
				foreach ($cico->result_array() as $data) 
				{					
					if($data['tapping_fix_1']=='' || $data['tapping_fix_1']==null)
					{
						$tapping_in = $data['tapping_time_1'] ;
					}else						
					{
						$tapping_in = $data['tapping_fix_1'] ;	
					}
					
					if($data['tapping_fix_2']=='' || $data['tapping_fix_2']==null)
					{
						$tapping_out = $data['tapping_time_2'] ;
					}else						
					{
						$tapping_out = $data['tapping_fix_2'] ;	
					}		

					if($data['dws_in_tanggal_fix']=='' || $data['dws_in_tanggal_fix']==null)
					{
						$dws_in_tanggal = $data['dws_in_tanggal'] ;
					}else						
					{
						$dws_in_tanggal = $data['dws_in_tanggal_fix'] ;	
					}
					
					if($data['dws_in_fix']=='' || $data['dws_in_fix']==null)
					{
						$dws_in = $data['dws_in'] ;
					}else						
					{
						$dws_in = $data['dws_in_fix'] ;	
					}
					
					if($data['dws_out_tanggal_fix']=='' || $data['dws_out_tanggal_fix']==null)
					{
						$dws_out_tanggal = $data['dws_out_tanggal'] ;
					}else						
					{
						$dws_out_tanggal = $data['dws_out_tanggal_fix'] ;	
					}
					
					if($data['dws_out_fix']=='' || $data['dws_out_fix']==null)
					{
						$dws_out = $data['dws_out'] ;
					}else						
					{
						$dws_out = $data['dws_out_fix'] ;	
					}
					
					$arr_cico_besok = array(
									'np_karyawan'		=> $data['np_karyawan'],					
									'tapping_in'		=> $tapping_in,
									'tapping_out'		=> $tapping_out,
									'dws_name'			=> $data['dws_name'],
									'dws_tanggal'		=> $data['dws_tanggal'],
									'dws_in_tanggal'	=> $dws_in_tanggal,
									'dws_in'			=> $dws_in,
									'dws_out_tanggal'	=> $dws_out_tanggal,
									'dws_out'			=> $dws_out	
								);	
				}
				
			
					$master = $this->m_pamlek_to_ess->select_master_data($tahun_bulan_besok,$tanggal_proses_besok,$np_karyawan);	
					$arr_master_besok = array();
					foreach ($master->result_array() as $data) 
					{
						//jika ada substitution
						$substitution = $this->m_pamlek_to_ess->get_substitution($np_karyawan,$tanggal_proses_besok);
						if($substitution['date'])
						{					
							$lintas_hari_masuk = $substitution['lintas_hari_masuk'];
							$lintas_hari_pulang = $substitution['lintas_hari_pulang'];
							
							if($lintas_hari_masuk==1)
							{
								$sub_tanggal_start_dws= date('Y-m-d', strtotime($tanggal_proses_besok . ' +1 day'));
							}else
							{
								$sub_tanggal_start_dws=$tanggal_proses_besok;
							}
							
							if($lintas_hari_pulang==1)
							{
								$tanggal_end_dws= date('Y-m-d', strtotime($tanggal_proses_besok . ' +1 day'));
							}else
							{
								$tanggal_end_dws=$tanggal_proses_besok;
							}
							
								$dws 				= $substitution['dws'];
								$tanggal_start_dws	= $sub_tanggal_start_dws;
								$start_time			= $substitution['start_time'];
								$tanggal_end_dws	= $tanggal_end_dws;
								$end_time			= $substitution['end_time'];
								$start_break		= $substitution['dws_break_start_time'];
								$end_break			= $substitution['dws_break_end_time'];
						}else
						{
							$dws				= $data['dws'];
							$tanggal_start_dws	= $data['tanggal_end_dws'];
							$start_time			= $data['start_time'];
							$tanggal_end_dws	= $data['tanggal_end_dws'];
							$end_time			= $data['end_time'];
							$start_break		= $data['start_break'];
							$end_break			= $data['end_break'];
						}
						
						$arr_master_besok = array(
										'np_karyawan'		=> $data['np_karyawan'],	
										'tapping_in'		=> $tanggal_proses_besok." ".'00:00:00',
										'tapping_out'		=> $tanggal_proses_besok." ".'23:59:59',	
										'dws_name'			=> $dws,
										'dws_tanggal'		=> $data['tanggal_dws'],
										'dws_in_tanggal'	=> $tanggal_start_dws,
										'dws_in'			=> $start_time,
										'dws_out_tanggal'	=> $tanggal_end_dws,
										'dws_out'			=> $end_time
										
									);											
					}	
			
					
							
				
				/*
				==================
				Search Tapping 
				==================
				*/				
				$tanggal_dws		= $sekarang['tanggal_dws'];			
				$tabel_pamlek 		= "pamlek_data_".$tahun_bulan;
				$tabel_kemarin		= "pamlek_data_".$tahun_bulan_kemarin;
				
				//sekarang							
				$dws_in_sekarang		= $sekarang_tanggal_start_dws." ".$sekarang_start_time;
				$dws_out_sekarang		= $sekarang_tanggal_end_dws." ".$sekarang_end_time;
			
				//Libur				
				if($sekarang_tanggal_start_dws==$sekarang_tanggal_end_dws && $sekarang_start_time==$sekarang_end_time)
				{
					$dws_in_sekarang		= $tanggal_proses." ".'00:00:00';
					//$dws_out_sekarang		= $tanggal_proses." ".'23:59:59';
					$dws_out_sekarang		= $tanggal_proses_besok." ".'00:00:00';
				}
				
					
					
					
					
				
				
				//kemarin			
				
				if($arr_master_kemarin==null || $arr_master_kemarin['dws_name']=='OFF')
				{
					//GILIR biar ga terlalu jauh
					//die($tanggal_proses_kemarin." x ".$sekarang_tanggal_end_dws);
					if($tanggal_proses_kemarin<$sekarang_tanggal_end_dws)
					{
						$y = date('Y-m-d', strtotime($sekarang_tanggal_end_dws . ' -2 day'));
						$dws_out_kemarin	= $y." ".'23:59:59';
					}else
					{
						$dws_out_kemarin	= $tanggal_proses_kemarin." ".'12:00:00';					
						
					}
						
					
					
				
				}else
				{
					
					$tanggal_search_kemarin=$arr_master_kemarin['dws_out_tanggal'];
					
				
					$dws_out_kemarin	= $tanggal_search_kemarin." ".$arr_master_kemarin['dws_out'];
				}
				
				
				
						
				if(@$arr_cico_kemarin['tapping_out'])
				{
					$tapping_out_kemarin= $arr_cico_kemarin['tapping_out']; //sudah ada tanggalnya di database
				}else
				{
					//$tapping_out_kemarin= $tanggal_proses_kemarin." ".'12:00:00';
					$tapping_out_kemarin = date('Y-m-d H:i:s',strtotime('-6 hour',strtotime($dws_in_sekarang)));
				}						
				
				
				//besok
				if(@$arr_master_besok['dws_in_tanggal'])
				{
					if( $arr_master_besok['dws_name']!='OFF')
					{
						$dws_in_besok	= $arr_master_besok['dws_in_tanggal']." ".$arr_master_besok['dws_in'];
					}else
					{						
						$dws_in_besok	= $tanggal_proses_besok." ".'00:00:00';
												
						if($dws_out_sekarang>=$dws_in_besok)
						{
							$dws_in_besok =  date('Y-m-d H:i:s',strtotime('+12 hour',strtotime($dws_in_besok)));
							
						}
						
						
						
					}
					
				}else
				{
						$dws_in_besok	= $tanggal_proses_besok." ".'00:00:00';
						
						
						if($dws_out_sekarang>=$dws_in_besok)
						{
							$dws_in_besok =  date('Y-m-d H:i:s',strtotime('+12 hour',strtotime($dws_in_besok)));
						}
												
				}
				
				
				
				//search tapping IN
				$search_tapping_in = $this->m_pamlek_to_ess->search_tapping_in($tanggal_dws,$np_karyawan,$tabel_pamlek,$tabel_kemarin,$dws_in_besok,$dws_out_kemarin,$tapping_out_kemarin,$dws_out_sekarang);
				
				if($search_tapping_in['tapping_time']==null)
				{
					$tapping_in = $tapping_out_kemarin;
				}else
				{
					$tapping_in = $search_tapping_in['tapping_time'];
				}
				
				
				$tabel_pamlek_plus		= "pamlek_data_".$tahun_bulan_besok;
								
				//search tapping OUT
				$search_tapping_out = $this->m_pamlek_to_ess->search_tapping_out($tanggal_dws,$np_karyawan,$tabel_pamlek,$tabel_pamlek_plus,$dws_in_sekarang,$dws_in_besok,$tapping_out_kemarin,$tapping_in);
				
				//masukan data ke array
				$in_out = array(
								'np_karyawan'		=> $np_karyawan,
								'personel_number'	=> $personel_number,
								'nama'				=> $nama_karyawan,
								'kode_unit'			=> $kode_unit,
								'nama_unit'			=> $nama_unit,
								'nama_jabatan'		=> $nama_jabatan,
								
								'dws_tanggal'		=> $tanggal_dws,
								
								'dws_name'			=> $sekarang['dws'],
								'dws_in_tanggal'	=> $sekarang['tanggal_start_dws'],
								'dws_in'			=> $sekarang['start_time'],
								'dws_out_tanggal'	=> $sekarang['tanggal_end_dws'],
								'dws_out'			=> $sekarang['end_time'],
								'dws_break_start'	=> $sekarang['start_break'],
								'dws_break_end'		=> $sekarang['end_break'],								
								
								'dws_name_fix'			=> $sekarang['dws_fix'],
								'dws_in_tanggal_fix'	=> $sekarang['tanggal_start_dws_fix'],
								'dws_in_fix'			=> $sekarang['start_time_fix'],
								'dws_out_tanggal_fix'	=> $sekarang['tanggal_end_dws_fix'],
								'dws_out_fix'			=> $sekarang['end_time_fix'],
								'dws_break_start_fix'	=> $sekarang['start_break_fix'],
								'dws_break_end_fix'		=> $sekarang['end_break_fix'],
								
								'tapping_type_1'	=> $search_tapping_in['tapping_type'],
								'tapping_time_1'	=> $search_tapping_in['tapping_time'],
								'tapping_terminal_1'=> $search_tapping_in['machine_id'],
								'tapping_type_2'	=> $search_tapping_out['tapping_type'],
								'tapping_time_2'	=> $search_tapping_out['tapping_time'],
								'tapping_terminal_2'=> $search_tapping_out['machine_id'],
								
								'action'			=> $sekarang['action'],
								'tm_status'			=> $sekarang['tm_status'],
								
								'proses'			=> '0',
								'waktu_proses'		=> null
							);
							
						
							
							
							
		//		echo "<br>".$np_karyawan;
		//		echo "<br>".$tanggal_dws."<br>";
		//		var_dump($in_out);
		//		echo "<br><br>";		
			
				//update atau insert tabel
				$tabel_cico		= "ess_cico_".$tahun_bulan;
				if(!$this->m_pamlek_to_ess->check_table_exist($tabel_cico))
				{
					$this->m_pamlek_to_ess->create_table_cico($tabel_cico);
				}
				
				$check_cico = $this->m_pamlek_to_ess->check_cico($tabel_cico, $np_karyawan,$tanggal_dws);
				if($check_cico['id']) //jika sudah ada data
				{
					$in_out = array(
								'np_karyawan'		=> $np_karyawan,
								'personel_number'	=> $personel_number,
								'nama'				=> $nama_karyawan,
								'kode_unit'			=> $kode_unit,
								'nama_unit'			=> $nama_unit,
								'nama_jabatan'		=> $nama_jabatan,
								
								'dws_tanggal'		=> $tanggal_dws,
								
								'dws_name'			=> $sekarang['dws'],
								'dws_in_tanggal'	=> $sekarang['tanggal_start_dws'],
								'dws_in'			=> $sekarang['start_time'],
								'dws_out_tanggal'	=> $sekarang['tanggal_end_dws'],
								'dws_out'			=> $sekarang['end_time'],
								'dws_break_start'	=> $sekarang['start_break'],
								'dws_break_end'		=> $sekarang['end_break'],								
								
								'dws_name_fix'			=> $sekarang['dws_fix'],
								'dws_in_tanggal_fix'	=> $sekarang['tanggal_start_dws_fix'],
								'dws_in_fix'			=> $sekarang['start_time_fix'],
								'dws_out_tanggal_fix'	=> $sekarang['tanggal_end_dws_fix'],
								'dws_out_fix'			=> $sekarang['end_time_fix'],
								'dws_break_start_fix'	=> $sekarang['start_break_fix'],
								'dws_break_end_fix'		=> $sekarang['end_break_fix'],
								
								'tapping_type_1'	=> $search_tapping_in['tapping_type'],
								'tapping_time_1'	=> $search_tapping_in['tapping_time'],
								'tapping_terminal_1'=> $search_tapping_in['machine_id'],
								'tapping_type_2'	=> $search_tapping_out['tapping_type'],
								'tapping_time_2'	=> $search_tapping_out['tapping_time'],
								'tapping_terminal_2'=> $search_tapping_out['machine_id'],
								
								'action'			=> $sekarang['action'],
								'tm_status'			=> $sekarang['tm_status'],
															
								'proses'			=> '0',
								'waktu_proses'		=> null
							);
							
					$this->m_pamlek_to_ess->update_cico($tabel_cico, $np_karyawan, $tanggal_dws, $in_out);
				}else //jika belum ada data
				{
					$this->m_pamlek_to_ess->insert_cico($tabel_cico,$in_out);
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
				//refresh lembur fix
				$check = $this->m_pengajuan_lembur->update_dws($np_karyawan, $tanggal_dws);
				
				// UPDATE DATA PERIZINAN DI TABEL, KHUSUS UNTUK RUBAH GILIR ADA INI
					$date_perizinan = date('Y-m-d', strtotime($start_date . ' +2 day'));
					
					$this->inisialisasi_get_data($date_perizinan,$date_perizinan,$np_karyawan);				
				// END OF KHUSUS
				
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
		
	//	echo "<br>===========================================================";
	//	echo "<br>Selesai Proses Pamlek_to_ess ".date("Y-m-d H:i:s");
	//	echo "<br>===========================================================";		


		
			
				
		}
		
	/*
		COPAS DARI pamlek_to_ess_perizinan
	*/	
	
	function inisialisasi_get_data($start_date=null, $end_date=null, $np=null){
		
		$this->load->model('pamlek/M_pamlek_to_ess_perizinan');
		$this->load->helper(['fungsi_helper','tanggal_helper','perizinan_helper']);
		
        $machine = get_machine('string');
        $query_fix = '';
        $array_insert = [];
        
        set_time_limit('0');
        
        if($start_date==null && $end_date==null){
            echo 'Need parameters: Start date (Y-m-d) and End date (Y-m-d)';
        } else{
            /*if((validateDate($start_date)==false || validateDate($end_date)==false) && ($start_date!='today' || $end_date!='today')){
                echo 'Date not valid.';
                exit();
            }*/
            
            if($start_date=='today'){
                $start_date=date('Y-m-d');
            }
			
            if($end_date=='today'){
                $end_date=date('Y-m-d');
            }
			
			$start_date = date('Y-m-d', strtotime($start_date . ' -2 day'));
			$end_date = date('Y-m-d', strtotime($end_date . ' -2 day'));
            
            /*$start_date_min2 = date_minus_days($start_date, 2);
            $end_date_min2 = date_minus_days($end_date, 2);*/
            $bulan_tahun_start = substr(str_replace('-','_',$start_date), 0,7);
            $bulan_tahun_end = substr(str_replace('-','_',$end_date), 0,7);
            if($end_date<$start_date){
                echo 'End date harus lebih besar atau sama dengan Start date';
            } else if($bulan_tahun_start != $bulan_tahun_end){
                echo 'Hanya dalam bulan yang sama.';
            } else{
                if(check_table_exist("erp_master_data_$bulan_tahun_start")=='ada'){
                    $table_kry = "erp_master_data_$bulan_tahun_start";
                    $field_kry = 'np_karyawan';
                } else{
                    $table_kry = 'mst_karyawan';
                    $field_kry = 'no_pokok';
                }
                
                if(check_table_exist("ess_cico_$bulan_tahun_start")=='ada'){
                    $table_cico = "ess_cico_$bulan_tahun_start";
                } else{
                    $table_cico = 'ess_cico';
                }
                
                if(check_table_exist("pamlek_data_$bulan_tahun_start")=='ada'){
                    $table_pamlek = "pamlek_data_$bulan_tahun_start";
                } else{
                    $table_pamlek = 'pamlek_data';
                }
                
                $msc = microtime(true);
                echo '<br>Scanning data...<br>';
                echo 'Start : '.date('Y-m-d H:i:s').'<br><br>';
                $query_string_first = 
                    "SELECT a.no_pokok, b.nama, b.nama_jabatan, b.personnel_number, b.kode_unit, b.nama_unit, a.id, 
                            (CASE WHEN a.tapping_type!='0' THEN CONCAT(a.tapping_time,' ',a.machine_id)
                        /*  Bowo, Untuk Tapping SIDT tidak perlu ada from           
									ELSE (
									SELECT CONCAT(m.dws_in_tanggal,' ',m.dws_in,' ')
                                            FROM $table_cico m WHERE m.np_karyawan=a.no_pokok AND (a.tapping_time BETWEEN (CONCAT(m.dws_in_tanggal,' ',m.dws_in)) AND (CONCAT(m.dws_out_tanggal,' ',m.dws_out)))
						   ) 
						END of Bowo */    
							END
						   ) as date_from,
                            (CASE WHEN a.tapping_type='0' THEN CONCAT(a.tapping_time,' ',a.machine_id)
                                    ELSE (SELECT (CASE WHEN aa.in_out='1' THEN CONCAT(aa.tapping_time,' ',aa.machine_id)
                                                    WHEN aa.in_out='0' THEN CONCAT(DATE_SUB(aa.tapping_time, INTERVAL 1 MINUTE),' ',aa.machine_id)
                                                    ELSE (SELECT CONCAT(bb.dws_out_tanggal,' ',bb.dws_out,' ')
                                                        FROM $table_cico bb WHERE bb.np_karyawan=a.no_pokok AND (a.tapping_time BETWEEN (CONCAT((CASE WHEN bb.dws_in_tanggal_fix IS NOT NULL THEN bb.dws_in_tanggal_fix ELSE bb.dws_in_tanggal END),' ',(CASE WHEN bb.dws_in_fix IS NOT NULL THEN bb.dws_in_fix ELSE bb.dws_in END))) AND (CONCAT((CASE WHEN bb.dws_out_tanggal_fix IS NOT NULL THEN bb.dws_out_tanggal_fix ELSE bb.dws_out_tanggal END),' ',(CASE WHEN bb.dws_out_fix IS NOT NULL THEN bb.dws_out_fix ELSE bb.dws_out END)))))
                                                    END ) 
                                    FROM $table_pamlek aa 
									WHERE aa.no_pokok=a.no_pokok 
									AND DATE_FORMAT(aa.tapping_time,'%Y-%m-%d')=DATE_FORMAT(a.tapping_time,'%Y-%m-%d') 
									/*BOWO Belum tentu saat tapping balik ke kantor pake izin yg sama
									AND aa.tapping_type=a.tapping_type 
									END OF BOWO*/
									AND aa.tapping_time > a.tapping_time ORDER BY aa.tapping_time ASC LIMIT 1)
                             END) as date_to,
                            a.tapping_time, 
                            a.in_out, a.tapping_type, a.machine_id
                    FROM $table_pamlek a
                    LEFT JOIN $table_kry b ON a.no_pokok=b.$field_kry
                    WHERE 
                            (a.tapping_type!='0' OR (a.tapping_type='0' AND a.machine_id in ($machine)))
                            AND (CASE WHEN a.tapping_type!='0' then a.in_out='0' ELSE a.in_out='1' END)";
                    
                    /*"SELECT a.no_pokok, b.nama, b.nama_jabatan, b.personnel_number, b.nama_unit, a.id, 
                        (CASE WHEN a.tapping_type!='0' THEN a.tapping_time 
                            ELSE (SELECT CONCAT(m.dws_in_tanggal,' ',m.dws_in) as get_dws_in 
                                FROM $table_cico m WHERE a.no_pokok=m.np_karyawan AND DATE_FORMAT(a.tapping_time,'%Y-%m-%d')=m.dws_in_tanggal) 
                            END
                        ) as date_from,
                        (case when a.tapping_type='0' THEN a.tapping_time ELSE 
                            (SELECT (CASE WHEN aa.in_out='1' AND aa.no_pokok=a.no_pokok AND aa.tapping_type=a.tapping_type AND DATE_FORMAT(aa.tapping_time,'%Y-%m-%d')=DATE_FORMAT(a.tapping_time,'%Y-%m-%d') AND aa.tapping_time > a.tapping_time THEN aa.tapping_time ELSE 
                                (SELECT (CASE WHEN bb.dws_out_tanggal IS NOT NULL AND bb.dws_out IS NOT NULL THEN CONCAT(bb.dws_out_tanggal,' ',bb.dws_out) ELSE NULL END) as get_dws_out 
                                FROM $table_cico bb WHERE bb.np_karyawan=a.no_pokok AND bb.dws_in_tanggal=DATE_FORMAT(a.tapping_time,'%Y-%m-%d'))
                            END ) 
                            FROM $table_pamlek aa WHERE aa.no_pokok=a.no_pokok AND DATE_FORMAT(aa.tapping_time,'%Y-%m-%d')=DATE_FORMAT(a.tapping_time,'%Y-%m-%d') AND aa.tapping_type=a.tapping_type AND aa.tapping_time > a.tapping_time ORDER BY aa.tapping_time ASC LIMIT 1)
                         END) as date_to,
                        a.tapping_time, 
                        a.in_out, a.tapping_type, a.machine_id
                    FROM $table_pamlek a
                    LEFT JOIN 
                        (SELECT x.no_pokok, x.nama, x.nama_jabatan, x.personnel_number, x.nama_unit FROM mst_karyawan x) b
                        ON a.no_pokok=b.no_pokok
                    WHERE 
                        (a.tapping_type!='0' OR (a.tapping_type='0' AND a.machine_id in ($machine)))
                        and CASE WHEN a.tapping_type!='0' then a.in_out='0' ELSE a.in_out='1' END";*/
                $query_string_last = " ORDER BY a.no_pokok, a.tapping_time";
                
                $query_fix .= $query_string_first;
                $query_fix .= " AND (DATE_FORMAT(a.tapping_time,'%Y-%m-%d') BETWEEN '$start_date' and '$end_date')";
                if(@$np){
                    $query_fix .= " AND a.no_pokok='$np'";
                }
                $query_fix .= " GROUP BY a.id";
                $query_fix .= $query_string_last;
                
                $get = $this->db->query($query_fix);
                //echo json_encode($get->result()); exit();
                //$array_insert = array();
                foreach($get->result() as $row){
                    $insert_start_date = null;
                    $insert_start_time = null;
                    $insert_end_date = null;
                    $insert_end_time = null;
                    $insert_start_machine = null;
                    $insert_end_machine = null;
                    
                    //if(validateDateTime($row->date_from)==true){
                    if($row->date_from!=NULL){
                        $insert_start_date = explode(' ', $row->date_from)[0];
                        $insert_start_time = explode(' ', $row->date_from)[1];
                        $insert_start_machine = explode(' ', $row->date_from)[2];
                    } 
                    
                    //if(validateDateTime($row->date_to)==true){
                    if($row->date_to!=NULL){
                        $insert_end_date = explode(' ', $row->date_to)[0];
                        $insert_end_time = explode(' ', $row->date_to)[1];
                        $insert_end_machine = explode(' ', $row->date_to)[2];
                    } 
                    
                    $this->db->query("CREATE TABLE IF NOT EXISTS ess_perizinan_$bulan_tahun_start LIKE ess_perizinan");
                    if($row->personnel_number!=NULL && trim($row->personnel_number)!=''){
                        if($row->tapping_type=='0'){
                            $tahun_bulan = str_replace('-','_',substr($row->tapping_time,0,7));
                            if(check_table_exist("ess_cico_$tahun_bulan")=='ada'){
                                $cek_row = $this->db->select("(CASE WHEN dws_in_tanggal_fix IS NOT NULL THEN dws_in_tanggal_fix ELSE dws_in_tanggal END) as tanggal_dws_in, (CASE WHEN dws_in_fix IS NOT NULL THEN dws_in_fix ELSE dws_in END) as time_dws_in, (CASE WHEN dws_out_tanggal_fix IS NOT NULL THEN dws_out_tanggal_fix ELSE dws_out_tanggal END) as tanggal_dws_out, (CASE WHEN dws_out_fix IS NOT NULL THEN dws_out_fix ELSE dws_out END) as time_dws_out")->where(['np_karyawan'=>$row->no_pokok, 'dws_tanggal'=>explode(' ',$row->tapping_time)[0]])->get("ess_cico_$tahun_bulan");
                                
                                if($cek_row->num_rows()>0){
                                    $get_cico_dws = $cek_row->row();
                                    //old: if(($row->tapping_time > date('Y-m-d H:i:s', strtotime($get_cico_dws->tanggal_dws_in.' '.$get_cico_dws->time_dws_in))) && ($row->tapping_time < date('Y-m-d H:i:s', strtotime($get_cico_dws->tanggal_dws_out.' '.$get_cico_dws->time_dws_out)))){
                                    if((date('Y-m-d H:i', strtotime($row->tapping_time)) > date('Y-m-d H:i', strtotime($get_cico_dws->tanggal_dws_in.' '.$get_cico_dws->time_dws_in))) && (date('Y-m-d H:i', strtotime($row->tapping_time)) < date('Y-m-d H:i', strtotime($get_cico_dws->tanggal_dws_out.' '.$get_cico_dws->time_dws_out)))){
                                        $array_insert = [
                                            'np_karyawan'=>$row->no_pokok,
                                            'nama'=>$row->nama,
                                            'personel_number'=>$row->personnel_number,
                                            'nama_jabatan'=>$row->nama_jabatan,
                                            'kode_unit'=>$row->kode_unit,
                                            'nama_unit'=>$row->nama_unit,
                                            'info_type'=>convert_pamlek_to_erp($row->tapping_type)['info_type'],
                                            'absence_type'=>convert_pamlek_to_erp($row->tapping_type)['absence_type'],
                                            'kode_pamlek'=>$row->tapping_type,
                                            'start_date'=>$insert_start_date,
                                            'end_date'=>$insert_end_date,
                                            'start_time'=>$insert_start_time,
                                            'end_time'=>$insert_end_time,
                                            'machine_id_start'=>$insert_start_machine,
                                            'machine_id_end'=>$insert_end_machine
                                        ];

                                        $this->M_pamlek_to_ess_perizinan->check_id_then_insert_data($array_insert, "ess_perizinan_$bulan_tahun_start");
                                        //$this->M_pamlek_to_ess_perizinan->update_cico($array_insert);
                                    }
                                }
                                
                            }
                        } else{
                            $array_insert = [
                                'np_karyawan'=>$row->no_pokok,
                                'nama'=>$row->nama,
                                'personel_number'=>$row->personnel_number,
                                'nama_jabatan'=>$row->nama_jabatan,
                                'kode_unit'=>$row->kode_unit,
                                'nama_unit'=>$row->nama_unit,
                                'info_type'=>convert_pamlek_to_erp($row->tapping_type)['info_type'],
                                'absence_type'=>convert_pamlek_to_erp($row->tapping_type)['absence_type'],
                                'kode_pamlek'=>$row->tapping_type,
                                'start_date'=>$insert_start_date,
                                'end_date'=>$insert_end_date,
                                'start_time'=>$insert_start_time,
                                'end_time'=>$insert_end_time,
                                'machine_id_start'=>$insert_start_machine,
                                'machine_id_end'=>$insert_end_machine
                            ];

                            $this->M_pamlek_to_ess_perizinan->check_id_then_insert_data($array_insert, "ess_perizinan_$bulan_tahun_start");
                            //$this->M_pamlek_to_ess_perizinan->update_cico($array_insert);
                        }
                    }
                }
                
                //echo json_encode($array_insert); exit();
                
                $msc = microtime(true)-$msc;
                echo "Done. Execution time: $msc seconds.<br>";
                echo "Inserted to database.<br>";
                echo "Table name: <b>ess_perizinan_$bulan_tahun_start</b><br>";
                echo "Total rows: <b>".$get->num_rows()."</b>";
                
                //insert ke tabel 'ess_status_proses_input', id proses = 8
                $this->db->insert('ess_status_proses_input', ['id_proses'=>8, 'waktu'=>date('Y-m-d H:i:s')]);
            }
            
            //update field 'id_perizinan' di tabel ess_cico_$tahun_bulan diambil dari 'id' di tabel ess_perizinan_$tahun_bulan
            /*$this->db->query("UPDATE $table_cico x
                            INNER JOIN 
                                (SELECT GROUP_CONCAT(a.id SEPARATOR ', ') as id_izin, b.id
                                FROM ess_perizinan_$bulan_tahun_start a
                                INNER JOIN 
                                    (SELECT c.id, c.np_karyawan,
                                        (CASE WHEN c.dws_in_tanggal_fix IS NOT NULL THEN c.dws_in_tanggal_fix ELSE c.dws_in_tanggal END) as dws_in_date,
                                        (CASE WHEN c.dws_in_fix IS NOT NULL THEN c.dws_in_fix ELSE c.dws_in END) as dws_in_time,
                                        (CASE WHEN c.dws_out_tanggal_fix IS NOT NULL THEN c.dws_out_tanggal_fix ELSE c.dws_out_tanggal END) as dws_out_date,
                                        (CASE WHEN c.dws_out_fix IS NOT NULL THEN c.dws_out_fix ELSE c.dws_out END) as dws_out_time
                                    FROM $table_cico c
                                ) b ON a.np_karyawan=b.np_karyawan AND
                                    (CASE WHEN a.start_date IS NOT NULL THEN (CONCAT(a.start_date,' ',a.start_time) BETWEEN CONCAT(b.dws_in_date,' ',b.dws_in_time) AND CONCAT(b.dws_out_date,' ',b.dws_out_time)) 
                                    ELSE (CONCAT(a.end_date,' ',a.end_time) BETWEEN CONCAT(b.dws_in_date,' ',b.dws_in_time) AND CONCAT(b.dws_out_date,' ',b.dws_out_time)) END)
                            GROUP BY b.id) y ON x.id = y.id
                            SET x.id_perizinan = (CASE WHEN x.id_perizinan IS NULL THEN y.id_izin ELSE CONCAT(x.id_perizinan,', ',y.id_izin) END)");*/
        }
        
    }
	
		public function cetak(){
			$tgl_awal 	= $this->input->post('tgl_awal');
			$tgl_akhir 	= $this->input->post('tgl_akhir');
			$np_karyawan = $this->input->post('np_karyawan');
			$get['tgl_awal'] = $tgl_awal;
			$get['tgl_akhir'] = $tgl_akhir;
			$get['np'] = $np_karyawan;
	        $data_nama = $this->m_perencanaan_jadwal_kerja->getDataCetak_nama($get);
	        $data_tgl = $this->m_perencanaan_jadwal_kerja->getDataCetak($get);
	        $period = new DatePeriod(
			     new DateTime($tgl_awal),
			     new DateInterval('P1D'),
			     new DateTime($tgl_akhir.' +1 day')
			);

			
	        error_reporting(E_ALL);
	        ini_set('display_errors', TRUE);
	        ini_set('display_startup_errors', TRUE);

	        header("Content-type: application/vnd.ms-excel");
	        //nama file
	        header("Content-Disposition: attachment; filename=Perencanaan_jadwal_kerja.xlsx");
	        header('Cache-Control: max-age=0');

	        $excel = PHPExcel_IOFactory::createReader('Excel2007');
	        $excel = $excel->load('./asset/Template_perencanaan_jadwal_kerja.xlsx');

	        //anggota
	        $excel->setActiveSheetIndex(0);
	        $kolom 	= 2;
	        $kolom2	= 2;
	        $baris 	= 5;
	        $no 	= 1;
	        $length = 0;
	        $alphabet = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH');

			foreach ($period as $key => $value) {
	        	$length++;
	            $excel->getActiveSheet()->setCellValueByColumnAndRow($kolom, 4, tanggal_indonesia($value->format('Y-m-d')));
	            $excel->getActiveSheet()->getColumnDimensionByColumn($kolom)->setWidth(25);
	        	$excel->getActiveSheet()->getStyleByColumnAndRow($kolom, 4)->applyFromArray(array(
	                'alignment' => array(
			            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			        ),
	                'borders' => array(
	                    'allborders' => array (
	                        'style' => PHPExcel_Style_Border::BORDER_THIN,
	                        ),
	                    )
	                )
	            );
	            $kolom += 1;
			}

			$excel->getActiveSheet()->mergeCells('A1:'.$alphabet[$length+1].'1');
			$excel->getActiveSheet()->getStyle('A1:'.$alphabet[$length+1].'1')->applyFromArray(array(
                'alignment' => array(
			            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			        )
	            )
            );

			// add text
			$excel->getActiveSheet()->mergeCells('C3:'.$alphabet[$length+1].'3');
			$excel->getActiveSheet()->setCellValue('C3','Tanggal');
			$excel->getActiveSheet()->getStyle('C3:'.$alphabet[$length+1].'3')->applyFromArray(array(
                'alignment' => array(
		            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		        ),
                'borders' => array(
                    'allborders' => array (
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        ),
                    )
                )
            );
	        foreach ($data_nama as $row) {
	        	$excel->getActiveSheet()->setCellValueExplicit('A'.$baris, $no++, PHPExcel_Cell_DataType::TYPE_STRING);
	        	$excel->getActiveSheet()->getStyleByColumnAndRow(0, $baris)->applyFromArray(array(
	                'alignment' => array(
			            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			        ),
	                'borders' => array(
	                    'allborders' => array (
	                        'style' => PHPExcel_Style_Border::BORDER_THIN,
	                        ),
	                    )
	                )
	            );
	        	$excel->getActiveSheet()->setCellValueExplicit('B'.$baris, strtoupper($row->nama), PHPExcel_Cell_DataType::TYPE_STRING);
	        	$excel->getActiveSheet()->getStyleByColumnAndRow(1, $baris)->applyFromArray(array(
	                'borders' => array(
	                    'allborders' => array (
	                        'style' => PHPExcel_Style_Border::BORDER_THIN,
	                        ),
	                    )
	                )
	            );
	            $excel->getActiveSheet()->getColumnDimensionByColumn(1)->setWidth(35);
	            foreach ($data_tgl as $isi) {
	            	if ($row->nama == $isi->nama) {
	            		foreach ($period as $key => $value) {
	            			if ($isi->date == $value->format('Y-m-d')) {
					            $excel->getActiveSheet()->setCellValueByColumnAndRow($kolom2, $baris, get_jadwal_from_dws(strtoupper($isi->dws)));
					            $excel->getActiveSheet()->getStyleByColumnAndRow($kolom2, $baris)->applyFromArray(array(
					                'alignment' => array(
							            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
							        ),
					                'borders' => array(
					                    'allborders' => array (
					                        'style' => PHPExcel_Style_Border::BORDER_THIN,
					                        ),
					                    )
					                )
					            );
					            for ($i=$kolom2; $i < $length+2; $i++) { 
					            	$excel->getActiveSheet()->getStyleByColumnAndRow($i, $baris)->applyFromArray(array(
						                'borders' => array(
						                    'allborders' => array (
						                        'style' => PHPExcel_Style_Border::BORDER_THIN,
						                        ),
						                    )
						                )
						            );
					            }
			    				$kolom2 = 2;
			            		break;
	            			}else{
	            				$excel->getActiveSheet()->getStyleByColumnAndRow($kolom2, $baris)->applyFromArray(array(
					                'borders' => array(
					                    'allborders' => array (
					                        'style' => PHPExcel_Style_Border::BORDER_THIN,
					                        ),
					                    )
					                )
					            );
	            				$kolom2 += 1;
	            			}
	            		}
	            	}
		        }
			    $kolom2 = 2;
	            $baris += 1;
	        }

	        $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
	        $objWriter->setIncludeCharts(TRUE);
	        $objWriter->setPreCalculateFormulas(TRUE);
	        PHPExcel_Calculation::getInstance($excel)->clearCalculationCache();
	        $objWriter->save('php://output');
	        exit();
	    }
		
	}
	
	/* End of file perencanaan_jadwal_kerja.php */
	/* Location: ./application/controllers/kehadiran/perencanaan_jadwal_kerja.php */