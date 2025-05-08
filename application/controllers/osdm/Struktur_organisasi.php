<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Struktur_organisasi extends CI_Controller {
		public function __construct(){
			parent::__construct();
			
			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'osdm/';
			$this->folder_model = 'osdm/';
			$this->folder_controller = 'osdm/';
			
			$this->akses = array();
						
			$this->load->model($this->folder_model."m_struktur_organisasi");
		
			$this->data["is_with_sidebar"] = true;
			
			array_push($this->data["css_sources"],"/asset/treant-js-master/treant-js-master/Treant.css");
			array_push($this->data["css_sources"],"/asset/treant-js-master/treant-js-master/examples/custom-colored/custom-colored.css");
			array_push($this->data["js_sources"],"/asset/treant-js-master/treant-js-master/vendor/raphael");
			array_push($this->data["js_sources"],"/asset/treant-js-master/treant-js-master/Treant");
			
			$this->data['judul'] = "Struktur Organisasi";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);

			izin($this->akses["akses"]);
		}
		
		public function index(){
			//	echo __FILE__ . __LINE__;die(var_dump($this->akses));	
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."struktur_organisasi";

			$this->load->view('template',$this->data);
		}
	}
	
	/* End of file struktur_organisasi.php */
	/* Location: ./application/controllers/osdm/struktur_organisasi.php */