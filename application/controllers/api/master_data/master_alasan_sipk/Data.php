<?php defined('BASEPATH') OR exit('No direct script access allowed');
include_once( APPPATH . 'core/Group_Controller.php' );
class Data extends Group_Controller {
    
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
    
    function index_get(){
        $get = $this->db->get('mst_sipk_alasan')->result();
        $this->response([
            'status'=>true,
            'message'=>'Success',
            'data'=>$get
        ], MY_Controller::HTTP_OK);
    }
}
