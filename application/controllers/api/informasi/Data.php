<?php defined('BASEPATH') OR exit('No direct script access allowed');
include_once( APPPATH . 'core/Group_Controller.php' );
class Data extends Group_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->helper("tanggal_helper");
        $this->load->helper("perizinan_helper");
        $this->load->model("api/lembur/M_lembur_api","lembur");

        $this->load->helper("karyawan_helper");
        $this->load->helper("cutoff_helper");

        $this->load->model("lembur/m_pengajuan_lembur");
        $this->load->model("lembur/m_tabel_pengajuan_lembur");
        
        if(!in_array($this->id_group,[4,5])){
            $this->response([
                'status'=>false,
                'message'=>"Hanya bisa diakses pengguna atau pengadministrasi unit kerja",
                'data'=>[]
            ], MY_Controller::HTTP_FORBIDDEN);
        }
    }
    
    function index_get() {
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
                $get_data_lembur = $this->lembur->get_lembur($params)->result();
                foreach($get_data_lembur as $tampil){
                    $row=[];
                    $no++;
                    $row['no'] = $no;
                    $row['id'] = $tampil->id;
                    $row['np_karyawan'] = $tampil->no_pokok;
                    $row['approver_np'] = $tampil->approval_pimpinan_np;
                    $row['nama'] = $tampil->nama;
                    $row['tertanggal'] = $tampil->tgl_dws;
                    $row['lembur_mulai'] = $tampil->tgl_mulai;
                    $row['jam_mulai'] = date('H:i:s', strtotime($tampil->jam_mulai));
                    $row['lembur_selesai'] = $tampil->tgl_selesai;
                    $row['jam_selesai'] = date('H:i:s', strtotime($tampil->jam_selesai));
                    $row['lembur_diakui'] = ($tampil->waktu_mulai_fix==null || $tampil->waktu_selesai_fix==null || $tampil->waktu_mulai_fix=='' || $tampil->waktu_selesai_fix=='') ? '-' : datetime_indo($tampil->waktu_mulai_fix).' s/d '.datetime_indo($tampil->waktu_selesai_fix);
                    
                    if($tampil->waktu_mulai_fix==null || $tampil->waktu_selesai_fix==null || $tampil->waktu_mulai_fix=='' || $tampil->waktu_selesai_fix=='' || $tampil->waktu_mulai_fix=='00:00:00' || $tampil->waktu_selesai_fix=='00:00:00') {
                        $row['status'] = "Tidak Diakui";
                    } else if($tampil->approval_status=='1') {
                        $row['status'] = "Disetujui SDM";
                    } else if($tampil->approval_status=='2') {
                        $row['status'] = "Ditolak SDM";
                    } else if($tampil->approval_status=='0' || $tampil->approval_status==null || $tampil->approval_status=='') {
                        if($tampil->approval_pimpinan_status=='1') {
                            $row['status'] = "Disetujui Atasan";
                        } else if($tampil->approval_pimpinan_status=='2') {
                            $row['status'] = "Ditolak Atasan";
                        } else if($tampil->approval_pimpinan_status=='0' || $tampil->approval_pimpinan_status==null || $tampil->approval_pimpinan_status=='') {
                            $row['status'] = "Menunggu Persetujuan";
                        }
                    } else {
                        $row['status'] = "Tidak Valid";
                    }
                    
                    if (($tampil->approval_status=='0' || $tampil->approval_status==null) || $tampil->waktu_mulai_fix==null || $tampil->waktu_selesai_fix==null || $tampil->waktu_mulai_fix=='' || $tampil->waktu_selesai_fix=='' || $tampil->waktu_mulai_fix=='00:00:00' || $tampil->waktu_selesai_fix=='00:00:00') {
					   $row['dapat_dihapus'] = true;
				    } else{
					   $row['dapat_dihapus'] = false;
                    }
                    
                    $data[]=$row;
                }
                
                $this->response([
                    'status'=>true,
                    'message'=>'Data Lembur bulan '.id_to_bulan($bulan)." ".$tahun,
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
        $data=[];
        $params=[];
        try {
            if(empty($this->post())){
                $this->response([
                    'status'=>false,
                    'message'=>"Data harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                    
                if($this->post()){
                    $arr_no_pokok    = $this->post("no_pokok");
                    $arr_np_approver = $this->post("np_approver");
                    $arr_tgl_dws     = $this->post("tgl_dws");
                    $arr_tgl_mulai   = $this->post("tgl_mulai");
                    $arr_tgl_selesai = $this->post("tgl_selesai");
                    $arr_jam_mulai   = $this->post("jam_mulai");
                    $arr_jam_selesai = $this->post("jam_selesai");
                    $created_by      = $this->post("created_by");
                    $created_at      = date("Y-m-d H:i:s");
                    
                    // for($i=0;$i<count($arr_no_pokok);$i++){
                    $kry = erp_master_data_by_np($arr_no_pokok, $arr_tgl_dws);
                    $apv = erp_master_data_by_np($arr_np_approver, $arr_tgl_dws);
                    $data = array("no_pokok"=>$arr_no_pokok,"nama"=>$kry['nama'],"nama_jabatan"=>$kry['nama_jabatan'],"nama_unit"=>$kry['nama_unit'],"kode_unit"=>$kry['kode_unit'],"approval_pimpinan_np"=>$arr_np_approver,"approval_pimpinan_nama"=>$apv['nama'],"approval_pimpinan_nama_jabatan"=>$apv['nama_jabatan'],"approval_pimpinan_nama_unit"=>$apv['nama_unit'],"approval_pimpinan_kode_unit"=>$apv['kode_unit'],"personel_number"=>$kry['personnel_number'],"tgl_dws"=>$arr_tgl_dws,"tgl_mulai"=>$arr_tgl_mulai,"tgl_selesai"=>$arr_tgl_selesai,"jam_mulai"=>$arr_jam_mulai,"jam_selesai"=>$arr_jam_selesai,"created_at"=>$created_at,"created_by"=>$created_by);
                    // }
                    
                    $tambah = $this->tambah($data);
                    if($tambah['status'] == true) {
                        $tambah['message'] = 'Penambahan Data Lembur <b>Berhasil</b> Dilakukan.';
                    }
                    
                    $this->response($tambah, MY_Controller::HTTP_OK);
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

    private function tambah($data){
        $return = array("status" => false, "message" => "", "data" => []);
        // for($i=0;$i<count($data);$i++){

        $get_date['start_input'] = date('Y-m-d', strtotime($data['tgl_mulai'])).' '.date('H:i:s', strtotime($data['jam_mulai']));
        $get_date['end_input'] = date('Y-m-d', strtotime($data['tgl_selesai'])).' '.date('H:i:s', strtotime($data['jam_selesai']));
        $date_dws = date('Y-m-d', strtotime($data['tgl_dws']));
        $plus1 = date('Y-m-d',strtotime($date_dws."+1 days"));
        $minus1 = date('Y-m-d',strtotime($date_dws."-1 days"));

        $get_jadwal = $this->m_pengajuan_lembur->cek_valid_lembur($data);
        $cek_uniq_lembur = $this->m_pengajuan_lembur->cek_uniq_lembur($data, null, null, null);
            
        //var_dump($cek_uniq_lembur);exit;
        if (($get_date['start_input'] < $get_date['end_input'] || (($data['tgl_mulai'] != $data['tgl_dws'] || $data['tgl_mulai'] != $plus1 || $data['tgl_mulai'] != $minus1) && ($data['tgl_selesai'] != $data['tgl_dws'] || $data['tgl_selesai'] != $plus1 || $data['tgl_selesai'] != $minus1))) && $cek_uniq_lembur['status'] == true) {
            $get_jadwal = $this->m_pengajuan_lembur->cek_valid_lembur($data);

            $data['waktu_mulai_fix'] = null;
            $data['waktu_selesai_fix'] = null;
            if ((bool)$get_jadwal != false && (bool)$this->m_pengajuan_lembur->cek_dws_lembur($data) == true) {
                if ($cek_uniq_lembur['message'] == 'Not Valid') {
                    $data['waktu_mulai_fix'] = $get_date['start_input'];
                    $data['waktu_selesai_fix'] = $get_date['end_input'];
                    $data['time_type'] = $get_jadwal['time_type'];
                    //echo 'a';
                }
                else if ($cek_uniq_lembur['message'] == 'Not DWS') {
                    $data['waktu_mulai_fix'] = $data['waktu_mulai_fix'];
                    $data['waktu_selesai_fix'] = $data['waktu_selesai_fix'];
                    $data['time_type'] = null;
                    //echo 'b';
                }
                else {
                    $data['waktu_selesai_fix'] = $get_jadwal['waktu_selesai_fix'];
                    $data['waktu_mulai_fix'] = $get_jadwal['waktu_mulai_fix'];
                    $data['time_type'] = $get_jadwal['time_type'];
                    //echo 'c';
                }
                
                //jika waktu mulai >= waktu selesai
                if($data['waktu_mulai_fix']>=$data['waktu_selesai_fix']) {
                    $data['waktu_mulai_fix'] = null;
                    $data['waktu_selesai_fix'] = null;
                }
            }
                                
            //check apakah dia pelaksana atau kaun yang boleh lembur
            //return $boleh_lembur['status'],$boleh_lembur['grade_pangkat'],$boleh_lembur['grup_jabatan'],$boleh_lembur['keterangan_hari']
            $check_boleh_lembur = $this->m_pengajuan_lembur->check_boleh_lembur($data['no_pokok'],$data['tgl_dws']);
            // $str==true;
            if($check_boleh_lembur['status']==true) { // jika boleh lembur
                //insert ke db
                $id = $this->m_pengajuan_lembur->tambah($data);
                if($id != null || $id != '') {
                    $return["status"] = true;                       
                    $arr_data_insert = $this->m_pengajuan_lembur->data_lembur($data);
                    $log_data_baru = "";
                    foreach($arr_data_insert as $key => $value){
                        if(!empty($log_data_baru)){
                            $log_data_baru .= "<br>";
                        }
                        $log_data_baru .= "$key = $value";
                    }
                    $log = array(
                        "id_pengguna" => $this->session->userdata("id_pengguna"),
                        "id_modul" => $this->data['id_modul'],
                        "id_target" => $arr_data_insert['id'],
                        "deskripsi" => "tambah ".strtolower(preg_replace("/_/"," ",__CLASS__)),
                        "kondisi_baru" => $log_data_baru,
                        "alamat_ip" => $this->data["ip_address"],
                        "waktu" => date("Y-m-d H:i:s")
                    );

                    $this->m_log->tambah($log);
                    $return["status"] = true;
                }
                else {
                    $nama = nama_karyawan_by_np($data['no_pokok']);
                    $return["message"] .= "Pengajuan Data Lembur <b>".$nama." (".$data['no_pokok'].")</b> Pada <b>".$data['tgl_mulai']." ".$data['jam_mulai']."</b> s/d <b>".$data['tgl_selesai']." ".$data['jam_selesai']."</b> <b>Gagal</b> Ditambahkan.<br>";
                }
            }
            else {                    
                $nama = nama_karyawan_by_np($data['no_pokok']);
                $return["message"] .= "Pengajuan Data Lembur <b>".$nama." (".$data['no_pokok'].")</b> Pada <b>".$data['tgl_mulai']." ".$data['jam_mulai']."</b> s/d <b>".$data['tgl_selesai']." ".$data['jam_selesai']."</b> <b>Gagal</b> Ditambahkan.<br>Grade Pangkat = <b>".$check_boleh_lembur['grade_pangkat']."</b> dan Group Jabatan = <b>".$check_boleh_lembur['grup_jabatan']."</b> pada hari <b>".$check_boleh_lembur['keterangan_hari']."</b> tidak mendapat uang lembur.<br>";
            }

        }
        else {
            $nama = nama_karyawan_by_np($data['no_pokok']);
            $return["message"] .= "Pengajuan Data Lembur <b>".$nama." (".$data['no_pokok'].")</b> Pada <b>".$data['tgl_mulai']." ".$data['jam_mulai']."</b> s/d <b>".$data['tgl_selesai']." ".$data['jam_selesai']."</b> <b>Gagal</b> Ditambahkan. ".$cek_uniq_lembur['message']."<br>";
        }
        // }
        return $return;
    }
}
