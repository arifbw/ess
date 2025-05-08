<?php defined('BASEPATH') OR exit('No direct script access allowed');
include_once( APPPATH . 'core/Group_Controller.php' );
class Pembatalan extends Group_Controller {
    
    function __construct(){
        parent::__construct();
        if(!in_array($this->id_group,[4,5,7])){
            $this->response([
                'status'=>false,
                'message'=>"Otoritas tidak diizinkan",
                'data'=>[]
            ], MY_Controller::HTTP_FORBIDDEN);
        }
    }
    
    function index_post(){
        if(empty($this->post('id'))){ 
            $this->response([
                'status'=>false,
                'message'=>"Id harus diisi",
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
        } else{
            $data_insert = [
                'canceled_np'=>$this->data_karyawan->np_karyawan,
                'canceled_nama'=>$this->data_karyawan->nama,
                // 'canceled_group'=>null,
                'canceled_at'=>date('Y-m-d H:i:s')
            ];
    
            $this->db->where('id', $this->post('id'))->update('ess_permohonan_spbe',$data_insert);

            $this->response([
                'status'=>true,
                'message'=>"Berhasil melakukan pembatalan",
                'data'=>$data_insert
            ], MY_Controller::HTTP_OK);
        }
    }
}
