<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Listrik extends CI_Controller {
	public function __construct(){
		parent::__construct();
		
		$meta = meta_data();
		foreach($meta as $key => $value){
			$this->data[$key] = $value;
		}
		
		$this->folder_view = 'faskar/';
		$this->folder_model = 'faskar/';
		$this->folder_controller = 'faskar/';
		
		$this->akses = array();
		
		$this->load->helper("cutoff_helper");
		$this->load->helper("tanggal_helper");
		$this->load->helper("karyawan_helper");
		$this->load->helper("reference_helper");
		
		#$this->load->model($this->folder_model."m_permohonan_cuti");
		
		$this->data["is_with_sidebar"] = true;
		
		$this->data['judul'] = "Listrik";
		$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		$this->akses = akses_helper($this->data['id_modul']);				
		$this->nama_db = $this->db->database;
		izin($this->akses["akses"]);
	}
	
	public function index(){
		$this->load->model("pelaporan/M_pelaporan");
		$array_daftar_karyawan = $this->M_pelaporan->select_daftar_karyawan();
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
		$this->data['content'] = $this->folder_view."listrik/index";
		$this->data['array_daftar_karyawan'] = $array_daftar_karyawan;
		$this->data['array_daftar_bulan'] = $bulan;

		$this->load->view('template',$this->data);
	}	
	
	public function tabel_listrik() {
		$params=[];
		$params['lokasi'] = $_POST['lokasi'];
		$params['bulan'] = $_POST['bulan'];
		$this->load->model($this->folder_model."M_tabel_listrik");
		$list = $this->M_tabel_listrik->get_datatables($params);
		$data = array();
		$no = $_POST['start'];

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->M_tabel_listrik->count_all($params),
			"recordsFiltered" => $this->M_tabel_listrik->count_filtered($params),
			"data" => $list
		);
		echo json_encode($output);
	}

	function get_bulan(){
		$get = $this->db->select('bulan')->where('bulan is not null',null,false)->group_by('bulan')->order_by('bulan','DESC')->get('faskar_listrik')->result();
		echo json_encode($get);
	}

	function action_insert(){
		// echo json_encode($_POST);
		$bulan = $this->input->post('periode_tahun').'-'.$this->input->post('periode_bulan');
		$data_insert = [];
		foreach($this->input->post() as $key=>$value){
			if(!in_array($key, ['periode_tahun', 'periode_bulan', 'pemakaian'])){
				$data_insert[$key] = $value;
			}
		}
		$cek = $this->db->where([
			'no_kontrol'=>$data_insert['no_kontrol'],
			'bulan'=>$bulan
		])->get('faskar_listrik');
		if($cek->num_rows()>0){
			$this->session->set_flashdata('failed','Data sudah pernah diinput');
		} else{
			$data_insert['kode'] = $this->uuid->v4();
			$data_insert['bulan'] = $bulan;
			$data_insert['created_at'] = date('Y-m-d H:i:s');
			$data_insert['created_by'] = $_SESSION['no_pokok'];
			$this->db->insert('faskar_listrik', $data_insert);
			$this->session->set_flashdata('success','Data telah ditambahkan');
		}
		redirect('faskar/listrik');
	}
}