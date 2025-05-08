<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Delete extends MY_Controller {
    
    function __construct(){
        parent::__construct();
    }
    
    function index_post(){        
        if(empty($this->post('np'))){
            $this->response([
                'status'=>false,
                'message'=>"NP harus diisi",
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
        } else{            
            $np = $this->post('np');
            $this->db->where(['np'=>$np])->delete('mobile_fcm_tokens');
            
            $this->response([
                'status'=>true,
                'message'=>'Fcm token telah dihapus.'
            ], MY_Controller::HTTP_OK);
        }
    }
}
