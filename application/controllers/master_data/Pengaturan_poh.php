<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Pengaturan_poh extends CI_Controller {
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
			
			$this->load->model($this->folder_model."m_pengaturan_poh");

			$this->data['success'] = "";
			$this->data['warning'] = "";
			
			$this->data["is_with_sidebar"] = true;
			
			// Report all errors
            error_reporting(E_ALL);

            // Display errors in output
            ini_set('display_errors', 1);
		}
		
		public function index(){
			$this->data['judul'] = "Pengaturan Pemegang Operasional Harian (POH)";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."pengaturan_poh";
			
			array_push($this->data['js_sources'],"master_data/pengaturan_poh");

			if($this->input->post()){
				if(!strcmp($this->input->post("aksi"),"ubah")){
					izin($this->akses["ubah"]);
					
					$this->data["kelompok_jabatan"] = array();
					$this->data["pangkat"] = array();
					
					foreach($_POST as $key=>$value){
						$this->data[$key] = $value;
					}	
					
					$ubah = $this->ubah($this->data["kode_kelompok_jabatan"],$this->data["nama_kelompok_jabatan"],$this->data["kelompok_jabatan"],$this->data["pangkat"]);
					
					/* if($ubah["status"]){
						$this->data['success'] = "Perubahan Pengaturan Pemegang Operasional Harian (POH) untuk jabatan <b>".$this->data["nama_kelompok_jabatan"]."</b> berhasil dilakukan.";
					}
					else{
						$this->data['warning'] = $ubah['error_info'];
					} */

					$this->data['panel_tambah'] = "";
					$this->data["nama"] = "";
					$this->data['status'] = "";
				}
				else{
					$this->data['panel_tambah'] = "";
					$this->data['nama'] = "";
					$this->data['status'] = "";
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
									$('#tabel_pengaturan_poh').DataTable({
										responsive: true
									});
								});
							</script>";
				
				array_push($this->data["js_header_script"],$js_header_script);
			
				$this->data["daftar_pengaturan_poh"] = $this->m_pengaturan_poh->daftar_pengaturan_poh();
				$this->data["daftar_kelompok_jabatan"] = $this->m_pengaturan_poh->daftar_kelompok_jabatan();
				$this->data["daftar_pangkat"] = $this->m_pengaturan_poh->daftar_pangkat();
				
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
	
		private function ubah($kode_kelompok_jabatan,$nama_kelompok_jabatan,$kelompok_jabatan,$pangkat){
			$return = array("status" => false, "error_info" => "");
			
			$arr_data_lama = $this->m_pengaturan_poh->data_poh($kode_kelompok_jabatan);
			$log_data_lama = "";
			
			foreach($arr_data_lama as $key => $value){
				if(strcmp($key,"id")!=0){
					if(!empty($log_data_lama)){
						$log_data_lama .= "<br>";
					}
					$log_data_lama .= "$key = $value";
				}
			}
			
			$this->m_pengaturan_poh->hapus($kode_kelompok_jabatan);
			
			foreach($kelompok_jabatan as $id_kelompok_jabatan_poh){
				$data = array(
							"kode_kelompok_jabatan"=>$kode_kelompok_jabatan,
							"nama_kelompok_jabatan"=>$nama_kelompok_jabatan,
							"id_kelompok_jabatan_poh"=>$id_kelompok_jabatan_poh
						);
				$this->m_pengaturan_poh->tambah($data);
			}
			foreach($pangkat as $id_pangkat_poh){
				$data = array(
							"kode_kelompok_jabatan"=>$kode_kelompok_jabatan,
							"nama_kelompok_jabatan"=>$nama_kelompok_jabatan,
							"id_pangkat_poh"=>$id_pangkat_poh
						);
				$this->m_pengaturan_poh->tambah($data);
			}
			
			$this->m_pengaturan_poh->update_id_jabatan_kelompok($kode_kelompok_jabatan);
			$this->m_pengaturan_poh->update_jabatan_poh($kode_kelompok_jabatan);
			$this->m_pengaturan_poh->update_pangkat_poh($kode_kelompok_jabatan);
			
			if($this->m_pengaturan_poh->cek_hasil_poh($kode_kelompok_jabatan,$kelompok_jabatan,$pangkat)){
				$return["status"] = true;
					
				$arr_data_baru = $this->m_pengaturan_poh->data_poh($kode_kelompok_jabatan);
				
				$log_data_baru = "";
				foreach($arr_data_baru as $key => $value){
					if(!empty($log_data_baru)){
						$log_data_baru .= "<br>";
					}
					$log_data_baru .= "$key = $value";
				}
				
				$log = array(
					"id_pengguna" => $this->session->userdata("id_pengguna"),
					"id_modul" => $this->data['id_modul'],
					"id_target" => $arr_data_lama["kode_kelompok_jabatan"],
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
				$return["error_info"] = "Perubahan Grup Pengguna <b>Gagal</b> Dilakukan.";
			}
			
			/*$cek = $this->m_pengaturan_poh->cek_ubah_poh($kode_kelompok_jabatan,$status,$nama_ubah);
			if($cek["status"]){
				$set = array('nama'=>$nama_ubah,'status'=>$status_ubah);
				
				$this->m_pengaturan_poh->ubah($set,$nama,$status);

				if($this->m_pengaturan_poh->cek_hasil_poh($kode_kelompok_jabatan,$status_ubah)){
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
					$return["error_info"] = "Perubahan Grup Pengguna <b>Gagal</b> Dilakukan.";
				}
			}
			else{
				$return["status"] = $cek["status"];
				$return["error_info"] = $cek["error_info"];	
			}

			return $return; */
		}
	}
	
	
	/* End of file Poh.php */
	/* Location: ./application/controllers/master_data/Poh.php */