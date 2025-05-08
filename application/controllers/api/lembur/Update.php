<?php defined('BASEPATH') OR exit('No direct script access allowed');
include_once( APPPATH . 'core/Group_Controller.php' );
class Update extends Group_Controller {
    
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

    function index_post(){
        $this->response([
            'status'=>false,
            'message'=>"Data harus diisi",
            'data'=>[]
        ], MY_Controller::HTTP_BAD_REQUEST);

        /*$data=[];
        $params=[];
        try {
            if(empty($this->post())){
                $this->response([
                    'status'=>false,
                    'message'=>"Data harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                $data['id'] = $this->post("id_pengajuan_lembur");
                $data['no_pokok'] = $this->post("no_pokok");
                $data['approval_pimpinan_np'] = $this->post("np_approver");
                $data['no_pokok_ubah'] = $this->post("no_pokok_ubah");
                $data['approval_pimpinan_np_ubah'] = $this->post("np_approver_ubah");
                $data['tgl_dws_ubah'] = $this->post("tgl_dws_ubah");
                $data['tgl_dws'] = $this->post("tgl_dws");
                $data['tgl_mulai'] = $this->post("tgl_mulai");
                $data['tgl_selesai'] = $this->post("tgl_selesai");
                $data['jam_mulai'] = $this->post("jam_mulai");
                $data['jam_selesai'] = $this->post("jam_selesai");
                $data['tgl_mulai_ubah'] = $this->post("tgl_mulai_ubah");
                $data['tgl_selesai_ubah'] = $this->post("tgl_selesai_ubah");
                $data['jam_mulai_ubah'] = $this->post("jam_mulai_ubah");
                $data['jam_selesai_ubah'] = $this->post("jam_selesai_ubah");
                $kry = erp_master_data_by_np($data['no_pokok_ubah'], $data['tgl_dws']);
                $apv = erp_master_data_by_np($data['approval_pimpinan_np_ubah'], $data['tgl_dws']);
                $data['nama'] = $kry['nama'];
                $data['nama_jabatan'] = $kry['nama_jabatan'];
                $data['nama_unit'] = $kry['nama_unit'];
                $data['kode_unit'] = $kry['kode_unit'];
                $data['approval_pimpinan_nama'] = $apv['nama'];
                $data['approval_pimpinan_nama_jabatan'] = $apv['nama_jabatan'];
                $data['approval_pimpinan_nama_unit'] = $apv['nama_unit'];
                $data['approval_pimpinan_kode_unit'] = $apv['kode_unit'];
                $data['personel_number'] = $kry['personnel_number'];

                $ubah = $this->ubah($data);
                if($ubah['status'] == true) {
                    $ubah['message'] = 'Penambahan Data Lembur <b>Berhasil</b> Dilakukan.';
                }

                $this->response($ubah, MY_Controller::HTTP_OK);
            }
        } catch(Exception $e){
            $this->response([
                'status'=>false,
                'message'=>'Error Exception',
                'data'=>$data
            ], MY_Controller::HTTP_BAD_REQUEST);
        }*/
    }

