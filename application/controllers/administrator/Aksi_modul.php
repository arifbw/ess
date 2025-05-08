<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Aksi_modul extends CI_Controller {
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
			
			$this->load->model($this->folder_model."m_aksi_modul");

			$this->data['success'] = "";
			$this->data['warning'] = "";
			
			$this->data["is_with_sidebar"] = true;
		}
		
		public function index(){
			$this->data['judul'] = "Aksi Modul";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);
			
			$this->data["akses"] = $this->akses;
			$this->data['content'] = $this->folder_view."aksi_modul";
			$this->data["navigasi_menu"] = menu_helper();
			
			array_push($this->data['js_sources'],"administrator/aksi_modul");
			
			if($this->akses["tambah"] or $this->akses["ubah"]){
				$this->data["daftar_modul_aksi"] = $this->m_aksi_modul->daftar_modul_aksi();
			}
			
			if($this->input->post()){
				if(!strcmp($this->input->post("aksi"),"tambah")){
					izin($this->akses["tambah"]);
					
					$this->data['modul'] = $this->input->post("modul");
					$this->data['nama'] = $this->input->post("nama");
					if(!strcmp($this->input->post("status"),"aktif")){
						$this->data['status'] = "1";
					}
					else if(!strcmp($this->input->post("status"),"non aktif")){
						$this->data['status'] = "0";
					}
					
					$tambah = $this->tambah($this->data['modul'],$this->data['nama'],$this->data['status']);
					
					$this->data['panel_tambah'] = "in";

					if($tambah['status']){
						$nama_modul = $this->m_aksi_modul->ambil_nama_modul($this->data["modul"]);
						$this->data['success'] = "Aksi untuk modul <b>$nama_modul</b> dengan nama <b>".$this->data['nama']."</b> berhasil ditambahkan.";
						$this->data['panel_tambah'] = "";
						$this->data['modul'] = "";
						$this->data['nama'] = "";
						$this->data['status'] = "";
					}
					else{
						$this->data['warning'] = $tambah['error_info'];
					}
				}
				else if(!strcmp($this->input->post("aksi"),"ubah")){
					izin($this->akses["ubah"]);
					$this->data['modul'] = $this->m_setting->ambil_id_modul($this->input->post("modul"));
					$this->data['modul_ubah'] = $this->m_setting->ambil_id_modul($this->input->post("modul_ubah"));
					
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
					
					$ubah = $this->ubah($this->data["modul"],$this->data["nama"],$this->data["status"],$this->data["modul_ubah"],$this->data["nama_ubah"],$this->data["status_ubah"]);

					if($ubah["status"]){
						$this->data['success'] = "Perubahan Aksi Modul berhasil dilakukan.";
					}
					else{
						$this->data['warning'] = $ubah['error_info'];
					}

					$this->data["nama"] = "";
					$this->data["status"] = "";
					$this->data['panel_tambah'] = "";
				}
				else{
					$this->data['nama'] = "";
					$this->data['panel_tambah'] = "";
					$this->data['status'] = "";
				}
			}
			else{
				$this->data['modul'] = "";
				$this->data['nama'] = "";
				$this->data['status'] = "";
				$this->data['panel_tambah'] = "";
			}
			if($this->akses["lihat"]){
				$js_header_script = "<script>
										$(document).ready(function() {
											$('#tabel_aksi_modul').DataTable({
												responsive: true
											});
										});
									</script>";
				array_push($this->data["js_header_script"],$js_header_script);
				
				$this->data["daftar_aksi_modul"] = $this->m_aksi_modul->daftar_aksi_modul();
				
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
		
		private function tambah($modul,$nama,$status){
			$return = array("status" => false, "error_info" => "");
			if($this->m_aksi_modul->cek_tambah_aksi_modul($modul,$nama)){
				$data = array(
							"id_modul" => $modul,
							"nama" => $nama,
							"status" => $status
						);
				$this->m_aksi_modul->tambah($data);

				if($this->m_aksi_modul->cek_hasil_aksi_modul($modul,$nama,$status)){
					$return["status"] = true;
					
					$arr_data_insert = $this->m_aksi_modul->data_aksi_modul($modul,$nama);
					
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
					$return["error_info"] = "Penambahan Aksi Modul <b>Gagal</b> Dilakukan.";
				}
			}
			else{
				$return["status"] = false;
				$return["error_info"] = "Aksi Modul dengan nama <b>$nama</b> sudah ada.";
			}
			return $return;
		}
	
		private function ubah($modul,$nama,$status,$modul_ubah,$nama_ubah,$status_ubah){
			$return = array("status" => false, "error_info" => "");
			$cek = $this->m_aksi_modul->cek_ubah_aksi_modul($modul,$nama,$status,$modul_ubah,$nama_ubah,$status_ubah);
			if($cek["status"]){
				$set = array('id_modul'=>$modul_ubah,'nama'=>$nama_ubah,'status'=>$status_ubah);
				$arr_data_lama = $this->m_aksi_modul->data_aksi_modul($modul,$nama);
				$log_data_lama = "";
				
				foreach($arr_data_lama as $key => $value){
					if(strcmp($key,"id")!=0){
						if(!empty($log_data_lama)){
							$log_data_lama .= "<br>";
						}
						$log_data_lama .= "$key = $value";
					}
				}
				$this->m_aksi_modul->ubah($set,$modul,$nama,$status);

				if($this->m_aksi_modul->cek_hasil_aksi_modul($modul_ubah,$nama_ubah,$status_ubah)){
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
					$return["error_info"] = "Perubahan Aksi Modul <b>Gagal</b> Dilakukan.";
				}
			}
			else{
				$return["status"] = $cek["status"];
				$return["error_info"] = $cek["error_info"];	
			}

			return $return;
		}
	}
	
	/* End of file aksi_modul.php */
	/* Location: ./application/controllers/administrator/aksi_modul.php */