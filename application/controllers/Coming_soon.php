<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Coming_soon extends CI_Controller {
		
		public function __construct(){
			parent::__construct();
		}

		public function index(){
			$this->load->view("coming_soon");
		}
	}
	