<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Manajemen_survey extends CI_Controller
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

		$this->load->model($this->folder_model . "/m_manajemen_survey");

		$this->data['success'] = "";
		$this->data['warning'] = "";

		$this->data["is_with_sidebar"] = true;
	}

	public function index()
	{
		//die(var_dump($_SESSION));
		$this->data['judul'] = "Survey";
		$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		$this->akses = akses_helper($this->data['id_modul']);

		izin($this->akses["akses"]);

		$this->data["akses"] = $this->akses;
		$this->data["navigasi_menu"] = menu_helper();
		$this->data['content'] = $this->folder_view . "manajemen_survey";
		$this->data['ref_karyawan'] = $this->db->select('nama, no_pokok')->distinct()->get('mst_karyawan')->result_array();

		array_push($this->data['js_sources'], "poin_reward/manajemen_survey");

		if ($this->input->post()) {

			$dir = 'uploads/images/survey/';

			if (!is_dir('./' . $dir)) {
				mkdir('./' . $dir, 0777, true);
			}

			if (!strcmp($this->input->post("aksi"), "tambah")) {
				izin($this->akses["tambah"]);

				$this->data['nama'] = $this->input->post("nama");
				$this->data['konten'] = $this->input->post("konten");
				$this->data['link'] = $this->input->post("link");
				$this->data['poin'] = $this->input->post("poin");
				$this->data['durasi_baca'] = $this->input->post("durasi_baca");
				$this->data['start_date'] = $this->input->post("start_date");
				$this->data['end_date'] = $this->input->post("end_date");
				$insert['np_tergabung'] = $this->input->post('np_tergabung') ? implode(',', $this->input->post('np_tergabung')) : 'all';

				if (!strcmp($this->input->post("status"), "aktif")) {
					$this->data['status'] = true;
				} else if (!strcmp($this->input->post("status"), "non aktif")) {
					$this->data['status'] = false;
				}

				$this->load->library('upload');
				$config['upload_path']   = "./uploads/images/survey/";
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

				$tambah = $this->tambah($this->data['nama'], $this->data['konten'], $this->data['link'], $this->data['poin'], $this->data['durasi_baca'], $this->data['start_date'], $this->data['end_date'], $this->data['gambar'], $this->data['status']);

				$this->data['panel_tambah'] = "in";

				if ($tambah['status']) {
					$this->data['success'] = "Survey dengan nama <b>" . $this->data['nama'] . "</b> berhasil ditambahkan.";

					$notifikasi = (@$this->input->post('notifikasi', true) == 'on' ? 1 : 0);
					if ($notifikasi) {
						$tipe = "Survey";
						$judul = $tipe . ' ' .  $this->data['nama'];
						$pesan = "Berlangsung pada "  . tanggal_indonesia($this->data['start_date']) . " hingga "  . tanggal_indonesia($this->data['end_date']);
						$deskripsi = $this->data['konten'];
						$kirim_notifikasi = $this->kirim_notifikasi($judul, $pesan, $deskripsi);

						if ($kirim_notifikasi['status']) {
							$this->data['success'] = $this->data['success'] . ' Pengumuman berhasil dikirimkan';
						} else {
							$this->data['success'] = $this->data['success'] . ' Pengumuman gagal dikirimkan';
						}
					}

					$this->data['panel_tambah'] = "";
					$this->data['nama'] = "";
					$this->data['konten'] = "";
					$this->data['link'] = "";
					$this->data['poin'] = "";
					$this->data['durasi_baca'] = "";
					$this->data['start_date'] = "";
					$this->data['end_date'] = "";
					$this->data['status'] = "";
				} else {
					$this->data['warning'] = $tambah['error_info'];
				}
			} else if (!strcmp($this->input->post("aksi"), "ubah")) {
				izin($this->akses["ubah"]);

				$this->data['id_manajemen_survey'] = $this->input->post("id_manajemen_survey");
				$this->data['nama'] = $this->input->post("nama");
				$this->data['nama_ubah'] = $this->input->post("nama_ubah");
				$this->data['konten'] = $this->input->post("konten");
				$this->data['konten_ubah'] = $this->input->post("konten_ubah");
				$this->data['link'] = $this->input->post("link");
				$this->data['link_ubah'] = $this->input->post("link_ubah");
				$this->data['poin'] = $this->input->post("poin");
				$this->data['poin_ubah'] = $this->input->post("poin_ubah");
				$this->data['durasi_baca'] = $this->input->post("durasi_baca");
				$this->data['durasi_baca_ubah'] = $this->input->post("durasi_baca_ubah");
				$this->data['start_date'] = $this->input->post("start_date");
				$this->data['start_date_ubah'] = $this->input->post("start_date_ubah");
				$this->data['end_date'] = $this->input->post("end_date");
				$this->data['end_date_ubah'] = $this->input->post("end_date_ubah");
				$this->data['status'] = (bool)$this->input->post("status");
				$insert['np_tergabung'] = $this->input->post('np_tergabung') ? implode(',', $this->input->post('np_tergabung')) : 'all';

				if (!strcmp($this->input->post("status_ubah"), "aktif")) {
					$this->data['status_ubah'] = true;
				} else if (!strcmp($this->input->post("status_ubah"), "non aktif")) {
					$this->data['status_ubah'] = false;
				}

				$this->data['gambar'] = $this->input->post("gambar");

				$this->load->library('upload');
				$config['upload_path']   = "./uploads/images/survey/";
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

				$ubah = $this->ubah($this->data['id_manajemen_survey'], $this->data['nama'], $this->data['nama_ubah'], $this->data['konten_ubah'], $this->data['link_ubah'], $this->data['poin_ubah'], $this->data['durasi_baca_ubah'], $this->data['start_date_ubah'], $this->data['end_date_ubah'], $this->data['gambar_ubah'], $this->data['status_ubah']);

				if ($ubah["status"]) {
					$this->data['success'] = "Perubahan survey berhasil dilakukan.";
				} else {
					$this->data['warning'] = $ubah['error_info'];
				}

				$this->data['panel_tambah'] = "";
				$this->data['nama'] = "";
				$this->data['konten'] = "";
				$this->data['link'] = "";
				$this->data['poin'] = "";
				$this->data['durasi_baca'] = "";
				$this->data['start_date'] = "";
				$this->data['end_date'] = "";
				$this->data['status'] = "";
			} else {
				$this->data['panel_tambah'] = "";
				$this->data['nama'] = "";
				$this->data['konten'] = "";
				$this->data['link'] = "";
				$this->data['poin'] = "";
				$this->data['durasi_baca'] = "";
				$this->data['start_date'] = "";
				$this->data['end_date'] = "";
				$this->data['status'] = "";
			}
		} else {
			$this->data['panel_tambah'] = "";
			$this->data['nama'] = "";
			$this->data['konten'] = "";
			$this->data['link'] = "";
			$this->data['poin'] = "";
			$this->data['durasi_baca'] = "";
			$this->data['start_date'] = "";
			$this->data['end_date'] = "";
			$this->data['status'] = "";
		}

		if ($this->akses["lihat"]) {
			$js_header_script = "<script>
								$(document).ready(function() {
									$('#tabel_manajemen_survey').DataTable({
										responsive: true
									});
								});
							</script>";

			array_push($this->data["js_header_script"], $js_header_script);

			$this->data["daftar_manajemen_survey"] = $this->m_manajemen_survey->daftar_manajemen_survey();

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

	private function tambah($nama, $konten, $link, $poin, $durasi_baca, $start_date, $end_date, $gambar, $status)
	{
		$return = array("status" => false, "error_info" => "");
		$cek = $this->m_manajemen_survey->cek_tambah_manajemen_survey($nama);
		if ($cek["status"]) {
			$data = array(
				"nama" => $nama,
				"konten" => $konten,
				"link" => $link,
				"durasi_baca" => $durasi_baca,
				"poin" => $poin,
				"start_date" => $start_date,
				"end_date" => $end_date,
				"gambar" => $gambar,
				"status" => $status,
				"created_at" => date('Y-m-d H:i:s'),
				"created_by_np" => $_SESSION["no_pokok"],
				"created_by_nama" => $_SESSION["nama"],
				"created_by_kode_unit" => $_SESSION["kode_unit"],
			);
			$this->m_manajemen_survey->tambah($data);

			if ($this->m_manajemen_survey->cek_hasil_manajemen_survey($nama, $konten, $link, $poin, $durasi_baca, $start_date, $end_date, $gambar, $status)) {
				$return["status"] = true;

				$arr_data_insert = $this->m_manajemen_survey->data_manajemen_survey($nama);

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
				$return["error_info"] = "Penambahan Survey <b>Gagal</b> Dilakukan.";
			}
		} else {
			$return["status"] = $cek["status"];
			$return["error_info"] = $cek["error_info"];
		}
		return $return;
	}

	private function ubah($id_manajemen_survey, $nama, $nama_ubah, $konten_ubah, $link_ubah, $poin_ubah, $durasi_baca_ubah, $start_date_ubah, $end_date_ubah, $gambar_ubah, $status_ubah)
	{
		$return = array("status" => false, "error_info" => "");
		$cek = $this->m_manajemen_survey->cek_ubah_manajemen_survey($nama, $nama_ubah);
		if ($cek["status"]) {
			$set = array(
				"nama" => $nama_ubah,
				"konten" => $konten_ubah,
				"link" => $link_ubah,
				"poin" => $poin_ubah,
				"durasi_baca" => $durasi_baca_ubah,
				"start_date" => $start_date_ubah,
				"end_date" => $end_date_ubah,
				"status" => $status_ubah,
				"updated_at" => date('Y-m-d H:i:s'),
				"updated_by_np" => $_SESSION["no_pokok"],
				"updated_by_nama" => $_SESSION["nama"],
				"updated_by_kode_unit" => $_SESSION["kode_unit"],
			);

			if (!empty($gambar_ubah)) $set["gambar"] = $gambar_ubah;

			$arr_data_lama = $this->m_manajemen_survey->data_manajemen_survey($nama);
			$log_data_lama = "";

			foreach ($arr_data_lama as $key => $value) {
				if (strcmp($key, "id") != 0) {
					if (!empty($log_data_lama)) {
						$log_data_lama .= "<br>";
					}
					$log_data_lama .= "$key = $value";
				}
			}

			$this->m_manajemen_survey->ubah($set, $id_manajemen_survey);

			if ($this->m_manajemen_survey->cek_hasil_manajemen_survey($nama_ubah, $konten_ubah, $link_ubah, $poin_ubah, $durasi_baca_ubah, $start_date_ubah, $end_date_ubah, $gambar_ubah, $status_ubah)) {
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
				$return["error_info"] = "Perubahan Survey <b>Gagal</b> Dilakukan.";
			}
		} else {
			$return["status"] = $cek["status"];
			$return["error_info"] = $cek["error_info"];
		}

		return $return;
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
}
	
	/* End of file manajemen_survey.php */
	/* Location: ./application/controllers/poin_reward/manajemen_survey.php */
