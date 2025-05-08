<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Master_dws extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->model("api/filter/M_filter_api","filter");
    }
    
	function index_get() {
        $get = $this->filter->mst_jadwal_kerja()->result();
        $this->response([
            'status'=>true,
            'message'=>'Success',
            'note'=>'Ambil atribut "dws" untuk filter ke perencanaan jadwal kerja',
            'data'=>$get
        ], MY_Controller::HTTP_OK);
	}
}
