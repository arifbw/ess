<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Delete extends MY_Controller {
    
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
            } else{
                $id = $this->post('id');
                
                $this->db->where('id',$id)->delete('ess_performance_comment');
                if($this->db->affected_rows()>0){
                    $this->response([
                        'status'=>true,
                        'message'=>'Deleted'
                    ], MY_Controller::HTTP_OK);
                } else{
                    $this->response([
                        'status'=>false,
                        'message'=>'Failed to delete'
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
