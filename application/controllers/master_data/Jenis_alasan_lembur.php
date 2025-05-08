<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Jenis_alasan_lembur extends CI_Controller {
		public function __construct(){
			parent::__construct();
			
			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'master_data/';
			$this->folder_model = 'master_data/';
			$this->folder_ajax_view = $this->folder_view.'ajax/';
			$this->akses = array();
			
			$this->load->model($this->folder_model."/m_jenis_alasan_lembur");

			$this->data['success'] = "";
			$this->data['warning'] = "";
			
			$this->data["is_with_sidebar"] = true;
		}

        public function index(){
			$this->data['judul'] = "Jenis Alasan Lembur";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."jenis_alasan_lembur";
			
			array_push($this->data['js_sources'],"master_data/jenis_alasan_lembur");

			if($this->input->post()){
				if(!strcmp($this->input->post("aksi"),"tambah")){
					izin($this->akses["tambah"]);
				
					$this->data['kategori_lembur'] = $this->input->post("kategori_lembur");
					if(!strcmp($this->input->post("status"),"aktif")){
						$this->data['status'] = true;
					}
					else if(!strcmp($this->input->post("status"),"non aktif")){
						$this->data['status'] = false;
					}
					
					$tambah = $this->tambah($this->data['kategori_lembur'],$this->data['status']);
					
					$this->data['panel_tambah'] = "in";

					if($tambah['status']){
						$this->data['success'] = "Jenis lembur dengan kategori_lembur <b>".$this->data['kategori_lembur']."</b> berhasil ditambahkan.";
						$this->data['panel_tambah'] = "";
						$this->data['kategori_lembur'] = "";
						$this->data['id'] = "";
						$this->data['status'] = "";
					}
					else{
						$this->data['warning'] = $tambah['error_info'];
					}
				}
				else if(!strcmp($this->input->post("aksi"),"ubah")){
					izin($this->akses["ubah"]);
					
					$this->data['kategori_lembur'] = $this->input->post("kategori_lembur");
					$this->data['kategori_lembur_ubah'] = $this->input->post("kategori_lembur_ubah");
					$this->data['status'] = (bool)$this->input->post("status");
					if(!strcmp($this->input->post("status_ubah"),"aktif")){
						$this->data['status_ubah'] = true;
					}
					else if(!strcmp($this->input->post("status_ubah"),"non aktif")){
						$this->data['status_ubah'] = false;
					}

					// var_dump($this->data);
					// die;
					$ubah = $this->ubah($this->data['kategori_lembur'],$this->data['kategori_lembur_ubah'],$this->data['status_ubah']);
					
					if($ubah["status"]){
						$this->data['success'] = "Perubahan jenis lembur berhasil dilakukan.";
					}
					else{
						$this->data['warning'] = $ubah['error_info'];
					}

					$this->data['panel_tambah'] = "";
					$this->data['kategori_lembur'] = "";
					$this->data['id'] = "";
					$this->data['status'] = "";
				}
				else{
					$this->data['panel_tambah'] = "";
					$this->data['kategori_lembur'] = "";
					$this->data['id'] = "";
					$this->data['status'] = "";
				}
			}
			else{
				$this->data['panel_tambah'] = "";
				$this->data['kategori_lembur'] = "";
				$this->data['id'] = "";
				$this->data['status'] = "";
			}
			
			if($this->akses["lihat"]){
				$js_header_script = "<script>
								$(document).ready(function() {
									$('#tabel_jenis_alasan_lembur').DataTable({
										responsive: true
									});
								});
							</script>";
				
				array_push($this->data["js_header_script"],$js_header_script);
				
				$this->data["daftar_jenis_alasan_lembur"] = $this->m_jenis_alasan_lembur->daftar_jenis_alasan_lembur();
				
				$log = array(
						"id_pengguna" => $this->session->userdata("id_pengguna"),
						"id_modul" => $this->data['id_modul'],
						// "deskripsi" => "lihat ".strtolower(preg_replace("//"," ",CLASS_)),
						"alamat_ip" => $this->data["ip_address"],
						"waktu" => date("Y-m-d H:i:s")
					);
				$this->m_log->tambah($log);
			}

			$this->load->view('template',$this->data);
		}

		private function tambah($kategori_lembur,$status){
			$return = array("status" => false, "error_info" => "");
			if($this->m_jenis_alasan_lembur->cek_tambah_jenis_alasan_lembur($kategori_lembur)){
				$data = array(
					"kategori_lembur" => $kategori_lembur,
					"status" => $status
				);
				
				$this->m_jenis_alasan_lembur->tambah($data);

				if($this->m_jenis_alasan_lembur->cek_hasil_jenis_alasan_lembur($kategori_lembur,$status)){
					$return["status"] = true;
					
					$arr_data_insert = $this->m_jenis_alasan_lembur->data_jenis_alasan_lembur($kategori_lembur);
					
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
						"deskripsi" => "tambah ".strtolower(preg_replace("//"," ",CLASS_)),
						"kondisi_baru" => $log_data_baru,
						"alamat_ip" => $this->data["ip_address"],
						"waktu" => date("Y-m-d H:i:s")
					);
					$this->m_log->tambah($log);
				}
				else{
					$return["status"] = false;
					$return["error_info"] = "Penambahan Jenis Alasan Lembur <b>Gagal</b> Dilakukan.";
				}
			}
			else{
				$return["status"] = false;
				$return["error_info"] = "Jenis Alasan Lembur dengan uraian <b>$kategori_lembur</b> sudah ada.";
			}
			
			return $return;
		}

		private function ubah($kategori_lembur,$kategori_lembur_ubah,$status_ubah){
			$return = array("status" => false, "error_info" => "");
			$cek = $this->m_jenis_alasan_lembur->cek_ubah_jenis_alasan_lembur($kategori_lembur,$kategori_lembur_ubah);
			if($cek["status"]){
				$set = array("kategori_lembur" => $kategori_lembur_ubah, "status"=>$status_ubah);
				
				$arr_data_lama = $this->m_jenis_alasan_lembur->data_jenis_alasan_lembur($kategori_lembur);
				$log_data_lama = "";
				
				foreach($arr_data_lama as $key => $value){
					if(!empty($log_data_lama)){
						$log_data_lama .= "<br>";
					}
					$log_data_lama .= "$key = $value";
				}
				
				$this->m_jenis_alasan_lembur->ubah($set,$kategori_lembur);

				if($this->m_jenis_alasan_lembur->cek_hasil_jenis_alasan_lembur($kategori_lembur_ubah,$status_ubah)){
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
						// "deskripsi" => "ubah ".strtolower(preg_replace("//"," ",CLASS_)),
						"kondisi_lama" => $log_data_lama,
						"kondisi_baru" => $log_data_baru,
						"alamat_ip" => $this->data["ip_address"],
						"waktu" => date("Y-m-d H:i:s")
					);
					$this->m_log->tambah($log);
				}
				else{
					$return["status"] = false;
					$return["error_info"] = "Perubahan Jenis Alasan Lembur <b>Gagal</b> Dilakukan.";
				}
			}
			else{
				$return["status"] = $cek["status"];
				$return["error_info"] = $cek["error_info"];	
			}

			return $return;
		}
	}
	
	/* End of file jenis_alasan_lembur.php */
	/* Location: ./application/controllers/master_data/jenis_alasan_lembur.php */