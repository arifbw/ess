<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Project_milestones extends MY_Controller {
    
    function __construct(){
        parent::__construct();
    }
    
    function index_get(){
        try {
            $data = $this->db->select('id,kode,nama')->get('ess_project_milestones')->result();
            $this->response([
                'status'=>true,
                'message'=>'Pilihan milestone Project',
                'description'=>'Dropdown milestone saat input project, ambil id dan nama. Nama bisa dicustom, tp default ambil dari master',
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
