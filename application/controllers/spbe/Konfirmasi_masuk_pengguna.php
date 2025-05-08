<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Konfirmasi_masuk_pengguna extends CI_Controller {
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
		
		$this->load->model($this->folder_model."M_pemeriksaan_spbe_masuk");
		
		$this->data["is_with_sidebar"] = true;
		
		$this->data['judul'] = "Konfirmasi SPBE Masuk oleh Pengguna";
		$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		$this->akses = akses_helper($this->data['id_modul']);				
		$this->nama_db = $this->db->database;
		izin($this->akses["akses"]);
	}
	
	public function index(){
		$this->data["akses"] = $this->akses;
		$this->data["navigasi_menu"] = menu_helper();
		$this->data['content'] = $this->folder_view."konfirmasi_masuk_pengguna";

		$this->load->view('template',$this->data);
	}

	function get_data_persetujuan(){
		$filter = ['otoritas'=>'pengguna', 'np'=>$this->session->userdata('no_pokok')];
		$filter['start_date'] = date('Y-m-d', strtotime($this->input->post('start_date')));
		$filter['end_date'] = date('Y-m-d', strtotime($this->input->post('end_date')));
		$data = $this->M_pemeriksaan_spbe_masuk->get_datatables($filter);
		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->M_pemeriksaan_spbe_masuk->count_all($filter),
			"recordsFiltered" => $this->M_pemeriksaan_spbe_masuk->count_filtered($filter),
			"data" => $data,
		);
		echo json_encode($output);
	}

	function simpan_approval(){
		$data_insert = [
			'konfirmasi_pembawa_status'=>$this->input->post('konfirmasi_pembawa_status'),
			'konfirmasi_pembawa_keterangan'=>$this->input->post('konfirmasi_pembawa_status')=='1' ? null:$this->input->post('konfirmasi_pembawa_keterangan'),
			'konfirmasi_pembawa_tanggal'=>$this->input->post('konfirmasi_pembawa_tanggal'),
			'konfirmasi_pembawa_jam'=>$this->input->post('konfirmasi_pembawa_jam'),
			'konfirmasi_pembawa_at'=>date('Y-m-d H:i:s'),
		];
		$this->db->where('id',$this->input->post('id'))->update('ess_permohonan_spbe',$data_insert);
		if($this->db->affected_rows()>0){
			$status = true;
			$message = 'Konfirmasi SPBE Kembali ke Perusahaan telah disimpan';
		} else{
			$status = false;
			$message = 'Gagal Melakukan Konfirmasi';
		}

		header('Content-Type: application/json; charset=utf-8');
		echo json_encode([
			'status'=>$status,
			'message'=>$message
		]);
	}
}