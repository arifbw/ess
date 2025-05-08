<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Profil extends MY_Controller {
    
    function __construct(){
        parent::__construct();
    }
    
	function index_get() {
        try {
            $data = $this->data_karyawan;
            $data->face_id = $this->account->face_id;
            $this->response([
                'status'=>true,
                'message'=>'Success',
                'data'=>$data
            ], MY_Controller::HTTP_OK);
        } catch(Exception $e){
            $this->response([
                'status'=>false,
                'message'=>'User not found',
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
        }
	}
}
