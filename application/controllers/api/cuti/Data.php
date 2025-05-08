<?php defined('BASEPATH') OR exit('No direct script access allowed');
include_once( APPPATH . 'core/Group_Controller.php' );
class Data extends Group_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->helper("tanggal_helper");
        $this->load->helper("karyawan_helper");
        $this->load->helper("cuti_helper");
        $this->load->model("api/filter/M_filter_api","filter");
        $this->load->model("api/cuti/M_cuti_api","cuti");
        $this->load->model("cuti/m_permohonan_cuti");
    }
    
    function index_get(){
        $data=[];
        $params=[];
        try {
            if(empty($this->get('bulan'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Bulan harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                $bulan = substr($this->get('bulan'),-2);
				$tahun = substr($this->get('bulan'),0,4);
                
                $params['tahun_bulan'] = $this->get('bulan');
                
                if($this->id_group==5){
                    $params['np'] = [$this->data_karyawan->np_karyawan];
                } else if($this->id_group==4){
                    $list = [];
                    foreach($this->list_pengadministrasi as $l){
                        $list[] = $l['kode_unit'];
                    }
                    $params['kode_unit'] = $list;
                }
                
                $no=0;
                $get_data_cuti = $this->cuti->get_cuti($params)->result();
                foreach($get_data_cuti as $tampil){
                    $row=[];
                    $no++;
                    $row['no'] = $no;
                    $row['id'] = $tampil->id;
                    $row['tanggal'] = $tampil->start_date!=null?$tampil->start_date:($tampil->end_date!=null?$tampil->end_date:null);
                    $row['np_karyawan'] = $tampil->np_karyawan;
                    $row['nama'] = $tampil->nama;

                    // uraian
                    if($tampil->is_cuti_bersama=='1') {
                        if(in_array($tampil->absence_type, ['2001|1000','2001|1010'])) $row['uraian'] = "Cuti Bersama (Memotong {$tampil->uraian})";
                        else if($tampil->absence_type == '2001|2080') $row['uraian'] = "Cuti Bersama (Mengambil {$tampil->uraian})";
                        else $row['uraian'] = $tampil->uraian;
                    } else $row['uraian'] = $tampil->uraian;
                    
                    if($tampil->start_date) {
                        $row['start_date'] = tanggal_indonesia($tampil->start_date);
                    } else {
                        $row['start_date'] = '';
                    }

                    if($tampil->end_date) {
                        $row['end_date'] = tanggal_indonesia($tampil->end_date);
                    } else {
                        $row['end_date'] = '';
                    }
                    
                    if($tampil->jumlah_bulan) {
                        $row['durasi'] = $tampil->jumlah_bulan." bulan ".$tampil->jumlah_hari." hari";		
                    } else {
                        $row['durasi'] = $tampil->jumlah_hari." hari ";
                    }
                    $row['alasan'] = $tampil->alasan;
                    $row['keterangan'] = $tampil->keterangan=='1' ? 'Dalam Kota':($tampil->keterangan=='2'?'Luar Kota':'');
                    
                    $row['status_cuti'] = status_cuti([
                        'status_1'=>$tampil->status_1,
                        'status_2'=>$tampil->status_2,
                        'approval_2'=>$tampil->approval_2,
                        'approval_sdm'=>$tampil->approval_sdm
                    ]);
                    $row['dapat_dibatalkan'] = $tampil->status_1=='3'?false:($tampil->status_2=='3'?false:true);
                    
                    $data[]=$row;
                }
                
                $this->response([
                    'status'=>true,
                    'message'=>'Data cuti bulan '.id_to_bulan($bulan)." ".$tahun,
                    'data'=>$data
                ], MY_Controller::HTTP_OK);
            }
        } catch(Exception $e){
            $this->response([
                'status'=>false,
                'message'=>'Error Exception',
                'data'=>$data
            ], MY_Controller::HTTP_BAD_REQUEST);
        }
    }
    
    function index_post(){
        $data = [];
        $data_insert = [];
        
        try {
            if(empty($this->post('np'))){
                $this->response([
                    'status'=>false,
                    'message'=>"NP harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($this->post('kode_erp'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Jenis cuti harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($this->post('start_date'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Tanggal awal harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($this->post('end_date'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Tanggal akhir harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($this->post('alasan'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Alasan harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($this->post('approval_1'))){
                $this->response([
                    'status'=>false,
                    'message'=>"NP Approver 1 harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($this->post('approval_1_jabatan'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Jabatan Approver 1 harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                
                # validasi tanggal akhir tidak boleh kurang dari tanggal awal
                if(date('Y-m-d', strtotime($this->post('end_date'))) < date('Y-m-d', strtotime($this->post('start_date')))){
                    $this->response([
                        'status'=>false,
                        'message'=>"Tanggal akhir lebih kecil dari tanggal awal.",
                        'data'=>$cek_exist->result()
                    ], MY_Controller::HTTP_BAD_REQUEST); exit;
                }

                $absence_type = $this->post('kode_erp');
                $type_cuber = $this->post('type_cuber');
                
                $data_karyawan = $this->filter->get_data_karyawan_by_np($this->post('np'))->row();
                $np_karyawan = $this->post('np');
                $start_date = $this->post('start_date');
                $end_date = $this->post('end_date');
                $date1 = new DateTime($start_date);
                $date2 = new DateTime($end_date);
                $jumlah_hari = abs($date2->diff($date1)->format("%a"))+1;

                # cek masa jabatan by tanggal masuk
                $givenDate = new DateTime($data_karyawan->tanggal_masuk);
                $now = new DateTime();
                $thirtyDaysAgo = $now->sub(new DateInterval('P30D'));
                if($givenDate > $thirtyDaysAgo || $data_karyawan->kontrak_kerja=='PKWT'){
                    if($givenDate > $thirtyDaysAgo) {
                        $allowed_cuti = ['2001|2040','2001|2065','2001|2064','2001|2068','2001|2030'];
                        if(!in_array($absence_type, $allowed_cuti)){
                            $this->response([
                                'status'=>false,
                                'message'=>"Pengajuan cuti bisa dilakukan di bulan berikutnya terhitung tanggal masuk : {$data_karyawan->tanggal_masuk}",
                                'data'=>[]
                            ], MY_Controller::HTTP_BAD_REQUEST); exit;
                        }
                    } else if($data_karyawan->kontrak_kerja=='PKWT') {
                        $allowed_cuti = ['2001|2040','2001|2065','2001|2064','2001|2068','2001|2030','2001|1000','2001|1020'];
                        if(!in_array($absence_type, $allowed_cuti) && ($absence_type=='2001|1020' && $type_cuber!='2001|1000')){
                            $this->response([
                                'status'=>false,
                                'message'=>"Pengajuan cuti bisa dilakukan di bulan berikutnya terhitung tanggal masuk : {$data_karyawan->tanggal_masuk}",
                                'data'=>[]
                            ], MY_Controller::HTTP_BAD_REQUEST); exit;
                        }
                    }
                }
                # END cek masa jabatan by tanggal masuk
                
                # check cuti tahunan
				if($absence_type=='2001|1000') {
					$cuti_tahunan_menunggu_sdm = cuti_tahunan_menunggu_sdm($np_karyawan);
					if($cuti_tahunan_menunggu_sdm=='') {
						$cuti_tahunan_menunggu_sdm=0;
					}
                    
					$sisa_cuti = sisa_cuti_tahunan($np_karyawan);
					if($sisa_cuti=='') {
						$sisa_cuti=0;
					}
                    
					$cuti_tahunan_menunggu_cutoff  = cuti_tahunan_menunggu_cutoff($np_karyawan);
					if($cuti_tahunan_menunggu_cutoff=='') {
						$cuti_tahunan_menunggu_cutoff=0;
					}
					
					$permintaan = $cuti_tahunan_menunggu_sdm+$cuti_tahunan_menunggu_cutoff+$jumlah_hari;
					
					if($sisa_cuti < $permintaan) {
						$this->response([
                            'status'=>false,
                            'message'=>"Sisa Cuti Tahunan tidak mencukupi.",
                            'data'=>[
                                'NP'=>$np_karyawan,
                                'Jumlah hari permohonan'=>$jumlah_hari,
                                'Sisa cuti tahunan'=>$sisa_cuti,
                                'Cuti menunggu persetujuan SDM'=>$cuti_tahunan_menunggu_sdm,
                                'Cuti menunggu cutoff'=>$cuti_tahunan_menunggu_cutoff
                            ]
                        ], MY_Controller::HTTP_BAD_REQUEST); exit;
					}
				}
                
                # validasi NP
                if($np_karyawan==$this->post('approval_1')) {
                    $this->response([
                        'status'=>false,
                        'message'=>"NP Atasan harus berbeda dengan NP Pemohon.",
                        'data'=>[]
                    ], MY_Controller::HTTP_BAD_REQUEST); exit;
				}
                
                # check sisa cuti untuk CUTI TAHUNAN
				if($absence_type=='2001|1000') {
					$sisa_cuti = sisa_cuti_tahunan($np_karyawan);
					if($jumlah_hari==0) {
                        $this->response([
                            'status'=>false,
                            'message'=>"Permohonan Cuti tidak boleh <= 0.",
                            'data'=>[
                                'NP'=>$np_karyawan,
                                'Sisa cuti tahunan'=>$sisa_cuti,
                                'Permohonan Cuti tahunan'=>$jumlah_hari
                            ]
                        ], MY_Controller::HTTP_BAD_REQUEST); exit;
					} else if($sisa_cuti<=0 || $sisa_cuti<$jumlah_hari) {
                        $this->response([
                            'status'=>false,
                            'message'=>"Sisa cuti tahunan tidak mencukupi.",
                            'data'=>[
                                'NP'=>$np_karyawan,
                                'Cuti tahunan'=>$sisa_cuti,
                                'Permohonan Cuti tahunan'=>$jumlah_hari
                            ]
                        ], MY_Controller::HTTP_BAD_REQUEST); exit;
					}
				}
                
                # check sisa cuti untuk CUTI BESAR
				if($absence_type=='2001|1010') {
                    $jumlah_bulan = @$this->post('jumlah_bulan') ? $this->post('jumlah_bulan'):0;
                    $data_insert['jumlah_bulan'] = $jumlah_bulan;
                    
					$cuti_besar_menunggu_sdm = cuti_besar_menunggu_sdm($np_karyawan);
					
					if($cuti_besar_menunggu_sdm['menunggu_sdm_bulan']=='') {
						$menunggu_sdm_bulan = 0;
					} else {
						$menunggu_sdm_bulan = $cuti_besar_menunggu_sdm['menunggu_sdm_bulan'];
					}
					
					if($cuti_besar_menunggu_sdm['menunggu_sdm_hari']=='') {
						$menunggu_sdm_hari = 0;
					} else {
                        $menunggu_sdm_hari = $cuti_besar_menunggu_sdm['menunggu_sdm_hari'];
					}
					
					# sisa cuti yang bisa dipakai
					$sisa_cuti	= sisa_cuti_besar($np_karyawan);
					$sisa_bulan	= $sisa_cuti['bulan']-$menunggu_sdm_bulan;
					$sisa_hari	= $sisa_cuti['hari']-$menunggu_sdm_hari;
					
					$sisa_cuti_bulan = $sisa_bulan;
					$sisa_cuti_hari = $sisa_hari;
					
					if(($sisa_cuti_bulan < $jumlah_bulan || $sisa_cuti_hari < $jumlah_hari)) {
                        $this->response([
                            'status'=>false,
                            'message'=>"Sisa cuti Besar tidak mencukupi.",
                            'data'=>[
                                'NP'=>$np_karyawan,
                                'Sisa Cuti Besar'=>"$sisa_cuti_bulan bulan dan $sisa_cuti_hari hari",
                                'Permohonan Cuti besar'=>"$jumlah_bulan bulan dan $jumlah_hari hari"
                            ]
                        ], MY_Controller::HTTP_BAD_REQUEST); exit;
					}
				}
                
                # check validasi hari cuti
				if(in_array($absence_type, ['2001|2060','2001|2066','2001|2061','2001|2062','2001|2063','2001|2068','2001|2067','2001|2064','2001|2065'])) {
                    switch ($absence_type) {
                        case "2001|2060":
                            $hari_cuti = 3;
                            break;
                        case "2001|2066":
                            $hari_cuti = 1;
                            break;
                        default:
                            $hari_cuti = 2;
                    }
					
					if($jumlah_hari > $hari_cuti) {
						$tipe = $this->m_permohonan_cuti->get_absence($absence_type);
                        $this->response([
                            'status'=>false,
                            'message'=>"Jumlah Hari $tipe Maksimal $hari_cuti Hari.",
                            'data'=>[]
                        ], MY_Controller::HTTP_BAD_REQUEST); exit;
					}
				}
                
                # data karyawan
                $data_insert['np_karyawan'] = $this->post('np');
                $data_insert['personel_number'] = @$data_karyawan->personnel_number?$data_karyawan->personnel_number:null;
                $data_insert['nama'] = @$data_karyawan->nama?$data_karyawan->nama:null;
                $data_insert['nama_jabatan'] = @$data_karyawan->nama_jabatan?$data_karyawan->nama_jabatan:null;
                $data_insert['kode_unit'] = @$data_karyawan->kode_unit?$data_karyawan->kode_unit:null;
                $data_insert['nama_unit'] = @$data_karyawan->nama_unit?$data_karyawan->nama_unit:null;
                
                # data cuti
                if($absence_type=='2001|1020'){
                    $absence_type = $type_cuber;
                    $is_cuti_bersama = '1';
                } else{
                    $is_cuti_bersama = '0';
                }
                $data_insert['absence_type'] = $absence_type;
                $data_insert['start_date'] = $this->post('start_date');
                $data_insert['end_date'] = $this->post('end_date');
                $data_insert['jumlah_hari'] = $jumlah_hari;
                
                $data_insert['alasan'] = $this->post('alasan');
                $data_insert['keterangan'] = @$this->post('keterangan')?$this->post('keterangan'):'1'; // default '1': dalam kota
                $data_insert['created_at'] = date('Y-m-d H:i:s');
                $data_insert['created_by'] = $this->data_karyawan->np_karyawan;
                $data_insert['is_migrasi'] = '0';
                $data_insert['is_cuti_bersama'] = $is_cuti_bersama;
                
                # approval
                $data_insert['approval_1'] = $this->post('approval_1');
                $data_insert['approval_1_jabatan'] = $this->post('approval_1_jabatan');
                
                if(!empty($this->post('approval_2'))){
                    if($this->post('approval_1')==$this->post('approval_2')){
                        $this->response([
                            'status'=>false,
                            'message'=>"NP Atasan tidak boleh sama.",
                            'data'=>[]
                        ], MY_Controller::HTTP_BAD_REQUEST); exit;
                    } else{
                        $data_insert['approval_2'] = $this->post('approval_2');
                        $data_insert['approval_2_jabatan'] = $this->post('approval_2_jabatan');
                    }
                } else{
                    $data_insert['approval_2'] = null;
                    $data_insert['approval_2_jabatan'] = null;
                }
                
                $cek_exist = $this->db->where("(np_karyawan='$np_karyawan' AND (('$start_date' BETWEEN start_date AND end_date) OR ('$end_date' BETWEEN start_date AND end_date)) AND (status_1='0' OR status_1='1' OR status_2='1' OR approval_sdm='1'))")->get('ess_cuti');
                
                if($cek_exist->num_rows() > 0){
                    $this->response([
                        'status'=>false,
                        'message'=>"Rentang tanggal tersebut sudah pernah mengajukan cuti. Silakan ganti tanggal.",
                        'data'=>$cek_exist->result()
                    ], MY_Controller::HTTP_OK);
                } else{
                    # proses insert
                    $this->db->trans_start();
                    $this->db->insert('ess_cuti',$data_insert);
                    if($this->db->trans_status()==true){
                        $this->db->trans_complete();
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
                        $this->response([
                            'status'=>true,
                            'message'=>"Cuti telah ditambahkan",
                            //'message'=>"Under development",
                            'data'=>$data_insert
                        ], MY_Controller::HTTP_OK);
                    } else{
                        $this->db->trans_rollback();
                        $this->response([
                            'status'=>false,
                            'message'=>$this->db->error()['message'],
                            'data'=>$data
                        ], MY_Controller::HTTP_BAD_REQUEST);
                    }
                }
            }
        } catch(Exception $e){
            $this->response([
                'status'=>false,
                'message'=>'Error Exception',
                'data'=>$data
            ], MY_Controller::HTTP_BAD_REQUEST);
        }
    }
}
