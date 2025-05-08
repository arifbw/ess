<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Lokasi_kerja extends CI_Controller {
		public function __construct(){
			parent::__construct();
			
			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'food_n_go/master_data/';
			$this->folder_model = 'kendaraan/';
			$this->folder_ajax_view = $this->folder_view.'ajax/';
			$this->akses = array();
			
			$this->load->model($this->folder_model."/m_master_data_kendaraan");

			$this->data['success'] = "";
			$this->data['warning'] = "";
			
			$this->data["is_with_sidebar"] = true;
		}
		
		public function index(){
			
			$this->data['judul'] = "Master Data Kendaraan";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."kendaraan";
			
			array_push($this->data['js_sources'],"food_n_go/master_data/kendaraan");

			if($this->input->post()){
				if(!strcmp($this->input->post("aksi"),"tambah")){
					izin($this->akses["tambah"]);
				
					$this->data['nama'] = $this->input->post("nama");
					$this->data['nopol'] = $this->input->post("nopol");
					if(!strcmp($this->input->post("status"),"aktif")){
						$this->data['status'] = true;
					}
					else if(!strcmp($this->input->post("status"),"non aktif")){
						$this->data['status'] = false;
					} else{
                        $this->data['status'] = false;
                    }
					
					$tambah = $this->tambah($this->data['nama'],$this->data['nopol'],$this->data['status']);
					
					$this->data['panel_tambah'] = "in";

					if($tambah['status']){
						$this->data['success'] = "Kendaraan dengan nama <b>".$this->data['nama']."</b> berhasil ditambahkan.";
						$this->data['panel_tambah'] = "";
						$this->data['nama'] = "";
						$this->data['nopol'] = "";
						$this->data['status'] = "";
					}
					else{
						$this->data['warning'] = $tambah['error_info'];
					}
				}
				else if(!strcmp($this->input->post("aksi"),"ubah")){
					izin($this->akses["ubah"]);
					
					$this->data['id_ubah'] = $this->input->post("id_ubah");
					$this->data['nama'] = $this->input->post("nama_old");
					$this->data['nama_ubah'] = $this->input->post("nama_ubah");
					$this->data['nopol'] = $this->input->post("nopol_old");
					$this->data['nopol_ubah'] = $this->input->post("nopol_ubah");
					$this->data['status'] = (bool)$this->input->post("status_old");
					if(!strcmp($this->input->post("status_ubah"),"aktif")){
						$this->data['status_ubah'] = true;
					}
					else if(!strcmp($this->input->post("status_ubah"),"non aktif")){
						$this->data['status_ubah'] = false;
					}
                    
                    $data_update = [
                        'id'=>$this->input->post("id_ubah"),
                        'nama'=>$this->input->post("nama_old"),
                        'nama_ubah'=>$this->input->post("nama_ubah"),
                        'nopol'=>$this->input->post("nopol_old"),
                        'nopol_ubah'=>$this->input->post("nopol_ubah"),
                        'status'=>$this->input->post("status_old"),
                        'status_ubah'=>$this->data['status_ubah'],
                    ];

					//$ubah = $this->ubah($this->data['nama'],$this->data['nama_ubah'],$this->data['nopol_ubah'],$this->data['status_ubah']);
					$ubah = $this->ubah($data_update);
					
					if($ubah["status"]){
						$this->data['success'] = "Perubahan jenis kendaraan berhasil dilakukan.";
					}
					else{
						$this->data['warning'] = $ubah['error_info'];
					}

					$this->data['panel_tambah'] = "";
					$this->data['nama'] = "";
					$this->data['nopol'] = "";
					$this->data['status'] = "";
				}
				else{
					$this->data['panel_tambah'] = "";
					$this->data['nama'] = "";
					$this->data['nopol'] = "";
					$this->data['status'] = "";
				}
			}
			else{
				$this->data['panel_tambah'] = "";
				$this->data['nama'] = "";
				$this->data['nopol'] = "";
				$this->data['status'] = "";
			}
			
			if($this->akses["lihat"]){
				$js_header_script = "<script>
								$(document).ready(function() {
									$('#tabel_jenis_kendaraan').DataTable({
										responsive: true
									});
								});
							</script>";
				
				array_push($this->data["js_header_script"],$js_header_script);
				
				$this->data["daftar_jenis_kendaraan"] = $this->m_master_data_kendaraan->daftar_jenis_kendaraan();
				
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
		
		private function tambah($nama,$nopol,$status){
			$return = array("status" => false, "error_info" => "");
			if($this->m_master_data_kendaraan->cek_tambah_jenis_kendaraan($nama)){
				$data = array(
							"nama" => $nama,
							"nopol" => $nopol,
							"status" => $status
						);
				$this->m_master_data_kendaraan->tambah($data);
				
				if($this->m_master_data_kendaraan->cek_hasil_jenis_kendaraan($nama,$nopol,$status)){
					$return["status"] = true;
					
					$arr_data_insert = $this->m_master_data_kendaraan->data_jenis_kendaraan($nama);
					
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
					$return["error_info"] = "Penambahan Jenis Kendaraan <b>Gagal</b> Dilakukan.";
				}
			}
			else{
				$return["status"] = false;
				$return["error_info"] = "Jenis Kendaraan dengan nama <b>$nama</b> sudah ada.";
			}
			return $return;
		}
	
		//private function ubah($nama,$nama_ubah,$nopol_ubah,$status_ubah){
		private function ubah($data_update){
			$return = array("status" => false, "error_info" => "");
			//$cek = $this->m_master_data_kendaraan->cek_ubah_jenis_kendaraan($nama,$nama_ubah);
			$cek = $this->m_master_data_kendaraan->cek_ubah_jenis_kendaraan($data_update);
			if($cek["status"]){
				$set = array("nama" => $data_update['nama_ubah'], "nopol"=>$data_update['nopol_ubah'], "status"=>$data_update['status_ubah']);
				
				$arr_data_lama = $this->m_master_data_kendaraan->data_jenis_kendaraan_new($data_update['id']);
				$log_data_lama = "";
				
				foreach($arr_data_lama as $key => $value){
					if(!empty($log_data_lama)){
						$log_data_lama .= "<br>";
					}
					$log_data_lama .= "$key = $value";
				}
				
				$this->m_master_data_kendaraan->ubah($set,$data_update['id']);

				if($this->m_master_data_kendaraan->cek_hasil_jenis_kendaraan($data_update['nama_ubah'],$data_update['nopol_ubah'],$data_update['status_ubah'])){
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
					$return["error_info"] = "Perubahan Jenis Kendaraan <b>Gagal</b> Dilakukan.";
				}
			}
			else{
				$return["status"] = $cek["status"];
				$return["error_info"] = $cek["error_info"];	
			}

			return $return;
		}
        
        function update_data(){
            echo json_encode($this->input->post());
        }
	}
	
	/* End of file jenis_cuti.php */
	/* Location: ./application/controllers/master_data/jenis_cuti.php */