<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Agenda extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		$meta = meta_data();

		foreach ($meta as $key => $value) {
			$this->data[$key] = $value;
		}

		$this->folder_view = 'sikesper/agenda/';
		$this->folder_model = 'sikesper/';
		$this->folder_ajax_view = $this->folder_view . 'ajax/';
		$this->akses = array(/* 'scan'=>true,'ubah'=>true,'tambah'=>true,'lihat'=>true */);

		$this->load->helper("tanggal_helper");

		$this->load->model($this->folder_model . "/M_agenda");
		$this->load->model("poin_reward/m_manajemen_poin", "poin");

		$this->data['success'] = "";
		$this->data['warning'] = "";

		$this->data["is_with_sidebar"] = true;
	}

	public function index()
	{
		// echo '<pre>';
		// var_dump($this->session->userdata());
		// die;

		if (@$this->input->get('kegiatan')) {
			$this->data['kegiatan_name'] = urldecode($this->input->get('kegiatan'));
		}
		if (@$this->input->get('kegiatan_id')) {
			$this->data['kegiatan_id'] = $this->input->get('kegiatan_id');
		}

		$this->data['judul'] = "Agenda";
		$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		$this->akses = akses_helper($this->data['id_modul']);

		// izin($this->akses["akses"]);

		$this->data["akses"] = $this->akses;
		$this->data["navigasi_menu"] = menu_helper();
		$this->data['content'] = $this->folder_view . "agenda";

		array_push($this->data['js_sources'], "sikesper/daftar_obat");

		if ($this->input->post()) {

			if (!strcmp($this->input->post("aksi"), "tambah")) {
				/*
				izin( $this->akses["tambah"]);

				if (!strcmp($this->input->post("status"), "1")) {
					$this->data['status'] = true;
				} else if (!strcmp($this->input->post("status"), "0")) {
					$this->data['status'] = false;
				} else {
					$this->data['status'] = false;
				}

				$this->load->library('upload');
				$config['upload_path']   = "./uploads/images/sikesper/agenda/";
				$config['allowed_types'] = "png|jpg|jpeg";
				$config['max_size']      = "1000";
				$config['overwrite']     = true;
				$config['file_name']     = $this->input->post('agenda') . '-' . time();

				$this->upload->initialize($config);
				if ($this->upload->do_upload('image')) {

					$insert['image'] = $this->upload->data('file_name');
				} else {
					$insert['image'] = "";
				}

				$waktu_tanggal = str_replace('/', '-', $this->input->post('tanggal'));
				$waktu_tanggal = date('Y-m-d', strtotime($waktu_tanggal));

				$insert['agenda'] = $this->input->post('agenda');
				$insert['id_kategori'] = $this->input->post('id_kategori');
				$insert['deskripsi'] = $this->input->post('deskripsi');
				$insert['tanggal'] = $waktu_tanggal;
				$insert['waktu_mulai'] = $this->input->post('waktu_mulai');
				$insert['waktu_selesai'] = $this->input->post('waktu_selesai');
				$insert['kuota'] = ($this->input->post('kuota') > 0) ? $this->input->post('kuota') : null;
				$insert['lokasi'] = $this->input->post('lokasi');
				$insert['alamat'] = $this->input->post('alamat');
				$insert['provinsi'] = $this->input->post('id_provinsi');
				$insert['kabupaten'] = $this->input->post('id_kabupaten');
				$insert['longitude'] = $this->input->post('longitude');
				$insert['latitude'] = $this->input->post('latitude');
				$insert['poin'] = $this->input->post('poin');
				$insert['cp_nama'] = $this->input->post('cp_nama');
				$insert['cp_nomor'] = $this->input->post('cp_nomor');
				$insert['np_tergabung'] = $this->input->post('np_tergabung')?implode(',', $this->input->post('np_tergabung')):'all';
				$insert['status'] = $this->input->post('status') !== null ? $this->input->post('status') : 0;

				// echo '<pre>';
				// var_dump($insert);
				// die;

				if (@$this->input->post('is_berkala')) {
					$insert['is_berkala'] = 1;

					if (@$this->input->post('tanggal_berkala') != []) {
						$array_berkala = [];
						foreach ($this->input->post('tanggal_berkala') as $val) {
							$waktu_berkala = str_replace('/', '-', $val);
							$waktu_berkala = date('Y-m-d', strtotime($waktu_berkala));
							if ($waktu_berkala > $waktu_tanggal) {
								$array_berkala[] = $waktu_berkala;
							}
						}
						$send_to_tambah['tanggal_berkala'] = array_unique($this->input->post('tanggal_berkala'));
					}
				} else {
					$insert['is_berkala'] = 0;
				}

				$send_to_tambah['insert'] = $insert;

				$tambah = $this->tambah($send_to_tambah, $this->data['status']);

				$this->data['panel_tambah'] = "in";

				if ($tambah['status']) {
					$this->data['success'] = "Agenda <b>" . $insert['agenda'] . "</b> berhasil ditambahkan.";

					$notifikasi = (@$this->input->post('notifikasi', true) == 'on' ? 1 : 0);
					if ($notifikasi) {
						$tipe = "Agenda";
						$judul = $tipe . ' ' .  $this->data['agenda'];
						$pesan = "Berlangsung pada "  . tanggal_indonesia($this->data['tanggal']);
						$deskripsi = $this->data['deskripsi'];
						$kirim_notifikasi = $this->kirim_notifikasi($judul, $pesan, $deskripsi);

						if ($kirim_notifikasi['status']) {
							$this->data['success'] = $this->data['success'] . ' Pengumuman berhasil dikirimkan';
						} else {
							$this->data['success'] = $this->data['success'] . ' Pengumuman gagal dikirimkan';
						}
					}

					$this->data['panel_tambah'] = "";
					$this->data['agenda'] = "";
					$this->data['deskripsi'] = "";
					$this->data['tanggal'] = "";
					$this->data['jam'] = "";
					$this->data['waktu_mulai'] = "";
					$this->data['waktu_selesai'] = "";
					$this->data['kuota'] = "";
					$this->data['lokasi'] = "";
					$this->data['alamat'] = "";
					$this->data['provinsi'] = "";
					$this->data['kabupaten'] = "";
					$this->data['longitude'] = "";
					$this->data['latitude'] = "";
					$this->data['poin'] = "";
					$this->data['cp_nama'] = "";
					$this->data['cp_nomor'] = "";
					$this->data['status'] = "";
				} else {
					$this->data['warning'] = $tambah['error_info'];
				}
				*/
			} else if (!strcmp($this->input->post("aksi"), "ubah")) {
				/*
				izin($this->akses["ubah"]);

				$this->load->library('upload');
				$config['upload_path']   = "./uploads/images/sikesper/agenda/";
				$config['allowed_types'] = "png|jpg";
				$config['max_size']      = "1000";
				$config['overwrite']     = true;
				$config['file_name']     = $this->input->post('agenda') . '-' . time();

				$this->upload->initialize($config);
				if ($this->upload->do_upload('image')) {

					$insert['image'] = $this->upload->data('file_name');
				}

				$waktu_tanggal = str_replace('/', '-', $this->input->post('tanggal'));
				$waktu_tanggal = date('Y-m-d', strtotime($waktu_tanggal));

				$insert['id'] = $this->input->post('no');
				$insert['agenda'] = $this->input->post('agenda');
				$insert['id_kategori'] = $this->input->post('id_kategori');
				$insert['deskripsi'] = $this->input->post('deskripsi');
				$insert['tanggal'] = $waktu_tanggal;
				$insert['waktu_mulai'] = $this->input->post('waktu_mulai');
				$insert['waktu_selesai'] = $this->input->post('waktu_selesai');
				$insert['kuota'] = ($this->input->post('kuota') > 0) ? $this->input->post('kuota') : null;
				$insert['lokasi'] = $this->input->post('lokasi');
				$insert['alamat'] = $this->input->post('alamat');
				$insert['provinsi'] = $this->input->post('id_provinsi');
				$insert['kabupaten'] = $this->input->post('id_kabupaten');
				$insert['longitude'] = $this->input->post('longitude');
				$insert['latitude'] = $this->input->post('latitude');
				$insert['poin'] = $this->input->post('poin');
				$insert['cp_nama'] = $this->input->post('cp_nama');
				$insert['cp_nomor'] = $this->input->post('cp_nomor');
				$insert['status'] = $this->input->post('status');
				$insert['np_tergabung'] = $this->input->post('np_tergabung')?implode(',', $this->input->post('np_tergabung')):'all';

				if (!strcmp($this->input->post("status"), "1")) {
					$this->data['status'] = true;
				} else if (!strcmp($this->input->post("status"), "0")) {
					$this->data['status'] = false;
				}

				$ubah = $this->ubah($insert);

				if ($ubah["status"]) {
					$this->data['success'] = "Perubahan Agenda Berhasil Dilakukan.";
				} else {
					$this->data['warning'] = $ubah['error_info'];
				}

				$this->data['panel_tambah'] = "";
				$this->data['agenda'] = "";
				$this->data['deskripsi'] = "";
				$this->data['tanggal'] = "";
				$this->data['jam'] = "";
				$this->data['waktu_mulai'] = "";
				$this->data['waktu_selesai'] = "";
				$this->data['kuota'] = "";
				$this->data['lokasi'] = "";
				$this->data['alamat'] = "";
				$this->data['provinsi'] = "";
				$this->data['kabupaten'] = "";
				$this->data['longitude'] = "";
				$this->data['latitude'] = "";
				$this->data['poin'] = "";
				$this->data['cp_nama'] = "";
				$this->data['cp_nomor'] = "";
				$this->data['status'] = "";
				*/
			} else if (!strcmp($this->input->post("aksi"), "scan")) {
				/*
				izin($this->akses["scan"]);

				$admin = (object) [
					"np_karyawan" => $_SESSION["no_pokok"],
					"nama" => $_SESSION["nama"],
					"kode_unit" => $_SESSION["kode_unit"],
				];

				$kode_scan = $this->input->post("kode_scan");
				$result = $this->poin->scan_kode_agenda($kode_scan, $admin);

				if ($result["status"]) {
					$this->data['success'] =  $result['message'];
					$this->data['kode_scan'] = "";
				} else {
					$this->data['warning'] = $result['message'];
				}
				*/
			} else {
				$this->data['panel_tambah'] = "";
				$this->data['agenda'] = "";
				$this->data['deskripsi'] = "";
				$this->data['tanggal'] = "";
				$this->data['jam'] = "";
				$this->data['waktu_mulai'] = "";
				$this->data['waktu_selesai'] = "";
				$this->data['kuota'] = "";
				$this->data['lokasi'] = "";
				$this->data['alamat'] = "";
				$this->data['provinsi'] = "";
				$this->data['kabupaten'] = "";
				$this->data['longitude'] = "";
				$this->data['latitude'] = "";
				$this->data['poin'] = "";
				$this->data['cp_nama'] = "";
				$this->data['cp_nomor'] = "";
				$this->data['status'] = "";
			}
		} else {
			$this->data['panel_tambah'] = "";
			$this->data['agenda'] = "";
			$this->data['deskripsi'] = "";
			$this->data['tanggal'] = "";
			$this->data['jam'] = "";
			$this->data['waktu_mulai'] = "";
			$this->data['waktu_selesai'] = "";
			$this->data['kuota'] = "";
			$this->data['lokasi'] = "";
			$this->data['alamat'] = "";
			$this->data['provinsi'] = "";
			$this->data['kabupaten'] = "";
			$this->data['longitude'] = "";
			$this->data['latitude'] = "";
			$this->data['poin'] = "";
			$this->data['cp_nama'] = "";
			$this->data['cp_nomor'] = "";
			$this->data['status'] = "";
		}

		if ($this->akses["lihat"]) {
			$js_header_script = "
							<script>
								var config = {
									route:'" . base_url() . "'
								}
							</script>
							<script src='" . base_url('asset/js/agenda') . "/datatable.serverside.js'></script>
							<script src='" . base_url('asset/datatables/js') . "/reload.js'></script>
							<script src='" . base_url('asset/select2') . "/select2.min.js'></script>
							<script src='" . base_url('asset/sweetalert2') . "/sweetalert2.js'></script>
							<script>
								$(document).ready(function() {
									$('.select2').select2();
								});
							</script>";

			array_push($this->data["js_header_script"], $js_header_script);

			$this->data["daftar_lokasi"] = $this->M_agenda->daftar_lokasi();

			$log = array(
				"id_pengguna" => $this->session->userdata("id_pengguna"),
				"id_modul" => $this->data['id_modul'],
				"deskripsi" => "lihat " . strtolower(preg_replace("/_/", " ", __CLASS__)),
				"alamat_ip" => $this->data["ip_address"],
				"waktu" => date("Y-m-d H:i:s")
			);
			$this->m_log->tambah($log);
		}

		$this->load->view('template', $this->data);
	}

	public function form($action, $id = null)
	{
		$this->data['judul'] = "Agenda";
		$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		$this->akses = akses_helper($this->data['id_modul']);

		izin($this->akses["akses"]);

		$this->data["akses"] = $this->akses;
		$this->data["navigasi_menu"] = menu_helper();
		$this->data['content'] = $this->folder_view . "form";

		array_push($this->data['js_sources'], "sikesper/daftar_obat");

		$this->data['panel_tambah'] = "";
		$this->data['nama'] = "";
		$this->data['status'] = "";

		if ($this->akses["tambah"]) {
			$js_header_script = "
							<script src='" . base_url('asset/select2') . "/select2.min.js'></script>

							<script>
								$(document).ready(function() {
									$('.select2').select2();
								});
							</script>";

			array_push($this->data["js_header_script"], $js_header_script);

			$this->data["daftar_lokasi"] = $this->M_agenda->daftar_lokasi();

			if ($id != '') {
				$this->data['data_agenda'] = $this->M_agenda->cek_daftar_agenda($id);
				if ($this->data['data_agenda']) {
					$this->data["action"] = "ubah";
					$this->data["id"] = $id;
				} else {
					redirect(site_url('sikesper/agenda'));
				}
			} else {

				$this->data["action"] = "tambah";
			}

			$this->data['ref_karyawan'] = $this->db->select('nama, no_pokok')->distinct()->get('mst_karyawan')->result_array();
			$log = array(
				"id_pengguna" => $this->session->userdata("id_pengguna"),
				"id_modul" => $this->data['id_modul'],
				"deskripsi" => "lihat " . strtolower(preg_replace("/_/", " ", __CLASS__)),
				"alamat_ip" => $this->data["ip_address"],
				"waktu" => date("Y-m-d H:i:s")
			);
			$this->m_log->tambah($log);
		}
		$this->load->view('template', $this->data);
	}

	public function getAgenda()
	{
		$this->data['judul'] = "Agenda";
		$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		$this->akses = akses_helper($this->data['id_modul']);
		
		izin($this->akses["akses"]);
		
		$list = $this->M_agenda->get_datatables();
		$data = array();

		foreach ($list as $field) {

			//untuk menambah action button
			$action = '<a class="btn btn-success btn-xs detail" data-id="' . $field->id . '" data-toggle="modal" data-target="#modal-detail">Detail</a> ';

			//memberi action melihat peserta yang sudah terdaftar
			if ($field->jml_daftar > 0) {
				if ($this->session->userdata('grup') != 5) {
					$action .= '<button data-toggle="modal" data-target="#modal-detail" data-agd="' . $field->id . '" class="btn btn-info btn-xs daftar-peserta">Peserta</button>';
				}
			}

			//seleksi apakah agenda sudah terlewat tanggal hari ini
			if ($field->tanggal >= date('Y-m-d')) {
				if ($field->jml_daftar <= 0) {
					if ($this->akses["ubah"]) {
						$action .= '<a href="' . site_url('sikesper/agenda/form/ubah/' . $field->id) . '" class="btn btn-primary btn-xs">Ubah</a>
							<a data-key="' . $field->id . '" class="btn btn-danger btn-xs delete">Hapus</a>';
					}
				}

				//reminder kegiatan kurang berapa hari lagi
				$from = date_create(date('Y-m-d'));
				$to = date_create(date('Y-m-d', strtotime($field->tanggal)));

				$diff = date_diff($from, $to);
				$diff = $diff->format('%d');

				$hari = $diff <= 7 && $diff > 0 ? '<span class="label label-warning">' . $diff . ' hari lagi</span>' : '<span class="label label-primary">Hari ini</span>';
				//end
			} else {
				$hari = '';
				$action .= "<br /> <h5><span class='label label-warning'>Agenda Sudah Lewat Tanggal</span></h5>";
			}

			//nama agenda dan keterangan agenda
			$agenda = 'Kegiatan <strong>' . $field->agenda . '</strong> ' . $hari . '<br>';
			$agenda .= '<i class="fa fa-calendar"></i> ' . tanggal_indonesia($field->tanggal) . '  <i class="fa fa-clock-o"></i> ' . $field->waktu_mulai . ' s/d ' . $field->waktu_selesai . '<br>';
			$agenda .= '<i class="fa fa-map-marker"></i> ' . $field->alamat . ', ' . $field->nama_lokasi;

			if ($field->kuota > 0) {
				$agenda .= '<h4><span class="label label-danger">Sisa Kuota ' . ($field->kuota - $field->jml_daftar) . '</span></h4>';
			} else {
				$agenda .= '<h4><span class="label label-danger">Kuota Tidak Terbatas</span></h4>';
			}

			//keterangan pengguna sudah terdaftar / belum
			if ($this->session->userdata('grup') == 5) {
				$cek_peserta = $this->M_agenda->cekTerdaftarAgenda($this->session->userdata('no_pokok'), $field->id);

				if ($cek_peserta) {
					$agenda .= '<h4><span class="label label-success">Anda sudah terdaftar di agenda ini</span></h4>';
				}
			}

			$row = array();
			$row['agenda'] = $agenda;
			$row['action'] = $action;

			if ($this->session->userdata('grup') != 5) {
				$row['jml_daftar'] = $field->jml_daftar;
			}

			$data[] = $row;
		}

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->M_agenda->count_all(),
			"recordsFiltered" => $this->M_agenda->count_filtered(),
			"data" => $data,
		);

		header('Content-Type: application/json');
		header("Access-Control-Allow-Origin: *");
		//output dalam format JSON
		echo json_encode($output);
	}

	public function getAgendaa()
	{
		$this->data['judul'] = "Agenda";
		$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		$this->akses = akses_helper($this->data['id_modul']);

		izin($this->akses["akses"]);

		$list = $this->M_agenda->get_datatables();
		$data = array();

		foreach ($list as $field) {

			$action = '<a class="btn btn-success btn-xs detail" data-id="' . $field->id . '" data-toggle="modal" data-target="#modal-detail">Detail</a> ';

			if ($field->jml_daftar <= 0) {
				if ($this->akses["ubah"]) {
					$action .= '<a href="' . site_url('sikesper/agenda/form/ubah/' . $field->id) . '" class="btn btn-primary btn-xs">Ubah</a>
						<a data-key="' . $field->id . '" class="btn btn-danger btn-xs delete">Hapus</a>';
				}
			}

			$row = array();
			$row['agenda'] = $field->agenda;
			$row['tanggal'] = date('d M Y', strtotime($field->tanggal));
			$row['nama_lokasi'] = $field->nama_lokasi;
			$row['sisa_kuota'] = ($field->kuota - $field->jml_daftar);
			$row['action'] = $action;

			if ($this->session->userdata('grup') != 5) {
				$row['jml_daftar'] = $field->jml_daftar;
			}

			$data[] = $row;
		}

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->M_agenda->count_all(),
			"recordsFiltered" => $this->M_agenda->count_filtered(),
			"data" => $data,
		);

		header('Content-Type: application/json');
		header("Access-Control-Allow-Origin: *");
		//output dalam format JSON
		echo json_encode($output);
	}

	private function tambah($data)
	{
		$return = array("status" => false, "error_info" => "");

		$data['insert']['created'] = date('Y-m-d H:i:s');
		$data['insert']['created_by_np'] = $_SESSION["no_pokok"];
		$data['insert']['created_by_nama'] = $_SESSION["nama"];

		$id_ = $this->M_agenda->insert($data['insert']);

		if ($this->M_agenda->cek_daftar_agenda($id_)) {
			if (@$data['tanggal_berkala']) {
				$data_tanggal_berkala = [];
				foreach ($data['tanggal_berkala'] as $val) {
					$data_tanggal_berkala[] = [
						'kode' => $this->uuid->v4(),
						'ess_agenda_id' => $id_,
						'tanggal' => $val,
						'created_at' => date('Y-m-d H:i:s')
					];
				}
				$this->db->insert_batch('ess_agenda_berkala', $data_tanggal_berkala);
			}
			$return["status"] = true;

			$log_data_baru = "";

			foreach ($data['insert'] as $key => $value) {
				if (!empty($log_data_baru)) {
					$log_data_baru .= "<br>";
				}
				$log_data_baru .= "$key = $value";
			}

			$log = array(
				"id_pengguna" => $this->session->userdata("id_pengguna"),
				"id_modul" => $this->data['id_modul'],
				"id_target" => $id_,
				"deskripsi" => "tambah " . strtolower(preg_replace("/_/", " ", __CLASS__)),
				"kondisi_baru" => $log_data_baru,
				"alamat_ip" => $this->data["ip_address"],
				"waktu" => date("Y-m-d H:i:s")
			);

			$this->m_log->tambah($log);
		} else {
			$return["status"] = false;
			$return["error_info"] = "Penambahan Agenda <b>Gagal</b> Dilakukan.";
		}

		return $return;
	}

	private function ubah($data_update)
	{
		$return = array("status" => false, "error_info" => "");

		$cek = $this->M_agenda->cek_daftar_agenda($data_update['id']);
		if ($cek) {

			$arr_data_lama = $cek;
			$log_data_lama = "";

			foreach ($arr_data_lama as $key => $value) {
				if (!empty($log_data_lama)) {
					$log_data_lama .= "<br>";
				}
				$log_data_lama .= "$key = $value";
			}

			$data_update['updated'] = date('Y-m-d H:i:s');
			$data_update['updated_by_np'] = $_SESSION["no_pokok"];
			$data_update['updated_by_nama'] = $_SESSION["nama"];
			$update = $this->M_agenda->update($data_update, $data_update['id']);

			if ($update) {
				$return["status"] = true;

				$log_data_baru = "";
				foreach ($data_update as $key => $value) {
					if (!empty($log_data_baru)) {
						$log_data_baru .= "<br>";
					}
					$log_data_baru .= "$key = $value";
				}

				$log = array(
					"id_pengguna" => $this->session->userdata("id_pengguna"),
					"id_modul" => $this->data['id_modul'],
					"id_target" => $arr_data_lama->id,
					"deskripsi" => "ubah " . strtolower(preg_replace("/_/", " ", __CLASS__)),
					"kondisi_lama" => $log_data_lama,
					"kondisi_baru" => $log_data_baru,
					"alamat_ip" => $this->data["ip_address"],
					"waktu" => date("Y-m-d H:i:s")
				);
				$this->m_log->tambah($log);
			} else {
				$return["status"] = false;
				$return["error_info"] = "Perubahan Agenda <b>Gagal</b> Dilakukan.";
			}
		} else {
			$return["status"] = $cek["status"];
			$return["error_info"] = $cek["error_info"];
		}

		return $return;
	}

	public function detail($id)
	{
		header('Content-Type: application/json');
		header("Access-Control-Allow-Origin: *");

		$data = $this->M_agenda->cek_daftar_agenda($id);

		if ($data) {
			echo json_encode(['status' => 'success', 'result' => [
				'id' => $id,
				'nama' => $data->nama,
				'tipe' => $data->tipe,
				'no_telp' => $data->no_telp,
				'provinsi' => $data->id_provinsi,
				'nama_provinsi' => $data->provinsi,
				'kabupaten' => $data->id_kabupaten,
				'nama_kabupaten' => $data->kabupaten,
				'alamat' => $data->alamat,
				'catatan' => $data->catatan,
				'aktif' => $data->aktif,
				'jumlah_pendaftar' => $data->jml_daftar,
				'daftar' => $data->id_daftar
			]]);
		} else {
			echo json_encode(['status' => 'failed', 'result' => null]);
		}
	}

	public function show($id)
	{
		header('Content-Type: application/json');
		header("Access-Control-Allow-Origin: *");

		$data = $this->M_agenda->cek_daftar_agenda($id);

		if ($data) {
			$view = $this->load->view(
				$this->folder_view . 'detail',
				[
					'agenda' => $id,
					'data_agenda' => $data
				],
				TRUE
			);

			echo json_encode(['status' => 'success', 'result' => $view]);
		} else {
			echo json_encode(['status' => 'failed', 'result' => null]);
		}
	}

	public function daftarPeserta($id)
	{
		header('Content-Type: application/json');
		header("Access-Control-Allow-Origin: *");

		$data = $this->M_agenda->cek_daftar_agenda($id);

		if ($data) {
			$peserta = $this->M_agenda->daftarPeserta($id);
			$view = $this->load->view(
				$this->folder_view . 'peserta',
				[
					'agenda' => $id,
					'data_peserta' => $peserta
				],
				TRUE
			);

			echo json_encode(['status' => 'success', 'result' => $view]);
		} else {
			echo json_encode(['status' => 'failed', 'result' => null]);
		}
	}

	public function daftar_provinsi()
	{
		header('Content-Type: application/json');
		header("Access-Control-Allow-Origin: *");

		$data = $this->M_agenda->daftar_provinsi();

		echo json_encode(['results' => $data, 'status' => 200]);
	}

	public function daftar_kabupaten($provinsi)
	{
		header('Content-Type: application/json');
		header("Access-Control-Allow-Origin: *");

		if ($provinsi != '') {
			$data = $this->M_agenda->daftar_kabupaten($provinsi);

			echo json_encode(['results' => $data, 'status' => 200]);
		} else {
			echo json_encode(['results' => NULL, 'status' => 404]);
		}
	}

	public function kategoriAgenda()
	{
		header('Content-Type: application/json');
		header("Access-Control-Allow-Origin: *");

		$data = $this->M_agenda->kategoriAgenda();

		echo json_encode(['results' => $data, 'status' => 200]);
	}

	public function tahunAgenda()
	{
		header('Content-Type: application/json');
		header("Access-Control-Allow-Origin: *");

		$data = $this->M_agenda->tahunAgenda();

		echo json_encode(['results' => $data, 'status' => 200]);
	}

	public function daftarAgenda()
	{
		$no_pokok = $this->input->post('kry');
		$agenda = $this->input->post('agenda');

		$cek_karyawan = $this->M_agenda->cekDataKaryawan($no_pokok);
		$cek_agenda = $this->M_agenda->cek_daftar_agenda($agenda);

		if ($cek_karyawan && $cek_agenda) {
			$this->db->insert('ess_agenda_pendaftaran', [
				'id_agenda' => $agenda,
				'np_karyawan' => $no_pokok,
				'daftar_at' => date('Y-m-d H:i:s'),
				'created' => date('Y-m-d H:i:s')
			]);

			$response = ['results' => 'success', 'status' => 200];
		} else {
			$response = ['results' => 'data not found', 'status' => 204];
		}

		header('Content-Type: application/json');
		header("Access-Control-Allow-Origin: *");

		echo json_encode($response);
	}

	public function cekAgenda()
	{
		$no_pokok = $this->input->post('kry');
		$agenda = $this->input->post('agenda');

		$data_agenda = $this->M_agenda->cek_daftar_agenda($agenda);
		$terdaftar = $this->M_agenda->countPeserta($agenda);
		$cek_peserta = $this->M_agenda->cekTerdaftarAgenda($no_pokok, $agenda);

		if ($data_agenda) {
			if ($data_agenda->tanggal >= date('Y-m-d')) {
				if ($terdaftar) {
					if ($data_agenda->kuota > 0) {
						if ($data_agenda->kuota > $terdaftar->peserta) {
							if (!$cek_peserta) {
								$msg = 'tersedia';
							} else if ($cek_peserta) {
								$msg = 'terdaftar';
							}
						} else {
							$msg = 'penuh';
						}
					} else {
						if (!$cek_peserta) {
							$msg = 'tersedia';
						} else if ($cek_peserta) {
							$msg = 'terdaftar';
						}
					}
				} else {
					$msg = 'tersedia';
				}
			} else {
				$msg = 'lewat';
			}
		} else {
			$msg = 'Agenda tidak ditemukan';
		}

		header('Content-Type: application/json');
		header("Access-Control-Allow-Origin: *");

		echo json_encode(['response' => $msg, 'status' => 200]);
	}

	public function checkDisable()
	{
		$agenda = $this->input->post('agenda');

		$data_agenda = $this->M_agenda->cek_daftar_agenda($agenda);
		$terdaftar = $this->M_agenda->countPeserta($agenda);

		if ($data_agenda) {
			if ($terdaftar->peserta > 0) {
				$msg = 'tidak bisa';
			} else {
				$msg = 'bisa';
			}
		} else {
			$msg = 'Agenda tidak ditemukan';
		}

		header('Content-Type: application/json');
		header("Access-Control-Allow-Origin: *");

		echo json_encode(['response' => $msg, 'status' => 200]);
	}

	public function hapus($id)
	{

		$data = $this->M_agenda->cek_daftar_agenda($id);

		if ($data) {

			$update = $this->M_agenda->update([
				'status' => '0',
				'updated' => date('Y-m-d H:i:s')
			], $id);

			echo json_encode(['status' => 'success', 'result' => 200]);
		} else {
			echo json_encode(['status' => 'failed', 'result' => 200]);
		}
	}

	public function report_peserta_agenda($id)
	{
		// Load plugin PHPExcel nya
		include APPPATH . 'third_party/phpexcel/PHPExcel.php';

		// Panggil class PHPExcel nya
		$excel = new PHPExcel();

		// Settingan awal fil excel
		$excel->getProperties()->setCreator('PERURI ESS')
			->setLastModifiedBy('Admin PERURI ESS')
			->setTitle("Rekap Peserta Agenda")
			->setSubject("Rekap")
			->setDescription("Rekap Peserta Agenda")
			->setKeywords("Data Peserta");

		// Buat sebuah variabel untuk menampung pengaturan style dari header tabel
		$style_col = array(
			'font' => array('bold' => true), // Set font nya jadi bold
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
			),
			'borders' => array(
				'top' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border top dengan garis tipis
				'right' => array('style'  => PHPExcel_Style_Border::BORDER_THIN),  // Set border right dengan garis tipis
				'bottom' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border bottom dengan garis tipis
				'left' => array('style'  => PHPExcel_Style_Border::BORDER_THIN) // Set border left dengan garis tipis
			)
		);

		// Buat sebuah variabel untuk menampung pengaturan style dari isi tabel
		$style_row = array(
			'alignment' => array(
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
			),
			'borders' => array(
				'top' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border top dengan garis tipis
				'right' => array('style'  => PHPExcel_Style_Border::BORDER_THIN),  // Set border right dengan garis tipis
				'bottom' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border bottom dengan garis tipis
				'left' => array('style'  => PHPExcel_Style_Border::BORDER_THIN) // Set border left dengan garis tipis
			)
		);

		//ambil data agenda
		$agenda = $this->M_agenda->detailAgenda($id);

		$excel->setActiveSheetIndex(0)->setCellValue('A1', $agenda->agenda); // Set kolom A1
		$excel->getActiveSheet()->mergeCells('A1:G1'); // Set Merge Cell pada kolom A1 sampai E1
		$excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(TRUE); // Set bold kolom A1
		$excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(15); // Set font size 15 untuk kolom A1
		$excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); // Set text center untuk kolom A1

		$excel->setActiveSheetIndex(0)->setCellValue('A2', 'Tanggal: ' . ($agenda->tanggal ? tanggal_indonesia($agenda->tanggal) : '-'));
		$excel->getActiveSheet()->mergeCells('A2:G2');
		$excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(TRUE);
		$excel->getActiveSheet()->getStyle('A2')->getFont()->setSize(12);
		$excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		//filter

		$excel->setActiveSheetIndex(0)->setCellValue('A4', "NO");
		$excel->getActiveSheet()->getStyle('A4')->applyFromArray($style_col);

		// Buat header tabel nya pada baris ke 3
		$excel->setActiveSheetIndex(0)->setCellValue('B4', "NP");
		$excel->setActiveSheetIndex(0)->setCellValue('C4', "NAMA");
		$excel->setActiveSheetIndex(0)->setCellValue('D4', "JENIS KELAMIN");
		$excel->setActiveSheetIndex(0)->setCellValue('E4', "USIA");
		$excel->setActiveSheetIndex(0)->setCellValue('F4', "UNIT KERJA");
		$excel->setActiveSheetIndex(0)->setCellValue('G4', "VERIFIKASI HADIR");

		// Apply style header yang telah kita buat tadi ke masing-masing kolom header
		$excel->getActiveSheet()->getStyle('A4')->applyFromArray($style_col);
		$excel->getActiveSheet()->getStyle('B4')->applyFromArray($style_col);
		$excel->getActiveSheet()->getStyle('C4')->applyFromArray($style_col);
		$excel->getActiveSheet()->getStyle('D4')->applyFromArray($style_col);
		$excel->getActiveSheet()->getStyle('E4')->applyFromArray($style_col);
		$excel->getActiveSheet()->getStyle('F4')->applyFromArray($style_col);
		$excel->getActiveSheet()->getStyle('G4')->applyFromArray($style_col);
		// Panggil function view yang ada di SiswaModel untuk menampilkan semua data siswanya

		$no = 1; // Untuk penomoran tabel, di awal set dengan 1
		$numrow = 5;

		$peserta = $this->M_agenda->daftarPesertaAgenda($id);

		foreach ($peserta as $val) {
			$d1 = new DateTime($val->tanggal_lahir);
			$d2 = new DateTime(date('Y-m-d'));

			$diff = $d2->diff($d1);

			$excel->setActiveSheetIndex(0)->setCellValue('A' . $numrow, $no++);
			$excel->getActiveSheet()->getStyle('A' . $numrow)->applyFromArray($style_row);

			$excel->setActiveSheetIndex(0)->setCellValue('B' . $numrow, $val->no_pokok);
			$excel->getActiveSheet()->getStyle('B' . $numrow)->applyFromArray($style_row);

			$excel->setActiveSheetIndex(0)->setCellValue('C' . $numrow, $val->nama);
			$excel->getActiveSheet()->getStyle('C' . $numrow)->applyFromArray($style_row);

			$excel->setActiveSheetIndex(0)->setCellValue('D' . $numrow, $val->jenis_kelamin);
			$excel->getActiveSheet()->getStyle('D' . $numrow)->applyFromArray($style_row);

			$excel->setActiveSheetIndex(0)->setCellValue('E' . $numrow, $diff->y);
			$excel->getActiveSheet()->getStyle('E' . $numrow)->applyFromArray($style_row);

			$excel->setActiveSheetIndex(0)->setCellValue('F' . $numrow, $val->nama_unit);
			$excel->getActiveSheet()->getStyle('F' . $numrow)->applyFromArray($style_row);

			$excel->setActiveSheetIndex(0)->setCellValue('G' . $numrow, ($val->verifikasi_hadir == 1 ? 'Ya' : 'Tidak'));
			$excel->getActiveSheet()->getStyle('G' . $numrow)->applyFromArray($style_row);

			$numrow++;
		}

		// Set width kolom
		$excel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
		$excel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
		$excel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
		$excel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
		$excel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
		$excel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
		$excel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);

		// Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
		$excel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(-1);
		// Set orientasi kertas jadi LANDSCAPE
		$excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		// Set judul file excel nya
		$excel->getActiveSheet(0)->setTitle("Rekap Peserta Agenda");
		$excel->setActiveSheetIndex(0);

		// Proses file excel
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="Rekap-Peserta-Agenda.xlsx"'); // Set nama file excel nya
		header('Cache-Control: max-age=0');
		$write = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
		$write->save('php://output');
	}

	public function list_karyawan($id_agenda)
	{
		header('Content-Type: application/json');
		header("Access-Control-Allow-Origin: *");

		$data = [];
		$list = [];
		foreach ($_SESSION['list_pengadministrasi'] as $row) {
			$list[] = $row['kode_unit'];
		}

		if ($list != []) {
			$data = $this->db->select('no_pokok as id, nama as text')
				->where_in('kode_unit', $list)
				->where("(no_pokok NOT IN (SELECT np_karyawan FROM ess_agenda_pendaftaran WHERE id_agenda=$id_agenda AND batal_at IS NULL))")
				->get('mst_karyawan')->result();
		}

		echo json_encode(['results' => $data, 'status' => 200]);
	}

	function registrasi_karyawan()
	{
		header('Content-Type: application/json');
		$data = $this->input->post();
		$message = '';
		$id_agenda = $this->input->post('agenda', true);
		$kry = $this->input->post('kry', true);

		try {
			$data_agenda = $this->db->where('id', $id_agenda)->get('ess_agenda')->row();
			$count_terdaftar = $this->db->select('count(id) as jumlah')->where('id_agenda', $id_agenda)->where('batal_at is null', null, false)->get('ess_agenda_pendaftaran')->row();
			if ($data_agenda->kuota > 0) { // ada kuota
				$sisa_kuota = $data_agenda->kuota - $count_terdaftar->jumlah;
				if ($sisa_kuota > 0) {
					$jumlah_didaftarkan = count($kry);
					if ($jumlah_didaftarkan <= $sisa_kuota) {
						$this->insert_to_pendaftaran(['id_agenda' => $id_agenda, 'np_karyawan' => $kry]);
						$message .= '<span class="label label-success">Karyawan berhasil didaftarkan</span>';
					} else {
						$message .= '<span class="label label-danger">Karyawan yang Anda daftarkan melebihi sisa kuota</span>';
					}
				} else {
					$message .= '<span class="label label-danger">Kuota sudah penuh</span>';
				}
			} else { // kuota tidak terbatas
				$this->insert_to_pendaftaran(['id_agenda' => $id_agenda, 'np_karyawan' => $kry]);
				$message .= '<span class="label label-success">Karyawan berhasil didaftarkan</span>';
			}

			echo json_encode(['status' => 200, 'message' => $message, 'data' => $data]);
		} catch (Exception $e) {
			echo json_encode(['status' => 500, 'message' => 'Error', 'data' => []]);
		}
	}

	function insert_to_pendaftaran($array)
	{
		$data_insert = [];
		$id_agenda = $array['id_agenda'];
		$kry = $array['np_karyawan'];
		foreach ($kry as $value) {
			$data_insert[] = [
				'id_agenda' => $id_agenda,
				'np_karyawan' => $value,
				'daftar_at' => date('Y-m-d H:i:s'),
				'created' => date('Y-m-d H:i:s')
			];
		}
		$this->db->insert_batch('ess_agenda_pendaftaran', $data_insert);
	}

	function verifikasi_hadir()
	{
		header('Content-Type: application/json');
		$data = $this->input->post();
		$message = '';
		$id_agenda = $this->input->post('agenda', true);
		$kry = $this->input->post('kry', true);
		$table_name = 'ess_agenda_pendaftaran';

		try {
			# unset all 'verifikasi_hadir' field to 0
			$this->db->where('id_agenda', $id_agenda)->where('batal_at is null', null, false)->update($table_name, ['verifikasi_hadir' => 0]);

			if ($kry != []) {
				$this->db->where('id_agenda', $id_agenda)->where('batal_at is null', null, false)->where_in('np_karyawan', $kry)->update($table_name, ['verifikasi_hadir' => 1, 'verifikasi_by' => $_SESSION['no_pokok'], 'updated_at' => date('Y-m-d H:i:s')]);
			}

			if ($this->db->affected_rows() > 0) {
				$message .= '<span class="label label-success">Kehadiran karyawan berhasil diverifikasi</span>';
			} else {
				$message .= '<span class="label label-danger">Gagal melakukan verifikasi kehadiran</span>';
			}

			echo json_encode(['status' => 200, 'message' => $message, 'data' => $data]);
		} catch (Exception $e) {
			echo json_encode(['status' => 500, 'message' => 'Error', 'data' => []]);
		}
	}

	private function kirim_notifikasi($judul, $pesan, $deskripsi = '')
	{
		$created_by_np = $this->data_karyawan->np_karyawan;
		$new_id = $this->uuid->v4();
		$data = [];
		$data['judul'] = $judul;
		$data['pesan'] = $pesan;
		$data['deskripsi'] = $deskripsi;
		$data['kode'] = $new_id;
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['created_by_np'] = $created_by_np;
		$data['publish_at'] = date('Y-m-d H:i:s');
		$this->db->insert('mobile_pengumuman', $data);

		if ($this->db->affected_rows() > 0) {
			$this->db->query("INSERT INTO mobile_pengumuman_delivered (np, mobile_pengumuman_kode, created_at)
									SELECT no_pokok, '$new_id', NOW()
									FROM usr_pengguna 
									WHERE no_pokok IN (SELECT no_pokok FROM mst_karyawan)");

			# broadcast notification to user
			$curl = curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_URL => 'https://fcm.googleapis.com/fcm/send',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS => '{
							"notification": {
								"title": "' . $judul . '",
								"body": "' . $pesan . '",
								"click_action": "FLUTTER_NOTIFICATION_CLICK"
							},
							"priority": "high",
							"data": {
								"type" : "broadcast_message",
							},
							"to": "/topics/broadcast_message"
						}',
				CURLOPT_HTTPHEADER => array(
					'Content-Type: application/json',
					'Authorization: key=AAAAK1KRpvw:APA91bHL2jhnA3FbzRDzIbyQRjxpmqkDPUClO5XxxW1WABAy1_WzfZeV43L71AGo_QDBB0dR-j3QG8fLcB0GiaV94WUeoUP5wf99REFPfpqrxPcxJdKZdwxIjCUuZY-WIvcYrYN2THkm'
				),
			));
			curl_exec($curl);
			curl_close($curl);
			# END of broadcast notification to user
		}

		$result = [
			'status' => true,
			'message' => 'Pengumuman telah dibuat.',
			'data' => $data
		];
		return $result;
	}

	function simpan_agenda(){
		$this->data['judul'] = "Agenda";
		$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		$this->akses = akses_helper($this->data['id_modul']);
		// if(@$this->input->post('no',true)){
		if(!strcmp($this->input->post("aksi"), "ubah")){
			izin($this->akses["ubah"]);

			$this->load->library('upload');
			$config['upload_path']   = "./uploads/images/sikesper/agenda/";
			$config['allowed_types'] = "png|jpg";
			$config['max_size']      = "1000";
			$config['overwrite']     = true;
			$config['file_name']     = $this->input->post('agenda') . '-' . time();

			$this->upload->initialize($config);
			if ($this->upload->do_upload('image')) {

				$insert['image'] = $this->upload->data('file_name');
			}

			$waktu_tanggal = str_replace('/', '-', $this->input->post('tanggal'));
			$waktu_tanggal = date('Y-m-d', strtotime($waktu_tanggal));

			$insert['id'] = $this->input->post('no');
			$insert['agenda'] = $this->input->post('agenda');
			$insert['id_kategori'] = $this->input->post('id_kategori');
			$insert['deskripsi'] = $this->input->post('deskripsi');
			$insert['tanggal'] = $waktu_tanggal;
			$insert['waktu_mulai'] = $this->input->post('waktu_mulai');
			$insert['waktu_selesai'] = $this->input->post('waktu_selesai');
			$insert['kuota'] = ($this->input->post('kuota') > 0) ? $this->input->post('kuota') : null;
			$insert['lokasi'] = $this->input->post('lokasi');
			$insert['alamat'] = $this->input->post('alamat');
			$insert['provinsi'] = $this->input->post('id_provinsi');
			$insert['kabupaten'] = $this->input->post('id_kabupaten');
			$insert['longitude'] = $this->input->post('longitude');
			$insert['latitude'] = $this->input->post('latitude');
			$insert['poin'] = $this->input->post('poin');
			$insert['cp_nama'] = $this->input->post('cp_nama');
			$insert['cp_nomor'] = $this->input->post('cp_nomor');
			$insert['status'] = $this->input->post('status');
			$insert['np_tergabung'] = $this->input->post('np_tergabung')?implode(',', $this->input->post('np_tergabung')):'all';

			if (!strcmp($this->input->post("status"), "1")) {
				$this->data['status'] = true;
			} else if (!strcmp($this->input->post("status"), "0")) {
				$this->data['status'] = false;
			}

			$ubah = $this->ubah($insert);

			if ($ubah["status"]) {
				$this->data['success'] = "Perubahan Agenda Berhasil Dilakukan.";
				$this->session->set_flashdata('success', "Perubahan Agenda Berhasil Dilakukan.");
			} else {
				$this->data['warning'] = $ubah['error_info'];
				$this->session->set_flashdata('warning', $ubah['error_info']);
			}

			$this->data['panel_tambah'] = "";
			$this->data['agenda'] = "";
			$this->data['deskripsi'] = "";
			$this->data['tanggal'] = "";
			$this->data['jam'] = "";
			$this->data['waktu_mulai'] = "";
			$this->data['waktu_selesai'] = "";
			$this->data['kuota'] = "";
			$this->data['lokasi'] = "";
			$this->data['alamat'] = "";
			$this->data['provinsi'] = "";
			$this->data['kabupaten'] = "";
			$this->data['longitude'] = "";
			$this->data['latitude'] = "";
			$this->data['poin'] = "";
			$this->data['cp_nama'] = "";
			$this->data['cp_nomor'] = "";
			$this->data['status'] = "";
			redirect('sikesper/agenda');
		} else if(!strcmp($this->input->post("aksi"), "tambah")){
			izin( $this->akses["tambah"]);
			if (!strcmp($this->input->post("status"), "1")) {
				$this->data['status'] = true;
			} else if (!strcmp($this->input->post("status"), "0")) {
				$this->data['status'] = false;
			} else {
				$this->data['status'] = false;
			}
	
			$this->load->library('upload');
			$config['upload_path']   = "./uploads/images/sikesper/agenda/";
			$config['allowed_types'] = "png|jpg|jpeg";
			$config['max_size']      = "1000";
			$config['overwrite']     = true;
			$config['file_name']     = $this->input->post('agenda') . '-' . time();
	
			$this->upload->initialize($config);
			if ($this->upload->do_upload('image')) {
	
				$insert['image'] = $this->upload->data('file_name');
			} else {
				$insert['image'] = "";
			}
	
			$waktu_tanggal = str_replace('/', '-', $this->input->post('tanggal'));
			$waktu_tanggal = date('Y-m-d', strtotime($waktu_tanggal));
	
			$insert['agenda'] = $this->input->post('agenda');
			$insert['id_kategori'] = $this->input->post('id_kategori');
			$insert['deskripsi'] = $this->input->post('deskripsi');
			$insert['tanggal'] = $waktu_tanggal;
			$insert['waktu_mulai'] = $this->input->post('waktu_mulai');
			$insert['waktu_selesai'] = $this->input->post('waktu_selesai');
			$insert['kuota'] = ($this->input->post('kuota') > 0) ? $this->input->post('kuota') : null;
			$insert['lokasi'] = $this->input->post('lokasi');
			$insert['alamat'] = $this->input->post('alamat');
			$insert['provinsi'] = $this->input->post('id_provinsi');
			$insert['kabupaten'] = $this->input->post('id_kabupaten');
			$insert['longitude'] = $this->input->post('longitude');
			$insert['latitude'] = $this->input->post('latitude');
			$insert['poin'] = $this->input->post('poin');
			$insert['cp_nama'] = $this->input->post('cp_nama');
			$insert['cp_nomor'] = $this->input->post('cp_nomor');
			$insert['np_tergabung'] = $this->input->post('np_tergabung')?implode(',', $this->input->post('np_tergabung')):'all';
			$insert['status'] = $this->input->post('status') !== null ? $this->input->post('status') : 0;
	
			if (@$this->input->post('is_berkala')) {
				$insert['is_berkala'] = 1;
	
				if (@$this->input->post('tanggal_berkala') != []) {
					$array_berkala = [];
					foreach ($this->input->post('tanggal_berkala') as $val) {
						$waktu_berkala = str_replace('/', '-', $val);
						$waktu_berkala = date('Y-m-d', strtotime($waktu_berkala));
						if ($waktu_berkala > $waktu_tanggal) {
							$array_berkala[] = $waktu_berkala;
						}
					}
					$send_to_tambah['tanggal_berkala'] = array_unique($this->input->post('tanggal_berkala'));
				}
			} else {
				$insert['is_berkala'] = 0;
			}
	
			$send_to_tambah['insert'] = $insert;
	
			$tambah = $this->tambah($send_to_tambah, $this->data['status']);
	
			$this->data['panel_tambah'] = "in";
	
			if ($tambah['status']) {
				$this->data['success'] = "Agenda <b>" . $insert['agenda'] . "</b> berhasil ditambahkan.";
				$this->session->set_flashdata('success', "Agenda <b>" . $insert['agenda'] . "</b> berhasil ditambahkan.");
	
				$notifikasi = (@$this->input->post('notifikasi', true) == 'on' ? 1 : 0);
				if ($notifikasi) {
					$tipe = "Agenda";
					$judul = $tipe . ' ' .  $this->data['agenda'];
					$pesan = "Berlangsung pada "  . tanggal_indonesia($this->data['tanggal']);
					$deskripsi = $this->data['deskripsi'];
					$kirim_notifikasi = $this->kirim_notifikasi($judul, $pesan, $deskripsi);
	
					if ($kirim_notifikasi['status']) {
						$this->data['success'] = $this->data['success'] . ' Pengumuman berhasil dikirimkan';
						$this->session->set_flashdata('success', $this->data['success'] . ' Pengumuman berhasil dikirimkan');
					} else {
						$this->data['success'] = $this->data['success'] . ' Pengumuman gagal dikirimkan';
						$this->session->set_flashdata('success', $this->data['success'] . ' Pengumuman gagal dikirimkan');
					}
				}
	
				$this->data['panel_tambah'] = "";
				$this->data['agenda'] = "";
				$this->data['deskripsi'] = "";
				$this->data['tanggal'] = "";
				$this->data['jam'] = "";
				$this->data['waktu_mulai'] = "";
				$this->data['waktu_selesai'] = "";
				$this->data['kuota'] = "";
				$this->data['lokasi'] = "";
				$this->data['alamat'] = "";
				$this->data['provinsi'] = "";
				$this->data['kabupaten'] = "";
				$this->data['longitude'] = "";
				$this->data['latitude'] = "";
				$this->data['poin'] = "";
				$this->data['cp_nama'] = "";
				$this->data['cp_nomor'] = "";
				$this->data['status'] = "";
			} else {
				$this->data['warning'] = $tambah['error_info'];
				$this->session->set_flashdata('warning', $tambah['error_info']);
			}
			redirect('sikesper/agenda');
		} else if(!strcmp($this->input->post("aksi"), "scan")){
			izin($this->akses["scan"]);

			$admin = (object) [
				"np_karyawan" => $_SESSION["no_pokok"],
				"nama" => $_SESSION["nama"],
				"kode_unit" => $_SESSION["kode_unit"],
			];

			$kode_scan = $this->input->post("kode_scan");
			$result = $this->poin->scan_kode_agenda($kode_scan, $admin);

			if ($result["status"]) {
				$this->data['success'] =  $result['message'];
				$this->data['kode_scan'] = "";
				$this->session->set_flashdata('success', $result['message']);
			} else {
				$this->data['warning'] = $result['message'];
				$this->session->set_flashdata('warning', $result['message']);
			}
			redirect('sikesper/agenda');
		} else{
			$this->session->set_flashdata('warning', 'Aksi tidak diperbolehkan');
			redirect('sikesper/agenda');
		}
	}
}
