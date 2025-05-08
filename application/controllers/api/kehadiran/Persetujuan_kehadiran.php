<?php defined('BASEPATH') OR exit('No direct script access allowed');
include_once( APPPATH . 'core/Group_Controller.php' );

class Persetujuan_kehadiran extends Group_Controller {

	public function __construct(){
		parent::__construct();
        $this->load->helper("tanggal_helper");
        $this->load->helper("perizinan_helper");
        $this->load->model("api/M_kehadiran_api","kehadiran");
		$this->load->model("kehadiran/m_persetujuan_kehadiran");
		$this->load->model("kehadiran/M_data_kehadiran", 'm_data_kehadiran');

		$this->load->helper("cutoff_helper");
		$this->load->helper("tanggal_helper");
		$this->load->helper("karyawan_helper");
		$this->load->helper("reference_helper");
        
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
            } else if(empty($this->get('np'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Bulan harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
        		$np = $this->get('np');
        		if ($np!='')
        			$np = $this->get('np');
        		else 
        			$np = $this->data_karyawan->np_karyawan;
        		$param = [$np];
        		$bulan = substr($this->get('bulan'),-2);
				$tahun = substr($this->get('bulan'),0,4);
				$tahun_bulan = $tahun.'_'.$bulan;

	            $get_kehadiran = $this->kehadiran->persetujuan_kehadiran($param, $tahun_bulan)->result();

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
						} else {
							$tapping_1_value_date = $tampil->dws_tanggal;
							$tapping_1_value_time = '';
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
						} else {
							$tapping_1_value_date = $tampil->dws_tanggal;
							$tapping_1_value_time = '';
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
					} else {
						$row['berangkat'] = '';
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
						} else {
							$tapping_2_value_date = $tampil->dws_tanggal;
							$tapping_2_value_time = '';
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
							
						} else {
							$tapping_2_value_date = $tampil->dws_tanggal;
							$tapping_2_value_time = '';
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
					} else {
						$row['pulang'] = '';
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
						$data_cuti = $this->m_data_kehadiran->select_cuti_by_id($tampil->id_cuti);
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
						$tampil_keterangan = "WFH"."||".$tampil_keterangan;
					}
					
					$row['keterangan'] = $tampil_keterangan;
	                # END keterangan

	                $id					= trim($tampil->id);
					$np_karyawan		= trim($tampil->np_karyawan);
					$nama				= trim($tampil->nama);
					$approval_np		= trim($tampil->tapping_fix_approval_np);
					$approval_nama		= trim($tampil->tapping_fix_approval_nama);		
					$approval_status	= trim($tampil->tapping_fix_approval_status);	
					$approval_date		= trim($tampil->tapping_fix_approval_date);	
					$tapping_fix_1_temp	= trim($tampil->tapping_fix_1_temp);	
					$tapping_fix_2_temp	= trim($tampil->tapping_fix_2_temp);

					if($approval_status=='1') {
						$approval_1_status 	= "kehadiran Telah disetujui pada $approval_date."; 
					}else if($approval_status=='2') {				
						$approval_1_status 	= "kehadiran TIDAK disetujui pada $approval_date."; 
					}else if($approval_status=='3') {
						$approval_1_status 	= "Permohonan kehadiran Dibatalkan oleh pemohon pada $approval_date."; 
					}else if($approval_status==''||$approval_status=='0') {			
						$approval_status = '0';
						$approval_1_status 	= "kehadiran BELUM disetujui."; 
					}
					
					$row['status_approval'] = $approval_status;
					$row['keterangan_approval'] = $approval_1_status;

					$btn_text		='menunggu persetujuan';

					if(($approval_status=='' || $approval_status=='0' || $approval_status == null)) { //menunggu atasan 1
						$btn_text		='Menunggu Atasan';
					}
					if(($approval_status=='1')) { //disetujui atasan
						$btn_text		='Disetujui Atasan';
					}
					if(($approval_status=='2')) { //ditolak atasan
						$btn_text		='Ditolak Atasan';
					}					
					if($approval_status=='3') { //dibatalkan
						$btn_text		='Dibatalkan';
					}

					$row['status_kehadiran'] = $btn_text;
					
					//cutoff ERP
					$sudah_cutoff = sudah_cutoff($tampil->dws_tanggal);
					
					if($sudah_cutoff) { //jika sudah lewat masa cutoff
						$row['is_aksi'] = '0';
						$row['keterangan_aksi'] = 'Tidak bisa melakukan aksi setelah Cutoff data di ERP pada $sudah_cutoff';
					} else {
						$referensi_data_pamlek='';
						$ambil_data_pamlek = $this->m_persetujuan_kehadiran->referensi_pamlek_by_tanggal($tampil->dws_tanggal,$tampil->np_karyawan);

						foreach ($ambil_data_pamlek->result_array() as $pam_dat) {						
							if($pam_dat['in_out']=='1') {
								$in_out="Masuk";
							} else {
								$in_out="Keluar";
							}
							
							if($pam_dat['nama']=='Izin Datang Terlambat') {
								$nama_tapping = 'Kehadiran';
							} else {
								$nama_tapping=$pam_dat['nama'];
							}
							
							$referensi_data_pamlek=$referensi_data_pamlek.$pam_dat['tapping_time'].' - '.$in_out.' - '.$nama_tapping."\n";
						}
					
						$row['is_aksi'] = '1';
						$row['keterangan_aksi'] = $referensi_data_pamlek;
					}
					$row['is_dinas_luar'] = (string)(int)$tampil->is_dinas_luar;

					# tambahan 2021-04-23
					$row['wfh'] = $tampil->wfh;
					$row['wfh_foto_1'] = $tampil->wfh=='1' ? ( is_file("./file/kehadiran2/{$tampil->wfh_foto_1}") ? base_url("file/kehadiran2/{$tampil->wfh_foto_1}") : null ) : null;
					$row['wfh_foto_2'] = $tampil->wfh=='1' ? ( is_file("./file/kehadiran2/{$tampil->wfh_foto_2}") ? base_url("file/kehadiran2/{$tampil->wfh_foto_2}") : null ) : null;
					# END tambahan 2021-04-23

					$row['dws_name'] = ($tampil->dws_name_fix!=null ? $tampil->dws_name_fix : $tampil->dws_name);
					$row['tapping_fix_approval_ket'] = $tampil->tapping_fix_approval_ket;
	                
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
        	if(empty($this->post('id')) || empty($this->post('tanggal')) || empty($this->post('status'))) {
                $this->response([
                    'status'=>false,
                    'message'=>"Data harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else {
        		$tanggal = $this->post('tanggal');
        		$y_m = date('Y_m', strtotime($tanggal));
        		$tabel = "ess_cico_$y_m";
        		$id = $this->post('id');

	            $get_kehadiran = $this->kehadiran->by_np($id, $tabel);
                
	            if ($get_kehadiran->num_rows() == 1) {
	            	$data = $get_kehadiran->row();

	            	// if ($data->tapping_fix_approval_status == '0') {
	            		$set['tapping_fix_approval_date'] = date('Y-m-d H:i:s');

	            		// if (($data->tapping_fix_1_temp!='' && $data->tapping_fix_1_temp!=null && $data->tapping_fix_1_temp!='0000-00-00 00:00:00') && ($data->tapping_fix_1=='' || $data->tapping_fix_1==null || $data->tapping_fix_1=='0000-00-00 00:00:00')) { 2021-04-28, diganti bawahnya karena tapping_fix tidak harus kosong
	            		if (($data->tapping_fix_1_temp!='' && $data->tapping_fix_1_temp!=null && $data->tapping_fix_1_temp!='0000-00-00 00:00:00')) {
	            			$set['tapping_fix_1'] = $data->tapping_fix_1_temp;
	            			$set['tapping_fix_approval_status'] = $this->post('status');
	            			$set['tapping_fix_1_temp'] = null;
	            		}
	            		// if (($data->tapping_fix_2_temp!='' && $data->tapping_fix_2_temp!=null && $data->tapping_fix_2_temp!='0000-00-00 00:00:00') && ($data->tapping_fix_2=='' || $data->tapping_fix_2==null || $data->tapping_fix_2=='0000-00-00 00:00:00')) { 2021-04-28, diganti bawahnya karena tapping_fix tidak harus kosong
	            		if (($data->tapping_fix_2_temp!='' && $data->tapping_fix_2_temp!=null && $data->tapping_fix_2_temp!='0000-00-00 00:00:00')) {
	            			$set['tapping_fix_2'] = $data->tapping_fix_2_temp;
	            			$set['tapping_fix_approval_status'] = $this->post('status');
	            			$set['tapping_fix_2_temp'] = null;
	            		}

	            		if ($this->post('status')!='1') {
	            			$set['tapping_fix_approval_alasan'] = $this->post('alasan');
	            		} else {
	            			$set['tapping_fix_approval_alasan'] = null;	
	            		}

	            		$where['id'] = $data->id;

						//===== Log Start =====
						$arr_data_lama = $this->m_persetujuan_kehadiran->select_kehadiran_by_id($id,$y_m);
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

	            		$this->db->set($set)->where($where)->update($tabel);
	            		if ($this->db->affected_rows() > 0) {
							//===== Log Start =====
							$arr_data_baru = $this->m_persetujuan_kehadiran->select_kehadiran_by_id($id,$y_m);
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

							/*
							2021-04-26, dicomment
							$log = array(
								"id_pengguna" => $this->account->id,
								"id_modul" => $this->data['id_modul'], # line ini bikin error
								"id_target" => $id,
								"deskripsi" => "Setuju ".strtolower(preg_replace("/_/"," ",__CLASS__)),
								"kondisi_lama" => $log_data_lama,
								"kondisi_baru" => $log_data_baru,
								"alamat_ip" => $this->data["ip_address"], # line ini bikin error
								"waktu" => date("Y-m-d H:i:s")
							);
							$this->m_log->tambah($log);*/
							//===== Log end =====

	            			$get_new = $this->kehadiran->by_np($id, $tabel)->row();
		            		$this->response([
			                    'status'=>true,
			                    'message'=>"Success",
			                    'data'=>$get_new
			                ], MY_Controller::HTTP_OK);
						} else {
		            		$this->response([
			                    'status'=>false,
			                    'message'=>"Gagal mengupadate data",
			                    'data'=>[]
			                ], MY_Controller::HTTP_BAD_REQUEST);
						}
	            	// } else {
	            	// 	$this->response([
		            //         'status'=>false,
		            //         'message'=>"Kehadiran telah diapprove",
		            //         'data'=>[]
		            //     ], MY_Controller::HTTP_BAD_REQUEST);
	            	// }
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