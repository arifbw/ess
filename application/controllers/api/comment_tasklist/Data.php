<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Data extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->model('api/comment_tasklist/M_comment_api','comment');
    }
    
    function index_get(){
        $data = [];
        try {
            if(!empty($this->get('tasklist_id'))){
                $get_tasklist = $this->comment->get_tasklist($this->get('tasklist_id'));
                $comment = [];
                foreach ($get_tasklist as $row) {
                    $row->time_now = date('Y-m-d H:i:s', strtotime('+5 Minutes'));
                    $row->status_update = (date('Y-m-d H:i:s', strtotime($row->created_at)) >= date('Y-m-d H:i:s', strtotime('-5 Minutes'))) ? 1 : 0;
                    $comment[] = $row;
                }
                $data['comment'] = $comment;
                $this->response([
                    'status'=>true,
                    'message'=>'Success',
                    'data'=>$data
                ], MY_Controller::HTTP_OK);
            } else{
                $this->response([
                    'status'=>false,
                    'message'=>'ID assesment is required',
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
}
