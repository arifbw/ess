<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Modul extends CI_Controller {
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
			
			$this->load->model($this->folder_model."m_modul");
			$this->load->model($this->folder_model."m_kelompok_modul");
			
			$this->data['success'] = "";
			$this->data['warning'] = "";
			
			$this->data["is_with_sidebar"] = true;
		}
		
		public function index(){
			$this->data['judul'] = "Modul";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."modul";
			
			array_push($this->data['js_sources'],"administrator/modul");

			if($this->input->post()){
				if(!strcmp($this->input->post("aksi"),"tambah")){
					izin($this->akses["tambah"]);
					
					$this->data['kelompok_modul'] = $this->input->post("kelompok_modul");
					$this->data['nama'] = $this->input->post("nama");
					$this->data['url'] = $this->input->post("url");
					$this->data['icon'] = $this->input->post("icon");
					if(!strcmp($this->input->post("status"),"aktif")){
						$this->data['status'] = "1";
					}
					else if(!strcmp($this->input->post("status"),"non aktif")){
						$this->data['status'] = "0";
					}
					
					$tambah = $this->tambah($this->data['kelompok_modul'],$this->data['nama'],$this->data['url'],$this->data['icon'],$this->data['status']);
					
					$this->data['panel_tambah'] = "in";

					if($tambah['status']){
						$this->data['success'] = "Modul dengan nama <b>".$this->data['nama']."</b> berhasil ditambahkan pada <b>".$tambah["nama_kelompok_modul"]."</b>.";

						$this->data["nama"] = "";
						$this->data["status"] = "";
						$this->data['url'] = "";
						$this->data['icon'] = "";
						$this->data['panel_tambah'] = "";
					}
					else{
						$this->data['warning'] = $tambah['error_info'];
					}
				}
				else if(!strcmp($this->input->post("aksi"),"ubah")){
					izin($this->akses["ubah"]);
					
					$this->data['kelompok_modul'] = (int)$this->input->post("kelompok_modul");
					$this->data['kelompok_modul_ubah'] = $this->input->post("kelompok_modul_ubah");
					$this->data['nama'] = $this->input->post("nama");
					$this->data['nama_ubah'] = $this->input->post("nama_ubah");
					$this->data['url'] = $this->input->post("url");
					$this->data['url_ubah'] = $this->input->post("url_ubah");
					$this->data['icon'] = $this->input->post("icon");
					$this->data['icon_ubah'] = $this->input->post("icon_ubah");
					
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

					$ubah = $this->ubah($this->data["kelompok_modul"],$this->data["nama"],$this->data["url"],$this->data["icon"],$this->data["status"],$this->data["kelompok_modul_ubah"],$this->data["nama_ubah"],$this->data["url_ubah"],$this->data["icon_ubah"],$this->data["status_ubah"]);

					if($ubah["status"]){
						$this->data['success'] = "Perubahan modul berhasil dilakukan.";
					}
					else{
						$this->data['warning'] = $ubah['error_info'];
					}

					$this->data["nama"] = "";
					$this->data["status"] = "";
					$this->data['url'] = "";
					$this->data['icon'] = "";
					$this->data['panel_tambah'] = "";
				}
				else{
					$this->data['nama'] = "";
					$this->data['url'] = "";
					$this->data['icon'] = "";
					$this->data['status'] = "";
					$this->data['panel_tambah'] = "";
				}
			}
			else{
				$this->data['nama'] = "";
				$this->data['status'] = "";
				$this->data['url'] = "";
				$this->data['icon'] = "";
				$this->data['panel_tambah'] = "";
			}
			
			if($this->akses["lihat"]){
				$js_header_script = "<script>
								$(document).ready(function() {
									$('#tabel_modul').DataTable({
										responsive: true
									});
								});
							</script>";
				
				array_push($this->data["js_header_script"],$js_header_script);
				
				$this->data["daftar_modul"] = $this->m_modul->daftar_modul();
				
				$log = array(
						"id_pengguna" => $this->session->userdata("id_pengguna"),
						"id_modul" => $this->data['id_modul'],
						"deskripsi" => "lihat ".strtolower(preg_replace("/_/"," ",__CLASS__)),
						"alamat_ip" => $this->data["ip_address"],
						"waktu" => date("Y-m-d H:i:s")
					);
				$this->m_log->tambah($log);
			}
			
			if($this->akses["tambah"] or $this->akses["ubah"]){
				$this->data["daftar_kelompok_modul"] = $this->m_kelompok_modul->daftar_kelompok_modul();
			}
			
			$this->load->view('template',$this->data);
		}
		
		private function tambah($id_kelompok_modul,$nama,$url,$icon,$status){
			$return = array("status" => false, "error_info" => "", "nama_kelompok_modul" => $this->m_kelompok_modul->ambil_nama($id_kelompok_modul));
			
			if($this->m_modul->cek_tambah_modul($id_kelompok_modul,$nama)){
				$data = array(
						"id_kelompok_modul" => $id_kelompok_modul,
						"nama" => $nama,
						"url" => $url,
						"icon" => $icon,
						"status" => $status
					);
				$this->m_modul->tambah($data);
				
				if($this->m_modul->cek_hasil_modul($id_kelompok_modul,$nama,$url,$icon,$status)){
					$return["status"] = true;
					
					$arr_data_insert = $this->m_modul->data_modul($id_kelompok_modul,$nama);
					
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
					$return["error_info"] = "Penambahan Modul <b>Gagal</b> Dilakukan pada Kelompok Modul <b>".$return["nama_kelompok_modul"]."</b>.";
				}
			}
			else{
				$return["status"] = false;
				$return["error_info"] = "Modul dengan nama <b>$nama</b> sudah ada pada Kelompok Modul <b>".$return["nama_kelompok_modul"]."</b>.";
			}
			return $return;
		}
	
		private function ubah($id_kelompok_modul,$nama,$url,$icon,$status,$id_kelompok_modul_ubah,$nama_ubah,$url_ubah,$icon_ubah,$status_ubah){
			$nama_kelompok_modul = $this->m_kelompok_modul->ambil_nama($id_kelompok_modul);
			$nama_kelompok_modul_ubah = $this->m_kelompok_modul->ambil_nama($id_kelompok_modul_ubah);
			
			$return = array("status" => false, "error_info" => "");
			
			$cek = $this->m_modul->cek_ubah_modul($id_kelompok_modul,$nama,$url,$status,$nama_kelompok_modul,$id_kelompok_modul_ubah,$nama_ubah,$url_ubah,$nama_kelompok_modul_ubah);
			if($cek["status"]){
				$set = array('id_kelompok_modul'=>$id_kelompok_modul_ubah,'nama'=>$nama_ubah,'url'=>$url_ubah,'icon'=>$icon_ubah,'status'=>$status_ubah);
				$arr_data_lama = $this->m_modul->data_modul($id_kelompok_modul,$nama);
				$log_data_lama = "";
				
				foreach($arr_data_lama as $key => $value){
					if(strcmp($key,"id")!=0){
						if(!empty($log_data_lama)){
							$log_data_lama .= "<br>";
						}
						$log_data_lama .= "$key = $value";
					}
				}
				
				$this->m_modul->ubah($set,$id_kelompok_modul,$nama,$url,$icon,$status);

				if($this->m_modul->cek_hasil_modul($id_kelompok_modul_ubah,$nama_ubah,$url_ubah,$icon_ubah,$status_ubah)){
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
					$return["error_info"] = "Perubahan Modul <b>Gagal</b> Dilakukan.";
				}
			}
			else{
				$return["status"] = $cek["status"];
				$return["error_info"] = $cek["error_info"];	
			}

			return $return;
		}
	}
	
	/* End of file modul.php */
	/* Location: ./application/controllers/administrator/modul.php */