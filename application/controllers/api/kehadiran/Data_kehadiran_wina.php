<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Data_kehadiran extends CI_Controller {
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
			$this->load->helper("perizinan_helper");
			$this->load->helper('form');
			
			$this->load->model($this->folder_model."m_data_kehadiran");
		
			$this->data["is_with_sidebar"] = true;
								
			$this->data['judul'] = "Data Kehadiran";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);
			
			
		}
		
		public function index()
		{			
			//	echo _FILE_ . _LINE_;die(var_dump($this->akses));	
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."data_kehadiran";
					
			
			//ambil tahun bulan tabel yang tersedia
			$array_tahun_bulan = array();
			
			$nama_db = $this->db->database;			
			$query = $this->db->query("SELECT table_name FROM information_schema.tables WHERE table_schema = '$nama_db' AND table_name like '%ess_cico_%' GROUP BY table_name ORDER BY table_name DESC;");
            
            $get_master_data = $this->db->select("min(TABLE_NAME) as minn, max(TABLE_NAME) as maxx")->where('TABLE_SCHEMA',$nama_db)->like('TABLE_NAME','erp_master_data_20','AFTER')->order_by('TABLE_NAME','ASC')->get('information_schema.TABLES')->row();
            if($get_master_data->minn!=NULL){
                $th_min = substr($get_master_data->minn,16,4);
                $bl_min = substr($get_master_data->minn,21,2);
                $this->data['minDate'] = date("$th_min/$bl_min/01");
            }
            if($get_master_data->maxx!=NULL){
                $th_max = substr($get_master_data->maxx,16,4);
                $bl_max = substr($get_master_data->maxx,21,2);
                $this->data['maxDate'] = date("$th_max/$bl_max/t");
            }
            
			foreach ($query->result_array() as $data) 
			{					
				$bulan = substr($data['table_name'],-2);
				$tahun = substr($data['table_name'],9,4);
				
				$bulan_tahun = $bulan."-".$tahun;				
				
				$array_tahun_bulan[] = $bulan_tahun; 
			}
				
			//ambil mst dws	
			
			$array_jadwal_kerja 	= $this->m_data_kehadiran->select_mst_karyawan_aktif();
			$array_daftar_karyawan	= $this->m_data_kehadiran->select_daftar_karyawan();
			$array_daftar_unit		= $this->m_data_kehadiran->select_daftar_unit();
			
			$this->data['array_tahun_bulan'] 		= $array_tahun_bulan;	
			$this->data['array_jadwal_kerja'] 		= $array_jadwal_kerja;
			$this->data['array_daftar_karyawan'] 	= $array_daftar_karyawan;
			$this->data['array_daftar_unit'] 		= $array_daftar_unit;
			//echo json_encode($this->data);exit();
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
				$np_list=$this->m_data_kehadiran->select_np_by_kode_unit($list_kode_unit);						
								
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
		
		public function ajax_getAtasanKehadiran(){
            echo $this->getAtasanKehadiran($this->input->post('vnp_karyawan'));
		}
		
		private function getAtasanKehadiran($np_karyawan){
            
            # heru menambahkan ini 2020-11-11 @09:42
            # START get last approver as default
            $get_last_approver = $this->db->select('tapping_fix_approval_np')
                ->where('np_karyawan',$np_karyawan)
                ->where('tapping_fix_approval_np IS NOT NULL',null,false)
                ->order_by('id','DESC')
                ->limit(1)
                ->get('ess_cico_'.date('Y_m'));
            if($get_last_approver->num_rows()==1){
                return $get_last_approver->row()->tapping_fix_approval_np;
            }
            # END get last approver as default
            else{
                
                if( date('Y_m')!='2019_01' ){
                    $get_last_approver_1 = $this->db->select('tapping_fix_approval_np')
                        ->where('np_karyawan',$np_karyawan)
                        ->where('tapping_fix_approval_np IS NOT NULL',null,false)
                        ->order_by('id','DESC')
                        ->limit(1)
                        ->get( 'ess_cico_'.date('Y_m', strtotime("-1 months")) );
                    if($get_last_approver_1->num_rows()==1){
                        return $get_last_approver_1->row()->tapping_fix_approval_np;
                    }
                }
                # END tambahan dari Heru
                
                $this->load->model("master_data/m_karyawan");
                $karyawan = $this->m_karyawan->get_posisi_karyawan($np_karyawan);

                if(empty($karyawan)){
                    $periode = date("Y_m");
                    $karyawan = $this->m_karyawan->get_posisi_karyawan_periode($np_karyawan,$periode);

                    if(empty($karyawan)){
                        $periode = date('Y_m', strtotime(date("Y-m-d") . " -1 months"));
                        $karyawan = $this->m_karyawan->get_posisi_karyawan_periode($np_karyawan,$periode);
                    }
                }

                if(strcmp($karyawan["jabatan"],"kepala")==0){
                    $kode_unit_atasan = str_pad(substr($karyawan["kode_unit"],0,strlen($karyawan["kode_unit"])-1),5,0);
                }
                else{
                    $kode_unit_atasan = str_pad($karyawan["kode_unit"],5,0);
                }

                $kode_unit_atasan = str_pad(substr($kode_unit_atasan,0,4),5,0);

                do{
                    $np_atasan = $this->m_karyawan->get_atasan($kode_unit_atasan);

                    $kode_unit_atasan = preg_replace("/0+$/","",$kode_unit_atasan);
                    $kode_unit_atasan = str_pad(substr($kode_unit_atasan,0,strlen($kode_unit_atasan)-1),5,"0");

                }while(empty($np_atasan) and strlen(preg_replace("/0+$/","",$kode_unit_atasan))>1);

                return $np_atasan["np"];
            }
		}
		
		public function ajax_getPilihanAtasanKehadiran(){
			
			//2-12-2020 - 7648 Tri Wibowo menambah periode sehingga atasanya pas saat periode tersebut
			$periode = null;
			
			$np_karyawan = $this->input->post('vnp_karyawan');
			
			//2-12-2020 - 7648 Tri Wibowo menambah periode sehingga atasanya pas saat periode tersebut
			$pisah = explode('#',$np_karyawan);
			$np_karyawan = $pisah[0];
			$periode     = $pisah[1];
			
			$pisah_periode = explode('-',$periode);
			$d = $pisah_periode[0];
			$m = $pisah_periode[1];
			$y = $pisah_periode[2];
			
			$periode = $y.'_'.$m;
			$periode_tanggal 	= $y.'-'.$m.'-'.$d;
			
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
			
			if(empty($karyawan)){
				//2-12-2020 - 7648 Tri Wibowo menambah periode sehingga atasanya pas saat periode tersebut
				if($periode==null)
				{
					$periode = date("Y_m");
				}
				$karyawan = $this->m_karyawan->get_posisi_karyawan_periode($np_karyawan,$periode);
				
				if(empty($karyawan)){
					//2-12-2020 - 7648 Tri Wibowo menambah periode sehingga atasanya pas saat periode tersebut
					if($periode==null)
					{
						$periode = date('Y_m', strtotime(date("Y-m-d") . " -1 months"));
					}
					$karyawan = $this->m_karyawan->get_posisi_karyawan_periode($np_karyawan,$periode);
				}
			}
			
			if(strcmp(substr($karyawan["kode_unit"],1,1),"0")==0){
				$karyawan["kode_unit"] = substr($karyawan["kode_unit"],0,3);
			}
			else{
				$karyawan["kode_unit"] = substr($karyawan["kode_unit"],0,2);
			}
			
			$this->load->model("lembur/m_pengajuan_lembur");
            $arr_pilihan = $this->m_pengajuan_lembur->get_apv(array($karyawan["kode_unit"]),$np_karyawan);
            //$arr_pilihan = $this->m_pengajuan_lembur->get_apv(array($karyawan["kode_unit"]),$this->input->post('vnp_karyawan'));
			echo json_encode($arr_pilihan);
		}
		
		public function tabel_data_kehadiran($tampil_bulan_tahun = null)
		{		
			$this->load->model($this->folder_model."/M_tabel_data_kehadiran");
			
			//akses ke menu ubah				
			if($this->akses["ubah"]) //jika pengguna
			{
				$disabled_ubah = '';
			}else
			{
				$disabled_ubah = 'disabled';
			}
						
			if($tampil_bulan_tahun=='')
			{
				$tampil_bulan_tahun = '';				
			}else
			{
				$bulan = substr($tampil_bulan_tahun,0,2);
				$tahun = substr($tampil_bulan_tahun,3,4);
				$tampil_bulan_tahun = $tahun."_".$bulan;
			}
			
			
			
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
				
			$list = $this->M_tabel_data_kehadiran->get_datatables($var,$tampil_bulan_tahun);
			$data = array();
			$no = $_POST['start'];
			foreach ($list as $tampil) {
				$no++;
				$row = array();
				$row[] = $no;
				$row[] = $tampil->kode_unit;
				$row[] = $tampil->np_karyawan;
				$row[] = $tampil->nama;		
				$row[] = hari_tanggal($tampil->dws_tanggal);	
				
				//7065 15.05.2020 tampilkan tanggal dan jam jadwal kerja
				/* if($tampil->dws_name_fix==null || $tampil->dws_name_fix=='')
				{
					$row[] = nama_dws_by_kode($tampil->dws_name);
				}else
				{
					$row[] = nama_dws_by_kode($tampil->dws_name_fix);
				} */
				
				$jadwal_kerja = "";
				if($tampil->dws_name_fix==null || $tampil->dws_name_fix=='')
				{
					$jadwal_kerja = nama_dws_by_kode($tampil->dws_name);
				}else
				{
					$jadwal_kerja = nama_dws_by_kode($tampil->dws_name_fix);
				}
				
				if(!empty($jadwal_kerja)){
					if(strcmp($jadwal_kerja,"OFF")!=0){						
						$jadwal_kerja .= "<br><br>";
			
						$jadwal_kerja .= hari_tanggal($tampil->dws_in_tanggal)." ".substr($tampil->dws_in,0,5);
						$jadwal_kerja .= " sampai dengan ";
						if(strcmp($tampil->dws_in_tanggal,$tampil->dws_out_tanggal)!=0){
							$jadwal_kerja .= hari_tanggal($tampil->dws_out_tanggal)." ";
						}
						$jadwal_kerja .= substr($tampil->dws_out,0,5);
					}
				}
				
				$row[] = $jadwal_kerja;
				
				$machine_id_1 = '';
				$machine_id_2 = '';
				
				if($tampil->tapping_fix_1==null || $tampil->tapping_fix_1=='')
				{
					$tapping_1				= $tampil->tapping_time_1;
					if($tapping_1)
					{
						$pisah_tapping_1		= explode(' ',$tapping_1);
						$tapping_1_value_date	= $pisah_tapping_1[0];
						$tapping_1_value_time	= $pisah_tapping_1[1];
						$tapping_1_value_time   = substr($tapping_1_value_time,0,5);
						
						$machine_id_1			= "<br>Machine id : ".$tampil->tapping_terminal_1;
					}else
					{
						$tapping_1_value_date	= $tampil->dws_tanggal;
						$tapping_1_value_time	= '';
					}
				}else
				{					
					$tapping_1					= $tampil->tapping_fix_1;
					
					if($tapping_1)
					{
						$pisah_tapping_1		= explode(' ',$tapping_1);
						$tapping_1_value_date	= $pisah_tapping_1[0];
						$tapping_1_value_time	= $pisah_tapping_1[1];
						$tapping_1_value_time   = substr($tapping_1_value_time,0,5);
						
						if(substr($tampil->tapping_time_1,0,16) != substr($tampil->tapping_fix_1,0,16)) //dirubah oleh ess
						{
							if($tampil->tapping_fix_approval_ket=='WFH MOBILE')
							{
								$machine_id_1			= "<br>Machine id : ".'ESS MOBILE';		
							}else
							{
								$machine_id_1			= "<br>Machine id : ".'ESS';	
							}
							
										
							
						}else//tidak dirubah
						{
							$machine_id_1			= "<br>Machine id : ".$tampil->tapping_terminal_1;
						}
					
						
					}else
					{
						$tapping_1_value_date	= $tampil->dws_tanggal;
						$tapping_1_value_time	= '';
					}					
				}
				
				if($tapping_1 || $tapping_1=='')
				{
					if(@$tampil->tapping_fix_1_temp) //check apakah ada perubahan belum di approve
					{
						
						$approval_status_id = $tampil->tapping_fix_approval_status;
						if($approval_status_id==0)
						{
							$approval_status_1 = "Belum Disetujui ".$tampil->tapping_fix_approval_np;
							$btn_warna		  	= "btn-warning";
						}else
						if($approval_status_id==1)
						{
							$approval_status_1 = "Disetujui ".$tampil->tapping_fix_approval_np;
							$btn_warna		  	= "btn-success";
						}else
						if($approval_status_id==2)
						{
							$approval_status_1 = "Ditolak ".$tampil->tapping_fix_approval_np;
							$btn_warna		  	= "btn-danger";
						}else
						if($approval_status_id==3)
						{
							$approval_status_1 = "Dibatalkan";
							$btn_warna		  	= "btn-danger";
						}
						
						if(substr($tampil->tapping_fix_1_temp,0,16)==(substr($tampil->tapping_time_1,0,16))) //ketika data yg diubah dan yg mau dirubah sama ga usah tampil
						{
							$show_tapping_temp_1 = "";
						}else
						{
							# $show_tapping_temp_1 = "<font color='grey'>".tanggal(substr($tampil->tapping_fix_1_temp,0,10))."<br>".substr($tampil->tapping_fix_1_temp,10,6)."</font>"."<br><a class='btn $btn_warna btn-xs btn-sm edit_button'>".$approval_status_1."</a>";
                            # heru delete class edit_button di label ini 2020-11-13 @10:45
							$show_tapping_temp_1 = "<font color='grey'>".tanggal(substr($tampil->tapping_fix_1_temp,0,10))."<br>".substr($tampil->tapping_fix_1_temp,10,6)."</font>"."<br><a class='btn $btn_warna btn-xs btn-sm'>".$approval_status_1."</a>";
						}							
					}else
					{
						$show_tapping_temp_1 = "";
					}					
					
					
					$row[] 	= "<strong>".tanggal(substr($tapping_1,0,10))."<br>".substr($tapping_1,10,6)."</strong>".$machine_id_1."<br><br>".$show_tapping_temp_1;
				}else
				{
					$row[]	= '';
				}
				
				if($tampil->tapping_fix_2==null || $tampil->tapping_fix_2=='')
				{
					$tapping_2  				= $tampil->tapping_time_2;
					
					if($tapping_2)
					{
						$pisah_tapping_2		= explode(' ',$tapping_2);
						$tapping_2_value_date	= $pisah_tapping_2[0];
						$tapping_2_value_time	= $pisah_tapping_2[1];
						$tapping_2_value_time   = substr($tapping_2_value_time,0,5);
						
						$machine_id_2			= "<br>Machine id : ".$tampil->tapping_terminal_2;
					}else
					{
						$tapping_2_value_date	= $tampil->dws_tanggal;
						$tapping_2_value_time	= '';
					}					
				}else
				{					
					$tapping_2 					= $tampil->tapping_fix_2;
					
					if($tapping_2)
					{
						$pisah_tapping_2		= explode(' ',$tapping_2);
						$tapping_2_value_date	= $pisah_tapping_2[0];
						$tapping_2_value_time	= $pisah_tapping_2[1];
						$tapping_2_value_time   = substr($tapping_2_value_time,0,5);
						
						if(substr($tampil->tapping_time_2,0,16) != substr($tampil->tapping_fix_2,0,16)) //dirubah oleh ess
						{
							if($tampil->tapping_fix_approval_ket=='WFH MOBILE')
							{
								$machine_id_2			= "<br>Machine id : ".'ESS MOBILE';		
							}else
							{
								$machine_id_2			= "<br>Machine id : ".'ESS';	
							}
							
						
						}else //tidak dirubah
						{
							$machine_id_2			= "<br>Machine id : ".$tampil->tapping_terminal_2;
						}						
						
					}else
					{
						$tapping_2_value_date	= $tampil->dws_tanggal;
						$tapping_2_value_time	= '';
					}		
				}
				
				if($tapping_2 || $tapping_2=='')
				{
					if(@$tampil->tapping_fix_2_temp) //check apakah ada perubahan belum di approve
					{						
						$approval_status_id = $tampil->tapping_fix_approval_status;
						if($approval_status_id==0)
						{
							$approval_status_2 	= "Belum Disetujui ".$tampil->tapping_fix_approval_np;
							$btn_warna		  	= "btn-warning";
						}else
						if($approval_status_id==1)
						{
							$approval_status_2 = "Disetujui ".$tampil->tapping_fix_approval_np;
							$btn_warna		  	= "btn-success";
						}else
						if($approval_status_id==2)
						{
							$approval_status_2 = "Ditolak ".$tampil->tapping_fix_approval_np;
							$btn_warna		  	= "btn-danger";
						}else
						if($approval_status_id==3)
						{
							$approval_status_2 = "Dibatalkan";
							$btn_warna		  	= "btn-danger";
						}
						
						if(substr($tampil->tapping_fix_2_temp,0,16)==(substr($tampil->tapping_time_2,0,16))) //ketika data yg diubah dan yg mau dirubah sama ga usah tampil
						{
							$show_tapping_temp_2 = "";
						}else
						{
							# $show_tapping_temp_2 = "<font color='grey'>".tanggal(substr($tampil->tapping_fix_2_temp,0,10))."<br>".substr($tampil->tapping_fix_2_temp,10,6)."</font>"."<br><a class='btn $btn_warna btn-xs btn-sm edit_button'>".$approval_status_2."</a>";
                            # heru delete class edit_button di label ini 2020-11-13 @10:45
							$show_tapping_temp_2 = "<font color='grey'>".tanggal(substr($tampil->tapping_fix_2_temp,0,10))."<br>".substr($tampil->tapping_fix_2_temp,10,6)."</font>"."<br><a class='btn $btn_warna btn-xs btn-sm'>".$approval_status_2."</a>";
						}
						
											
					}else
					{
						$show_tapping_temp_2 = "";
					}
					
					$row[] 	= "<strong>".tanggal(substr($tapping_2,0,10))."<br>".substr($tapping_2,10,6)."</strong>".$machine_id_2."<br><br>".$show_tapping_temp_2;
				}else
				{
					$row[]	= '';
				}
				
				$tampil_keterangan 	= '';
				$hari_libur 		= hari_libur_by_tanggal($tampil->dws_tanggal);
				

				$hari_cuti_bersama = $this->db->query("SELECT * FROM ess_cuti_bersama WHERE tanggal_cuti_bersama='$tampil->dws_tanggal' AND np_karyawan='$tampil->np_karyawan' LIMIT 1")->row_array();
								
				//7648 Tri Wibowo, 6 Januari 2019 - ketika sudah di pembatalan maka tidak tampil
				$hari_pembatalan =  $this->db->query("SELECT * FROM ess_pembatalan_cuti WHERE is_cuti_bersama='1' AND date='$tampil->dws_tanggal' AND np_karyawan='$tampil->np_karyawan' LIMIT 1")->row_array();
				//jika ada pembatalan
				$id_cuti_bersama = null;
				if($hari_pembatalan['id']==null)
				{
					$id_cuti_bersama = $hari_cuti_bersama['id'];
				}
	
				if($hari_libur)
				{
					//$row[] = $hari_libur;
					if($tampil_keterangan=='')
					{
						$tampil_keterangan = $hari_libur;
					}else
					{
						$tampil_keterangan = $tampil_keterangan."<br><br>".$hari_libur;
					}
					
				}
				
			
				
				if($tampil->id_cuti)
				{
					//$row[] = 'Cuti';
					
					$data_cuti 		= $this->m_data_kehadiran->select_cuti_by_id($tampil->id_cuti);
					$tampil_cuti 	= $data_cuti['uraian'];
					
					if($tampil_keterangan=='')
					{
						$tampil_keterangan = $tampil_cuti;
					}else
					{
						$tampil_keterangan = $tampil_keterangan."<br><br>".$tampil_cuti;
					}
					
				}else				
				if($tampil->id_sppd)
				{
					//$row[] = 'Dinas';
					if($tampil_keterangan=='')
					{
						$tampil_keterangan = 'Dinas';
					}else
					{
						$tampil_keterangan = $tampil_keterangan."<br><br>".'Dinas';
					}
					
					
				}else				
				if($id_cuti_bersama!=null)
				{
					//$row[] = 'Dinas';
					if($tampil_keterangan=='')
					{
						$tampil_keterangan = 'Cuti Bersama';
					}else
					{
						$tampil_keterangan = $tampil_keterangan."<br><br>".'Cuti Bersama';
					}					
					
				}else
				{
					$id_perizinan=explode(",",$tampil->id_perizinan);
					$isi='';
					foreach($id_perizinan as $value)
					{
						$tahun_bulan = substr($tampil->dws_tanggal,0,7);
						
						$tahun_bulan = str_replace('-','_',$tahun_bulan);
						
						$izin = perizinan_by_id($tahun_bulan,$value);
						$kode_erp = $izin['info_type']."|".$izin['absence_type'];
						$nama_perizinan=nama_perizinan_by_kode_erp($kode_erp);
						
						if($nama_perizinan)
						{
							$isi=$isi."".$nama_perizinan."<br><br>";
						}							
					}
					
					//$row[] = $tampil->keterangan."<br><br>".$isi;
					if(!$hari_libur)
					{
						if($tampil_keterangan=='')
						{
							if ((strpos($isi, 'Izin Dinas Keluar Perusahaan Non-Pendidikan') !== false) || 
								(strpos($isi, 'Izin Dinas Keluar Perusahaan Pendidikan / Non-Pendidikan') !== false) || // heru menambahkan ini, based on table mst_perizinan id 4
								(strpos($isi, 'Izin Dinas Luar Lokasi Pendidikan') !== false) || 
								(strpos($isi, 'Izin Pribadi dengan Potongan') !== false) ||
								(strpos($isi, 'Izin Pribadi Tanpa Potongan') !== false)
							) 
							{								
								$tampil_keterangan =$isi;
							}else
							{	
								if($tampil->keterangan)
								{
									//Tri Wibowo, 4 Maret 2020, kadang isinya ada TK TM AB jadi keterangnya tidak usah di tampilin
									//Nyalain lagi
									/*
									6337
									6886
									6891
									6887
									7089
									7102
									bulan maret
									*/
									
							/*		
									if ((strpos($tampil->keterangan, 'Izin Dinas Keluar Perusahaan Non-Pendidikan') !== false) || 
										(strpos($tampil->keterangan, 'Izin Dinas Luar Lokasi Pendidikan') !== false) ||
										(strpos($tampil->keterangan, 'Izin Pribadi dengan Potongan') !== false) ||
										(strpos($tampil->keterangan, 'Izin Pribadi Tanpa Potongan') !== false)
									)
									{
							*/
										//aslinya ini
										$tampil_keterangan = $tampil->keterangan."<br><br>".$isi;
						/*			}else
									{
										$tampil_keterangan =$isi;
									}										
						*/			
									
									
									
									
									
									
									
									
								}else
								{
									$tampil_keterangan =$isi;
								}
								
							}
							
							
						}else
						{
							if ((strpos($isi, 'Izin Dinas Keluar Perusahaan Non-Pendidikan') !== false) || 
								(strpos($isi, 'Izin Dinas Keluar Perusahaan Pendidikan / Non-Pendidikan') !== false) || // heru menambahkan ini, based on table mst_perizinan id 4
								(strpos($isi, 'Izin Dinas Luar Lokasi Pendidikan') !== false) ||
								(strpos($isi, 'Izin Pribadi dengan Potongan') !== false) ||
								(strpos($isi, 'Izin Pribadi Tanpa Potongan') !== false)
							) 
							{								
								$tampil_keterangan = $tampil_keterangan."<br><br>".$isi;	
							}else
							{
								if($tampil->keterangan)
								{
									$tampil_keterangan = $tampil_keterangan."<br><br>".$tampil->keterangan."<br><br>".$isi;	
								}else
								{
									$tampil_keterangan = $tampil_keterangan."<br><br>".$isi;
								}
								
								
							}
							
							
						}		
					}
										
				}
				
				//16 03 2020 - Tri Wibowo, WORK FROM HOME
				if($tampil->wfh==1)
				{
					$tampil_keterangan = "<mark>WFH</mark>"."<br><br>".$tampil_keterangan;
				}
				
				
				$row[] = $tampil_keterangan;
				//check_sidt biar tidak bisa edit ketika SIDT						
				if(preg_match("/Izin Datang Terlambat/i", $tampil_keterangan)) {
					$ada_sidt='1';
				} else {
					$ada_sidt='0';
				}
				
				//cutoff ERP
				$sudah_cutoff = sudah_cutoff($tampil->dws_tanggal);
				
				if($sudah_cutoff) //jika sudah lewat masa cutoff
				{
					$row[] = "<button  class='btn btn-primary btn-xs edit_button' data-toggle='tooltip' title='Tidak bisa melakukan aksi setelah Cutoff data di ERP pada $sudah_cutoff' disabled>Submit ERP</button>";
				}else
				{					
					if(@$tampil->wfh_foto_1)
					{
						$foto_1 = base_url()."file/kehadiran2/$tampil->wfh_foto_1";
						//kehadiran 2 direct ke home (wina)
					}else
					{
						$foto_1 = '';
					}
					
					if(@$tampil->wfh_foto_2)
					{
						$foto_2 = base_url()."file/kehadiran2/$tampil->wfh_foto_2";
						//kehadiran 2 direct ke home (wina)
					}else
					{
						$foto_2 = '';
					}

					if($tapping_1_value_time=='' && $tampil->tapping_fix_approval_status!='2') //jika belum di batalkan maka tampil yg di temp
					{						
						$tapping_1_value_time   = substr($tampil->tapping_fix_1_temp,10,6);
					}
					
					if($tapping_2_value_time=='' && $tampil->tapping_fix_approval_status!='2') //jika belum di batalkan maka tampil yg di temp
					{						
						$tapping_2_value_time   = substr($tampil->tapping_fix_2_temp,10,6);
					}		
					
						
					if($this->akses["ubah kode unit"]) 
					{						
						$row[] = "<a class='btn btn-primary btn-xs edit_button' data-toggle='modal' data-target='#modal_ubah'
						data-id='$tampil->id'
						data-np-karyawan='$tampil->np_karyawan'
						data-nama='$tampil->nama'
						data-dws-tanggal='".date('d-m-Y', strtotime($tampil->dws_tanggal))."'
						data-dws-name='$tampil->dws_name'
						data-kode-unit='$tampil->kode_unit'
						data-tapping-1-date='".date('d-m-Y', strtotime($tapping_1_value_date))."'
						data-tapping-1-time='$tapping_1_value_time'
						data-tapping-2-date='".date('d-m-Y', strtotime($tapping_2_value_date))."'
						data-tapping-2-time='$tapping_2_value_time'
						data-tapping-fix-approval-ket='$tampil->tapping_fix_approval_ket'
						data-tapping-fix-approval-np='$tampil->tapping_fix_approval_np'
						data-ada-sidt='$ada_sidt'
						data-wfh='$tampil->wfh'	
						data-wfh_foto_1='$foto_1'
						data-wfh_foto_2='$foto_2'
						$disabled_ubah						
						>Ubah</a>
						<br><br>
						<a class='btn btn-primary btn-xs edit_kode_unit' data-toggle='modal' data-target='#modal_ubah_kode_unit'
						data-id='$tampil->id'
						data-np-karyawan='$tampil->np_karyawan'
						data-nama='$tampil->nama'
						data-dws-tanggal='".date('d-m-Y', strtotime($tampil->dws_tanggal))."'
						data-dws-name='$tampil->dws_name'
						data-kode-unit='$tampil->kode_unit'					
						$disabled_ubah>Kode Unit</a>";
					}else
					{
						
						$row[] = "<a class='btn btn-primary btn-xs edit_button' data-toggle='modal' data-target='#modal_ubah'
						data-id='$tampil->id'
						data-np-karyawan='$tampil->np_karyawan'
						data-nama='$tampil->nama'
						data-dws-tanggal='".date('d-m-Y', strtotime($tampil->dws_tanggal))."'
						data-dws-name='$tampil->dws_name'
						data-kode-unit='$tampil->kode_unit'
						data-tapping-1-date='".date('d-m-Y', strtotime($tapping_1_value_date))."'
						data-tapping-1-time='$tapping_1_value_time'
						data-tapping-2-date='".date('d-m-Y', strtotime($tapping_2_value_date))."'
						data-tapping-2-time='$tapping_2_value_time'
						data-tapping-fix-approval-ket='$tampil->tapping_fix_approval_ket'
						data-tapping-fix-approval-np='$tampil->tapping_fix_approval_np'
						data-ada-sidt='$ada_sidt'
						data-wfh='$tampil->wfh'
						data-wfh_foto_1='$foto_1'
						data-wfh_foto_2='$foto_2'
						$disabled_ubah
						>Ubah</a>";
					}
					
				}
				
				
				
				
								
				$data[] = $row;
			}

			$output = array(
							"draw" => $_POST['draw'],
							"recordsTotal" => $this->M_tabel_data_kehadiran->count_all($var,$tampil_bulan_tahun),
							"recordsFiltered" => $this->M_tabel_data_kehadiran->count_filtered($var,$tampil_bulan_tahun),
							"data" => $data,
					);
			//output to json format
			echo json_encode($output);
		}
		
		public function action_insert_data_kehadiran()
		{
            //echo json_encode($this->input->post()); exit();
			$submit = $this->input->post('submit');
										
			if($submit)
			{					
				$np_karyawan 		= $this->input->post('insert_np_karyawan');
				$nama 				= $this->input->post('insert_nama');
				$dws_tanggal 		= date('Y-m-d', strtotime($this->input->post('insert_dws_tanggal')));
				$insert_dws_id 		= $this->input->post('insert_dws_id');			
				
				$tapping_fix_1_date = date('Y-m-d', strtotime($this->input->post('insert_tapping_fix_1_date')));
				$tapping_fix_1_time = $this->input->post('insert_tapping_fix_1_time');	
				$tapping_fix_1 		= $tapping_fix_1_date." ".$tapping_fix_1_time;					
				
				
				$tapping_fix_2_date = date('Y-m-d', strtotime($this->input->post('insert_tapping_fix_2_date')));
				$tapping_fix_2_time = $this->input->post('insert_tapping_fix_2_time');	
				$tapping_fix_2 		= $tapping_fix_2_date." ".$tapping_fix_2_time;			
				
				//validasi ketika tapping keluar lebih kecil dari tapping masuk
				if($tapping_fix_2<=$tapping_fix_1)
				{
					$this->session->set_flashdata('warning',"Gagal, Tapping Keluar harus lebih besar dari Tapping Masuk");				
					redirect(base_url($this->folder_controller.'data_kehadiran'));	
				}
				
				
                $tahun_bulan = str_replace('-','_',substr($dws_tanggal,0,7));
				if(check_table_exist("erp_master_data_$tahun_bulan")=='belum ada'){
                    $this->session->set_flashdata('warning',"Tabel Master Data tidak ada. Proses tidak dapat dilanjutkan.");
                    redirect(base_url($this->folder_controller.'data_kehadiran'));
                } else{
                    //cek apakah ada np di tabel erp master data
                    $cek_np = $this->db->where(['np_karyawan'=>$np_karyawan, 'tanggal_dws'=>$dws_tanggal])->get("erp_master_data_$tahun_bulan");
                    if($cek_np->num_rows()<1){
                        $this->session->set_flashdata('warning',"Karyawan dengan NP: $np_karyawan dan Tanggal DWS: $dws_tanggal tidak ada di tabel 'erp_master_data_$tahun_bulan'. Proses tidak dapat dilanjutkan.");
                        redirect(base_url($this->folder_controller.'data_kehadiran'));
                    }
                }
				
				
				$dws = $this->m_data_kehadiran->select_mst_jadwal_kerja_by_id($insert_dws_id);
				$dws_name			= $dws['dws'];
				$dws_in				= $dws['dws_start_time'];
				$dws_out			= $dws['dws_end_time'];
				$dws_break_start	= $dws['dws_break_start_time'];
				$dws_break_end		= $dws['dws_break_end_time'];
				
				if($dws['lintas_hari_masuk']=='1')
				{
					$dws_in_tanggal		= date('Y-m-d', strtotime($dws_tanggal . ' +1 day'));	
				}else
				{
					$dws_in_tanggal		= $dws_tanggal ;				
				}
				
				if($dws['lintas_hari_pulang']=='1')
				{
					$dws_out_tanggal	= date('Y-m-d', strtotime($dws_tanggal . ' +1 day'));	
				}else
				{
					$dws_out_tanggal	= $dws_tanggal ;
				}
				
				
				
				
				
				
				$bulan			= substr($dws_tanggal,5,2);
				$tahun 			= substr($dws_tanggal,0,4);
				$tahun_bulan	= $tahun."_".$bulan;	
				
				$tampil_bulan_tahun = $bulan."-".$tahun;
								
				//validasi bulan tahun
				if($tampil_bulan_tahun=='' || $tampil_bulan_tahun==null)
				{
					$this->session->set_flashdata('warning',"Bulan Kehadiran harus terisi");
					redirect(base_url($this->folder_controller.'data_kehadiran'));
				}
				
				//validasi data sudah ada
				$data_exist['np_karyawan'] 		= $np_karyawan;
				$data_exist['dws_tanggal'] 		= $dws_tanggal;
				$data_exist['tahun_bulan'] 		= $tahun_bulan;
								
				$exist	= $this->m_data_kehadiran->check_cico_exist($data_exist);
					
				
		
				if($exist!=0)
				{
					$this->session->set_flashdata('warning',"<b>Gagal input</b>, Data <b>$np_karyawan | $nama</b> pada <b>$dws_tanggal</b> sudah tersedia.");				
					redirect(base_url($this->folder_controller.'data_kehadiran'));
				}
				
								
				$data_insert['np_karyawan'] 		= $np_karyawan;
				$data_insert['nama'] 				= erp_master_data_by_np($np_karyawan, $dws_tanggal)['nama'];
				$data_insert['personel_number'] 	= erp_master_data_by_np($np_karyawan, $dws_tanggal)['personnel_number'];
				$data_insert['nama_unit'] 			= erp_master_data_by_np($np_karyawan, $dws_tanggal)['nama_unit'];
				$data_insert['kode_unit'] 			= erp_master_data_by_np($np_karyawan, $dws_tanggal)['kode_unit'];
				$data_insert['nama_jabatan'] 		= erp_master_data_by_np($np_karyawan, $dws_tanggal)['nama_jabatan'];
				$data_insert['dws_tanggal'] 		= $dws_tanggal;
				$data_insert['dws_name'] 			= $dws_name;				
				$data_insert['dws_in'] 				= $dws_in;
				$data_insert['dws_out'] 			= $dws_out;
				$data_insert['dws_in_tanggal'] 		= $dws_in_tanggal;
				$data_insert['dws_out_tanggal'] 	= $dws_out_tanggal;
				$data_insert['dws_break_start']		= $dws_break_start;
				$data_insert['dws_break_end'] 		= $dws_break_end;			
				$data_insert['tapping_fix_1']		= $tapping_fix_1;
				$data_insert['tapping_fix_2'] 		= $tapping_fix_2;
				$data_insert['tahun_bulan'] 		= $tahun_bulan;
				
				$insert = $this->m_data_kehadiran->insert_data_kehadiran($data_insert);
					
				if($insert==0)
				{
					$this->session->set_flashdata('warning',"<b>Gagal</b> input kehadiran");
				}else
				{
					$this->session->set_flashdata('success',"Berhasil, data kehadiran <b>$np_karyawan | $nama</b> tanggal <b>$dws_tanggal</b> telah masuk database");
					
					//===== Log Start =====
					$arr_data_baru = $this->m_data_kehadiran->select_kehadiran_by_id($insert,$tahun_bulan);					
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
						"deskripsi" => "insert ".strtolower(preg_replace("//"," ",CLASS_)),						
						"kondisi_baru" => $log_data_baru,
						"alamat_ip" => $this->data["ip_address"],
						"waktu" => date("Y-m-d H:i:s")
					);
					$this->m_log->tambah($log);
					//===== Log end =====
					
				}	
				
				//tembak LEMBUR
				//update id_lembur
				$this->load->model('lembur/m_pengajuan_lembur');
				$get_lembur['no_pokok'] = $np_karyawan;
				$get_lembur['tgl_dws'] = $dws_tanggal;
				//refresh lembur fix
				$check = $this->m_pengajuan_lembur->update_dws($np_karyawan, $dws_tanggal);
				$this->m_pengajuan_lembur->set_cico($get_lembur);
				
				$this->session->set_flashdata('tampil_bulan_tahun',$tampil_bulan_tahun);
				redirect(base_url($this->folder_controller.'data_kehadiran'));				
				
			}else
			{
				$this->session->set_flashdata('warning',"Terjadi Kesalahan");
				
				redirect(base_url($this->folder_controller.'data_kehadiran'));	
			}	
		}
					
		public function action_update_data_kehadiran()
		{			
			//echo json_encode($this->input->post()); exit();
            $submit = $this->input->post('submit');
								
			if($submit)
			{					
				$id 				= $this->input->post('edit_id');
				$np_karyawan 		= $this->input->post('edit_np_karyawan');
				$nama 				= $this->input->post('edit_nama');
				$dws_tanggal 		= date('Y-m-d', strtotime($this->input->post('edit_dws_tanggal')));
				$dws_name 			= $this->input->post('edit_dws_name');			
				
				$tapping_1_date 	= date('Y-m-d', strtotime($this->input->post('edit_tapping_1_date')));		
				$tapping_1_time 	= $this->input->post('edit_tapping_1_time');		
				$tapping_1 			= $tapping_1_date." ".$tapping_1_time;						
								
				$tapping_2_date 	= date('Y-m-d', strtotime($this->input->post('edit_tapping_2_date')));		
				$tapping_2_time 	= $this->input->post('edit_tapping_2_time');		
				$tapping_2 			= $tapping_2_date." ".$tapping_2_time;
								
				$np_approval_array 	= $this->input->post('edit_approval');
				$np_approval		= $np_approval_array[0];
							
				$tapping_fix_approval_ket 	= $this->input->post('edit_tapping_fix_approval_ket');
				
				$tampil_bulan_tahun	= $this->input->post('edit_tampil_bulan_tahun');
							
				$bulan	= substr($tampil_bulan_tahun,0,2);
				$tahun 	= substr($tampil_bulan_tahun,3,4);
				$tahun_bulan		= $tahun."_".$bulan;
				
				//16 03 2020 - Tri Wibowo, WORK FROM HOME
				$wfh		= $this->input->post('edit_wfh');
				$wfh_foto	= $this->input->post('edit_wfh_foto');
				
				//27 07 2020  - Tri Wibowo, Work From Home, Check keterangan wfh tapi tidak checklist
				$kalimat = $tapping_fix_approval_ket;
				
				if($wfh=='1')
				{
					//do nothing
				}else
				{
					if(preg_match("/wfh/i", $kalimat)) 
					{
						$this->session->set_flashdata('warning',"WFH Belum Checklist Komitmen");				
						redirect(base_url($this->folder_controller.'data_kehadiran'));	
					} 
					
					if(preg_match("/work from home/i", $kalimat)) 
					{
						$this->session->set_flashdata('warning',"WFH Belum Checklist Komitmen");				
						redirect(base_url($this->folder_controller.'data_kehadiran'));	
					} 
					
					if(preg_match("/dari rumah/i", $kalimat)) 
					{
						$this->session->set_flashdata('warning',"WFH Belum Checklist Komitmen");				
						redirect(base_url($this->folder_controller.'data_kehadiran'));	
					} 
					
					if(preg_match("/di rumah/i", $kalimat)) 
					{
						$this->session->set_flashdata('warning',"WFH Belum Checklist Komitmen");				
						redirect(base_url($this->folder_controller.'data_kehadiran'));	
					} 
					
					if(preg_match("/covid/i", $kalimat)) 
					{
						$this->session->set_flashdata('warning',"WFH Belum Checklist Komitmen");				
						redirect(base_url($this->folder_controller.'data_kehadiran'));	
					} 
				}
				
				//validasi ketika tapping keluar lebih kecil dari tapping masuk
				if(strtotime($tapping_2)<=strtotime($tapping_1))
				{
					if($wfh=='1' && ($tapping_2_time=='' || $tapping_2_time==null || $tapping_2_time=='00:00'|| $tapping_2_time==' 00:00') && @$tapping_1_time) //jika wfh boleh isi masuk nya dulu
					{
						//do nothing
					}else
					{						
						$this->session->set_flashdata('warning',"Gagal, Tapping Keluar harus lebih besar dari Tapping Masuk, wfh=$wfh, tapping in = $tapping_1, tapping out = $tapping_2");				
							redirect(base_url($this->folder_controller.'data_kehadiran'));	
					}	
				}
				
				//===== Log Start =====
				$arr_data_lama = $this->m_data_kehadiran->select_kehadiran_by_id($id,$tahun_bulan);
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
				
			
				$this->load->library(array('upload'));
				$this->load->helper(array('form', 'url'));
					
				$config['upload_path'] = 'file/kehadiran2';			
				$config['allowed_types'] 	= 'gif|jpg|jpeg|image/jpeg|image|png';
				$config['max_size']			= '2000';	
				$config['encrypt_name'] 	= true;	
				
				$edit_wfh_foto[0] 	= null;
				$edit_wfh_foto[1]	= null;	
				$files = $_FILES;
				
				//check apakah ada file sebelumnya :
				if($tampil_bulan_tahun=='')
				{
					$tabel = 'ess_cico';
				}else
				{
					$tabel = 'ess_cico'."_".$tahun_bulan;
				}
					
				$ambil = $this->db->query("SELECT wfh_foto_1,wfh_foto_2 FROM $tabel WHERE id='$id'")->row_array();
				$ambil_wfh_foto_1 = $ambil['wfh_foto_1'];
				$ambil_wfh_foto_2 = $ambil['wfh_foto_2'];
					
				if(@$files && $wfh=='1')
				{
					
					$cpt = count($_FILES['edit_wfh_foto']['name']);
					for($i=0; $i<$cpt; $i++)
					{
						
						$_FILES['edit_wfh_foto']['name']= $files['edit_wfh_foto']['name'][$i];
						$_FILES['edit_wfh_foto']['type']= $files['edit_wfh_foto']['type'][$i];
						$_FILES['edit_wfh_foto']['tmp_name']= $files['edit_wfh_foto']['tmp_name'][$i];
						$_FILES['edit_wfh_foto']['error']= $files['edit_wfh_foto']['error'][$i];
						$_FILES['edit_wfh_foto']['size']= $files['edit_wfh_foto']['size'][$i];
						
						$this->upload->initialize($config);
																				
						if($i==0)
						{
							if($files['edit_wfh_foto']['name'][$i])
							{								
								if(@$ambil_wfh_foto_1)
								{
									$path		= './file/kehadiran2/'.$ambil_wfh_foto_1;
									$this->load->helper("file");
									unlink($path);
								}
																
								$gambar='edit_wfh_foto';
								if($this->upload->do_upload($gambar))
								{	
									$up=$this->upload->data();								
									$edit_wfh_foto[$i]=$up['file_name'];
								}else
								{
									$error =$this->upload->display_errors();
									$this->session->set_flashdata('warning',"Terjadi Kesalahan, $error");				
									redirect(base_url($this->folder_controller.'data_kehadiran'));	
								}
								
							}else
							{
								$edit_wfh_foto[$i]= $ambil_wfh_foto_1;
							}								
						}

						if($i==1)
						{
							if($files['edit_wfh_foto']['name'][$i])
							{
								
								if(@$ambil_wfh_foto_2)
								{
									$path		= './file/kehadiran2/'.$ambil_wfh_foto_2;
									$this->load->helper("file");
									unlink($path);
								}
																							
								$gambar='edit_wfh_foto';
								if($this->upload->do_upload($gambar))
								{	
									$up=$this->upload->data();								
									$edit_wfh_foto[$i]=$up['file_name'];
								}else
								{
									$error =$this->upload->display_errors();
									$this->session->set_flashdata('warning',"Terjadi Kesalahan, $error");				
									redirect(base_url($this->folder_controller.'data_kehadiran'));	
								}
								
								
							}else
							{
								$edit_wfh_foto[$i]= $ambil_wfh_foto_2;
							}
						}
						
							
							
							
										
					}
				}else
				{	
					//pake data sebelum	
					$edit_wfh_foto[0]= $ambil_wfh_foto_1;					
					$edit_wfh_foto[1]= $ambil_wfh_foto_1;					
				}
				
				$data_update['id'] 			= $id;
				$data_update['np_karyawan'] = $np_karyawan;
				$data_update['nama_unit'] 	= nama_unit_by_np($np_karyawan);
				$data_update['nama_jabatan']= nama_jabatan_by_np($np_karyawan);
				$data_update['nama'] 		= $nama;
				$data_update['dws_tanggal'] = $dws_tanggal;
				$data_update['dws_name'] 	= $dws_name;
				$data_update['tapping_1']	= $tapping_1;
				$data_update['tapping_2'] 	= $tapping_2;
				$data_update['tahun_bulan'] = $tahun_bulan;
				$data_update['tapping_fix_approval_ket'] = $tapping_fix_approval_ket;
									
				$this->load->model("master_data/m_karyawan");
				$approval = $this->m_karyawan->get_posisi_karyawan($np_approval);
									
				$data_update['tapping_fix_approval_status'] 		= "0"; //default belum di approve
				/*$data_update['tapping_fix_approval_np'] 			= $approval['no_pokok'];
				$data_update['tapping_fix_approval_nama'] 			= $approval['nama'];
				$data_update['tapping_fix_approval_nama_jabatan'] 	= $approval['nama_jabatan'];*/
                
                # heru mengganti approver ini dari post data , 2020-11-12 @14:40
				$data_update['tapping_fix_approval_np'] 			= $this->input->post('edit_approval');
				$data_update['tapping_fix_approval_nama'] 			= $this->input->post('approval_input');
				$data_update['tapping_fix_approval_nama_jabatan'] 	= $this->input->post('approval_input_jabatan');
                # END heru mengganti approver ini dari post data , 2020-11-12 @14:40
                
				$data_update['tapping_fix_approval_date'] 			= date('Y-m-d H:i:s');
				
				//16 03 2020 - Tri Wibowo, WORK FROM HOME
				$data_update['wfh'] = $wfh;
				$data_update['wfh_foto_1'] = $edit_wfh_foto[0];
				$data_update['wfh_foto_2'] = $edit_wfh_foto[1];
				
				$update = $this->m_data_kehadiran->update_data_kehadiran($data_update);
					
				if($update=='0')
				{
					$this->session->set_flashdata('warning',"Update Gagal");
				}else
				{
					$this->session->set_flashdata('success',"Update Berhasil, ".$update);
					
					//===== Log Start =====
					$arr_data_baru = $this->m_data_kehadiran->select_kehadiran_by_id($id,$tahun_bulan);
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
						"deskripsi" => "update ".strtolower(preg_replace("//"," ",CLASS_)),
						"kondisi_lama" => $log_data_lama,
						"kondisi_baru" => $log_data_baru,
						"alamat_ip" => $this->data["ip_address"],
						"waktu" => date("Y-m-d H:i:s")
					);
					$this->m_log->tambah($log);
					//===== Log end =====
					
					
				}	
				
				//tembak LEMBUR
				//update id_lembur
				$this->load->model('lembur/m_pengajuan_lembur');
				$get_lembur['no_pokok'] = $np_karyawan;
				$get_lembur['tgl_dws'] = $dws_tanggal;
				$this->m_pengajuan_lembur->set_cico($get_lembur);
				//refresh lembur fix
				$check = $this->m_pengajuan_lembur->update_dws($np_karyawan, $dws_tanggal);
				
				$this->session->set_flashdata('tampil_bulan_tahun',$tampil_bulan_tahun);
				redirect(base_url($this->folder_controller.'data_kehadiran'));				
				
			}else
			{
				$this->session->set_flashdata('warning',"Terjadi Kesalahan");
				
				redirect(base_url($this->folder_controller.'data_kehadiran'));	
			}	
		}
		
		public function action_update_kode_unit()
		{			
			//echo json_encode($this->input->post()); exit();
            $submit = $this->input->post('submit');
					
					
			if($submit)
			{					
				$id 				= $this->input->post('kd_id');
				$np_karyawan 		= $this->input->post('kd_np_karyawan');
				$nama 				= $this->input->post('kd_nama');
				$dws_tanggal 		= date('Y-m-d', strtotime($this->input->post('kd_dws_tanggal')));
				$dws_name 			= $this->input->post('kd_dws_name');			
				$kode_unit 			= $this->input->post('kd_kode_unit');			
							
				$tampil_bulan_tahun	= $this->input->post('kd_tampil_bulan_tahun');
				
				$bulan	= substr($tampil_bulan_tahun,0,2);
				$tahun 	= substr($tampil_bulan_tahun,3,4);
				$tahun_bulan		= $tahun."_".$bulan;
												
				//===== Log Start =====
				$arr_data_lama = $this->m_data_kehadiran->select_kehadiran_by_id($id,$tahun_bulan);
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
				
				$data_update['id'] 			= $id;
				$data_update['np_karyawan'] = $np_karyawan;
				$data_update['nama'] 		= $nama;
				$data_update['dws_tanggal'] = $dws_tanggal;
				$data_update['kode_unit'] 	= $kode_unit;
				$data_update['tahun_bulan'] = $tahun_bulan;
											
				$update = $this->m_data_kehadiran->update_kode_unit($data_update);
					
				if($update=='0')
				{
					$this->session->set_flashdata('warning',"Update Gagal");
				}else
				{
					$this->session->set_flashdata('success',"Update Berhasil, ".$update);
					
					//===== Log Start =====
					$arr_data_baru = $this->m_data_kehadiran->select_kehadiran_by_id($id,$tahun_bulan);
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
						"deskripsi" => "update ".strtolower(preg_replace("//"," ",CLASS_)),
						"kondisi_lama" => $log_data_lama,
						"kondisi_baru" => $log_data_baru,
						"alamat_ip" => $this->data["ip_address"],
						"waktu" => date("Y-m-d H:i:s")
					);
					$this->m_log->tambah($log);
					//===== Log end =====
					
					
				}	
											
				$this->session->set_flashdata('tampil_bulan_tahun',$tampil_bulan_tahun);
				redirect(base_url($this->folder_controller.'data_kehadiran'));				
				
			}else
			{
				$this->session->set_flashdata('warning',"Terjadi Kesalahan");
				
				redirect(base_url($this->folder_controller.'data_kehadiran'));	
			}	
		}
		
		public function cetak() {
			$this->load->library('phpexcel'); 
			$this->load->model($this->folder_model."/M_tabel_data_kehadiran");

			$set['np_karyawan'] = $this->input->post('np_karyawan');
			$tampil_bulan_tahun = $this->input->post('bulan');
			
			if($tampil_bulan_tahun=='')
				$tampil_bulan_tahun = '';				
			else {
				$bulan = substr($tampil_bulan_tahun,0,2);
				$tahun = substr($tampil_bulan_tahun,3,4);
				$tampil_bulan_tahun = $tahun."_".$bulan;
			}
			
			if($_SESSION["grup"]==4) { //jika Pengadministrasi Unit Kerja		
				$ada_data=0;
				$var=array();
				$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
				foreach ($list_pengadministrasi as $data) { //looping list_pengadministrasi
					array_push($var,$data['kode_unit']);
					$ada_data=1;
				}

				if($ada_data==0)
					$var='';
			}
			else if($_SESSION["grup"]==5) //jika Pengguna
				$var 	= $_SESSION["no_pokok"];
			else
				$var = 1;
			$get_data = $this->M_tabel_data_kehadiran->_get_excel($var,$tampil_bulan_tahun,$set);
	        error_reporting(E_ALL);
	        ini_set('display_errors', TRUE);
	        ini_set('display_startup_errors', TRUE);

	        header("Content-type: application/vnd.ms-excel");
	        //nama file
	        header("Content-Disposition: attachment; filename=Data_kehadiran.xlsx");
	        header('Cache-Control: max-age=0');

	        $excel = PHPExcel_IOFactory::createReader('Excel2007');
	        $excel = $excel->load('./asset/Template_data_kehadiran.xlsx');

	        //anggota
	        $excel->setActiveSheetIndex(0);
	        $kolom 	= 2;
	        $awal 	= 4;
	        $no = 1;

			foreach ($get_data as $tampil) {
				$excel->getActiveSheet()->setCellValueExplicit('A'.$awal, $no++, PHPExcel_Cell_DataType::TYPE_STRING);
	            $excel->getActiveSheet()->setCellValueExplicit('B'.$awal, strtoupper($tampil->np_karyawan), PHPExcel_Cell_DataType::TYPE_STRING);
	            $excel->getActiveSheet()->setCellValueExplicit('C'.$awal, ucwords($tampil->nama), PHPExcel_Cell_DataType::TYPE_STRING);
	            $excel->getActiveSheet()->setCellValueExplicit('D'.$awal, date('d-m-Y', strtotime($tampil->dws_tanggal)), PHPExcel_Cell_DataType::TYPE_STRING);
				
				if($tampil->dws_name_fix==null || $tampil->dws_name_fix=='')
	            	$excel->getActiveSheet()->setCellValueExplicit('E'.$awal, strtoupper(nama_dws_by_kode($tampil->dws_name)), PHPExcel_Cell_DataType::TYPE_STRING);
				else
	            	$excel->getActiveSheet()->setCellValueExplicit('E'.$awal, strtoupper(nama_dws_by_kode($tampil->dws_name_fix)), PHPExcel_Cell_DataType::TYPE_STRING);
				
				if($tampil->tapping_fix_1==null || $tampil->tapping_fix_1=='') {
					$tapping_1 = $tampil->tapping_time_1;
					if($tapping_1) {
						$pisah_tapping_1		= explode(' ',$tapping_1);
						$tapping_1_value_date	= $pisah_tapping_1[0];
						$tapping_1_value_time	= $pisah_tapping_1[1];
						$tapping_1_value_time   = substr($tapping_1_value_time,0,5);
						$machine_id_1			= '';
					}
					else {
						$tapping_1_value_date	= $tampil->dws_tanggal;
						$tapping_1_value_time	= '';
					}
				}
				else {
					$tapping_1					= $tampil->tapping_fix_1;
					if($tapping_1) {
						$pisah_tapping_1		= explode(' ',$tapping_1);
						$tapping_1_value_date	= $pisah_tapping_1[0];
						$tapping_1_value_time	= $pisah_tapping_1[1];
						$tapping_1_value_time   = substr($tapping_1_value_time,0,5);
						$machine_id_1			= '';
					}
					else {
						$tapping_1_value_date	= $tampil->dws_tanggal;
						$tapping_1_value_time	= '';
					}					
				}
				
				if($tapping_1)
	            	$excel->getActiveSheet()->setCellValueExplicit('F'.$awal, ucwords(tanggal_indonesia(substr($tapping_1,0,10))." ".substr($tapping_1,10,6)." ".$machine_id_1), PHPExcel_Cell_DataType::TYPE_STRING);
				else
	            	$excel->getActiveSheet()->setCellValueExplicit('F'.$awal, ' ', PHPExcel_Cell_DataType::TYPE_STRING);
				
				if($tampil->tapping_fix_2==null || $tampil->tapping_fix_2=='') {
					$tapping_2  				= $tampil->tapping_time_2;
					if($tapping_2) {
						$pisah_tapping_2		= explode(' ',$tapping_2);
						$tapping_2_value_date	= $pisah_tapping_2[0];
						$tapping_2_value_time	= $pisah_tapping_2[1];
						$tapping_2_value_time   = substr($tapping_2_value_time,0,5);
						//$machine_id_2			= "<br>Machine id : ".$tampil->tapping_terminal_2;
						$machine_id_2			= '';
					}
					else {
						$tapping_2_value_date	= $tampil->dws_tanggal;
						$tapping_2_value_time	= '';
					}					
				}
				else {					
					$tapping_2 					= $tampil->tapping_fix_2;
					if($tapping_2) {
						$pisah_tapping_2		= explode(' ',$tapping_2);
						$tapping_2_value_date	= $pisah_tapping_2[0];
						$tapping_2_value_time	= $pisah_tapping_2[1];
						$tapping_2_value_time   = substr($tapping_2_value_time,0,5);
						$machine_id_2			= "";
					}
					else {
						$tapping_2_value_date	= $tampil->dws_tanggal;
						$tapping_2_value_time	= '';
					}		
				}
				
				if($tapping_2)
	            	$excel->getActiveSheet()->setCellValueExplicit('G'.$awal, ucwords(tanggal_indonesia(substr($tapping_2,0,10))." ".substr($tapping_2,10,6)."  ".$machine_id_2), PHPExcel_Cell_DataType::TYPE_STRING);
				else
	            	$excel->getActiveSheet()->setCellValueExplicit('G'.$awal, '', PHPExcel_Cell_DataType::TYPE_STRING);
				
				$tampil_keterangan 	= '';
				$hari_libur 		= hari_libur_by_tanggal($tampil->dws_tanggal);
								
				if($hari_libur) {
					if($tampil_keterangan=='')
						$tampil_keterangan = $hari_libur;
					else
						$tampil_keterangan = $tampil_keterangan."<br><br>".$hari_libur;
				}
				
				$hari_cuti_bersama = $this->db->query("SELECT * FROM ess_cuti_bersama WHERE tanggal_cuti_bersama='$tampil->dws_tanggal' AND np_karyawan='$tampil->np_karyawan' LIMIT 1")->row_array();
								
				//7648 Tri Wibowo, 6 Januari 2019 - ketika sudah di pembatalan maka tidak tampil
				$hari_pembatalan =  $this->db->query("SELECT * FROM ess_pembatalan_cuti WHERE is_cuti_bersama='1' AND date='$tampil->dws_tanggal' AND np_karyawan='$tampil->np_karyawan' LIMIT 1")->row_array();
				//jika ada pembatalan
				$id_cuti_bersama = null;
				if($hari_pembatalan['id']==null)
				{
					$id_cuti_bersama = $hari_cuti_bersama['id'];
				}
				
				if($tampil->id_cuti) {
					if($tampil_keterangan=='')
						$tampil_keterangan = 'Cuti';
					else
						$tampil_keterangan = $tampil_keterangan.". ".'Cuti';
				}
				else if($tampil->id_sppd) {
					if($tampil_keterangan=='')
						$tampil_keterangan = 'Dinas';
					else
						$tampil_keterangan = $tampil_keterangan.". >".'Dinas';
				}
				else if($id_cuti_bersama) {
					if($tampil_keterangan=='')
						$tampil_keterangan = 'Cuti Bersama';
					else
						$tampil_keterangan = $tampil_keterangan.". ".'Cuti Bersama';
				}
				else{
					$id_perizinan=explode(",",$tampil->id_perizinan);
					$isi='';
					foreach($id_perizinan as $value) {
						$tahun_bulan = substr($tampil->dws_tanggal,0,7);
						$tahun_bulan = str_replace('-','_',$tahun_bulan);
						$izin = perizinan_by_id($tahun_bulan,$value);
						$kode_erp = $izin['info_type']."|".$izin['absence_type'];
						$nama_perizinan=nama_perizinan_by_kode_erp($kode_erp);
						if($nama_perizinan)
							$isi=$isi."".$nama_perizinan.". ";		
					}
					
					if(!$hari_libur) {
						if($tampil_keterangan=='') {
							if ((strpos($isi, 'Izin Dinas Keluar Perusahaan Non-Pendidikan') !== false) || (strpos($isi, 'Izin Dinas Luar Lokasi Pendidikan') !== false) || (strpos($isi, 'Izin Pribadi dengan Potongan') !== false) || (strpos($isi, 'Izin Pribadi Tanpa Potongan') !== false)) {
								$tampil_keterangan =$isi;
							}
							else {	
								if($tampil->keterangan)
									$tampil_keterangan = $tampil->keterangan.". ".$isi;
								else
									$tampil_keterangan =$isi;	
							}
						}
						else {
							if ((strpos($isi, 'Izin Dinas Keluar Perusahaan Non-Pendidikan') !== false) || (strpos($isi, 'Izin Dinas Luar Lokasi Pendidikan') !== false) ||
								(strpos($isi, 'Izin Pribadi dengan Potongan') !== false) || (strpos($isi, 'Izin Pribadi Tanpa Potongan') !== false)) {								
								$tampil_keterangan = $tampil_keterangan.". ".$isi;
							}
							else {
								if($tampil->keterangan)
									$tampil_keterangan = $tampil_keterangan.". ".$tampil->keterangan.". ".$isi;	
								else
									$tampil_keterangan = $tampil_keterangan.". ".$isi;
							}
						}
					}										
				}
				
				if($tampil->wfh==1)
				{
					$tampil_keterangan = "Work From Home".". ".$tampil_keterangan;
				}
				
				$excel->getActiveSheet()->setCellValueExplicit('H'.$awal, $tampil_keterangan, PHPExcel_Cell_DataType::TYPE_STRING);
	            $awal += 1;	
			}

	        $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
	        $objWriter->setIncludeCharts(TRUE);
	        $objWriter->setPreCalculateFormulas(TRUE);
	        PHPExcel_Calculation::getInstance($excel)->clearCalculationCache();
	        $objWriter->save('php://output');
	        exit();
		}
		
		public function cetak_per_unit() {
			$this->load->library('phpexcel'); 
			$this->load->model($this->folder_model."/M_tabel_data_kehadiran");

			$set['kode_unit'] = $this->input->post('kode_unit');
			$tampil_bulan_tahun = $this->input->post('bulan');
			
			if($tampil_bulan_tahun=='')
				$tampil_bulan_tahun = '';				
			else {
				$bulan = substr($tampil_bulan_tahun,0,2);
				$tahun = substr($tampil_bulan_tahun,3,4);
				$tampil_bulan_tahun = $tahun."_".$bulan;
			}
			
			if($_SESSION["grup"]==4) { //jika Pengadministrasi Unit Kerja		
				$ada_data=0;
				$var=array();
				$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
				foreach ($list_pengadministrasi as $data) { //looping list_pengadministrasi
					array_push($var,$data['kode_unit']);
					$ada_data=1;
				}

				if($ada_data==0)
					$var='';
			}
			else if($_SESSION["grup"]==5) //jika Pengguna
				$var 	= $_SESSION["no_pokok"];
			else
				$var = 1;
			$get_data = $this->M_tabel_data_kehadiran->_get_excel_per_unit($var,$tampil_bulan_tahun,$set);
	        error_reporting(E_ALL);
	        ini_set('display_errors', TRUE);
	        ini_set('display_startup_errors', TRUE);

	        header("Content-type: application/vnd.ms-excel");
	        //nama file
	        header("Content-Disposition: attachment; filename=Data_kehadiran.xlsx");
	        header('Cache-Control: max-age=0');

	        $excel = PHPExcel_IOFactory::createReader('Excel2007');
	        $excel = $excel->load('./asset/Template_data_kehadiran.xlsx');

	        //anggota
	        $excel->setActiveSheetIndex(0);
	        $kolom 	= 2;
	        $awal 	= 4;
	        $no = 1;

			foreach ($get_data as $tampil) {
				$excel->getActiveSheet()->setCellValueExplicit('A'.$awal, $no++, PHPExcel_Cell_DataType::TYPE_STRING);
	            $excel->getActiveSheet()->setCellValueExplicit('B'.$awal, strtoupper($tampil->np_karyawan), PHPExcel_Cell_DataType::TYPE_STRING);
	            $excel->getActiveSheet()->setCellValueExplicit('C'.$awal, ucwords($tampil->nama), PHPExcel_Cell_DataType::TYPE_STRING);
	            $excel->getActiveSheet()->setCellValueExplicit('D'.$awal, date('d-m-Y', strtotime($tampil->dws_tanggal)), PHPExcel_Cell_DataType::TYPE_STRING);
				
				if($tampil->dws_name_fix==null || $tampil->dws_name_fix=='')
	            	$excel->getActiveSheet()->setCellValueExplicit('E'.$awal, strtoupper(nama_dws_by_kode($tampil->dws_name)), PHPExcel_Cell_DataType::TYPE_STRING);
				else
	            	$excel->getActiveSheet()->setCellValueExplicit('E'.$awal, strtoupper(nama_dws_by_kode($tampil->dws_name_fix)), PHPExcel_Cell_DataType::TYPE_STRING);
				
				if($tampil->tapping_fix_1==null || $tampil->tapping_fix_1=='') {
					$tapping_1 = $tampil->tapping_time_1;
					if($tapping_1) {
						$pisah_tapping_1		= explode(' ',$tapping_1);
						$tapping_1_value_date	= $pisah_tapping_1[0];
						$tapping_1_value_time	= $pisah_tapping_1[1];
						$tapping_1_value_time   = substr($tapping_1_value_time,0,5);
						//$machine_id_1			= "<br>Machine id : ".$tampil->tapping_terminal_1;
						$machine_id_1			= '';
					}
					else {
						$tapping_1_value_date	= $tampil->dws_tanggal;
						$tapping_1_value_time	= '';
					}
				}
				else {
					$tapping_1					= $tampil->tapping_fix_1;
					if($tapping_1) {
						$pisah_tapping_1		= explode(' ',$tapping_1);
						$tapping_1_value_date	= $pisah_tapping_1[0];
						$tapping_1_value_time	= $pisah_tapping_1[1];
						$tapping_1_value_time   = substr($tapping_1_value_time,0,5);
						$machine_id_1			= '';
					}
					else {
						$tapping_1_value_date	= $tampil->dws_tanggal;
						$tapping_1_value_time	= '';
					}					
				}
				
				if($tapping_1)
	            	$excel->getActiveSheet()->setCellValueExplicit('F'.$awal, ucwords(tanggal_indonesia(substr($tapping_1,0,10))." ".substr($tapping_1,10,6)." ".$machine_id_1), PHPExcel_Cell_DataType::TYPE_STRING);
				else
	            	$excel->getActiveSheet()->setCellValueExplicit('F'.$awal, ' ', PHPExcel_Cell_DataType::TYPE_STRING);
				
				if($tampil->tapping_fix_2==null || $tampil->tapping_fix_2=='') {
					$tapping_2  				= $tampil->tapping_time_2;
					if($tapping_2) {
						$pisah_tapping_2		= explode(' ',$tapping_2);
						$tapping_2_value_date	= $pisah_tapping_2[0];
						$tapping_2_value_time	= $pisah_tapping_2[1];
						$tapping_2_value_time   = substr($tapping_2_value_time,0,5);
						//$machine_id_2			= "<br>Machine id : ".$tampil->tapping_terminal_2;
						$machine_id_2			= '';
					}
					else {
						$tapping_2_value_date	= $tampil->dws_tanggal;
						$tapping_2_value_time	= '';
					}					
				}
				else {					
					$tapping_2 					= $tampil->tapping_fix_2;
					if($tapping_2) {
						$pisah_tapping_2		= explode(' ',$tapping_2);
						$tapping_2_value_date	= $pisah_tapping_2[0];
						$tapping_2_value_time	= $pisah_tapping_2[1];
						$tapping_2_value_time   = substr($tapping_2_value_time,0,5);
						$machine_id_2			= "";
					}
					else {
						$tapping_2_value_date	= $tampil->dws_tanggal;
						$tapping_2_value_time	= '';
					}		
				}
				
				if($tapping_2)
	            	$excel->getActiveSheet()->setCellValueExplicit('G'.$awal, ucwords(tanggal_indonesia(substr($tapping_2,0,10))." ".substr($tapping_2,10,6)."  ".$machine_id_2), PHPExcel_Cell_DataType::TYPE_STRING);
				else
	            	$excel->getActiveSheet()->setCellValueExplicit('G'.$awal, '', PHPExcel_Cell_DataType::TYPE_STRING);
				
				$tampil_keterangan 	= '';
				$hari_libur 		= hari_libur_by_tanggal($tampil->dws_tanggal);
								
				if($hari_libur) {
					if($tampil_keterangan=='')
						$tampil_keterangan = $hari_libur;
					else
						$tampil_keterangan = $tampil_keterangan."<br><br>".$hari_libur;
				}
				
				$hari_cuti_bersama = $this->db->query("SELECT * FROM ess_cuti_bersama WHERE tanggal_cuti_bersama='$tampil->dws_tanggal' AND np_karyawan='$tampil->np_karyawan' LIMIT 1")->row_array();
								
				//7648 Tri Wibowo, 6 Januari 2019 - ketika sudah di pembatalan maka tidak tampil
				$hari_pembatalan =  $this->db->query("SELECT * FROM ess_pembatalan_cuti WHERE is_cuti_bersama='1' AND date='$tampil->dws_tanggal' AND np_karyawan='$tampil->np_karyawan' LIMIT 1")->row_array();
				//jika ada pembatalan
				$id_cuti_bersama = null;
				if($hari_pembatalan['id']==null)
				{
					$id_cuti_bersama = $hari_cuti_bersama['id'];
				}
				
				if($tampil->id_cuti) {
					if($tampil_keterangan=='')
						$tampil_keterangan = 'Cuti';
					else
						$tampil_keterangan = $tampil_keterangan.". ".'Cuti';
				}
				else if($tampil->id_sppd) {
					if($tampil_keterangan=='')
						$tampil_keterangan = 'Dinas';
					else
						$tampil_keterangan = $tampil_keterangan.". ".'Dinas';
				}
				else if($id_cuti_bersama) {
					if($tampil_keterangan=='')
						$tampil_keterangan = 'Cuti Bersama';
					else
						$tampil_keterangan = $tampil_keterangan.". ".'Cuti Bersama';
				}
				else{
					$id_perizinan=explode(",",$tampil->id_perizinan);
					$isi='';
					foreach($id_perizinan as $value) {
						$tahun_bulan = substr($tampil->dws_tanggal,0,7);
						$tahun_bulan = str_replace('-','_',$tahun_bulan);
						$izin = perizinan_by_id($tahun_bulan,$value);
						$kode_erp = $izin['info_type']."|".$izin['absence_type'];
						$nama_perizinan=nama_perizinan_by_kode_erp($kode_erp);
						if($nama_perizinan)
							$isi=$isi."".$nama_perizinan."<br><br>";		
					}
					
					if(!$hari_libur) {
						if($tampil_keterangan=='') {
							if ((strpos($isi, 'Izin Dinas Keluar Perusahaan Non-Pendidikan') !== false) || (strpos($isi, 'Izin Dinas Luar Lokasi Pendidikan') !== false) || (strpos($isi, 'Izin Pribadi dengan Potongan') !== false) || (strpos($isi, 'Izin Pribadi Tanpa Potongan') !== false)) {
								$tampil_keterangan =$isi;
							}
							else {	
								if($tampil->keterangan)
									$tampil_keterangan = $tampil->keterangan.". ".$isi;
								else
									$tampil_keterangan =$isi;	
							}
						}
						else {
							if ((strpos($isi, 'Izin Dinas Keluar Perusahaan Non-Pendidikan') !== false) || (strpos($isi, 'Izin Dinas Luar Lokasi Pendidikan') !== false) ||
								(strpos($isi, 'Izin Pribadi dengan Potongan') !== false) || (strpos($isi, 'Izin Pribadi Tanpa Potongan') !== false)) {								
								$tampil_keterangan = $tampil_keterangan.". ".$isi;
							}
							else {
								if($tampil->keterangan)
									$tampil_keterangan = $tampil_keterangan.". ".$tampil->keterangan.". ".$isi;	
								else
									$tampil_keterangan = $tampil_keterangan.". ".$isi;
							}
						}
					}										
				}
				
				if($tampil->wfh==1)
				{
					$tampil_keterangan = "Work From Home".". ".$tampil_keterangan;
				}
				
				$excel->getActiveSheet()->setCellValueExplicit('H'.$awal, $tampil_keterangan, PHPExcel_Cell_DataType::TYPE_STRING);
	            $awal += 1;	
			}

	        $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
	        $objWriter->setIncludeCharts(TRUE);
	        $objWriter->setPreCalculateFormulas(TRUE);
	        PHPExcel_Calculation::getInstance($excel)->clearCalculationCache();
	        $objWriter->save('php://output');
	        exit();
		}
	}
	
	/* End of file data_kehadiran.php */
	/* Location: ./application/controllers/kehadiran/data_kehadiran.php */