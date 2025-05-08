<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Profil_karyawan extends CI_Controller {
		public function __construct(){
			parent::__construct();
			
			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'informasi/';
			$this->folder_model = 'informasi/';
			$this->folder_controller = 'informasi/';
			$this->folder_foto = 'foto/biodata/';
			$this->ekstensi = "jpg";
			
			$this->akses = array();
						
			$this->load->model($this->folder_model."m_profil_karyawan");
		
			$this->data["is_with_sidebar"] = true;
								
			$this->data['judul'] = "Profil Karyawan";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);

			izin($this->akses["akses"]);
		}
		
		public function index(){
			//	echo __FILE__ . __LINE__;die(var_dump($this->akses));	
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."profil_karyawan";

			$np 	= $_SESSION["no_pokok"];
			$this->data["karyawan"] = $this->m_profil_karyawan->profil_karyawan($np);
			
			$this->data["karyawan"]["foto"] = $this->m_profil_karyawan->ambil_foto_karyawan($np);
			
			if(empty($this->data["karyawan"]["foto"])){
				$this->data["karyawan"]["foto"] = "default.jpg";
			}
			
			$this->data["karyawan"]["foto"] = $this->folder_foto.$this->data["karyawan"]["foto"];

			$this->load->view('template',$this->data);
		}
	}
	
	/* End of file profil_karyawan.php */
	/* Location: ./application/controllers/informasi/profil_karyawan.php */