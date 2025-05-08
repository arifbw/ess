<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Sto extends CI_Controller {
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
			
			//$this->load->model($this->folder_model."m_master_menu");

			$this->data['success'] = "";
			$this->data['warning'] = "";
			
			$this->data["is_with_sidebar"] = true;
		}
		
		public function index($nama_menu=null){
//            $nama_menu = urldecode($nama_menu);
			$this->data["navigasi_menu"] = menu_helper();

//			$cek = $this->m_master_menu->ambil_id_menu($nama_menu);
//			$url = $this->m_setting->ambil_url_modul("Master Menu");
            
            $this->data["loop_count"] = $this->db->select('level')->where('object_type!=', 'P ')->group_by('level')->get('ess_sto');
			
//			if($cek["hasil"] and isset ($_SERVER["HTTP_REFERER"]) and strcmp(base_url($url),substr($_SERVER["HTTP_REFERER"],0,strlen(base_url($url))))==0){
				$this->data['judul'] = "STO";
				$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
				$this->akses = akses_helper($this->data['id_modul']);
			
				izin($this->akses["akses"]);
				
				$this->data["akses"] = $this->akses;
				//$this->data['judul'] .= " : ".$nama_menu;
				$this->data['id_menu'] = @$cek["id"];
				
				$_SERVER["PHP_SELF"] = substr_replace($_SERVER["PHP_SELF"],"master_menu",strpos($_SERVER["PHP_SELF"],__FUNCTION__));

				$this->data['content'] = $this->folder_view."sto";
				array_push($this->data['js_sources'],"master_data/sto");
				
				$log = array(
						"id_pengguna" => $this->session->userdata("id_pengguna"),
						"id_modul" => $this->data['id_modul'],
						"id_target" => $this->data['id_menu'],
						"deskripsi" => "lihat ".strtolower(preg_replace("/_/"," ",__FUNCTION__))." : ".$nama_menu,
						"alamat_ip" => $this->data["ip_address"],
						"waktu" => date("Y-m-d H:i:s")
					);
				//$this->m_log->tambah($log);
				
				$this->load->view('template',$this->data);
//			}
//			else{
//				redirect(base_url($url));
//			}
			/*$this->data['judul'] = "Master Menu";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."master_menu";
			
			array_push($this->data['js_sources'],"master_data/master_menu");

			if($this->input->post()){
				if(!strcmp($this->input->post("aksi"),"tambah")){
					izin($this->akses["tambah"]);
					
					$this->data['nama'] = $this->input->post("nama");
					if(!strcmp($this->input->post("status"),"aktif")){
						$this->data['status'] = "1";
					}
					else if(!strcmp($this->input->post("status"),"non aktif")){
						$this->data['status'] = "0";
					}

					$tambah = $this->tambah($this->data['nama'],$this->data['status']);
					
					$this->data['panel_tambah'] = "in";

					if($tambah['status']){
						$this->data['success'] = "Master Menu dengan nama <b>".$this->data['nama']."</b> berhasil ditambahkan.";
						$this->data['panel_tambah'] = "";
						$this->data['nama'] = "";
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
					$ubah = $this->ubah($this->data["nama"],$this->data["status"],$this->data["nama_ubah"],$this->data["status_ubah"]);

					if($ubah["status"]){
						$this->data['success'] = "Perubahan Master Menu berhasil dilakukan.";
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
				}
			}
			else{
				$this->data['nama'] = "";
				$this->data['status'] = "";
				$this->data['panel_tambah'] = "";
			}

			if($this->akses["lihat"]){
				$js_header_script = "<script>
										$(document).ready(function() {
											$('#tabel_master_menu').DataTable({
												responsive: true
											});
										});
									</script>";
				array_push($this->data["js_header_script"],$js_header_script);

				$this->data["daftar_master_menu"] = $this->m_master_menu->daftar_master_menu();
				
				$log = array(
						"id_pengguna" => $this->session->userdata("id_pengguna"),
						"id_modul" => $this->data['id_modul'],
						"deskripsi" => "lihat ".strtolower(preg_replace("/_/"," ",__CLASS__)),
						"alamat_ip" => $this->data["ip_address"],
						"waktu" => date("Y-m-d H:i:s")
					);
				$this->m_log->tambah($log);
			}
			
			if($this->akses["isi"]){
				$this->data["url_isi_menu"] = $this->m_setting->ambil_url_modul("Isi Menu");
			}
			$this->load->view('template',$this->data);*/
		}
		
		public function isi_menu($nama_menu){
			$nama_menu = urldecode($nama_menu);
			$this->data["navigasi_menu"] = menu_helper();

			$cek = $this->m_master_menu->ambil_id_menu($nama_menu);
			$url = $this->m_setting->ambil_url_modul("Master Menu");
			
			if($cek["hasil"] and isset ($_SERVER["HTTP_REFERER"]) and strcmp(base_url($url),substr($_SERVER["HTTP_REFERER"],0,strlen(base_url($url))))==0){
				$this->data['judul'] = "Isi Menu";
				$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
				$this->akses = akses_helper($this->data['id_modul']);
			
				izin($this->akses["akses"]);
				
				$this->data["akses"] = $this->akses;
				$this->data['judul'] .= " : ".$nama_menu;
				$this->data['id_menu'] = $cek["id"];
				
				$_SERVER["PHP_SELF"] = substr_replace($_SERVER["PHP_SELF"],"master_menu",strpos($_SERVER["PHP_SELF"],__FUNCTION__));

				$this->data['content'] = $this->folder_view."isi_menu";
				array_push($this->data['js_sources'],"master_data/isi_menu");
				
				$log = array(
						"id_pengguna" => $this->session->userdata("id_pengguna"),
						"id_modul" => $this->data['id_modul'],
						"id_target" => $this->data['id_menu'],
						"deskripsi" => "lihat ".strtolower(preg_replace("/_/"," ",__FUNCTION__))." : ".$nama_menu,
						"alamat_ip" => $this->data["ip_address"],
						"waktu" => date("Y-m-d H:i:s")
					);
				$this->m_log->tambah($log);
				
				$this->load->view('template',$this->data);
			}
			else{
				redirect(base_url($url));
			}
		}
		
		private function tambah($nama,$status){
			$return = array("status" => false, "error_info" => "");
			if($this->m_master_menu->cek_tambah_master_menu($nama)){
				$data = array(
							"nama" => $nama,
							"status" => $status
						);
				$this->m_master_menu->tambah($data);
				
				if($this->m_master_menu->cek_hasil_master_menu($nama,$status)){
					$return["status"] = true;
					
					$arr_data_insert = $this->m_master_menu->data_master_menu($nama);
					
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
					$return["error_info"] = "Penambahan Master Menu <b>Gagal</b> Dilakukan.";
				}
			}
			else{
				$return["status"] = false;
				$return["error_info"] = "Master menu dengan nama <b>$nama</b> sudah ada.";
			}
			return $return;
		}

		public function ubah($nama,$status,$nama_ubah,$status_ubah){
			$return = array("status" => false, "error_info" => "");
			$cek = $this->m_master_menu->cek_ubah_master_menu($nama,$status,$nama_ubah);
			if($cek["status"]){
				$set = array('nama'=>$nama_ubah,'status'=>$status_ubah);
				$arr_data_lama = $this->m_master_menu->data_master_menu($nama);
				$log_data_lama = "";
				
				foreach($arr_data_lama as $key => $value){
					if(strcmp($key,"id")!=0){
						if(!empty($log_data_lama)){
							$log_data_lama .= "<br>";
						}
						$log_data_lama .= "$key = $value";
					}
				}
				
				$this->m_master_menu->ubah($set,$nama,$status);

				if($this->m_master_menu->cek_hasil_master_menu($nama_ubah,$status_ubah)){
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
					$return["error_info"] = "Perubahan Master Menu <b>Gagal</b> Dilakukan.";
				}
			}
			else{
				$return["status"] = $cek["status"];
				$return["error_info"] = $cek["error_info"];	
			}

			return $return;
		}
	}
	
	/* End of file master_menu.php */
	/* Location: ./application/controllers/master_data/master_menu.php */