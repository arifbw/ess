<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Data extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->model('api/projects/M_comment_api','comment');
    }
    
    function index_get(){
        $data = [];
        try {
            if( empty($this->get('project_id')) ){
                $this->response([
                    'status'=>false,
                    'message'=>'ID Project harus diisi',
                    'data'=>$data
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                $get_comments = $this->comment->get_project_comments($this->get('project_id'));
                
                foreach ($get_comments as $row) {
                    $comment = $row;
                    $comment['time_now'] = date('Y-m-d H:i:s', strtotime('+5 Minutes'));
                    $comment['status_update'] = (date('Y-m-d H:i:s', strtotime($row['created_at'])) >= date('Y-m-d H:i:s', strtotime('-5 Minutes'))) ? 1 : 0;
                    $data[] = $comment;
                }
                
                $this->response([
                    'status'=>true,
                    'message'=>'Success',
                    'data'=>$data
                ], MY_Controller::HTTP_OK);
            }
        } catch(Exception $e){
            $this->response([
                'status'=>false,
                'message'=>'Error Exception',
                'data'=>$data
            ], MY_Controller::HTTP_BAD_REQUEST);
        }
    }
}
