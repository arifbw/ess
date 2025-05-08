<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Lupa_password extends CI_Controller {
		public function __construct(){
			parent::__construct();
			
			$meta = meta_data();
			
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->load->model("m_login");			
			
			$this->folder_view = 'login/';
			
			$this->data["is_with_sidebar"] = false;
			
			$this->data['judul'] = ucwords(str_replace("_"," ",__CLASS__));
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		}

		public function index(){
			if(!empty($this->session->userdata("username"))){
				redirect(base_url());
			}
			
			$this->data["warning"] = "";
			$this->data["success"] = "";
			
			if($this->input->post()){
				if(!empty($this->input->post("username"))){
					$user = $this->m_login->validasi_username($this->input->post("username"));
					if($user["status"]){
						$chars	= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
						$nums	= "0123456789";
						
						$new_password = "";
						
						$panjang_password=$this->m_setting->ambil_pengaturan("Panjang Password");
						$panjang_password=substr($panjang_password,0,strpos($panjang_password," "));
						
						while(strlen($new_password)<$panjang_password){
							if(rand(0,1)==0){
								$new_password .= substr($chars,rand(0,strlen($chars)),1);
							}
							else{
								$new_password .= substr($nums,rand(0,strlen($nums)),1);
							}
						}
						
						$this->email_lupa_password($this->input->post("username"),$new_password);
						
						$this->m_login->ubah_password($this->input->post("username"),md5($new_password),true);
						
						$this->data["success"] = "Password baru telah terkirim ke email Anda.";
					}
					else{
						$this->data["warning"] = "Username tidak terdaftar.";
					}
				}
			}
			
			$this->data['content'] = $this->folder_view."login";
			$this->load->view('template',$this->data);
		}
		
		private function email_lupa_password($username,$new_password){
			$this->load->helper("email_helper");
			
			$subject = "Konfirmasi Lupa Password Aplikasi ESS";
			$content = "";
			
			$content .= "<table>";
				$content .= "<tr>";
					$content .= "<td>Username</td>";
					$content .= "<td>:</td>";
					$content .= "<td>$username</td>";
				$content .= "</tr>";
				$content .= "<tr>";
					$content .= "<td>Password</td>";
					$content .= "<td>:</td>";
					$content .= "<td>$new_password</td>";
				$content .= "</tr>";
			$content .= "</table>";
			
			$to = $username."@peruri.co.id";
			
			kirim_email($subject,$content,$to);
		}
	}
	
	/* End of file Lupa_password.php */
	/* Location: ./application/controllers/Lupa_password.php */
