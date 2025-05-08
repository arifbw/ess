<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pengecekan_masuk_petugas_pamsiknilmat extends CI_Controller {
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
		
		$this->load->model($this->folder_model."M_pemeriksaan_spbi_masuk");
		
		$this->data["is_with_sidebar"] = true;
		
		$this->data['judul'] = "Pemeriksaan SPBI Masuk oleh Petugas Pamsiknilmat";
		$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		$this->akses = akses_helper($this->data['id_modul']);				
		$this->nama_db = $this->db->database;
		izin($this->akses["akses"]);
	}
	
	public function index(){
		$this->data["akses"] = $this->akses;
		$this->data["navigasi_menu"] = menu_helper();
		$this->data['content'] = $this->folder_view."pengecekan_masuk_petugas_pamsiknilmat";

		$this->load->view('template',$this->data);
	}

	function get_data_persetujuan(){
		$filter = ['otoritas'=>'petugas pamsiknilmat', 'np'=>$this->session->userdata('no_pokok')];
		$filter['start_date'] = date('Y-m-d', strtotime($this->input->post('start_date')));
		$filter['end_date'] = date('Y-m-d', strtotime($this->input->post('end_date')));
		$data = $this->M_pemeriksaan_spbi_masuk->get_datatables($filter);
		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->M_pemeriksaan_spbi_masuk->count_all($filter),
			"recordsFiltered" => $this->M_pemeriksaan_spbi_masuk->count_filtered($filter),
			"data" => $data,
		);
		echo json_encode($output);
	}

	function simpan_approval(){
		$ess_permohonan_spbi_id = $this->input->post('ess_permohonan_spbi_id');
		$new_id = $this->uuid->v4();
		$data_pos = [
			'id'=>$new_id,
			'ess_permohonan_spbi_id'=>$ess_permohonan_spbi_id,
			'pos_id'=>$this->input->post('pos_id'),
			'pos_nama'=>$this->input->post('pos_nama'),
			'tanggal'=>$this->input->post('tanggal'),
			'jam'=>$this->input->post('jam'),
			'keterangan'=>$this->input->post('keterangan'),
			'posisi'=>$this->input->post('posisi'),
			'barang_sesuai'=>$this->input->post('barang_sesuai'),
			'approval_np'=>$this->session->userdata('no_pokok'),
			'approval_nama'=>$this->session->userdata('nama'),
			'created_at'=>date('Y-m-d H:i:s')
		];
		$this->db->insert('ess_permohonan_spbi_pos',$data_pos);

		if($this->db->affected_rows()>0){
			$kondisi_barang = $this->input->post('kondisi_barang');
			for ($i=0; $i < count($kondisi_barang) ; $i++) { 
				$kondisi_barang[$i]['created_at'] = date('Y-m-d H:i:s');
				$kondisi_barang[$i]['ess_permohonan_spbi_pos_id'] = $new_id;
				$kondisi_barang[$i]['id'] = $this->uuid->v4();
			}
			if( count($kondisi_barang) > 0 ) $this->db->insert_batch('ess_permohonan_spbi_kondisi_barang',$kondisi_barang);

			# update _pos
			$this->db->where('id',$new_id)->update('ess_permohonan_spbi_pos',['kondisi_barang_json'=>json_encode($kondisi_barang)]);

			$approval_pengamanan_posisi = $this->db->where('ess_permohonan_spbi_id',$ess_permohonan_spbi_id)->order_by('tanggal','DESC')->order_by('jam','DESC')->get('ess_permohonan_spbi_pos')->result_array();
			$this->db->where('id',$ess_permohonan_spbi_id)->update('ess_permohonan_spbi',[
				'approval_pengamanan_posisi'=>json_encode($approval_pengamanan_posisi),
				'approval_pengamanan_masuk'=>'1',
				'approval_pengamanan_updated_at'=>date('Y-m-d H:i:s')
			]);

			$status = true;
			$message = 'Pemeriksaan SPBI Kembali ke Perusahaan telah disimpan';
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