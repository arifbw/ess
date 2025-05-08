<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Data_karyawan extends CI_Controller {
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
            if( $this->input->get('key') != hash('sha256','qumonPeruri2021'.date('Y-m-d')) ){
                echo json_encode([
                    'status'=>false,
                    'message'=>'Invalid Key',
                    'data'=>[]
                ]);
                exit;
            }
            
            $get = $this->db->get('mst_karyawan')->result_array();
            echo json_encode([
                'status'=>true,
                'message'=>'Data Karyawan',
                'data'=>$get
            ]);
        }
    }
}