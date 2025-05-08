<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Manajemen_poin extends CI_Controller
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

		$this->load->model($this->folder_model . "m_manajemen_poin");
		$this->load->helper("tanggal_helper");

		$this->data['success'] = "";
		$this->data['warning'] = "";

		$this->data["is_with_sidebar"] = true;
	}
	public function get_poin()
	{
	}
	public function index()
	{
		$this->data['judul'] = "Manajemen Poin";
		$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		$this->akses = akses_helper($this->data['id_modul']);

		izin($this->akses["akses"]);

		$this->data["akses"] = $this->akses;
		$this->data["navigasi_menu"] = menu_helper();
		$this->data['content'] = $this->folder_view . "manajemen_poin";

		array_push($this->data['js_sources'], "poin_reward/manajemen_poin");

		$this->data["nama"] = "";
		$this->data["keterangan"] = "";
		$this->data["status"] = "";
		$this->data["panel_tambah"] = "";
		$this->data["tahun"] = date('Y');

		if ($this->input->post()) {
			if (!strcmp($this->input->post("aksi"), "tambah")) {
				izin($this->akses["tambah"]);

				$this->data['nama'] = $this->input->post("nama");
				$this->data['poin'] = $this->input->post("poin");
				$this->data['unit'] = $this->input->post("unit");
				if (!strcmp($this->input->post("status"), "aktif")) {
					$this->data['status'] = "1";
				} else if (!strcmp($this->input->post("status"), "non aktif")) {
					$this->data['status'] = "0";
				}

				$tambah = $this->tambah($this->data['nama'], $this->data['poin'], $this->data['unit'], $this->data['status']);

				$this->data['panel_tambah'] = "in";

				if ($tambah['status']) {
					$this->data['success'] = "Kelompok Modul dengan nama <b>" . $this->data['nama'] . "</b> berhasil ditambahkan.";
					$this->data['panel_tambah'] = "";
					$this->data['nama'] = "";
					$this->data['keterangan'] = "";
					$this->data['status'] = "";
				} else {
					$this->data['warning'] = $tambah['error_info'];
				}
			} else if (!strcmp($this->input->post("aksi"), "ubah")) {
				izin($this->akses["ubah"]);

				$this->data['nama'] = $this->input->post("nama");
				$this->data['nama_ubah'] = $this->input->post("nama_ubah");
				$this->data['unit'] = $this->input->post("unit");
				$this->data['unit_ubah'] = $this->input->post("unit_ubah");
				$this->data['poin'] = $this->input->post("poin");
				$this->data['poin_ubah'] = $this->input->post("poin_ubah");

				if (!strcmp($this->input->post("status"), "aktif")) {
					$this->data['status'] = "1";
				} else if (!strcmp($this->input->post("status"), "non aktif")) {
					$this->data['status'] = "0";
				}

				if (!strcmp($this->input->post("status_ubah"), "aktif")) {
					$this->data['status_ubah'] = "1";
				} else if (!strcmp($this->input->post("status_ubah"), "non aktif")) {
					$this->data['status_ubah'] = "0";
				}

				$ubah = $this->ubah($this->data["nama"], $this->data["unit"], $this->data["poin"], $this->data["status"], $this->data["nama_ubah"], $this->data["unit_ubah"], $this->data["poin_ubah"], $this->data["status_ubah"]);

				if ($ubah["status"]) {
					$this->data['success'] = "Perubahan kelompok modul berhasil dilakukan.";
				} else {
					$this->data['warning'] = $ubah['error_info'];
				}
			} else if (!strcmp($this->input->post("aksi"), "filter_tahun")) {
				$this->data["tahun"] = $this->input->post("tahun");
			}
		}

		if ($this->akses["lihat"]) {
			$js_header_script = "<script>
								$(document).ready(function() {
									$('#tabel_manajemen_poin').DataTable({
										responsive: true
									});
								});
							</script>";

			array_push($this->data["js_header_script"], $js_header_script);

			$this->data["daftar_manajemen_poin"] = $this->m_manajemen_poin->daftar_manajemen_poin($this->data["tahun"]);
			// print_r($this->data["daftar_manajemen_poin"]);
			// die;
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
			$this->data["url_riwayat"] = $this->m_setting->ambil_url_modul("Riwayat Poin");
		}

		$this->load->view('template', $this->data);
	}

	private function tambah($nama, $poin, $unit, $status)
	{
		$return = array("status" => false, "error_info" => "");
		if ($this->m_manajemen_poin->cek_tambah_manajemen_poin($nama)) {
			$data = array(
				"nama" => $nama,
				"point" => $poin,
				"unit" => $unit,
				"status" => $status
			);
			// print_r($data);
			// die;
			$this->m_manajemen_poin->tambah($data);

			if ($this->m_manajemen_poin->cek_hasil_manajemen_poin($nama, $poin, $unit)) {
				$return["status"] = true;

				$arr_data_insert = $this->m_manajemen_poin->data_manajemen_poin($nama);

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
				$return["error_info"] = "Penambahan Kelompok Modul <b>Gagal</b> Dilakukan.";
			}
		} else {
			$return["status"] = false;
			$return["error_info"] = "Kelompok Modul dengan nama <b>$nama</b> sudah ada.";
		}
		return $return;
	}

	private function ubah($nama, $unit, $poin, $status, $nama_ubah, $unit_ubah, $poin_ubah, $status_ubah)
	{
		$return = array("status" => false, "error_info" => "");
		$cek = $this->m_manajemen_poin->cek_ubah_manajemen_poin($nama, $nama_ubah);
		if ($cek["status"]) {
			$set = array('nama' => $nama_ubah, 'unit' => $unit_ubah, 'point' => $poin_ubah, 'status' => $status_ubah);
			$arr_data_lama = $this->m_manajemen_poin->data_manajemen_poin($nama);
			$log_data_lama = "";

			foreach ($arr_data_lama as $key => $value) {
				if (strcmp($key, "id") != 0) {
					if (!empty($log_data_lama)) {
						$log_data_lama .= "<br>";
					}
					$log_data_lama .= "$key = $value";
				}
			}

			$this->m_manajemen_poin->ubah($set, $nama, $unit, $poin, $status);

			if ($this->m_manajemen_poin->cek_hasil_manajemen_poin($nama_ubah, $unit_ubah, $poin_ubah, $status_ubah)) {
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
				$return["error_info"] = "Perubahan Kelompok Modul <b>Gagal</b> Dilakukan.";
			}
		} else {
			$return["status"] = $cek["status"];
			$return["error_info"] = $cek["error_info"];
		}

		return $return;
	}

	public function riwayat($tahun, $np = '', $nama = '')
	{
		if (empty($tahun)) $tahun = date('Y');
		$nama = urldecode($nama);
		$this->data['judul'] = "Manajemen Poin";
		$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		$this->akses = akses_helper($this->data['id_modul']);

		izin($this->akses["akses"]);

		$this->data["akses"] = $this->akses;
		$this->data['judul'] .= " (Riwayat) : " . $nama;
		$this->data["navigasi_menu"] = menu_helper();
		$this->data['content'] = $this->folder_view . "riwayat_poin";

		array_push($this->data['js_sources'], "poin_reward/riwayat_poin");

		if ($this->akses["lihat"]) {
			$js_header_script = "<script>
								$(document).ready(function() {
									$('#tabel_riwayat_poin').DataTable({
										responsive: true
									});
								});
							</script>";

			array_push($this->data["js_header_script"], $js_header_script);

			$this->data["daftar_riwayat_poin"] = $this->m_manajemen_poin->daftar_riwayat_poin($np, $tahun);
			// print_r($this->data["daftar_riwayat_poin"]);
			// die;
			// $log = array(
			// 	"id_pengguna" => $this->session->userdata("id_pengguna"),
			// 	"id_modul" => $this->data['id_modul'],
			// 	"deskripsi" => "lihat " . strtolower(preg_replace("/_/", " ", __CLASS__)),
			// 	"alamat_ip" => $this->data["ip_address"],
			// 	"waktu" => date("Y-m-d H:i:s")
			// );
			// $this->m_log->tambah($log);
		}

		$this->load->view('template', $this->data);
	}
}
	
	/* End of file manajemen_poin.php */
	/* Location: ./application/controllers/poin_reward/manajemen_poin.php */
