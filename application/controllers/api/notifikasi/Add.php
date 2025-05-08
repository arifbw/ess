<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Add extends MY_Controller {
    
    function __construct(){
        parent::__construct();
    }
    
    function index_post(){
        $data_insert = [];
        
        if(empty($this->post('np_pengirim'))){
            $this->response([
                'status'=>false,
                'message'=>"NP Pengirim harus diisi",
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
        } else if(empty($this->post('np_penerima'))){
            $this->response([
                'status'=>false,
                'message'=>"NP Penerima harus diisi",
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
        } else{
            $data = $this->post();
            $data['id'] = $this->uuid->v4();
            $data['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert('mobile_notifikasi_log',$data);
            $this->response([
                'status'=>true,
                'message'=>'Notifikasi tersimpan',
                'data'=>$data
            ], MY_Controller::HTTP_OK);
        }
    }
}
