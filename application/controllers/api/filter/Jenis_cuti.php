<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Jenis_cuti extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->model("api/filter/M_filter_api","filter");
    }
    
	function index_get() {
        try {
            $get = $this->filter->jenis_cuti()->result();
            $this->response([
                'status'=>true,
                'message'=>'Success',
                'data'=>$get
            ], MY_Controller::HTTP_OK);
        } catch(Exception $e){
            $this->response([
                'status'=>false,
                'message'=>'Error Exception',
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
        }
	}
}
