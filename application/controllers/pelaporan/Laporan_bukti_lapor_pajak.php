<?php defined('BASEPATH') or exit('No direct script access allowed');

class Laporan_bukti_lapor_pajak extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();

		$meta = meta_data();
		foreach ($meta as $key => $value) {
			$this->data[$key] = $value;
		}

		$this->folder_view = 'pelaporan/laporan_bukti_lapor_pajak/';
		$this->folder_model = 'pelaporan/';
		$this->folder_controller = 'pelaporan/';

		$this->akses = array();

		$this->load->helper("cutoff_helper");
		$this->load->helper("tanggal_helper");
		$this->load->helper("karyawan_helper");
		$this->load->helper("reference_helper");

		$this->data["is_with_sidebar"] = true;

		$this->data['judul'] = "Laporan Bukti Lapor Pajak";
		$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		$this->akses = akses_helper($this->data['id_modul']);
		$this->nama_db = $this->db->database;
		izin($this->akses["akses"]);
	}

	public function index()
	{
		$this->load->model($this->folder_model . "M_pelaporan");

		$array_daftar_karyawan	= $this->M_pelaporan->select_daftar_karyawan();
		$array_daftar_agama	= $this->db->get('mst_agama');

		$this->data["akses"] 					= $this->akses;
		$this->data["navigasi_menu"] 			= menu_helper();
		$this->data['content'] 					= $this->folder_view . "lapor_pajak";
		$this->data['array_daftar_karyawan'] 	= $array_daftar_karyawan;
		$this->data['array_agama'] 				= $array_daftar_agama;
		$this->data['regulasi']					= @$this->db->where('id_laporan', $this->data['id_modul'])->get('mst_regulasi')->row()->regulasi;

		$this->load->view('template', $this->data);
	}

	public function tabel_lapor_pajak()
	{
		$this->load->model($this->folder_model . "M_tabel_lapor_pajak");
		if ($_SESSION["grup"] == 4) { //jika Pengadministrasi Unit Kerja
			$var = array();
			$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
			foreach ($list_pengadministrasi as $data) { //looping list_pengadministrasi
				array_push($var, $data['kode_unit']);
			}
		} else if ($_SESSION["grup"] == 5) { //jika Pengguna
			$var = $_SESSION["no_pokok"];
		} else {
			$var = 1;
		}

		$list = $this->M_tabel_lapor_pajak->get_datatables($var);
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $tampil) {
			$no++;
			$tahun			= trim($tampil->tahun);
			// $keterangan			= trim($tampil->keterangan);
			$no_tanda_terima_elektronik			= trim($tampil->no_tanda_terima_elektronik);

			$row = array();
			$row[] = $no;
			$row[] = $tampil->np_karyawan . ' - ' . $tampil->nama_karyawan . '<br><small>' . $tampil->nama_unit . '</small>';
			$row[] = $tahun;
			$row[] = $tampil->status_spt;
			$row[] = $no_tanda_terima_elektronik;
			
			$buttons = "<button class='btn btn-primary btn-xs detail_button' data-toggle='modal' data-target='#modal_detail' data-id=" . $tampil->id . ">Detail</button>";
			if($this->akses["hapus"]) $buttons .= "<button class='btn btn-danger btn-xs delete_button ml-2' type='button' data-id=" . $tampil->id . ">Hapus</button>";
			$row[] = $buttons;

			$data[] = $row;
		}

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->M_tabel_lapor_pajak->count_all($var),
			"recordsFiltered" => $this->M_tabel_lapor_pajak->count_filtered($var),
			"data" => $data,
		);
		//output to json format
		echo json_encode($output);
	}

	function action_insert_lapor_pajak()
	{
		// $thn = $this->input->post('tahun', true);
		// echo $thn;
		// exit;
		$this->load->helper('form');
		$this->load->library('form_validation');

		if ($this->akses["tambah"]) {
			$fail = array();
			$error = "";

			$this->form_validation->set_rules('np_karyawan', 'Karyawan', 'required', [
				'required' => '%s harus diisi.',
			]);
			$this->form_validation->set_rules('status_spt', 'Status SPT', 'required', [
				'required' => '%s harus diisi.',
			]);
			$this->form_validation->set_rules('tahun', 'Tahun', 'required', [
				'required' => '%s harus diisi.',
			]);
			// $this->form_validation->set_rules('no_tanda_terima_elektronik', 'No Tanda Terima Elektronik', 'required|exact_length[21]');
			$this->form_validation->set_rules('no_tanda_terima_elektronik', 'No Tanda Terima Elektronik', 'required', [
				'required' => '%s harus diisi.',
			]);
			$this->form_validation->set_error_delimiters('<div class="error">', '</div>');

			$np_karyawan = $this->input->post('np_karyawan', true);
			$tahun = $this->input->post('tahun', true);

			if(@$this->input->post('edit_id', true) == ''){
				$check_duplicate = $this->db
					->group_start()
						->group_start()
							->where('np_karyawan', $np_karyawan)
							->where('tahun', $tahun)
						->group_end()
						->or_where('no_tanda_terima_elektronik', $this->input->post('no_tanda_terima_elektronik', true))
					->group_end()
					->where('deleted_at', null)
					->get('laporan_bukti_lapor_pajak');
			} else{
				$check_duplicate = $this->db
					->group_start()
						->group_start()
							->where('np_karyawan', $np_karyawan)
							->where('tahun', $tahun)
						->group_end()
						->or_where('no_tanda_terima_elektronik', $this->input->post('no_tanda_terima_elektronik', true))
					->group_end()
					->where('id!=', $this->input->post('edit_id', true))
					->where('deleted_at', null)
					->get('laporan_bukti_lapor_pajak');
			}

			if ($check_duplicate->num_rows() > 0) {
				$row_check_duplicate = $check_duplicate->row();
				if($row_check_duplicate->no_tanda_terima_elektronik == $this->input->post('no_tanda_terima_elektronik', true)) $error_msg = "Nomor Tanda Terima Elektronik ".$this->input->post('no_tanda_terima_elektronik', true)." sudah pernah diinput.";
				else if($row_check_duplicate->tahun == $tahun) $error_msg = "Laporan bukti lapor pajak tahun {$tahun} sudah ada.";
				else $error_msg = 'Data sudah ada.';
				$this->session->set_flashdata('warning', $error_msg);
				redirect(base_url($this->folder_controller . 'laporan_bukti_lapor_pajak'));
				return;
			}

			if (($this->input->post('edit_id', true) == '' && $_FILES['surat_keterangan']['tmp_name'] == '') || $this->form_validation->run() == FALSE) {
				$this->session->set_flashdata('warning', 'Data Belum Lengkap');
				$this->index();
			} else {
				// $submit = $this->input->post('submit');
				// if ($submit) {
					$this->load->library('upload');

					if ($_FILES['surat_keterangan']['tmp_name'] != '') {
						$np_karyawan = $this->input->post('np_karyawan', true);
						$tahun = $this->input->post('tahun', true);

						$file_ext = pathinfo($_FILES['surat_keterangan']['name'], PATHINFO_EXTENSION);
						$file_name = md5(uniqid() . $np_karyawan . $tahun) . '.' . $file_ext;
						$upload_path = 'uploads/pelaporan/laporan_bukti_lapor_pajak/' . $np_karyawan;
						if (!is_dir($upload_path)) {
							mkdir($upload_path, 0755, true);
						}
						$full_file_path = $upload_path . '/' . $file_name;

						$config['upload_path'] = $upload_path;
						$config['file_name'] = $file_name;
						$config['allowed_types'] = 'pdf|jpg|png|jpeg';
						$config['max_size'] = '2048';

						$this->upload->initialize($config);

						if ($this->upload->do_upload('surat_keterangan')) {
							$up = $this->upload->data();
							$surat_keterangan = $full_file_path;
						} else {
							$error = $this->upload->display_errors();
						}
					}

					if ($error == "") {
						$data_insert = [];
						$np_karyawan		= $this->input->post('np_karyawan', true);
						$start_date			= date('Y-m-d');
						$end_date			= date('Y-m-d');
						$tahun_bulan     	= $start_date != null ? str_replace('-', '_', substr("$start_date", 0, 7)) : str_replace('-', '_', substr("$end_date", 0, 7));

						$data_insert = [
							'np_karyawan' => $np_karyawan,
							'nama_karyawan' => erp_master_data_by_np($np_karyawan, $start_date)['nama'],
							'personel_number' => erp_master_data_by_np($np_karyawan, $start_date)['personnel_number'],
							'nama_jabatan' => erp_master_data_by_np($np_karyawan, $start_date)['nama_jabatan'],
							'kode_unit' => erp_master_data_by_np($np_karyawan, $start_date)['kode_unit'],
							'nama_unit' => erp_master_data_by_np($np_karyawan, $start_date)['nama_unit'],

							'no_tanda_terima_elektronik' => $this->input->post('no_tanda_terima_elektronik', true),
							'status_spt' => $this->input->post('status_spt', true),
							'tahun' => $this->input->post('tahun', true),
						];

						if (@$surat_keterangan)
							$data_insert['surat_keterangan'] = $surat_keterangan;

						if ($this->input->post('edit_id', true) != '') {
							$data_lama = $this->db->where('id', $this->input->post('edit_id', true))->get('laporan_bukti_lapor_pajak')->row();

							$data_insert['updated_at'] = date('Y-m-d H:i:s');
							$data_insert['updated_by'] = $_SESSION['no_pokok'];
							$this->db->set($data_insert)->where('id', $this->input->post('edit_id', true))->update('laporan_bukti_lapor_pajak');
						} else {
							$data_insert['created_at'] = date('Y-m-d H:i:s');
							$data_insert['created_by'] = $_SESSION['no_pokok'];
							$this->db->set($data_insert)->insert('laporan_bukti_lapor_pajak');
						}

						$this->session->set_flashdata('success', "Berhasil Unggah Laporan Bukti Lapor Pajak");
						// echo $this->db->last_query();
						// exit;
						redirect(base_url($this->folder_controller . 'laporan_bukti_lapor_pajak'));
					} else {
						$this->session->set_flashdata('warning', "Terjadi Kesalahan Upload , $error");
						redirect(base_url($this->folder_controller . 'laporan_bukti_lapor_pajak'));
					}
				// } else {
				// 	$this->session->set_flashdata('warning', "Terjadi Kesalahan Input Data");
				// 	redirect(base_url($this->folder_controller . 'laporan_bukti_lapor_pajak'));
				// }
			}
		} else {
			$this->session->set_flashdata('warning', "Anda Tidak Memiliki Hak Akses");
			redirect(base_url($this->folder_controller . 'laporan_bukti_lapor_pajak'));
		}
	}

	public function view_detail()
	{
		$id = $this->input->post('id_');
		$tabel = 'laporan_bukti_lapor_pajak';

		$lap = $this->db->select("*")->where('id', $id)->get($tabel . ' a')->row_array();
		$data['detail'] = $lap;

		$base_upload_path = 'uploads/pelaporan/laporan_bukti_lapor_pajak/';
		$file_path = $lap['surat_keterangan'];
		$data['file_path'] = $file_path;


		$data["judul"] = ucwords(preg_replace("/_/", " ", __CLASS__));

		$this->load->view($this->folder_view . "detail_lapor_pajak", $data);
	}

	public function hapus()
	{
		$id = $this->input->post('id', true);
		$this->load->model($this->folder_model . "M_pelaporan");
		if (@$id != null) {
			$get = $this->M_pelaporan->ambil_by_id($id, 'laporan_bukti_lapor_pajak');
			$this->db->where('id', $id)->set(array('deleted_at' => date('Y-m-d H:i:s'), 'deleted_by' => $_SESSION['no_pokok']))->update("laporan_bukti_lapor_pajak");

			if ($this->db->affected_rows() > 0) {
				$old = $this->db->select('surat_keterangan')->where('id',$id)->get('laporan_bukti_lapor_pajak')->row();
				if(is_file($old->surat_keterangan)) unlink($old->surat_keterangan);

				$return["status"] = true;

				$log_data_lama = "";
				foreach ($get as $key => $value) {
					if (strcmp($key, "id") != 0) {
						if (!empty($log_data_lama)) {
							$log_data_lama .= "<br>";
						}
						$log_data_lama .= "{$key} = {$value}";
					}
				}

				$log = array(
					"id_pengguna" => $this->session->userdata("id_pengguna"),
					"id_modul" => $this->data['id_modul'],
					"id_target" => $get["id"],
					"deskripsi" => "hapus " . strtolower(preg_replace("/_/", " ", __CLASS__)),
					"kondisi_lama" => $log_data_lama,
					"kondisi_baru" => '',
					"alamat_ip" => $this->data["ip_address"],
					"waktu" => date("Y-m-d H:i:s")
				);
				$this->m_log->tambah($log);

				$return['status'] = true;
				$return['msg'] = 'Laporan Bukti Lapor Pajak Berhasil Dihapus';
			} else {
				$return['status'] = false;
				$return['msg'] = 'Laporan Bukti Lapor Pajak Gagal Dihapus';
			}
		} else {
			$return['status'] = false;
			$return['msg'] = 'Laporan Bukti Lapor Pajak Gagal Dihapus';
		}

		echo json_encode($return);
	}
}
