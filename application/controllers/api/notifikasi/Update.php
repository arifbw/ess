<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Update extends MY_Controller {
    
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
            $data = $this->post();
            $data['updated_at'] = date('Y-m-d H:i:s');
            unset($data['id']);
            $this->db->where(['id'=>$id])->update('mobile_notifikasi_log', $data);
            
            $this->response([
                'status'=>true,
                'message'=>'Notifikasi telah diupdate',
                'data'=>$this->db->where('id',$id)->get('mobile_notifikasi_log')->row_array()
            ], MY_Controller::HTTP_OK);
        }
    }
}
