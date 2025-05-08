<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Kelas_perawatan extends CI_Controller {
		public function __construct(){
			parent::__construct();
			
			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'sikesper/master_data/';
			$this->folder_model = 'sikesper/';
			$this->folder_ajax_view = $this->folder_view.'ajax/';
			$this->akses = array();
			
			$this->load->model($this->folder_model."/m_master_data_kelas_perawatan");

			$this->data['success'] = "";
			$this->data['warning'] = "";
			
			$this->data["is_with_sidebar"] = true;
		}
		
		public function index() {
			$this->data['judul'] = "Kelas Perawatan";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."kelas_perawatan";
			
			array_push($this->data['js_sources'],"sikesper/master_data/kelas_perawatan");

			if($this->input->post()){
				if(!strcmp($this->input->post("aksi"),"tambah")){
					izin($this->akses["tambah"]);
				
					$insert['nama_pangkat'] = implode(',', $this->input->post("pangkat"));
					$insert['kelas'] = $this->input->post("kelas");
					$insert['status'] = $this->input->post("status");
					if(!strcmp($this->input->post("status"),"aktif")){
						$insert['status'] = '1';
					} else if(!strcmp($this->input->post("status"),"non aktif")){
						$insert['status'] = '0';
					} else{
                        $insert['status'] = '0';
                    }
					$tambah = $this->tambah($insert);
					
					$this->data['panel_tambah'] = "in";

					if($tambah['status']){
						$this->data['success'] = "Kelas perawatan dengan nama <b>".$insert['kelas']."</b> berhasil ditambahkan.";
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
					
					$insert['nama_pangkat'] = implode(',', $this->input->post("pangkat"));
					$insert['kelas'] = $this->input->post("kelas");
					$insert['status'] = $this->input->post("status");
					if(!strcmp($this->input->post("status"),"aktif")){
						$insert['status'] = '1';
					} else if(!strcmp($this->input->post("status"),"non aktif")){
						$insert['status'] = '0';
					} else{
                        $insert['status'] = '0';
                    }
					$ubah = $this->ubah($insert);
					
					if($ubah["status"]){
						$this->data['success'] = "Perubahan kelas perawatan berhasil dilakukan.";
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
									$('#tabel_kelas_perawatan').DataTable({
										responsive: true
									});
									$('.select2').select2();
								});
							</script>";
				
				array_push($this->data["js_header_script"],$js_header_script);
				
				$this->data["kelas_perawatan"] = $this->m_master_data_kelas_perawatan->kelas_perawatan();
				if (count($this->data["kelas_perawatan"])==0) {
					$this->data["pangkat"] = $this->m_master_data_kelas_perawatan->kelas_perawatan_pangkat();
				}
				else {
					$this->data["pangkat"] = $this->m_master_data_kelas_perawatan->kelas_perawatan_pangkat_new();
				}
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
		
		private function tambah($data){
			$return = array("status" => false, "error_info" => "");
			if($this->m_master_data_kelas_perawatan->cek_tambah_kelas_perawatan($data['kelas'])){
				$data['created'] = date('Y-m-d H:i:s');
				$this->m_master_data_kelas_perawatan->tambah($data);
				$id_ = $this->db->insert_id();
				
				if($this->m_master_data_kelas_perawatan->cek_hasil_kelas_perawatan($data['kelas'], $data['status'], $data['nama_pangkat'])){
					$return["status"] = true;
					$arr_data_insert = $this->m_master_data_kelas_perawatan->kelas_perawatan($id_);
					
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
					$return["error_info"] = "Penambahan Kelas Perawatan <b>Gagal</b> Dilakukan.";
				}
			}
			else{
				$return["status"] = false;
				$return["error_info"] = "Kelas perawatan dengan nama <b>".$data['kelas']."</b> sudah ada.";
			}
			return $return;
		}
	
		//private function ubah($nama,$nama_ubah,$nopol_ubah,$status_ubah){
		private function ubah($data_update){
			$return = array("status" => false, "error_info" => "");
			$arr_data_lama = $this->m_master_data_kelas_perawatan->kelas_perawatan_by_name($data_update['kelas']);

			$cek = $this->m_master_data_kelas_perawatan->cek_ubah_kelas_perawatan($arr_data_lama['id']);
			if($cek["status"]){
				$set = $data_update;
				
				$log_data_lama = "";
				
				foreach($arr_data_lama as $key => $value){
					if(!empty($log_data_lama)){
						$log_data_lama .= "<br>";
					}
					$log_data_lama .= "$key = $value";
				}
				
				$data_update['updated'] = date('Y-m-d H:i:s');
				$this->m_master_data_kelas_perawatan->ubah($data_update, $arr_data_lama['id']);

				if($this->m_master_data_kelas_perawatan->cek_hasil_kelas_perawatan($data_update['kelas'],$data_update['status'],$data_update['nama_pangkat'])){
					$return["status"] = true;
					
					$log_data_baru = "";
					foreach($data_update as $key => $value){
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
					$return["error_info"] = "Perubahan Kelas Perawatan <b>Gagal</b> Dilakukan.";
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