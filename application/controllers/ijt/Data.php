<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Data extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();

		$meta = meta_data();
		foreach ($meta as $key => $value) {
			$this->data[$key] = $value;
		}

		$this->folder_view = 'ijt/';
		$this->folder_model = 'ijt/';
		$this->folder_controller = 'ijt/';

		$this->akses = array();

		$this->load->helper("tanggal_helper");

		$this->load->model($this->folder_model . "m_internal_job_tender");

		$this->data["is_with_sidebar"] = true;

		$this->data['judul'] = "Data Job Tender";
		$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		$this->akses = akses_helper($this->data['id_modul']);
		izin($this->akses["akses"]);
	}

	public function index()
	{
		$this->data["akses"] 					= $this->akses;
		$this->data["navigasi_menu"] 			= menu_helper();
		$this->data['content'] 					= $this->folder_view . "data";
		$this->data['get_data'] 				= $this->m_internal_job_tender->get_data();
		$this->data['jabatan'] 					= $this->db->select('*')->get('mst_jabatan')->result();

		$this->load->view('template', $this->data);
	}

	public function action_insert_data()
	{

		$this->load->library('form_validation');

		$this->form_validation->set_rules('nama_jabatan', 'Nama Jabatan', 'required');
		$this->form_validation->set_rules('kode_jabatan', 'Kode Jabatan', 'required');
		$this->form_validation->set_rules('kode_unit', 'Kode Unit', 'required');
		$this->form_validation->set_rules('deskripsi', 'Deskripsi', 'required');
		$this->form_validation->set_rules('start_date', 'Start Date', 'required');
		$this->form_validation->set_rules('end_date', 'End Date', 'required');

		if ($this->form_validation->run() == FALSE) {
			$this->session->set_flashdata('warning', validation_errors());
			redirect(base_url($this->folder_controller . 'data'));
		} else {
			$nama_jabatan = html_escape($this->input->post('nama_jabatan'));
			$kode_jabatan = html_escape($this->input->post('kode_jabatan'));
			$kode_unit = html_escape($this->input->post('kode_unit'));
			$deskripsi = html_escape($this->input->post('deskripsi'));
			$start_date = date('Y-m-d', strtotime($this->input->post('start_date')));
			$end_date = date('Y-m-d', strtotime($this->input->post('end_date')));

			// $gambar_data = $this->upload_image();

			// $gambar_json = json_encode($gambar_data);

			$data_insert['nama_jabatan']		= $nama_jabatan;
			$data_insert['kode_unit']			= $kode_unit;
			$data_insert['kode_jabatan']		= $kode_jabatan;
			$data_insert['deskripsi']			= $deskripsi;
			$data_insert['start_date']			= $start_date;
			$data_insert['end_date']			= $end_date;
			// $data_insert['gambar']				= $gambar_json;

			$insert_data = $this->m_internal_job_tender->insert_data($data_insert);

			if ($insert_data != "0") {
				$this->session->set_flashdata('success', "Data berhasil ditambahkan.");
			} else {
				$this->session->set_flashdata('warning', "Data Gagal ditambahkan");
			}

			redirect(base_url($this->folder_controller . 'data'));
		}
	}

	private function upload_image()
	{
		$config['upload_path'] = './uploads/images/job_tender/';
		$config['allowed_types'] = 'jpg|jpeg|png';
		$config['max_size'] = 8192;
		$config['encrypt_name'] = TRUE;

		$this->load->library('upload', $config);

		if ($this->upload->do_upload('gambar')) {
			$data = $this->upload->data();
			return [
				'file_name' => $data['file_name'],
				'file_size' => $data['file_size'],
				'file_type' => $data['file_type'],
			];
		} else {
			return ['error' => $this->upload->display_errors()];
		}
	}

	public function action_apply_data()
	{
		$this->load->library(['upload', 'form_validation']);
		$this->load->model('m_internal_job_tender');

		$response = ['status' => false, 'errors' => [], 'message' => ''];
		$this->form_validation->set_rules('job_id', 'Job ID', 'required|integer', [
			'required' => 'Job ID wajib diisi.',
			'integer' => 'Job ID harus berupa angka.'
		]);

		if ($this->form_validation->run() == FALSE) {
			$response['errors'] = $this->form_validation->error_array();
			log_message('error', 'Validasi gagal: ' . json_encode($response['errors']));
		} else {
			$np = $this->session->userdata('no_pokok');
			$job_id = $this->input->post('job_id');
			$motivasi = html_escape($this->input->post('motivasi'));
			$upload_path = "./uploads/job_tender/$np/";

			if (!is_dir($upload_path)) {
				if (!mkdir($upload_path, 0755, true)) {
					log_message('error', "Gagal membuat folder: $upload_path");
					$response['message'] = "Gagal membuat folder penyimpanan.";
					echo json_encode($response);
					return;
				}
			}

			$apply_data = [
				'np' => $np,
				'job_id' => $job_id,
				'motivasi' => $motivasi,
				'created_at' => date('Y-m-d H:i:s')
			];

			$ijt_apply_id = $this->m_internal_job_tender->insert_apply($apply_data);

			if (!$ijt_apply_id) {
				log_message('error', 'Gagal menyimpan data ijt_apply: ' . json_encode($apply_data));
				$response['message'] = "Gagal menyimpan data pendaftaran.";
				echo json_encode($response);
				return;
			}

			$files_cv = $_FILES['file_cv'];
			$files_doc = $_FILES['file_doc'];
			$uploaded_files = [];

			function upload_files($files, $upload_path, $prefix)
			{
				$uploaded = [];
				for ($i = 0; $i < count($files['name']); $i++) {
					$_FILES['file_upload']['name'] = $files['name'][$i];
					$_FILES['file_upload']['type'] = $files['type'][$i];
					$_FILES['file_upload']['tmp_name'] = $files['tmp_name'][$i];
					$_FILES['file_upload']['error'] = $files['error'][$i];
					$_FILES['file_upload']['size'] = $files['size'][$i];

					$file_number = $i + 1;
					$file_name = "$prefix" . ($file_number > 1 ? "_$file_number" : "") . '-' . time();
					$config['upload_path'] = $upload_path;
					$config['allowed_types'] = "jpg|jpeg|pdf|png";
					$config['max_size'] = "8192";
					$config['file_name'] = $file_name;

					$ci = &get_instance();
					$ci->upload->initialize($config);

					if ($ci->upload->do_upload('file_upload')) {
						$upload_data = $ci->upload->data();
						$uploaded[] = [
							'file_name' => $upload_data['file_name'],
							'file_size' => $upload_data['file_size'],
							'file_type' => $upload_data['file_type'],
						];
					} else {
						log_message('error', "Upload gagal untuk file: " . $files['name'][$i] . " | Error: " . $ci->upload->display_errors());
					}
				}
				return $uploaded;
			}

			$uploaded_cv = upload_files($files_cv, $upload_path, 'cv');
			$uploaded_doc = upload_files($files_doc, $upload_path, 'dokumen_pendukung');

			if (empty($uploaded_cv) && empty($uploaded_doc)) {
				log_message('error', 'Tidak ada file yang berhasil diunggah.');
				$response['message'] = "Gagal mengunggah file.";
				echo json_encode($response);
				return;
			}

			$all_files = array_merge($uploaded_cv, $uploaded_doc);
			$counter_cv = 1;
			$counter_doc = 1;

			$data_dokumen = [];

			foreach ($all_files as $file) {
				$nama_dokumen = strpos($file['file_name'], 'cv') !== false ? "cv" : "dokumen_pendukung";
				$nama_dokumen .= ($nama_dokumen == "cv") ? "_$counter_cv" : "_$counter_doc";

				$data_dokumen[] = [
					'ijt_apply_id' => $ijt_apply_id,
					'nama_dokumen' => $nama_dokumen,
					'file' => json_encode($file),
					'created_at' => date('Y-m-d H:i:s')
				];

				if ($nama_dokumen == "cv_$counter_cv") {
					$counter_cv++;
				} else {
					$counter_doc++;
				}
			}

			log_message('debug', "Menyimpan data dokumen: " . json_encode($data_dokumen));

			$result = $this->m_internal_job_tender->apply_dokumen($data_dokumen);

			if ($result) {
				$response['status'] = true;
				$response['message'] = 'Data berhasil ditambahkan.';
			} else {
				log_message('error', 'Gagal menyimpan dokumen ke database: ' . json_encode($data_dokumen));
				$response['message'] = "Gagal menyimpan data.";
			}
		}

		echo json_encode($response);
	}




	// public function action_apply_data()
	// {
	// 	$this->load->library(['upload', 'form_validation']);

	// 	$response = ['status' => false, 'errors' => [], 'message' => ''];

	// 	// $this->form_validation->set_rules('motivasi', 'Motivasi', [
	// 	// 	'required' => 'Motivasi wajib diisi.',
	// 	// ]);

	// 	$this->form_validation->set_rules('job_id', 'Job ID', 'required|integer', [
	// 		'required' => 'Job ID wajib diisi.',
	// 		'integer' => 'Job ID harus berupa angka.'
	// 	]);

	// 	if ($this->form_validation->run() == FALSE) {
	// 		$response['errors'] = array_merge($response['errors'], $this->form_validation->error_array());
	// 	} else {
	// 		$np = $this->session->userdata('no_pokok');
	// 		$upload_path = "./uploads/job_tender/$np/";

	// 		if (!is_dir($upload_path)) {
	// 			mkdir($upload_path, 0755, true);
	// 		}

	// 		$config['upload_path'] = $upload_path;
	// 		$config['allowed_types'] = "jpg|jpeg|pdf|png";
	// 		$config['max_size'] = "8192";
	// 		$config['overwrite'] = true;
	// 		$config['file_name'] = $this->input->post('file_cv') . '-' . time();

	// 		$this->upload->initialize($config);

	// 		if (!$this->upload->do_upload('file_cv')) {
	// 			$response['errors']['file_cv'] = $this->upload->display_errors('', '');
	// 		} else {
	// 			$upload_data = $this->upload->data();

	// 			$data_insert['file_cv'] = [
	// 				'file_name' => $upload_data['file_name'],
	// 				'file_size' => $upload_data['file_size'],
	// 				'file_type' => $upload_data['file_type'],
	// 			];

	// 			$motivasi = html_escape($this->input->post('motivasi'));
	// 			$job_id = $this->input->post('job_id');

	// 			$data_insert['np'] = $np;
	// 			$data_insert['job_id'] = $job_id;
	// 			$data_insert['motivasi'] = $motivasi;
	// 			$data_insert['file_cv'] = json_encode($data_insert['file_cv']);

	// 			$insert_data = $this->m_internal_job_tender->apply_data($data_insert);

	// 			if ($insert_data != "0") {
	// 				$response['status'] = true;
	// 				$response['message'] = 'Data berhasil ditambahkan.';
	// 			} else {
	// 				$response['message'] = 'Data Gagal ditambahkan.';
	// 			}
	// 		}
	// 	}

	// 	echo json_encode($response);
	// }


	public function action_update_data()
	{
		$this->load->library('form_validation');

		$this->form_validation->set_rules('id', 'ID', 'required');
		$this->form_validation->set_rules('nama_jabatan', 'Nama Jabatan', 'required');
		$this->form_validation->set_rules('kode_jabatan', 'Kode Jabatan', 'required');
		$this->form_validation->set_rules('kode_unit', 'Kode Unit', 'required');
		$this->form_validation->set_rules('deskripsi', 'Deskripsi', 'required');
		$this->form_validation->set_rules('start_date', 'Start Date', 'required');
		$this->form_validation->set_rules('end_date', 'End Date', 'required');

		if ($this->form_validation->run() == FALSE) {
			$errors = [];
			foreach ($this->form_validation->error_array() as $field => $error) {
				$errors[$field] = $error;
			}
			$response = [
				'status' => false,
				'errors' => $errors
			];
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode($response));
			return;
		} else {
			$id = html_escape($this->input->post('id'));
			$nama_jabatan = html_escape($this->input->post('nama_jabatan'));
			$kode_jabatan = html_escape($this->input->post('kode_jabatan'));
			$kode_unit = html_escape($this->input->post('kode_unit'));
			$deskripsi = html_escape($this->input->post('deskripsi'));
			$start_date = date('Y-m-d', strtotime($this->input->post('start_date')));
			$end_date = date('Y-m-d', strtotime($this->input->post('end_date')));

			$current_data = $this->m_internal_job_tender->get_data_by_id($id);
			$gambar_lama = isset($current_data->gambar) ? $current_data->gambar : null;

			$data_update = [
				'nama_jabatan' => $nama_jabatan,
				'kode_jabatan' => $kode_jabatan,
				'kode_unit' => $kode_unit,
				'deskripsi' => $deskripsi,
				'start_date' => $start_date,
				'end_date' => $end_date,
			];

			// if (!empty($_FILES['gambar']['name'])) {
			// 	$gambar_data = $this->upload_image();

			// 	if (!isset($gambar_data['error'])) {
			// 		$data_update['gambar'] = json_encode($gambar_data);
			// 	} else {
			// 		$response = [
			// 			'status' => false,
			// 			'errors' => $gambar_data['error']
			// 		];
			// 		$this->output
			// 			->set_content_type('application/json')
			// 			->set_output(json_encode($response));
			// 		return;
			// 	}
			// } else {
			// 	$data_update['gambar'] = $gambar_lama;
			// }

			$update_result = $this->m_internal_job_tender->update_data($id, $data_update);

			if ($update_result) {
				$response = [
					'status' => true,
					'message' => "Data berhasil diperbarui."
				];
			} else {
				$response = [
					'status' => false,
					'errors' => "Data gagal diperbarui."
				];
			}
		}
		echo json_encode($response);
	}


	public function table_data()
	{
		$this->load->model($this->folder_model . "M_tabel_data");
		$this->load->model($this->folder_model . "M_internal_job_tender");

		$list 	= $this->M_tabel_data->get_datatables();

		$data = array();
		$no = @$_POST['start'];

		$current_date = date('Y-m-d');
		$no_pokok = $this->session->userdata('no_pokok');
		foreach ($list as $tampil) {
			$no++;

			$row = array(
				'no' => $no,
				'id' => $tampil->id,
				'nama_jabatan' => $tampil->nama_jabatan,
				'kode_unit' => @$tampil->kode_unit,
				'kode_jabatan' => @$tampil->kode_jabatan,
				'gambar' => json_decode(@$tampil->gambar),
				'jumlah_pendaftar' => $tampil->jumlah_pendaftar,
				'deskripsi' => $tampil->deskripsi,
				'm_date' => $tampil->start_date,
				'e_date' => $tampil->end_date,
				'start_date' => tanggal_indonesia($tampil->start_date),
				'end_date' => tanggal_indonesia($tampil->end_date),
			);

			$is_applied_info = $this->M_internal_job_tender->is_applied_info($no_pokok, $tampil->id);

			if ($this->session->userdata('grup') == '5') {
				$info_pengguna = '';
				if ($is_applied_info) {
					$info_pengguna .= "<span class='text-primary'>Anda Sudah Apply</span>";
					switch ($is_applied_info->is_verval) {
						case '1':
							$info_pengguna .= "<br><span class='text-primary'>dan Lolos Seleksi Administrasi</span>";
							break;
						case '2':
							$info_pengguna .= "<br><span class='text-danger'>dan Tidak Lolos Seleksi Administrasi</span>";
							break;
						default:
							break;
					}
				} elseif (strtotime($tampil->start_date) > strtotime($current_date)) {
					$info_pengguna .= "<span class='text-warning'>Job Tender Belum Dibuka</span>";
				}
				$row['info_pengguna'] = $info_pengguna;
			}

			if (@$this->akses['ubah'] || @$this->akses['unggah'] || @$this->akses['hapus'] || @$this->akses['lihat poster']) {
				$actions = array();
				if (@$this->akses['ubah']) {
					$actions[] = "<button class='btn btn-primary btn-sm btn-update'>Ubah</button>";
				}
				if (@$this->akses['unggah']) {
					if ($is_applied_info == null) {
						$actions[] = "<button class='btn btn-primary btn-sm btn-apply'>Apply</button>";
					}
				}
				if (@$this->akses['hapus']) {
					$actions[] = "<button class='btn btn-danger btn-sm btn-hapus'>Hapus</button>";
				}
				if (@$this->akses['lihat poster']) {
					$actions[] = "<a class='btn btn-primary btn-sm btn-lihat-poster'>Lihat Poster</a>";
				}
				$row['actions'] = implode(' ', $actions);
			}


			$data[] = $row;
		}

		$output = array(
			"draw" => @$_POST['draw'],
			"recordsTotal" => $this->M_tabel_data->count_all(),
			"recordsFiltered" => $this->M_tabel_data->count_filtered(),
			"data" => $data,
		);
		//output to json format
		echo json_encode($output);
	}

	function destroy($id)
	{
		$this->db->trans_begin();
		$data['deleted_at'] = date('Y-m-d H:i:s');

		$this->db->where('id', $id)->update('ijt_data', $data);

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();

			$this->session->set_flashdata('warning', "Gagal menghapus data!");
			redirect(base_url($this->folder_controller . 'data'));
		} else {
			$this->db->trans_commit();

			$this->session->set_flashdata('success', "Berhasil menghapus data!");
			redirect(base_url($this->folder_controller . 'data'));
		}
	}
}
