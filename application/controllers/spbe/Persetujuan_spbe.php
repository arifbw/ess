<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Persetujuan_spbe extends CI_Controller {
	public function __construct(){
		parent::__construct();
		
		$meta = meta_data();
		foreach($meta as $key => $value){
			$this->data[$key] = $value;
		}
		
		$this->folder_view = 'spbe/';
		$this->folder_model = 'spbe/';
		$this->folder_controller = 'spbe/';
		
		$this->akses = array();
		
		$this->load->helper("cutoff_helper");
		$this->load->helper("tanggal_helper");
		$this->load->helper("karyawan_helper");
		$this->load->helper("reference_helper");
		
		$this->load->model($this->folder_model."M_tabel_persetujuan_spbe");
		
		$this->data["is_with_sidebar"] = true;
		
		$this->data['judul'] = "Persetujuan SPBE";
		$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		$this->akses = akses_helper($this->data['id_modul']);				
		$this->nama_db = $this->db->database;
		izin($this->akses["akses"]);
	}
	
	public function index(){
		$this->data["akses"] = $this->akses;
		$this->data["navigasi_menu"] = menu_helper();
		$this->data['content'] = $this->folder_view."persetujuan_spbe";
		$this->load->view('template',$this->data);
	}

	function get_data_persetujuan(){
		$filter = ['np'=>$this->session->userdata('no_pokok')];
		$filter['start_date'] = date('Y-m-d', strtotime($this->input->post('start_date')));
		$filter['end_date'] = date('Y-m-d', strtotime($this->input->post('end_date')));
		$data = $this->M_tabel_persetujuan_spbe->get_datatables($filter);
		
		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->M_tabel_persetujuan_spbe->count_all($filter),
			"recordsFiltered" => $this->M_tabel_persetujuan_spbe->count_filtered($filter),
			"data" => $data,
			"np_login" => @$this->session->userdata('no_pokok') ?: null
		);
		
		echo json_encode($output);
	}

	function def_button(){
		$id = $this->input->post('id');
		$data['kasek'] = $this->db->where('id',$id)->where('approval_kasek_np', $this->session->userdata('no_pokok'))->get('ess_permohonan_spbe')->row();
		$data['atasan'] = $this->db->where('id',$id)->where('approval_atasan_np', $this->session->userdata('no_pokok'))->get('ess_permohonan_spbe')->row();
		
		echo json_encode($data);
	}

	function simpan_approval_atasan(){
		$np_login = $this->session->userdata('no_pokok');
		$spbe = $this->db->where('id',$this->input->post('id'))->get('ess_permohonan_spbe')->row();
		$approval_atasan_status = $this->input->post('approval_atasan_status');
		// $approval_kasek_status = $this->input->post('approval_kasek_status');
		
		if( $spbe->approval_pengamanan_keluar=='1' && $approval_atasan_status=='2' ){
			$status = false;
			$message = 'Pembatalan hanya lewat Petugas Pos karena barang sudah diapprove Petugas Pos';
		} else{
			$data_insert = [];
			if($np_login==$spbe->approval_kasek_np){
				$data_insert['approval_kasek_status'] = $approval_atasan_status;
				$data_insert['approval_kasek_keterangan'] = $approval_atasan_status=='1' ? null:$this->input->post('approval_atasan_keterangan');
				$data_insert['approval_kasek_updated_at'] = date('Y-m-d H:i:s');
			} else if($np_login==$spbe->approval_atasan_np){
				$data_insert['approval_atasan_status'] = $approval_atasan_status;
				$data_insert['approval_atasan_keterangan'] = $approval_atasan_status=='1' ? null:$this->input->post('approval_atasan_keterangan');
				$data_insert['approval_atasan_updated_at'] = date('Y-m-d H:i:s');
			}

			if($data_insert!=[]){
				$this->db->where('id',$this->input->post('id'))->update('ess_permohonan_spbe',$data_insert);
				if($this->db->affected_rows()>0){
					$status = true;
					$message = 'Persetujuan SPBE telah disimpan';
				} else{
					$status = false;
					$message = 'Gagal Melakukan Persetujuan';
				}
			} else{
				$status = false;
				$message = 'Gagal Melakukan Persetujuan';
			}
		}

		return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
				'status'=>$status,
				'message'=>$message
			]));
	}
}