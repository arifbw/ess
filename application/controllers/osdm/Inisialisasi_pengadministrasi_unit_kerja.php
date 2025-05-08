<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Inisialisasi_pengadministrasi_unit_kerja extends CI_Controller {
		public function __construct(){
			parent::__construct();
			
			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'osdm/';
			$this->folder_model = 'osdm/';
			$this->folder_ajax_view = $this->folder_view.'ajax/';
			$this->akses = array();
			
			$this->load->model($this->folder_model."m_inisialisasi_pengadministrasi_unit_kerja");

			$this->data['success'] = "";
			$this->data['warning'] = "";
			
			$this->data["is_with_sidebar"] = true;
		}
		
		public function index(){
			$this->data['judul'] = "Inisialisasi Pengadministrasi Unit Kerja";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."inisialisasi_pengadministrasi_unit_kerja";
			
			array_push($this->data['js_sources'],"osdm/pengadministrasi_unit_kerja");
			array_push($this->data['js_sources'],"administrator/pengguna");

			if($this->input->post()){
				izin($this->akses["ubah"]);
				
				//var_dump($this->input->post()); exit();
				
				$username = $this->input->post("username");
				
				$this->load->model("administrator/m_pengguna");
				$data_pengguna = $this->m_pengguna->data_pengguna($username);
				
				$is_pilih_unit_kerja_awal = $this->input->post("is_pilih_unit_kerja_awal");
				$is_pilih_unit_kerja = $this->input->post("is_pilih_unit_kerja");
				
				$admin_unit_kerja = $this->input->post("admin_unit_kerja");
				$admin_unit_kerja_ubah = $this->input->post("admin_unit_kerja_ubah");
				
				if(strcmp($is_pilih_unit_kerja_awal,$is_pilih_unit_kerja)!=0){
					$this->load->model("administrator/m_grup_pengguna");
					
					$data = array(
						"id_pengguna" => $data_pengguna["id"],
						"id_grup_pengguna" => $this->m_grup_pengguna->ambil_id_grup_pengguna("Pengadministrasi Unit Kerja")
					);
					
					$this->load->model("administrator/m_pengguna_grup_pengguna");
					
					if(strcmp($is_pilih_unit_kerja,"ya")==0){
						$this->m_pengguna_grup_pengguna->tambah($data);
					}
					else if(strcmp($is_pilih_unit_kerja,"tidak")==0){
						$this->m_pengguna_grup_pengguna->hapus($data);
					}
					
					$log_data_lama = "Pengadministrasi Unit Kerja = ".$is_pilih_unit_kerja_awal;
					$log_data_baru = "Pengadministrasi Unit Kerja = ".$is_pilih_unit_kerja;
					
					$log = array(
							"id_pengguna" => $this->session->userdata("id_pengguna"),
							"id_modul" => $this->data['id_modul'],
							"id_target" => $data_pengguna["id"],
							"deskripsi" => "ubah grup pengguna",
							"kondisi_lama" => $log_data_lama,
							"kondisi_baru" => $log_data_baru,
							"alamat_ip" => $this->data["ip_address"],
							"waktu" => date("Y-m-d H:i:s")
						);
						$this->m_log->tambah($log);
				}
				
				$this->ubah_pengadministrasi($username,$is_pilih_unit_kerja,$admin_unit_kerja,$admin_unit_kerja_ubah);
				
			}
			else{
				$this->data['nama'] = "";
				$this->data['status'] = "";
				$this->data['panel_tambah'] = "";
			}
			
			if($this->akses["lihat"]){
				$js_header_script = "<script>
								$(document).ready(function() {
									$('#tabel_pengadministrasi_unit_kerja').DataTable({
										responsive: true
									});
								});
							</script>";
				
				array_push($this->data["js_header_script"],$js_header_script);
			
				$arr_id_grup_pengguna = array();
				$this->load->model("administrator/m_grup_pengguna");
				array_push($arr_id_grup_pengguna,$this->m_grup_pengguna->ambil_id_grup_pengguna("Pengguna"));
				array_push($arr_id_grup_pengguna,$this->m_grup_pengguna->ambil_id_grup_pengguna("Pengadministrasi Unit Kerja"));
				
				$this->data["daftar_pengguna_pengadministrasi"] = $this->m_inisialisasi_pengadministrasi_unit_kerja->daftar_pengguna_pengadministrasi($arr_id_grup_pengguna);
				
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
		
		private function ubah_pengadministrasi($username,$is_pilih_unit_kerja,$admin_unit_kerja,$admin_unit_kerja_ubah){
			if(strcmp($admin_unit_kerja,$admin_unit_kerja_ubah)!=0){
				$data_pengguna = $this->m_pengguna->data_pengguna($username);
				$id_pengguna = $data_pengguna["id"];
				
				$this->load->model("administrator/m_pengadministrasi");
				
				$data = array(
						"id_pengguna" => $id_pengguna
					);
					
				$arr_pengadminstrasi_lama = $this->m_pengadministrasi->data_pengadministrasi($username);
				$log_data_lama = "";
				foreach($arr_pengadminstrasi_lama as $pengadministrasi_lama){
					if(!empty($log_data_lama)){
						$log_data_lama .= "<br>";
					}
					$log_data_lama .= $pengadministrasi_lama["kode_unit"]." - ".$pengadministrasi_lama["nama_unit"];
					
				}
				
				$this->m_pengadministrasi->hapus($data);

				if(strcmp($is_pilih_unit_kerja,"ya")==0){
					$arr_data_baru = explode(",",$admin_unit_kerja_ubah);
					$arr_tambah = array();
					foreach($arr_data_baru as $data_baru){
						array_push($arr_tambah,array("id_pengguna" => $id_pengguna,"kode_unit"=>$data_baru));
					}
					
					$this->m_pengadministrasi->tambah($arr_tambah);
					
					if($this->m_pengadministrasi->cek_pengadministrasi($id_pengguna,$arr_data_baru)){
						$log_data_baru = "";
						
						$arr_pengadminstrasi_baru = $this->m_pengadministrasi->data_pengadministrasi($username);
						foreach($arr_pengadminstrasi_baru as $pengadministrasi_baru){
							if(!empty($log_data_baru)){
								$log_data_baru .= "<br>";
							}
							$log_data_baru .= $pengadministrasi_baru["kode_unit"]." - ".$pengadministrasi_baru["nama_unit"];
						}
						
						$log = array(
							"id_pengguna" => $this->session->userdata("id_pengguna"),
							"id_modul" => $this->data['id_modul'],
							"id_target" => $id_pengguna,
							"deskripsi" => "ubah pengadministrasi unit kerja",
							"kondisi_lama" => $log_data_lama,
							"kondisi_baru" => $log_data_baru,
							"alamat_ip" => $this->data["ip_address"],
							"waktu" => date("Y-m-d H:i:s")
						);
						$this->m_log->tambah($log);
					}
				}
			}
		}
	}
	
	
	/* End of file pengadministrasi_unit_kerja.php */
	/* Location: ./application/controllers/osdm/pengadministrasi_unit_kerja.php */