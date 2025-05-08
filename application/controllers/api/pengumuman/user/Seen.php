<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Seen extends MY_Controller {
    
    function __construct(){
        parent::__construct();
    }
    
    function index_get(){
        
        if(empty($this->get('kode_pengumuman'))){
            $this->response([
                'status'=>false,
                'message'=>"Kode Pengumuman harus diisi",
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
        } else{
            $this->db
                ->where('mobile_pengumuman_kode', $this->get('kode_pengumuman'))
                ->where('np', $this->data_karyawan->np_karyawan)
                ->update('mobile_pengumuman_delivered', [
                    'seen_at'=>date('Y-m-d H:i:s')
                ]);
            
            $data = $this->db
                ->select("mobile_pengumuman.kode, mobile_pengumuman.judul, mobile_pengumuman.pesan, mobile_pengumuman.deskripsi, mobile_pengumuman.created_at, mobile_pengumuman.publish_at, mobile_pengumuman_delivered.seen_at")
                ->from('mobile_pengumuman_delivered')
                ->join('mobile_pengumuman', "mobile_pengumuman_delivered.mobile_pengumuman_kode=mobile_pengumuman.kode AND mobile_pengumuman.deleted_at IS NULL AND mobile_pengumuman.publish_status='1'")
                ->where('mobile_pengumuman_delivered.np', $this->data_karyawan->np_karyawan)
                ->where('mobile_pengumuman_delivered.mobile_pengumuman_kode', $this->get('kode_pengumuman'))
                ->get()
                ->row();
            
            $this->response([
                'status'=>true,
                'message'=>'Lihat Pengumuman.',
                'data'=>$data
            ], MY_Controller::HTTP_OK);
        }
        
    }
}
