<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Add extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->model('api/comment_tasklist/M_comment_api','comment');
    }
    
    function index_post(){
        $data = [];
        $data_insert = [];

        try {
            if(empty($this->post('tasklist_id'))){
                $this->response([
                    'status'=>false,
                    'message'=>"tasklist_id harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($this->post('comment'))){
                $this->response([
                    'status'=>false,
                    'message'=>"isi Komentar harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                
                $data_insert['ess_performance_management_id'] = $this->post('tasklist_id');
                $data_insert['np_karyawan'] = $this->post('np_karyawan');
                $data_insert['comment'] = $this->post('comment');
                $data_insert['created_at'] = date('Y-m-d H:i:s');

                $this->db->insert('ess_performance_comment', $data_insert);

                if($this->db->affected_rows()>0){
                    $this->response([
                        'status'=>true,
                        'message'=>'Berhasil disimpan',
                        'data'=>$data_insert
                    ], MY_Controller::HTTP_OK);
                } else{
                    $this->response([
                        'status'=>false,
                        'message'=>'Gagal menambahkan list baru',
                        'data'=>$data
                    ], MY_Controller::HTTP_BAD_REQUEST);
                }
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
