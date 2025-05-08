<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Hari_libur extends CI_Controller {
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
			
			$this->load->model($this->folder_model."m_hari_libur");

			$this->data['success'] = "";
			$this->data['warning'] = "";
			
			$this->data["is_with_sidebar"] = true;
		}
		
		public function index(){
			$this->data['judul'] = "Hari Libur";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."hari_libur";
			
			array_push($this->data['js_sources'],"master_data/hari_libur");

			if($this->input->post()){
				if(!strcmp($this->input->post("aksi"),"tambah")){
					izin($this->akses["tambah"]);
					
					$this->data['tanggal'] = $this->input->post("tanggal");
					$this->data['deskripsi'] = $this->input->post("deskripsi");
					if(!strcmp($this->input->post("hari_raya_keagamaan"),"ya")){
						$this->data['hari_raya_keagamaan'] = "1";
					}
					else if(!strcmp($this->input->post("hari_raya_keagamaan"),"tidak")){
						$this->data['hari_raya_keagamaan'] = "0";
					}
					
					$tambah = $this->tambah($this->data['tanggal'],$this->data['deskripsi'],$this->data['hari_raya_keagamaan']);
					
					$this->data['panel_tambah'] = "in";

					if($tambah['status']){
						$this->data['success'] = "Hari libur dengan tanggal <b>".tanggal($this->data['tanggal'])."</b> berhasil ditambahkan.";
						$this->data['panel_tambah'] = "";
						$this->data['tanggal'] = "";
						$this->data['deskripsi'] = "";
						$this->data['hari_raya_keagamaan'] = "";
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
					
					if(!strcmp($this->input->post("hari_raya_keagamaan"),"ya")){
						$this->data['hari_raya_keagamaan'] = "1";
					}
					else if(!strcmp($this->input->post("hari_raya_keagamaan"),"tidak")){
						$this->data['hari_raya_keagamaan'] = "0";
					}
					
					if(!strcmp($this->input->post("hari_raya_keagamaan_ubah"),"ya")){
						$this->data['hari_raya_keagamaan_ubah'] = "1";
					}
					else if(!strcmp($this->input->post("hari_raya_keagamaan_ubah"),"tidak")){
						$this->data['hari_raya_keagamaan_ubah'] = "0";
					}

					$ubah = $this->ubah($this->data["tanggal"],$this->data["deskripsi"],$this->data["hari_raya_keagamaan"],$this->data["tanggal_ubah"],$this->data["deskripsi_ubah"],$this->data["hari_raya_keagamaan_ubah"]);

					if($ubah["status"]){
						$this->data['success'] = "Perubahan hari libur berhasil dilakukan.";
					}
					else{
						$this->data['warning'] = $ubah['error_info'];
					}

					$this->data["tanggal"] = "";
					$this->data["deskripsi"] = "";
					$this->data["hari_raya_keagamaan"] = "";
					$this->data["panel_tambah"] = "";
				}
				else{
					$this->data["tanggal"] = "";
					$this->data["deskripsi"] = "";
					$this->data["hari_raya_keagamaan"] = "";
					$this->data["panel_tambah"] = "";
				}
			}
			else{
				$this->data["tanggal"] = "";
				$this->data["deskripsi"] = "";
				$this->data["hari_raya_keagamaan"] = "";
				$this->data["panel_tambah"] = "";
			}
			
			if($this->akses["lihat"]){
				$js_header_script = "<script>
								$(document).ready(function() {
									$('#tabel_hari_libur').DataTable({
										responsive: true,
										order:false
									});
								});
							</script>";
				
				array_push($this->data["js_header_script"],$js_header_script);
				
				$this->data["daftar_hari_libur"] = $this->m_hari_libur->daftar_hari_libur();
				
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
		
		private function tambah($tanggal,$deskripsi,$hari_raya_keagamaan){
			$return = array("status" => false, "error_info" => "");
			if($this->m_hari_libur->cek_tambah_hari_libur($tanggal)){
				$data = array(
						"tanggal" => $tanggal,
						"deskripsi" => $deskripsi,
						"hari_raya_keagamaan" => $hari_raya_keagamaan
					);
				$this->m_hari_libur->tambah($data);
				
				if($this->m_hari_libur->cek_hasil_hari_libur($tanggal,$deskripsi,$hari_raya_keagamaan)){
					$return["status"] = true;
					
					$arr_data_insert = $this->m_hari_libur->data_hari_libur($tanggal);
					
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
					$return["error_info"] = "Penambahan Hari Libur <b>Gagal</b> Dilakukan.";
				}
			}
			else{
				$return["status"] = false;
				$return["error_info"] = "Hari Libur dengan tanggal <b>".tanggal($tanggal)."</b> sudah ada.";
			}
			return $return;
		}
	
		private function ubah($tanggal,$deskripsi,$hari_raya_keagamaan,$tanggal_ubah,$deskripsi_ubah,$hari_raya_keagamaan_ubah){
			$return = array("status" => false, "error_info" => "");
			$cek = $this->m_hari_libur->cek_ubah_hari_libur($tanggal,$tanggal_ubah);
			if($cek["status"]){
				$set = array('tanggal'=>$tanggal_ubah,'deskripsi'=>$deskripsi_ubah,'hari_raya_keagamaan'=>$hari_raya_keagamaan_ubah);
				$arr_data_lama = $this->m_hari_libur->data_hari_libur($tanggal);
				$log_data_lama = "";
				
				foreach($arr_data_lama as $key => $value){
					if(strcmp($key,"id")!=0){
						if(!empty($log_data_lama)){
							$log_data_lama .= "<br>";
						}
						$log_data_lama .= "$key = $value";
					}
				}
				
				$this->m_hari_libur->ubah($set,$tanggal,$deskripsi,$hari_raya_keagamaan);

				if($this->m_hari_libur->cek_hasil_hari_libur($tanggal_ubah,$deskripsi_ubah,$hari_raya_keagamaan_ubah)){
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
					$return["error_info"] = "Perubahan Hari Libur <b>Gagal</b> Dilakukan.";
				}
			}
			else{
				$return["status"] = $cek["status"];
				$return["error_info"] = $cek["error_info"];	
			}

			return $return;
		}
	}
	
	/* End of file hari_libur.php */
	/* Location: ./application/controllers/master_data/hari_libur.php */