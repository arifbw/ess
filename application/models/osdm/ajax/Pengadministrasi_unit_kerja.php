<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Pengadministrasi_unit_kerja extends CI_Controller {
		public function __construct(){
			parent::__construct();
			
			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'osdm/ajax/';
			$this->folder_model = 'administrator/';
			$this->akses = array();
			
			$this->load->model($this->folder_model."m_pengadministrasi");

			$this->data['success'] = "";
			$this->data['warning'] = "";
			
			$this->data["is_with_sidebar"] = true;
		}
		
		public function pengadministrasi($username){
			$this->data['judul'] = "Pengadministrasi Unit Kerja";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);
			
			$data["akses"] = $this->akses;
			
			$this->load->model($this->folder_model."m_grup_pengguna");
			
			$data["nama_grup_pengadministrasi"]="Pengadministrasi Unit Kerja";
			$id_grup_pengadministrasi = $this->m_grup_pengguna->ambil_id_grup_pengguna($data["nama_grup_pengadministrasi"]);
			
			$this->load->model($this->folder_model."m_pengguna");
			
			$id_pengguna = $this->m_pengguna->data_pengguna($username)["id"];
			
			$this->load->model($this->folder_model."m_pengguna_grup_pengguna");
			$data["cek_pengadministrasi"] = $this->m_pengguna_grup_pengguna->cek_anggota_grup($id_pengguna,$id_grup_pengadministrasi);
			
			$this->load->model("master_data/m_satuan_kerja");
			$data["daftar_satuan_kerja"] = $this->m_satuan_kerja->daftar_satuan_kerja();
			
			$data["username"] = $username;
			$data["pengadministrasi"] = $this->m_pengadministrasi->data_pengadministrasi($username);
			$data["admin_unit_kerja"] = "";
			
			foreach($data["pengadministrasi"] as $admin_unit_kerja){
				if(!empty($data["admin_unit_kerja"])){
					$data["admin_unit_kerja"] .= ",";
				}
				$data["admin_unit_kerja"] .= $admin_unit_kerja["kode_unit"];
			}
			
			$this->load->view($this->folder_view."pengadministrasi_unit_kerja",$data);
		}
	}
	
	
	/* End of file pengadministrasi_unit_kerja.php */
	/* Location: ./application/controllers/osdm/pengadministrasi_unit_kerja.php */