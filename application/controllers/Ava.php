<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ava extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		$meta = meta_data();

		foreach ($meta as $key => $value) {
			$this->data[$key] = $value;
		}

		$this->load->model("m_login");

		$this->folder_view = 'login/';

		$this->data["is_with_sidebar"] = false;

		$this->data['judul'] = __CLASS__;
		$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
	}

	public function index()
	{
		$is_active_ava = @$this->config->item('AVA_LOGIN');

		if ($is_active_ava != '1') {
			redirect(base_url());
		}

		if (!empty($this->session->userdata("username"))) {
			redirect(base_url());
		}
		$this->data["warning"] = "";

		$data['captcha'] = $this->generateCaptcha();

		$this->data['url_lupa_password'] = $this->m_setting->ambil_url_modul("Lupa Password");
		$this->data['url_portal'] = $this->m_setting->ambil_pengaturan("URL Portal");
		$this->data['content'] = $this->folder_view . "ava";
		$this->load->view('template', $this->data);
	}

	public function login()
	{
		$username = htmlspecialchars($this->input->post('username'));
		$password = $this->input->post('password');
		$captcha = htmlspecialchars($this->input->post('captcha'));
		$password = md5($password);

		// Retrieve the CAPTCHA value from the session
		$captcha_session = $this->session->userdata('captcha');

		// Compare the user's input with the CAPTCHA from the session
		if ($captcha !== $captcha_session) {
			// CAPTCHA validation failed
			// Set an error message
			$this->session->set_flashdata('failed', 'CAPTCHA validation failed. Please try again.');

			// Redirect the user back to the login page
			redirect('ava');
		}

		$login = $this->m_login->validasi_username_password($username, $password);


		if ($login["status"]) {
			$grup = $this->m_login->ambil_grup($login["id_pengguna"]);

			$list_id_grup = "";
			$list_nama_grup = "";


			for ($i = 0; $i < count($grup); $i++) {
				if (!empty($list_id_grup)) {
					$list_id_grup .= "|";
				}
				$list_id_grup .= $grup[$i]["id"];

				if (!empty($list_nama_grup)) {
					$list_nama_grup .= "|";
				}
				$list_nama_grup .= $grup[$i]["nama"];
			}

			$karyawan 				= $this->m_login->ambil_data_karyawan($login["no_pokok"]);
			$list_pengadministrasi	= $this->m_login->list_pengadministrasi($login["id_pengguna"]);

			$masa_aktif_password	= $this->m_setting->ambil_pengaturan("Masa Aktif Password");

			//25 10 2021 - Tri Wibowo 7648 - Non aktif check password berkala, karena login via portal
			//$sisa_usia_password		= (int)substr($masa_aktif_password,0,strpos($masa_aktif_password," ")) - (int)$login["usia_password"];

			$sisa_usia_password = 1;
			$foto_profile = base_url("foto/profile/" . $this->m_login->ambil_foto_karyawan($login["no_pokok"]));

			$grup_pengguna_standar = $this->m_setting->ambil_pengaturan("Grup Pengguna Standar");

			$id_grup = $grup[0]["id"];

			for ($i = 0; $i < count($grup); $i++) {
				if (strcmp($grup_pengguna_standar, $grup[$i]["nama"]) == 0) {
					$id_grup = $grup[$i]["id"];
				}
			}

			$session_data = array(
				"id_pengguna" => $login["id_pengguna"],
				"username" => $login["username"],
				"list_id_grup" => $list_id_grup,
				"list_nama_grup" => $list_nama_grup,
				"list_pengadministrasi" => $list_pengadministrasi,
				"grup" => $id_grup,
				"no_pokok" => $login["no_pokok"],
				"nama" => $karyawan["nama"],
				"kode_unit" => $karyawan["kode_unit"],
				"nama_unit" => $karyawan["nama_unit"],
				"nama_unit_singkat" => $karyawan["nama_unit_singkat"],
				"kode_jabatan" => $karyawan["kode_jabatan"],
				"nama_jabatan" => $karyawan["nama_jabatan"],
				"foto_profile" => $foto_profile,
				"browse_as_mode" => false,
				"browse_as_id_pengguna_original" => "",
				"browse_as_username_original" => "",
				"browse_as_list_id_grup_original" => "",
				"browse_as_list_nama_grup_original" => "",
				"browse_as_grup_original" => "",
				"browse_as_no_pokok_original" => "",
				"browse_as_nama_original" => "",
				"browse_as_kode_unit_original" => "",
				"browse_as_nama_unit_original" => "",
				"browse_as_kode_jabatan_original" => "",
				"browse_as_nama_jabatan_original" => "",
				"browse_as_foto_profile" => "",
				"sisa_usia_password" => $sisa_usia_password,
				"asal_login" => $this->m_setting->ambil_pengaturan("Nama Aplikasi")
			);
			// Add user data in session
			$this->session->set_userdata($session_data);

			$log = array(
				"id_pengguna" => $this->session->userdata("id_pengguna"),
				"id_modul" => $this->data['id_modul'],
				"deskripsi" => preg_replace("/_/", " ", __FUNCTION__),
				"kondisi_baru" => "berhasil login<br>nama grup = " . $grup[0]["nama"],
				"alamat_ip" => $this->data["ip_address"],
				"waktu" => date("Y-m-d H:i:s")
			);
			$this->m_log->tambah($log);

			$this->m_login->ubah_waktu_login($username);

			# insert log
			insert_login_history([
				'file' => __FILE__ . '/' . __FUNCTION__,
				'description' => 'success',
				'input_from' => 'web',
				'np' => $login["no_pokok"],
				'user_id' => $login["id_pengguna"]
			]);

			redirect(base_url() . 'ava');
			# END: insert log
		} else {
			# insert log
			insert_login_history([
				'file' => __FILE__ . '/' . __FUNCTION__,
				'description' => 'failed',
				'input_from' => 'web',
				'username' => $username
			]);
			$this->session->set_flashdata('failed', 'Username atau Password Salah');
			// redirect(base_url());
		}

		$return = array("status" => $login["status"], "default_password" => $login["default_password"]);

		return 	redirect(base_url() . 'ava');
	}

	public function create_user_sso()
	{
		$username = htmlspecialchars($this->input->get('username'));
		$password = $this->input->get('password');
		$np = htmlspecialchars($this->input->get('no_pokok'));

		$validate_pass = $this->validate_password($password);

		if (!$validate_pass) {
			echo json_encode([
				'status' => false,
				'message' => 'Password harus minimal 8 karakter, berisi huruf besar, huruf kecil, angka, dan simbol.',
			]);
			return;
		}

		$cek = $this->m_login->cek_user($username);

		if (!$cek['status']) {
			$usr = array(
				'username' => $username,
				'no_pokok' => $np,
				'password' => md5($password),
				// 'password_sso' => md5($password),
				'status' => 1,
				'waktu_daftar' => date("Y-m-d H:i:s"),
				'last_login' => date("Y-m-d H:i:s"),
				'last_change_password' => date("Y-m-d H:i:s"),
				'default_password' => 0,
				'face_id' => '',
				// Tambahkan kolom lain sesuai kebutuhan
			);
			$this->db->insert('usr_pengguna', $usr);
		}
	}

	public function update_user_sso()
	{
		$username = htmlspecialchars($this->input->get('username'));
		$np = htmlspecialchars($this->input->get('no_pokok'));
		$np_lama = htmlspecialchars($this->input->get('no_pokok_lama'));
		$nama = htmlspecialchars($this->input->get('nama'));
		$email = htmlspecialchars($this->input->get('email'));
		$status = htmlspecialchars($this->input->get('status'));

		$data = $this->db->select("a.*")
			->from('usr_pengguna' . " a")
			->where("a.username", $username)
			->or_where('a.no_pokok', $np_lama)
			->get();
		$result = $data->row_array();

		if ($result) {
			$usr = array(
				'username' => $username,
				'no_pokok' => $np,
				'default_password' => 0,
			);

			if (isset($status)) {
				$usr['status'] = $status;
			}

			$this->db->where('id', $result['id']); // Kondisi WHERE, misalnya berdasarkan ID
			$this->db->update('usr_pengguna', $usr);
			return true;
		}
	}

	function update_pass_from_sso()
	{
		$pass = $this->input->get('password');
		$np = htmlspecialchars($this->input->get('no_pokok'));
		// $key = htmlspecialchars($this->input->get('key'));

		$validate_pass = $this->validate_password($pass);

		if (!$validate_pass) {
			echo json_encode([
				'status' => false,
				'message' => 'Password harus minimal 8 karakter, berisi huruf besar, huruf kecil, angka, dan simbol.',
			]);
			return;
		}

		// if ($key == $this->m_keycloak->encrypt($np)) {
		$this->db->where("no_pokok", $np);
		$this->db->where("status", 1);
		$this->db->set("password", md5($pass));
		$this->db->set("default_password", 0);
		$this->db->set("last_change_password", date("Y-m-d H:i:s"));
		$this->db->update('usr_pengguna');
		// }
	}

	function validate_password($password)
	{
		$regex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';
		if (!preg_match($regex, $password)) {
			return FALSE;
		}
		return TRUE;
	}

	function createRoleSurrounding()
	{
		$nama = htmlspecialchars($this->input->get('nama'));
		$status = htmlspecialchars($this->input->get('status'));
		$id = htmlspecialchars($this->input->get('id'));

		$data = array(
			'nama' => $nama,
			'status' => $status,
		);

		$this->db->insert('sys_grup_pengguna', $data);

		$is_data_saved = $this->db->affected_rows() > 0;

		if ($is_data_saved) {
			$id = $this->db->insert_id();
			// Lakukan sesuatu jika data berhasil disimpan
			echo json_encode([
				'status' => true,
				'data' => [
					'id' => $id,
					'nama' => $nama,
					'status' => $status,
				]
			]);
		} else {
			// Lakukan sesuatu jika data tidak berhasil disimpan
			echo json_encode([
				'status' => false,
				'message' => 'Gagal menyimpan data.',
			]);
		}
	}

	function updateRoleSurrounding()
	{
		$nama = htmlspecialchars($this->input->get('nama'));
		$nama_lama = htmlspecialchars($this->input->get('nama_lama'));
		$status = htmlspecialchars($this->input->get('status'));
		$id = htmlspecialchars($this->input->get('id'));

		$data = $this->db->select("a.*")
			->from('sys_grup_pengguna' . " a")
			->where("a.id", $id)
			->or_where('a.nama', $nama_lama)
			->get();

		$result = $data->row_array();

		if (!$result) return false;

		$usr = array(
			'nama' => $nama,
			'status' => $status,
		);
		$this->db->where('id', $id); // Kondisi WHERE, misalnya berdasarkan ID
		$this->db->update('sys_grup_pengguna', $usr);
		$is_data_updated = $this->db->affected_rows() > 0;

		if ($is_data_updated) {
			echo json_encode([
				'status' => true,
				'data' => [
					'id' => $id,
					'nama' => $nama,
					'status' => $status,
				]
			]);
		} else {
			echo json_encode([
				'status' => false,
				'message' => 'Gagal menyimpan data.',
			]);
		}
	}

	function assignRole()
	{
		$role_id = htmlspecialchars($this->input->get('role_id'));
		$username = htmlspecialchars($this->input->get('username'));
		$no_pokok = htmlspecialchars($this->input->get('no_pokok'));

		$user = $this->db->select('a.id')
			->from('usr_pengguna a')
			->where('a.username', $username)
			->or_where('a.no_pokok', $no_pokok)
			->get();

		$result = $user->row_array();

		if (!$result) return false;

		$existingData = $this->db->where('id_pengguna', $result['id'])
			->where('id_grup_pengguna', $role_id)
			->get('usr_pengguna_grup_pengguna')
			->row_array();

		// Menyiapkan data untuk operasi update atau insert
		$usr = array(
			'id_pengguna' => $result['id'],
			'id_grup_pengguna' => $role_id,
		);

		if ($existingData) {
			return false;
		} else {
			// Jika data belum ada, lakukan operasi insert
			$this->db->insert('usr_pengguna_grup_pengguna', $usr);
			return true;
		}
	}

	function unassignRole()
	{
		$role_id = htmlspecialchars($this->input->get('role_id'));
		$username = htmlspecialchars($this->input->get('username'));
		$no_pokok = htmlspecialchars($this->input->get('no_pokok'));

		$user = $this->db->select('a.id')
			->from('usr_pengguna a')
			->where('a.username', $username)
			->or_where('a.no_pokok', $no_pokok)
			->get();

		$result = $user->row_array();

		if (!$result) return false;

		$existingData = $this->db->where('id_pengguna', $result['id'])
			->where('id_grup_pengguna', $role_id)
			->get('usr_pengguna_grup_pengguna')
			->row_array();

		if ($existingData) {
			$this->db->where('id_pengguna', $result['id']);
			$this->db->where_in('id_grup_pengguna', [$role_id]);
			$this->db->delete('usr_pengguna_grup_pengguna'); // Ganti dengan nama tabel Anda
			return true;
		} else {
			return false;
		}
	}

	function getKodeUnits()
	{
		$result = $this->db->from("mst_satuan_kerja")
			->order_by("kode_unit")
			->get()
			->result_array();

		echo json_encode($result);
		return;
	}

	function assignKodeUnit()
	{
		$kode_unit = htmlspecialchars($this->input->get('kode_unit'));
		$username = htmlspecialchars($this->input->get('username'));
		$no_pokok = htmlspecialchars($this->input->get('no_pokok'));

		$user = $this->db->select('a.id')
			->from('usr_pengguna a')
			->where('a.username', $username)
			->or_where('a.no_pokok', $no_pokok)
			->get();

		$result = $user->row_array();

		if (!$result) return false;

		$existingData = $this->db->where('id_pengguna', $result['id'])
			->where('kode_unit', $kode_unit)
			->get('usr_pengadministrasi')
			->row_array();

		// Menyiapkan data untuk operasi update atau insert
		$usr = array(
			'id_pengguna' => $result['id'],
			'kode_unit' => $kode_unit,
		);

		if ($existingData) {
			return false;
		} else {
			// Jika data belum ada, lakukan operasi insert
			$this->db->insert('usr_pengadministrasi', $usr);
			return true;
		}
	}

	function unassignKodeUnit()
	{
		$kode_unit = htmlspecialchars($this->input->get('kode_unit'));
		$username = htmlspecialchars($this->input->get('username'));
		$no_pokok = htmlspecialchars($this->input->get('no_pokok'));

		$user = $this->db->select('a.id')
			->from('usr_pengguna a')
			->where('a.username', $username)
			->or_where('a.no_pokok', $no_pokok)
			->get();

		$result = $user->row_array();

		if (!$result) return false;

		$existingData = $this->db->where('id_pengguna', $result['id'])
			->where('kode_unit', $kode_unit)
			->get('usr_pengadministrasi')
			->row_array();

		if ($existingData) {
			$this->db->where('id_pengguna', $result['id']);
			$this->db->where_in('kode_unit', [$kode_unit]);
			$this->db->delete('usr_pengadministrasi'); // Ganti dengan nama tabel Anda
			return true;
		} else {
			return false;
		}
	}

	public function generateCaptcha($length = 6)
	{
		$characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$captcha = '';
		$characterLength = strlen($characters);
		for ($i = 0; $i < $length; $i++) {
			$captcha .= $characters[rand(0, $characterLength - 1)];
		}
		// Store the generated captcha in the session for validation
		$this->session->set_userdata('captcha', $captcha);
		return $captcha;
	}
}
