<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Save extends MY_Controller {
    
    function __construct(){
        parent::__construct();
    }
    
    function index_post(){
        $data_insert = [];
        
        if(empty($this->post('fcm_token'))){
            $this->response([
                'status'=>false,
                'message'=>"Fcm token harus diisi",
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
        } else{
            $fcm_token = $this->post('fcm_token');
            $np = $this->data_karyawan->np_karyawan;
            
            $data_insert['fcm_token'] = $fcm_token;
            $data_insert['np'] = $np;
            $data_insert['created_at'] = date('Y-m-d H:i:s');
            
            # cek exist
            $cek = $this->db->where(['np'=>$np])->get('mobile_fcm_tokens');
            
            if($cek->num_rows()==0){
                $this->db->insert('mobile_fcm_tokens',$data_insert);
                $message = 'Fcm token telah disimpan';
            } else{
                $this->db->where('np',$np)->update('mobile_fcm_tokens',$data_insert);
                $message = 'Fcm token telah diupdate';
            }
            
            if($this->db->affected_rows()>0){
                $this->response([
                    'status'=>true,
                    'message'=>$message,
                    'data'=>$data_insert
                ], MY_Controller::HTTP_OK);
            } else{
                $this->response([
                    'status'=>false,
                    'message'=>'Tidak dapat mengupdate ke database.',
                    'data'=>[]
                ], MY_Controller::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }
}
