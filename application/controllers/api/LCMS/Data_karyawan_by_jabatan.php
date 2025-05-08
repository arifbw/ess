<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Data_karyawan_by_jabatan extends CI_Controller {
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
        } else if(empty( $this->input->get('kode_jabatan',true) )){
            echo json_encode([
                'status'=>false,
                'message'=>'Required Kode Jabatan',
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
            
            $get = $this->db->where('kode_jabatan',$this->input->get('kode_jabatan'))->get('mst_karyawan');
            if($get->num_rows()>0){
                echo json_encode([
                    'status'=>true,
                    'message'=>'OK',
                    'data'=>$get->result()
                ]);
            } else{
                echo json_encode([
                    'status'=>false,
                    'message'=>'Not Found',
                    'data'=>[]
                ]);
            }
        }
    }
}