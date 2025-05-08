<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Menu extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        if(!@$this->input->request_headers()['id_group']){
            $this->response([
                'status'=>false,
                'message'=>'ID group is required.'
            ], MY_Controller::HTTP_BAD_REQUEST); exit;
        }
        $this->id_group = $this->input->request_headers()['id_group'];
    }
    
	function index_get() {
        $get = menu_helper_mobile($this->id_group);
        try {
            $this->response([
                'status'=>true,
                'message'=>'Success',
                'data'=>$get
            ], MY_Controller::HTTP_OK);
        } catch(Exception $e){
            $this->response([
                'status'=>false,
                'message'=>'User not found',
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
        }
	}
}
