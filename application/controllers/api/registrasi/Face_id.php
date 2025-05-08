<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Face_id extends MY_Controller {
    
    function __construct(){
        parent::__construct();
    }
    
    function index_post(){
        $data = [];
        $data_insert = [];
        
        try {
            if(empty($this->post('face_id'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Face ID harus diisi",
                    'data'=>$data
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                $face_id = $this->post('face_id');
                $data_insert['face_id'] = $face_id;

                $this->db->where('id',$this->account->id)->update('usr_pengguna',$data_insert);
                if($this->db->affected_rows()>0){
                    $this->response([
                        'status'=>true,
                        'message'=>'Berhasil mendaftarkan Wajah',
                        'data'=>$data_insert
                    ], MY_Controller::HTTP_OK);
                } else{
                    $this->response([
                        'status'=>false,
                        'message'=>"Gagal",
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
