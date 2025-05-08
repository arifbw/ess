<?php defined('BASEPATH') OR exit('No direct script access allowed');
include_once( APPPATH . 'core/Group_Controller.php' );
class Data extends Group_Controller {
    
    function __construct(){
        parent::__construct();
        if(!in_array($this->id_group,[15])){
            $this->response([
                'status'=>false,
                'message'=>"Hanya bisa diakses oleh otoritas: Admin Pamsiknilmat Masterdata",
                'data'=>[]
            ], MY_Controller::HTTP_FORBIDDEN);
        }
    }
    
    function index_get(){
        $data = $this->db->from('mst_pos')->order_by("kode_pos asc")->get()->result_array();
        
        $this->response([
            'status'=>true,
            'message'=>"Data Pos",
            'data'=>$data
        ], MY_Controller::HTTP_OK);
    }
}
