<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Update_ceklis extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->model('api/tasklist/M_tasklist_api','tasklist');
    }
    
    function index_post(){
        $data_insert = [];
        try {
            if(empty($this->post('id'))){
                $this->response([
                    'status'=>false,
                    'message'=>'ID harus diisi'
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(!in_array($this->post('value_check'),['1','0'])){
                $this->response([
                    'status'=>false,
                    'message'=>'Value harus diisi (1/0)'
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                $id = $this->post('id');
                $checked = $this->post('value_check');
                
                $data_insert['checked'] = $checked;
                $data_insert['checked_at'] = date('Y-m-d H:i:s');

                $data_insert['progress'] =  $this->post('progress');
                
                if(!empty($this->post('keterangan'))){
                    $data_insert['hasil_pekerjaan'] = $this->post('keterangan');
                }
                
                $this->db->where('id',$id)->update('ess_performance_management',$data_insert);
                if($this->db->affected_rows()>0){
                    $this->response([
                        'status'=>true,
                        'message'=>'Checklist Updated'
                    ], MY_Controller::HTTP_OK);
                } else{
                    $this->response([
                        'status'=>false,
                        'message'=>'Failed to update'
                    ], MY_Controller::HTTP_BAD_REQUEST);
                }
            }
        } catch(Exception $e){
            $this->response([
                'status'=>false,
                'message'=>'Error Exception'
            ], MY_Controller::HTTP_BAD_REQUEST);
        }
    }
}
