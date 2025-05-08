<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Daftar_ruangan extends CI_Controller {
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
			
			$this->load->model($this->folder_model."/m_master_data_ruangan");

			$this->data['success'] = "";
			$this->data['warning'] = "";
			
			$this->data["is_with_sidebar"] = true;
		}
		
		public function index(){
			
			$this->data['judul'] = "Daftar Ruangan";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."ruangan";
			
			array_push($this->data['js_sources'],"food_n_go/master_data/daftar_ruangan");

			if($this->input->post()){
				if(!strcmp($this->input->post("aksi"),"tambah")){
					izin($this->akses["tambah"]);
				
					$this->data['nama'] = $this->input->post("nama");
					$this->data['id_gedung'] = $this->input->post("id_gedung");

					if(!strcmp($this->input->post("status"),"aktif")){
						$this->data['status'] = true;
					}
					else if(!strcmp($this->input->post("status"),"non aktif")){
						$this->data['status'] = false;
					} else{
                        $this->data['status'] = false;
                    }

                    $data_tambah = array(
                    	'id_gedung' => $this->input->post("id_gedung"),
                    	'nama' => $this->input->post("nama"),
                    	'status' => $this->data['status']
                    );
					
					$tambah = $this->tambah($data_tambah);
					
					$this->data['panel_tambah'] = "in";

					if($tambah['status']){
						$this->data['success'] = "Ruangan dengan nama <b>".$this->data['nama']."</b> berhasil ditambahkan.";
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
					$this->data['nama'] = $this->input->post("nama_old");
					$this->data['nama_ubah'] = $this->input->post("nama_ubah");
					$this->data['kapasitas'] = $this->input->post("kapasitas_old"); # heru menambahkan ini, 2020-11-25 @08:46
					$this->data['kapasitas_ubah'] = $this->input->post("kapasitas_ubah"); # heru menambahkan ini, 2020-11-25 @08:46
					$this->data['id_gedung'] = $this->input->post("id_gedung_old");
					$this->data['id_gedung_ubah'] = $this->input->post("id_gedung");
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
                        'kapasitas'=>$this->input->post("kapasitas_old"), # heru menambahkan ini, 2020-11-25 @08:46
                        'kapasitas_ubah'=>$this->input->post("kapasitas_ubah"), # heru menambahkan ini, 2020-11-25 @08:46
                        'status'=>$this->input->post("status_old"),
                        'status_ubah'=>$this->data['status_ubah'],
                        'id_gedung' => $this->data['id_gedung'],
						'id_gedung_ubah' => $this->data['id_gedung_ubah']
                    ];

					$ubah = $this->ubah($data_update);
					
					if($ubah["status"]){
						$this->data['success'] = "Perubahan Jenis Ruangan Berhasil Dilakukan.";
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
									$('#tabel_daftar_ruangan').DataTable({
										responsive: true
									});
								});
							</script>";
				
				array_push($this->data["js_header_script"],$js_header_script);
				
				$this->data["daftar_ruangan"] = $this->m_master_data_ruangan->daftar_ruangan();
				
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
			if($this->m_master_data_ruangan->cek_tambah_daftar_ruangan($data['nama'])){

				$this->m_master_data_ruangan->tambah($data);
				
				if($this->m_master_data_ruangan->cek_hasil_daftar_ruangan($data['nama'],$data['status'])){
					$return["status"] = true;
					
					$arr_data_insert = $this->m_master_data_ruangan->daftar_ruangan($data['nama']);
					
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
					$return["error_info"] = "Penambahan Jenis Ruangan <b>Gagal</b> Dilakukan.";
				}
			}
			else{
				$return["status"] = false;
				$return["error_info"] = "Jenis Ruangan dengan nama <b>".$data['nama']."</b> sudah ada.";
			}
			return $return;
		}
	
		//private function ubah($nama,$nama_ubah,$nopol_ubah,$status_ubah){
		private function ubah($data_update){
			$return = array("status" => false, "error_info" => "");
			//$cek = $this->m_master_data_ruangan->cek_ubah_daftar_ruangan($nama,$nama_ubah);
			$cek = $this->m_master_data_ruangan->cek_ubah_daftar_ruangan($data_update);
			if($cek["status"]){
				$set = array("nama" => $data_update['nama_ubah'], "kapasitas" => $data_update['kapasitas_ubah'], "id_gedung" => $data_update['id_gedung_ubah'], "status"=>$data_update['status_ubah']); # heru menambahkan "kapasitas" 2020-11-25 @09:00
				
				$arr_data_lama = $this->m_master_data_ruangan->daftar_ruangan_new($data_update['id']);
				$log_data_lama = "";
				
				foreach($arr_data_lama as $key => $value){
					if(!empty($log_data_lama)){
						$log_data_lama .= "<br>";
					}
					$log_data_lama .= "$key = $value";
				}
				
				$this->m_master_data_ruangan->ubah($set,$data_update['id']);

				if($this->m_master_data_ruangan->cek_hasil_daftar_ruangan($data_update['nama_ubah'],$data_update['status_ubah'])){
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
					$return["error_info"] = "Perubahan Jenis Ruangan <b>Gagal</b> Dilakukan.";
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

			$data = $this->m_master_data_ruangan->cek_daftar_ruangan($id);

			if($data){
				echo json_encode(['status' => 'success', 'result' => [
					'id' => $id,
					'nama' => $data->nama,
					'kapasitas' => $data->kapasitas, # heru menambahkan ini, 2020-11-25 @09:00
					'gedung' => $data->gedung,
					'nama_gedung' => $data->nama_gedung,
					'lokasi' => $data->lokasi,
					'nama_lokasi' => $data->nama_lokasi,
					'status' => $data->status
				]]);
			}else{
				echo json_encode(['status' => 'failed', 'result' => null]);
			}
		}

		public function daftar_lokasi()
		{
			header('Content-Type: application/json');
	    	header("Access-Control-Allow-Origin: *");

			$data = $this->m_master_data_ruangan->daftar_lokasi();

			echo json_encode(['results' => $data, 'status' => 200]);
		}

		public function daftar_gedung($lokasi)
		{
			header('Content-Type: application/json');
        	header("Access-Control-Allow-Origin: *");

        	if($lokasi != ''){
				$data = $this->m_master_data_ruangan->daftar_gedung($lokasi);

				echo json_encode(['results' => $data, 'status' => 200]);
			}else{
				echo json_encode(['results' => NULL, 'status' => 404]);
			}
		}
        
        function update_data(){
            echo json_encode($this->input->post());
        }
	}
	
	/* End of file jenis_cuti.php */
	/* Location: ./application/controllers/master_data/jenis_cuti.php */