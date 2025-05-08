<?php defined('BASEPATH') OR exit('No direct script access allowed');

	class Biaya_kesehatan extends CI_Controller {

		public function __construct(){
			parent::__construct();
			
			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'master_data/';
			// $this->folder_model = 'master_data/';
			$this->folder_controller = 'master_data/';
			
			$this->akses = array();
			
			// $this->load->helper("cutoff_helper");
			// $this->load->helper("tanggal_helper");
			// $this->load->helper("karyawan_helper");
			// $this->load->helper("reference_helper");
			
			$this->data["is_with_sidebar"] = true;
			
			$this->data['judul'] = "Plafon Internet";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
            $this->nama_db = $this->db->database;
			izin($this->akses["akses"]);
		}

        public function index() {
            $this->load->model($this->folder_model."M_plafon");

            $array_daftar_karyawan	= $this->M_plafon->select_daftar_karyawan();
			
			$this->data["akses"] 					= $this->akses;
			$this->data["navigasi_menu"] 			= menu_helper();
			$this->data['content'] 					= $this->folder_view."biaya_kesehatan";
            $this->data['array_daftar_karyawan'] 	= $array_daftar_karyawan;

			$this->load->view('template',$this->data);
		}

		public function tabel_internet() {
			$this->load->model($this->folder_model."M_plafon_internet");
			$list = $this->M_plafon_internet->get_datatables();
			$data = array();
			$no = $_POST['start'];
	
			$output = array(
				"draw" => $_POST['draw'],
				"recordsTotal" => $this->M_plafon_internet->count_all(),
				"recordsFiltered" => $this->M_plafon_internet->count_filtered(),
				"data" => $list
			);
			echo json_encode($output);
		}

		function action_insert(){
			$data_insert = [];
			foreach($this->input->post() as $key=>$value){
				if(!in_array($key, ['id','plafon'])) $data_insert[$key] = $value;
			}

			if( $this->input->post('plafon')!='' )
				$data_insert['plafon'] = $this->input->post('plafon');
			else{
				$data_insert['plafon'] = null;
				$data_insert['ket'] = 'at cost';
			}
			// echo json_encode($data_insert); exit;

			# jika edit ada atribute 'id'
			if(@$this->input->post('id')){
				$data_insert['updated_at'] = date('Y-m-d H:i:s');
				$data_insert['updated_by'] = $_SESSION['no_pokok'];
				$this->db->where('id',$this->input->post('id'))->update('mst_plafon_internet', $data_insert);
				$this->session->set_flashdata('success',"Master plafon internet a.n <b>{$data_insert['nama_karyawan']}</b> telah diupdate");
			} else{ # ini untuk kondisi input => tidak ada atribute 'id'
				$cek = $this->db->where([
					'np_karyawan'=>$data_insert['np_karyawan']
				])->get('mst_plafon_internet');
				if($cek->num_rows()>0){
					$row = $cek->row();
					if($row->deleted_at==null)
						$this->session->set_flashdata('failed','Data sudah pernah diinput');
					else{
						$data_insert['deleted_at'] = null;
						$data_insert['deleted_by'] = null;
						$this->db->where('np_karyawan',$this->input->post('np_karyawan'))->update('mst_plafon_internet', $data_insert);
						$this->session->set_flashdata('success',"Master plafon internet a.n <b>{$data_insert['nama_karyawan']}</b> berhasil ditambahkan");
					}
				} else{
					$data_insert['created_at'] = date('Y-m-d H:i:s');
					$data_insert['created_by'] = $_SESSION['no_pokok'];
					$this->db->insert('mst_plafon_internet', $data_insert);
					$this->session->set_flashdata('success',"Master plafon internet a.n <b>{$data_insert['nama_karyawan']}</b> berhasil ditambahkan");
				}
			}

			redirect('master_data/plafon/internet');
		}

		function hapus(){
			$id = $this->input->post('id');
			$this->db->where('id',$id)->update('mst_plafon_internet', [
				'deleted_at'=>date('Y-m-d H:i:s'),
				'deleted_by'=>$_SESSION['no_pokok']
			]);
			echo json_encode([
				'status'=>true,
				'message'=>'Data dihapus'
			]);
		}
    }