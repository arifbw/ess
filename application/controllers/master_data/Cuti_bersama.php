<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Cuti_bersama extends CI_Controller {
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
			
			$this->load->model($this->folder_model."m_cuti_bersama");

			$this->data['success'] = "";
			$this->data['warning'] = "";
			
			$this->data["is_with_sidebar"] = true;
		}
		
		public function index(){
			$this->data['judul'] = "Cuti Bersama";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."cuti_bersama";
			
			array_push($this->data['js_sources'],"master_data/cuti_bersama");

			if($this->input->post()){
				if(!strcmp($this->input->post("aksi"),"tambah")){
					izin($this->akses["tambah"]);
					
					$this->data['tanggal'] = $this->input->post("tanggal");
					$this->data['deskripsi'] = $this->input->post("deskripsi");

					$tambah = $this->tambah($this->data['tanggal'],$this->data['deskripsi']);
					
					$this->data['panel_tambah'] = "in";

					if($tambah['status']){
						$this->data['success'] = "Cuti bersama dengan tanggal <b>".tanggal($this->data['tanggal'])."</b> berhasil ditambahkan.";
						$this->data['panel_tambah'] = "";
						$this->data['tanggal'] = "";
						$this->data['deskripsi'] = "";
					}
					else{
						$this->data['warning'] = $tambah['error_info'];
					}
				}
				else if(!strcmp($this->input->post("aksi"),"ubah")){
					izin($this->akses["ubah"]);
					
					$this->data['tanggal'] = $this->input->post("tanggal");
					$this->data['tanggal_ubah'] = $this->input->post("tanggal_ubah");
					$this->data['deskripsi'] = $this->input->post("deskripsi");
					$this->data['deskripsi_ubah'] = $this->input->post("deskripsi_ubah");

					$ubah = $this->ubah($this->data["tanggal"],$this->data["deskripsi"],$this->data["tanggal_ubah"],$this->data["deskripsi_ubah"]);

					if($ubah["status"]){
						$this->data['success'] = "Perubahan cuti bersama berhasil dilakukan.";
					}
					else{
						$this->data['warning'] = $ubah['error_info'];
					}

					$this->data["tanggal"] = "";
					$this->data["deskripsi"] = "";
					$this->data["panel_tambah"] = "";
				}
				else{
					$this->data["tanggal"] = "";
					$this->data["deskripsi"] = "";
					$this->data["panel_tambah"] = "";
				}
			}
			else{
				$this->data["tanggal"] = "";
				$this->data["deskripsi"] = "";
				$this->data["panel_tambah"] = "";
			}
			
			if($this->akses["lihat"]){
				$js_header_script = "<script>
								$(document).ready(function() {
									$('#tabel_cuti_bersama').DataTable({
										responsive: true,
										order:false
									});
								});
							</script>";
				
				array_push($this->data["js_header_script"],$js_header_script);
				
				$this->data["daftar_cuti_bersama"] = $this->m_cuti_bersama->daftar_cuti_bersama();
				
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
		
		private function tambah($tanggal,$deskripsi){
			$return = array("status" => false, "error_info" => "");
			if($this->m_cuti_bersama->cek_tambah_cuti_bersama($tanggal)){
				$data = array(
						"tanggal" => $tanggal,
						"deskripsi" => $deskripsi
					);
				$this->m_cuti_bersama->tambah($data);
				
				if($this->m_cuti_bersama->cek_hasil_cuti_bersama($tanggal,$deskripsi)){
					$return["status"] = true;
					
					$arr_data_insert = $this->m_cuti_bersama->data_cuti_bersama($tanggal);
					
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
					$return["error_info"] = "Penambahan Cuti Bersama <b>Gagal</b> Dilakukan.";
				}
			}
			else{
				$return["status"] = false;
				$return["error_info"] = "Cuti Bersama dengan tanggal <b>".tanggal($tanggal)."</b> sudah ada sebagai Cuti Bersama atau Hari Libur.";
			}
			return $return;
		}
	
		private function ubah($tanggal,$deskripsi,$tanggal_ubah,$deskripsi_ubah){
			$return = array("status" => false, "error_info" => "");
			$cek = $this->m_cuti_bersama->cek_ubah_cuti_bersama($tanggal,$tanggal_ubah);
			if($cek["status"]){
				$set = array('tanggal'=>$tanggal_ubah,'deskripsi'=>$deskripsi_ubah);
				$arr_data_lama = $this->m_cuti_bersama->data_cuti_bersama($tanggal);
				$log_data_lama = "";
				
				foreach($arr_data_lama as $key => $value){
					if(strcmp($key,"id")!=0){
						if(!empty($log_data_lama)){
							$log_data_lama .= "<br>";
						}
						$log_data_lama .= "$key = $value";
					}
				}
				
				$this->m_cuti_bersama->ubah($set,$tanggal,$deskripsi);

				if($this->m_cuti_bersama->cek_hasil_cuti_bersama($tanggal_ubah,$deskripsi_ubah)){
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
					$return["error_info"] = "Perubahan Cuti Bersama <b>Gagal</b> Dilakukan.";
				}
			}
			else{
				$return["status"] = $cek["status"];
				$return["error_info"] = $cek["error_info"];	
			}

			return $return;
		}
	}
	
	/* End of file cuti_bersama.php */
	/* Location: ./application/controllers/master_data/cuti_bersama.php */