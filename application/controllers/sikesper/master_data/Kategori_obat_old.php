<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Kategori_obat extends CI_Controller {
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
			
			$this->load->model($this->folder_model."/m_master_data_kategori_obat");

			$this->data['success'] = "";
			$this->data['warning'] = "";
			
			$this->data["is_with_sidebar"] = true;
		}
		
		public function index() {
			$this->data['judul'] = "Kategori Obat";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."kategori_obat";
			
			array_push($this->data['js_sources'],"sikesper/master_data/kategori_obat");

			$this->data["parent_kategori"] = $this->m_master_data_kategori_obat->kategori_obat_parent();
			if($this->input->post()){
				if(!strcmp($this->input->post("aksi"),"tambah")){
					izin($this->akses["tambah"]);
				
					$insert['no_kode'] = $this->input->post("no_kode");
					$insert['nama_kategori'] = $this->input->post("nama_kategori");
					$insert['jenis'] = $this->input->post("jenis");
					$insert['id_parent'] = $this->input->post("id_parent");
					$insert['status'] = $this->input->post("status");
					if(!strcmp($this->input->post("status"),"aktif")){
						$this->data['status'] = true;
					}
					else if(!strcmp($this->input->post("status"),"non aktif")){
						$this->data['status'] = false;
					} else{
                        $this->data['status'] = false;
                    }
					$tambah = $this->tambah($insert, $this->data['status']);
					
					$this->data['panel_tambah'] = "in";

					if($tambah['status']){
						$this->data['success'] = "Kategori obat dengan nama <b>".$insert['nama_kategori']."</b> berhasil ditambahkan.";
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
					
					$this->data['id_ubah'] = $this->input->post("id_ubah");
					$this->data['no_kode_ubah'] = $this->input->post("no_kode_ubah");
					$this->data['jenis_ubah'] = $this->input->post("jenis_ubah");
					$this->data['id_parent_ubah'] = $this->input->post("id_parent_ubah");
					$this->data['nama_ubah'] = $this->input->post("nama_ubah");
					$this->data['status'] = (bool)$this->input->post("status_old");
					if(!strcmp($this->input->post("status_ubah"),"aktif")){
						$this->data['status_ubah'] = true;
					}
					else if(!strcmp($this->input->post("status_ubah"),"non aktif")){
						$this->data['status_ubah'] = false;
					}
                    
                    $data_update = $this->input->post();
					$ubah = $this->ubah($data_update);
					
					if($ubah["status"]){
						$this->data['success'] = "Perubahan kategori obat berhasil dilakukan.";
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
									$('#tabel_kategori_obat').DataTable({
										responsive: true
									});
									$('.select2').select2();
								});
							</script>";
				
				array_push($this->data["js_header_script"],$js_header_script);
				
				$this->data["kategori_obat"] = $this->m_master_data_kategori_obat->kategori_obat();
				
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
			if($this->m_master_data_kategori_obat->cek_tambah_kategori_obat($data['nama_kategori'], $data['jenis'])){
				$data['created'] = date('Y-m-d H:i:s');
				$this->m_master_data_kategori_obat->tambah($data);
				$id_ = $this->db->insert_id();
				
				if($this->m_master_data_kategori_obat->cek_hasil_kategori_obat($data['nama_kategori'], $data['status'], $data['jenis'])){
					$return["status"] = true;
					$arr_data_insert = $this->m_master_data_kategori_obat->kategori_obat($id_);
					
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
					$return["error_info"] = "Penambahan Kategori Obat <b>Gagal</b> Dilakukan.";
				}
			}
			else{
				$return["status"] = false;
				$return["error_info"] = "Kategori obat dengan nama <b>".$data['nama_kategori']."</b> dan jenis <b>".$data['jenis']."</b> sudah ada.";
			}
			return $return;
		}
	
		//private function ubah($nama,$nama_ubah,$nopol_ubah,$status_ubah){
		private function ubah($data_update){
			$return = array("status" => false, "error_info" => "");
			//$cek = $this->m_master_data_kategori_obat->cek_ubah_kategori_obat($nama,$nama_ubah);
			$cek = $this->m_master_data_kategori_obat->cek_ubah_kategori_obat($data_update);
			if($cek["status"]){
				$set = array(
					"nama_kategori" => $data_update['nama_kategori_ubah'],
					"no_kode" => $data_update['no_kode_ubah'],
					"jenis" => $data_update['jenis_ubah'],
					"id_parent" => $data_update['id_parent_ubah'],
					"status"=>$data_update['status_ubah']
				);
				
				$arr_data_lama = $this->m_master_data_kategori_obat->kategori_obat($data_update['id_ubah']);
				$log_data_lama = "";
				
				foreach($arr_data_lama as $key => $value){
					if(!empty($log_data_lama)){
						$log_data_lama .= "<br>";
					}
					$log_data_lama .= "$key = $value";
				}
				
				$set['updated'] = date('Y-m-d H:i:s');
				$this->m_master_data_kategori_obat->ubah($set, $data_update['id_ubah']);

				if($this->m_master_data_kategori_obat->cek_hasil_kategori_obat($data_update['nama_kategori_ubah'],$data_update['status_ubah'],$data_update['jenis_ubah'])){
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
					$return["error_info"] = "Perubahan Kategori Obat <b>Gagal</b> Dilakukan.";
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