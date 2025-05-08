<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Detail extends CI_Controller {
	public function __construct(){
		parent::__construct();
		
		$meta = meta_data();
		foreach($meta as $key => $value){
			$this->data[$key] = $value;
		}
		
		$this->folder_view = 'faskar/persetujuan/internet/';
		$this->folder_model = 'faskar/internet/';
		$this->folder_controller = 'faskar/persetujuan/internet/';
		
		$this->akses = array();
		
		$this->data["is_with_sidebar"] = true;
		
		$this->data['judul'] = "Persetujuan Internet";
		$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		$this->akses = akses_helper($this->data['id_modul']);
		izin($this->akses["akses"]);
	}
	
	public function data($kode){
		$cek_kode = $this->db->where('kode', $kode)->get('ess_faskar_internet_header');
		if($cek_kode->num_rows()!=1){
			$this->session->set_flashdata('failed', 'Kode tidak valid');
			redirect('faskar/persetujuan/internet/header');
		} else{
			$header = $cek_kode->row();
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
			$this->data['content'] = $this->folder_view."detail";
			$this->data['array_daftar_bulan'] = $bulan;
			$this->data['header'] = $header;
	
			$this->load->view('template',$this->data);
		}
	}	
	
	public function tabel_internet() {
		$params=[];
		$params['header_id'] = $_POST['header_id'];
		$this->load->model($this->folder_model."M_tabel_internet_detail");
		$list = $this->M_tabel_internet_detail->get_datatables($params);
		$data = array();
		$no = $_POST['start'];

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->M_tabel_internet_detail->count_all($params),
			"recordsFiltered" => $this->M_tabel_internet_detail->count_filtered($params),
			"data" => $list
		);
		echo json_encode($output);
	}
}