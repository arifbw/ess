<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Edit extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->model('api/tasklist/M_tasklist_api','tasklist');
    }
    
    function index_get(){
        $data = [];
        try {
            if(!empty($this->get('id'))){
                $id = $this->get('id');
                $get = $this->tasklist->get_id_task($id);
                if($get->num_rows()==1){
                    $this->response([
                        'status'=>true,
                        'message'=>'Success',
                        'data'=>$get->row()
                    ], MY_Controller::HTTP_FOUND);
                } else{
                    $this->response([
                        'status'=>false,
                        'message'=>'ID not found',
                        'data'=>$data
                    ], MY_Controller::HTTP_NOT_FOUND);
                }
            } else{
                $this->response([
                    'status'=>false,
                    'message'=>'ID is required',
                    'data'=>$data
                ], MY_Controller::HTTP_BAD_REQUEST);
            }
        } catch(Exception $e){
            $this->response([
                'status'=>false,
                'message'=>'Error Exception',
                'data'=>$data
            ], MY_Controller::HTTP_BAD_REQUEST);
        }
    }
    
    function index_post(){
        $data_insert = [];
        try {
            if(empty($this->post('id'))){
                $this->response([
                    'status'=>false,
                    'message'=>'ID harus diisi'
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($this->post('target_pekerjaan'))){
                $this->response([
                    'status'=>false,
                    'message'=>'Target pekerjaan harus diisi'
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                $id = $this->post('id');
                $target_pekerjaan = $this->post('target_pekerjaan');
                
                $data_insert['target_pekerjaan'] = $target_pekerjaan;
                $data_insert['updated_at'] = date('Y-m-d H:i:s');
                
                $this->db->where('id',$id)->update('ess_performance_management',$data_insert);
                if($this->db->affected_rows()>0){
                    $this->response([
                        'status'=>true,
                        'message'=>'Updated'
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
