<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Delete extends MY_Controller {
    
    function __construct(){
        parent::__construct();
    }
    
    function index_post(){
        if(empty($this->post('id'))){
            $this->response([
                'status'=>false,
                'message'=>"ID harus diisi",
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
        } else{
            $id = $this->post('id');
            $data = $this->db->where('id',$id)->get('mobile_notifikasi_log')->row_array();
            if(@$data['id'])
                $data['deleted_at'] = date('Y-m-d H:i:s');
            
            $this->db->where(['id'=>$id])->delete('mobile_notifikasi_log');
            
            $this->response([
                'status'=>true,
                'message'=>'Notifikasi telah dihapus',
                'data'=>$data
            ], MY_Controller::HTTP_OK);
        }
    }
}
