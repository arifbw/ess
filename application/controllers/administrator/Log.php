<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Log extends CI_Controller {
		public function __construct(){
			parent::__construct();
			
			$meta = meta_data();
			
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'administrator/';
			
			$this->data["is_with_sidebar"] = true;
			
			$this->data['judul'] = __CLASS__;
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
		}
		
		public function index(){
			izin($this->akses["akses"]);
			$this->data['content'] = $this->folder_view."log";
			$this->data["navigasi_menu"] = menu_helper();
			
			$this->load->model("administrator/m_aksi_modul");
			$this->data["daftar_modul_aksi"] = $this->m_aksi_modul->daftar_modul_aksi();
						
			$this->data["id_pengguna"] = "";
			$this->data["id_modul"] = "";
			$this->data["id_target"] = "";
			$this->data["nama_target_judul"] = "";
			$this->data["isi_target_judul"] = "";
			$this->data["url_modul"] = "";
				
			$this->load->model("administrator/m_pengguna");
			if($this->akses["lihat log pengguna lain"]){
				$this->data["daftar_pengguna"] = $this->m_pengguna->daftar_pengguna();
			}
			else{
				$this->data["daftar_pengguna"] = $this->m_pengguna->ambil_data_pengguna($this->session->userdata("id_pengguna"));
				$this->data["id_pengguna"] = $this->session->userdata("id_pengguna");
			}
			
			if($this->input->post()){
				$this->data["id_pengguna"] = $this->input->post("pengguna");
				$this->data["id_modul"] = $this->input->post("modul");
				$this->data["id_target"] = $this->input->post("target");
				$this->data["nama_target_judul"] = $this->input->post("nama_target_judul");
				$this->data["isi_target_judul"] = $this->input->post("isi_target_judul");
				$this->data["url_modul"] = $this->input->post("url_modul");
			}
			$this->data["daftar_log"] = $this->daftar_log($this->data["id_pengguna"],$this->data["id_modul"],$this->data["id_target"]); 
			
			$js_header_script = "<script>
									$(document).ready(function() {
										$('#tabel_log').DataTable({
											responsive: true
										});
                                        $('.select2').select2();
									});
								</script>";
			array_push($this->data["js_header_script"],$js_header_script);
			$this->load->view('template',$this->data);
		}

		private function daftar_log($id_pengguna,$id_modul="",$id_target=""){
			$daftar_log = $this->m_log->lihat($id_pengguna,$id_modul,$id_target);
			
			return $daftar_log;
		}
	}
	
	/* End of file log.php */
	/* Location: ./application/controllers/log.php */