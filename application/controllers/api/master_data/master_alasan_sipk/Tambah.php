<?php defined('BASEPATH') OR exit('No direct script access allowed');
include_once( APPPATH . 'core/Group_Controller.php' );
class Tambah extends Group_Controller {
    
    function __construct(){
        parent::__construct();
        if(!in_array($this->id_group,[15])){
            $this->response([
                'status'=>false,
                'message'=>"Otoritas tidak diizinkan",
                'data'=>[]
            ], MY_Controller::HTTP_FORBIDDEN);
        }
    }
    
    function index_post(){
        if(empty($this->post('alasan'))){ 
            $this->response([
                'status'=>false,
                'message'=>"Alasan harus diisi",
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
            exit();
        }
        $alasan = $this->post('alasan');
        
        $cek = $this->db->where('LOWER(alasan)',strtolower($alasan))->get('mst_sipk_alasan');
        if( $cek->num_rows()>0 ){
            $this->response([
                'status'=>false,
                'message'=>"Alasan {$alasan} sudah pernah diinput",
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
            exit();
        }

        $this->db->insert('mst_sipk_alasan',[
            'alasan'=>$alasan,
            'status'=>@$this->post('status') ? ($this->post('status')==1 ? 1:0) : 1
        ]);
        
        $this->response([
            'status'=>true,
            'message'=>"Alasan {$alasan} berhasil diinput",
            'data'=>$data
        ], MY_Controller::HTTP_OK);
    }
}
