<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Profil extends CI_Controller {
		public function __construct(){
			parent::__construct();
			
			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'pengguna/';
			$this->folder_model = 'pengguna/';
			$this->akses = array();
			
			$this->load->model($this->folder_model."m_profil");

			$this->data['success'] = "";
			$this->data['warning'] = "";
			
			$this->data["is_with_sidebar"] = true;
		}
		
		public function index(){
			$this->data['judul'] = "Profil";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."profil";
			
			array_push($this->data['js_sources'],"pengguna/profil");

			if($this->input->post()){
				if(!empty($this->input->post("password_lama")) and !empty($this->input->post("password_baru")) and !empty($this->input->post("konfirmasi_password_baru"))){
					izin($this->akses["ubah profil"]);
					
					$username = $this->input->post("username");
					$password_lama = $this->input->post("password_lama");
					$password_baru = $this->input->post("password_baru");
					$konfirmasi_password_baru = $this->input->post("konfirmasi_password_baru");
					if(strcmp($password_baru,$konfirmasi_password_baru)!=0){
						$this->data['warning'] = "Password gagal diubah karena Konfirmasi <i>Password</i> Baru tidak sesuai.";
					}
					else{
						$ubah = $this->ubah($username,$password_lama,$password_baru);
					}
					
					if($ubah['status']){
						if(strcmp($username,$this->session->userdata("userame"))==0){
							$masa_aktif_password = $this->m_setting->ambil_pengaturan("Masa Aktif Password");
							$sisa_usia_password	= (int)substr($masa_aktif_password,0,strpos($masa_aktif_password," "));
							$this->session->set_userdata("sisa_usia_password", $sisa_usia_password);
						}
						$this->data['success'] = "Password berhasil diubah.";
					}
					else{
						$this->data['warning'] = $ubah['error_info'];
					}
				}
			}
			else{
			}
			
			if($this->akses["lihat"]){			
				$this->data["profil"] = $this->m_profil->data_profil($this->session->userdata("username"));
				
				$masa_aktif_password = $this->m_setting->ambil_pengaturan("Masa Aktif Password");
				
				$this->data["profil"]["sisa_usia_password"] = (int)substr($masa_aktif_password,0,strpos($masa_aktif_password," ")) - (int)$this->data["profil"]["usia_password"];
				
				$this->data["profil"]["satuan_usia_password"] = substr($masa_aktif_password,strpos($masa_aktif_password," "));
				
				$this->data["profil"]["default_password"] = (bool)$this->data["profil"]["default_password"];
				
				$log = array(
						"id_pengguna" => $this->session->userdata("id_pengguna"),
						"id_modul" => $this->data['id_modul'],
						"deskripsi" => "lihat ".strtolower(preg_replace("/_/"," ",__CLASS__)),
						"alamat_ip" => $this->data["ip_address"],
						"waktu" => date("Y-m-d H:i:s")
					);
				$this->m_log->tambah($log);
			}
			
			$this->load->view('template',$this->data);
		}
	
		private function ubah($username,$password_lama,$password_baru){
			$return = array("status" => false, "error_info" => "");
			$this->load->model("m_login");
			$cek = $this->m_login->validasi_username_password($username,md5($password_lama));
			if($cek["status"]){
				$this->m_login->ubah_password($username,md5($password_baru));
				
				$cek = $this->m_login->validasi_username_password($username,md5($password_baru));

				if($cek["status"]){
					$return["status"] = true;
					
					$log = array(
						"id_pengguna" => $this->session->userdata("id_pengguna"),
						"id_modul" => $this->data['id_modul'],
						"id_target" => $cek["id_pengguna"],
						"deskripsi" => "ubah password",
						"alamat_ip" => $this->data["ip_address"],
						"waktu" => date("Y-m-d H:i:s")
					);
					$this->m_log->tambah($log);
				}
				else{
					$return["status"] = false;
					$return["error_info"] = "Perubahan Password <b>Gagal</b> Dilakukan.";
				}
			}
			else{
				$return["status"] = $cek["status"];
				$return["error_info"] = "Kombinasi <i>Username</i> dan <i>Password</i> Lama salah.";
			}

			return $return;
		}
	}
	
	
	/* End of file profil.php */
	/* Location: ./application/controllers/pengguna/Profil.php */