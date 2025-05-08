<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Edukasi_kesehatan extends CI_Controller {
		public function __construct(){
			parent::__construct();
			
			$meta = meta_data();
			
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'sikesper/edukasi_kesehatan/';
			$this->folder_model = 'sikesper/';
			$this->folder_ajax_view = $this->folder_view.'ajax/';
			$this->akses = array();
			
			$this->load->model($this->folder_model."/M_reimbursement");

			$this->data['success'] = "";
			$this->data['warning'] = "";
			
			$this->data["is_with_sidebar"] = true;
		}

		public function index(){
			$this->data['judul'] = "Edukasi Kesehatan";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);

			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."daftar";

			$this->data['panel_tambah'] = "";
			$this->data['nama'] = "";
			$this->data['status'] = "";
			
			if($this->akses["lihat"]){
							
				$this->data["feed"] = $this->feed();
				
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

		public function feed()
		{
			$json   = file_get_contents(base_url('uploads/rss.xml'));
			$xml=simplexml_load_string($json, 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NOBLANKS);
			$data = json_encode($xml->channel);
			$data = json_decode($data, TRUE); 
			
			return $data['item'];
		}
	}
?>