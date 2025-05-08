<?php defined('BASEPATH') OR exit('No direct script access allowed');

	class Regulasi extends CI_Controller {
		public function __construct(){
			parent::__construct();

			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}

			$this->folder_view = 'pelaporan/';
			$this->folder_model = 'pelaporan/';
			$this->folder_controller = 'pelaporan/';

			$this->akses = array();

			$this->load->model($this->folder_model."/m_regulasi");

			$this->data["is_with_sidebar"] = true;

			$this->data['judul'] = "Regulasi";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);

			izin($this->akses["akses"]);
		}
		
		public function index(){
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."master_data/regulasi";
			$this->data["daftar_regulasi"] = $this->m_regulasi->daftar_regulasi();
			$this->data['pelaporan'] = $this->m_regulasi->daftar_laporan_tambah();
			$this->data['pelaporan_ubah'] = $this->m_regulasi->daftar_laporan_ubah();

			$this->load->view('template',$this->data);
		}
		
		function tambah(){
			$pelaporan = $this->input->post('pelaporan');
			$regulasi = $this->input->post('regulasi');
			$status = $this->input->post('status');
			if($this->m_regulasi->cek_tambah($pelaporan)){
				$data = array(
					"regulasi" => $regulasi,
					"id_laporan" => $pelaporan,
					"status" => $status
				);
				$this->m_regulasi->tambah($data);

				if($this->m_regulasi->cek_hasil($pelaporan,$status)){
					$this->session->set_flashdata('success',"Berhasil Tambah Regulasi");
					redirect(base_url($this->folder_controller.'master_data/regulasi'));
				}
				else{
					$this->session->set_flashdata('warning',"Gagal Tambah Regulasi");
                    redirect(base_url($this->folder_controller.'master_data/regulasi'));
				}
			}
			else{
				$this->session->set_flashdata('warning',"Regulasi sudah ada");
                redirect(base_url($this->folder_controller.'master_data/regulasi'));
			}
		}

		function get_data() {
			$return = array(
				'status' => false
			);
			$data = $this->m_regulasi->data_regulasi($this->input->post('id'));
			if($data){
				$return['status'] = true;
				$return['data'] = $data;
				$return['message'] = "Berhasil ambil data";
			}else{
				$return['message'] = "Gagal ambil data";
			}
			echo json_encode($return);
		}

		function ubah(){
			$id = $this->input->post('id');
			$pelaporan = $this->input->post('pelaporan');
			$regulasi = $this->input->post('regulasi');
			$status = $this->input->post('status');
			$status = $status=='aktif'?'1':'0';
			if($this->m_regulasi->data_regulasi($id)){
				// if($this->m_regulasi->cek_tambah($pelaporan)){
					$data = array(
						"regulasi" => $regulasi,
						"id_laporan" => $pelaporan,
						"status" => $status
					);
					$this->m_regulasi->ubah($data,$id);
					if($this->db->affected_rows()){
						$this->session->set_flashdata('success',"Berhasil Ubah Regulasi");
						redirect(base_url($this->folder_controller.'master_data/regulasi'));
					}
					else{
						$this->session->set_flashdata('warning',"Gagal Ubah Regulasi");
						redirect(base_url($this->folder_controller.'master_data/regulasi'));
					}
				// }
				// else{
				// 	$this->session->set_flashdata('warning',"Regulasi sudah ada");
				// 	redirect(base_url($this->folder_controller.'master_data/regulasi'));
				// }
			}
			else{
				$this->session->set_flashdata('warning',"Regulasi tidak ada");
                redirect(base_url($this->folder_controller.'master_data/regulasi'));
			}
		}
	}