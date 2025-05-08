<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Gambar_dinamis extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		$meta = meta_data();
		foreach ($meta as $key => $value) {
			$this->data[$key] = $value;
		}

		$this->folder_view = 'master_data/';
		$this->folder_model = 'master_data/';
		$this->folder_ajax_view = $this->folder_view . 'ajax/';
		$this->akses = array();

		$this->load->model($this->folder_model . "/m_gambar_dinamis");

		$this->data['success'] = "";
		$this->data['warning'] = "";

		$this->data["is_with_sidebar"] = true;
	}

	public function index()
	{
		//die(var_dump($_SESSION));
		$this->data['judul'] = "Gambar Dinamis";
		$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		$this->akses = akses_helper($this->data['id_modul']);

		izin($this->akses["akses"]);

		$this->data["akses"] = $this->akses;
		$this->data["navigasi_menu"] = menu_helper();
		$this->data['content'] = $this->folder_view . "gambar_dinamis";

		array_push($this->data['js_sources'], "master_data/gambar_dinamis");

		if ($this->input->post()) {

			$dir = 'uploads/images/gambar_dinamis/';

			if (!is_dir('./' . $dir)) {
				mkdir('./' . $dir, 0777, true);
			}
			
			if (!strcmp($this->input->post("aksi"), "tambah")) {
				izin($this->akses["tambah"]);

				$this->data['nama'] = $this->input->post("nama");

				if (!strcmp($this->input->post("status"), "aktif")) {
					$this->data['status'] = true;
				} else if (!strcmp($this->input->post("status"), "non aktif")) {
					$this->data['status'] = false;
				}

				$this->load->library('upload');
				$config['upload_path']   = "./uploads/images/gambar_dinamis/";
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

				$tambah = $this->tambah($this->data['nama'], $this->data['gambar'], $this->data['status']);

				$this->data['panel_tambah'] = "in";

				if ($tambah['status']) {
					$this->data['success'] = "Gambar Dinamis dengan nama <b>" . $this->data['nama'] . "</b> berhasil ditambahkan.";
					$this->data['panel_tambah'] = "";
					$this->data['nama'] = "";
					$this->data['status'] = "";
				} else {
					$this->data['warning'] = $tambah['error_info'];
				}
			} else if (!strcmp($this->input->post("aksi"), "ubah")) {
				izin($this->akses["ubah"]);

				$this->data['nama'] = $this->input->post("nama");
				$this->data['nama_ubah'] = $this->input->post("nama_ubah");
				$this->data['status'] = (bool)$this->input->post("status");
				if (!strcmp($this->input->post("status_ubah"), "aktif")) {
					$this->data['status_ubah'] = true;
				} else if (!strcmp($this->input->post("status_ubah"), "non aktif")) {
					$this->data['status_ubah'] = false;
				}

				$this->data['gambar'] = $this->input->post("gambar");

				$this->load->library('upload');
				$config['upload_path']   = "./uploads/images/gambar_dinamis/";
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

				$ubah = $this->ubah($this->data['nama'], $this->data['nama_ubah'], $this->data['gambar_ubah'], $this->data['status_ubah']);

				if ($ubah["status"]) {
					$this->data['success'] = "Perubahan Gambar Dinamis berhasil dilakukan.";
				} else {
					$this->data['warning'] = $ubah['error_info'];
				}

				$this->data['panel_tambah'] = "";
				$this->data['nama'] = "";
				$this->data['status'] = "";
			} else {
				$this->data['panel_tambah'] = "";
				$this->data['nama'] = "";
				$this->data['status'] = "";
			}
		} else {
			$this->data['panel_tambah'] = "";
			$this->data['nama'] = "";
			$this->data['status'] = "";
		}

		if ($this->akses["lihat"]) {
			$js_header_script = "
				<script src='" . base_url('asset/select2') . "/select2.min.js'></script>
				<script>
					$(document).ready(function() {
						$('#tabel_gambar_dinamis').DataTable({
							responsive: true
						});
						$('.select2').select2();
					});
				</script>";

			array_push($this->data["js_header_script"], $js_header_script);

			$this->data["daftar_gambar_dinamis"] = $this->m_gambar_dinamis->daftar_gambar_dinamis();

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

	private function tambah($nama, $gambar, $status)
	{
		$return = array("status" => false, "error_info" => "");
		$cek = $this->m_gambar_dinamis->cek_tambah_gambar_dinamis($nama);
		if ($cek["status"]) {
			$data = array(
				"nama" => $nama,
				"gambar" => $gambar,
				"status" => $status,
				"created_at" => date('Y-m-d H:i:s'),
				"created_by_np" => $_SESSION["no_pokok"],
				"created_by_nama" => $_SESSION["nama"],
				"created_by_kode_unit" => $_SESSION["kode_unit"],
			);
			$this->m_gambar_dinamis->tambah($data);

			if ($this->m_gambar_dinamis->cek_hasil_gambar_dinamis($nama, $gambar, $status)) {
				$return["status"] = true;

				$arr_data_insert = $this->m_gambar_dinamis->data_gambar_dinamis($nama);

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
				$return["error_info"] = "Penambahan Gambar Dinamis <b>Gagal</b> Dilakukan.";
			}
		} else {
			$return["status"] = $cek["status"];
			$return["error_info"] = $cek["error_info"];
		}
		return $return;
	}

	private function ubah($nama, $nama_ubah, $gambar_ubah, $status_ubah)
	{
		$return = array("status" => false, "error_info" => "");
		$cek = $this->m_gambar_dinamis->cek_ubah_gambar_dinamis($nama, $nama_ubah);
		if ($cek["status"]) {
			$set = array(
				"nama" => $nama_ubah,
				"status" => $status_ubah,
				"updated_at" => date('Y-m-d H:i:s'),
				"updated_by_np" => $_SESSION["no_pokok"],
				"updated_by_nama" => $_SESSION["nama"],
				"updated_by_kode_unit" => $_SESSION["kode_unit"],
			);

			if (!empty($gambar_ubah)) $set["gambar"] = $gambar_ubah;

			$arr_data_lama = $this->m_gambar_dinamis->data_gambar_dinamis($nama);
			$log_data_lama = "";

			foreach ($arr_data_lama as $key => $value) {
				if (strcmp($key, "id") != 0) {
					if (!empty($log_data_lama)) {
						$log_data_lama .= "<br>";
					}
					$log_data_lama .= "$key = $value";
				}
			}

			$this->m_gambar_dinamis->ubah($set, $nama);

			if ($this->m_gambar_dinamis->cek_hasil_gambar_dinamis($nama_ubah, $gambar_ubah, $status_ubah)) {
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
				$return["error_info"] = "Perubahan Gambar Dinamis <b>Gagal</b> Dilakukan.";
			}
		} else {
			$return["status"] = $cek["status"];
			$return["error_info"] = $cek["error_info"];
		}

		return $return;
	}
}
	
	/* End of file gambar_dinamis.php */
	/* Location: ./application/controllers/master_data/gambar_dinamis.php */
