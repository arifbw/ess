<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Hapus extends MY_Controller {
    
    function __construct(){
        parent::__construct();
    }
    
    function index_post() {        
        if(empty($this->post('id'))){
            $this->response([
                'status'=>false,
                'message'=>"ID harus diisi",
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
        } else{
            $data = $this->db->where('id',$this->post('id'))->get('ess_request_perizinan')->row();
            $this->db->where('id',$this->post('id'))->delete('ess_request_perizinan');
            $this->response([
                'status'=>true,
                'message'=>'Perizinan berhasil dihapus',
                'data'=>$data
            ], MY_Controller::HTTP_OK);
        }
    }
    
}
