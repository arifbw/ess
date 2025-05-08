<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Header extends CI_Controller {
	public function __construct(){
		parent::__construct();
		
		$meta = meta_data();
		foreach($meta as $key => $value){
			$this->data[$key] = $value;
		}
		
		$this->folder_view = 'faskar/verifikasi/ponsel/';
		$this->folder_model = 'faskar/ponsel/';
		$this->folder_controller = 'faskar/verifikasi/ponsel/';
		
		$this->akses = array();
		
		$this->data["is_with_sidebar"] = true;
		
		$this->data['judul'] = "Verifikasi Pulsa";
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
		$this->load->model("faskar/verifikasi/ponsel/M_tabel_ponsel_header");
		$list = $this->M_tabel_ponsel_header->get_datatables();
		$data = array();
		$no = $_POST['start'];

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->M_tabel_ponsel_header->count_all(),
			"recordsFiltered" => $this->M_tabel_ponsel_header->count_filtered(),
			"data" => $list
		);
		echo json_encode($output);
	}

	function approval(){
		$header = $this->input->post();
		$this->load->view('faskar/verifikasi/ponsel/approval', [
			'header'=>$header
		]);
	}

	function save_approval(){
		$id = $this->input->post('id',true);
		$approval_sdm = $this->input->post('approval_sdm',true);
		$data_insert = [
			'approval_status'=>$this->input->post('approval_sdm',true),
			'alasan_sdm'=>($approval_sdm=='3' ? null : $this->input->post('alasan_sdm',true)),
			'approval_sdm_at'=>date('Y-m-d H:i:s'),
			'approval_sdm_by'=>$_SESSION['no_pokok']
		];

		$this->db->where('id',$id)->update('ess_faskar_ponsel_header',$data_insert);

		echo json_encode([
			'status'=>true,
			'message'=>'Verifikasi telah dilakukan'
		]);
	}
}