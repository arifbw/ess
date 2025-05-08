<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Jadwal_kerja extends CI_Controller {
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
			
			$this->load->model($this->folder_model."/m_jadwal_kerja");

			$this->data['success'] = "";
			$this->data['warning'] = "";
			
			$this->data["is_with_sidebar"] = true;
		}
		
		public function index(){
			$this->data['judul'] = "Jadwal Kerja";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."jadwal_kerja";
			
			array_push($this->data['js_sources'],"master_data/jadwal_kerja");

			if($this->input->post()){
				if(!strcmp($this->input->post("aksi"),"tambah")){
					izin($this->akses["tambah"]);
				
					$this->data['nama'] = $this->input->post("nama");
					$this->data['hari'] = (bool)$this->input->post("hari");
					$this->data['formasi_gilir'] = (bool)$this->input->post("formasi_gilir");
					$this->data['lintas_hari_masuk'] = (bool)$this->input->post("lintas_hari_masuk");
					$this->data['jam_masuk'] = $this->input->post("jam_masuk");
					if(!empty($this->input->post("istirahat"))){
						$this->data['istirahat'] = $this->input->post("istirahat");						
					}
					else{
						$this->data['istirahat'] = "";
					}
					$this->data['lintas_hari_mulai_istirahat'] = (bool)$this->input->post("lintas_hari_mulai_istirahat");
					$this->data['jam_mulai_istirahat'] = $this->input->post("jam_mulai_istirahat");
					$this->data['lintas_hari_akhir_istirahat'] = (bool)$this->input->post("lintas_hari_akhir_istirahat");
					$this->data['jam_akhir_istirahat'] = $this->input->post("jam_akhir_istirahat");
					$this->data['lintas_hari_pulang'] = (bool)$this->input->post("lintas_hari_pulang");
					$this->data['jam_pulang'] = $this->input->post("jam_pulang");
					if(!strcmp($this->input->post("status"),"aktif")){
						$this->data['status'] = true;
					}
					else if(!strcmp($this->input->post("status"),"non aktif")){
						$this->data['status'] = false;
					}
					$this->data['kode_erp'] = $this->input->post("kode_erp");
					if(empty($this->input->post("varian"))){
						$this->data['varian'] = "";
					}
					else{
						$this->data['varian'] = "A";
					}
					
					$tambah = $this->tambah($this->data['nama'],$this->data['hari'],$this->data['lintas_hari_masuk'],$this->data['jam_masuk'],$this->data['istirahat'],$this->data['lintas_hari_mulai_istirahat'],$this->data['jam_mulai_istirahat'],$this->data['lintas_hari_akhir_istirahat'],$this->data['jam_akhir_istirahat'],$this->data['lintas_hari_pulang'],$this->data['jam_pulang'],$this->data['kode_erp'],$this->data['varian'],$this->data['status']);
					
					$this->data['panel_tambah'] = "in";

					if($tambah['status']){
						$this->data['success'] = "Jadwal Kerja dengan nama <b>".$this->data['nama']."</b> berhasil ditambahkan.";
						$this->data['panel_tambah'] = "";
						$this->data['nama'] = "";
						$this->data['hari'] = "";
						$this->data['formasi_gilir'] = "";
						$this->data['lintas_hari_masuk'] = "";
						$this->data['jam_masuk'] = "";
						$this->data['istirahat'] = "";
						$this->data['lintas_hari_mulai_istirahat'] = "";
						$this->data['jam_mulai_istirahat'] = "";
						$this->data['lintas_hari_akhir_istirahat'] = "";
						$this->data['jam_akhir_istirahat'] = "";
						$this->data['lintas_hari_pulang'] = "";
						$this->data['jam_pulang'] = "";
						$this->data['kode_erp'] = "";
						$this->data['varian'] = "";
						$this->data['status'] = "";
					}
					else{
						$this->data['warning'] = $tambah['error_info'];
					}
				}
				else if(!strcmp($this->input->post("aksi"),"ubah")){
					izin($this->akses["ubah"]);
					
					$this->data['id_jadwal_kerja'] = $this->input->post("id_jadwal_kerja");
					$this->data['nama'] = $this->input->post("nama");
					$this->data['nama_ubah'] = $this->input->post("nama_ubah");
					$this->data['hari'] = $this->input->post("hari");
					$this->data['hari_ubah'] = $this->input->post("hari_ubah");
					$this->data['formasi_gilir'] = $this->input->post("formasi_gilir");
					$this->data['formasi_gilir_ubah'] = $this->input->post("formasi_gilir_ubah");
					$this->data['lintas_hari_masuk'] = (bool)$this->input->post("lintas_hari_masuk");
					$this->data['lintas_hari_masuk_ubah'] = (bool)$this->input->post("lintas_hari_masuk_ubah");
					$this->data['jam_masuk'] = $this->input->post("jam_masuk");
					$this->data['jam_masuk_ubah'] = $this->input->post("jam_masuk_ubah");
					$this->data['istirahat'] = $this->input->post("istirahat");
					$this->data['istirahat_ubah'] = $this->input->post("istirahat_ubah");
					$this->data['lintas_hari_mulai_istirahat'] = (bool)$this->input->post("lintas_hari_mulai_istirahat");
					$this->data['lintas_hari_mulai_istirahat_ubah'] = (bool)$this->input->post("lintas_hari_mulai_istirahat_ubah");
					$this->data['jam_mulai_istirahat'] = $this->input->post("jam_mulai_istirahat");
					$this->data['jam_mulai_istirahat_ubah'] = $this->input->post("jam_mulai_istirahat_ubah");
					$this->data['lintas_hari_akhir_istirahat'] = (bool)$this->input->post("lintas_hari_akhir_istirahat");
					$this->data['lintas_hari_akhir_istirahat_ubah'] = (bool)$this->input->post("lintas_hari_akhir_istirahat_ubah");
					$this->data['jam_akhir_istirahat'] = $this->input->post("jam_akhir_istirahat");
					$this->data['jam_akhir_istirahat_ubah'] = $this->input->post("jam_akhir_istirahat_ubah");
					$this->data['lintas_hari_pulang'] = (bool)$this->input->post("lintas_hari_pulang");
					$this->data['lintas_hari_pulang_ubah'] = (bool)$this->input->post("lintas_hari_pulang_ubah");
					$this->data['jam_pulang'] = $this->input->post("jam_pulang");
					$this->data['jam_pulang_ubah'] = $this->input->post("jam_pulang_ubah");
					$this->data['kode_erp'] = $this->input->post("kode_erp");
					$this->data['kode_erp_ubah'] = $this->input->post("kode_erp_ubah");
					$this->data['varian'] = $this->input->post("varian");
					if(empty($this->input->post("varian_ubah"))){
						$this->data['varian_ubah'] = "";
					}
					else{
						$this->data['varian_ubah'] = "A";
					}
					$this->data['status'] = (bool)$this->input->post("status");
					if(!strcmp($this->input->post("status_ubah"),"aktif")){
						$this->data['status_ubah'] = true;
					}
					else if(!strcmp($this->input->post("status_ubah"),"non aktif")){
						$this->data['status_ubah'] = false;
					}

					$ubah = $this->ubah($this->data['id_jadwal_kerja'], $this->data['nama'], $this->data['kode_erp'], $this->data['varian'], $this->data['nama_ubah'], $this->data['hari_ubah'], $this->data['formasi_gilir_ubah'], $this->data['lintas_hari_masuk_ubah'], $this->data['jam_masuk_ubah'], $this->data['istirahat_ubah'],$this->data['lintas_hari_mulai_istirahat_ubah'],$this->data['jam_mulai_istirahat_ubah'],$this->data['lintas_hari_akhir_istirahat_ubah'],$this->data['jam_akhir_istirahat_ubah'],$this->data['lintas_hari_pulang_ubah'],$this->data['jam_pulang_ubah'],$this->data['kode_erp_ubah'],$this->data['varian_ubah'],$this->data['status_ubah']);
					
					if($ubah["status"]){
						$this->data['success'] = "Perubahan jadwal kerja berhasil dilakukan.";
					}
					else{
						$this->data['warning'] = $ubah['error_info'];
					}

					$this->data['panel_tambah'] = "";
					$this->data['nama'] = "";
					$this->data['hari'] = "";
					$this->data['formasi_gilir'] = "";
					$this->data['lintas_hari_masuk'] = "";
					$this->data['jam_masuk'] = "";
					$this->data['istirahat'] = "";
					$this->data['lintas_hari_mulai_istirahat'] = "";
					$this->data['jam_mulai_istirahat'] = "";
					$this->data['lintas_hari_akhir_istirahat'] = "";
					$this->data['jam_akhir_istirahat'] = "";
					$this->data['lintas_hari_pulang'] = "";
					$this->data['jam_pulang'] = "";
					$this->data['kode_erp'] = "";
					$this->data['status'] = "";
				}
				else{
					$this->data['panel_tambah'] = "";
					$this->data['nama'] = "";
					$this->data['hari'] = "";
					$this->data['formasi_gilir'] = "";
					$this->data['lintas_hari_masuk'] = "";
					$this->data['jam_masuk'] = "";
					$this->data['istirahat'] = "";
					$this->data['lintas_hari_mulai_istirahat'] = "";
					$this->data['jam_mulai_istirahat'] = "";
					$this->data['lintas_hari_akhir_istirahat'] = "";
					$this->data['jam_akhir_istirahat'] = "";
					$this->data['lintas_hari_pulang'] = "";
					$this->data['jam_pulang'] = "";
					$this->data['kode_erp'] = "";
					$this->data['status'] = "";
				}
			}
			else{
				$this->data['panel_tambah'] = "";
				$this->data['nama'] = "";
				$this->data['hari'] = "";
				$this->data['formasi_gilir'] = "";
				$this->data['lintas_hari_masuk'] = "";
				$this->data['jam_masuk'] = "";
				$this->data['istirahat'] = "";
				$this->data['lintas_hari_mulai_istirahat'] = "";
				$this->data['jam_mulai_istirahat'] = "";
				$this->data['lintas_hari_akhir_istirahat'] = "";
				$this->data['jam_akhir_istirahat'] = "";
				$this->data['lintas_hari_pulang'] = "";
				$this->data['jam_pulang'] = "";
				$this->data['kode_erp'] = "";
				$this->data['status'] = "";
			}
			
			if($this->akses["lihat"]){
				$js_header_script = "<script>
								$(document).ready(function() {
									$('#tabel_jadwal_kerja').DataTable({
										responsive: true
									});
								});
							</script>";
				
				array_push($this->data["js_header_script"],$js_header_script);
				
				$this->data["daftar_jadwal_kerja"] = $this->m_jadwal_kerja->daftar_jadwal_kerja();
				
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
		
		private function tambah($nama,$hari,$lintas_hari_masuk,$jam_masuk,$istirahat,$lintas_hari_mulai_istirahat,$jam_mulai_istirahat,$lintas_hari_akhir_istirahat,$jam_akhir_istirahat,$lintas_hari_pulang,$jam_pulang,$kode_erp,$varian,$status){
			$return = array("status" => false, "error_info" => "");
			$cek = $this->m_jadwal_kerja->cek_tambah_jadwal_kerja($kode_erp,$varian,$nama);
			if($cek["status"]){
				$data = array(
							"description" => $nama,
							"libur" => $hari,
							"lintas_hari_masuk" => $lintas_hari_masuk,
							"dws_start_time" => $jam_masuk,
							"istirahat" => $istirahat,
							"lintas_hari_mulai_istirahat" => $lintas_hari_mulai_istirahat,
							"dws_break_start_time" => $jam_mulai_istirahat,
							"lintas_hari_akhir_istirahat" => $lintas_hari_akhir_istirahat,
							"dws_break_end_time" => $jam_akhir_istirahat,
							"lintas_hari_pulang" => $lintas_hari_pulang,
							"dws_end_time" => $jam_pulang,
							"dws" => $kode_erp,
							"dws_variant" => $varian,
							"status" => $status
						);
				$this->m_jadwal_kerja->tambah($data);
				
				if($this->m_jadwal_kerja->cek_hasil_jadwal_kerja($nama,$hari,$lintas_hari_masuk,$jam_masuk,$istirahat,$lintas_hari_mulai_istirahat,$jam_mulai_istirahat,$lintas_hari_akhir_istirahat,$jam_akhir_istirahat,$lintas_hari_pulang,$jam_pulang,$kode_erp,$varian,$status)){
					$return["status"] = true;
					
					$arr_data_insert = $this->m_jadwal_kerja->data_jadwal_kerja($nama);
					
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
					$return["error_info"] = "Penambahan Jadwal Kerja <b>Gagal</b> Dilakukan.";
				}
			}
			else{
				$return["status"] = $cek["status"];
				$return["error_info"] = $cek["error_info"];	
			}
			return $return;
		}
	
		private function ubah($id_jadwal_kerja, $nama, $kode_erp, $varian, $nama_ubah, $hari_ubah, $formasi_gilir_ubah, $lintas_hari_masuk_ubah, $jam_masuk_ubah, $istirahat_ubah, $lintas_hari_mulai_istirahat_ubah, $jam_mulai_istirahat_ubah, $lintas_hari_akhir_istirahat_ubah, $jam_akhir_istirahat_ubah, $lintas_hari_pulang_ubah, $jam_pulang_ubah, $kode_erp_ubah, $varian_ubah, $status_ubah){
			$return = array("status" => false, "error_info" => "");
			$cek = $this->m_jadwal_kerja->cek_ubah_jadwal_kerja($nama,$nama_ubah,$kode_erp,$kode_erp_ubah,$varian,$varian_ubah);
			if($cek["status"]){
				$set = array("description" => $nama_ubah, "libur" => $hari_ubah, "gilir" => $formasi_gilir_ubah, "lintas_hari_masuk" => $lintas_hari_masuk_ubah, "dws_start_time" => $jam_masuk_ubah, "istirahat" => $istirahat_ubah, "lintas_hari_mulai_istirahat" => $lintas_hari_mulai_istirahat_ubah, "dws_break_start_time" => $jam_mulai_istirahat_ubah, "lintas_hari_akhir_istirahat" => $lintas_hari_akhir_istirahat_ubah, "dws_break_end_time" => $jam_akhir_istirahat_ubah, "lintas_hari_pulang" => $lintas_hari_pulang_ubah, "dws_end_time" => $jam_pulang_ubah, "dws"=>$kode_erp_ubah, "dws_variant" => $varian_ubah, "status"=>$status_ubah);
				
				$arr_data_lama = $this->m_jadwal_kerja->data_jadwal_kerja($kode_erp,$varian);
				$log_data_lama = "";
				
				foreach($arr_data_lama as $key => $value){
					if(strcmp($key,"id")!=0){
						if(!empty($log_data_lama)){
							$log_data_lama .= "<br>";
						}
						$log_data_lama .= "$key = $value";
					}
				}
				
				$this->m_jadwal_kerja->ubah($set,$id_jadwal_kerja);

				if($this->m_jadwal_kerja->cek_hasil_jadwal_kerja($nama_ubah, $hari_ubah, $lintas_hari_masuk_ubah, $jam_masuk_ubah, $istirahat_ubah, $lintas_hari_mulai_istirahat_ubah, $jam_mulai_istirahat_ubah, $lintas_hari_akhir_istirahat_ubah, $jam_akhir_istirahat_ubah, $lintas_hari_pulang_ubah, $jam_pulang_ubah, $kode_erp_ubah, $varian_ubah, $status_ubah)){
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
					$return["error_info"] = "Perubahan Jadwal Kerja <b>Gagal</b> Dilakukan.";
				}
			}
			else{
				$return["status"] = $cek["status"];
				$return["error_info"] = $cek["error_info"];	
			}

			return $return;
		}
	}
	
	/* End of file jadwal_kerja.php */
	/* Location: ./application/controllers/master_data/jadwal_kerja.php */