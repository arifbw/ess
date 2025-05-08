<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Kelompok_modul extends CI_Controller {
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
			
			$this->load->model($this->folder_model."m_kelompok_modul");

			$this->data['success'] = "";
			$this->data['warning'] = "";
			
			$this->data["is_with_sidebar"] = true;
		}
		
		public function index(){
			$this->data['judul'] = "Kelompok Modul";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."kelompok_modul";
			
			array_push($this->data['js_sources'],"administrator/kelompok_modul");

			if($this->input->post()){
				if(!strcmp($this->input->post("aksi"),"tambah")){
					izin($this->akses["tambah"]);
					
					$this->data['nama'] = $this->input->post("nama");
					$this->data['keterangan'] = $this->input->post("keterangan");
					if(!strcmp($this->input->post("status"),"aktif")){
						$this->data['status'] = "1";
					}
					else if(!strcmp($this->input->post("status"),"non aktif")){
						$this->data['status'] = "0";
					}
					
					$tambah = $this->tambah($this->data['nama'],$this->data['keterangan'],$this->data['status']);
					
					$this->data['panel_tambah'] = "in";

					if($tambah['status']){
						$this->data['success'] = "Kelompok Modul dengan nama <b>".$this->data['nama']."</b> berhasil ditambahkan.";
						$this->data['panel_tambah'] = "";
						$this->data['nama'] = "";
						$this->data['keterangan'] = "";
						$this->data['status'] = "";
					}
					else{
						$this->data['warning'] = $tambah['error_info'];
					}
				}
				else if(!strcmp($this->input->post("aksi"),"ubah")){
					izin($this->akses["ubah"]);
					
					$this->data['nama'] = $this->input->post("nama");
					$this->data['nama_ubah'] = $this->input->post("nama_ubah");
					$this->data['keterangan'] = $this->input->post("keterangan");
					$this->data['keterangan_ubah'] = $this->input->post("keterangan_ubah");
					
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

					$ubah = $this->ubah($this->data["nama"],$this->data["keterangan"],$this->data["status"],$this->data["nama_ubah"],$this->data["keterangan_ubah"],$this->data["status_ubah"]);

					if($ubah["status"]){
						$this->data['success'] = "Perubahan kelompok modul berhasil dilakukan.";
					}
					else{
						$this->data['warning'] = $ubah['error_info'];
					}

					$this->data["nama"] = "";
					$this->data["keterangan"] = "";
					$this->data["status"] = "";
					$this->data["panel_tambah"] = "";
				}
				else{
					$this->data["nama"] = "";
					$this->data["keterangan"] = "";
					$this->data["status"] = "";
					$this->data["panel_tambah"] = "";
				}
			}
			else{
				$this->data["nama"] = "";
				$this->data["keterangan"] = "";
				$this->data["status"] = "";
				$this->data["panel_tambah"] = "";
			}
			
			if($this->akses["lihat"]){
				$js_header_script = "<script>
								$(document).ready(function() {
									$('#tabel_kelompok_modul').DataTable({
										responsive: true
									});
								});
							</script>";
				
				array_push($this->data["js_header_script"],$js_header_script);
				
				$this->data["daftar_kelompok_modul"] = $this->m_kelompok_modul->daftar_kelompok_modul();
				
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
		
		private function tambah($nama,$keterangan,$status){
			$return = array("status" => false, "error_info" => "");
			if($this->m_kelompok_modul->cek_tambah_kelompok_modul($nama)){
				$data = array(
						"nama" => $nama,
						"keterangan" => $keterangan,
						"status" => $status
					);
				$this->m_kelompok_modul->tambah($data);
				
				if($this->m_kelompok_modul->cek_hasil_kelompok_modul($nama,$keterangan,$status)){
					$return["status"] = true;
					
					$arr_data_insert = $this->m_kelompok_modul->data_kelompok_modul($nama);
					
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
					$return["error_info"] = "Penambahan Kelompok Modul <b>Gagal</b> Dilakukan.";
				}
			}
			else{
				$return["status"] = false;
				$return["error_info"] = "Kelompok Modul dengan nama <b>$nama</b> sudah ada.";
			}
			return $return;
		}
	
		private function ubah($nama,$keterangan,$status,$nama_ubah,$keterangan_ubah,$status_ubah){
			$return = array("status" => false, "error_info" => "");
			$cek = $this->m_kelompok_modul->cek_ubah_kelompok_modul($nama,$nama_ubah);
			if($cek["status"]){
				$set = array('nama'=>$nama_ubah,'keterangan'=>$keterangan_ubah,'status'=>$status_ubah);
				$arr_data_lama = $this->m_kelompok_modul->data_kelompok_modul($nama);
				$log_data_lama = "";
				
				foreach($arr_data_lama as $key => $value){
					if(strcmp($key,"id")!=0){
						if(!empty($log_data_lama)){
							$log_data_lama .= "<br>";
						}
						$log_data_lama .= "$key = $value";
					}
				}
				
				$this->m_kelompok_modul->ubah($set,$nama,$keterangan,$status);

				if($this->m_kelompok_modul->cek_hasil_kelompok_modul($nama_ubah,$keterangan_ubah,$status_ubah)){
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
					$return["error_info"] = "Perubahan Kelompok Modul <b>Gagal</b> Dilakukan.";
				}
			}
			else{
				$return["status"] = $cek["status"];
				$return["error_info"] = $cek["error_info"];	
			}

			return $return;
		}
	}
	
	/* End of file kelompok_modul.php */
	/* Location: ./application/controllers/administrator/kelompok_modul.php */