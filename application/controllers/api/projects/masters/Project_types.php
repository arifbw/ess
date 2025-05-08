<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Project_types extends MY_Controller {
    
    function __construct(){
        parent::__construct();
    }
    
    function index_get(){
        try {
            $data = $this->db->select('id,kode,nama')->where('is_visible','1')->get('ess_project_types')->result();
            $this->response([
                'status'=>true,
                'message'=>'Pilihan jenis project',
                'data'=>$data
            ], MY_Controller::HTTP_OK);
        } catch(Exception $e){
            $this->response([
                'status'=>false,
                'message'=>'Error Exception',
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
        }
    }
}
