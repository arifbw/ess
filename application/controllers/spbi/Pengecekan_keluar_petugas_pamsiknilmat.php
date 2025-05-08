<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pengecekan_keluar_petugas_pamsiknilmat extends CI_Controller {
	public function __construct(){
		parent::__construct();
		
		$meta = meta_data();
		foreach($meta as $key => $value){
			$this->data[$key] = $value;
		}
		
		$this->folder_view = 'spbi/';
		$this->folder_model = 'spbi/';
		$this->folder_controller = 'spbi/';
		
		$this->akses = array();
		
		$this->load->helper("cutoff_helper");
		$this->load->helper("tanggal_helper");
		$this->load->helper("karyawan_helper");
		$this->load->helper("reference_helper");
		
		$this->load->model($this->folder_model."M_pemeriksaan_spbi_keluar");
		
		$this->data["is_with_sidebar"] = true;
		
		$this->data['judul'] = "Pemeriksaan SPBI Keluar oleh Petugas Pamsiknilmat";
		$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		$this->akses = akses_helper($this->data['id_modul']);				
		$this->nama_db = $this->db->database;
		izin($this->akses["akses"]);
	}
	
	public function index(){
		$this->data["akses"] = $this->akses;
		$this->data["navigasi_menu"] = menu_helper();
		$this->data['content'] = $this->folder_view."pengecekan_keluar_petugas_pamsiknilmat";

		$this->load->view('template',$this->data);
	}

	function get_data_persetujuan(){
		$filter = ['otoritas'=>'petugas pamsiknilmat'];
		$filter['start_date'] = date('Y-m-d', strtotime($this->input->post('start_date')));
		$filter['end_date'] = date('Y-m-d', strtotime($this->input->post('end_date')));
		$data = $this->M_pemeriksaan_spbi_keluar->get_datatables($filter);
		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->M_pemeriksaan_spbi_keluar->count_all($filter),
			"recordsFiltered" => $this->M_pemeriksaan_spbi_keluar->count_filtered($filter),
			"data" => $data,
		);
		echo json_encode($output);
	}

	function simpan_approval(){
		$spbi = $this->db->where('id',$this->input->post('id'))->get('ess_permohonan_spbi')->row();
		$pengecek1_status = $this->input->post('pengecek1_status');

		if( $spbi->approval_pengamanan_keluar=='1' && $pengecek1_status=='2' ){
			$status = false;
			$message = 'Pembatalan hanya lewat Petugas Pos karena barang sudah diapprove Petugas Pos';
		} else{
			$data_insert = [
				'pengecek1_np'=>$this->session->userdata('no_pokok'),
				'pengecek1_nama'=>$this->session->userdata('nama'),
				'pengecek1_jabatan'=>$this->session->userdata('nama_jabatan'),
				'pengecek1_status'=>$pengecek1_status,
				'pengecek1_keterangan'=>$pengecek1_status=='1' ? null:$this->input->post('pengecek1_keterangan'),
				'pengecek1_tanggal'=>$this->input->post('pengecek1_tanggal'),
				'pengecek1_jam'=>$this->input->post('pengecek1_jam'),
				'pengecek1_updated_at'=>date('Y-m-d H:i:s'),
			];
			$this->db->where('id',$this->input->post('id'))->update('ess_permohonan_spbi',$data_insert);
			if($this->db->affected_rows()>0){
				$status = true;
				$message = 'Pengecekan SPBI Keluar Perusahaan telah disimpan';
			} else{
				$status = false;
				$message = 'Gagal Melakukan Pengecekan';
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