<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Detail extends CI_Controller {
	public function __construct(){
		parent::__construct();
		
		$meta = meta_data();
		foreach($meta as $key => $value){
			$this->data[$key] = $value;
		}
		
		$this->folder_view = 'faskar/info/pam/';
		$this->folder_model = 'faskar/info/pam/';
		$this->folder_controller = 'faskar/info/pam/';
		
		$this->akses = array();

		$this->load->helper("karyawan_helper");
		
		$this->data["is_with_sidebar"] = true;
		
		$this->data['judul'] = "Info Air dari PAM";
		$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		$this->akses = akses_helper($this->data['id_modul']);
		izin($this->akses["akses"]);
	}
	
	public function index(){
		$this->data["akses"] = $this->akses;
		$this->data["navigasi_menu"] = menu_helper();
		$this->data['content'] = $this->folder_view."index";

		$this->load->view('template',$this->data);
	}	
	
	public function tabel_detail() {
		$params = [
			'np'=>$_SESSION['no_pokok']
		];
		$this->load->model($this->folder_model."M_tabel_pam_detail");
		$list = $this->M_tabel_pam_detail->get_datatables($params);

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->M_tabel_pam_detail->count_all($params),
			"recordsFiltered" => $this->M_tabel_pam_detail->count_filtered($params),
			"data" => $list
		);
		echo json_encode($output);
	}

	function view_info(){
		$data = [];
		$header = $this->input->post();
		$text = str_replace('Status ','',$header['judul']);
		$data['header'] = $header;
		//DETAIL
		if($header['approval_status']=='1' || $header['approval_status']=='3' || $header['approval_status']=='4') {
			$data['approval_status'] = "Laporan {$text} <b>TELAH DISETUJUI Atasan</b> pada <b>".datetime_indo($header['approval_atasan_at'])."</b>";
			$data['approval_warna'] ='success';
		}else if($header['approval_status']=='2') {
			$data['approval_status'] = "Laporan {$text} <b>TIDAK DISETUJI Atasan</b> pada <b>".datetime_indo($header['approval_atasan_at'])."</b>"; 
			$data['approval_warna'] ='danger';
		} else if($header['approval_status']=='0' || $header['approval_status']==null) {
			$data['approval_status'] = "Menunggu Persetujuan Atasan"; 
			$data['approval_warna'] ='info';
		}

		if($header['approval_status']=='3' || $header['approval_status']=='5') {
			$data['sdm_status'] = "Laporan {$text} <b>TELAH DIVERIFIKASI SDM</b> pada <b>".datetime_indo($header['approval_sdm_at'])."</b>";
			$data['sdm_warna']= 'success';
		}else if($header['approval_status']=='4') {
			$data['sdm_status'] = "Laporan {$text} <b>TIDAK DISETUJUI SDM</b> pada <b>".datetime_indo($header['approval_sdm_at'])."</b>";
			$data['sdm_warna'] = 'danger';
		}

		$this->load->view("{$this->folder_view}view_info", $data);
	}
}