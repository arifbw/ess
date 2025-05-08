<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sto extends CI_Controller {
    public function __construct(){
        parent::__construct();
    }
    
    function index_get(){
        header('Content-Type: application/json');
        if(empty( $this->input->get('key',true) )){
            echo json_encode([
                'status'=>false,
                'message'=>'Required key',
                'data'=>[]
            ]);
        } else{
            if( $this->input->get('key') != hash('sha256','trackingPengadaan'.date('Y-m-d')) ){
                echo json_encode([
                    'status'=>false,
                    'message'=>'Invalid Key',
                    'data'=>[]
                ]);
                exit;
            }

            $data = $this->db->where('object_type','O')->order_by('object_abbreviation','ASC')->get('ess_sto')->result_array();
            echo json_encode([
                'status'=>true,
                'message'=>'Data STO realtime',
                'data'=>$data
            ]);
        }
    }
}