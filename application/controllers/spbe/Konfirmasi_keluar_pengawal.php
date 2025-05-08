<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Konfirmasi_keluar_pengawal extends CI_Controller {
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
		
		$this->load->model($this->folder_model."M_pemeriksaan_spbe_keluar");
		
		$this->data["is_with_sidebar"] = true;
		
		$this->data['judul'] = "Konfirmasi SPBE Keluar oleh Pengawas/Pengawal/Penyegel";
		$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		$this->akses = akses_helper($this->data['id_modul']);				
		$this->nama_db = $this->db->database;
		izin($this->akses["akses"]);
	}
	
	public function index(){
		$this->data["akses"] = $this->akses;
		$this->data["navigasi_menu"] = menu_helper();
		$this->data['content'] = $this->folder_view."konfirmasi_keluar_pengawal";

		$this->load->view('template',$this->data);
	}

	function get_data_persetujuan(){
		$filter = ['otoritas'=>'pengguna', 'np'=>$this->session->userdata('no_pokok')];
		$filter['start_date'] = date('Y-m-d', strtotime($this->input->post('start_date')));
		$filter['end_date'] = date('Y-m-d', strtotime($this->input->post('end_date')));
		$data = $this->M_pemeriksaan_spbe_keluar->get_datatables($filter);
		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->M_pemeriksaan_spbe_keluar->count_all($filter),
			"recordsFiltered" => $this->M_pemeriksaan_spbe_keluar->count_filtered($filter),
			"data" => $data,
		);
		echo json_encode($output);
	}

	function simpan_approval(){
		$spbe = $this->db->where('id',$this->input->post('id'))->get('ess_permohonan_spbe')->row();
		$konfirmasi_pengguna = $this->input->post('konfirmasi_pengguna');

		if( $spbe->approval_pengamanan_keluar=='1' && $konfirmasi_pengguna=='2' ){
			$status = false;
			$message = 'Pembatalan hanya lewat Petugas Pos karena barang sudah diapprove Petugas Pos';
		} else{
			$data_insert = [
				'konfirmasi_pengguna'=>$this->input->post('konfirmasi_pengguna'),
				'konfirmasi_pengguna_keterangan'=>$this->input->post('konfirmasi_pengguna')=='1' ? null:$this->input->post('konfirmasi_pengguna_keterangan'),
				'konfirmasi_pengguna_tanggal'=>$this->input->post('konfirmasi_pengguna_tanggal'),
				'konfirmasi_pengguna_jam'=>$this->input->post('konfirmasi_pengguna_jam'),
				'konfirmasi_pengguna_at'=>date('Y-m-d H:i:s'),
			];
			$this->db->where('id',$this->input->post('id'))->update('ess_permohonan_spbe',$data_insert);
			if($this->db->affected_rows()>0){
				$status = true;
				$message = 'Konfirmasi SPBE Keluar Perusahaan telah disimpan';
			} else{
				$status = false;
				$message = 'Gagal Melakukan Konfirmasi';
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