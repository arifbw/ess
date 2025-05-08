<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Data extends MY_Controller {
    
    function __construct(){
        parent::__construct();
    }
    
	function index_get() {
        $get = $this->db->where('status',1)->get('mst_pos')->result();
        $this->response([
            'status'=>true,
            'message'=>'List POS',
            'data'=>$get
        ], MY_Controller::HTTP_OK);
	}
}
