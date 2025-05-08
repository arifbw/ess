<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Daftar_gedung extends CI_Controller {
		public function __construct(){
			parent::__construct();
			
			$meta = meta_data();
			
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'food_n_go/master_data/';
			$this->folder_model = 'konsumsi/';
			$this->folder_ajax_view = $this->folder_view.'ajax/';
			$this->akses = array();
			
			$this->load->model($this->folder_model."/M_daftar_gedung");

			$this->data['success'] = "";
			$this->data['warning'] = "";
			
			$this->data["is_with_sidebar"] = true;
		}

		public function index(){
			$this->data['judul'] = "Daftar Gedung";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);

			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."daftar_gedung";
			
			array_push($this->data['js_sources'],"sikesper/daftar_obat");

			if($this->input->post()){

				if(!strcmp($this->input->post("aksi"),"tambah")){
					izin($this->akses["tambah"]);

					if(!strcmp($this->input->post("status"),"1")) {
						$this->data['status'] = true;
					}
					else if(!strcmp($this->input->post("status"),"0")){
						$this->data['status'] = false;
					} else{
                        $this->data['status'] = false;
                    }

                    $insert['nama'] = $this->input->post('nama');
                    $insert['lokasi'] = $this->input->post('lokasi');
                    $insert['status'] = $this->input->post('status');


					$tambah = $this->tambah($insert, $this->data['status']);
					
					$this->data['panel_tambah'] = "in";

					if($tambah['status']){
						$this->data['success'] = "Lokasi <b>".$insert['nama']."</b> berhasil ditambahkan.";
						$this->data['panel_tambah'] = "";
						$this->data['nama'] = "";
						$this->data['status'] = "";
					}
					else{
						$this->data['warning'] = $tambah['error_info'];
					}
				}
				else if(!strcmp($this->input->post("aksi"),"ubah")){
					izin($this->akses["ubah"]);
					
					$this->data['id_ubah'] = $this->input->post("id");
					$this->data['nama_ubah'] = $this->input->post("nama");
					$this->data['lokasi_ubah'] = $this->input->post("lokasi");
					$this->data['status'] = (bool)$this->input->post("status_lama");

					if(!strcmp($this->input->post("status"),"1")){
						$this->data['status_ubah'] = true;
					}
					else if(!strcmp($this->input->post("status"),"0")){
						$this->data['status_ubah'] = false;
					}
                    
                    $data_update = $this->input->post();
					$ubah = $this->ubah($data_update);
					
					if($ubah["status"]){
						$this->data['success'] = "Perubahan Gedung Berhasil Dilakukan.";
					}
					else{
						$this->data['warning'] = $ubah['error_info'];
					}

					$this->data['panel_tambah'] = "";
					$this->data['nama'] = "";
					$this->data['status'] = "";
				}
				else{
					$this->data['panel_tambah'] = "";
					$this->data['nama'] = "";
					$this->data['status'] = "";
				}
			}
			else{
				$this->data['panel_tambah'] = "";
				$this->data['nama'] = "";
				$this->data['status'] = "";
			}
			
			if($this->akses["lihat"]){
				$js_header_script = "
							<script src='".base_url('asset/select2')."/select2.min.js'></script>
							<script>
								$(document).ready(function() {
									$('#tabel_daftar_gedung').DataTable({
										responsive: true
									});
								});
							</script>";
				
				array_push($this->data["js_header_script"],$js_header_script);
				
				$this->data["daftar_gedung"] = $this->M_daftar_gedung->daftar_gedung();
				$this->data["daftar_lokasi"] = $this->M_daftar_gedung->daftar_lokasi();
				
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

		private function tambah($data)
		{
			$return = array("status" => false, "error_info" => "");

			$data['created'] = date('Y-m-d H:i:s');
			$this->M_daftar_gedung->insert($data);
			$id_ = $this->db->insert_id();

			if($this->M_daftar_gedung->cek_daftar_gedung($id_)){
				$return["status"] = true;

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
					"id_target" => $id_,
					"deskripsi" => "tambah ".strtolower(preg_replace("/_/"," ",__CLASS__)),
					"kondisi_baru" => $log_data_baru,
					"alamat_ip" => $this->data["ip_address"],
					"waktu" => date("Y-m-d H:i:s")
				);

				$this->m_log->tambah($log);
			}else{
				$return["status"] = false;
				$return["error_info"] = "Penambahan Gedung <b>Gagal</b> Dilakukan.";
			}

			return $return;
		}

		private function ubah($data_update){
			$return = array("status" => false, "error_info" => "");

			$cek = $this->M_daftar_gedung->cek_daftar_gedung($data_update['id']);
			if($cek){
				$set = array(
					"nama" => $data_update['nama'],
					"lokasi" => $data_update['lokasi'],
					"status"=>$data_update['status']
				);
				
				$arr_data_lama = $cek;
				$log_data_lama = "";
				
				foreach($arr_data_lama as $key => $value){
					if(!empty($log_data_lama)){
						$log_data_lama .= "<br>";
					}
					$log_data_lama .= "$key = $value";
				}
				
				$set['updated'] = date('Y-m-d H:i:s');
				$update = $this->M_daftar_gedung->update($set, $data_update['id']);

				if($update){
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
						"id_target" => $arr_data_lama->id,
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
					$return["error_info"] = "Perubahan Lokasi <b>Gagal</b> Dilakukan.";
				}
			}
			else{
				$return["status"] = $cek["status"];
				$return["error_info"] = $cek["error_info"];	
			}

			return $return;
		}

		public function detail($id)
		{
			header('Content-Type: application/json');
        	header("Access-Control-Allow-Origin: *");

			$data = $this->M_daftar_gedung->cek_daftar_gedung($id);

			if($data){
				echo json_encode(['status' => 'success', 'result' => [
					'id' => $id,
					'nama' => $data->nama,
					'lokasi' => $data->lokasi,
					'status' => $data->status
				]]);
			}else{
				echo json_encode(['status' => 'failed', 'result' => null]);
			}
		}
	}
?>