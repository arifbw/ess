<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Permohonan_spbi extends CI_Controller {
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
		
		$this->load->model($this->folder_model."M_tabel_permohonan_spbi");
		$this->load->model($this->folder_model."M_permohonan_spbi");
		
		$this->data["is_with_sidebar"] = true;
		
		$this->data['judul'] = "Permohonan SPBI";
		$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		$this->akses = akses_helper($this->data['id_modul']);				
		$this->nama_db = $this->db->database;
		izin($this->akses["akses"]);
	}
	
	public function index(){
		$this->data["akses"] = $this->akses;
		$this->data["navigasi_menu"] = menu_helper();
		$this->data['content'] = $this->folder_view."permohonan_spbi";

		$this->load->view('template',$this->data);
	}

	function get_data_permohonan(){
		$filter = [];
		$filter['start_date'] = date('Y-m-d', strtotime($this->input->post('start_date')));
		$filter['end_date'] = date('Y-m-d', strtotime($this->input->post('end_date')));
		switch ($this->session->userdata('grup')) {
			case '4':
				$unit=array();
				$list_pengadministrasi = $this->session->userdata('list_pengadministrasi');
				foreach ($list_pengadministrasi as $i) {	
					array_push($unit,$i['kode_unit']);
				}
				$filter['kode_unit'] = $unit;
				break;
			case '5':
				$filter['np'] = $this->session->userdata('no_pokok');
				break;
			default:
				# code...
				break;
		}
		
		$data = $this->M_tabel_permohonan_spbi->get_datatables($filter);
		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->M_tabel_permohonan_spbi->count_all($filter),
			"recordsFiltered" => $this->M_tabel_permohonan_spbi->count_filtered($filter),
			"data" => $data,
		);
		echo json_encode($output);
	}
	
	public function tambah(){
		$array_daftar_karyawan	= $this->M_permohonan_spbi->select_daftar_karyawan([
			'grup'=>$_SESSION["grup"],
			'list_pengadministrasi'=>$_SESSION["list_pengadministrasi"],
			'no_pokok'=>$_SESSION["no_pokok"]
		])->result();
		$pos = $this->db->where('status','1')->get('mst_pos')->result();
		$this->data["pos"] = $pos;
		$this->data["array_daftar_karyawan"] = $array_daftar_karyawan;
		$this->data["akses"] = $this->akses;
		$this->data["navigasi_menu"] = menu_helper();
		$this->data['content'] = $this->folder_view."tambah_permohonan_spbi";

		$this->load->view('template',$this->data);
	}

	function simpan(){
		$response = [];
		$status = false;
		$message = '';

		$data_insert = [];
		$array_detail = ['np_karyawan','nama','nama_jabatan','kode_unit','nama_unit','milik','maksud','dikirim_ke','keluar_tanggal','approval_atasan_np','approval_atasan_nama','approval_atasan_jabatan','approval_atasan_kode_unit','approval_atasan_nama_unit','approval_atasan_nama_unit_singkat'];
		foreach ($this->input->post() as $key => $value) {
			if(in_array($key,$array_detail)) $data_insert[$key] = trim($value);
		}

		$array_pos_keluar = $this->input->post('pos_keluar_id',true);
		$integer_pos_keluar = array_map('intval', $array_pos_keluar);
		$pos_keluar_id = implode(',',$integer_pos_keluar);
		$data_insert['pos_keluar'] = json_encode($this->input->post('pos_keluar',true));
		$data_insert['pos_keluar_id'] = $pos_keluar_id;

		$array_pos_masuk = $this->input->post('pos_masuk_id',true);
		$integer_pos_masuk = array_map('intval', $array_pos_masuk);
		$pos_masuk_id = implode(',',$integer_pos_masuk);
		$data_insert['pos_masuk'] = json_encode($this->input->post('pos_masuk',true));
		$data_insert['pos_masuk_id'] = $pos_masuk_id;

		$barang = $this->input->post('barang');

		$last_counter = $this->db->select('MAX(no_urut) AS counter')->where("DATE_FORMAT(created_at,'%Y')", date('Y'))->where("approval_atasan_kode_unit", $this->input->post('approval_atasan_kode_unit'))->get('ess_permohonan_spbi')->row_array()['counter'];
		$no_urut = ($last_counter!=null ? $last_counter+1 : 1);
		// $nomor_surat = str_pad($no_urut, 6, "0", STR_PAD_LEFT);
		$nomor_surat = str_pad($no_urut, 6, "0", STR_PAD_LEFT) . '/' . $this->input->post('approval_atasan_nama_unit_singkat') . '/' . bulan_to_romawi(date('m')) . '/' . date('Y');
		$data_insert['nomor_surat'] = $nomor_surat;
		$data_insert['no_urut'] = $no_urut;
		$data_insert['created_at'] = date('Y-m-d H:i:s');
		$data_insert['created_by'] = $_SESSION['no_pokok'];
		$data_insert['uuid'] = $this->uuid->v4();

		$pilih_pengawal = $this->input->post('pilih_pengawal');
		switch ($pilih_pengawal) {
			case '2':
				$data_insert['konfirmasi_pengguna_np'] = $this->input->post('konfirmasi_pengguna_np');
				$data_insert['konfirmasi_pengguna_nama'] = $this->input->post('konfirmasi_pengguna_nama');
				$data_insert['konfirmasi_pengguna_jabatan'] = $this->input->post('konfirmasi_pengguna_jabatan');
				$data_insert['nama_pembawa_barang'] = $this->input->post('konfirmasi_pengguna_nama');
				break;
			case '3':
				$data_insert['konfirmasi_pengguna_np'] = $this->input->post('konfirmasi_pengguna_np');
				$data_insert['konfirmasi_pengguna_nama'] = $this->input->post('konfirmasi_pengguna_nama');
				$data_insert['konfirmasi_pengguna_jabatan'] = $this->input->post('konfirmasi_pengguna_jabatan');
				$data_insert['nama_pembawa_barang'] = $this->input->post('nama_pembawa_barang');
				break;
			default:
				$data_insert['konfirmasi_pengguna_np'] = $this->input->post('np_karyawan');
				$data_insert['konfirmasi_pengguna_nama'] = $this->input->post('nama');
				$data_insert['konfirmasi_pengguna_jabatan'] = $this->input->post('nama_jabatan');
				$data_insert['nama_pembawa_barang'] = $this->input->post('nama');
				break;
		}

		$this->db->insert('ess_permohonan_spbi',$data_insert);

		if($this->db->affected_rows()>0){
			$new_id = $this->db->insert_id();
			$data_barang = [];
			foreach ($barang as $row) {
				if(trim($row['nama_barang'])!=''){
					$value = $row;
					$value['id'] = $this->uuid->v4();
					$value['ess_permohonan_spbi_id'] = $new_id;
					$value['created_at'] = date('Y-m-d H:i:s');
					$data_barang[] = $value;
				}
			}
			$this->db->insert_batch('ess_permohonan_spbi_barang',$data_barang);
			$this->db->where('id',$new_id)->update('ess_permohonan_spbi',['barang'=>json_encode($data_barang)]);
			$status = true;
			$message = 'Data telah ditambahkan';
		} else{
			$message = 'Gagal menambahkan';
		}

		$response['status'] = $status;
		$response['message'] = $message;

		header("Content-Type: application/json");
		echo json_encode($response);
	}

	function get_atasan(){
		$this->load->model('m_approval');
		$np_karyawan = $this->input->post('np_karyawan');
		$kode_unit = [$this->input->post('kode_unit')];
		$list = $this->m_approval->list_atasan_minimal_kasek($kode_unit, $np_karyawan);
		header("Content-Type: application/json");
		echo json_encode($list);
	}
}