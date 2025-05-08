<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Data extends MY_Controller {
    
    function __construct(){
        parent::__construct();
    }
    
    function index_get(){
        if(empty($this->get('np_penerima'))){
            $this->response([
                'status'=>false,
                'message'=>"NP Penerima harus diisi",
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
        } else{
            $np_penerima = $this->get('np_penerima');
            $data = $this->db->where(['np_penerima'=>$np_penerima])->order_by('created_at','DESC')->get('mobile_notifikasi_log')->result();
            
            $this->response([
                'status'=>true,
                'message'=>'List notifikasi NP Penerima '.$np_penerima,
                'data'=>$data
            ], MY_Controller::HTTP_OK);
        }
    }
}
