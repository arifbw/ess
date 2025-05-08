<?php defined('BASEPATH') OR exit('No direct script access allowed');
include_once( APPPATH . 'core/Group_Controller.php' );

class Data_kehadiran extends Group_Controller {

	public function __construct(){
		parent::__construct();
        $this->load->helper("tanggal_helper");
        $this->load->helper("perizinan_helper");
        $this->load->model("api/M_kehadiran_api","kehadiran");
        $this->load->model("kehadiran/M_data_kehadiran"); # "$this->M_data_kehadiran->select_cuti_by_id()" butuh nge-load Model ini, ditambahkan 2021-04-07, heru

		$this->load->helper("cutoff_helper");
		$this->load->helper("tanggal_helper");
		$this->load->helper("karyawan_helper");
		$this->load->helper("perizinan_helper");
        
        if(!in_array($this->id_group,[1,2,3,4,5])){
            $this->response([
                'status'=>false,
                'message'=>"Hanya bisa diakses pengguna atau pengadministrasi unit kerja",
                'data'=>[]
            ], MY_Controller::HTTP_FORBIDDEN);
        }
	}
	
	public function index_get() {        
        $data=[];
        
        try {
        	if(empty($this->get('bulan'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Bulan harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(date('Y-m-d') < ($this->get('bulan').'-01')) {
                $this->response([
                    'status'=>false,
                    'message'=>"Bulan tidak valid",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
				$bulan = substr($this->get('bulan'),-2);
				$tahun = substr($this->get('bulan'),0,4);
				$tahun_bulan = $tahun.'_'.$bulan;

        		$np = $this->data_karyawan->np_karyawan;
        		$param = [$np];

	            $get_kehadiran = $this->kehadiran->get_kehadiran($param, $tahun_bulan)->result();
	            
	            foreach ($get_kehadiran as $tampil) {
	                $row=[];
	                $row['id'] = $tampil->id;
	                $row['kode_unit'] = $tampil->kode_unit;
	                $row['np_karyawan'] = $tampil->np_karyawan;
	                $row['nama'] = $tampil->nama;
	                $row['tanggal_dws'] = $tampil->dws_tanggal;
	                $row['hari_tanggal'] = hari_tanggal($tampil->dws_tanggal);
	                
	                # jadwal kerja
	                $jadwal_kerja = "";
					if($tampil->dws_name_fix==null || $tampil->dws_name_fix=='') {
						$jadwal_kerja = nama_dws_by_kode($tampil->dws_name);
					} else {
						$jadwal_kerja = nama_dws_by_kode($tampil->dws_name_fix);
					}
					$row['jadwal_kerja'] = $jadwal_kerja;
					
	                $waktu_kerja = '';
					if(!empty($jadwal_kerja)){
						if(strcmp($jadwal_kerja,"OFF")!=0){			
							$waktu_kerja .= hari_tanggal($tampil->dws_in_tanggal)." ".substr($tampil->dws_in,0,5);
							$waktu_kerja .= " sampai dengan ";
							if(strcmp($tampil->dws_in_tanggal,$tampil->dws_out_tanggal)!=0){
								$waktu_kerja .= hari_tanggal($tampil->dws_out_tanggal)." ";
							}
							$waktu_kerja .= substr($tampil->dws_out,0,5);
						}
					}
					$row['waktu_kerja'] = $waktu_kerja;
	                # END jadwal kerja
	                
	                # berangkat
	                $machine_id_1 = '';
					$machine_id_2 = '';
					
					if($tampil->tapping_fix_1==null || $tampil->tapping_fix_1=='') {
						$tapping_1 = $tampil->tapping_time_1;
						if($tapping_1) {
							$pisah_tapping_1 = explode(' ',$tapping_1);
							$tapping_1_value_date = $pisah_tapping_1[0];
							$tapping_1_value_time = $pisah_tapping_1[1];
							$tapping_1_value_time = substr($tapping_1_value_time,0,5);
							
							$machine_id_1 = "|Machine id : ".$tampil->tapping_terminal_1;
                            
                            # tambahan 2021-04-07, heru
                            $tgl_berangkat = $pisah_tapping_1[0];
                            $waktu_berangkat = $pisah_tapping_1[1];
						} else {
							$tapping_1_value_date = $tampil->dws_tanggal;
							$tapping_1_value_time = '';
                            
                            # tambahan 2021-04-07, heru
                            $tgl_berangkat = $tampil->dws_tanggal;
                            $waktu_berangkat = null;
						}
					} else {					
						$tapping_1 = $tampil->tapping_fix_1;
						
						if($tapping_1) {
							$pisah_tapping_1 = explode(' ',$tapping_1);
							$tapping_1_value_date = $pisah_tapping_1[0];
							$tapping_1_value_time = $pisah_tapping_1[1];
							$tapping_1_value_time = substr($tapping_1_value_time,0,5);
							
							if(substr($tampil->tapping_time_1,0,16) != substr($tampil->tapping_fix_1,0,16)) //dirubah oleh ess
							{
								$machine_id_1 = "|Machine id : ".'ESS';					
								
							} else//tidak dirubah
							{
								$machine_id_1 = "|Machine id : ".$tampil->tapping_terminal_1;
							}
                            
                            # tambahan 2021-04-07, heru
                            $tgl_berangkat = $pisah_tapping_1[0];
                            $waktu_berangkat = $pisah_tapping_1[1];
						} else {
							$tapping_1_value_date = $tampil->dws_tanggal;
							$tapping_1_value_time = '';
                            
                            # tambahan 2021-04-07, heru
                            $tgl_berangkat = $tampil->dws_tanggal;
                            $waktu_berangkat = null;
						}					
					}
					
					if($tapping_1 || $tapping_1=='') {
						if(@$tampil->tapping_fix_1_temp) //check apakah ada perubahan belum di approve
						{
							
							$approval_status_id = $tampil->tapping_fix_approval_status;
							if($approval_status_id==0) {
								$approval_status_1 = "Belum Disetujui ".$tampil->tapping_fix_approval_np;
							} else if($approval_status_id==1) {
								$approval_status_1 = "Disetujui ".$tampil->tapping_fix_approval_np;
							} else if($approval_status_id==2) {
								$approval_status_1 = "Ditolak ".$tampil->tapping_fix_approval_np;
							} else if($approval_status_id==3) {
								$approval_status_1 = "Dibatalkan";
							}
							
							if(substr($tampil->tapping_fix_1_temp,0,16)==(substr($tampil->tapping_time_1,0,16))) //ketika data yg diubah dan yg mau dirubah sama ga usah tampil
							{
								$show_tapping_temp_1 = "";
							} else {
								$show_tapping_temp_1 = tanggal(substr($tampil->tapping_fix_1_temp,0,10))."|".substr($tampil->tapping_fix_1_temp,10,6)."|".$approval_status_1;
							}							
						} else {
							$show_tapping_temp_1 = "";
						}
						$row['berangkat'] = tanggal(substr($tapping_1,0,10))."|".substr($tapping_1,10,6).$machine_id_1."||".$show_tapping_temp_1;
                        
                        # tambahan 2021-04-07, heru
                        if($show_tapping_temp_1!=''){
                            $tgl_berangkat = substr($tampil->tapping_fix_1_temp,0,10);
                            $waktu_berangkat = substr($tampil->tapping_fix_1_temp,10,6);
                        } else{
                            $tgl_berangkat = substr($tapping_1,0,10);
                            $waktu_berangkat = substr($tapping_1,10,6);
                        }
					} else {
						$row['berangkat'] = '';
                        
                        # tambahan 2021-04-07, heru
                        $tgl_berangkat = null;
                        $waktu_berangkat = null;
					}
	                # END berangkat
	                
	                # pulang
	                if($tampil->tapping_fix_2==null || $tampil->tapping_fix_2=='') {
						$tapping_2 = $tampil->tapping_time_2;
						
						if($tapping_2) {
							$pisah_tapping_2 = explode(' ',$tapping_2);
							$tapping_2_value_date = $pisah_tapping_2[0];
							$tapping_2_value_time = $pisah_tapping_2[1];
							$tapping_2_value_time = substr($tapping_2_value_time,0,5);
							
							$machine_id_2 = "|Machine id : ".$tampil->tapping_terminal_2;
                            
                            # tambahan 2021-04-07, heru
                            $tgl_pulang = $pisah_tapping_2[0];
                            $waktu_pulang = $pisah_tapping_2[1];
						} else {
							$tapping_2_value_date = $tampil->dws_tanggal;
							$tapping_2_value_time = '';
                            
                            # tambahan 2021-04-07, heru
                            $tgl_pulang = $tampil->dws_tanggal;
                            $waktu_pulang = null;
						}					
					} else {					
						$tapping_2 = $tampil->tapping_fix_2;
						
						if($tapping_2) {
							$pisah_tapping_2 = explode(' ',$tapping_2);
							$tapping_2_value_date = $pisah_tapping_2[0];
							$tapping_2_value_time = $pisah_tapping_2[1];
							$tapping_2_value_time = substr($tapping_2_value_time,0,5);
							
							if(substr($tampil->tapping_time_2,0,16) != substr($tampil->tapping_fix_2,0,16)) //dirubah oleh ess
							{
								$machine_id_2 = "|Machine id : ".'ESS';
							
							} else //tidak dirubah
							{
								$machine_id_2 = "|Machine id : ".$tampil->tapping_terminal_2;
							}						
                            
                            # tambahan 2021-04-07, heru
                            $tgl_pulang = $pisah_tapping_2[0];
                            $waktu_pulang = $pisah_tapping_2[1];
							
						} else {
							$tapping_2_value_date = $tampil->dws_tanggal;
							$tapping_2_value_time = '';
                            
                            # tambahan 2021-04-07, heru
                            $tgl_pulang = $tampil->dws_tanggal;
                            $waktu_pulang = null;
						}		
					}
					
					if($tapping_2 || $tapping_2=='') {
						if(@$tampil->tapping_fix_2_temp) //check apakah ada perubahan belum di approve
						{						
							$approval_status_id = $tampil->tapping_fix_approval_status;
							if($approval_status_id==0) {
								$approval_status_2 = "Belum Disetujui ".$tampil->tapping_fix_approval_np;
							} else if($approval_status_id==1) {
								$approval_status_2 = "Disetujui ".$tampil->tapping_fix_approval_np;
							} else if($approval_status_id==2) {
								$approval_status_2 = "Ditolak ".$tampil->tapping_fix_approval_np;
							} else if($approval_status_id==3) {
								$approval_status_2 = "Dibatalkan";
							}
							
							if(substr($tampil->tapping_fix_2_temp,0,16)==(substr($tampil->tapping_time_2,0,16))) //ketika data yg diubah dan yg mau dirubah sama ga usah tampil
							{
								$show_tapping_temp_2 = "";
							} else {
								$show_tapping_temp_2 = tanggal(substr($tampil->tapping_fix_2_temp,0,10))."|".substr($tampil->tapping_fix_2_temp,10,6)."|".$approval_status_2;
							} 					
						} else {
							$show_tapping_temp_2 = "";
						}
						
						$row['pulang'] = tanggal(substr($tapping_2,0,10))."|".substr($tapping_2,10,6).$machine_id_2."||".$show_tapping_temp_2;
                        
                        # tambahan 2021-04-07, heru
                        if($show_tapping_temp_2!=''){
                            $tgl_pulang = substr($tampil->tapping_fix_2_temp,0,10);
                            $waktu_pulang = substr($tampil->tapping_fix_2_temp,10,6);
                        } else{
                            $tgl_pulang = substr($tapping_2,0,10);
                            $waktu_pulang = substr($tapping_2,10,6);
                        }
					} else {
						$row['pulang'] = '';
                        
                        # tambahan 2021-04-07, heru
                        $tgl_pulang = null;
                        $waktu_pulang = null;
					}
	                # END pulang
	                
	                # keterangan
	                $tampil_keterangan = '';
					$hari_libur = hari_libur_by_tanggal($tampil->dws_tanggal);
	                
					$hari_cuti_bersama = $this->db->query("SELECT * FROM ess_cuti_bersama WHERE tanggal_cuti_bersama='$tampil->dws_tanggal' AND np_karyawan='$tampil->np_karyawan' LIMIT 1")->row_array();
									
					//7648 Tri Wibowo, 6 Januari 2019 - ketika sudah di pembatalan maka tidak tampil
					$hari_pembatalan =  $this->db->query("SELECT * FROM ess_pembatalan_cuti WHERE is_cuti_bersama='1' AND date='$tampil->dws_tanggal' AND np_karyawan='$tampil->np_karyawan' LIMIT 1")->row_array();
					//jika ada pembatalan
					$id_cuti_bersama = null;
					if($hari_pembatalan['id']==null) {
						$id_cuti_bersama = $hari_cuti_bersama['id'];
					}
		
					if($hari_libur) {
						//$row[] = $hari_libur;
						if($tampil_keterangan=='') {
							$tampil_keterangan = $hari_libur; 
	                    } else {
							$tampil_keterangan = $tampil_keterangan."||".$hari_libur;
						}
					}
					
					if($tampil->id_cuti) {					
						$data_cuti = $this->M_data_kehadiran->select_cuti_by_id($tampil->id_cuti);
						$tampil_cuti = $data_cuti['uraian'];
						
						if($tampil_keterangan=='') {
							$tampil_keterangan = $tampil_cuti;
						} else {
							$tampil_keterangan = $tampil_keterangan."||".$tampil_cuti;
						}
						
					} else if($tampil->id_sppd) {
						if($tampil_keterangan=='') {
							$tampil_keterangan = 'Dinas';
						} else {
							$tampil_keterangan = $tampil_keterangan."||".'Dinas';
						}
					} else if($id_cuti_bersama!=null) {
						if($tampil_keterangan=='') {
							$tampil_keterangan = 'Cuti Bersama';
						} else {
							$tampil_keterangan = $tampil_keterangan."||".'Cuti Bersama';
						}
					} else {
						$id_perizinan=explode(",",$tampil->id_perizinan);
						$isi='';
						foreach($id_perizinan as $value) {
							$tahun_bulan = substr($tampil->dws_tanggal,0,7);
							
							$tahun_bulan = str_replace('-','_',$tahun_bulan);
							
							$izin = perizinan_by_id($tahun_bulan,$value);
							$kode_erp = $izin['info_type']."|".$izin['absence_type'];
							$nama_perizinan=nama_perizinan_by_kode_erp($kode_erp);
							
							if($nama_perizinan) {
								$isi=$isi."".$nama_perizinan."||";
							}							
						}
	                    
						if(!$hari_libur) {
							if($tampil_keterangan=='') {
								if ((strpos($isi, 'Izin Dinas Keluar Perusahaan Non-Pendidikan') !== false) || 
									(strpos($isi, 'Izin Dinas Luar Lokasi Pendidikan') !== false) ||
									(strpos($isi, 'Izin Pribadi dengan Potongan') !== false) ||
									(strpos($isi, 'Izin Pribadi Tanpa Potongan') !== false)
								) {								
									$tampil_keterangan =$isi;
								} else {	
									if($tampil->keterangan) {
	                                    //aslinya ini
	                                    $tampil_keterangan = $tampil->keterangan."||".$isi;
									} else {
										$tampil_keterangan =$isi;
									}
								}
							} else {
								if ((strpos($isi, 'Izin Dinas Keluar Perusahaan Non-Pendidikan') !== false) || 
									(strpos($isi, 'Izin Dinas Luar Lokasi Pendidikan') !== false) ||
									(strpos($isi, 'Izin Pribadi dengan Potongan') !== false) ||
									(strpos($isi, 'Izin Pribadi Tanpa Potongan') !== false)
								) {								
									$tampil_keterangan = $tampil_keterangan."||".$isi;	
								} else {
									if($tampil->keterangan) {
										$tampil_keterangan = $tampil_keterangan."||".$tampil->keterangan."||".$isi;	
									} else {
										$tampil_keterangan = $tampil_keterangan."||".$isi;
									}
								}
							}		
						}					
					}
					
					if($tampil->wfh==1){
						if($tampil->is_dinas_luar==1)
							$tampil_keterangan = "Izin Dinas Luar (WFA)"."||".$tampil_keterangan;
						else
							$tampil_keterangan = "WFH"."||".$tampil_keterangan;
					}
					
					$row['keterangan'] = $tampil_keterangan;
	                # END keterangan
                    
                    # tambahan 2021-04-07, heru
					$row['approval_np'] = $tampil->tapping_fix_approval_np;
					$row['approval_nama'] = $tampil->tapping_fix_approval_nama;
					$row['tgl_berangkat'] = $tgl_berangkat;
					$row['waktu_berangkat'] = trim($waktu_berangkat);
					$row['tgl_pulang'] = $tgl_pulang;
					$row['waktu_pulang'] = trim($waktu_pulang);
                    # END tambahan 2021-04-07, heru

					# tambahan 2021-04-23
					$row['wfh'] = $tampil->wfh;
					$row['wfh_foto_1'] = $tampil->wfh=='1' ? ( is_file("./file/kehadiran2/{$tampil->wfh_foto_1}") ? base_url("file/kehadiran2/{$tampil->wfh_foto_1}") : null ) : null;
					$row['wfh_foto_2'] = $tampil->wfh=='1' ? ( is_file("./file/kehadiran2/{$tampil->wfh_foto_2}") ? base_url("file/kehadiran2/{$tampil->wfh_foto_2}") : null ) : null;
					# END tambahan 2021-04-23

					$row['dws_name'] = ($tampil->dws_name_fix!=null ? $tampil->dws_name_fix : $tampil->dws_name);
					$row['tapping_fix_approval_ket'] = $tampil->tapping_fix_approval_ket;

					# tambahan 2021-04-26
					$row['tapping_fix_approval_status'] = $tampil->tapping_fix_approval_status;
					switch ($tampil->tapping_fix_approval_status) {
						case 0:
							$row['tapping_fix_approval_description'] = 'Belum Disetujui';
						  	break;
						case 1:
							$row['tapping_fix_approval_description'] = 'Disetujui';
						  	break;
						case 2:
							$row['tapping_fix_approval_description'] = 'Ditolak';
						  	break;
						case 3:
							$row['tapping_fix_approval_description'] = 'Dibatalkan';
						  	break;
						default:
							$row['tapping_fix_approval_description'] = null;
					}
					# END tambahan 2021-04-26
	                
	                $data[] = $row;
	            }

	            $this->response([
	                'status'=>true,
	                'message'=>'Success',
	                'data'=>$data
	            ], MY_Controller::HTTP_OK);
	        }

        } catch(Exception $e){
            $this->response([
                'status'=>false,
                'message'=>'Not found',
                'data'=>$data
            ], MY_Controller::HTTP_BAD_REQUEST);
        }
	}	

	public function index_post() {	
		$data=[];
        
        try {
        	if(empty($this->post('id')) || empty($this->post('tanggal')) || empty($this->post('np')) || empty($this->post('approver')) || empty($this->post('tgl_berangkat')) || empty($this->post('tgl_pulang')) || empty($this->post('alasan'))) {
                $this->response([
                    'status'=>false,
                    'message'=>"Semua data harus dilengkapi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else {
        		$id = $this->post('id');
        		$tanggal = $this->post('tanggal');
        		$np = $this->post('np');
        		$approver = $this->post('approver');
        		// $dws = $this->post('dws');
        		$tgl_berangkat = $this->post('tgl_berangkat');
        		$tgl_pulang = $this->post('tgl_pulang');
        		$alasan = $this->post('alasan');

        		$y_m = date('Y_m', strtotime($tanggal));
        		$tabel = "ess_cico_$y_m";
	            $get_kehadiran = $this->kehadiran->by_np($id, $tabel);
                
	            if ($get_kehadiran->num_rows() == 1 && $get_kehadiran->row()->np_karyawan=$np) {
	            	$get_approver = mst_karyawan_by_np($approver);

	            	$set['tapping_fix_1_temp'] = date('Y-m-d H:i:s', strtotime($tgl_berangkat));
	            	$set['tapping_fix_2_temp'] = date('Y-m-d H:i:s', strtotime($tgl_pulang));
					
	            	$set['tapping_fix_approval_status'] = '0';
	            	$set['tapping_fix_approval_np'] = $approver;
	            	$set['tapping_fix_approval_nama'] = $get_approver['nama']; //$data['nama'];
	            	$set['tapping_fix_approval_nama_jabatan'] = $get_approver['nama_jabatan']; //$data['nama_jabatan'];
	            	$set['tapping_fix_approval_ket'] = $alasan;

					//if(strtotime($tgl_berangkat)<=strtotime($tgl_pulang)) { bener ini if nya??
					if(strtotime($tgl_pulang)<strtotime($tgl_berangkat)) {
						$this->response([
		                    'status'=>false,
		                    'message'=>"Semua data harus dilengkapi",
		                    'data'=>[]
		                ], MY_Controller::HTTP_BAD_REQUEST);
					} else {

						# tambahan wfh, foto, 2021-04-23
						if( @$this->post('wfh') ){
							$is_wfh = ($this->post('wfh')=='1' ? '1':'0');
							$set['wfh'] = $is_wfh;
							if( $is_wfh=='1' ){
								# ambil file sebelumnya
								$wfh_files = $this->db->select('wfh_foto_1, wfh_foto_2')->where('id',$id)->get($tabel)->row();

								$this->load->library(array('upload'));
								$this->load->helper(array('form', 'url'));
								
								$config['upload_path'] = 'file/kehadiran2/';			
								$config['allowed_types'] = 'gif|jpg|jpeg|png';
								$config['max_size']	= '2000';	
								$config['encrypt_name'] = true;

								$message_upload = 'Tidak ada file baru yang diupload';

								$this->upload->initialize($config);
								if( $_FILES['wfh_foto_1'] ){
									$message_upload = '';
									if( $this->upload->do_upload('wfh_foto_1') ){
										$up=$this->upload->data();
										$set['wfh_foto_1'] = $up['file_name'];

										$wfh_file_1 = './file/kehadiran2/'.$wfh_files->wfh_foto_1;
										if( is_file($wfh_file_1) )
											unlink($wfh_file_1);
										$message_upload .= "File WFH 1 ({$up['file_name']}) telah diupload";
									} else{
										$error1 =$this->upload->display_errors();
										$message_upload .= $error1;
									}
								}

								if( $_FILES['wfh_foto_2'] ){
									$message_upload = empty($_FILES['wfh_foto_1']) ? '' : $message_upload;
									if( $this->upload->do_upload('wfh_foto_2') ){
										$up=$this->upload->data();
										$set['wfh_foto_2'] = $up['file_name'];

										$wfh_file_2 = './file/kehadiran2/'.$wfh_files->wfh_foto_2;
										if( is_file($wfh_file_2) )
											unlink($wfh_file_2);
										$message_upload .= ($message_upload!='' ? " | File WFH 2 ({$up['file_name']}) telah diupload":"File WFH 1 ({$up['file_name']}) telah diupload");
									} else{
										$error2 =$this->upload->display_errors();
										$message_upload .= ($message_upload!='' ? " | $error2":$error2);
									}
								}
							}
						}
						# END tambahan wfh, foto

						//===== Log Start =====
						$arr_data_lama = $get_kehadiran->row_array();
						$log_data_lama = "";				
						foreach($arr_data_lama as $key => $value){
							if(strcmp($key,"id")!=0){
								if(!empty($log_data_lama)){
									$log_data_lama .= "<br>";
								}
								$log_data_lama .= "$key = $value";
							}
						}
						//===== Log End =====

	            		$this->db->set($set)->where('id', $id)->update($tabel);
	            		if ($this->db->affected_rows() > 0) {
							//===== Log Start =====
							$arr_data_baru = $this->kehadiran->by_np($id, $tabel)->row_array();
							$log_data_baru = "";					
							foreach($arr_data_baru as $key => $value){
								if(strcmp($key,"id")!=0){
									if(!empty($log_data_baru)){
										$log_data_baru .= "<br>";
									}
									$log_data_baru .= "$key = $value";
								}
							}			
							//===== Log End =====

							/*$log = array(
								"id_pengguna" => $this->account->id,
								"id_modul" => $this->data['id_modul'],
								"id_target" => $id,
								"deskripsi" => "update ".strtolower(preg_replace("/_/"," ",__CLASS__)),
								"kondisi_lama" => $log_data_lama,
								"kondisi_baru" => $log_data_baru,
								"alamat_ip" => $this->data["ip_address"],
								"waktu" => date("Y-m-d H:i:s")
							);
							$this->m_log->tambah($log);*/
							//===== Log end =====

	            			$get_new = $this->kehadiran->by_np($id, $tabel)->row();
							$response = [
			                    'status'=>true,
			                    'message'=>"Success",
			                    'data'=>$get_new
			                ];
							if( @$message_upload )
								$response['file_wfh'] = $message_upload;
		            		$this->response($response, MY_Controller::HTTP_OK);
						} else {
		            		$this->response([
			                    'status'=>false,
			                    'message'=>"Gagal mengupadate data",
			                    'data'=>[]
			                ], MY_Controller::HTTP_BAD_REQUEST);
						}
					}
	            } else {
	        		$this->response([
	                    'status'=>false,
	                    'message'=>"Data tidak ditemukan",
	                    'data'=>[]
	                ], MY_Controller::HTTP_BAD_REQUEST);
	            }
	        }
	    } catch(Exception $e){
            $this->response([
                'status'=>false,
                'message'=>'Not found',
                'data'=>$data
            ], MY_Controller::HTTP_BAD_REQUEST);
        }
    }
}

/* End of file data_kehadiran.php */
/* Location: ./application/controllers/kehadiran/data_kehadiran.php */