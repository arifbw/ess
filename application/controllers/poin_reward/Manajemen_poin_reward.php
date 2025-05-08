<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Manajemen_poin_reward extends CI_Controller
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

		$this->load->model($this->folder_model . "/m_manajemen_poin_reward");
		$this->load->model("poin_reward/m_manajemen_poin", "poin");
		$this->load->model("M_dashboard", "dashboard");

		$this->data['success'] = "";
		$this->data['warning'] = "";

		$this->data["is_with_sidebar"] = true;
	}

	public function index()
	{
		$this->data['judul'] = "Manajemen Poin Reward";
		$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		$this->akses = akses_helper($this->data['id_modul']);

		izin($this->akses["akses"]);

		$this->data["akses"] = $this->akses;
		$this->data["navigasi_menu"] = menu_helper();
		$this->data['content'] = $this->folder_view . "manajemen_poin_reward";

		array_push($this->data['js_sources'], "poin_reward/manajemen_poin_reward");

		if ($this->input->post()) {

			$dir = 'uploads/images/poin_reward/';

			if (!is_dir('./' . $dir)) {
				mkdir('./' . $dir, 0777, true);
			}

			if (!strcmp($this->input->post("aksi"), "tambah")) {
				izin($this->akses["tambah"]);

				$this->data['nama'] = $this->input->post("nama");
				$this->data['konten'] = $this->input->post("konten");
				$this->data['poin'] = $this->input->post("poin");
				$this->data['kuota'] = $this->input->post("kuota");
				$this->data['start_date'] = date('Y-m-d', strtotime($this->input->post('start_date')));
				$this->data['end_date'] = date('Y-m-d', strtotime($this->input->post('end_date')));

				if (!strcmp($this->input->post("status"), "aktif")) {
					$this->data['status'] = true;
				} else if (!strcmp($this->input->post("status"), "non aktif")) {
					$this->data['status'] = false;
				}

				$this->load->library('upload');
				$config['upload_path']   = "./uploads/images/poin_reward/";
				$config['allowed_types'] = "png";
				$config['max_size']      = "1000";
				$config['overwrite']     = true;
				$config['file_name']     = $this->input->post('nama') . '-' . time();

				$this->upload->initialize($config);
				if ($this->upload->do_upload('gambar')) {

					$this->data['gambar'] = $this->upload->data('file_name');
				} else {
					$this->data['gambar'] = null;
				}

				$tambah = $this->tambah($this->data['nama'], $this->data['konten'], $this->data['poin'], $this->data['kuota'], $this->data['start_date'], $this->data['end_date'], $this->data['gambar'], $this->data['status']);

				$this->data['panel_tambah'] = "in";

				if ($tambah['status']) {
					$this->data['success'] = "Poin Reward dengan nama <b>" . $this->data['nama'] . "</b> berhasil ditambahkan.";
					$this->data['panel_tambah'] = "";
					$this->data['nama'] = "";
					$this->data['konten'] = "";
					$this->data['poin'] = "";
					$this->data['kuota'] = "";
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
				$this->data['konten'] = $this->input->post("konten");
				$this->data['konten_ubah'] = $this->input->post("konten_ubah");
				$this->data['poin'] = $this->input->post("poin");
				$this->data['poin_ubah'] = $this->input->post("poin_ubah");
				$this->data['kuota'] = $this->input->post("kuota");
				$this->data['kuota_ubah'] = $this->input->post("kuota_ubah");
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

				$this->data['gambar'] = $this->input->post("gambar");

				$this->load->library('upload');
				$config['upload_path']   = "./uploads/images/poin_reward/";
				$config['allowed_types'] = "png";
				$config['max_size']      = "1000";
				$config['overwrite']     = true;
				$config['file_name']     = $this->input->post('nama') . '-' . time();

				$this->upload->initialize($config);
				if ($this->upload->do_upload('gambar_ubah')) {
					$this->data['gambar_ubah'] = $this->upload->data('file_name');
				} else {
					$this->data['gambar_ubah'] = null;
				}

				$ubah = $this->ubah($this->data['nama'], $this->data['nama_ubah'], $this->data['konten_ubah'], $this->data['poin_ubah'], $this->data['kuota_ubah'], $this->data['start_date_ubah'], $this->data['end_date_ubah'], $this->data['gambar_ubah'], $this->data['status_ubah']);

				if ($ubah["status"]) {
					$this->data['success'] = "Perubahan Poin Reward berhasil dilakukan.";
				} else {
					$this->data['warning'] = $ubah['error_info'];
				}
			} else if (!strcmp($this->input->post("aksi"), "scan")) {
				izin($this->akses["scan"]);

				$admin = (object) [
					"np_karyawan" => $_SESSION["no_pokok"],
					"nama" => $_SESSION["nama"],
					"kode_unit" => $_SESSION["kode_unit"],
				];

				$kode_scan = $this->input->post("kode_scan");
				$result = $this->poin->scan_kode_poin_reward($kode_scan, $admin);

				if ($result["status"]) {
					$this->data['success'] =  $result['message'];
					$this->data['kode_scan'] = "";
				} else {
					$this->data['warning'] = $result['message'];
				}
			}
		}

		if (!$this->input->post() || strcmp($this->input->post("aksi"), "tambah")) {
			$this->data['panel_tambah'] = "";
			$this->data['nama'] = "";
			$this->data['konten'] = "";
			$this->data['poin'] = "";
			$this->data['kuota'] = "";
			$this->data['start_date'] = "";
			$this->data['end_date'] = "";
			$this->data['status'] = "";
		}

		if ($this->akses["lihat"]) {
			$js_header_script = "
					<script src='" . base_url('asset/select2') . "/select2.min.js'></script>
					<script>
						$(document).ready(function() {
							$('#tabel_manajemen_poin_reward').DataTable({
								responsive: true
							});
							$('.select2').select2();
						});
					</script>";

			array_push($this->data["js_header_script"], $js_header_script);

			$this->data["daftar_manajemen_poin_reward"] = $this->m_manajemen_poin_reward->daftar_manajemen_poin_reward();

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
			$this->data["url_riwayat"] = $this->m_setting->ambil_url_modul("Riwayat Poin Reward");
		}

		$this->load->view('template', $this->data);
	}

	private function tambah($nama, $konten, $poin, $kuota, $start_date, $end_date, $gambar, $status)
	{
		$return = array("status" => false, "error_info" => "");
		if ($this->m_manajemen_poin_reward->cek_tambah_manajemen_poin_reward($nama)) {
			$data = array(
				"nama" => $nama,
				"konten" => $konten,
				"poin" => $poin,
				"kuota" => $kuota,
				"start_date" => $start_date,
				"end_date" => $end_date,
				"gambar" => $gambar,
				"status" => $status,
				"created_at" => date('Y-m-d H:i:s'),
				"created_by_np" => $_SESSION["no_pokok"],
				"created_by_nama" => $_SESSION["nama"],
				"created_by_kode_unit" => $_SESSION["kode_unit"],
			);
			$this->m_manajemen_poin_reward->tambah($data);

			if ($this->m_manajemen_poin_reward->cek_hasil_manajemen_poin_reward($nama, $konten, $poin, $kuota, $start_date, $end_date, $gambar, $status)) {
				$return["status"] = true;

				$arr_data_insert = $this->m_manajemen_poin_reward->data_manajemen_poin_reward($nama);

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
				$return["error_info"] = "Penambahan Poin Reward <b>Gagal</b> Dilakukan.";
			}
		} else {
			$return["status"] = false;
			$return["error_info"] = "Poin Reward dengan nama <b>$nama</b> sudah ada.";
		}
		return $return;
	}

	private function ubah($nama, $nama_ubah, $konten_ubah, $poin_ubah, $kuota_ubah, $start_date_ubah, $end_date_ubah, $gambar_ubah, $status_ubah)
	{
		$return = array("status" => false, "error_info" => "");
		$cek = $this->m_manajemen_poin_reward->cek_ubah_manajemen_poin_reward($nama, $nama_ubah);
		if ($cek["status"]) {
			$set = array(
				"nama" => $nama_ubah,
				"konten" => $konten_ubah,
				"poin" => $poin_ubah,
				"kuota" => $kuota_ubah,
				"start_date" => $start_date_ubah,
				"end_date" => $end_date_ubah,
				"status" => $status_ubah,
				"updated_at" => date('Y-m-d H:i:s'),
				"updated_by_np" => $_SESSION["no_pokok"],
				"updated_by_nama" => $_SESSION["nama"],
				"updated_by_kode_unit" => $_SESSION["kode_unit"],
			);

			if (!empty($gambar_ubah)) $set["gambar"] = $gambar_ubah;

			$arr_data_lama = $this->m_manajemen_poin_reward->data_manajemen_poin_reward($nama);
			$log_data_lama = "";

			foreach ($arr_data_lama as $key => $value) {
				if (!empty($log_data_lama)) {
					$log_data_lama .= "<br>";
				}
				$log_data_lama .= "$key = $value";
			}

			$this->m_manajemen_poin_reward->ubah($set, $nama);

			if ($this->m_manajemen_poin_reward->cek_hasil_manajemen_poin_reward($nama_ubah, $konten_ubah, $poin_ubah, $kuota_ubah, $start_date_ubah, $end_date_ubah, $gambar_ubah, $status_ubah)) {
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
				$return["error_info"] = "Perubahan Poin Reward <b>Gagal</b> Dilakukan.";
			}
		} else {
			$return["status"] = $cek["status"];
			$return["error_info"] = $cek["error_info"];
		}
		return $return;
	}

	public function riwayat($np = '', $nama = '')
	{
		$nama = urldecode($nama);
		$this->data['judul'] = "Manajemen Poin Reward";
		$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		$this->akses = akses_helper($this->data['id_modul']);

		izin($this->akses["akses"]);

		$this->data["akses"] = $this->akses;
		$this->data['judul'] .= " (Riwayat) : " . $nama;
		$this->data["navigasi_menu"] = menu_helper();
		$this->data['content'] = $this->folder_view . "riwayat_poin_reward";

		array_push($this->data['js_sources'], "poin_reward/riwayat_poin_reward");

		if ($this->akses["lihat"]) {
			$js_header_script = "<script>
								$(document).ready(function() {
									$('#tabel_riwayat_poin_reward').DataTable({
										responsive: true
									});
								});
							</script>";

			array_push($this->data["js_header_script"], $js_header_script);

			$this->data["daftar_riwayat_poin_reward"] = $this->m_manajemen_poin_reward->daftar_riwayat_poin_reward($np);
		}

		$this->load->view('template', $this->data);
	}
}
	
	/* End of file manajemen_poin_reward.php */
	/* Location: ./application/controllers/poin_reward/manajemen_poin_reward.php */
