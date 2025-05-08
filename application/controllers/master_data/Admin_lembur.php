<?php defined('BASEPATH') OR exit('No direct script access allowed');

	class Admin_lembur extends CI_Controller {

		public function __construct(){
			parent::__construct();
            $meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'master_data/';
			$this->folder_model = 'master_data/';
			$this->folder_ajax_view = $this->folder_view.'ajax/';
			$this->akses = array();
			
			$this->load->model($this->folder_model."/m_admin_lembur");

			$this->data['success'] = "";
			$this->data['warning'] = "";
			
			$this->data["is_with_sidebar"] = true;
        }

        public function index(){
            $this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."admin_lembur";
			
			array_push($this->data['js_sources'],"master_data/admin_lembur");


			$start_date_realisasi = isset($POST["start_date_realisasi"]) ? $POST["start_date_realisasi"] : NULL;
			$end_date_realisasi = isset($POST["end_date_realisasi"]) ? $POST["end_date_realisasi"] : NULL;

			$this->data["realisasi_target_divisi"]= $this->m_admin_lembur->get_realisasi_target_divisi($start_date_realisasi , $end_date_realisasi);
            $this->data["realisasi_target_departemen"]= $this->m_admin_lembur->get_realisasi_target_departemen();
            $this->data["top_divisi"]= $this->m_admin_lembur->get_top_divisi();
            $this->data["top_departemen"]= $this->m_admin_lembur->get_top_departemen();
            $this->data["rekap_lembur_bulan"]=$this->m_admin_lembur->get_rekap_lembur_bulan();
            $this->load->view('template',$this->data);
        }
    }