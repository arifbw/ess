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
                                $row['hapus'] = '1';
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
        $data_insert = [];
        // $this->response([
        //     'status'=>false,
        //     'message'=>"Data Post",
        //     'data'=>$this->post()
        // ], MY_Controller::HTTP_OK);
        $absence_type_butuh_approval = ['G|2001|5050', '0|2001|5000', 'H|2001|5030', 'C|2001|5040', 'F|2001|5020', 'E|2001|5010', 'SIPK|2001|5030'];
        $absence_type = $this->post('absence_type');
        $explode = explode('|', $absence_type);
        $info_type = $explode[1];
        $absence_type2 = $explode[2];
        $kode_pamlek = $explode[0];
        
        $np_karyawan		= $this->post('np_karyawan');				
        $start_date			= @$this->post('start_date') ? date('Y-m-d',strtotime($this->post('start_date'))) : null;
        $start_time			= @$this->post('start_time') ? $this->post('start_time'):null;
        $end_date			= date('Y-m-d',strtotime($this->post('end_date')));
        $end_time			= $this->post('end_time');
        $approval_1_np		= $this->post('approval_1_np');
        $approval_1_nama	= $this->post('approval_1_nama');
        $approval_1_jabatan	= $this->post('approval_1_jabatan');
        $approval_2_np		= (@$this->post('approval_2_np')!='') ? $this->post('approval_2_np') : null;
        $approval_2_nama	= (@$this->post('approval_2_nama')!='') ? $this->post('approval_2_nama') : null;
        $approval_2_jabatan	= (@$this->post('approval_2_jabatan')!='') ? $this->post('approval_2_jabatan') : null;
        $pos				= json_encode($this->post('pos'));
        
        $start_date_time = ($kode_pamlek=='0' ? null : date('Y-m-d H:i', strtotime($start_date.' '.$start_time)));
        $end_date_time = date('Y-m-d H:i', strtotime($end_date.' '.$end_time));
        $tahun_bulan     = $start_date!=null ? str_replace('-','_',substr("$start_date", 0, 7)) : str_replace('-','_',substr("$end_date", 0, 7)) ;
        
        $erp_master_data_by_np = erp_master_data_by_np($np_karyawan, $start_date);
        $nama_karyawan 		= $erp_master_data_by_np['nama'];
        $personel_number	= $erp_master_data_by_np['personnel_number'];
        $nama_jabatan		= $erp_master_data_by_np['nama_jabatan'];
        $kode_unit 			= $erp_master_data_by_np['kode_unit'];
        $nama_unit 			= $erp_master_data_by_np['nama_unit'];
        
        $this->db->query("CREATE TABLE IF NOT EXISTS ess_perizinan_$tahun_bulan LIKE ess_perizinan");
        
        $data_insert = [
            'np_karyawan'=>$np_karyawan,
            'nama'=>$nama_karyawan,
            'personel_number'=>$personel_number,
            'nama_jabatan'=>$nama_jabatan,
            'kode_unit'=>$kode_unit,
            'nama_unit'=>$nama_unit,
            'info_type'=>$info_type,
            'absence_type'=>$absence_type2,
            'kode_pamlek'=>$kode_pamlek,
            'start_date'=>$start_date,
            'start_time'=>$start_time,
            'end_date'=>$end_date,
            'end_time'=>$end_time,
            'start_date_input'=>$start_date_time,
            'end_date_input'=>$end_date_time,
            'approval_1_np'=>$approval_1_np,
            'approval_1_nama'=>$approval_1_nama,
            'approval_1_jabatan'=>$approval_1_jabatan,
            'approval_2_np'=>$approval_2_np,
            'approval_2_nama'=>$approval_2_nama,
            'approval_2_jabatan'=>$approval_2_jabatan,
            'end_time'=>$end_time,
            'machine_id_start'=>'ess',
            'machine_id_end'=>'ess',
            'pos'=>$pos,
            'created_at'=>date('Y-m-d H:i:s'),
            'created_by'=>$this->data_karyawan->np_karyawan,
            'alasan'=>trim($this->post('alasan',true)) # tambahan untuk alasan, 2021-03-10
        ];
        
        # 2021-04-01, heru ganti query jadi ini
        $cek_izin = $this->db
            ->where('np_karyawan',$data_insert['np_karyawan'])
            ->where('date_batal IS NULL',null,false)
            ->where("(
                        CASE 
                            WHEN approval_2_np IS NOT NULL THEN (approval_1_status IS NULL OR approval_2_status IS NULL)
                            WHEN approval_2_np IS NULL THEN approval_1_status IS NULL
                        END
                    )")
            ->get('ess_request_perizinan');

        if($np_karyawan=='' || $np_karyawan==null){
            $this->response([
                'status'=>false,
                'message'=>"NP harus diisi",
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
        } else if(($start_date=='' || $start_date==null) && $kode_pamlek!='0' ){
            $this->response([
                'status'=>false,
                'message'=>"Start date tidak boleh kosong",
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
        } else if($end_date=='' || $end_date==null){
            $this->response([
                'status'=>false,
                'message'=>"End date tidak boleh kosong",
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
        } else if($start_date_time >= $end_date_time){
            $this->response([
                'status'=>false,
                'message'=>"Tanggal Akhir Perizinan harus lebih besar dari Tanggal Mulai Perizinan",
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
        } else if( in_array($absence_type, $absence_type_butuh_approval) ){
            if( trim($approval_1_np)=='' || $approval_1_np==null ){
                $this->response([
                    'status'=>false,
                    'message'=>"Approval 1 harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            }

            if( in_array($absence_type, ['H|2001|5030', 'C|2001|5040', 'F|2001|5020', 'E|2001|5010']) ){
                if( $approval_2_np==null ){
                    $this->response([
                        'status'=>false,
                        'message'=>"Approval 2 harus diisi",
                        'data'=>[]
                    ], MY_Controller::HTTP_BAD_REQUEST);
                }
            }
        } 
        
        // else if($cek_izin->num_rows() > 0){
            # 2021-04-01, tambahan untuk filter izin yg belum diapprove
        $count_pending = 0;
        foreach( $cek_izin->result() as $row ){
            if( $row->approval_1_status=='2' || $row->approval_2_status=='2' ){
                # do nothing
                continue;
            } else{
                if( $row->approval_2_np!=null ){
                    if( $row->approval_1_status==null )
                        $count_pending++;
                    else{
                        if( $row->approval_1_status=='1' && $row->approval_2_status==null )
                            $count_pending++;
                    }
                } else{
                    if( $row->approval_1_status==null )
                        $count_pending++;
                }
            }
        }
        
        if( $count_pending>0 ){
            $this->response([
                'status'=>false,
                'message'=>"Permohonan Perizinan Terakhir Belum Diapprove Oleh Atasan/Keamanan",
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
        }
        // }
        
        $start_date_time_validasi = ($start_date_time==null ? $end_date_time : $start_date_time);
        $cek = $this->db->query("SELECT * 
        FROM ess_perizinan_$tahun_bulan
        WHERE np_karyawan='$np_karyawan' 
        AND (
            ('$start_date_time_validasi' BETWEEN DATE_FORMAT(CONCAT(start_date,' ',start_time),'%Y-%m-%d %H:%i') AND DATE_FORMAT(CONCAT(end_date,' ',end_time),'%Y-%m-%d %H:%i'))
            OR ('$end_date_time' BETWEEN DATE_FORMAT(CONCAT(start_date,' ',start_time),'%Y-%m-%d %H:%i') AND DATE_FORMAT(CONCAT(end_date,' ',end_time),'%Y-%m-%d %H:%i'))
            OR (DATE_FORMAT(CONCAT(start_date,' ',start_time),'%Y-%m-%d %H:%i') BETWEEN '$start_date_time_validasi' AND '$end_date_time')
            OR (DATE_FORMAT(CONCAT(end_date,' ',end_time),'%Y-%m-%d %H:%i') BETWEEN '$start_date_time_validasi' AND '$end_date_time')
        ) ");
        
        //15 04 2021, Tri Wibowo 7648, Dimatikan Sementara Karena Masih salah, contoh row 108 di excel google drive
        //if($cek->num_rows()>0){
        if(1==2){
            $this->response([
                'status'=>false,
                'message'=>"Data perizinan dengan nama $nama_karyawan, pada rentang tanggal $start_date $start_time sampai $end_date $end_time sudah ada",
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
        } else{
            $this->db->insert("ess_request_perizinan", $data_insert);
            $this->response([
                'status'=>true,
                'message'=>"Data perizinan dengan nama $nama_karyawan, pada rentang tanggal $start_date $start_time sampai $end_date $end_time berhasil ditambahkan",
                'data'=>[]
            ], MY_Controller::HTTP_OK);
        }
    }
}
