<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Pengguna_grup_pengguna extends CI_Controller {
		public function __construct(){
			parent::__construct();
					
			$this->folder_view = 'administrator/ajax/';
			$this->folder_model = 'administrator/';
			$this->akses = array();
			
			$this->load->model("m_setting");
			$this->load->model($this->folder_model."m_pengguna");
			$this->load->model($this->folder_model."m_pengguna_grup_pengguna");
			$this->load->model($this->folder_model."m_pengadministrasi");

			$this->meta = meta_data();
			$this->data['judul'] = "Grup Pengguna";
			
			$this->data['success'] = "";
			$this->data['warning'] = "";
		}

		public function lihat($username){
			$this->data['judul'] = "Pengguna";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			$this->data["akses"] = $this->akses;
			$this->data['modal'] = "grup";
			
			$this->data["username"] = $username;
			
			$this->load->model($this->folder_model."m_grup_pengguna");
			$this->data["daftar_grup_pengguna"] = $this->m_grup_pengguna->daftar_grup_pengguna_aktif();
			
			$grup_pengguna_user = $this->m_pengguna_grup_pengguna->grup_pengguna_user($username);
			
			$this->data["arr_grup_pengguna_user"] = array();
			
			for($i=0;$i<count($grup_pengguna_user);$i++){
				array_push($this->data["arr_grup_pengguna_user"],$grup_pengguna_user[$i]["id_grup_pengguna"]);
			}
			
			$pengadministrasi = $this->m_pengadministrasi->data_pengadministrasi($username);
			$this->data["admin_unit_kerja"] = "";
			
			foreach($pengadministrasi as $admin_unit_kerja){
				if(!empty($this->data["admin_unit_kerja"])){
					$this->data["admin_unit_kerja"] .= ",";
				}
				$this->data["admin_unit_kerja"] .= $admin_unit_kerja["kode_unit"];
			}
			
			$this->data['content'] = $this->folder_view."pengguna_grup_pengguna";
			$this->load->view($this->data['content'],$this->data);
		}
		
		public function unit_kerja($username){
			$this->data['judul'] = "Pengguna";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			$this->data["akses"] = $this->akses;
			$this->data['modal'] = "unit_kerja";
			
			$this->data["username"] = $username;

		$this->load->model("master_data/m_satuan_kerja");
		$this->load->model("m_keycloak"); //

		$dataUser = $this->db->select('id,username,no_pokok')->where('username', $username)->get('usr_pengguna')->row_array();

		$token = $this->m_keycloak->master();

		$satuan_kerja = $this->m_satuan_kerja->daftar_satuan_kerja();
		$pengadministrasi = $this->m_pengadministrasi->data_pengadministrasi($username);

		if ($token) {
			$user = $this->m_keycloak->getUserByFirstName($dataUser['no_pokok'], $token);
			$userId = @$user['id'];
			$unit_kerja = $this->m_keycloak->getUnitKerja($token, $userId);

			foreach ($unit_kerja as $item) {
				$nameParts = explode('_', $item->name);
				if (isset($nameParts[1])) $item->name = $nameParts[1];
				if (isset($nameParts[0])) $item->kode_unit = $nameParts[0];
			}


			$this->m_keycloak->matchUnitKerja($pengadministrasi, $unit_kerja, $dataUser['id']);
		}

		$this->data["daftar_satuan_kerja"] = $satuan_kerja;

		$this->data["pengadministrasi"] = $pengadministrasi;

		$this->data['content'] = $this->folder_view . "pengguna_grup_pengguna";
		$this->load->view($this->data['content'], $this->data);
	}
}
	
	/* End of file pengguna_grup_pengguna.php */
	/* Location: ./application/controllers/administrator/ajax/pengguna_grup_pengguna.php */
