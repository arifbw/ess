<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pemeriksaan_keluar_admin_pamsiknilmat extends CI_Controller {
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
		
		$this->load->model($this->folder_model."M_pemeriksaan_pos_keluar");
		
		$this->data["is_with_sidebar"] = true;
		
		$this->data['judul'] = "Pemeriksaan SPBI Keluar oleh Admin Pamsiknilmat";
		$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		$this->akses = akses_helper($this->data['id_modul']);				
		$this->nama_db = $this->db->database;
		izin($this->akses["akses"]);
	}
	
	public function index(){
		$this->data["akses"] = $this->akses;
		$this->data["navigasi_menu"] = menu_helper();
		$this->data['content'] = $this->folder_view."pemeriksaan_keluar_admin_pamsiknilmat";

		$this->load->view('template',$this->data);
	}

	function get_data_persetujuan(){
		$filter['start_date'] = date('Y-m-d', strtotime($this->input->post('start_date')));
		$filter['end_date'] = date('Y-m-d', strtotime($this->input->post('end_date')));
		$data = $this->M_pemeriksaan_pos_keluar->get_datatables($filter);
		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->M_pemeriksaan_pos_keluar->count_all($filter),
			"recordsFiltered" => $this->M_pemeriksaan_pos_keluar->count_filtered($filter),
			"data" => $data,
		);
		echo json_encode($output);
	}

	function simpan_approval(){
		$ess_permohonan_spbi_id = $this->input->post('ess_permohonan_spbi_id');
		$data_pos = [
			'id'=>$this->uuid->v4(),
			'ess_permohonan_spbi_id'=>$ess_permohonan_spbi_id,
			'pos_id'=>@$this->input->post('pos_id') ?: null,
			'pos_nama'=>$this->input->post('pos_nama'),
			'tanggal'=>$this->input->post('tanggal'),
			'jam'=>$this->input->post('jam'),
			'keterangan'=>$this->input->post('keterangan'),
			'posisi'=>$this->input->post('posisi'),
			'barang_sesuai'=>$this->input->post('kondisi_barang_keluar'),
			'approval_np'=>$this->session->userdata('no_pokok'),
			'approval_nama'=>$this->session->userdata('nama'),
			'created_at'=>date('Y-m-d H:i:s')
		];
		$this->db->insert('ess_permohonan_spbi_pos',$data_pos);

		if($this->db->affected_rows()>0){
			$approval_pengamanan_posisi = $this->db->where('ess_permohonan_spbi_id',$ess_permohonan_spbi_id)->order_by('tanggal','DESC')->order_by('jam','DESC')->get('ess_permohonan_spbi_pos')->result_array();
			$this->db->where('id',$ess_permohonan_spbi_id)->update('ess_permohonan_spbi',[
				'approval_pengamanan_posisi'=>json_encode($approval_pengamanan_posisi),
				'approval_pengamanan_keluar'=>'1',
				'approval_pengamanan_updated_at'=>date('Y-m-d H:i:s'),
				'kondisi_barang_keluar'=>$this->input->post('kondisi_barang_keluar')
			]);

			$status = true;
			$message = 'Pemeriksaan SPBI Keluar Perusahaan telah disimpan';
		} else{
			$status = false;
			$message = 'Gagal Melakukan Pemeriksaan';
		}

		header('Content-Type: application/json; charset=utf-8');
		echo json_encode([
			'status'=>$status,
			'message'=>$message
		]);
	}
}