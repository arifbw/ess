<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Cdtp extends CI_Controller {
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
			
			$this->load->model($this->folder_model."m_cdtp");

			$this->data['success'] = "";
			$this->data['warning'] = "";
			
			$this->data["is_with_sidebar"] = true;
		}
		
		public function index(){
			$this->data['judul'] = "Cuti di Luar Tanggungan Perusahaan (CDTP)";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."cdtp";
			
			array_push($this->data['js_sources'],"osdm/cdtp");

			if($this->input->post()){
				if(!strcmp($this->input->post("aksi"),"tambah")){
					izin($this->akses["tambah"]);
					
					list($this->data['no_pokok'],$this->data['nama']) = explode(" - ",$this->input->post("karyawan"));
					$this->data['tanggal_mulai'] = $this->input->post("tanggal_mulai");
					$this->data['tanggal_selesai'] = $this->input->post("tanggal_selesai");
					$this->data['skep'] = $this->input->post("skep");
					
					$tambah = $this->tambah($this->data['no_pokok'],$this->data['nama'],$this->data['tanggal_mulai'],$this->data['tanggal_selesai'],$this->data['skep']);
					
					$this->data['panel_tambah'] = "in";

					if($tambah['status']){
						$this->data['success'] = "Cuti di Luar Tanggungan Perusahaan (CDTP) dengan nama <b>".$this->data['nama']."</b> berhasil ditambahkan.";
						$this->data['panel_tambah'] = "";
						$this->data['no_pokok'] = "";
						$this->data['nama'] = "";
						$this->data['tanggal_mulai'] = "";
						$this->data['tanggal_selesai'] = "";
						$this->data['skep'] = "";
					}
					else{
						$this->data['warning'] = $tambah['error_info'];
					}
				}
				else if(!strcmp($this->input->post("aksi"),"ubah")){
					izin($this->akses["ubah"]);
					
					list($this->data['no_pokok'],$this->data['nama']) = explode(" - ",$this->input->post("karyawan"));
					list($this->data['no_pokok_ubah'],$this->data['nama_ubah']) = explode(" - ",$this->input->post("karyawan_ubah"));
					$this->data['tanggal_mulai'] = $this->input->post("tanggal_mulai");
					$this->data['tanggal_mulai_ubah'] = $this->input->post("tanggal_mulai_ubah");
					$this->data['tanggal_selesai'] = $this->input->post("tanggal_selesai");
					$this->data['tanggal_selesai_ubah'] = $this->input->post("tanggal_selesai_ubah");
					$this->data['skep'] = $this->input->post("skep");
					$this->data['skep_ubah'] = $this->input->post("skep_ubah");

					$ubah = $this->ubah($this->data["no_pokok"],$this->data["nama"],$this->data["tanggal_mulai"],$this->data["tanggal_selesai"],$this->data["skep"],$this->data["no_pokok_ubah"],$this->data["nama_ubah"],$this->data["tanggal_mulai_ubah"],$this->data["tanggal_selesai_ubah"],$this->data["skep_ubah"]);

					if($ubah["status"]){
						$this->data['success'] = "Perubahan kelompok modul berhasil dilakukan.";
					}
					else{
						$this->data['warning'] = $ubah['error_info'];
					}

					$this->data["nama"] = "";
					$this->data["tanggal_mulai"] = "";
					$this->data["status"] = "";
					$this->data["panel_tambah"] = "";
				}
				else{
					$this->data["panel_tambah"] = "";
					$this->data['no_pokok'] = "";
					$this->data['nama'] = "";
					$this->data['tanggal_mulai'] = "";
					$this->data['tanggal_selesai'] = "";
					$this->data['skep'] = "";
				}
			}
			else{
				$this->data["panel_tambah"] = "";
				$this->data['no_pokok'] = "";
				$this->data['nama'] = "";
				$this->data['tanggal_mulai'] = "";
				$this->data['tanggal_selesai'] = "";
				$this->data['skep'] = "";
			}
			
			if($this->akses["lihat"]){
				$js_header_script = "<script>
								$(document).ready(function() {
									$('#tabel_cdtp').DataTable({
										responsive: true
									});
								});
							</script>";
				
				array_push($this->data["js_header_script"],$js_header_script);
				
				$this->data["daftar_cdtp"] = $this->m_cdtp->daftar_cdtp();
				
				$log = array(
						"id_pengguna" => $this->session->userdata("id_pengguna"),
						"id_modul" => $this->data['id_modul'],
						"deskripsi" => "lihat ".strtolower(preg_replace("/_/"," ",__CLASS__)),
						"alamat_ip" => $this->data["ip_address"],
						"waktu" => date("Y-m-d H:i:s")
					);
				$this->m_log->tambah($log);
			}
			
			if($this->akses["tambah"] or $this->akses["ubah"]){
				$this->load->model("master_data/m_karyawan");
				$this->data["daftar_karyawan"] = $this->m_karyawan->daftar_karyawan();
			}
			
			$this->load->view('template',$this->data);
		}
		
		private function tambah($no_pokok,$nama,$tanggal_mulai,$tanggal_selesai,$skep){
			$return = array("status" => false, "error_info" => "");
			if($this->m_cdtp->cek_tambah_cdtp($no_pokok)){
				$data = array(
						"no_pokok" => $no_pokok,
						"nama" => $nama,
						"tanggal_mulai" => $tanggal_mulai,
						"tanggal_selesai" => $tanggal_selesai,
						"skep" => $skep
					);
				$this->m_cdtp->tambah($data);
				
				if($this->m_cdtp->cek_hasil_cdtp($no_pokok,$nama,$tanggal_mulai,$tanggal_selesai,$skep)){
					$return["status"] = true;
					
					$arr_data_insert = $this->m_cdtp->data_cdtp($no_pokok);
					
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
					$return["error_info"] = "Penambahan Cuti di Luar Tanggungan Perusahaan (CDTP) <b>Gagal</b> Dilakukan.";
				}
			}
			else{
				$return["status"] = false;
				$return["error_info"] = "Cuti di Luar Tanggungan Perusahaan (CDTP) dengan nama <b>$nama</b> sudah ada.";
			}
			return $return;
		}
	
		private function ubah($no_pokok,$nama,$tanggal_mulai,$tanggal_selesai,$skep,$no_pokok_ubah,$nama_ubah,$tanggal_mulai_ubah,$tanggal_selesai_ubah,$skep_ubah){
			$return = array("status" => false, "error_info" => "");
			$cek = $this->m_cdtp->cek_ubah_cdtp($no_pokok,$no_pokok_ubah);
			if($cek["status"]){
				$set = array('no_pokok'=>$no_pokok_ubah,'nama'=>$nama_ubah,'tanggal_mulai'=>$tanggal_mulai_ubah,'tanggal_selesai'=>$tanggal_selesai_ubah,'skep'=>$skep_ubah);
				$arr_data_lama = $this->m_cdtp->data_cdtp($no_pokok);
				$log_data_lama = "";
				
				foreach($arr_data_lama as $key => $value){
					if(strcmp($key,"id")!=0){
						if(!empty($log_data_lama)){
							$log_data_lama .= "<br>";
						}
						$log_data_lama .= "$key = $value";
					}
				}
				
				$this->m_cdtp->ubah($set,$no_pokok,$nama,$tanggal_mulai,$tanggal_selesai,$skep);

				if($this->m_cdtp->cek_hasil_cdtp($no_pokok_ubah,$nama_ubah,$tanggal_mulai_ubah,$tanggal_selesai_ubah,$skep_ubah)){
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
					$return["error_info"] = "Perubahan Cuti di Luar Tanggungan Perusahaan (CDTP) <b>Gagal</b> Dilakukan.";
				}
			}
			else{
				$return["status"] = $cek["status"];
				$return["error_info"] = $cek["error_info"];	
			}

			return $return;
		}
	}
	
	/* End of file cdtp.php */
	/* Location: ./application/controllers/osdm/cdtp.php */