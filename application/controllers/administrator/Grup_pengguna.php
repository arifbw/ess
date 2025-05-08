<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Grup_pengguna extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		$meta = meta_data();
		foreach ($meta as $key => $value) {
			$this->data[$key] = $value;
		}

		$this->folder_view = 'administrator/';
		$this->folder_model = 'administrator/';
		$this->folder_ajax_view = $this->folder_view . 'ajax/';
		$this->akses = array();

		$this->load->model($this->folder_model . "m_grup_pengguna");
		$this->load->model("m_keycloak"); //

			$this->data['success'] = "";
			$this->data['warning'] = "";
			
			$this->data["is_with_sidebar"] = true;
		}
		
		public function index(){
			$this->data['judul'] = "Grup Pengguna";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."grup_pengguna";
			
			array_push($this->data['js_sources'],"administrator/grup_pengguna");

			if($this->input->post()){
				if(!strcmp($this->input->post("aksi"),"tambah")){
					izin($this->akses["tambah"]);
					
					$this->data['nama'] = $this->input->post("nama");
					if(!strcmp($this->input->post("status"),"aktif")){
						izin($this->akses["tambah"]);
						
						$this->data['status'] = "1";
					}
					else if(!strcmp($this->input->post("status"),"non aktif")){
						$this->data['status'] = "0";
					}
					
					$tambah = $this->tambah($this->data['nama'],$this->data['status']);
					
					$this->data['panel_tambah'] = "in";

					if($tambah['status']){
						$this->data['success'] = "Grup Pengguna dengan nama <b>".$this->data['nama']."</b> berhasil ditambahkan.";
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
					
					$this->data['nama'] = $this->input->post("nama");
					$this->data['nama_ubah'] = $this->input->post("nama_ubah");
					
					if(!strcmp($this->input->post("status"),"aktif")){
						$this->data['status'] = "1";
					}
					else if(!strcmp($this->input->post("status"),"non aktif")){
						$this->data['status'] = "0";
					}
					
					if(!strcmp($this->input->post("status_ubah"),"aktif")){
						$this->data['status_ubah'] = "1";
					}
					else if(!strcmp($this->input->post("status_ubah"),"non aktif")){
						$this->data['status_ubah'] = "0";
					}

					$ubah = $this->ubah($this->data["nama"],$this->data["status"],$this->data["nama_ubah"],$this->data["status_ubah"]);

					if($ubah["status"]){
						$this->data['success'] = "Perubahan Grup Pengguna berhasil dilakukan.";
					}
					else{
						$this->data['warning'] = $ubah['error_info'];
					}

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
									$('#tabel_grup_pengguna').DataTable({
										responsive: true
									});
								});
							</script>";

			array_push($this->data["js_header_script"], $js_header_script);

			$this->data["daftar_grup_pengguna"] = $this->m_grup_pengguna->daftar_grup_pengguna();

			$log = array(
				"id_pengguna" => $this->session->userdata("id_pengguna"),
				"id_modul" => $this->data['id_modul'],
				"deskripsi" => "lihat " . strtolower(preg_replace("/_/", " ", __CLASS__)),
				"alamat_ip" => $this->data["ip_address"],
				"waktu" => date("Y-m-d H:i:s")
			);
			$this->m_log->tambah($log);
		}

		if ($this->akses["menu"]) {
			$this->data["url_menu"] = $this->m_setting->ambil_url_modul("Menu Grup Pengguna");
		}

		if ($this->akses["hak akses"]) {
			$this->data["url_hak_akses"] = $this->m_setting->ambil_url_modul("Hak Akses Grup Pengguna");
		}

		$this->load->view('template', $this->data);
	}

	public function daftar_pengguna($nama_grup_pengguna)
	{
		$nama_grup_pengguna = urldecode($nama_grup_pengguna);
		$grup_pengguna = $this->m_grup_pengguna->data_grup_pengguna($nama_grup_pengguna);
		$this->data["daftar_pengguna"] = $this->m_grup_pengguna->daftar_pengguna($grup_pengguna["id"]);

		$this->load->view("administrator/daftar_pengguna", $this->data);
	}

	private function tambah($nama, $status)
	{
		$return = array("status" => false, "error_info" => "");
		if ($this->m_grup_pengguna->cek_tambah_grup_pengguna($nama)) {
			$data = array(
				"nama" => $nama,
				"status" => $status
			);

			$this->m_grup_pengguna->tambah($data);

			if ($this->m_grup_pengguna->cek_hasil_grup_pengguna($nama, $status)) {
				$return["status"] = true;

				$arr_data_insert = $this->m_grup_pengguna->data_grup_pengguna($nama);

				// tambah otoritas sycn keycloak
				$this->m_keycloak->createOtoritas($arr_data_insert);

				$log_data_baru = "";

				foreach ($data as $key => $value) {
					if (!empty($log_data_baru)) {
						$log_data_baru .= "<br>";
					}
					$log_data_baru .= "$key = $value";
				}

				$log = array(
					"id_pengguna" => $this->session->userdata("id_pengguna"),
					"id_modul" => $this->data['id_modul'],
					"id_target" => $arr_data_insert['id'],
					"deskripsi" => "tambah " . strtolower(preg_replace("/_/", " ", __CLASS__)),
					"kondisi_baru" => $log_data_baru,
					"alamat_ip" => $this->data["ip_address"],
					"waktu" => date("Y-m-d H:i:s")
				);
				$this->m_log->tambah($log);
			} else {
				$return["status"] = false;
				$return["error_info"] = "Penambahan Grup Pengguna <b>Gagal</b> Dilakukan.";
			}
		} else {
			$return["status"] = false;
			$return["error_info"] = "Grup Pengguna dengan nama <b>$nama</b> sudah ada.";
		}
		return $return;
	}

	private function ubah($nama, $status, $nama_ubah, $status_ubah)
	{
		$return = array("status" => false, "error_info" => "");
		$cek = $this->m_grup_pengguna->cek_ubah_grup_pengguna($nama, $status, $nama_ubah);
		if ($cek["status"]) {
			$set = array('nama' => $nama_ubah, 'status' => $status_ubah);
			$arr_data_lama = $this->m_grup_pengguna->data_grup_pengguna($nama);
			$log_data_lama = "";

			foreach ($arr_data_lama as $key => $value) {
				if (strcmp($key, "id") != 0) {
					if (!empty($log_data_lama)) {
						$log_data_lama .= "<br>";
					}
					$log_data_lama .= "$key = $value";
				}
			}

			$this->m_grup_pengguna->ubah($set, $nama, $status);

			if ($this->m_grup_pengguna->cek_hasil_grup_pengguna($nama_ubah, $status_ubah)) {
				$return["status"] = true;

				$log_data_baru = "";
				foreach ($set as $key => $value) {
					if (!empty($log_data_baru)) {
						$log_data_baru .= "<br>";
					}
					$log_data_baru .= "$key = $value";
				}

				$keycloak = [
					'set' => $set,
					'data' => $arr_data_lama
				];

				$this->m_keycloak->editOtoritas($keycloak);

				$log = array(
					"id_pengguna" => $this->session->userdata("id_pengguna"),
					"id_modul" => $this->data['id_modul'],
					"id_target" => $arr_data_lama["id"],
					"deskripsi" => "ubah " . strtolower(preg_replace("/_/", " ", __CLASS__)),
					"kondisi_lama" => $log_data_lama,
					"kondisi_baru" => $log_data_baru,
					"alamat_ip" => $this->data["ip_address"],
					"waktu" => date("Y-m-d H:i:s")
				);
				$this->m_log->tambah($log);
			} else {
				$return["status"] = false;
				$return["error_info"] = "Perubahan Grup Pengguna <b>Gagal</b> Dilakukan.";
			}
		} else {
			$return["status"] = $cek["status"];
			$return["error_info"] = $cek["error_info"];
		}

			return $return;
		}
		
		public function menu($nama_grup_pengguna){
			$nama_grup_pengguna = urldecode($nama_grup_pengguna);
			$this->data['judul'] = "Menu Grup Pengguna";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);
			array_push($this->data['js_sources'],"administrator/menu_grup_pengguna");
			
			$this->data["akses"] = $this->akses;
			$this->data['judul'] .= " : ".$nama_grup_pengguna;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."menu_grup_pengguna";
			
			$this->load->model($this->folder_model."m_master_menu");
			$this->load->model($this->folder_model."m_menu_grup_pengguna");
			
			if($this->input->post()){
				izin($this->akses["ubah"]);
				
				$id_grup_pengguna = $this->input->post("id_grup_pengguna");
				$arr_id_posisi_menu = $this->input->post("id_posisi_menu");
				$arr_id_master_menu = $this->input->post("id_master_menu");
				
				for($i=0;$i<count($arr_id_posisi_menu);$i++){
					$hasil = $this->simpan_menu_grup_pengguna($id_grup_pengguna,$arr_id_posisi_menu[$i],$arr_id_master_menu[$i]);
					
					if($hasil["status"]){
						if(!empty($this->data['success'])){
							$this->data['success'] .= "<br>";
						}
						$this->data['success'] .= $hasil["success_info"];
					}
					else{
						if(!empty($this->data['warning'])){
							$this->data['warning'] .= "<br>";
						}
						$this->data['warning'] .= $hasil['error_info'];
					}
				}
			}
			
			if($this->akses["lihat"]){
				$this->data["id_grup_pengguna"] = $this->m_grup_pengguna->ambil_id_grup_pengguna($nama_grup_pengguna);
				
				$this->data["daftar_menu_grup_pengguna"] = $this->m_menu_grup_pengguna->daftar_menu_grup_pengguna($this->data["id_grup_pengguna"]);
				$this->data["daftar_master_menu"] = $this->m_master_menu->daftar_master_menu();
				
				$this->data["url_grup_pengguna"] = $this->m_setting->ambil_url_modul("Grup Pengguna");
				
				$log = array(
						"id_pengguna" => $this->session->userdata("id_pengguna"),
						"id_modul" => $this->data['id_modul'],
						"deskripsi" => "lihat ".strtolower($this->data['judul']),
						"alamat_ip" => $this->data["ip_address"],
						"waktu" => date("Y-m-d H:i:s")
					);
				$this->m_log->tambah($log);
			}
			$this->load->view('template',$this->data);
		}
		
		private function simpan_menu_grup_pengguna($id_grup_pengguna,$id_posisi_menu,$id_master_menu){
			$return = array("status" => false, "error_info" => "", "success_info" => "");
			
			if($this->m_menu_grup_pengguna->cek_posisi_menu_master_menu_grup_pengguna($id_grup_pengguna,$id_posisi_menu,$id_master_menu)){
				$return["status"] = true;
			}
			else{
				$nama_posisi_menu = $this->m_menu_grup_pengguna->ambil_nama_posisi_menu($id_posisi_menu);
				
				if($this->m_menu_grup_pengguna->cek_posisi_menu_grup_pengguna($id_grup_pengguna,$id_posisi_menu)){
					$set = array("id_master_menu"=>$id_master_menu);
					$arr_data_lama = $this->m_menu_grup_pengguna->data_menu_grup_pengguna($id_grup_pengguna,$id_posisi_menu);
					
					$log_data_lama = "";
				
					foreach($arr_data_lama as $key => $value){
						if(strcmp($key,"id")!=0){
							if(!empty($log_data_lama)){
								$log_data_lama .= "<br>";
							}
							$log_data_lama .= "$key = $value";
						}
					}
					
					$this->m_menu_grup_pengguna->ubah_menu_grup_pengguna($set,$id_grup_pengguna,$id_posisi_menu);
					if($this->m_menu_grup_pengguna->cek_posisi_menu_master_menu_grup_pengguna($id_grup_pengguna,$id_posisi_menu,$id_master_menu)){
						$return["status"] = true;
						$arr_data_baru = $this->m_menu_grup_pengguna->data_menu_grup_pengguna($id_grup_pengguna,$id_posisi_menu);
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
							"id_target" => $arr_data_lama["id_posisi_menu"],
							"deskripsi" => "ubah ".strtolower(preg_replace("/_/"," ",__CLASS__)),
							"kondisi_lama" => $log_data_lama,
							"kondisi_baru" => $log_data_baru,
							"alamat_ip" => $this->data["ip_address"],
							"waktu" => date("Y-m-d H:i:s")
						);
						$this->m_log->tambah($log);
						$return["success_info"] = "Berhasil mengubah posisi menu <b>$nama_posisi_menu</b>";
					}
					else{
						$return["status"] = false;
						$return["error_info"] = "Gagal mengubah posisi menu <b>$nama_posisi_menu</b>";
					}
				}
				else{
					$data = array(
								"id_grup_pengguna" => $id_grup_pengguna,
								"id_posisi_menu" => $id_posisi_menu,
								"id_master_menu" => $id_master_menu
							);
					$this->m_menu_grup_pengguna->tambah($data);
					
					if($this->m_menu_grup_pengguna->cek_posisi_menu_master_menu_grup_pengguna($id_grup_pengguna,$id_posisi_menu,$id_master_menu)){
						$return["status"] = true;
						$return["success_info"] = "Berhasil mengisi posisi menu <b>$nama_posisi_menu</b>";
					}
					else{
						$return["status"] = false;
						$return["error_info"] = "Gagal mengisi posisi menu <b>$nama_posisi_menu</b>";
					}
				}
			}
		}
		
		public function hak_akses($nama_grup_pengguna){
			$nama_grup_pengguna = urldecode($nama_grup_pengguna);
			$this->data['judul'] = "Hak Akses Grup Pengguna";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);
			
			$this->data["akses"] = $this->akses;
			$this->data['judul'] .= " : ".$nama_grup_pengguna;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."hak_akses_grup_pengguna";
			
			$this->load->model($this->folder_model."m_hak_akses_grup_pengguna");
			
			if($this->input->post()){
				izin($this->akses["ubah"]);
				
				$id_grup_pengguna = $this->input->post("id_grup_pengguna");
				$arr_id_aksi = $this->input->post("id_aksi");
				
				$hasil = $this->simpan_hak_akses_grup_pengguna($id_grup_pengguna,$arr_id_aksi);
				
				if($hasil["status"]){
					if(!empty($this->data['success'])){
						$this->data['success'] .= "<br>";
					}
					$this->data['success'] .= $hasil["success_info"];
					
					if((int)$id_grup_pengguna==(int)$this->session->userdata("grup")){
						$this->akses = akses_helper($this->data['id_modul']);
				
						izin($this->akses["akses"]);
						
						$this->data["akses"] = $this->akses;						
					}
				}
				else{
					if(!empty($this->data['warning'])){
						$this->data['warning'] .= "<br>";
					}
					$this->data['warning'] .= $hasil['error_info'];
				}
			}

			if($this->akses["lihat"]){
				$this->data["id_grup_pengguna"] = $this->m_grup_pengguna->ambil_id_grup_pengguna($nama_grup_pengguna);
				
				$this->data["daftar_hak_akses_grup_pengguna"] = $this->m_hak_akses_grup_pengguna->daftar_hak_akses_grup_pengguna($this->data["id_grup_pengguna"]);
				$this->data["daftar_hak_akses"] = $this->m_hak_akses_grup_pengguna->daftar_hak_akses();
				
				$hitung_hak_akses_per_kelompok = $this->m_hak_akses_grup_pengguna->hitung_hak_akses_per_kelompok();
				
				foreach($hitung_hak_akses_per_kelompok as $hitung){
					$arr_banyak_per_kelompok[$hitung["nama_kelompok_modul"]] = (int)$hitung["banyak"];
				}
				
				$this->data["banyak_per_kelompok"] = $arr_banyak_per_kelompok;
			}
			
			$this->data["url_grup_pengguna"] = $this->m_setting->ambil_url_modul("Grup Pengguna");
			
			$this->load->view('template',$this->data);
		}
		
		private function simpan_hak_akses_grup_pengguna($id_grup_pengguna,$arr_id_aksi){
			$return = array("status" => false, "error_info" => "");
		
			$arr_data_lama = $this->m_hak_akses_grup_pengguna->daftar_hak_akses();
			$data = array(
						"id_grup_pengguna" => $id_grup_pengguna
					);
			
			$this->m_hak_akses_grup_pengguna->hapus($data);
			
			for($i=0;$i<count($arr_id_aksi);$i++){
				$data = array("id_grup_pengguna" => $id_grup_pengguna,"id_aksi_modul" => $arr_id_aksi[$i]);
				$this->m_hak_akses_grup_pengguna->tambah($data);
			}
			
			if($this->m_hak_akses_grup_pengguna->cek_hak_akses_grup_pengguna($id_grup_pengguna,$arr_id_aksi)){
				$return["status"] = true;
				$arr_data_baru = $this->m_hak_akses_grup_pengguna->daftar_hak_akses();
				
				$log_data_lama = "";
				$kelompok = "";
				$modul = "";
				for($i=0;$i<count($arr_data_lama);$i++){
					if(strcmp($arr_data_lama[$i]["nama_kelompok_modul"],$kelompok)!=0){
						$kelompok = $arr_data_lama[$i]["nama_kelompok_modul"];
						$log_data_lama .= "<li>";
							$log_data_lama .= $arr_data_lama[$i]["nama_kelompok_modul"];
							$log_data_lama .= "<ul>";
					}
					
						if(strcmp($arr_data_lama[$i]["nama_modul"],$modul)!=0){
							$modul = $arr_data_lama[$i]["nama_modul"];
							$log_data_lama .= "<li>";
								$log_data_lama .= $arr_data_lama[$i]["nama_modul"];
								$log_data_lama .= "<ul>";
						}
									$log_data_lama .= "<li>".$arr_data_lama[$i]["nama_modul"]."</li>";
						
						if($i==count($arr_data_lama)-1){
								$log_data_lama .= "</ul>";
							$log_data_lama .= "</li>";
						}
						else if($i<count($arr_data_lama)-1 and strcmp($arr_data_lama[$i+1]["nama_modul"],$modul)!=0){
								$log_data_lama .= "</ul>";
							$log_data_lama .= "</li>";
						}
					
					
					if($i==count($arr_data_lama)-1){
							$log_data_lama .= "</ul>";
						$log_data_lama .= "</li>";
					}
					else if($i<count($arr_data_lama)-1 and strcmp($arr_data_lama[$i+1]["nama_kelompok_modul"],$kelompok)!=0){
							$log_data_lama .= "</ul>";
						$log_data_lama .= "</li>";
					}
				}
				
				if(!empty($log_data_lama)){
					$log_data_lama = "<ul>".$log_data_lama."</ul>";
				}
				
				$log_data_baru = "";
				$kelompok = "";
				$modul = "";
				for($i=0;$i<count($arr_data_baru);$i++){
					if(strcmp($arr_data_baru[$i]["nama_kelompok_modul"],$kelompok)!=0){
						$kelompok = $arr_data_baru[$i]["nama_kelompok_modul"];
						$log_data_baru .= "<li>";
							$log_data_baru .= $arr_data_baru[$i]["nama_kelompok_modul"];
							$log_data_baru .= "<ul>";
					}
					
						if(strcmp($arr_data_baru[$i]["nama_modul"],$modul)!=0){
							$modul = $arr_data_baru[$i]["nama_modul"];
							$log_data_baru .= "<li>";
								$log_data_baru .= $arr_data_baru[$i]["nama_modul"];
								$log_data_baru .= "<ul>";
						}
									$log_data_baru .= "<li>".$arr_data_baru[$i]["nama_modul"]."</li>";
						
						if($i==count($arr_data_baru)-1){
								$log_data_baru .= "</ul>";
							$log_data_baru .= "</li>";
						}
						else if($i<count($arr_data_baru)-1 and strcmp($arr_data_baru[$i+1]["nama_modul"],$modul)!=0){
								$log_data_baru .= "</ul>";
							$log_data_baru .= "</li>";
						}
					
					
					if($i==count($arr_data_baru)-1){
							$log_data_baru .= "</ul>";
						$log_data_baru .= "</li>";
					}
					else if($i<count($arr_data_baru)-1 and strcmp($arr_data_baru[$i+1]["nama_kelompok_modul"],$kelompok)!=0){
							$log_data_baru .= "</ul>";
						$log_data_baru .= "</li>";
					}
				}
				
				if(!empty($log_data_baru)){
					$log_data_baru = "<ul>".$log_data_baru."</ul>";
				}
				
				$log = array(
					"id_pengguna" => $this->session->userdata("id_pengguna"),
					"id_modul" => $this->data['id_modul'],
					"deskripsi" => "ubah ".strtolower(preg_replace("/_/"," ",__CLASS__)),
					"kondisi_lama" => $log_data_lama,
					"kondisi_baru" => $log_data_baru,
					"alamat_ip" => $this->data["ip_address"],
					"waktu" => date("Y-m-d H:i:s")
				);
				$this->m_log->tambah($log);
				
				$return["success_info"] = "Berhasil mengubah hak akses untuk grup pengguna ini.";
			}
			else{
				$return["status"] = false;
				$return["error_info"] = "Gagal mengubah hak akses untuk grup pengguna ini.";
			}
			
			return $return;
		}
	}
	
	
	/* End of file grup_pengguna.php */
	/* Location: ./application/controllers/administrator/grup_pengguna.php */
