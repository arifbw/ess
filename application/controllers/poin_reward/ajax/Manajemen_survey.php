<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Manajemen_survey extends CI_Controller {
		public function __construct(){
			parent::__construct();
			
			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'poin_reward/ajax/';
			$this->folder_model = 'poin_reward/';
			$this->akses = array();
			
			$this->load->model($this->folder_model."/m_manajemen_survey");

			$this->data['success'] = "";
			$this->data['warning'] = "";
			
			$this->data["is_with_sidebar"] = true;
		}
		
		public function salin($id){
			$data = $this->m_manajemen_survey->ambil_manajemen_survey_id($id);
			$data["function"] = __FUNCTION__;
			$this->load->view($this->folder_view."manajemen_survey",$data);
		}
		
		public function ubah($id){
			$data = $this->m_manajemen_survey->ambil_manajemen_survey_id($id);
			$data["function"] = __FUNCTION__;
			$data["judul"] = ucwords(preg_replace("/_/"," ",__CLASS__));
			$this->load->view($this->folder_view."manajemen_survey",$data);
		}
	}
	
	/* End of file manajemen_survey.php */
	/* Location: ./application/controllers/administrator/manajemen_survey.php */