    private function ubah($data){
        $return = array("status" => false, "message" => "", "data" => []);

        $kry = erp_master_data_by_np($data['no_pokok'], $data['tgl_dws']);
        $apv = erp_master_data_by_np($data['approval_pimpinan_np_ubah'], $data['tgl_dws']);
        $set = array("no_pokok" => $data['no_pokok'], "nama" => $kry['nama'], "nama_jabatan" => $kry['nama_jabatan'], "nama_unit" => $kry['nama_unit'],"kode_unit" => $kry['kode_unit'], "approval_pimpinan_np" => $data['approval_pimpinan_np_ubah'], "approval_pimpinan_nama" => $apv['nama'], "approval_pimpinan_nama_jabatan" => $apv['nama_jabatan'], "approval_pimpinan_nama_unit" => $apv['nama_unit'], "approval_pimpinan_kode_unit" => $apv['kode_unit'], "personel_number" => $kry['personnel_number'], "tgl_dws" =>  $data['tgl_dws_ubah'], "tgl_mulai" =>  $data['tgl_mulai_ubah'], "tgl_selesai" => $data['tgl_selesai_ubah'], "jam_mulai" => $data['jam_mulai_ubah'], "jam_selesai" => $data['jam_selesai_ubah']);
        $where = array("id" => $data['id'], "no_pokok" => $data['no_pokok'], "tgl_mulai" =>  $data['tgl_mulai'], "tgl_selesai" => $data['tgl_selesai'], "jam_mulai" => $data['jam_mulai'], "jam_selesai" => $data['jam_selesai']);
        //$set['updated_by']  = $this->session->userdata("no_pokok");
        $set['updated_by']  = $this->data_karyawan->np_karyawan; # heru menambahkan row ini 2020-11-16 @14:29
        $set['updated_at']  = date("Y-m-d H:i:s");
        $where_update = array("id" => $data['id']);

        $get_date['start_input'] = date('Y-m-d', strtotime($set['tgl_mulai'])).' '.date('H:i:s', strtotime($set['jam_mulai']));
        $get_date['end_input'] = date('Y-m-d', strtotime($set['tgl_selesai'])).' '.date('H:i:s', strtotime($set['jam_selesai']));
        $date_dws = date('Y-m-d', strtotime($set['tgl_dws']));
        $plus1 = date('Y-m-d',strtotime($date_dws."+1 days"));
        $minus1 = date('Y-m-d',strtotime($date_dws."-1 days"));

        $get_jadwal = $this->m_pengajuan_lembur->cek_valid_lembur($set);
        $cek_uniq_lembur = $this->m_pengajuan_lembur->cek_uniq_lembur($set, $data['id'], null, null);
                
        //check apakah dia pelaksana atau kaun yang boleh lembur
        //return $boleh_lembur['status'],$boleh_lembur['grade_pangkat'],$boleh_lembur['grup_jabatan'],$boleh_lembur['keterangan_hari']
        $check_boleh_lembur = $this->m_pengajuan_lembur->check_boleh_lembur($data['no_pokok'],$data['tgl_dws']);
        
        if($check_boleh_lembur['status']==false)
        {
            $cek_uniq_lembur['message'] = 'Not Allowed';
        }
        
        //echo (int)$cek_uniq_lembur['status'];exit;
        if (($get_date['start_input'] < $get_date['end_input'] || (($set['tgl_mulai'] != $set['tgl_dws'] || $set['tgl_mulai'] != $plus1 || $set['tgl_mulai'] != $minus1) && ($set['tgl_selesai'] != $set['tgl_dws'] || $set['tgl_selesai'] != $plus1 || $data[$i]['tgl_selesai'] != $minus1))) && $cek_uniq_lembur['status'] == true && $check_boleh_lembur['status']==true) {
            $get_jadwal = $this->m_pengajuan_lembur->cek_valid_lembur($set);
            
            //if((bool)$this->m_pengajuan_lembur->cek_uniq_lembur($data[$i]) == true) {
            $set['waktu_mulai_fix'] = null;
            $set['waktu_selesai_fix'] = null;
            if ((bool)$get_jadwal != false && (bool)$this->m_pengajuan_lembur->cek_dws_lembur($set) == true) {
                //var_dump($get_jadwal);exit;
                if ($cek_uniq_lembur['message'] == 'Not Valid') {
                    $set['waktu_mulai_fix'] = $get_date['start_input'];
                    $set['waktu_selesai_fix'] = $get_date['end_input'];
                    $set['time_type'] = $get_jadwal['time_type'];
                }
                else if ($cek_uniq_lembur['message'] == 'Not DWS') {
                    $set['waktu_mulai_fix'] = $set['waktu_mulai_fix'];
                    $set['waktu_selesai_fix'] = $set['waktu_selesai_fix'];
                    $set['time_type'] = null;
                }
                else {
                    $set['waktu_selesai_fix'] = $get_jadwal['waktu_selesai_fix'];
                    $set['waktu_mulai_fix'] = $get_jadwal['waktu_mulai_fix'];
                    $set['time_type'] = $get_jadwal['time_type'];
                }
                //echo 'a';exit;
                
                //jika waktu mulai >= waktu selesai
                if($set['waktu_mulai_fix']>=$set['waktu_selesai_fix']) 
                {
                    $set['waktu_selesai_fix']   = null;
                    $set['waktu_mulai_fix']     = null;
                }
            }
            //      var_dump($get_jadwal);exit;
            //echo  $cek_uniq_lembur['status'] ;exit;
                //var_dump($cek_uniq_lembur);exit;
                
            $arr_data_lama = $this->m_pengajuan_lembur->ambil_pengajuan_lembur_id($data['id']);
            $log_data_lama = "";
            foreach($arr_data_lama as $key => $value){
                if(strcmp($key,"id")!=0){
                    if(!empty($log_data_lama)){
                        $log_data_lama .= "<br>";
                    }
                    $log_data_lama .= "$key = $value";
                }
            }
            $this->m_pengajuan_lembur->ubah($set, $where_update);
            $return["status"] = true;
                
            $log_data_baru = "";
            foreach($set as $key => $value){
                if(!empty($log_data_baru)){
                    $log_data_baru .= "<br>";
                }
                $log_data_baru .= "$key = $value";
            }
            
            $log = array(
                //"id_pengguna" => $this->session->userdata("id_pengguna"),
                "id_pengguna" => $this->account->id, # heru menambahkan row ini 2020-11-16 @14:29
                "id_modul" => $this->data['id_modul'],
                "id_target" => $arr_data_lama["id"],
                "deskripsi" => "ubah ".strtolower(preg_replace("/_/"," ",__CLASS__)),
                "kondisi_lama" => $log_data_lama,
                "kondisi_baru" => $log_data_baru,
                "alamat_ip" => $this->data["ip_address"],
                "waktu" => date("Y-m-d H:i:s")
            );
            $this->m_log->tambah($log);
            $return["status"] = true;
        }
        else {
            $return["message"] = "Perubahan Data Lembur <b>Gagal</b> Dilakukan. ";
        }

        return $return;
    }
}
