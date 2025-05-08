<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Manajemen_daily_quest extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		$meta = meta_data();
		foreach ($meta as $key => $value) {
			$this->data[$key] = $value;
		}

		$this->folder_view = 'poin_reward/';
		$this->folder_model = 'poin_reward/';
		$this->folder_ajax_view = $this->folder_view . 'ajax/';
		$this->akses = array();

		$this->load->helper("tanggal_helper");

		$this->load->model($this->folder_model . "/m_manajemen_daily_quest");

		$this->data['success'] = "";
		$this->data['warning'] = "";

		$this->data["is_with_sidebar"] = true;
	}

	public function index()
	{
		$this->data['judul'] = "Manajemen Daily Quest";
		$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		$this->akses = akses_helper($this->data['id_modul']);

		izin($this->akses["akses"]);

		$this->data["akses"] = $this->akses;
		$this->data["navigasi_menu"] = menu_helper();
		$this->data['content'] = $this->folder_view . "manajemen_daily_quest";

		array_push($this->data['js_sources'], "poin_reward/manajemen_daily_quest");

		if ($this->input->post()) {

			$dir = 'uploads/images/daily_quest/';

			if (!is_dir('./' . $dir)) {
				mkdir('./' . $dir, 0777, true);
			}

			if (!strcmp($this->input->post("aksi"), "tambah")) {
				izin($this->akses["tambah"]);

				$this->data['nama'] = $this->input->post("nama");
				$this->data['link'] = $this->input->post("link");
				$this->data['poin'] = $this->input->post("poin");
				$this->data['poin_harian'] = $this->input->post("poin_harian");
				$this->data['start_date'] = date('Y-m-d', strtotime($this->input->post('start_date')));
				$this->data['end_date'] = date('Y-m-d', strtotime($this->input->post('end_date')));

				if (!strcmp($this->input->post("status"), "aktif")) {
					$this->data['status'] = true;
				} else if (!strcmp($this->input->post("status"), "non aktif")) {
					$this->data['status'] = false;
				}

				// $this->load->library('upload');
				// $config['upload_path']   = "./uploads/images/daily_quest/";
				// $config['allowed_types'] = "png";
				// $config['max_size']      = "1000";
				// $config['overwrite']     = true;
				// $config['file_name']     = $this->input->post('nama') . '-' . time();

				// $this->upload->initialize($config);
				// if ($this->upload->do_upload('gambar')) {

				// 	$this->data['gambar'] = $this->upload->data('file_name');
				// } else {
					$this->data['gambar'] = null;
				// }

				$tambah = $this->tambah($this->data['nama'], $this->data['link'], $this->data['poin'], $this->data['poin_harian'], $this->data['start_date'], $this->data['end_date'], $this->data['gambar'], $this->data['status']);

				$this->data['panel_tambah'] = "in";

				if ($tambah['status']) {
					$this->data['success'] = "Daily Quest dengan nama <b>" . $this->data['nama'] . "</b> berhasil ditambahkan.";
					$this->data['panel_tambah'] = "";
					$this->data['nama'] = "";
					// $this->data['link'] = "";
					$this->data['poin'] = "";
					$this->data['poin_harian'] = "";
					$this->data['start_date'] = "";
					$this->data['end_date'] = "";
					$this->data['status'] = "";
				} else {
					$this->data['warning'] = $tambah['error_info'];
				}
			} else if (!strcmp($this->input->post("aksi"), "ubah")) {
				izin($this->akses["ubah"]);

				$this->data['nama'] = $this->input->post("nama");
				$this->data['nama_ubah'] = $this->input->post("nama_ubah");
				$this->data['link'] = $this->input->post("link");
				$this->data['link_ubah'] = $this->input->post("link_ubah");
				$this->data['poin'] = $this->input->post("poin");
				$this->data['poin_ubah'] = $this->input->post("poin_ubah");
				$this->data['poin_harian'] = $this->input->post("poin_harian");
				$this->data['poin_harian_ubah'] = $this->input->post("poin_harian_ubah");
				$this->data['start_date'] = $this->input->post("start_date");
				$this->data['start_date_ubah'] = $this->input->post("start_date_ubah");
				$this->data['end_date'] = $this->input->post("end_date");
				$this->data['end_date_ubah'] = $this->input->post("end_date_ubah");
				$this->data['status'] = (bool)$this->input->post("status");
				if (!strcmp($this->input->post("status_ubah"), "aktif")) {
					$this->data['status_ubah'] = true;
				} else if (!strcmp($this->input->post("status_ubah"), "non aktif")) {
					$this->data['status_ubah'] = false;
				}

				// $this->data['gambar'] = $this->input->post("gambar");

				// $this->load->library('upload');
				// $config['upload_path']   = "./uploads/images/daily_quest/";
				// $config['allowed_types'] = "png";
				// $config['max_size']      = "1000";
				// $config['overwrite']     = true;
				// $config['file_name']     = $this->input->post('nama') . '-' . time();

				// $this->upload->initialize($config);
				// if ($this->upload->do_upload('gambar_ubah')) {
				// 	$this->data['gambar_ubah'] = $this->upload->data('file_name');
				// } else {
					$this->data['gambar_ubah'] = null;
				// }

				$ubah = $this->ubah($this->data['nama'], $this->data['nama_ubah'], $this->data['poin'], $this->data['poin_ubah'], $this->data['poin_harian'], $this->data['poin_harian_ubah'], $this->data['link_ubah'], $this->data['start_date_ubah'], $this->data['end_date_ubah'], $this->data['gambar_ubah'], $this->data['status_ubah']);

				if ($ubah["status"]) {
					$this->data['success'] = "Perubahan Daily Quest berhasil dilakukan.";
				} else {
					$this->data['warning'] = $ubah['error_info'];
				}

				$this->data['panel_tambah'] = "";
				$this->data['nama'] = "";
				// $this->data['link'] = "";
				$this->data['poin'] = "";
				$this->data['poin_harian'] = "";
				$this->data['start_date'] = "";
				$this->data['end_date'] = "";
				$this->data['status'] = "";
			} else {
				$this->data['panel_tambah'] = "";
				$this->data['nama'] = "";
				// $this->data['link'] = "";
				$this->data['poin'] = "";
				$this->data['poin_harian'] = "";
				$this->data['start_date'] = "";
				$this->data['end_date'] = "";
				$this->data['status'] = "";
			}
		} else {
			$this->data['panel_tambah'] = "";
			$this->data['nama'] = "";
			// $this->data['link'] = "";
			$this->data['poin'] = "";
			$this->data['poin_harian'] = "";
			$this->data['start_date'] = "";
			$this->data['end_date'] = "";
			$this->data['status'] = "";
		}

		if ($this->akses["lihat"]) {
			$js_header_script = "
					<script src='" . base_url('asset/select2') . "/select2.min.js'></script>
					<script>
						$(document).ready(function() {
							$('#tabel_manajemen_daily_quest').DataTable({
								responsive: true
							});
							$('.select2').select2();
						});
					</script>";

			array_push($this->data["js_header_script"], $js_header_script);

			$this->data["daftar_manajemen_daily_quest"] = $this->m_manajemen_daily_quest->daftar_manajemen_daily_quest();

			$log = array(
				"id_pengguna" => $this->session->userdata("id_pengguna"),
				"id_modul" => $this->data['id_modul'],
				"deskripsi" => "lihat " . strtolower(preg_replace("/_/", " ", __CLASS__)),
				"alamat_ip" => $this->data["ip_address"],
				"waktu" => date("Y-m-d H:i:s")
			);
			$this->m_log->tambah($log);
		}

		if ($this->akses["riwayat"]) {
			$this->data["url_riwayat"] = $this->m_setting->ambil_url_modul("Riwayat Daily Quest");
		}

		$this->load->view('template', $this->data);
	}

	private function tambah($nama, $link, $poin, $poin_harian, $start_date, $end_date, $gambar, $status)
	{
		$return = array("status" => false, "error_info" => "");
		$cek_date = $this->m_manajemen_daily_quest->cek_date_manajemen_daily_quest($start_date, $end_date);
		if (!$cek_date["status"]) {
			$return["status"] = $cek_date["status"];
			$return["error_info"] = $cek_date["error_info"];
		} else {
			if ($this->m_manajemen_daily_quest->cek_tambah_manajemen_daily_quest($nama)) {
				$data = array(
					"nama" => $nama,
					// "link" => $link,
					"poin" => $poin,
					"poin_harian" => $poin_harian,
					"start_date" => $start_date,
					"end_date" => $end_date,
					// "gambar" => $gambar,
					"status" => $status,
					"created_at" => date('Y-m-d H:i:s'),
					"created_by_np" => $_SESSION["no_pokok"],
					"created_by_nama" => $_SESSION["nama"],
					"created_by_kode_unit" => $_SESSION["kode_unit"],
				);
				$this->m_manajemen_daily_quest->tambah($data);

				if ($this->m_manajemen_daily_quest->cek_hasil_manajemen_daily_quest($nama, $link, $poin, $poin_harian, $start_date, $end_date, $gambar, $status)) {
					$return["status"] = true;

					$arr_data_insert = $this->m_manajemen_daily_quest->data_manajemen_daily_quest($nama);

					$log_data_baru = "";

					foreach ($data as $key => $value) {
						if (!empty($log_data_baru)) {
							$log_data_baru .= "<br>";
						}
						$log_data_baru .= "$key = $value";
					}

					$log = array(
						"id_pengguna" => $this->session->userdata("id_pengguna"),
						"id_modul" => $this->data['id_modul'],
						"id_target" => $arr_data_insert['id'],
						"deskripsi" => "tambah " . strtolower(preg_replace("/_/", " ", __CLASS__)),
						"kondisi_baru" => $log_data_baru,
						"alamat_ip" => $this->data["ip_address"],
						"waktu" => date("Y-m-d H:i:s")
					);
					$this->m_log->tambah($log);
				} else {
					$return["status"] = false;
					$return["error_info"] = "Penambahan Daily Quest <b>Gagal</b> Dilakukan.";
				}
			} else {
				$return["status"] = false;
				$return["error_info"] = "Daily Quest dengan nama <b>$nama</b> sudah ada.";
			}
		}
		return $return;
	}

	private function ubah($nama, $nama_ubah, $poin, $poin_ubah, $poin_harian, $poin_harian_ubah, $link_ubah, $start_date_ubah, $end_date_ubah, $gambar_ubah, $status_ubah)
	{
		$return = array("status" => false, "error_info" => "");

		$cek_poin = $this->m_manajemen_daily_quest->cek_poin_manajemen_daily_quest($nama, $poin, $poin_ubah, $poin_harian, $poin_harian_ubah);
		if (!$cek_poin["status"]) {
			$return["status"] = $cek_poin["status"];
			$return["error_info"] = $cek_poin["error_info"];
			return $return;
		}

		$cek_date = $this->m_manajemen_daily_quest->cek_date_manajemen_daily_quest($start_date_ubah, $end_date_ubah, $nama, $poin_ubah, $poin_harian_ubah);
		if (!$cek_date["status"]) {
			$return["status"] = $cek_date["status"];
			$return["error_info"] = $cek_date["error_info"];
		} else {
			$cek = $this->m_manajemen_daily_quest->cek_ubah_manajemen_daily_quest($nama, $nama_ubah);
			if ($cek["status"]) {
				$set = array(
					"nama" => $nama_ubah,
					// "link" => $link_ubah,
					"poin" => $poin_ubah,
					"poin_harian" => $poin_harian_ubah,
					"start_date" => $start_date_ubah,
					"end_date" => $end_date_ubah,
					"status" => $status_ubah,
					"updated_at" => date('Y-m-d H:i:s'),
					"updated_by_np" => $_SESSION["no_pokok"],
					"updated_by_nama" => $_SESSION["nama"],
					"updated_by_kode_unit" => $_SESSION["kode_unit"],
				);

				// if (!empty($gambar_ubah)) $set["gambar"] = $gambar_ubah;

				$arr_data_lama = $this->m_manajemen_daily_quest->data_manajemen_daily_quest($nama);
				$log_data_lama = "";

				foreach ($arr_data_lama as $key => $value) {
					if (!empty($log_data_lama)) {
						$log_data_lama .= "<br>";
					}
					$log_data_lama .= "$key = $value";
				}

				$this->m_manajemen_daily_quest->ubah($set, $nama);

				if ($this->m_manajemen_daily_quest->cek_hasil_manajemen_daily_quest($nama_ubah, $link_ubah, $poin_ubah, $poin_harian_ubah, $start_date_ubah, $end_date_ubah, $gambar_ubah, $status_ubah)) {
					$return["status"] = true;

					$log_data_baru = "";
					foreach ($set as $key => $value) {
						if (!empty($log_data_baru)) {
							$log_data_baru .= "<br>";
						}
						$log_data_baru .= "$key = $value";
					}

					$log = array(
						"id_pengguna" => $this->session->userdata("id_pengguna"),
						"id_modul" => $this->data['id_modul'],
						"id_target" => $arr_data_lama["id"],
						"deskripsi" => "ubah " . strtolower(preg_replace("/_/", " ", __CLASS__)),
						"kondisi_lama" => $log_data_lama,
						"kondisi_baru" => $log_data_baru,
						"alamat_ip" => $this->data["ip_address"],
						"waktu" => date("Y-m-d H:i:s")
					);
					$this->m_log->tambah($log);
				} else {
					$return["status"] = false;
					$return["error_info"] = "Perubahan Daily Quest <b>Gagal</b> Dilakukan.";
				}
			} else {
				$return["status"] = $cek["status"];
				$return["error_info"] = $cek["error_info"];
			}
		}
		return $return;
	}

	public function riwayat($np = '', $nama = '')
	{
		$nama = urldecode($nama);
		$this->data['judul'] = "Manajemen Daily Quest";
		$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		$this->akses = akses_helper($this->data['id_modul']);

		izin($this->akses["akses"]);

		$this->data["akses"] = $this->akses;
		$this->data['judul'] .= " (Riwayat) : " . $nama;
		$this->data["navigasi_menu"] = menu_helper();
		$this->data['content'] = $this->folder_view . "riwayat_daily_quest";

		array_push($this->data['js_sources'], "poin_reward/riwayat_daily_quest");

		if ($this->akses["lihat"]) {
			$js_header_script = "<script>
								$(document).ready(function() {
									$('#tabel_riwayat_daily_quest').DataTable({
										responsive: true
									});
								});
							</script>";

			array_push($this->data["js_header_script"], $js_header_script);

			$this->data["daftar_riwayat_daily_quest"] = $this->m_manajemen_daily_quest->daftar_riwayat_daily_quest($np);
		}

		$this->load->view('template', $this->data);
	}
}
	
	/* End of file manajemen_daily_quest.php */
	/* Location: ./application/controllers/daily_quest/manajemen_daily_quest.php */
