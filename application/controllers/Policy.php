<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Policy extends CI_Controller {
		public function __construct(){
			parent::__construct();
			
			$meta = meta_data();
			
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			
		}

		public function index(){
			
			$this->load->helper('download');
			
			$data   = file_get_contents('./file/privacy_policy_ess_mobile.pdf');
			
			$name   = "privacy_policy_ess_mobile.pdf";
			//force_download($name, $data);
            
			$this->load->view('policy/policy');
		}
		
			
		
	}
	/* End of file login.php */
	/* Location: ./application/controllers/login.php */