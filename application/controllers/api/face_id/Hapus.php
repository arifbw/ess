<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Hapus extends MY_Controller {
    
    function __construct(){
        parent::__construct();
    }
    
    function index_get(){
        $data = [];
        $data_insert = [];
        
        try {
            $data_insert['face_id'] = null;

            $this->db->where('id',$this->account->id)->update('usr_pengguna', $data_insert);

            if($this->db->affected_rows()>0){
                $this->response([
                    'status'=>true,
                    'message'=>'Face ID telah dihapus',
                    'data'=>['datetime'=>date('Y-m-d H:i:s')]
                ], MY_Controller::HTTP_OK);
            } else{
                $this->response([
                    'status'=>false,
                    'message'=>'Gagal',
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
