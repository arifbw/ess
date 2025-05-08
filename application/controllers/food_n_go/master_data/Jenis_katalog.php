<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Jenis_katalog extends CI_Controller {
		public function __construct(){
			parent::__construct();
			
			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'food_n_go/katalog/';
			$this->folder_model = 'konsumsi/';
			$this->folder_ajax_view = $this->folder_view.'ajax/';
			$this->akses = array();
			
			$this->load->model($this->folder_model."/m_katalog");

			$this->data['success'] = "";
			$this->data['warning'] = "";
			
			$this->data["is_with_sidebar"] = true;
		}
		
		public function index(){
			
			$this->data['judul'] = "Katalog Konsumsi Rapat";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."jenis_katalog";
			
			array_push($this->data['js_sources'],"");

			if($this->input->post()){
				if(!strcmp($this->input->post("aksi"),"tambah")){
					izin($this->akses["tambah"]);
				
					$this->data['nama'] = $this->input->post("nama");
					$this->data['jenis'] = $this->input->post("jenis");
					$this->data['harga'] = $this->input->post("harga");
					$this->data['id_penyedia'] = $this->input->post("id_penyedia");

					if(!strcmp($this->input->post("status"),"aktif")){
						$this->data['status'] = true;
					}
					else if(!strcmp($this->input->post("status"),"non aktif")){
						$this->data['status'] = false;
					} else{
                        $this->data['status'] = false;
                    }
					
					$tambah = $this->tambah($this->data);
					
					$this->data['panel_tambah'] = "in";

					if($tambah['status']){
						$this->data['success'] = "Katalog <b>".$this->data['nama']."</b> berhasil ditambahkan.";
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
					
					$this->data['nama'] = $this->input->post("nama");
					$this->data['jenis'] = $this->input->post("jenis");
					$this->data['harga'] = $this->input->post("harga");
					$this->data['id_penyedia'] = $this->input->post("id_penyedia");

					if(!strcmp($this->input->post("status"),"aktif")){
						$this->data['status'] = 1;
					}
					else if(!strcmp($this->input->post("status"),"non aktif")){
						$this->data['status'] = 0;
					}
                    
                    $data_update = [
                        'id'=>$this->input->post("id"),
                        'nama'=>$this->input->post("nama"),
                        'jenis'=>$this->input->post("jenis"),
                        'harga'=>$this->input->post("harga"),
                        'id_penyedia'=>$this->input->post("id_penyedia"),
                        'status'=>$this->data['status'],
                    ];
                    
					$ubah = $this->ubah($data_update);
					
					if($ubah["status"]){
						$this->data['success'] = "Perubahan Katalog Konsumsi Rapat berhasil dilakukan.";
					}
					else{
						$this->data['warning'] = $ubah['error_info'];
					}

					$this->data['panel_tambah'] = "";
					$this->data['status'] = "";
				}
				else{
					$this->data['panel_tambah'] = "";
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
				$js_header_script = "
							<script src='".base_url('asset/select2')."/select2.min.js'></script>
							<script>
								$(document).ready(function() {
									$('#tabel_jenis_katalog').DataTable({
										responsive: true
									});
								});
							</script>";
				
				array_push($this->data["js_header_script"],$js_header_script);
				
				$this->data['daftar_katalog'] = $this->m_katalog->daftar_katalog();
				$this->data['penyedia'] = $this->m_katalog->daftar_penyedia();

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
			
			$makanan['id_penyedia'] = $data['id_penyedia'];
			$makanan['nama'] = $data['nama'];
			$makanan['harga'] = $data['harga'];
			$makanan['jenis'] = $data['jenis'];
			$makanan['status'] = $data['status'];

			$tambah = $this->m_katalog->tambah($makanan);
			
			$jenis_katalog = $this->m_katalog->detail_katalog($tambah);

			if($jenis_katalog){
				$return["status"] = true;
				
				$log_data_baru = "";
				
				foreach($jenis_katalog as $key => $value){
					if(!empty($log_data_baru)){
						$log_data_baru .= "<br>";
					}
					$log_data_baru .= "$key = $value";
				}
				
				$log = array(
					"id_pengguna" => $this->session->userdata("id_pengguna"),
					"id_modul" => $this->data['id_modul'],
					"id_target" => $jenis_katalog->id,
					"deskripsi" => "tambah ".strtolower(preg_replace("/_/"," ",__CLASS__)),
					"kondisi_baru" => $log_data_baru,
					"alamat_ip" => $this->data["ip_address"],
					"waktu" => date("Y-m-d H:i:s")
				);
				$this->m_log->tambah($log);
			}
			else{
				$return["status"] = false;
				$return["error_info"] = "Penambahan Katalog Konsumsi Rapat <b>Gagal</b> Dilakukan.";
			}

			return $return;
		}
	
		private function ubah($data_update){
			$return = array("status" => false, "error_info" => "");

			$cek = $this->m_katalog->detail_katalog($data_update['id']);
			if($cek){

				$set_makanan = array(
					'id_penyedia' => $data_update['id_penyedia'],
					'nama' => $data_update['nama'],
					'jenis' => $data_update['jenis'],
					'harga' => $data_update['harga'],
					'status' => $data_update['status']
				);
				
				$arr_data_lama = $cek;
				$log_data_lama = "";
				
				foreach($arr_data_lama as $key => $value){
					if(!empty($log_data_lama)){
						$log_data_lama .= "<br>";
					}
					$log_data_lama .= "$key = $value";
				}

				if($this->m_katalog->ubah('mst_jenis_katalog', $set_makanan, $cek->id)){

					$return["status"] = true;
					
					$log_data_baru = "";
					foreach($set_makanan as $key => $value){
						if(!empty($log_data_baru)){
							$log_data_baru .= "<br>";
						}
						$log_data_baru .= "$key = $value";
					}
					
					$log = array(
						"id_pengguna" => $this->session->userdata("id_pengguna"),
						"id_modul" => $this->data['id_modul'],
						"id_target" => $cek->id,
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

		public function detail($id)
		{
			header('Content-Type: application/json');
        	header("Access-Control-Allow-Origin: *");

			$data = $this->m_katalog->detail_katalog($id);

			if($data){
				echo json_encode(['status' => 'success', 'result' => [
					'id' => $id,
					'nama' => $data->nama,
					'harga' => $data->harga,
					'jenis' => $data->jenis,
					'penyedia' => $data->id_penyedia,
					'nama_penyedia' => $data->nama_penyedia,
					'lokasi' => $data->lokasi,
					'nama_lokasi' => $data->nama_lokasi,
					'status' => $data->status
				]]);
			}else{
				echo json_encode(['status' => 'failed', 'result' => null]);
			}
		}

		public function daftar_penyedia($lokasi)
		{
			header('Content-Type: application/json');
        	header("Access-Control-Allow-Origin: *");

        	if($lokasi != ''){
				$data = $this->m_katalog->daftar_penyedia($lokasi);

				echo json_encode(['results' => $data, 'status' => 200]);
			}else{
				echo json_encode(['results' => NULL, 'status' => 404]);
			}
		}

		public function daftar_lokasi()
		{
			header('Content-Type: application/json');
        	header("Access-Control-Allow-Origin: *");

			$data = $this->m_katalog->daftar_lokasi();

			echo json_encode(['results' => $data, 'status' => 200]);
		}
        
        function update_data(){
            echo json_encode($this->input->post());
        }
	}
	
	/* End of file jenis_cuti.php */
	/* Location: ./application/controllers/master_data/jenis_cuti.php */