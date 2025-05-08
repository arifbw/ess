<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Info_header extends CI_Controller {
	public function __construct(){
		parent::__construct();
		$this->load->helper("tanggal_helper");
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

		$this->load->view('faskar/view_info', $data);
	}
}