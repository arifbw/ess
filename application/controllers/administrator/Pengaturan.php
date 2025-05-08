<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Pengaturan extends CI_Controller {
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
			
			$this->load->model($this->folder_model."m_pengaturan");

			$this->data['success'] = "";
			$this->data['warning'] = "";
			
			$this->data["is_with_sidebar"] = true;
		}
		
		public function index(){
			$this->data['judul'] = "Pengaturan";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			//var_dump($this->akses);
			izin($this->akses["akses"]);
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."pengaturan";
			
			array_push($this->data['js_sources'],"administrator/pengaturan");

			if($this->input->post()){
				if(!strcmp($this->input->post("aksi"),"tambah")){
					izin($this->akses["tambah"]);
				
					$this->data['nama'] = $this->input->post("nama");
					$this->data['isi'] = $this->input->post("isi");
					
					$tambah = $this->tambah($this->data['nama'],$this->data['isi']);
					
					$this->data['panel_tambah'] = "in";

					if($tambah['status']){
						$this->data['success'] = "Pengaturan dengan nama <b>".$this->data['nama']."</b> berhasil ditambahkan.";
						$this->data['panel_tambah'] = "";
						$this->data['nama'] = "";
						$this->data['isi'] = "";
					}
					else{
						$this->data['warning'] = $tambah['error_info'];
					}
				}
				else if(!strcmp($this->input->post("aksi"),"ubah")){
					izin($this->akses["ubah"]);
					
					$this->data['nama'] = $this->input->post("nama");
					$this->data['nama_ubah'] = $this->input->post("nama_ubah");
					$this->data['isi'] = $this->input->post("isi");
					$this->data['isi_ubah'] = $this->input->post("isi_ubah");

					$ubah = $this->ubah($this->data["nama"],$this->data["isi"],$this->data["nama_ubah"],$this->data["isi_ubah"]);

					if($ubah["status"]){
						$this->data['success'] = "Perubahan pengaturan berhasil dilakukan.";
					}
					else{
						$this->data['warning'] = $ubah['error_info'];
					}

					$this->data['panel_tambah'] = "";
					$this->data["nama"] = "";
					$this->data['isi'] = "";
				}
				else{
					$this->data['panel_tambah'] = "";
					$this->data['nama'] = "";
					$this->data['isi'] = "";
				}
			}
			else{
				$this->data['nama'] = "";
				$this->data['isi'] = "";
				$this->data['panel_tambah'] = "";
			}
			
			if($this->akses["lihat"]){
				$js_header_script = "<script>
								$(document).ready(function() {
									$('#tabel_pengaturan').DataTable({
										responsive: true
									});
								});
							</script>";
				
				array_push($this->data["js_header_script"],$js_header_script);
				
				$this->data["daftar_pengaturan"] = $this->m_pengaturan->daftar_pengaturan();
				
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
		
		private function tambah($nama,$isi){
			$return = array("status" => false, "error_info" => "");
			if($this->m_pengaturan->cek_tambah_pengaturan($nama)){
				$data = array(
							"nama" => $nama,
							"isi" => $isi
						);
				$this->m_pengaturan->tambah($data);
				
				if($this->m_pengaturan->cek_hasil_pengaturan($nama,$isi)){
					$return["status"] = true;
					
					$arr_data_insert = $this->m_pengaturan->data_pengaturan($nama);
					
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
					$return["error_info"] = "Penambahan Pengaturan <b>Gagal</b> Dilakukan.";
				}
			}
			else{
				$return["status"] = false;
				$return["error_info"] = "Pengaturan dengan nama <b>$nama</b> sudah ada.";
			}
			return $return;
		}
	
		private function ubah($nama,$isi,$nama_ubah,$isi_ubah){
			$return = array("status" => false, "error_info" => "");
			$cek = $this->m_pengaturan->cek_ubah_pengaturan($nama,$nama_ubah);
			if($cek["status"]){
				$set = array('nama'=>$nama_ubah,'isi'=>$isi_ubah);
				$arr_data_lama = $this->m_pengaturan->data_pengaturan($nama);
				$log_data_lama = "";
				
				foreach($arr_data_lama as $key => $value){
					if(strcmp($key,"id")!=0){
						if(!empty($log_data_lama)){
							$log_data_lama .= "<br>";
						}
						$log_data_lama .= "$key = $value";
					}
				}
				
				$this->m_pengaturan->ubah($set,$nama,$isi);

				if($this->m_pengaturan->cek_hasil_pengaturan($nama_ubah,$isi_ubah)){
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
					$return["error_info"] = "Perubahan Pengaturan <b>Gagal</b> Dilakukan.";
				}
			}
			else{
				$return["status"] = $cek["status"];
				$return["error_info"] = $cek["error_info"];	
			}

			return $return;
		}
	}
	
	/* End of file pengaturan.php */
	/* Location: ./application/controllers/administrator/pengaturan.php */