<?php defined('BASEPATH') OR exit('No direct script access allowed');
include_once( APPPATH . 'core/Group_Controller.php' );
class Permohonan extends Group_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->helper("tanggal_helper");
        $this->load->helper("cutoff_helper");
        $this->load->helper("perizinan_helper");
        $this->load->helper("karyawan_helper");
        $this->load->model("api/filter/M_filter_api","filter");
        $this->load->model("api/M_perizinan_api","perizinan");
    }
    
    function index_get(){
        $data=[];
        $params=[];
        try {
            /*$this->response([
                'status'=>false,
                'message'=>"Akses ditutup",
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);*/

            if(empty($this->get('bulan'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Bulan harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                $bulan = substr($this->get('bulan'),-2);
				$tahun = substr($this->get('bulan'),0,4);
                
                $params['table_name'] = 'ess_perizinan_'.$tahun.'_'.$bulan;
                $params['bulan'] = $this->get('bulan');
                
                if(@$this->get('jenis_perizinan')){
                    $jenis_perizinan = $this->perizinan->get_jenis_by_id($this->get('jenis_perizinan'))->row();
                    $params['jenis_izin'] = [$jenis_perizinan->kode_pamlek];
                }
                
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
                $get_data_izin = $this->perizinan->get_permohonan($params)->result();
                // $a = $this->db->last_query();
                foreach($get_data_izin as $tampil){
                    $row=[];
                    $no++;
                    $row['no'] = $no;
                    $row['id'] = $tampil->id;
                    $row['tanggal'] = $tampil->start_date!=null?$tampil->start_date:($tampil->end_date!=null?$tampil->end_date:null);
                    $row['np_karyawan'] = $tampil->np_karyawan;
                    $row['nama'] = $tampil->nama;
                    $row['kode_pamlek'] = $tampil->kode_pamlek;
                    $row['nama_perizinan'] = get_perizinan_name($tampil->kode_pamlek)->nama;
                    
                    # date input
                    if($tampil->start_date_input) {
                        $row['start_date_input'] = tanggal_indonesia(date('Y-m-d', strtotime($tampil->start_date_input))).', '.date('H:i:s', strtotime($tampil->start_date_input));
                    } else {
                        $row['start_date_input'] = '';
                    }
                    
                    if($tampil->end_date_input) {
                        $row['end_date_input'] = tanggal_indonesia(date('Y-m-d', strtotime($tampil->end_date_input))).', '.date('H:i:s', strtotime($tampil->end_date_input));
                    } else {
                        $row['end_date_input'] = '';
                    }
                    # date input
                    
                    # date realisasi
                    if($tampil->start_date) {
                        $row['start_date_realisasi'] = tanggal_indonesia($tampil->start_date).', '.$tampil->start_time;
                    } else {
                        $row['start_date_realisasi'] = '';
                    }

                    if($tampil->end_date) {
                        $row['end_date_realisasi'] = tanggal_indonesia($tampil->end_date).', '.$tampil->end_time;
                    } else {
                        $row['end_date_realisasi'] = '';
                    }
                    # date realisasi
                    
                    $row['sumber_data'] = $tampil->is_machine=='1'?'Mesin Pamlek':'ESS';

                    if($tampil->start_date) {
                        $tanggal_check = $tampil->start_date;
                    }else {
                        $tanggal_check = $tampil->end_date;
                    }
                    
                    $sudah_cutoff = sudah_cutoff($tanggal_check);
                    
                    if($sudah_cutoff) { //jika sudah lewat masa cutoff
                        $row['cutoff_data'] = "1";
                        $row['hapus'] = "Tidak bisa melakukan aksi setelah Cutoff data di ERP pada $sudah_cutoff";
                    }else {
                        $row['cutoff_data'] = "0";
                        $row['hapus'] = "1";

                        

                        if ($tampil->date_batal==null) {
                            if ($tampil->approval_1_status==null && $tampil->approval_2_status==null) {
                                $np_hapus = $tampil->np_karyawan;
                                $tanggal_hapus = ($tampil->start_date!=NULL ? $tampil->start_date : $tampil->end_date);
                                $time_hapus = ($tampil->start_time!=NULL ? $tampil->start_time : $tampil->end_time);
                                $date_time_hapus = date('Y-m-d H:i:s', strtotime($tanggal_hapus.' '.$time_hapus));
                                
                                # cek pengamanan
                                if( $tampil->approval_pengamanan_posisi!=null ){
                                    $count_status_1 = 0;
                                    $approval_keamanan = json_decode($tampil->approval_pengamanan_posisi);
                                    foreach ($approval_keamanan as $value) {
                                        if( $value->status=='1' ) $count_status_1++;
                                    }
                                    $row['hapus'] = $count_status_1>0 ? '0':'1';
                                } else{
                                    $row['hapus'] = '1';
                                }
                            } else {
                                $row['hapus'] = '0';
                            }
                        } else {
                            $row['hapus'] = '0';
                        }
                    }

                    $np_karyawan    = trim($tampil->np_karyawan);
                    $nama           = trim($tampil->nama);
                    $kode_pamlek    = trim($tampil->kode_pamlek);
                    $created_at     = trim($tampil->created_at);
                    $start_date     = trim(tanggal_indonesia($tampil->start_date).' '.$tampil->start_time);
                    $end_date       = trim(tanggal_indonesia($tampil->end_date).' '.$tampil->end_time);
                    $approval_1     = trim($tampil->approval_1_np);
                    $approval_2     = trim($tampil->approval_2_np); 
                    $status_1       = trim($tampil->approval_1_status);
                    $status_2       = trim($tampil->approval_2_status);
                    $approval_1_date= trim($tampil->approval_1_updated_at);
                    $approval_2_date= trim($tampil->approval_2_updated_at);

                    if($status_1=='1') {
                        $approval_1_nama    = $approval_1." | ".nama_karyawan_by_np($approval_1);
                        $approval_1_status  = "Izin Telah Disetujui pada $approval_1_date."; 
                    }else if($status_1=='2') {
                        $approval_1_nama    = $approval_1." | ".nama_karyawan_by_np($approval_1);
                        $approval_1_status  = "Izin TIDAK disetujui pada $approval_1_date."; 
                    }else if($status_1=='3') {
                        $approval_1_nama    = $approval_1." | ".nama_karyawan_by_np($approval_1);
                        $approval_1_status  = "Pengajuan Izin Dibatalkan oleh pemohon pada $approval_1_date."; 
                    }else if($status_1==''||$status_1=='0'||$status_1==null) {
                        $status_1 = '0';
                        $approval_1_nama    = $approval_1." | ".nama_karyawan_by_np($approval_1);
                        $approval_1_status  = "Izin BELUM disetujui."; 
                    }
                    
                    if($status_2=='1') {                    
                        $approval_2_nama    = $approval_2." | ".nama_karyawan_by_np($approval_2);
                        $approval_2_status  = "Izin Telah Disetujui pada $approval_2_date."; 
                    }else if($status_2=='2') {
                        $approval_2_nama    = $approval_2." | ".nama_karyawan_by_np($approval_2);
                        $approval_2_status  = "Izin TIDAK disetujui pada $approval_2_date."; 
                    }else if($status_2=='3') {
                        $approval_2_nama    = $approval_2." | ".nama_karyawan_by_np($approval_2);
                        $approval_2_status  = "Pengajuan Izin Dibatalkan oleh pemohon pada $approval_2_date."; 
                    }else if($status_2==''||$status_2=='0'||$status_2==null) {
                        $status_2 = '0';
                        $approval_2_nama    = $approval_2." | ".nama_karyawan_by_np($approval_2);
                        $approval_2_status  = "Izin BELUM disetujui."; 
                    }

                    $row['approval_1_nama'] = $approval_1_nama;
                    $row['approval_2_nama'] = $approval_2_nama;
                    $row['approval_1_status'] = $approval_1_status;
                    $row['approval_1_keterangan'] = $tampil->approval_1_keterangan;
                    $row['approval_2_status'] = $approval_2_status;
                    $row['approval_2_keterangan'] = $tampil->approval_2_keterangan;
                    $row['status_1'] = $status_1;
                    $row['status_2'] = $status_2;

                    $btn_warna      ='btn-default';
                    $btn_text       ='Menunggu Persetujuan';
                    $btn_disabled   ='0';
                    
                    if(($status_1=='' || $status_1 == null) && ($status_2!='2' || $status_2!='1')) { //menunggu atasan 1
                        $btn_warna      ='btn-warning';
                        $btn_text       ='Menunggu Atasan 1';
                    }

                    if(($status_1=='1') && ($status_2!='2' || $status_2!='1')) { //disetujui atasan 1
                        if($tampil->approval_2_np==null || $tampil->approval_2_np=='') { //jika tidak ada atasan 2
                            $btn_warna      ='btn-success';
                            $btn_text       ='Disetujui Atasan 1';
                            $btn_disabled   ='0';
                        }else { //jika ada atasan 2
                            $btn_warna      ='btn-warning';
                            $btn_text       ='Disetujui Atasan 1, Menunggu Atasan 2';
                            $btn_disabled   ='1';
                        }
                    }

                    if(($status_1=='2') && ($status_2!='2' || $status_2!='1')) { //ditolak atasan 1
                        $btn_warna      ='btn-danger';
                        $btn_text       ='Ditolak Atasan 1';
                        $btn_disabled   ='1';
                    }

                    if($status_2=='1') { //disetujui atasan  2
                        $btn_warna      ='btn-success';
                        $btn_text       ='Disetujui Atasan 2';
                        $btn_disabled   ='1';
                        
                        if($status_1=='0' || $status_1==null) { //jika paralel atasan 2 belum approve
                            $btn_warna      ='btn-warning';
                            $btn_text       ='Disetujui Atasan 2, Menunggu Atasan 1';
                            $btn_disabled   ='1';
                        }
                    }
                    
                    if($status_2=='2') { //ditolak atasan 2
                        $btn_warna      ='btn-danger';
                        $btn_text       ='Ditolak Atasan 2';
                        $btn_disabled   ='1';
                    }
                        
                    if($tampil->date_batal!=null) { //dibatalkan
                        $btn_warna      ='btn-danger';
                        $btn_text       ='Dibatalkan';
                        $btn_disabled   ='1';
                    }

                    $row['btn_warna'] = $btn_warna;
                    $row['btn_status'] = $btn_text;
                    $row['btn_disabled'] = $btn_disabled;

                    $row['created_at'] = $created_at;

                    
                    /*$row['status_perizinan'] = status_perizinan([
                        'kode_pamlek'=>$tampil->kode_pamlek,
                        'approval_1_status'=>$tampil->approval_1_status,
                        'approval_2_status'=>$tampil->approval_2_status,
                        'is_machine'=>$tampil->is_machine,
                        'pengguna_status'=>$tampil->pengguna_status
                    ]);*/
                    $row['pengguna_status'] = $tampil->pengguna_status;
                    $row['alasan'] = $tampil->alasan;
                    $row['alasan_batal'] = $tampil->alasan_batal;
                    $row['data_pos'] = implode(",", array_column($this->db->where("id in ('".(implode("','", json_decode($tampil->pos)))."')")->get('mst_pos')->result_array(), 'nama'));
                    $row['realisasi'] = json_decode($tampil->approval_pengamanan_posisi);
                    // $row['dapat_dibatalkan'] = $tampil->pengguna_status=='3'?false:true;
                    
                    $data[]=$row;
                }
                
                $this->response([
                    'status'=>true,
                    // 'a'=>$a,
                    'message'=>'Data '.(@$jenis_perizinan->nama ? $jenis_perizinan->nama:'Perizinan').' bulan '.id_to_bulan($bulan)." ".$tahun,
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
        $allowed_izin = [1,2,3,4,5,6,7];
        
        try {
            if(empty($this->post('np'))){
                $this->response([
                    'status'=>false,
                    'message'=>"NP harus diisi",
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
            } else if(empty($this->post('start_time'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Waktu awal harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($this->post('end_time'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Waktu akhir harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($this->post('jenis_perizinan'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Jenis perizinan harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($this->post('approval_1_np')) || ctype_alnum($this->post('approval_1_np'))==false || strlen($this->post('approval_1_np'))!=4) {
                $this->response([
                    'status'=>false,
                    'message'=>"Approver 1 harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($this->post('pos'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Pos harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                
                # validasi jenis izin yg diperbolehkan
                if(!in_array($this->post('jenis_perizinan'),$allowed_izin)){
                    $this->response([
                        'status'=>false,
                        'message'=>"Jenis perizinan tidak diperbolehkan",
                        'data'=>[]
                    ], MY_Controller::HTTP_METHOD_NOT_ALLOWED); exit;
                }
                
                # validasi tanggal tidak bisa back date
                if(date('Y-m-d', strtotime($this->post('start_date'))) < date('Y-m-d')){
                    $this->response([
                        'status'=>false,
                        'message'=>"Permohonan izin tidak bisa back date",
                        'data'=>[]
                    ], MY_Controller::HTTP_METHOD_NOT_ALLOWED); exit;
                }
                
                # approval 1
                $explode_approval_1_np = explode('|',$this->post('approval_1_np'));
                $data_insert['approval_1_np'] = $explode_approval_1_np[0];
                $data_insert['approval_1_nama'] = $explode_approval_1_np[1];
                
                if(in_array($this->post('jenis_perizinan'),[3,4,6,7])){
                   /* if(empty($this->post('approval_2_np'))){
                        $this->response([
                            'status'=>false,
                            'message'=>"Approver 2 harus diisi",
                            'data'=>[]
                        ], MY_Controller::HTTP_METHOD_NOT_ALLOWED); exit;
                    } else{*/
                    if(!empty($this->post('approval_2_np'))) {
                        $explode_approval_2_np = explode('|',$this->post('approval_2_np'));
                        if($explode_approval_2_np[0]==$explode_approval_1_np[0]){
                            $this->response([
                                'status'=>false,
                                'message'=>"Approver 2 tidak boleh sama dengan Approver 1",
                                'data'=>[]
                            ], MY_Controller::HTTP_METHOD_NOT_ALLOWED); exit;
                        } else {
                            # approval 2
                            $data_insert['approval_2_np'] = $explode_approval_2_np[0];
                            $data_insert['approval_2_nama'] = $explode_approval_2_np[1];
                        }
                    }
                    // }
                }
                
                # karyawan
                $np = $this->post('np');
                $data_karyawan = $this->filter->get_data_karyawan_by_np($np)->row();
                
                $id_jenis_perizinan = $this->post('jenis_perizinan');
                $start_date = date('Y-m-d', strtotime($this->post('start_date')));
                $end_date = date('Y-m-d', strtotime($this->post('end_date')));
                $start_time = $this->post('start_time');
                $end_time = $this->post('end_time');
                $pos = $this->post('pos');
                
                # get jenis perizinan
                $mst_perizinan = $this->db->where('id',$id_jenis_perizinan)->get('mst_perizinan')->row();
                $info_type = explode('|',$mst_perizinan->kode_erp)[0];
                $absence_type = explode('|',$mst_perizinan->kode_erp)[1];
                $kode_pamlek = $mst_perizinan->kode_pamlek;
                
                $data_insert['np_karyawan'] = $np;
                $data_insert['nama'] = @$data_karyawan->nama?$data_karyawan->nama:null;
                $data_insert['personel_number'] = @$data_karyawan->personnel_number?$data_karyawan->personnel_number:null;
                $data_insert['nama_jabatan'] = @$data_karyawan->nama_jabatan?$data_karyawan->nama_jabatan:null;
                $data_insert['kode_unit'] = @$data_karyawan->kode_unit?$data_karyawan->kode_unit:null;
                $data_insert['nama_unit'] = @$data_karyawan->nama_unit?$data_karyawan->nama_unit:null;
                $data_insert['info_type'] = $info_type;
                $data_insert['absence_type'] = $absence_type;
                $data_insert['kode_pamlek'] = $kode_pamlek;

                $start_date_time = date('Y-m-d H:i', strtotime($start_date.' '.$start_time));
                $end_date_time = date('Y-m-d H:i', strtotime($end_date.' '.$end_time));
                $data_insert['start_date_input'] = $start_date_time;
                $data_insert['end_date_input'] = $end_date_time;
                $data_insert['start_date'] = $start_date;
                $data_insert['end_date'] = $end_date;
                $data_insert['start_time'] = $start_time;
                $data_insert['end_time'] = $end_time;

                $data_insert['pos'] = $pos;
                $data_insert['is_machine'] = '0';
                $data_insert['machine_id_start'] = 'ess';
                $data_insert['machine_id_end'] = 'ess';
                
                # cek exist
                if ($kode_pamlek=='0')
                    $cek_izin = $this->db->where(['np_karyawan'=>$np, 'end_date'=>$end_date, 'kode_pamlek'=>$kode_pamlek])->get('ess_perizinan_'.date('Y_m', strtotime($start_date)));
                else
                    $cek_izin = $this->db->where(['np_karyawan'=>$np, 'start_date'=>$start_date, 'kode_pamlek'=>$kode_pamlek])->get('ess_perizinan_'.date('Y_m', strtotime($start_date)));
                
                if($cek_izin->num_rows()>0){
                    $this->response([
                        'status'=>false,
                        'message'=>'Tidak dapat diteruskan. Jenis perizinan tidak dapat diinput pada tanggal yang sama.',
                        'data'=>$cek_izin->result()
                    ], MY_Controller::HTTP_BAD_REQUEST);
                } else{
                    # insert
                    // $this->db->insert('ess_perizinan_'.date('Y_m', strtotime($start_date)), $data_insert);
                    $this->db->insert('ess_request_perizinan', $data_insert);
                    $this->response([
                        'status'=>true,
                        'message'=>'Izin telah diinput',
                        'data'=>$data_insert
                    ], MY_Controller::HTTP_OK);
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
