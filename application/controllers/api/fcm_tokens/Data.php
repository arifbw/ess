<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Data extends MY_Controller {
    
    function __construct(){
        parent::__construct();
    }
    
    function index_get(){
        if(empty($this->get('np'))){
            $this->response([
                'status'=>false,
                'message'=>"NP harus diisi",
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
        } else{
            $np = $this->get('np');
            $data = $this->db->where(['np'=>$np])->get('mobile_fcm_tokens')->result();
            
            $this->response([
                'status'=>true,
                'message'=>'Get token NP '.$np,
                'data'=>$data
            ], MY_Controller::HTTP_OK);
        }
    }
}
