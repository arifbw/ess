<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Kehadiran2 extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->model('api/M_kehadiran_api','kehadiran');
        $this->load->helper("karyawan_helper");
        $this->load->helper("perizinan_helper");
    }
    
	function index_get() {        
        $np = $this->data_karyawan->np_karyawan;
        $param = [$np];
        $data=[];
        
        try {
            $get_kehadiran = $this->kehadiran->get_kehadiran($param)->result();
            
            foreach ($get_kehadiran as $tampil) {
                $row=[];
                $row['kode_unit'] = $tampil->kode_unit;
                $row['np_karyawan'] = $tampil->np_karyawan;
                $row['nama'] = $tampil->nama;
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
                
                $data[] = $row;
            }
            
            $this->response([
                'status'=>true,
                'message'=>'Success',
                'data'=>$data
            ], MY_Controller::HTTP_OK);
        } catch(Exception $e){
            $this->response([
                'status'=>false,
                'message'=>'Not found',
                'data'=>$data
            ], MY_Controller::HTTP_BAD_REQUEST);
        }
	}
}
