<?php defined('BASEPATH') OR exit('No direct script access allowed');
include_once( APPPATH . 'core/Group_Controller.php' );
class List_karyawan extends Group_Controller {
    
    function __construct(){
        parent::__construct();
       
    }

    function index_get(){
            
        $this->db->select('*')
			 ->from("mst_karyawan");

        $this->db->order_by("no_pokok");
        $data = $this->db->get()->result_array();
        
        $this->response([
            'status'=>true,
            'message'=>"Data mst karyawan",
            'data'=>$data
        ], MY_Controller::HTTP_OK);
    }
}
