<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Data_sto extends CI_Controller {
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
            if( $this->input->get('key') != hash('sha256','lcmsPeruri'.date('Y-m-d')) ){
                echo json_encode([
                    'status'=>false,
                    'message'=>'Invalid Key',
                    'data'=>[]
                ]);
                exit;
            }
            
            $get = $this->db->where('object_type','O')->where('object_abbreviation!=','99997')->order_by('object_abbreviation','ASC')->get('ess_sto')->result();
            echo json_encode([
                'status'=>true,
                'message'=>'OK',
                'data'=>$get
            ]);
        }
    }
}