<?php defined('BASEPATH') OR exit('No direct script access allowed');

	class Alasan_sipk extends CI_Controller {

		public function __construct(){
			parent::__construct();
			
			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'master_data/';
			$this->folder_model = 'master_data/';
			$this->folder_controller = 'master_data/';
			
			$this->akses = array();
			
			$this->load->helper("cutoff_helper");
			$this->load->helper("tanggal_helper");
			$this->load->helper("karyawan_helper");
			$this->load->helper("reference_helper");
			
			$this->data["is_with_sidebar"] = true;
			
			$this->data['judul'] = "Alasan SIPK";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
            $this->nama_db = $this->db->database;
			izin($this->akses["akses"]);
		}

        public function index() {
            $this->load->model($this->folder_model."M_alasan_sipk");
            $alasan	= $this->M_alasan_sipk->alasan();
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."alasan_sipk";
            $this->data['alasan'] = $alasan;

			$this->load->view('template',$this->data);
		}

		function action_insert(){
			$alasan = trim($this->input->post('alasan',true));
			$status = trim($this->input->post('status',true));

			$data_insert = [];
			foreach($this->input->post() as $key=>$value){
				if(!in_array($key, ['id'])) $data_insert[$key] = trim($value);
			}

			# jika edit ada atribute 'id'
			if(@$this->input->post('id')){
				$cek = $this->db->where('LOWER(alasan)',strtolower($alasan))->where('id!=',$this->input->post('id'))->get('mst_sipk_alasan');
				if($cek->num_rows()>0){
					$this->session->set_flashdata('failed',"Alasan {$alasan} sudah pernah diinput");
				} else{
					$this->db->where('id',$this->input->post('id'))->update('mst_sipk_alasan', $data_insert);
					$this->session->set_flashdata('success',"Alasan telah diupdate");
				}
			} else{ # ini untuk kondisi input => tidak ada atribute 'id'
				$cek = $this->db->where('LOWER(alasan)',strtolower($alasan))->get('mst_sipk_alasan');
				if($cek->num_rows()>0){
					$this->session->set_flashdata('failed',"Alasan {$alasan} sudah pernah diinput");
				} else{
					$this->db->insert('mst_sipk_alasan', $data_insert);
					$this->session->set_flashdata('success',"Alasan {$alasan} berhasil ditambahkan");
				}
			}

			redirect('master_data/alasan_sipk');
		}
    }