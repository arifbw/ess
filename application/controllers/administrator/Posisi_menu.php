<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Posisi_menu extends CI_Controller {
		public function __construct(){
			parent::__construct();
			
			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'administrator/';
			$this->folder_model = 'administrator/';
			$this->folder_ajax_view = $this->folder_view.'ajax/';
			$this->akses = array();
			
			$this->load->model($this->folder_model."m_posisi_menu");

			$this->data['success'] = "";
			$this->data['warning'] = "";
			
			$this->data["is_with_sidebar"] = true;
		}
		
		public function index(){
			$this->data['judul'] = "Posisi Menu";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->data['id_kelompok_modul'] = $this->m_setting->ambil_id_kelompok_modul($this->data['id_modul']);
			$this->data['nama_kelompok_modul'] = $this->m_setting->ambil_nama_kelompok_modul($this->data['id_kelompok_modul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."posisi_menu";

			array_push($this->data['js_sources'],"administrator/posisi_menu");
			
			if($this->input->post()){
				if(!strcmp($this->input->post("aksi"),"tambah")){
					izin($this->akses["tambah"]);
					
					$this->data['nama'] = $this->input->post("nama");
					$this->data['shortcode'] = $this->input->post("shortcode");
					
					$tambah = $this->tambah($this->data['nama'],$this->data['shortcode']);
					
					$this->data['panel_tambah'] = "in";

					if($tambah['status']){
						$this->data['success'] = "Posisi Menu dengan nama <b>".$this->data['nama']."</b> berhasil ditambahkan.";
						$this->data['panel_tambah'] = "";
						$this->data['nama'] = "";
						$this->data['shortcode'] = "";
					}
					else{
						$this->data['warning'] = $tambah['error_info'];
					}
				}
				else if(!strcmp($this->input->post("aksi"),"ubah")){
					izin($this->akses["ubah"]);
					$this->data['nama'] = $this->input->post("nama");
					$this->data['nama_ubah'] = $this->input->post("nama_ubah");
					$this->data['shortcode'] = $this->input->post("shortcode");
					$this->data['shortcode_ubah'] = $this->input->post("shortcode_ubah");
					
					$ubah = $this->ubah($this->data["nama"],$this->data["shortcode"],$this->data["nama_ubah"],$this->data["shortcode_ubah"]);
					
					if($ubah["status"]){
						$this->data['success'] = "Perubahan posisi menu berhasil dilakukan.";
					}
					else{
						$this->data['warning'] = $ubah['error_info'];
					}

					$this->data["nama"] = "";
					$this->data["status"] = "";
					$this->data['url'] = "";
					$this->data['panel_tambah'] = "";
				}
				else{
					$this->data['panel_tambah'] = "";
					$this->data['nama'] = "";
					$this->data['url'] = "";
					$this->data['icon'] = "";
					$this->data['status'] = "";
				}
			}
			else{
				$this->data['nama'] = "";
				$this->data['shortcode'] = "";
				$this->data['panel_tambah'] = "";
			}
			
			if($this->akses["lihat"]){
				$js_header_script = "<script>
								$(document).ready(function() {
									$('#tabel_posisi_menu').DataTable({
										responsive: true
									});
								});
							</script>";
				array_push($this->data["js_header_script"],$js_header_script);
				
				$this->data["daftar_posisi_menu"] = $this->m_posisi_menu->daftar_posisi_menu();
			
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
		
		public function tambah($nama,$shortcode){
			$return = array("status" => false, "error_info" => "");
			if($this->m_posisi_menu->cek_tambah_posisi_menu($nama,$shortcode)){
				$data = array(
							"nama" => $nama,
							"shortcode" => $shortcode
						);
				$this->m_posisi_menu->tambah($data);
				
				if($this->m_posisi_menu->cek_hasil_posisi_menu($nama,$shortcode)){
					$return["status"] = true;
					
					$arr_data_insert = $this->m_posisi_menu->data_posisi_menu($nama);
					
					$log_data_baru = "";
					
					foreach($data as $key => $value){
						if(!empty($log_data_baru)){
							$log_data_baru .= "<br>";
						}
						$log_data_baru .= "$key = $value";
					}
					
					$log = array(
						"id_pengguna" => $this->session->userdata("id_pengguna"),
						"id_modul" => $this->data['id_modul'],
						"id_target" => $arr_data_insert['id'],
						"deskripsi" => "tambah ".strtolower(preg_replace("/_/"," ",__CLASS__)),
						"kondisi_baru" => $log_data_baru,
						"alamat_ip" => $this->data["ip_address"],
						"waktu" => date("Y-m-d H:i:s")
					);
					$this->m_log->tambah($log);
				}
				else{
					$return["status"] = false;
					$return["error_info"] = "Penambahan Posisi Menu <b>Gagal</b> Dilakukan.";
				}
			}
			else{
				$return["status"] = false;
				$return["error_info"] = "Posisi Menu dengan nama <b>$nama</b> atau <i>shortcode</i> <b>$shortcode</b> sudah ada.";
			}
			return $return;
		}
		
		private function ubah($nama,$shortcode,$nama_ubah,$shortcode_ubah){
			$return = array("status" => false, "error_info" => "");
			$cek = $this->m_posisi_menu->cek_ubah_posisi_menu($nama,$shortcode,$nama_ubah,$shortcode_ubah);
			if($cek["status"]){
				$set = array('nama'=>$nama_ubah,'shortcode'=>$shortcode_ubah);
				$arr_data_lama = $this->m_posisi_menu->data_posisi_menu($nama);
				$log_data_lama = "";
				
				foreach($arr_data_lama as $key => $value){
					if(strcmp($key,"id")!=0){
						if(!empty($log_data_lama)){
							$log_data_lama .= "<br>";
						}
						$log_data_lama .= "$key = $value";
					}
				}
				
				$this->m_posisi_menu->ubah($set,$nama,$shortcode);

				if($this->m_posisi_menu->cek_hasil_posisi_menu($nama_ubah,$shortcode_ubah)){
					$return["status"] = true;
					
					$log_data_baru = "";
					foreach($set as $key => $value){
						if(!empty($log_data_baru)){
							$log_data_baru .= "<br>";
						}
						$log_data_baru .= "$key = $value";
					}
					
					$log = array(
						"id_pengguna" => $this->session->userdata("id_pengguna"),
						"id_modul" => $this->data['id_modul'],
						"id_target" => $arr_data_lama["id"],
						"deskripsi" => "ubah ".strtolower(preg_replace("/_/"," ",__CLASS__)),
						"kondisi_lama" => $log_data_lama,
						"kondisi_baru" => $log_data_baru,
						"alamat_ip" => $this->data["ip_address"],
						"waktu" => date("Y-m-d H:i:s")
					);
					$this->m_log->tambah($log);
				}
				else{
					$return["status"] = false;
					$return["error_info"] = "Perubahan Posisi Menu <b>Gagal</b> Dilakukan.";
				}
			}
			else{
				$return["status"] = $cek["status"];
				$return["error_info"] = $cek["error_info"];	
			}

			return $return;
		}

	}
	
	/* End of file posisi_menu.php */
	/* Location: ./application/controllers/administrator/posisi_menu.php */