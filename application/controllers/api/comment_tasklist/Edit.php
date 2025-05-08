<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Edit extends MY_Controller {
    
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
            } else if(empty($this->post('comment'))){
                $this->response([
                    'status'=>false,
                    'message'=>'Target pekerjaan harus diisi'
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                $id = $this->post('id');
                $data_comment = $this->db->where('id', $id)->get('ess_performance_comment')->row();
                if($data_comment){
                    if(date('Y-m-d H:i:s', strtotime($data_comment->created_at)) >= date('Y-m-d H:i:s', strtotime('-5 Minutes'))){
                        $data_insert['comment'] = $this->post('comment');
                        
                        $this->db->where('id',$id)->update('ess_performance_comment',$data_insert);
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
                    } else {
                        $this->response([
                            'status'=>false,
                            'message'=>'Sudah tidak dapat mengupdate komentar ini !'
                        ], MY_Controller::HTTP_BAD_REQUEST);
                    }
                } else {
                    $this->response([
                        'status'=>false,
                        'message'=>'Komentar tidak ditemukan !'
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
