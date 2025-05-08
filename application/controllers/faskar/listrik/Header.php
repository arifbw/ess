<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Header extends CI_Controller {
	public function __construct(){
		parent::__construct();
		
		$meta = meta_data();
		foreach($meta as $key => $value){
			$this->data[$key] = $value;
		}
		
		$this->folder_view = 'faskar/listrik/';
		$this->folder_model = 'faskar/listrik/';
		$this->folder_controller = 'faskar/listrik/';
		
		$this->akses = array();

		$this->load->helper("karyawan_helper");
		
		$this->data["is_with_sidebar"] = true;
		
		$this->data['judul'] = "Listrik dari PLN";
		$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		$this->akses = akses_helper($this->data['id_modul']);				
		$this->nama_db = $this->db->database;
		izin($this->akses["akses"]);
	}
	
	public function index(){
		$bulan = [
			['id'=>'01', 'value'=>'Januari'],
			['id'=>'02', 'value'=>'Februari'],
			['id'=>'03', 'value'=>'Maret'],
			['id'=>'04', 'value'=>'April'],
			['id'=>'05', 'value'=>'Mei'],
			['id'=>'06', 'value'=>'Juni'],
			['id'=>'07', 'value'=>'Juli'],
			['id'=>'08', 'value'=>'Agustus'],
			['id'=>'09', 'value'=>'September'],
			['id'=>'10', 'value'=>'Oktober'],
			['id'=>'11', 'value'=>'November'],
			['id'=>'12', 'value'=>'Desember']
		];
		
		$this->data["akses"] = $this->akses;
		$this->data["navigasi_menu"] = menu_helper();
		$this->data['content'] = $this->folder_view."index";
		$this->data['array_daftar_bulan'] = $bulan;

		$this->load->view('template',$this->data);
	}	
	
	public function tabel_header() {
		$this->load->model($this->folder_model."M_tabel_listrik_header");
		$list = $this->M_tabel_listrik_header->get_datatables();
		$data = array();
		$no = $_POST['start'];

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->M_tabel_listrik_header->count_all(),
			"recordsFiltered" => $this->M_tabel_listrik_header->count_filtered(),
			"data" => $list
		);
		echo json_encode($output);
	}

	function save_new_header(){
		$pemakaian_bulan = $this->input->post('periode_pemakaian_tahun', true).'-'.$this->input->post('periode_pemakaian_bulan', true);
		$pembayaran_bulan = $this->input->post('periode_pembayaran_tahun', true).'-'.$this->input->post('periode_pembayaran_bulan', true);
		$data_insert = [];
		$data_insert['lokasi'] = $this->input->post('lokasi', true);
		$data_insert['pemakaian_bulan'] = $pemakaian_bulan;
		$data_insert['pembayaran_bulan'] = $pembayaran_bulan;
		
		$this->db->where([
			'lokasi'=>$this->input->post('lokasi', true),
			'pemakaian_bulan'=>$pemakaian_bulan,
			'pembayaran_bulan'=>$pembayaran_bulan
		]);
		if(@$this->input->post('kode')){
			$this->db->where('kode!=', $this->input->post('kode'));
		} else{
			$this->db->group_start()
				->where('submit_date is null',null,false)
				->or_where('approval_status','0')
				->or_where('approval_status','1')
				->or_where('approval_status','3')
			->group_end();
		}
		$cek = $this->db->get('ess_faskar_listrik_header');
		if($cek->num_rows()>0){
			$this->session->set_flashdata('failed','Data sudah pernah diinput');
		} else{
			if(@$this->input->post('kode')){
				$data_insert['updated_at'] = date('Y-m-d H:i:s');
				$data_insert['updated_by'] = $_SESSION['no_pokok'];
				$this->db->where('kode', $this->input->post('kode'))->update('ess_faskar_listrik_header', $data_insert);
				$message = 'Data telah diupdate';
			} else{
				$data_insert['kode'] = $this->uuid->v4();
				$data_insert['created_at'] = date('Y-m-d H:i:s');
				$data_insert['created_by'] = $_SESSION['no_pokok'];
				$this->db->insert('ess_faskar_listrik_header', $data_insert);
				$message = 'Data telah ditambahkan';
			}
			$this->session->set_flashdata('success', $message);
		}
		redirect('faskar/listrik/header');
	}

	function ajukan(){
		$header = $this->input->post();
		$this->load->view('faskar/listrik/ajukan', [
			'header'=>$header
		]);
	}

	function save_ajukan(){
		$data = $this->input->post();
		$data['submit_date'] = date('Y-m-d H:i:s');
		$data['approval_status'] = '0';
		$this->db->where('id', $this->input->post('id',true))->update('ess_faskar_listrik_header', $data);
		echo json_encode([
			'status'=>true,
			'message'=>'Telah diajukan ke Atasan'
		]);
	}
}