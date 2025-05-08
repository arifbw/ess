<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Data_vaksin_penyintas extends CI_Controller {
	public function __construct(){
		parent::__construct();
		
		$meta = meta_data();
		foreach($meta as $key => $value){
			$this->data[$key] = $value;
		}
		
		$this->folder_view = 'vaksinasi/data_vaksin_penyintas/';
		$this->folder_model = 'vaksinasi/data_vaksin_penyintas/';
		$this->folder_controller = 'vaksinasi/';
		
		$this->akses = array();
		
		$this->load->helper("cutoff_helper");
		$this->load->helper("tanggal_helper");
		$this->load->helper("karyawan_helper");
		$this->load->helper("reference_helper");
					
		// $this->load->model($this->folder_model."M_data_vaksin_keluarga");
		
		$this->data["is_with_sidebar"] = true;
		
		$this->data['judul'] = "Data Vaksin Penyintas";
		$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		$this->akses = akses_helper($this->data['id_modul']);
		$this->nama_db = $this->db->database;
		izin($this->akses["akses"]);
	}
	
	public function index() {
		// switch ($_SESSION['grup']) {
		// 	case "5":
		// 		$list_np = $this->db->select('no_pokok, nama')->where('no_pokok', $_SESSION['no_pokok'])->get('mst_karyawan')->row_array();
		// 	  	break;
		// 	case "4":
		// 		$list_np = $this->db->select('no_pokok, nama')->where_in('kode_unit', array_column($_SESSION['list_pengadministrasi'], 'kode_unit'))->get('mst_karyawan')->result_array();
		// 	  	break;
		// 	default:
		// 		$list_np = [];
		// }
		
		$this->data["akses"] = $this->akses;
		// $this->data["list_np"] = $list_np;
		$this->data["navigasi_menu"] = menu_helper();
		$this->data['content'] = $this->folder_view."index";
		$this->load->view('template',$this->data);
	}

	function save(){
		$input=[];
		$response = [];
		foreach( $this->input->post() as $key=>$value){
			$field = str_replace('ubah_','',$key);
			if( !in_array($field, ['alamat_kode_prov','alamat_kode_kab','alamat_kode_kec','alamat_kode_kel','mst_klinik_id','tempat_lahir_keluarga','undefined']) ){
				if($field=='status_kawin'){
					$input[$field] = (trim($value)!='' ? strtoupper(trim($value)) : null);
				} else{
					$input[$field] = (trim($value)!='' ? trim($value) : null);
				}
			}
		}

		if( $this->input->post('ubah_status_vaksin')=='2' )
			$input['mst_klinik_id'] = $this->input->post('ubah_mst_klinik_id');
		else
			$input['mst_klinik_id'] = null;

		# cek ke table data_penyintas
		$this->check_data_penyintas($input['np_karyawan']);

		$cek = $this->db
			->where('np_karyawan', $input['np_karyawan'])
			->where('tipe_keluarga', $input['tipe_keluarga'])
			->get('data_vaksin_keluarga');
		if( $cek->num_rows() >0 ){
			$input['updated_at'] = date('Y-m-d H:i:s');
			$input['updated_by_np'] = $_SESSION['no_pokok'];
			$this->db->where('uuid', $cek->row()->uuid)->update('data_vaksin_keluarga', $input);
			$response['status'] = true;
			$response['message'] = '<div class="alert alert-success alert-dismissable">
										<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
										Data berhasil diupdate.
									</div>';
		} else{
			$input['uuid'] = $this->uuid->v4();
			$input['created_at'] = date('Y-m-d H:i:s');
			$input['created_by_np'] = $_SESSION['no_pokok'];
			$this->db->insert('data_vaksin_keluarga', $input);

			$response['status'] = true;
			$response['message'] = '<div class="alert alert-success alert-dismissable">
										<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
										Data berhasil ditambahkan.
									</div>';
		}
		$response['data'] = $this->generate_new_data($input['np_karyawan']);
		echo json_encode($response);
	}

	private function generate_new_data($np){
		$this->db->select("mst_karyawan.personnel_number as personal_number, mst_karyawan.no_pokok as np_karyawan, mst_karyawan.nama as nama_karyawan, mst_karyawan.tempat_lahir, 'Diri Sendiri' as tipe_keluarga, mst_karyawan.tempat_lahir as tempat_lahir_keluarga, mst_karyawan.tanggal_lahir, mst_karyawan.nama as nama_lengkap, NOW() as updated, 'Ditanggung' as status_tanggungan, (CASE WHEN mst_karyawan.jenis_kelamin='Laki-laki' THEN 'L' WHEN mst_karyawan.jenis_kelamin='Perempuan' THEN 'P' ELSE null END) as jenis_kelamin, ess_kesehatan_data_penyintas.id as id
				, data_vaksin_keluarga.uuid
				, data_vaksin_keluarga.nik
				, data_vaksin_keluarga.no_hp
				, data_vaksin_keluarga.email
				, data_vaksin_keluarga.status_kawin
				, data_vaksin_keluarga.alamat
				, data_vaksin_keluarga.status_vaksin
				, data_vaksin_keluarga.created_at
				, data_vaksin_keluarga.updated_at
				, data_vaksin_keluarga.mst_klinik_id
				, data_vaksin_keluarga.usia
				, data_vaksin_keluarga.tanggal_pcr_negatif
				, mst_klinik.kelurahan
				, mst_klinik.kecamatan
				, mst_klinik.kabupaten
				, mst_klinik.provinsi");
		$this->db->where('mst_karyawan.no_pokok', $np);
		$data = $this->db->from('mst_karyawan')
			->join('ess_kesehatan_data_penyintas', 'ess_kesehatan_data_penyintas.np_karyawan=mst_karyawan.no_pokok', 'LEFT')
			->join('data_vaksin_keluarga', 'data_vaksin_keluarga.np_karyawan=ess_kesehatan_data_penyintas.np_karyawan AND data_vaksin_keluarga.tipe_keluarga=ess_kesehatan_data_penyintas.tipe_keluarga', 'LEFT')
			->join('mst_klinik', 'mst_klinik.id=data_vaksin_keluarga.mst_klinik_id', 'LEFT')
			->get()
			->row();

		return $data;
	}

	private function check_data_penyintas($np){
		$check = $this->db->where('np_karyawan', $np)->get('ess_kesehatan_data_penyintas');
		if($check->num_rows()==0){
			$this->db->query("INSERT INTO ess_kesehatan_data_penyintas (personal_number, np_karyawan, nama_karyawan, tempat_lahir, tipe_keluarga, tempat_lahir_keluarga, tanggal_lahir, nama_lengkap, updated, status_tanggungan, jenis_kelamin)
			SELECT mst_karyawan.personnel_number, mst_karyawan.no_pokok, mst_karyawan.nama, mst_karyawan.tempat_lahir, 'Diri Sendiri', mst_karyawan.tempat_lahir, mst_karyawan.tanggal_lahir, mst_karyawan.nama, NOW(), 'Ditanggung', (CASE WHEN mst_karyawan.jenis_kelamin='Laki-laki' THEN 'L' WHEN mst_karyawan.jenis_kelamin='Perempuan' THEN 'P' ELSE null END)
			FROM mst_karyawan
			WHERE mst_karyawan.no_pokok='$np'");
		}
	}
}