<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Delete extends MY_Controller {
    
    function __construct(){
        parent::__construct();
    }
    
    function index_post(){
        $data_insert = [];
        try {
            if( empty($this->post('kode')) ){
                $this->response([
                    'status'=>false,
                    'message'=>'Kode harus diisi'
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                $kode = $this->post('kode');
                
                $this->db->where('kode',$kode)->delete('ess_project_tasklist_comments');
                if($this->db->affected_rows()>0){
                    $this->response([
                        'status'=>true,
                        'message'=>'Deleted'
                    ], MY_Controller::HTTP_OK);
                } else{
                    $this->response([
                        'status'=>false,
                        'message'=>'Failed to delete'
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
