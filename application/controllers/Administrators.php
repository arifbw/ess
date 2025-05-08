<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Administrators extends CI_Controller {
		public function __construct(){
			parent::__construct();
			
			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'administrator/';
			$this->folder_ajax_view = $this->folder_view.'ajax/';
			
			$this->load->model("m_administrator");
			//$this->load->model("m_pengaturan");

			$this->data['success'] = "";
			$this->data['warning'] = "";
			
			$this->data["is_with_sidebar"] = true;
		}

		public function index(){
			$this->data['content'] = 'dashboard';
			$this->data["navigasi_menu"] = menu_helper();
			
			$this->load->view('template',$this->data);
		}
		
		public function pengguna(){
			$this->data['judul'] = "Pengguna";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul_nama($this->data['judul']);
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."pengguna";
			
			array_push($this->data['js_sources'],"administrator/pengguna");
			
			if($this->input->post()){
				if(!strcmp($this->input->post("aksi"),"tambah")){
					$this->data['username'] = $this->input->post("username");
					$this->data['password'] = $this->input->post("password");
					$this->data['email'] = $this->input->post("email");
					$this->data['pilihan_grup'] = $this->input->post("pilihan_grup_tambah");
					if(!strcmp($this->input->post("status"),"aktif")){
						$this->data['status'] = "1";
					}
					else if(!strcmp($this->input->post("status"),"non aktif")){
						$this->data['status'] = "0";
					}

					
					$tambah = $this->m_administrator->tambah_pengguna($this->data['username'],$this->data['password'],$this->data['email'],$this->data['pilihan_grup'],$this->data['status']);
					
					$this->data['panel_tambah'] = "in";

					if($tambah['status']){
						$this->data['success'] = "Pengguna dengan <i>username</i> <b>".$this->data['username']."</b> berhasil ditambahkan.";
						$this->data['panel_tambah'] = "";
						$this->data['username'] = "";
						$this->data['password'] = "";
						$this->data['email'] = "";
						$this->data['pilihan_grup_tambah'] = "";
						$this->data['status'] = "";
					}
					else{
						$this->data['warning'] = $tambah['error_info'];
					}
				}
				else if(!strcmp($this->input->post("aksi"),"ubah")){
					$this->data['nama'] = $this->input->post("nama");
					$this->data['nama_ubah'] = $this->input->post("nama_ubah");
					
					if(!strcmp($this->input->post("status"),"aktif")){
						$this->data['status'] = "1";
					}
					else if(!strcmp($this->input->post("status"),"non aktif")){
						$this->data['status'] = "0";
					}
					
					if(!strcmp($this->input->post("status_ubah"),"aktif")){
						$this->data['status_ubah'] = "1";
					}
					else if(!strcmp($this->input->post("status_ubah"),"non aktif")){
						$this->data['status_ubah'] = "0";
					}
					$ubah = $this->m_administrator->ubah_grup_pengguna($this->data["nama"],$this->data["status"],$this->data["nama_ubah"],$this->data["status_ubah"]);

					if($ubah["status"]){
						$this->data['success'] = "Perubahan Grup Pengguna berhasil dilakukan.";
					}
					else{
						$this->data['warning'] = $ubah['error_info'];
					}

					$this->data["nama"] = "";
					$this->data["status"] = "";
					$this->data['panel_tambah'] = "";
				}
				else{
					$this->data['username'] = "";
					$this->data['password'] = "";
					$this->data['email'] = "";
					$this->data['pilihan_grup_tambah'] = "";
					$this->data['panel_tambah'] = "";
					$this->data['status'] = "";
				}
			}
			else{
				$this->data['username'] = "";
				$this->data['password'] = "";
				$this->data['email'] = "";
				$this->data['pilihan_grup_tambah'] = "";
				$this->data['panel_tambah'] = "";
				$this->data['status'] = "";
			}

			$js_header_script = "<script>
									$(document).ready(function() {
										$('#tabel_pengguna').DataTable({
											responsive: true
										});
									});
								</script>";
			array_push($this->data["js_header_script"],$js_header_script);

			$this->data["daftar_grup_pengguna"] = $this->m_administrator->daftar_grup_pengguna();

			$this->load->view('template',$this->data);
		}
	}
	
	/* End of file administrators.php */
	/* Location: ./application/controllers/administrators.php */