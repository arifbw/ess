<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Jadwal_kerja extends CI_Controller {
		public function __construct(){
			parent::__construct();
			
			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'master_data/ajax/';
			$this->folder_model = 'master_data/';
			$this->akses = array();
			
			$this->load->model($this->folder_model."/m_jadwal_kerja");

			$this->data['success'] = "";
			$this->data['warning'] = "";
			
			$this->data["is_with_sidebar"] = true;
		}
		
		public function salin($id){
			$data = $this->m_jadwal_kerja->ambil_jadwal_kerja_id($id);
			$data["function"] = __FUNCTION__;
			$this->load->view($this->folder_view."jadwal_kerja",$data);
		}
		
		public function ubah($id){
			$data = $this->m_jadwal_kerja->ambil_jadwal_kerja_id($id);
			$data["function"] = __FUNCTION__;
			$data["dws_start_time"] = substr($data["dws_start_time"],0,5);
			$data["dws_break_start_time"] = substr($data["dws_break_start_time"],0,5);
			$data["dws_break_end_time"] = substr($data["dws_break_end_time"],0,5);
			$data["dws_end_time"] = substr($data["dws_end_time"],0,5);
			$data["judul"] = ucwords(preg_replace("/_/"," ",__CLASS__));
			$this->load->view($this->folder_view."jadwal_kerja",$data);
		}
	}
	
	/* End of file jadwal_kerja.php */
	/* Location: ./application/controllers/administrator/jadwal_kerja.php */