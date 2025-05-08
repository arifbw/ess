<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Data extends MY_Controller {
    
    function __construct(){
        parent::__construct();
    }
    
    function index_get(){
        $data = $this->db
            ->select("mobile_pengumuman.kode, mobile_pengumuman.judul, mobile_pengumuman.pesan, mobile_pengumuman.deskripsi, mobile_pengumuman.created_at, mobile_pengumuman.publish_at, mobile_pengumuman_delivered.seen_at")
            ->from('mobile_pengumuman_delivered')
            ->join('mobile_pengumuman', "mobile_pengumuman_delivered.mobile_pengumuman_kode=mobile_pengumuman.kode AND mobile_pengumuman.deleted_at IS NULL AND mobile_pengumuman.publish_status='1'")
            ->where('mobile_pengumuman_delivered.np', $this->data_karyawan->np_karyawan)
            ->order_by('mobile_pengumuman.created_at','DESC')
            ->get()
            ->result();
        
        $this->response([
            'status'=>true,
            'message'=>'List Pengumuman.',
            'data'=>$data
        ], MY_Controller::HTTP_OK);
    }
}
