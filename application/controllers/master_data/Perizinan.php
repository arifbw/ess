<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Perizinan extends CI_Controller {
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
			
			$this->load->model($this->folder_model."/m_perizinan");

			$this->data['success'] = "";
			$this->data['warning'] = "";
			
			$this->data["is_with_sidebar"] = true;
		}
		
		public function index(){
			$this->data['judul'] = "Perizinan";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."perizinan";
			
			array_push($this->data['js_sources'],"master_data/perizinan");

			if($this->input->post()){
				if(!strcmp($this->input->post("aksi"),"tambah")){
					izin($this->akses["tambah"]);
				
					$this->data['nama'] = $this->input->post("nama");
					$this->data['keterangan'] = $this->input->post("keterangan");
					$this->data['kode_pamlek'] = $this->input->post("kode_pamlek");
					$this->data['kode_erp'] = $this->input->post("kode_erp");
					if(!strcmp($this->input->post("status"),"aktif")){
						$this->data['status'] = true;
					}
					else if(!strcmp($this->input->post("status"),"non aktif")){
						$this->data['status'] = false;
					}
					
					$tambah = $this->tambah($this->data['nama'],$this->data['keterangan'],$this->data['kode_pamlek'],$this->data['kode_erp'],$this->data['status']);
					
					$this->data['panel_tambah'] = "in";

					if($tambah['status']){
						$this->data['success'] = "Perizinan dengan nama <b>".$this->data['nama']."</b> berhasil ditambahkan.";
						$this->data['panel_tambah'] = "";
						$this->data['nama'] = "";
						$this->data['keterangan'] = "";
						$this->data['kode_pamlek'] = "";
						$this->data['kode_erp'] = "";
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
					$this->data['kode_pamlek'] = $this->input->post("kode_pamlek");
					$this->data['kode_pamlek_ubah'] = $this->input->post("kode_pamlek_ubah");
					$this->data['kode_erp'] = $this->input->post("kode_erp");
					$this->data['kode_erp_ubah'] = $this->input->post("kode_erp_ubah");
					$this->data['status'] = (bool)$this->input->post("status");
					if(!strcmp($this->input->post("status_ubah"),"aktif")){
						$this->data['status_ubah'] = true;
					}
					else if(!strcmp($this->input->post("status_ubah"),"non aktif")){
						$this->data['status_ubah'] = false;
					}

					$ubah = $this->ubah($this->data['nama'],$this->data['nama_ubah'],$this->data['keterangan_ubah'],$this->data['kode_pamlek_ubah'],$this->data['kode_erp_ubah'],$this->data['status_ubah']);
					
					if($ubah["status"]){
						$this->data['success'] = "Perubahan perizinan berhasil dilakukan.";
					}
					else{
						$this->data['warning'] = $ubah['error_info'];
					}

					$this->data['panel_tambah'] = "";
					$this->data['nama'] = "";
					$this->data['keterangan'] = "";
					$this->data['kode_pamlek'] = "";
					$this->data['kode_erp'] = "";
					$this->data['status'] = "";
				}
				else{
					$this->data['panel_tambah'] = "";
					$this->data['nama'] = "";
					$this->data['keterangan'] = "";
					$this->data['kode_pamlek'] = "";
					$this->data['kode_erp'] = "";
					$this->data['status'] = "";
				}
			}
			else{
				$this->data['panel_tambah'] = "";
				$this->data['nama'] = "";
				$this->data['keterangan'] = "";
				$this->data['kode_pamlek'] = "";
				$this->data['kode_erp'] = "";
				$this->data['status'] = "";
			}
			
			if($this->akses["lihat"]){
				$js_header_script = "<script>
								$(document).ready(function() {
									$('#tabel_perizinan').DataTable({
										responsive: true
									});
								});
							</script>";
				
				array_push($this->data["js_header_script"],$js_header_script);
				
				$this->data["daftar_perizinan"] = $this->m_perizinan->daftar_perizinan();
				
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
		
		private function tambah($nama,$keterangan,$kode_pamlek,$kode_erp,$status){
			$return = array("status" => false, "error_info" => "");
			if($this->m_perizinan->cek_tambah_perizinan($nama)){
				$data = array(
							"nama" => $nama,
							"keterangan" => $keterangan,
							"kode_pamlek" => $kode_pamlek,
							"kode_erp" => $kode_erp,
							"status" => $status
						);
				$this->m_perizinan->tambah($data);
				
				if($this->m_perizinan->cek_hasil_perizinan($nama,$keterangan,$kode_pamlek,$kode_erp,$status)){
					$return["status"] = true;
					
					$arr_data_insert = $this->m_perizinan->data_perizinan($nama);
					
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
					$return["error_info"] = "Penambahan Perizinan <b>Gagal</b> Dilakukan.";
				}
			}
			else{
				$return["status"] = false;
				$return["error_info"] = "Perizinan dengan nama <b>$nama</b> sudah ada.";
			}
			return $return;
		}
	
		private function ubah($nama,$nama_ubah,$keterangan_ubah,$kode_pamlek_ubah,$kode_erp_ubah,$status_ubah){
			$return = array("status" => false, "error_info" => "");
			$cek = $this->m_perizinan->cek_ubah_perizinan($nama,$nama_ubah);
			if($cek["status"]){
				$set = array("nama" => $nama_ubah, "keterangan" => $keterangan_ubah, "kode_pamlek" => $kode_pamlek_ubah, "kode_erp"=>$kode_erp_ubah, "status"=>$status_ubah);
				
				$arr_data_lama = $this->m_perizinan->data_perizinan($nama);
				$log_data_lama = "";
				
				foreach($arr_data_lama as $key => $value){
					if(!empty($log_data_lama)){
						$log_data_lama .= "<br>";
					}
					$log_data_lama .= "$key = $value";
				}
				
				$this->m_perizinan->ubah($set,$nama);

				if($this->m_perizinan->cek_hasil_perizinan($nama_ubah,$keterangan_ubah,$kode_pamlek_ubah,$kode_erp_ubah,$status_ubah)){
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
					$return["error_info"] = "Perubahan Perizinan <b>Gagal</b> Dilakukan.";
				}
			}
			else{
				$return["status"] = $cek["status"];
				$return["error_info"] = $cek["error_info"];	
			}

			return $return;
		}
	}
	
	/* End of file perizinan.php */
	/* Location: ./application/controllers/master_data/perizinan.php */