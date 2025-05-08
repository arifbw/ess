<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Np extends MY_Controller {
    
    function __construct(){
        parent::__construct();
    }
    
	function index_get() {
        $data = bin2hex($this->data_karyawan->np_karyawan.'#'.date('Y-m-d H:i:s'));
        $this->response([
            'status'=>true,
            'message'=>'QR Code NP: '.$this->data_karyawan->np_karyawan,
            'data'=>$data
        ], MY_Controller::HTTP_OK);
	}
}
