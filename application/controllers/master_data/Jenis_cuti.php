<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Jenis_cuti extends CI_Controller {
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
			
			$this->load->model($this->folder_model."/m_jenis_cuti");

			$this->data['success'] = "";
			$this->data['warning'] = "";
			
			$this->data["is_with_sidebar"] = true;
		}
		
		public function index(){
			$this->data['judul'] = "Jenis Cuti";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."jenis_cuti";
			
			array_push($this->data['js_sources'],"master_data/jenis_cuti");

			if($this->input->post()){
				if(!strcmp($this->input->post("aksi"),"tambah")){
					izin($this->akses["tambah"]);
				
					$this->data['uraian'] = $this->input->post("uraian");
					$this->data['kode_erp'] = $this->input->post("kode_erp");
					if(!strcmp($this->input->post("status"),"aktif")){
						$this->data['status'] = true;
					}
					else if(!strcmp($this->input->post("status"),"non aktif")){
						$this->data['status'] = false;
					}
					
					$tambah = $this->tambah($this->data['uraian'],$this->data['kode_erp'],$this->data['status']);
					
					$this->data['panel_tambah'] = "in";

					if($tambah['status']){
						$this->data['success'] = "Jenis Cuti dengan uraian <b>".$this->data['uraian']."</b> berhasil ditambahkan.";
						$this->data['panel_tambah'] = "";
						$this->data['uraian'] = "";
						$this->data['kode_erp'] = "";
						$this->data['status'] = "";
					}
					else{
						$this->data['warning'] = $tambah['error_info'];
					}
				}
				else if(!strcmp($this->input->post("aksi"),"ubah")){
					izin($this->akses["ubah"]);
					
					$this->data['uraian'] = $this->input->post("uraian");
					$this->data['uraian_ubah'] = $this->input->post("uraian_ubah");
					$this->data['kode_erp'] = $this->input->post("kode_erp");
					$this->data['kode_erp_ubah'] = $this->input->post("kode_erp_ubah");
					$this->data['status'] = (bool)$this->input->post("status");
					if(!strcmp($this->input->post("status_ubah"),"aktif")){
						$this->data['status_ubah'] = true;
					}
					else if(!strcmp($this->input->post("status_ubah"),"non aktif")){
						$this->data['status_ubah'] = false;
					}

					$ubah = $this->ubah($this->data['uraian'],$this->data['uraian_ubah'],$this->data['kode_erp_ubah'],$this->data['status_ubah']);
					
					if($ubah["status"]){
						$this->data['success'] = "Perubahan jenis cuti berhasil dilakukan.";
					}
					else{
						$this->data['warning'] = $ubah['error_info'];
					}

					$this->data['panel_tambah'] = "";
					$this->data['uraian'] = "";
					$this->data['kode_erp'] = "";
					$this->data['status'] = "";
				}
				else{
					$this->data['panel_tambah'] = "";
					$this->data['uraian'] = "";
					$this->data['kode_erp'] = "";
					$this->data['status'] = "";
				}
			}
			else{
				$this->data['panel_tambah'] = "";
				$this->data['uraian'] = "";
				$this->data['kode_erp'] = "";
				$this->data['status'] = "";
			}
			
			if($this->akses["lihat"]){
				$js_header_script = "<script>
								$(document).ready(function() {
									$('#tabel_jenis_cuti').DataTable({
										responsive: true
									});
								});
							</script>";
				
				array_push($this->data["js_header_script"],$js_header_script);
				
				$this->data["daftar_jenis_cuti"] = $this->m_jenis_cuti->daftar_jenis_cuti();
				
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
		
		private function tambah($uraian,$kode_erp,$status){
			$return = array("status" => false, "error_info" => "");
			if($this->m_jenis_cuti->cek_tambah_jenis_cuti($uraian)){
				$data = array(
							"uraian" => $uraian,
							"kode_erp" => $kode_erp,
							"status" => $status
						);
				$this->m_jenis_cuti->tambah($data);
				
				if($this->m_jenis_cuti->cek_hasil_jenis_cuti($uraian,$kode_erp,$status)){
					$return["status"] = true;
					
					$arr_data_insert = $this->m_jenis_cuti->data_jenis_cuti($uraian);
					
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
					$return["error_info"] = "Penambahan Jenis Cuti <b>Gagal</b> Dilakukan.";
				}
			}
			else{
				$return["status"] = false;
				$return["error_info"] = "Jenis Cuti dengan uraian <b>$uraian</b> sudah ada.";
			}
			return $return;
		}
	
		private function ubah($uraian,$uraian_ubah,$kode_erp_ubah,$status_ubah){
			$return = array("status" => false, "error_info" => "");
			$cek = $this->m_jenis_cuti->cek_ubah_jenis_cuti($uraian,$uraian_ubah);
			if($cek["status"]){
				$set = array("uraian" => $uraian_ubah, "kode_erp"=>$kode_erp_ubah, "status"=>$status_ubah);
				
				$arr_data_lama = $this->m_jenis_cuti->data_jenis_cuti($uraian);
				$log_data_lama = "";
				
				foreach($arr_data_lama as $key => $value){
					if(!empty($log_data_lama)){
						$log_data_lama .= "<br>";
					}
					$log_data_lama .= "$key = $value";
				}
				
				$this->m_jenis_cuti->ubah($set,$uraian);

				if($this->m_jenis_cuti->cek_hasil_jenis_cuti($uraian_ubah,$kode_erp_ubah,$status_ubah)){
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
					$return["error_info"] = "Perubahan Jenis Cuti <b>Gagal</b> Dilakukan.";
				}
			}
			else{
				$return["status"] = $cek["status"];
				$return["error_info"] = $cek["error_info"];	
			}

			return $return;
		}
	}
	
	/* End of file jenis_cuti.php */
	/* Location: ./application/controllers/master_data/jenis_cuti.php */