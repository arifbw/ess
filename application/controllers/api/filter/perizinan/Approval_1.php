<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Approval_1 extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->model("api/filter/M_filter_api","filter");
        $this->load->model('m_approval');
    }
    
	function index_get() {
        //$get_data = $this->m_approval->list_atasan_minimal_kadep([$kode_unit],null);
        try {
            //$get = $this->filter->jenis_perizinan()->result();
            $this->response([
                'status'=>true,
                'message'=>'Success',
                'data'=>[]
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
