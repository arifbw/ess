<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Get_kadep_kasek extends CI_Controller {
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
            
            $get = $this->db
                ->like('nama_jabatan','kepala')
                ->where("(SUBSTR(kode_unit,4,2)='00' OR SUBSTR(kode_unit,5)='0' ) AND SUBSTR(kode_unit,3,1)!='0'")
                ->order_by('kode_unit','ASC')
                ->get('mst_jabatan')->result();
            echo json_encode([
                'status'=>true,
                'message'=>'OK',
                'data'=>$get
            ]);
        }
    }
}