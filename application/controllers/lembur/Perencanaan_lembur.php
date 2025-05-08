<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Perencanaan_lembur extends CI_Controller {
	public function __construct() {
		parent::__construct();

		$meta = meta_data();
		foreach ($meta as $key => $value) {
			$this->data[$key] = $value;
		}

		$this->folder_view = 'lembur/';
		$this->folder_model = 'lembur/';
		$this->folder_ajax_view = $this->folder_view . 'ajax/';

		$this->akses = array();

		$this->load->helper("karyawan_helper");
		$this->load->helper("tanggal_helper");
		$this->load->helper("cutoff_helper");
		$this->load->helper("string");

		$this->load->model($this->folder_model . "/M_perencanaan_lembur");
		$this->load->model($this->folder_model . "/M_tabel_perencanaan_lembur");
		$this->load->model($this->folder_model . "/M_tabel_perencanaan_lembur_detail");
		$this->load->model("master_data/M_mst_kategori_lembur", 'kategori_lembur');

		$this->data['success'] = "";
		$this->data['warning'] = "";

		$this->data["is_with_sidebar"] = true;
	}

    public function index() {
		$this->data['judul'] = "Perencanaan Lembur";
		$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		$this->akses = akses_helper($this->data['id_modul']);

		izin($this->akses["akses"]);

		$this->data["akses"] = $this->akses;
		$this->data["filter_periode"] = $this->M_perencanaan_lembur->filter_periode();
		$this->data["navigasi_menu"] = menu_helper();
		$this->data['content'] = $this->folder_view . "perencanaan_lembur/index";
		// array_push($this->data['js_sources'], "lembur/perencanaan_lembur/index");
        $this->load->view('template', $this->data);
	}

    public function get_data() {
		// Mengambil data dari model
		$list = $this->M_tabel_perencanaan_lembur->get_datatables();
		$no = $_POST['start'];
	
		// Mengambil semua id perencanaan lembur
		$perencanaan_lembur_ids = array_map(function($e) {
			return $e->id;
		}, $list);
	
		// Inisialisasi array kosong untuk evidence data
		$evidence_data = [];
	
		if($perencanaan_lembur_ids!=[]){
			// Mengambil semua evidence dari tabel ess_perencanaan_lembur_evidence
			$this->db->where_in('perencanaan_lembur_id', $perencanaan_lembur_ids);
			$evidence_query = $this->db->get('ess_perencanaan_lembur_evidence');
			foreach ($evidence_query->result() as $row) {
				if (!isset($evidence_data[$row->perencanaan_lembur_id])) {
					$evidence_data[$row->perencanaan_lembur_id] = [];
				}
				$evidence_data[$row->perencanaan_lembur_id][] = $row->evidence;
			}
		
			// Mengambil data dari tabel ess_perencanaan_lembur jika tidak ada di ess_perencanaan_lembur_evidence
			$this->db->where_in('id', $perencanaan_lembur_ids);
			$perencanaan_query = $this->db->get('ess_perencanaan_lembur');
			foreach ($perencanaan_query->result() as $row) {
				if (!isset($evidence_data[$row->id])) {
					$evidence_data[$row->id] = [$row->evidence];
				}
			}
		}
	
		foreach ($list as $key => $val) {
			$no++;
			$list[$key]->no = $no;
	
			// Mengambil evidence berdasarkan id perencanaan lembur
			$evidence_paths = isset($evidence_data[$val->id]) ? $evidence_data[$val->id] : [];
	
			if (count($evidence_paths) > 1) {
				// Jika lebih dari satu file, buat tombol untuk download ZIP
				$list[$key]->button_file = '<a class="btn btn-sm btn-warning" href="' . base_url('lembur/perencanaan_lembur/download_zip/' . $val->id) . '">Download ZIP</a>';
			} elseif (count($evidence_paths) == 1 && is_file($evidence_paths[0])) {
				// Jika hanya satu file dan itu file
				$list[$key]->button_file = '<a class="btn btn-sm btn-warning" href="' . base_url($evidence_paths[0]) . '">Download PDF</a>';
			} else {
				// Jika tidak ada file evidence ditemukan
				$list[$key]->button_file = 'File Tidak Ditemukan';
			}
		}
	
		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->M_tabel_perencanaan_lembur->count_all(),
			"recordsFiltered" => $this->M_tabel_perencanaan_lembur->count_filtered(),
			"data" => $list,
		);
		echo json_encode($output);
	}

	public function download_zip($id) {
		// Mengambil evidence dari tabel ess_perencanaan_lembur_evidence
		$this->db->where('perencanaan_lembur_id', $id);
		$evidence_query = $this->db->get('ess_perencanaan_lembur_evidence');
		$evidence_paths = [];
		foreach ($evidence_query->result() as $row) {
			$evidence_paths[] = $row->evidence;
		}
	
		// Jika tidak ada evidence di ess_perencanaan_lembur_evidence, cek di ess_perencanaan_lembur
		if (empty($evidence_paths)) {
			$this->db->where('id', $id);
			$perencanaan_query = $this->db->get('ess_perencanaan_lembur');
			foreach ($perencanaan_query->result() as $row) {
				$evidence_paths[] = $row->evidence;
			}
		}
	
		if (count($evidence_paths) > 1) {
			// Path to the .zip file in the temporary_cache directory
			$temporary_cache_dir = 'uploads/dwnld_perencanaan_file_zip';
			if (!is_dir($temporary_cache_dir)) {
				mkdir($temporary_cache_dir, 0777, true); // Create directory if it doesn't exist
			}
			$zip_file = $temporary_cache_dir . '/File_NDE_' . $id . '.zip';
	
			// Create a new ZipArchive instance
			$zip = new ZipArchive();
			if ($zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
				foreach ($evidence_paths as $evidence_path) {
					if (is_file($evidence_path)) {
						$zip->addFile($evidence_path, basename($evidence_path));
					}
				}
				$zip->close();
				// Send the file to the browser as a download
				header('Content-Type: application/zip');
				header('Content-disposition: attachment; filename=' . basename($zip_file));
				header('Content-Length: ' . filesize($zip_file));
				readfile($zip_file);
			} else {
				echo 'Gagal Membuat File ZIP';
			}
		} else {
			echo 'File NDE Tidak Ditemukan atau hanya satu file';
		}
	}	

    public function input_perencanaan_lembur() {
        $this->data['judul'] = "Perencanaan Lembur";
		$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		$this->akses = akses_helper($this->data['id_modul']);

		izin($this->akses["akses"]);

		// sto
		$sess_grup = $this->session->userdata('grup');
		if($sess_grup==3){ // Admin SDM - Remunerasi
			$this->db->group_start();
				$this->db->where("SUBSTR(object_abbreviation,1,1) <>", '0');
				$this->db->where("SUBSTR(object_abbreviation,2,4)", '0000');
			$this->db->group_end();
			$this->db->or_group_start();
				$this->db->where("SUBSTR(object_abbreviation,2,1) <>", '0');
				$this->db->where("SUBSTR(object_abbreviation,3,3)", '000');
			$this->db->group_end();
		} else if($sess_grup==31){ // Admin - Perencanaan Lembur Divisi
			$sess_kode_unit = $this->session->userdata('kode_unit');
			// $this->db->where("SUBSTR(object_abbreviation,1,1)", substr($sess_kode_unit, 0, 1));
			$kode_divisi = substr($sess_kode_unit, 0, 2). '000';
			$this->db->where("object_abbreviation", $kode_divisi);
		}
		$sto = $this->db->where('object_type', 'O')->get('ess_sto')->result();
		$this->data['sto'] = $sto;

		// karyawan
		if($sess_grup==3){ // Admin SDM - Remunerasi
			
		} else if($sess_grup==31){ // Admin - Perencanaan Lembur Divisi
			$sess_kode_unit = $this->session->userdata('kode_unit');
			$this->db->like("kode_unit", substr($sess_kode_unit, 0, 2), 'AFTER');
		}
		$mst_karyawan = $this->db->select('no_pokok, nama, kode_unit, nama_unit')->get('mst_karyawan')->result();
		$this->data['mst_karyawan'] = $mst_karyawan;

		// periode lembur
		$current_date = date('Y-m-d');
		$periods = get_surrounding_periods('2024-03-02', $current_date, 4, 4);
		$this->data['current_date'] = $current_date;
		$this->data['periode_lembur'] = $periods;

		// kategori lembur
		$this->data['kategori_lembur'] = $this->kategori_lembur->get_all();

		$this->data["akses"] = $this->akses;
		$this->data["navigasi_menu"] = menu_helper();
		// $this->data['content'] = $this->folder_view . "perencanaan_lembur/input";
		$this->data['content'] = $this->folder_view . "perencanaan_lembur/input_period";
		// array_push($this->data['js_sources'], "lembur/perencanaan_lembur/index");
        
        $this->load->view('template', $this->data);
    }

    public function simpan_data_perencanaan() {
		$status = false;
		$message = '';
	
		// Validasi input
		$this->form_validation->set_rules('kode_unit', 'Kode Unit', 'required');
		if(!$this->input->post('uuid', true)) {
			if (empty($_FILES['evidence']['name'])) {
				$this->form_validation->set_rules('evidence', 'NDE', 'required');
			}
		}
	
		if ($this->form_validation->run() == FALSE) {
			$message = 'Inputan belum lengkap';
		} else {
			$id = null;
			$kode_unit = $this->input->post('kode_unit', true);
			$dir = "uploads/perencanaan_lembur/";
	
			// Membuat direktori jika belum ada
			if (!is_dir($dir)) {
				mkdir($dir, 0777, true);
			}
	
			$config['upload_path'] = $dir;
			$config['allowed_types'] = 'pdf';
			$config['encrypt_name'] = true;
	
			// Load upload library
			$this->load->library('upload');
			$evidence_files = [];
	
			// Membuat folder baru untuk menyimpan semua file yang diunggah
			$folder_name = uniqid('upload_', true); // Membuat nama folder unik
			$new_dir = $dir . $folder_name . '/';
			mkdir($new_dir, 0777, true); // Membuat direktori baru untuk file-file
	
			foreach ($_FILES['evidence']['name'] as $key => $value) {
				if (!empty($value)) {
					$_FILES['file']['name'] = $_FILES['evidence']['name'][$key];
					$_FILES['file']['type'] = $_FILES['evidence']['type'][$key];
					$_FILES['file']['tmp_name'] = $_FILES['evidence']['tmp_name'][$key];
					$_FILES['file']['error'] = $_FILES['evidence']['error'][$key];
					$_FILES['file']['size'] = $_FILES['evidence']['size'][$key];
	
					// Initialize upload library with new file array
					$this->upload->initialize($config);
	
					// Upload file
					if ($this->upload->do_upload('file')) {
						$files = $this->upload->data();
	
						// Rename file and move to directory
						$new_filename = $files['file_name'];
						rename($dir . $new_filename, $new_dir . $new_filename);
	
						// Save file path to array
						$evidence_files[] = $new_dir . $new_filename;
					} else {
						// Failed to upload file
						$message = $this->upload->display_errors();
						echo json_encode([
							'status' => $status,
							'message' => $message
						]);
						exit;
					}
				}
			}
	
			// Prepare data for insertion or update
			$data_insert_perencanaan = [
				'uuid' => $this->uuid->v4(),
				'kode_unit' => $kode_unit,
				'tanggal_mulai' => $this->input->post('tanggal_mulai', true),
				'tanggal_selesai' => $this->input->post('tanggal_selesai', true),
				'created_at' => date('Y-m-d H:i:s')
			];
	
			// Insert data perencanaan lembur
			$id_perencanaan = $this->M_perencanaan_lembur->insert_perencanaan($data_insert_perencanaan);
	
			if ($id_perencanaan) {
				// Insert data evidence
				$data_insert_evidence = [];
				foreach ($evidence_files as $file_path) {
					$data_insert_evidence[] = [
						'id' => $this->uuid->v4(), // Generate UUID for each evidence entry
						'perencanaan_lembur_id' => $id_perencanaan,
						'evidence' => $file_path,
						'created_at' => date('Y-m-d H:i:s')
					];
				}
				$this->M_perencanaan_lembur->insert_batch_perencanaan_evidence($data_insert_evidence);
	
				// Process further as needed
				$detail = json_decode($this->input->post('detail', true), true);
				foreach ($detail as $key => $value) {
					$detail[$key]['id'] = $this->uuid->v4();
					$detail[$key]['perencanaan_lembur_id'] = $id_perencanaan;
					$detail[$key]['total_jam_lembur'] = (int)$value['jumlah_karyawan'] * (int)$value['jam_lembur'];
					$detail[$key]['created_at'] = date('Y-m-d H:i:s');
				}

				$tanggal_mulai = $this->input->post('tanggal_mulai', true);
				$tanggal_selesai = $this->input->post('tanggal_selesai', true);
				$filter_detail = array_filter($detail, function ($item) use ($tanggal_mulai, $tanggal_selesai) {
					return $item['tanggal'] >= $tanggal_mulai && $item['tanggal'] <= $tanggal_selesai;
				});

				// delete existing data
				$this->M_perencanaan_lembur->update_detail(['deleted_at'=>date('Y-m-d H:i:s')], ['perencanaan_lembur_id'=>$id, 'deleted_at'=>null]);

				// then insert new data
				if($filter_detail!=[]) {
					$insert_detail = $this->M_perencanaan_lembur->insert_multiple_detail($filter_detail);
					if($insert_detail==true){
						// hard delete
						$this->M_perencanaan_lembur->hard_delete_detail(['perencanaan_lembur_id'=>$id, 'deleted_at!='=>null]);

						// update nama jenis lembur
						$this->M_perencanaan_lembur->update_nama_jenis_lembur();
					}
				}
				$status = true;
				$message = 'Berhasil Disimpan';
			} else{
				$message = 'Gagal Disimpan';
			}			
		}
	
		// Return JSON response
		echo json_encode([
			'status' => $status,
			'message' => $message
		]);
	}	
	
	// detail
	function detail($id = null){
		if($id){
			$detail = $this->M_perencanaan_lembur->get_perencanaan(['uuid'=>$id, 'deleted_at'=>null])->row();
			if($detail){
				$this->data['judul'] = "Perencanaan Lembur";
				$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
				$this->akses = akses_helper($this->data['id_modul']);

				izin($this->akses["akses"]);

				$this->db->where("object_abbreviation", $detail->kode_unit);
				$sto = $this->db->where('object_type', 'O')->get('ess_sto')->row();
				$this->data['sto'] = $sto;

				$mst_karyawan = $this->db->select('kry.no_pokok, kry.nama')
					->join('ess_perencanaan_lembur_detail detail', "FIND_IN_SET(kry.no_pokok,detail.list_np) AND detail.perencanaan_lembur_id = $detail->id AND detail.deleted_at IS NULL")
					->group_by('kry.no_pokok, kry.nama')
					->get('mst_karyawan kry')->result();
				$this->data['mst_karyawan'] = $mst_karyawan;

				$this->data['perencanaan'] = $detail;

				// kategori lembur
				$this->data['kategori_lembur'] = $this->kategori_lembur->get_all();

				$this->data["akses"] = $this->akses;
				$this->data["navigasi_menu"] = menu_helper();
				$this->data['content'] = $this->folder_view . "perencanaan_lembur/detail";
				
				$this->load->view('template', $this->data);
			} else{
				echo 'Not Found';
			}
		} else{
			echo 'Missing Parameter';
		}
	}

	public function get_data_karyawan() {
		$list 	= $this->M_tabel_perencanaan_lembur_detail->get_datatables();
		$no = $_POST['start'];

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->M_tabel_perencanaan_lembur_detail->count_all(),
			"recordsFiltered" => $this->M_tabel_perencanaan_lembur_detail->count_filtered(),
			"data" => $list,
		);
		echo json_encode($output);
	}
	// END detail

	// edit
	function edit($id = null){
		if($id){
			// if(date('Y-m-d') >= date('Y-m-10')){
			// 	echo 'Sudah Cut Off';
			// 	exit;
			// }
			$perencanaan = $this->M_perencanaan_lembur->get_perencanaan(['uuid'=>$id, 'deleted_at'=>null])->row();
			if($perencanaan){
				$this->data['judul'] = "Perencanaan Lembur";
				$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
				$this->akses = akses_helper($this->data['id_modul']);

				izin($this->akses["akses"]);

				// sto
				$sess_grup = $this->session->userdata('grup');
				if($sess_grup==3){ // Admin SDM - Remunerasi
					$this->db->group_start();
						$this->db->where("SUBSTR(object_abbreviation,1,1) <>", '0');
						$this->db->where("SUBSTR(object_abbreviation,2,4)", '0000');
					$this->db->group_end();
					$this->db->or_group_start();
						$this->db->where("SUBSTR(object_abbreviation,2,1) <>", '0');
						$this->db->where("SUBSTR(object_abbreviation,3,3)", '000');
					$this->db->group_end();
				} else if($sess_grup==31){ // Admin - Perencanaan Lembur Divisi
					$sess_kode_unit = $this->session->userdata('kode_unit');
					$kode_divisi = substr($sess_kode_unit, 0, 2). '000';
					$this->db->where("object_abbreviation", $kode_divisi);
				}
				$sto = $this->db->where('object_type', 'O')->get('ess_sto')->result();
				$this->data['sto'] = $sto;

				// karyawan
				if($sess_grup==3){ // Admin SDM - Remunerasi
			
				} else if($sess_grup==31){ // Admin - Perencanaan Lembur Divisi
					$sess_kode_unit = $this->session->userdata('kode_unit');
					$this->db->like("kode_unit", substr($sess_kode_unit, 0, 2), 'AFTER');
				}
				$mst_karyawan = $this->db->select('no_pokok, nama, kode_unit, nama_unit')->get('mst_karyawan')->result();
				$this->data['mst_karyawan'] = $mst_karyawan;

				// periode lembur
				$current_date = $perencanaan->tanggal_mulai;
				$periods = get_surrounding_periods('2024-03-02', $current_date, 4, 4);
				$this->data['current_date'] = $current_date;
				$this->data['periode_lembur'] = $periods;

				// kategori lembur
				$this->data['kategori_lembur'] = $this->kategori_lembur->get_all();
				
				$this->data['perencanaan'] = $perencanaan;
				$detail = $this->M_perencanaan_lembur->get_detail(['perencanaan_lembur_id'=>$perencanaan->id, 'deleted_at'=>null])->result();
				$this->data['perencanaan_detail'] = $detail;

				$this->data["akses"] = $this->akses;
				$this->data["navigasi_menu"] = menu_helper();
				// $this->data['content'] = $this->folder_view . "perencanaan_lembur/input";
				$this->data['content'] = $this->folder_view . "perencanaan_lembur/input_period";
				
				$this->load->view('template', $this->data);
			} else{
				echo 'Not Found';
			}
		} else{
			echo 'Missing Parameter';
		}
	}
	// END edit

	function hapus(){
		// if(date('Y-m-d') >= date('Y-m-10')){
		// 	echo json_encode([
		// 		'status'=>false,
		// 		'message'=>'Sudah Cut Off'
		// 	]);
		// 	exit;
		// }

		$id = $this->input->post('id', true);
		$this->db->trans_start();

		// Soft delete in ess_perencanaan_lembur table
		$this->db->where('id', $id);
		$this->db->update($this->M_perencanaan_lembur->table, ['deleted_at' => date('Y-m-d H:i:s')]);

		// Retrieve filenames from ess_perencanaan_lembur_evidence before deletion
		$this->db->select('evidence');
		$this->db->where('perencanaan_lembur_id', $id);
		$query = $this->db->get('ess_perencanaan_lembur_evidence'); // Use the correct table name
		$files = $query->result();

		// Soft delete in ess_perencanaan_lembur_evidence table
		$this->db->where('perencanaan_lembur_id', $id);
		$this->db->update('ess_perencanaan_lembur_evidence', ['deleted_at' => date('Y-m-d H:i:s')]);

		// Soft delete in ess_perencanaan_lembur_detail table
		$this->db->where('perencanaan_lembur_id', $id);
		$this->db->update('ess_perencanaan_lembur_detail', ['deleted_at' => date('Y-m-d H:i:s')]);
		
		if ($this->db->trans_status() === true) {
			$this->db->trans_complete();
			// Delete directories related to evidence files
			foreach ($files as $file) {
				$evidence_path = FCPATH . $file->evidence;
				$directory_to_delete = dirname($evidence_path); // Get directory path

				// Check if directory exists before attempting to delete
				if (is_dir($directory_to_delete)) {
					// Recursively delete directory
					$this->deleteDirectory($directory_to_delete);
				}
			}

			echo json_encode([
				'status' => true,
				'message' => 'Sudah dihapus'
			]);
		} else {
			$this->db->trans_rollback();
			echo json_encode([
				'status' => false,
				'message' => 'Gagal menghapus'
			]);
		}
	}

	// Function to recursively delete a directory and its contents
	function deleteDirectory($dir) {
		if (!file_exists($dir)) {
			return true;
		}

		if (!is_dir($dir)) {
			return unlink($dir);
		}

		foreach (scandir($dir) as $item) {
			if ($item == '.' || $item == '..') {
				continue;
			}

			if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
				return false;
			}
		}

		return rmdir($dir);
	}

	function hapus_detail(){
		$id = $this->input->post('id', true);
		$this->db->trans_start();
		$this->db->where('id', $id);
		$this->db->update($this->M_perencanaan_lembur->detail, ['deleted_at'=>date('Y-m-d H:i:s')]);
		if($this->db->trans_status()===true){
			$this->db->trans_complete();
			echo json_encode([
				'status'=>true,
				'message'=>'Sudah dihapus'
			]);
		} else{
			$this->db->trans_rollback();
			echo json_encode([
				'status'=>false,
				'message'=>'Gagal dihapus'
			]);
		}
	}

	function update_detail(){
		$status = false;
		$message = '';
		$this->form_validation->set_rules('id', 'ID', 'required');
		$this->form_validation->set_rules('list_np', 'NP', 'required');
		$this->form_validation->set_rules('jam_lembur', 'Jam Lembur', 'required');
		$this->form_validation->set_rules('jenis_hari', 'Jenis Hari', 'required');
		$this->form_validation->set_rules('mst_kategori_lembur_id', 'Jenis Lembur', 'required');
		$this->form_validation->set_rules('alasan_lembur', 'Alasan Lembur', 'required');
		if ($this->form_validation->run()==FALSE) {
			$message = 'Inputan belum lengkap';
		} else{
			$id = $this->input->post('id', true);
			$list_np = $this->input->post('list_np', true);
			$jam_lembur = $this->input->post('jam_lembur', true);
			$jenis_hari = $this->input->post('jenis_hari', true);
			$mst_kategori_lembur_id = $this->input->post('mst_kategori_lembur_id', true);
			$alasan_lembur = $this->input->post('alasan_lembur', true);

			$cek = $this->M_perencanaan_lembur->get_detail(['id'=>$id, 'deleted_at'=>null])->row();
			if($cek){
				if($jenis_hari=='libur'){
					if($jam_lembur <= 12){
						$data_insert = [
							'jam_lembur'=>$jam_lembur,
							'jenis_hari'=>$jenis_hari,
							'mst_kategori_lembur_id'=>$mst_kategori_lembur_id,
							'alasan_lembur'=>$alasan_lembur,
							'updated_at'=>date('Y-m-d H:i:s')
						];
						$this->M_perencanaan_lembur->update_detail($data_insert, ['id'=>$id]);
						$this->M_perencanaan_lembur->update_nama_jenis_lembur($id);
						$status = true;
						$message = 'Data telah disimpan';
					} else{
						echo json_encode([
							'status'=>false,
							'message'=>'Jumlah jam melebihi'
						]);
						exit;
					}
				} else{
					if( (int) $cek->jam_lembur == (int) $jam_lembur && $cek->jenis_hari == $jenis_hari ){
						$data_insert = [
							'jam_lembur'=>$jam_lembur,
							'jenis_hari'=>$jenis_hari,
							'mst_kategori_lembur_id'=>$mst_kategori_lembur_id,
							'alasan_lembur'=>$alasan_lembur,
							'updated_at'=>date('Y-m-d H:i:s')
						];
						$this->M_perencanaan_lembur->update_detail($data_insert, ['id'=>$id]);
						$this->M_perencanaan_lembur->update_nama_jenis_lembur($id);
						$status = true;
						$message = 'Data telah disimpan';
					} else{
						$perencanaan_id = $cek->perencanaan_lembur_id;
						$array_list_np = explode(',', $list_np);
						$errorValue = 0;
						$successValue = 0;
						foreach ($array_list_np as $key => $np) {
							$findNp = $this->db->select('SUM(jam_lembur) AS jam_lembur')
								->where('deleted_at IS NULL',null,false)
								->where('perencanaan_lembur_id',$perencanaan_id)
								->where('id!=',$id)
								->where('jenis_hari','kerja')
								->where("FIND_IN_SET('{$np}', list_np)",null,false)
								->get('ess_perencanaan_lembur_detail')->row();
							if( (@$findNp ? (int)$findNp->jam_lembur : 0) + (int) $jam_lembur <=18 ){
								$successValue++;
							} else{
								$errorValue++;
							}
						}

						if($errorValue > 0){
							$message = 'Jumlah jam melebihi';
						} else{
							$data_insert = [
								'jam_lembur'=>$jam_lembur,
								'jenis_hari'=>$jenis_hari,
								'mst_kategori_lembur_id'=>$mst_kategori_lembur_id,
								'alasan_lembur'=>$alasan_lembur,
								'updated_at'=>date('Y-m-d H:i:s')
							];
							$this->M_perencanaan_lembur->update_detail($data_insert, ['id'=>$id]);
							$this->M_perencanaan_lembur->update_nama_jenis_lembur($id);
							$status = true;
							$message = 'Data telah disimpan';
						}
					}
				}
			} else{
				$message = 'Data tidak ditemukan';
			}
		}

		echo json_encode([
			'status'=>$status,
			'message'=>$message
		]);
	}

	function cek_perencanaan(){
		$kode_unit = $this->input->post('kode_unit', true);
		$tanggal_mulai = $this->input->post('tanggal_mulai', true);
		$tanggal_selesai = $this->input->post('tanggal_selesai', true);

		$this->db->where(
			[
				'kode_unit'=>$kode_unit, 
				'tanggal_mulai'=>$this->input->post('tanggal_mulai', true), 
				'tanggal_selesai'=>$this->input->post('tanggal_selesai', true), 
				'deleted_at'=>null
			]
		);
		if(@$this->input->post('perencanaan_id', true)){
			$this->db->where('id!=', $this->input->post('perencanaan_id', true));
		}
		$cek = $this->db->get($this->M_perencanaan_lembur->table)->row();
		if($cek){
			$unit = $this->db->select('object_name')->where('object_abbreviation', $kode_unit)->get('ess_sto')->row();
			$nama_unit = $unit->object_name ?: '';
			echo json_encode([
				'status'=>false,
				'message'=>"Perencanaan {$nama_unit} periode ".tanggal_indonesia($this->input->post('tanggal_mulai', true))." s/d ".tanggal_indonesia($this->input->post('tanggal_selesai', true))." sudah pernah diinput"
			]);
		} else{
			echo json_encode([
				'status'=>true,
				'message'=>'OK'
			]);
		}
	}

	// simpan import
	function simpan_excel() {
		$status = false;
		$message = '';
		$data_success = [];
		$data_error = [];
		$real_data = [];
	
		// Aturan validasi untuk field 'kode_unit' dan 'evidence'
		$this->form_validation->set_rules('kode_unit', 'Kode Unit', 'required');
		if (!@$this->input->post('uuid', true)) {
			if (empty($_FILES['evidence']['name'][0])) {
				$this->form_validation->set_rules('evidence', 'NDE', 'required');
			}
		}
	
		// Aturan validasi untuk field 'excel_file'
		if (empty($_FILES['excel_file']['name'])) {
			$this->form_validation->set_rules('excel_file', 'Silakan pilih file Excel', 'required');
		}
	
		// Memeriksa jika validasi form gagal
		if ($this->form_validation->run() == FALSE) {
			$message = 'Inputan belum lengkap';
		} else {
			// Inisialisasi variabel
			$id = null;
			$kode_unit = $this->input->post('kode_unit', true);
			$dir = "uploads/perencanaan_lembur/";
			if (!is_dir($dir)) mkdir($dir, 0777);
	
			// Mengunggah file Excel
			$uploadExcelFile = $this->upload_file($dir, 'excel_file', 'xls|xlsx');
			if ($uploadExcelFile['status'] == true) {
				$excel_file = $uploadExcelFile['data']['full_path'];
			} else {
				echo json_encode([
					'status' => $status,
					'message' => $uploadExcelFile['message']
				]);
				exit;
			}
	
			// Memeriksa apakah ini operasi update berdasarkan 'uuid'
			if (@$this->input->post('uuid', true)) { // update
				$uuid = $this->input->post('uuid', true);
				$cek = $this->M_perencanaan_lembur->get_perencanaan(['uuid' => $uuid, 'deleted_at' => null])->row();
				if ($cek) {
					// Memeriksa duplikasi entri
					$this->db->where([
						'kode_unit' => $kode_unit,
						'tanggal_mulai' => $this->input->post('tanggal_mulai', true),
						'tanggal_selesai' => $this->input->post('tanggal_selesai', true),
						'deleted_at' => null
					]);
					$this->db->where('id!=', $cek->id);
					$cek_duplikat = $this->db->get($this->M_perencanaan_lembur->table)->row();
					if ($cek_duplikat) {
						$unit = $this->db->select('object_name')->where('object_abbreviation', $kode_unit)->get('ess_sto')->row();
						$nama_unit = $unit->object_name ?: '';
						echo json_encode([
							'status' => false,
							'message' => "Perencanaan {$nama_unit} periode " . tanggal_indonesia($this->input->post('tanggal_mulai', true)) . " s/d " . tanggal_indonesia($this->input->post('tanggal_selesai', true)) . " sudah pernah diinput"
						]);
						exit;
					}
	
					// Menyiapkan data untuk update
					$data_insert = [
						'kode_unit' => $kode_unit,
						'tanggal_mulai' => $this->input->post('tanggal_mulai', true),
						'tanggal_selesai' => $this->input->post('tanggal_selesai', true),
						'updated_at' => date('Y-m-d H:i:s')
					];
	
					// Mengunggah file PDF jika ada
					if (!empty($_FILES['evidence']['name'][0])) {
						foreach ($_FILES['evidence']['name'] as $key => $value) {
							if ($_FILES['evidence']['error'][$key] == 0) {
								$folder_name = uniqid('upload_', true); 
								$new_dir = $dir . $folder_name . '/';
								if (!is_dir($new_dir)) mkdir($new_dir, 0777, true);
	
								$_FILES['file']['name'] = $_FILES['evidence']['name'][$key];
								$_FILES['file']['type'] = $_FILES['evidence']['type'][$key];
								$_FILES['file']['tmp_name'] = $_FILES['evidence']['tmp_name'][$key];
								$_FILES['file']['error'] = $_FILES['evidence']['error'][$key];
								$_FILES['file']['size'] = $_FILES['evidence']['size'][$key];
	
								$uploadPdfFile = $this->upload_file($new_dir, 'file', 'pdf');
								if ($uploadPdfFile['status'] == true) {
									$evidence_path = $new_dir . $uploadPdfFile['data']['file_name'];
									$evidence_data = [
										'id' => $this->uuid->v4(),
										'perencanaan_lembur_id' => $cek->id,
										'evidence' => $evidence_path,
										'created_at' => date('Y-m-d H:i:s')
									];
									$this->db->insert('ess_perencanaan_lembur_evidence', $evidence_data);
								} else {
									echo json_encode([
										'status' => $status,
										'message' => $uploadPdfFile['message']
									]);
									exit;
								}
							}
						}
					}
	
					// Melakukan update data ke dalam database
					$this->M_perencanaan_lembur->update_perencanaan($data_insert, ['id' => $cek->id]);
					$id = $cek->id;
				} else {
					$message = 'Data perencanaan tidak ditemukan';
					echo json_encode([
						'status' => $status,
						'message' => $message
					]);
					exit;
				}
			} else { // insert
				// Memeriksa duplikasi entri
				$cek = $this->M_perencanaan_lembur->get_perencanaan([
					'kode_unit' => $kode_unit,
					'tanggal_mulai' => $this->input->post('tanggal_mulai', true),
					'tanggal_selesai' => $this->input->post('tanggal_selesai', true),
					'deleted_at' => null
				])->row();
				if ($cek) {
					$unit = $this->db->select('object_name')->where('object_abbreviation', $kode_unit)->get('ess_sto')->row();
					$nama_unit = $unit->object_name ?: '';
					$message = "Perencanaan {$nama_unit} periode " . tanggal_indonesia($this->input->post('tanggal_mulai', true)) . " s/d " . tanggal_indonesia($this->input->post('tanggal_selesai', true)) . " sudah pernah diinput";
					echo json_encode([
						'status' => $status,
						'message' => $message
					]);
					exit;
				}
	
				// Mengunggah file PDF
				if (!empty($_FILES['evidence']['name'][0])) {
					foreach ($_FILES['evidence']['name'] as $key => $value) {
						if ($_FILES['evidence']['error'][$key] == 0) {
							$folder_name = uniqid('upload_', true);
							$new_dir = $dir . $folder_name . '/';
							if (!is_dir($new_dir)) mkdir($new_dir, 0777, true);
	
							$_FILES['file']['name'] = $_FILES['evidence']['name'][$key];
							$_FILES['file']['type'] = $_FILES['evidence']['type'][$key];
							$_FILES['file']['tmp_name'] = $_FILES['evidence']['tmp_name'][$key];
							$_FILES['file']['error'] = $_FILES['evidence']['error'][$key];
							$_FILES['file']['size'] = $_FILES['evidence']['size'][$key];
	
							$uploadPdfFile = $this->upload_file($new_dir, 'file', 'pdf');
							if ($uploadPdfFile['status'] == true) {
								$evidence_paths[] = $new_dir . $uploadPdfFile['data']['file_name'];
							} else {
								echo json_encode([
									'status' => $status,
									'message' => $uploadPdfFile['message']
								]);
								exit;
							}
						}
					}
				}
	
				// Menyiapkan data untuk insert
				$data_insert = [
					'uuid' => $this->uuid->v4(),
					'kode_unit' => $kode_unit,
					'tanggal_mulai' => $this->input->post('tanggal_mulai', true),
					'tanggal_selesai' => $this->input->post('tanggal_selesai', true),
					'created_at' => date('Y-m-d H:i:s')
				];
				$id = $this->M_perencanaan_lembur->insert_perencanaan($data_insert);
	
				if ($id) {
					foreach ($evidence_paths as $evidence_path) {
						$evidence_data = [
							'id' => $this->uuid->v4(),
							'perencanaan_lembur_id' => $id,
							'evidence' => $evidence_path,
							'created_at' => date('Y-m-d H:i:s')
						];
						$this->db->insert('ess_perencanaan_lembur_evidence', $evidence_data);
					}
				} else {
					echo json_encode([
						'status' => $status,
						'message' => 'Gagal menyimpan perencanaan'
					]);
					exit;
				}
			}
	
			// Memproses data Excel dan melakukan insert ke dalam database
			if ($id) {
				$excel_data = $this->olah_excel_detail($id, $excel_file);
				$detail = array_map(function($item) use($id) {
					$item['perencanaan_lembur_id'] = $id;
					$item['jumlah_karyawan'] = 1;
					$item['total_jam_lembur'] = 1 * (int) $item['jam_lembur'];
					$item['created_at'] = date('Y-m-d H:i:s');
					return $item;
				}, $excel_data['ok']);
	
				if ($excel_data['ok'] != []) {
					$this->M_perencanaan_lembur->insert_multiple_detail($detail);
				}
	
				// Update nama jenis lembur di dalam database
				$this->M_perencanaan_lembur->update_nama_jenis_lembur();
	
				// Mengatur pesan sukses dan data
				$status = true;
				$message = 'Berhasil Disimpan';
				$data_success = $excel_data['ok'];
				$data_error = $excel_data['not_ok'];
				$real_data = $excel_data['real_data'];
			} else {
				$message = 'Gagal Disimpan';
			}
		}
	
		// Mengeluarkan respons JSON
		echo json_encode([
			'status' => $status,
			'message' => $message,
			'data_error' => $data_error
		]);
	}
		
	
	private function upload_file($uploadPath, $fieldName, $allowedTypes){
        $config['upload_path'] = $uploadPath;
        $config['allowed_types'] = $allowedTypes;
        $config['encrypt_name'] = true;

        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if (!$this->upload->do_upload($fieldName)) {
            return [
                'status' => false,
                'message' => $this->upload->display_errors(),
				'data' => []
            ];
        } else {
            return [
                'status' => true,
                'message' => 'File uploaded successfully',
                'data' => $this->upload->data()
            ];
        }
    }	

	private function olah_excel_detail($perencanaan_id, $excel_file){
		$perencanaan = $this->M_perencanaan_lembur->get_perencanaan(['id'=>$perencanaan_id])->row();
		$tanggal_mulai = $perencanaan->tanggal_mulai;
		$tanggal_selesai = $perencanaan->tanggal_selesai;
		$existing = $this->db->select('id, tanggal, list_np, jam_lembur, jenis_hari, mst_kategori_lembur_id, alasan_lembur')
			->where('deleted_at IS NULL',null,false)
			->where('perencanaan_lembur_id', $perencanaan_id)
			->where('tanggal >=', $tanggal_mulai)
			->where('tanggal <=', $tanggal_selesai)
			->get($this->M_perencanaan_lembur->detail)->result_array();

		$spreadsheet = IOFactory::load($excel_file);
		$sheet = $spreadsheet->getActiveSheet();
		$all = $sheet->toArray();
		
		$excel_error = [];
		$excel_data = [];
		$real_data = [];
		$fields = ['tanggal','list_np','jam_lembur','jenis_hari','mst_kategori_lembur_id','alasan_lembur'];
		
		for ($row = 11; $row <= count($all); $row++) {
			$rowData = [];
			$nullValue = 0;
			for ($col = 1; $col <= 6; $col++) {
				$cell = $sheet->getCellByColumnAndRow($col, $row);
				// if (Date::isDateTime($cell)) {
				// 	$date = Date::excelToDateTimeObject($cell->getValue());
				// 	$formattedDate = $date->format('Y-m-d');
				// 	$value = $formattedDate;
				// } else {
				// 	$value = $cell->getValue();
				// }
				if ($col == 3) { // Kolom jam_lembur berada di kolom ke-3 (sesuaikan jika berbeda)
					$value = preg_replace('/\s+/', '', $cell->getValue()); // Hilangkan semua whitespace dari nilai jam_lembur
					$rowData[$fields[($col-1)]] = is_numeric($value) ? (int)$value : 0; // Pastikan nilai jam_lembur adalah angka
				} else if (Date::isDateTime($cell)) {
					$date = Date::excelToDateTimeObject($cell->getValue());
					$formattedDate = preg_replace('/\s+/', '', $date->format('Y-m-d')); // Hilangkan whitespace pada tanggal
					$value = $formattedDate;
					$rowData[$fields[($col-1)]] = $value;
				} else {
					$value = preg_replace('/\s+/', '', $cell->getValue()); // Untuk string biasa, hilangkan whitespace juga
					$rowData[$fields[($col-1)]] = $value;
				}

				// $value = $sheet->getCellByColumnAndRow($col, $row)->getValue();
				$rowData['id'] = $this->uuid->v4();
				if($col==1) $rowData[$fields[($col-1)]] = date('Y-m-d', strtotime($value));
				else if(in_array($value, [2,4,6])) $rowData[$fields[($col-1)]] = (string) $value;
				else $rowData[$fields[($col-1)]] = $value;
				
				if($value) {}
				else $nullValue++;
			}

			if($nullValue < 6 && $rowData['tanggal'] >= date('Y-m-d', strtotime($tanggal_mulai)) && $rowData['tanggal'] <= date('Y-m-d', strtotime($tanggal_selesai))) {
				$real_data[] = $rowData;
				$array_list_np = array_map(function($np) {
					return preg_replace('/\s+/', '', $np);
				}, explode(',', (string) $rowData['list_np']));
				$tanggal = $rowData['tanggal'];
				$mst_kategori_lembur_id = $rowData['mst_kategori_lembur_id'];
				$jenis_hari = trim(strtolower($rowData['jenis_hari']));
				$jam_lembur = is_numeric($rowData['jam_lembur']) ? (int) $rowData['jam_lembur'] : 0;
				foreach ($array_list_np as $key => $np) {
					$findNpTanggal = array_filter($excel_data, function($item) use ($tanggal, $np, $mst_kategori_lembur_id) {
						return $item['tanggal'] == $tanggal && $item['list_np'] == (string) $np && $item['mst_kategori_lembur_id'] == $mst_kategori_lembur_id;
					});

					$findNpTanggalExist = array_filter($existing, function($item) use ($tanggal, $np, $mst_kategori_lembur_id) {
						return $item['tanggal'] == $tanggal && $item['list_np'] == (string) $np && $item['mst_kategori_lembur_id'] == $mst_kategori_lembur_id;
					});

					if(empty($findNpTanggal) && empty($findNpTanggalExist)){
						if($jenis_hari=='libur'){
							if($jam_lembur < 13){
								$newData = $rowData;
								$newData['list_np'] = (string) $np;
								$excel_data[] = $newData;
							} else{
								$newData = $rowData;
								$newData['reason'] = 'Jumlah jam melebihi';
								$excel_error[] = $newData;
							}
						} else if($jenis_hari=='kerja'){
							$sumByNp = array_reduce($excel_data, function ($acc, $item) {
								if ($item['jenis_hari'] == 'kerja') {
									$npList = explode(',', (string) $item['list_np']);
									foreach ($npList as $i) {
										$i = trim($i);
										if (isset($acc["{$i}"])) {
											$acc["{$i}"] += is_numeric($item['jam_lembur']) ? (int) $item['jam_lembur'] : 0;
										} else {
											$acc["{$i}"] = is_numeric($item['jam_lembur']) ? (int) $item['jam_lembur'] : 0;
										}
									}
								}
								return $acc;
							}, []);

							$sumByNpExisting = array_reduce($existing, function ($acc, $item) {
								if ($item['jenis_hari'] == 'kerja') {
									$npList = explode(',', (string) $item['list_np']);
									foreach ($npList as $i) {
										$i = trim($i);
										if (isset($acc["{$i}"])) {
											$acc["{$i}"] += is_numeric($item['jam_lembur']) ? (int) $item['jam_lembur'] : 0;
										} else {
											$acc["{$i}"] = is_numeric($item['jam_lembur']) ? (int) $item['jam_lembur'] : 0;
										}
									}
								}
								return $acc;
							}, []);
							
							if(( (@$sumByNp["{$np}"] ?: 0) + (@$sumByNpExisting["{$np}"] ?: 0) + $jam_lembur ) < 19){
								$newData = $rowData;
								$newData['list_np'] = (string) $np;
								$excel_data[] = $newData;
							} else{
								$newData = $rowData;
								$newData['reason'] = 'Jumlah jam melebihi';
								$excel_error[] = $newData;
							}
						}
					} else{
						$newData = $rowData;
						$newData['reason'] = 'Data dobel';
						$excel_error[] = $newData;
					}
				}
			}
			else break;
		}

		return [
			'ok' => $excel_data,
			'not_ok' => $excel_error,
			'real_data' => $real_data
		];
	}

	function download_excel_daftar_karyawan($perencanaan_id = null){
		try {
			if($perencanaan_id){
				$spreadsheet = new Spreadsheet();
				$sheet = $spreadsheet->getActiveSheet();
				$style_col = [
					'font' => ['bold' => true],
					'alignment' => [
						'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
						'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
					],
					'borders' => [
						'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
						'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
						'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
						'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
					]
				];
				// Buat sebuah variabel untuk menampung pengaturan style dari isi tabel
				$style_row = [
					'alignment' => [
						'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
					],
					'borders' => [
						'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
						'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
						'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
						'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
					]
				];

				$perencanaan = $this->M_perencanaan_lembur->get_perencanaan_join_sto(['rencana.uuid'=>$perencanaan_id, 'rencana.deleted_at'=>null])->row();
				setlocale(LC_TIME, 'id_ID.UTF-8');
				$startDate = DateTime::createFromFormat('Y-m-d', $perencanaan->tanggal_mulai);
				$formattedStartDate = strftime('%d %b %Y', $startDate->getTimestamp());
				$endDate = DateTime::createFromFormat('Y-m-d', $perencanaan->tanggal_selesai);
				$formattedEndDate = strftime('%d %b %Y', $endDate->getTimestamp());

				$title = strtoupper("Daftar Karyawan Lembur " . $perencanaan->object_name . " Periode " . "{$formattedStartDate} - {$formattedEndDate}");

				$sheet->setCellValue('A1', 'DAFTAR KARYAWAN LEMBUR');
				$sheet->mergeCells('A1:F1');
				$sheet->getStyle('A1')->getFont()->setBold(true);
				$sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
				$sheet->setCellValue('A2', 'UNIT');
				$sheet->setCellValue('B2', ': '. strtoupper($perencanaan->object_name));
				$sheet->getStyle('A2')->getFont()->setBold(true);
				$sheet->getStyle('B2')->getAlignment()->setHorizontal('left');
				$sheet->setCellValue('A3', 'PERIODE');
				$sheet->setCellValue('B3', ': '. "{$formattedStartDate} - {$formattedEndDate}");
				$sheet->getStyle('A3')->getFont()->setBold(true);
				$sheet->getStyle('B3')->getAlignment()->setHorizontal('left');

				// Buat header tabel nya
				$sheet->setCellValue('A5', "Tanggal");
				$sheet->setCellValue('B5', "NP");
				$sheet->setCellValue('C5', "Jam Lembur");
				$sheet->setCellValue('D5', "Jenis Hari");
				$sheet->setCellValue('E5', "Jenis Lembur");
				$sheet->setCellValue('F5', "Alasan Lembur");
				// Apply style header yang telah kita buat tadi ke masing-masing kolom header
				$sheet->getStyle('A5')->applyFromArray($style_col);
				$sheet->getStyle('B5')->applyFromArray($style_col);
				$sheet->getStyle('C5')->applyFromArray($style_col);
				$sheet->getStyle('D5')->applyFromArray($style_col);
				$sheet->getStyle('E5')->applyFromArray($style_col);
				$sheet->getStyle('F5')->applyFromArray($style_col);

				$detail = $this->M_perencanaan_lembur->get_detail_join_karyawan(['detail.perencanaan_lembur_id'=>$perencanaan->id, 'detail.deleted_at'=>null])->result();

				$no = 1;
				$numrow = 6;
				foreach ($detail as $data) {
					$sheet->setCellValue('A' . $numrow, $data->tanggal);
					$sheet->setCellValue('B' . $numrow, "{$data->no_pokok} - {$data->nama}");
					$sheet->setCellValue('C' . $numrow, $data->jam_lembur);
					$sheet->setCellValue('D' . $numrow, $data->jenis_hari);
					$sheet->setCellValue('E' . $numrow, $data->jenis_lembur);
					$sheet->setCellValue('F' . $numrow, $data->alasan_lembur);

					// Apply style row yang telah kita buat tadi ke masing-masing baris (isi tabel)
					$sheet->getStyle('A' . $numrow)->applyFromArray($style_row);
					$sheet->getStyle('B' . $numrow)->applyFromArray($style_row);
					$sheet->getStyle('C' . $numrow)->applyFromArray($style_row);
					$sheet->getStyle('D' . $numrow)->applyFromArray($style_row);
					$sheet->getStyle('E' . $numrow)->applyFromArray($style_row);
					$sheet->getStyle('F' . $numrow)->applyFromArray($style_row);

					$no++;
					$numrow++;
				}
				// Set width kolom
				$sheet->getColumnDimension('A')->setAutoSize(true);
				$sheet->getColumnDimension('B')->setAutoSize(true);
				$sheet->getColumnDimension('C')->setAutoSize(true);
				$sheet->getColumnDimension('D')->setAutoSize(true);
				$sheet->getColumnDimension('E')->setAutoSize(true);
				$sheet->getColumnDimension('F')->setAutoSize(true);
				$spreadsheet->getActiveSheet()->freezePane('A6');

				// Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
				$sheet->getDefaultRowDimension()->setRowHeight(-1);
				// Set orientasi kertas jadi LANDSCAPE
				$sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
				// Set judul file excel nya
				$sheet->setTitle("Daftar Karyawan Lembur");
				$filename = "Daftar Karyawan Lembur Unit " . $perencanaan->object_name . " Periode " . "{$formattedStartDate} - {$formattedEndDate}" . " - ". date('YmdHis');
				// Proses file excel
				header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				header('Content-Disposition: attachment; filename="' . $filename . '.xlsx"'); // Set nama file excel nya
				header('Cache-Control: max-age=0');
				$writer = new Xlsx($spreadsheet);
				$writer->save('php://output');
			} else{
				echo 'Missing Parameter';
			}
		} catch (\Throwable $th) {
			throw $th;
		}
	}
}