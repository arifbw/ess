<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Logout extends CI_Controller {
		public function __construct(){
			parent::__construct();
			
			$meta = meta_data();
			
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			//$this->load->model("m_logout");
			
			$this->folder_view = 'login/';
			
			$this->data["is_with_sidebar"] = false;
			
			$this->data['judul'] = __CLASS__;
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		}

		public function index(){
			$this->logout();
		}
		
		private function logout(){
			$log = array(
					"id_pengguna" => $this->session->userdata("id_pengguna"),
					"id_modul" => $this->data['id_modul'],
					"deskripsi" => preg_replace("/_/"," ",__FUNCTION__),
					"kondisi_lama" => "login",
					"kondisi_baru" => "berhasil logout",
					"alamat_ip" => $this->data["ip_address"],
					"waktu" => date("Y-m-d H:i:s")
				);
			$this->session->sess_destroy();
			$this->m_log->tambah($log);
			redirect("home");
		}
	}
	
	/* End of file logout.php */
	/* Location: ./application/controllers/logout.php */