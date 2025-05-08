<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Group_Controller extends MY_Controller {	

	function __construct(){
        parent::__construct();
        
        # cek id_group
        if(!@$this->input->request_headers()['id_group']){
            $this->response([
                'status'=>false,
                'message'=>'ID group is required.'
            ], MY_Controller::HTTP_BAD_REQUEST); exit;
        }
        $this->id_group = $this->input->request_headers()['id_group'];
    }

}
