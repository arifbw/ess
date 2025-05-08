<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Edit extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        $this->current_table = 'ess_project_x_milestones';
    }
    
    function index_get(){
        $data = [];
        try {
            if(empty($this->get('id'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Kode Milestone harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                $get = $this->db->where( 'kode',$this->get('id') )->get( $this->current_table );
                if( $get->num_rows()==1 ){
                    $data = $get->row();
                    $this->response([
                        'status'=>true,
                        'message'=>'Detail Milestone',
                        'data'=>$data
                    ], MY_Controller::HTTP_OK);
                } else{
                    $this->response([
                        'status'=>false,
                        'message'=>'Milestone Tidak ditemukan',
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
    
    function index_post(){
        try {
            if(empty($this->post('id'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Kode Milestone harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($this->post('milestone'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Nama Milestone harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                $data_insert = [];
                $data_insert['nama'] = $this->post('milestone');
                $data_insert['updated_at'] = date('Y-m-d H:i:s');
                $this->db->where('kode',$this->post('id'))->update($this->current_table, $data_insert);
                
                if($this->db->affected_rows()>0){
                    $this->response([
                        'status'=>true,
                        'message'=>'Update Milestone Berhasil',
                        'data'=>$data_insert
                    ], MY_Controller::HTTP_OK);
                } else{
                    $this->response([
                        'status'=>false,
                        'message'=>'Gagal update Milestone',
                        'data'=>[]
                    ], MY_Controller::HTTP_BAD_REQUEST);
                }
            }
        } catch(Exception $e){
            $this->response([
                'status'=>false,
                'message'=>'Error Exception',
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
        }
    }
    
}
