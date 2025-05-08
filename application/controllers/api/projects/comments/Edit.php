<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Edit extends MY_Controller {
    
    function __construct(){
        parent::__construct();
    }
    
    function index_post(){
        $data_insert = [];
        try {
            if(empty($this->post('kode'))){
                $this->response([
                    'status'=>false,
                    'message'=>'ID harus diisi'
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($this->post('comment'))){
                $this->response([
                    'status'=>false,
                    'message'=>'Komentar harus diisi'
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                $kode = $this->post('kode');
                $data_comment = $this->db->where('kode', $kode)->get('ess_project_tasklist_comments')->row();
                if($data_comment){
                    if(date('Y-m-d H:i:s', strtotime($data_comment->created_at)) >= date('Y-m-d H:i:s', strtotime('-5 Minutes'))){
                        $data_insert['comment'] = $this->post('comment');
                        $data_insert['updated_at'] = date('Y-m-d H:i:s');
                        
                        $this->db->where('kode',$kode)->update('ess_project_tasklist_comments',$data_insert);
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
                            'message'=>'Sudah tidak dapat mengupdate komentar ini'
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
