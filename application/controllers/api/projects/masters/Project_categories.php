<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Project_categories extends MY_Controller {
    
    function __construct(){
        parent::__construct();
    }
    
    function index_get(){
        try {
            $data = $this->db->select('id,kode,nama')->get('ess_project_categories')->result();
            $this->response([
                'status'=>true,
                'message'=>'Pilihan kategori/tagging Project',
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
