<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Publish_status extends MY_Controller {
    
    function __construct(){
        parent::__construct();
    }
    
    function index_post(){
        if(empty($this->post('kode_pengumuman'))){
            $this->response([
                'status'=>false,
                'message'=>"Kode Pengumuman harus diisi",
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
        } else if(empty($this->post('publish_status'))){
            $this->response([
                'status'=>false,
                'message'=>"Status Publish harus diisi",
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
        } else{
            $data=[];
            $data['publish_status'] = ($this->post('publish_status')=='1' ? '1':'0');
            $data['publish_at'] = date('Y-m-d H:i:s');
            $this->db->where('kode', $this->post('kode_pengumuman'))->update('mobile_pengumuman', $data);
            $row = $this->db->where('kode', $this->post('kode_pengumuman'))->get('mobile_pengumuman')->row();
            
            $this->response([
                'status'=>true,
                'message'=>'Status Publish telah diubah.',
                'data'=>$row
            ], MY_Controller::HTTP_OK);
        }
    }
}
