<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Log extends CI_Controller {
		public function __construct(){
			parent::__construct();
			
			$meta = meta_data();
			
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'login/';
			
			$this->data["is_with_sidebar"] = false;
			
			$this->data['judul'] = __CLASS__;
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		}

		public function index(){
			$this->data["daftar_pengguna"] = $this->m_pengguna->daftar_pengguna();
			
			$this->data['content'] = $this->folder_view."login";
			$this->load->view('template',$this->data);
		}
	}
	
	/* End of file log.php */
	/* Location: ./application/controllers/log.php */