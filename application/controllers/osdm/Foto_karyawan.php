<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Foto_karyawan extends CI_Controller {
		public function __construct(){
			parent::__construct();
			
			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'osdm/';
			$this->folder_model = 'osdm/';
			$this->akses = array();
			
			$this->load->model($this->folder_model."m_foto_karyawan");

			$this->data['success'] = "";
			$this->data['warning'] = "";
			
			$this->data["folder_lihat_biodata"] = "/foto/biodata/";
			$this->data["folder_lihat_profile"] = "/foto/profile/";
			$this->data["folder_sementara"] = dirname($_SERVER["SCRIPT_FILENAME"])."/foto/temp/";
			$this->data["folder_biodata"] = dirname($_SERVER["SCRIPT_FILENAME"]).$this->data["folder_lihat_biodata"];
			$this->data["folder_profile"] = dirname($_SERVER["SCRIPT_FILENAME"]).$this->data["folder_lihat_profile"];
			
			$this->data["is_with_sidebar"] = true;
		}
		
		public function index(){
			$this->data['judul'] = "Foto Karyawan";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			izin($this->akses["akses"]);
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."foto_karyawan";
			
			array_push($this->data['js_sources'],"osdm/foto_karyawan");

			if($this->input->post()){
				if(strcmp($this->input->post("aksi"),"tambah")==0){
					if(count($_FILES["file_foto"]["name"])>0){
						$hasil_tambah = $this->tambah();
						
						if($hasil_tambah["berhasil"]>0){
							$this->data["success"] = "Berhasil menambahkan ".$hasil_tambah["berhasil"]." foto karyawan";							
						}
						if($hasil_tambah["sudah_ada"]>0){
							$this->data["warning"] = "Terdapat ".$hasil_tambah["sudah_ada"]." karyawan yang sudah ada foto. Gunakan fitur ubah foto.";
						}
						if($hasil_tambah["gagal"]>0){
							if(!empty($this->data["warning"])){
								$this->data["warning"] .= "<br>";
							}
							$this->data["warning"] .= "Gagal menambahkan ".$hasil_tambah["berhasil"]." foto karyawan";							
						}
					}
					
				}
				else if(strcmp($this->input->post("aksi"),"ubah")==0){
					if(count($_FILES["file_foto"]["name"])>0){
						$hasil_ubah = $this->ubah();
						
						if($hasil_tambah["berhasil"]>0){
							$this->data["success"] = "Berhasil mengubah ".$hasil_tambah["berhasil"]." foto karyawan";							
						}
						if($hasil_tambah["gagal"]>0){
							$this->data["warning"] .= "Gagal mengubah ".$hasil_tambah["berhasil"]." foto karyawan";							
						}
					}
				}
			}
			else{
			}
			
			if($this->akses["tambah"]){
				$this->data['panel_tambah'] = "";
				$this->data["max_file"] = 50;
			}
			
			if($this->akses["lihat"]){
				/* $js_header_script = "<script>
								$(document).ready(function() {
									$('#tabel_pengadministrasi_unit_kerja').DataTable({
										responsive: true
									});
								});
							</script>";
				
				array_push($this->data["js_header_script"],$js_header_script); */
				
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
	
		public function lihat($halaman,$cari){
			$this->data['judul'] = "Foto Karyawan";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			izin($this->akses["akses"]);
			
			$this->data["akses"] = $this->akses;
			
			$this->data["halaman"] = $halaman;
			$this->data["foto_per_baris"] = 4;
			$this->data["baris"] = 5;
			$this->data["foto_per_halaman"] = $this->data["foto_per_baris"]*$this->data["baris"];

			$this->data["banyak_foto"] = $this->m_foto_karyawan->banyak_foto($cari);
			$this->data["banyak_halaman"] = ceil($this->data["banyak_foto"]/$this->data["foto_per_halaman"]);
			
			$this->data["daftar_foto"] = $this->m_foto_karyawan->lihat($this->data["halaman"],$this->data["foto_per_halaman"],$cari);
			
			$this->data["cari"] = $cari;

			$this->load->view($this->folder_view."daftar_foto_karyawan",$this->data);
		}
		
		private function tambah(){
			
			 // Report all errors
            //error_reporting(E_ALL);

            // Display errors in output
            //ini_set('display_errors', 1);
			
			$banyak_file = count($_FILES["file_foto"]["name"]);
			$hitung["gagal"] = 0;
			$hitung["sudah_ada"] = 0;
			$hitung["berhasil"] = 0;
			
			if($banyak_file>0){
				$config['upload_path']          = $this->data["folder_sementara"];
				$config['allowed_types']        = "gif|jpg|png";
				$this->load->library('upload', $config);
				
				$config_biodata['image_library']='gd2';
				$config_biodata['create_thumb']= FALSE;
				$config_biodata['maintain_ratio']= FALSE;
				$config_biodata['quality']= '50%';
				$config_biodata['width']= 150;
				$config_biodata['height']= 200;
				
				$config_profile['image_library']='gd2';
				$config_profile['create_thumb']= FALSE;
				$config_profile['maintain_ratio']= FALSE;
				$config_profile['quality']= '50%';
				$config_profile['width']= 30;
				$config_profile['height']= 45;
				
				$this->load->library("image_lib");
				
				$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
				
				$this->load->model("master_data/m_karyawan");
				
				for($i=0;$i<$banyak_file;$i++){
					$_FILES['file']['name']     = $_FILES['file_foto']['name'][$i];
					$_FILES['file']['type']     = $_FILES['file_foto']['type'][$i];
					$_FILES['file']['tmp_name'] = $_FILES['file_foto']['tmp_name'][$i];
					$_FILES['file']['error']    = $_FILES['file_foto']['error'][$i];
					$_FILES['file']['size']     = $_FILES['file_foto']['size'][$i];
					
					if ( ! $this->upload->do_upload("file")){
						$error = array('error' => $this->upload->display_errors());
					}
					else{
						$uploaded = $this->upload->data();
						
						//cek apakah np tersebut sudah ada fotonya
						$data_simpan["no_pokok"] = pathinfo($this->data["folder_sementara"].$uploaded["file_name"])["filename"];
						$cek = $this->m_foto_karyawan->cek_ada($data_simpan["no_pokok"]);
						
						//kalo udah ada, warning bilang udah ada
						if($cek){
							$hitung["sudah_ada"]++;
						}
						//kalo belom ada, tambah
						else{
							$code = "";
							while(strlen($code)<4){
								$code .= $chars[rand(0,strlen($chars)-1)];
							}
							$nama_file = md5($code).".".strtolower(pathinfo($this->data["folder_sementara"].$uploaded["file_name"], PATHINFO_EXTENSION));
							
							//resize untuk biodata
							$config_biodata['source_image']=$this->data["folder_sementara"].$uploaded["file_name"];
							$config_biodata['new_image']=$this->data["folder_biodata"].$nama_file;
							$this->image_lib->clear();
							$this->image_lib->initialize($config_biodata);
							$is_resize_biodata = $this->image_lib->resize();
							
							//resize untuk profile
							$config_profile['source_image']=$this->data["folder_sementara"].$uploaded["file_name"];
							$config_profile['new_image']=$this->data["folder_profile"].$nama_file;
							$this->image_lib->clear();
							$this->image_lib->initialize($config_profile);
							$is_resize_profile = $this->image_lib->resize();
																
							if($is_resize_biodata and $is_resize_profile){
								$karyawan = $this->m_karyawan->get_karyawan($data_simpan["no_pokok"]);
								
								if(isset($karyawan["nama"])){
									$data_simpan["nama"] = $karyawan["nama"];
								}
								else{
									$data_simpan["nama"] = "";
								}

								$data_simpan["code"] = $code;
								$data_simpan["nama_file"] = $nama_file;
								$data_simpan["waktu_ubah"] = date("Y-m-d H:i:s");
								
																
								$this->m_foto_karyawan->tambah($data_simpan);
								
								if($this->m_foto_karyawan->cek($data_simpan)){
									$hitung["berhasil"]++;
									
									$arr_data_baru = $this->m_foto_karyawan->data_file($data_simpan["no_pokok"]);
									$log_data_baru = "";
									
									foreach($arr_data_baru as $key => $value){
										if(in_array($key,array("no_pokok","nama","nama_file","waktu_ubah"))!=0){
											if(!empty($log_data_baru)){
												$log_data_baru .= "<br>";
											}
											$log_data_baru .= "$key = $value";
											if(strcmp($key,"nama_file")==0){
												$log_data_baru .= "<img src='".base_url($this->data["folder_lihat_biodata"].$value)."'>";
											}
										}
									}
									
									$log = array(
										"id_pengguna" => $this->session->userdata("id_pengguna"),
										"id_modul" => $this->data['id_modul'],
										"id_target" => $arr_data_baru["id"],
										"deskripsi" => "ubah ".strtolower(preg_replace("/_/"," ",__CLASS__)),
										"kondisi_baru" => $log_data_baru,
										"alamat_ip" => $this->data["ip_address"],
										"waktu" => date("Y-m-d H:i:s")
									);
									$this->m_log->tambah($log);
								}
								else{
									$hitung["gagal"]++;
								}
								
							}
							else{
								$hitung["gagal"]++;
							}
							//bikin log
							
						}
						unlink($this->data["folder_sementara"].$uploaded["file_name"]);
					}
				}
			}
			
			return $hitung;
		}
		
		private function ubah(){
			$banyak_file = count($_FILES["file_foto"]["name"]);
			$hitung["gagal"] = 0;
			$hitung["sudah_ada"] = 0;
			$hitung["berhasil"] = 0;
			
			if($banyak_file>0){
				$config['upload_path']          = $this->data["folder_sementara"];
				$config['allowed_types']        = "gif|jpg|png";
				$this->load->library('upload', $config);
				
				$config_biodata['image_library']='gd2';
				$config_biodata['create_thumb']= FALSE;
				$config_biodata['maintain_ratio']= FALSE;
				$config_biodata['quality']= '50%';
				$config_biodata['width']= 150;
				$config_biodata['height']= 200;
				
				$config_profile['image_library']='gd2';
				$config_profile['create_thumb']= FALSE;
				$config_profile['maintain_ratio']= FALSE;
				$config_profile['quality']= '50%';
				$config_profile['width']= 30;
				$config_profile['height']= 45;
				
				$this->load->library("image_lib");
				
				$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
				
				$this->load->model("master_data/m_karyawan");
				
				if ( ! $this->upload->do_upload("file")){
					$error = array('error' => $this->upload->display_errors());
				}
				else{
					$uploaded = $this->upload->data();
					
					//cek apakah np tersebut sudah ada fotonya
					$data_simpan["no_pokok"] = pathinfo($this->data["folder_sementara"].$uploaded["file_name"])["filename"];
					
					$code = "";
					while(strlen($code)<4){
						$code .= $chars[rand(0,strlen($chars)-1)];
					}
					$nama_file = md5($code).".".strtolower(pathinfo($this->data["folder_sementara"].$uploaded["file_name"], PATHINFO_EXTENSION));
					
					//resize untuk biodata
					$config_biodata['source_image']=$this->data["folder_sementara"].$uploaded["file_name"];
					$config_biodata['new_image']=$this->data["folder_biodata"].$nama_file;
					$this->image_lib->clear();
					$this->image_lib->initialize($config_biodata);
					$is_resize_biodata = $this->image_lib->resize();
					
					//resize untuk profile
					$config_profile['source_image']=$this->data["folder_sementara"].$uploaded["file_name"];
					$config_profile['new_image']=$this->data["folder_profile"].$nama_file;
					$this->image_lib->clear();
					$this->image_lib->initialize($config_profile);
					$is_resize_profile = $this->image_lib->resize();
														
					if($is_resize_biodata and $is_resize_profile){
						$hitung_berhasil++;
						
						$data_simpan["nama"] = $this->m_karyawan->get_karyawan($data_simpan["no_pokok"])["nama"];
						if(empty($data_simpan["nama"])){
							$data_simpan["nama"] = "";
						}

						$data_simpan["code"] = $code;
						$data_simpan["nama_file"] = $nama_file;
						$data_simpan["waktu_ubah"] = date("Y-m-d H:i:s");
						
						$arr_data_lama = $this->m_karyawan->data_file($data_simpan["no_pokok"]);
						$log_data_lama = "";
						
						foreach($arr_data_lama as $key => $value){
							if(in_array($key,array("no_pokok","nama","nama_file","waktu_ubah"))!=0){
								if(!empty($log_data_lama)){
									$log_data_lama .= "<br>";
								}
								$log_data_lama .= "$key = $value";
								if(strcmp($key,"nama_file")==0){
									$log_data_lama .= "<img src='".base_url($this->data["folder_lihat_biodata"].$value)."'>";
								}
							}
						}
						
						$this->m_foto_karyawan->tambah($data_simpan);
						
						if($this->m_foto_karyawan->cek($data_simpan)){
							$hitung["berhasil"]++;
							
							$arr_data_baru = $this->m_karyawan->data_file($data_simpan["no_pokok"]);
							$log_data_baru = "";
							
							foreach($arr_data_baru as $key => $value){
								if(in_array($key,array("no_pokok","nama","nama_file","waktu_ubah"))!=0){
									if(!empty($log_data_baru)){
										$log_data_baru .= "<br>";
									}
									$log_data_baru .= "$key = $value";
									if(strcmp($key,"nama_file")==0){
										$log_data_baru .= "<img src='".base_url($this->data["folder_lihat_biodata"].$value)."'>";
									}
								}
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
							$hitung["gagal"]++;
						}
						
					}
					else{
						$hitung["gagal"]++;
					}
						
					unlink($this->data["folder_sementara"].$uploaded["file_name"]);
				}
			}
			
			return $hitung;
		}
	}
	
	
	/* End of file foto_karyawan.php */
	/* Location: ./application/controllers/osdm/foto_karyawan.php */