<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Pegawai extends CI_Controller {
		public function __construct(){
			parent::__construct();
			
			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'pegawai/';
			$this->folder_ajax_view = $this->folder_view.'ajax/';
			
			$this->load->model("m_pegawai");

			$this->data['success'] = "";
			$this->data['warning'] = "";
		}

		public function daftar_pegawai(){
			$this->data['judul'] = "Daftar Pegawai";
			$this->data['id_modul'] = $this->m_pengaturan->ambil_id_modul_nama($this->data['judul']);
			$this->data['content'] = $this->folder_view."daftar_pegawai";
			$this->data["navigasi_menu"] = menu_helper(2);
			
			array_push($this->data['js_sources'],"daftar_pegawai");
			
			// $this->data["daftar_modul_aksi"] = $this->m_administrator->daftar_modul_aksi();
			
			if($this->input->post()){
				if(!strcmp($this->input->post("aksi"),"tambah")){
					$this->data["no_pokok"] = $this->input->post("no_pokok");
					$this->data["nama"] = $this->input->post("nama");
					$this->data["jenis_kelamin"] = $this->input->post("jenis_kelamin");
					$this->data["nomor_ktp"] = $this->input->post("nomor_ktp");
					$this->data["tanggal_lahir"] = $this->input->post("tanggal_lahir");
					$this->data["tanggal_masuk"] = $this->input->post("tanggal_masuk");

					$tambah = $this->m_pegawai->tambah_pegawai($this->data);
				}
			}
			
			$js_header_script = "<script>
								$(document).ready(function() {
									$('#tabel_daftar_pegawai').DataTable({
										responsive: true
									});
								});
						</script>";
			array_push($this->data["js_header_script"],$js_header_script);
			
			$this->data["daftar_pegawai"] = $this->m_pegawai->daftar_pegawai();
			
			$this->load->view('template',$this->data);
		}
		
		public function detail_pegawai($no_pokok){
			$this->data['judul'] = "Detail Pegawai";
			$this->data['id_modul'] = $this->m_pengaturan->ambil_id_modul_nama($this->data['judul']);
			$this->data['content'] = $this->folder_view."daftar_pegawai";
			$this->data["navigasi_menu"] = menu_helper(2);
			
			array_push($this->data['js_sources'],"daftar_pegawai");
			
			// $this->data["daftar_modul_aksi"] = $this->m_administrator->daftar_modul_aksi();
			
			if($this->input->post()){
				if(!strcmp($this->input->post("aksi"),"tambah")){
					$this->data["no_pokok"] = $this->input->post("no_pokok");
					$this->data["nama"] = $this->input->post("nama");
					$this->data["jenis_kelamin"] = $this->input->post("jenis_kelamin");
					$this->data["nomor_ktp"] = $this->input->post("nomor_ktp");
					$this->data["tanggal_lahir"] = $this->input->post("tanggal_lahir");
					$this->data["tanggal_masuk"] = $this->input->post("tanggal_masuk");

					$tambah = $this->m_pegawai->tambah_pegawai($this->data);
				}
			}
			
			$js_header_script = "<script>
								$(document).ready(function() {
									$('#tabel_daftar_pegawai').DataTable({
										responsive: true
									});
								});
						</script>";
			array_push($this->data["js_header_script"],$js_header_script);
			
			$this->data["daftar_pegawai"] = $this->m_pegawai->daftar_pegawai();
			
			$this->load->view('template',$this->data);
		}
		
		public function index(){
			$this->data['content'] = 'dashboard';
			$this->data["navigasi_menu"] = menu_helper(2);
			
			$this->load->view('template',$this->data);
		}
	}
	
	/* End of file pegawai.php */
	/* Location: ./application/controllers/pegawai.php */