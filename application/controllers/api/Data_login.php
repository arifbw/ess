<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Data_login extends CI_Controller {
    
    function __construct(){
        parent::__construct();
    }
    
	function index() {
        echo json_encode($this->session->userdata());
	}
}
