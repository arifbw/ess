<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Login extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		$meta = meta_data();

		foreach ($meta as $key => $value) {
			$this->data[$key] = $value;
		}

		$this->load->model("m_login");
		$this->load->model("m_keycloak");
		$this->folder_view = 'login/';

		$this->data["is_with_sidebar"] = false;

		$this->data['judul'] = __CLASS__;
		$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
	}

	public function index()
	{
        $this->output->set_header('X-Frame-Options: DENY');
		if (!empty($this->session->userdata("username"))) {
			redirect(base_url());
		}

		$this->data["warning"] = "";

		if ($this->input->post()) {
			if (!empty($this->input->post("username")) and !empty($this->input->post("password"))) {
				$login = $this->login($this->input->post("username"), $this->input->post("password"));
				if ($login["status"]) {
					if ($login["default_password"]) {
						redirect(base_url($this->m_setting->ambil_url_modul("Profil")));
					} else {
						redirect(base_url());
					}
				} else {
					$this->data["warning"] = "Kombinasi <b><i>Username</i></b> atau <b><i>Password</i></b> tidak tepat.";
				}
			} else if (empty($this->input->post("username")) and empty($this->input->post("password"))) {
				$this->data["warning"] = "<b><i>Username</i></b> dan <b><i>Password</i></b> harus diisi.";
			} else if (empty($this->input->post("username"))) {
				$this->data["warning"] = "<b><i>Username</i></b> harus diisi.";
			} else if (empty($this->input->post("password"))) {
				$this->data["warning"] = "<b><i>Password</i></b> harus diisi.";
			}
		}

		$this->data['url_lupa_password'] = $this->m_setting->ambil_url_modul("Lupa Password");
		$this->data['url_portal'] = $this->m_setting->ambil_pengaturan("URL Portal");
		$this->data['content'] = $this->folder_view . "login";
		$this->load->view('template', $this->data);
	}

	// fitur dimatikan karena sudah ada /ava
	// private function login($username, $password)
	// {
	// 	$pass_ori = $password;
	// 	$password = md5($password);

	// 	$panjang_u_kc = strlen($username);

	// 	if ($panjang_u_kc <= 5) {
	// 		$search = $this->m_keycloak->getUserByFirstName($username);

	// 		if (@$search['username']) {
	// 			$username = $search['username'];
	// 		}
	// 	}

	// 	$token = $this->m_keycloak->auth($username, $pass_ori);
	// 	$user_sso = $this->m_keycloak->getUserId($token);

	// 	if (!$token) {
	// 		redirect('login');
	// 	}

	// 	$login = $this->m_login->validasi_username($user_sso->username);
		
	// 	if ($user_sso && empty($login['status'])) {

	// 		$cek = $this->m_login->cek_user($user_sso->username);

	// 		if ($cek['status'] && $cek['is_active'] == 0) {
	// 			$this->db->where("(username = '$user_sso->username' OR no_pokok = '$user_sso->firstName')");
	// 			$this->db->set("status", "1");
	// 			$this->db->update('usr_pengguna');
	// 		} else {
	// 			$password =	$this->m_keycloak->getPassword($user_sso->firstName);
	// 			$decrypt = $this->m_keycloak->decrypt($password);
	// 			$usr = array(
	// 				'username' => $user_sso->username,
	// 				'no_pokok' => $user_sso->firstName,
	// 				'password' => md5($decrypt),
	// 				'status' => 1,
	// 				'waktu_daftar' => date("Y-m-d H:i:s"),
	// 				'last_login' => date("Y-m-d H:i:s"),
	// 				'last_change_password' => date("Y-m-d H:i:s"),
	// 				'default_password' => 0,
	// 				'face_id' => '',
	// 				// Tambahkan kolom lain sesuai kebutuhan
	// 			);
	// 			$this->db->insert('usr_pengguna', $usr);
	// 		}

	// 		$login = $this->m_login->validasi_username($user_sso->username);
	// 	}

	// 	if ($login["status"]) {
	// 		//sync ke sso 
	// 		$access = $this->m_keycloak->getRoleMapping($token, $user_sso->id);

	// 		if (empty($access)) {

	// 			$this->db->where("(username = '$user_sso->username' OR no_pokok = '$user_sso->firstName')");
	// 			$this->db->set("status", "0");
	// 			$this->db->update('usr_pengguna');

	// 			redirect(base_url());
	// 		}

	// 		$grup = $this->m_login->ambil_grup($login["id_pengguna"]);
	// 		$cekRole = $this->m_keycloak->matchRole($grup, $access, $login["id_pengguna"]);

	// 		$list_id_grup = "";
	// 		$list_nama_grup = "";


	// 		for ($i = 0; $i < count($cekRole); $i++) {
	// 			if (!empty($list_id_grup)) {
	// 				$list_id_grup .= "|";
	// 			}
	// 			$list_id_grup .= $cekRole[$i]["id"];

	// 			if (!empty($list_nama_grup)) {
	// 				$list_nama_grup .= "|";
	// 			}
	// 			$list_nama_grup .= $cekRole[$i]["nama"];
	// 		}

	// 		$karyawan 				= $this->m_login->ambil_data_karyawan($login["no_pokok"]);
	// 		$list_pengadministrasi	= $this->m_login->list_pengadministrasi($login["id_pengguna"]);

	// 		if (in_array(4, array_column($cekRole, 'id'))) {
	// 			$unit_kerja = $this->m_keycloak->getUnitKerja($token, $user_sso->id);
	// 			foreach ($unit_kerja as $item) {
	// 				$nameParts = explode('_', $item->name);
	// 				$item->name = $nameParts[1];
	// 				$item->kode_unit = $nameParts[0];
	// 			}

	// 			$list_pengadministrasi	= $this->m_keycloak->matchUnitKerja($list_pengadministrasi, $unit_kerja, $login["id_pengguna"]);
	// 		} else {

	// 			$tabel = 'usr_pengadministrasi';
	// 			$this->db->where('id_pengguna', $login["id_pengguna"]);
	// 			$this->db->delete($tabel);

	// 			$list_pengadministrasi	= $this->m_login->list_pengadministrasi($login["id_pengguna"]);
	// 		}

	// 		$masa_aktif_password	= $this->m_setting->ambil_pengaturan("Masa Aktif Password");

	// 		//25 10 2021 - Tri Wibowo 7648 - Non aktif check password berkala, karena login via portal
	// 		//$sisa_usia_password		= (int)substr($masa_aktif_password,0,strpos($masa_aktif_password," ")) - (int)$login["usia_password"];

	// 		$sisa_usia_password = 1;
	// 		$foto_profile = base_url("foto/profile/" . $this->m_login->ambil_foto_karyawan($login["no_pokok"]));

	// 		$grup_pengguna_standar = $this->m_setting->ambil_pengaturan("Grup Pengguna Standar");

	// 		$id_grup = $cekRole[0]["id"];

	// 		for ($i = 0; $i < count($cekRole); $i++) {
	// 			if (strcmp($grup_pengguna_standar, $cekRole[$i]["nama"]) == 0) {
	// 				$id_grup = $cekRole[$i]["id"];
	// 			}
	// 		}

	// 		$session_data = array(
	// 			"id_pengguna" => $login["id_pengguna"],
	// 			"username" => $login["username"],
	// 			"list_id_grup" => $list_id_grup,
	// 			"list_nama_grup" => $list_nama_grup,
	// 			"list_pengadministrasi" => $list_pengadministrasi,
	// 			"grup" => $id_grup,
	// 			"no_pokok" => $login["no_pokok"],
	// 			"nama" => $karyawan["nama"],
	// 			"kode_unit" => $karyawan["kode_unit"],
	// 			"nama_unit" => $karyawan["nama_unit"],
	// 			"nama_unit_singkat" => $karyawan["nama_unit_singkat"],
	// 			"kode_jabatan" => $karyawan["kode_jabatan"],
	// 			"nama_jabatan" => $karyawan["nama_jabatan"],
	// 			"foto_profile" => $foto_profile,
	// 			"browse_as_mode" => false,
	// 			"browse_as_id_pengguna_original" => "",
	// 			"browse_as_username_original" => "",
	// 			"browse_as_list_id_grup_original" => "",
	// 			"browse_as_list_nama_grup_original" => "",
	// 			"browse_as_grup_original" => "",
	// 			"browse_as_no_pokok_original" => "",
	// 			"browse_as_nama_original" => "",
	// 			"browse_as_kode_unit_original" => "",
	// 			"browse_as_nama_unit_original" => "",
	// 			"browse_as_kode_jabatan_original" => "",
	// 			"browse_as_nama_jabatan_original" => "",
	// 			"browse_as_foto_profile" => "",
	// 			"sisa_usia_password" => $sisa_usia_password,
	// 			"asal_login" => $this->m_setting->ambil_pengaturan("Nama Aplikasi"),
	// 			"access_token" => $token,
	// 			'id_sso' =>  $user_sso->id
	// 		);
	// 		// Add user data in session
	// 		$this->session->set_userdata($session_data);

	// 		$log = array(
	// 			"id_pengguna" => $this->session->userdata("id_pengguna"),
	// 			"id_modul" => $this->data['id_modul'],
	// 			"deskripsi" => preg_replace("/_/", " ", __FUNCTION__),
	// 			"kondisi_baru" => "berhasil login<br>nama grup = " . $grup[0]["nama"],
	// 			"alamat_ip" => $this->data["ip_address"],
	// 			"waktu" => date("Y-m-d H:i:s")
	// 		);
	// 		$this->m_log->tambah($log);

	// 		$this->m_login->ubah_waktu_login($username);

	// 		# insert log
	// 		insert_login_history([
	// 			'file' => __FILE__ . '/' . __FUNCTION__,
	// 			'description' => 'success',
	// 			'input_from' => 'web',
	// 			'np' => $login["no_pokok"],
	// 			'user_id' => $login["id_pengguna"]
	// 		]);
	// 		# END: insert log
	// 	} else {
	// 		# insert log
	// 		insert_login_history([
	// 			'file' => __FILE__ . '/' . __FUNCTION__,
	// 			'description' => 'failed',
	// 			'input_from' => 'web',
	// 			'username' => $username
	// 		]);
	// 		# END: insert log
	// 	}

	// 	$return = array("status" => $login["status"], "default_password" => $login["default_password"]);

	// 	return $return;
	// }

	public function login_sso()
	{

		$token = htmlspecialchars($this->input->get('access_token'));
		$refresh_token = htmlspecialchars($this->input->get('refresh_token'));

		$user_sso = $this->m_keycloak->getUserId($token);

		$access = $this->m_keycloak->getRoleMapping($token, $user_sso->id);

		if (empty($access)) {

			$this->db->where("(username = '$user_sso->username' OR no_pokok = '$user_sso->firstName')");
			$this->db->set("status", "0");
			$this->db->update('usr_pengguna');

			redirect(base_url());
		}

		//habib sso, ganti pagai sistem sso, 2024-04-04
		//Tri Wibowo - 7648 - 24 02 2021, karena di portal kadang ada pkwt yang ganti np, maka di ess menyesuaikan np tersebut 
		// $this->m_login->update_np_dari_portal($user_sso->username, $user_sso->firstName);
		//end of Tri Wibowo - 7648 - 24 02 2021

		$login = $this->m_login->cek_user($user_sso->username);

		if ($login['status']) {
			if ($login['is_active'] == 0) {
				$this->db->where("(username = '$user_sso->username' OR no_pokok = '$user_sso->firstName')");
				$this->db->set("status", "1");
				$this->db->update('usr_pengguna');
			}
		} else {
			$password =	$this->m_keycloak->getPassword($user_sso->firstName);
			$decrypt = $this->m_keycloak->decrypt($password);
			$usr = array(
				'username' => $user_sso->username,
				'no_pokok' => $user_sso->firstName,
				'password' => md5($decrypt),
				'status' => 1,
				'waktu_daftar' => date("Y-m-d H:i:s"),
				'last_login' => date("Y-m-d H:i:s"),
				'last_change_password' => date("Y-m-d H:i:s"),
				'default_password' => 0,
				'face_id' => '',
				// Tambahkan kolom lain sesuai kebutuhan
			);
			$this->db->insert('usr_pengguna', $usr);

			$login = $this->m_login->cek_user($user_sso->username);
		}

		if ($login["status"]) {
			$grup = $this->m_login->ambil_grup($login["id_pengguna"]);

			$cekRole = $this->m_keycloak->matchRole($grup, $access, $login["id_pengguna"]);

			$list_id_grup = "";
			$list_nama_grup = "";


			for ($i = 0; $i < count($cekRole); $i++) {
				if (!empty($list_id_grup)) {
					$list_id_grup .= "|";
				}
				$list_id_grup .= $cekRole[$i]["id"];

				if (!empty($list_nama_grup)) {
					$list_nama_grup .= "|";
				}
				$list_nama_grup .= $cekRole[$i]["nama"];
			}

			$karyawan 				= $this->m_login->ambil_data_karyawan($login["no_pokok"]);

			$list_pengadministrasi	= $this->m_login->list_pengadministrasi($login["id_pengguna"]);

			if (in_array(4, array_column($cekRole, 'id'))) {
				$unit_kerja = $this->m_keycloak->getUnitKerja($token, $user_sso->id);
				foreach ($unit_kerja as $item) {
					$nameParts = explode('_', $item->name);
					$item->name = $nameParts[1];
					$item->kode_unit = $nameParts[0];
				}

				$list_pengadministrasi	= $this->m_keycloak->matchUnitKerja($list_pengadministrasi, $unit_kerja, $login["id_pengguna"]);
			} else {

				$tabel = 'usr_pengadministrasi';
				$this->db->where('id_pengguna', $login["id_pengguna"]);
				$this->db->delete($tabel);

				$list_pengadministrasi	= $this->m_login->list_pengadministrasi($login["id_pengguna"]);
			}

			$masa_aktif_password	= $this->m_setting->ambil_pengaturan("Masa Aktif Password");

			//25 10 2021 - Tri Wibowo 7648 - Non aktif check password berkala, karena login via portal
			//$sisa_usia_password		= (int)substr($masa_aktif_password,0,strpos($masa_aktif_password," ")) - (int)$login["usia_password"];

			$sisa_usia_password = 1;
			$foto_profile = base_url("foto/profile/" . $this->m_login->ambil_foto_karyawan($login["no_pokok"]));

			$grup_pengguna_standar = $this->m_setting->ambil_pengaturan("Grup Pengguna Standar");

			$id_grup = $cekRole[0]["id"];

			for ($i = 0; $i < count($cekRole); $i++) {
				if (strcmp($grup_pengguna_standar, $cekRole[$i]["nama"]) == 0) {
					$id_grup = $cekRole[$i]["id"];
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
				"asal_login" => $this->m_setting->ambil_pengaturan("Nama Aplikasi"),
				"access_token" => $token,
				'id_sso' =>  $user_sso->id
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

			$this->m_login->ubah_waktu_login($login["username"]);

			# insert log
			insert_login_history([
				'file' => __FILE__ . '/' . __FUNCTION__,
				'description' => 'success',
				'input_from' => 'web',
				'np' => $login["no_pokok"],
				'user_id' => $login["id_pengguna"]
			]);
			# END: insert log

			return redirect(base_url());
		} else {
			# insert log
			insert_login_history([
				'file' => __FILE__ . '/' . __FUNCTION__,
				'description' => 'failed',
				'input_from' => 'web',
				'username' => $login["username"]
			]);
			# END: insert log
		}

		$return = array("status" => $login["status"], "default_password" => $login["default_password"]);

		return redirect(base_url());
	}

	public function login_portal($menu, $encrypt_uid)
	{
		$decrypted_txt = $this->openssl_decrypt('decrypt', $encrypt_uid);

		if ($decrypted_txt == false) {
			redirect(base_url(''));
		}

		list($random, $username, $np, $datetime_request, $chars) = explode("|", $decrypted_txt);

		if (empty($username)) {
			redirect(base_url(''));
		}

		//Tri Wibowo - 7648 - 24 02 2021, karena di portal kadang ada pkwt yang ganti np, maka di ess menyesuaikan np tersebut 
		$this->m_login->update_np_dari_portal($username, $np);
		//end of Tri Wibowo - 7648 - 24 02 2021			

		$login = $this->m_login->validasi_username($username);

		if (!$login["status"]) {
			$this->load->helper("pengguna_helper");

			$tambah = tambah_pengguna($np, $username, 1);
			if ($tambah["status"]) {
				$this->load->model("administrator/m_grup_pengguna");

				//set group 'pengguna' untuk user tersebut
				$arr_grup_pengguna = array($this->m_grup_pengguna->ambil_id_grup_pengguna("Pengguna"));
				$simpan_grup = simpan_grup($username, $arr_grup_pengguna);

				/*
					//jika environment training
					//if(base_url()=="http://10.30.10.198/ess/")
					if(1==1)
					{					
						//set group 'Pengadministrasi Unit Kerja' untuk user tersebut
						$ambil_kode_unit = $this->m_grup_pengguna->ambil_kode_unit($np);
					
						if($ambil_kode_unit)
						{
							//cari pengguna_id
							$this->load->model("administrator/m_pengguna");
							$data_pengguna = $this->m_pengguna->data_pengguna($username);
							$id_pengguna = $data_pengguna["id"];
							
							//insert group pengguna
							$grup_unit_kerja = $this->m_grup_pengguna->ambil_id_grup_pengguna("Pengadministrasi Unit Kerja");							
							$arr_grup_unit_kerja=array();
							$arr_grup_unit_kerja=array("id_pengguna" => $id_pengguna,"id_grup_pengguna"=>$grup_unit_kerja);							
							$this->db->insert('usr_pengguna_grup_pengguna',$arr_grup_unit_kerja);
							
							//insert group pengadministrasi
							$arr_tambah_pengadministrasi=array("id_pengguna" => $id_pengguna,"kode_unit"=>$ambil_kode_unit);														
							$this->db->insert('usr_pengadministrasi',$arr_tambah_pengadministrasi);
						
						}
					}	
				*/



				if ($simpan_grup["status"]) {
					$login = $this->m_login->validasi_username($username);
				}
			}
		}

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
			$sisa_usia_password		= (int)substr($masa_aktif_password, 0, strpos($masa_aktif_password, " ")) - (int)$login["usia_password"];

			$foto_profile = base_url("foto/profile/" . $this->m_login->ambil_foto_karyawan($login["no_pokok"]));

			//die(var_dump($sisa_usia_password));

			/* // Report all errors
				error_reporting(E_ALL);

				// Display errors in output
				ini_set('display_errors', 1); */

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
				"asal_login" => "Portal"
			);
			// Add user data in session
			$this->session->set_userdata($session_data);
			//die(var_dump($_SESSION));
			$log = array(
				"id_pengguna" => 0,
				"id_modul" => $this->data['id_modul'],
				"deskripsi" => preg_replace("/_/", " ", __FUNCTION__),
				"kondisi_baru" => "berhasil login<br>nama grup = " . $grup[0]["nama"],
				"alamat_ip" => $this->data["ip_address"],
				"waktu" => date("Y-m-d H:i:s")
			);
			$this->m_log->tambah($log);

			$this->m_login->ubah_waktu_login($username);
		}

		if ($menu == 'dashboard') {
			redirect(base_url());
		} else
			if ($menu == 'kehadiran') {
			redirect(base_url('kehadiran/data_kehadiran'));
		}
	}

	private function openssl_decrypt($action, $string)
	{
		$output = false;
		$encrypt_method = "AES-256-CBC";
		$secret_key = 'fajaSsd1fjDwASjA12SAGSHga3yus' . date('Ymd');
		$secret_iv = 'ASsadkmjku4jLOIh2jfGda5' . date('Ymd');
		// hash
		$key = hash('sha256', $secret_key);

		// iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
		$iv = substr(hash('sha256', $secret_iv), 0, 16);
		if ($action == 'encrypt') {
			$output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
			$output = base64_encode($output);
		} else if ($action == 'decrypt') {
			$output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);

			$pisah 				= explode('|', $output);
			$datetime_request 	= $pisah[3];
			$datetime_expired 	= date('Y-m-d H:i:s', strtotime('+120 seconds', strtotime($datetime_request)));

			$datetime_now		= date('Y-m-d H:i:s');




			if ($datetime_now > $datetime_expired || !$datetime_request) {
				$output = false;
			}
		}
		return $output;
	}

	public function set_session_grup($id_grup_pengguna)
	{
		// die($id_grup_pengguna);
		$dataString = $this->session->userdata("list_id_grup");
		$dataArray = explode("|", $dataString);

		$array = $dataArray;
		
		$data = $id_grup_pengguna;

		if (in_array($data, $array)) {
			$this->session->set_userdata("grup", $id_grup_pengguna);
		} else {
			redirect('https://ess.peruri.co.id');
		}	

		// J971 robi purnomo, menonaktifkan session injection
	}

	public function user_switch($username = "")
	{
		$login = $this->m_login->validasi_username($username);

		if ($login["status"]) {
			$id_pengguna_lama = $this->session->userdata("id_pengguna");
			$username_lama = $this->session->userdata("username");

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

			$karyawan = $this->m_login->ambil_data_karyawan($login["no_pokok"]);
			$list_pengadministrasi	= $this->m_login->list_pengadministrasi($login["id_pengguna"]);

			$foto_profile = base_url("foto/profile/" . $this->m_login->ambil_foto_karyawan($login["no_pokok"]));

			if (!$this->session->userdata("browse_as_mode")) {
				$session_data = array(
					"id_pengguna" => $login["id_pengguna"],
					"username" => $login["username"],
					"list_id_grup" => $list_id_grup,
					"list_nama_grup" => $list_nama_grup,
					"list_pengadministrasi" => $list_pengadministrasi,
					"grup" => $grup[0]["id"],
					"no_pokok" => $login["no_pokok"],
					"nama" => $karyawan["nama"],
					"kode_unit" => $karyawan["kode_unit"],
					"nama_unit" => $karyawan["nama_unit"],
					"kode_jabatan" => $karyawan["kode_jabatan"],
					"nama_jabatan" => $karyawan["nama_jabatan"],
					"foto_profile" => $foto_profile,
					"browse_as_mode" => true,
					"browse_as_id_pengguna_original" => $this->session->userdata("id_pengguna"),
					"browse_as_username_original" => $this->session->userdata("username"),
					"browse_as_list_id_grup_original" => $this->session->userdata("list_id_grup"),
					"browse_as_list_nama_grup_original" => $this->session->userdata("list_nama_grup"),
					"browse_as_list_pengadministrasi_original" => $this->session->userdata("list_pengadministrasi"),
					"browse_as_grup_original" => $this->session->userdata("grup"),
					"browse_as_no_pokok_original" => $this->session->userdata("no_pokok"),
					"browse_as_nama_original" => $this->session->userdata("nama"),
					"browse_as_kode_unit_original" => $this->session->userdata("kode_unit"),
					"browse_as_nama_unit_original" => $this->session->userdata("nama_unit"),
					"browse_as_kode_jabatan_original" => $this->session->userdata("kode_jabatan"),
					"browse_as_nama_jabatan_original" => $this->session->userdata("nama_jabatan"),
					"browse_as_foto_profile" => $this->session->userdata("foto_profile")
				);
			} else {
				$session_data = array(
					"id_pengguna" => $login["id_pengguna"],
					"username" => $login["username"],
					"list_id_grup" => $list_id_grup,
					"list_nama_grup" => $list_nama_grup,
					"list_pengadministrasi" => $list_pengadministrasi,
					"grup" => $grup[0]["id"],
					"no_pokok" => $login["no_pokok"],
					"nama" => $karyawan["nama"],
					"kode_unit" => $karyawan["kode_unit"],
					"nama_unit" => $karyawan["nama_unit"],
					"kode_jabatan" => $karyawan["kode_jabatan"],
					"nama_jabatan" => $karyawan["nama_jabatan"],
					"foto_profile" => $foto_profile,
					"browse_as_mode" => true,
					"browse_as_id_pengguna_original" => $this->session->userdata("browse_as_id_pengguna_original"),
					"browse_as_username_original" => $this->session->userdata("browse_as_username_original"),
					"browse_as_list_id_grup_original" => $this->session->userdata("browse_as_list_id_grup_original"),
					"browse_as_list_nama_grup_original" => $this->session->userdata("browse_as_list_nama_grup_original"),
					"browse_as_list_pengadministrasi_original" => $this->session->userdata("list_pengadministrasi"),
					"browse_as_grup_original" => $this->session->userdata("browse_as_grup_original"),
					"browse_as_no_pokok_original" => $this->session->userdata("browse_as_no_pokok_original"),
					"browse_as_nama_original" => $this->session->userdata("browse_as_nama_original"),
					"browse_as_kode_unit_original" => $this->session->userdata("browse_as_kode_unit_original"),
					"browse_as_nama_unit_original" => $this->session->userdata("browse_as_nama_unit_original"),
					"browse_as_kode_jabatan_original" => $this->session->userdata("browse_as_kode_jabatan_original"),
					"browse_as_nama_jabatan_original" => $this->session->userdata("browse_as_nama_jabatan_original"),
					"browse_as_foto_profile" => $this->session->userdata("browse_as_foto_profile")
				);
			}
			// Add user data in session
			$this->session->set_userdata($session_data);

			$id_pengguna_baru = $this->session->userdata("id_pengguna");
			$username_baru = $this->session->userdata("username");

			$log = array(
				"id_pengguna" => $id_pengguna_lama,
				"id_modul" => $this->data['id_modul'],
				"deskripsi" => preg_replace("/_/", " ", __FUNCTION__),
				"kondisi_lama" => "username = $username_lama",
				"kondisi_baru" => "username = $username_baru",
				"alamat_ip" => $this->data["ip_address"],
				"waktu" => date("Y-m-d H:i:s")
			);
			$this->m_log->tambah($log);
		}
	}

	public function user_switch_off()
	{
		if ($this->session->userdata("browse_as_mode")) {
			$id_pengguna_lama = $this->session->userdata("id_pengguna");
			$username_lama = $this->session->userdata("username");
			$session_data = array(
				"id_pengguna" => $this->session->userdata("browse_as_id_pengguna_original"),
				"username" => $this->session->userdata("browse_as_username_original"),
				"list_id_grup" => $this->session->userdata("browse_as_list_id_grup_original"),
				"list_nama_grup" => $this->session->userdata("browse_as_list_nama_grup_original"),
				"list_pengadministrasi" => $this->session->userdata("list_pengadministrasi"),
				"grup" => $this->session->userdata("browse_as_grup_original"),
				"no_pokok" => $this->session->userdata("browse_as_no_pokok_original"),
				"nama" => $this->session->userdata("browse_as_nama_original"),
				"kode_unit" => $this->session->userdata("browse_as_kode_unit_original"),
				"nama_unit" => $this->session->userdata("browse_as_nama_unit_original"),
				"kode_jabatan" => $this->session->userdata("browse_as_kode_jabatan_original"),
				"nama_jabatan" => $this->session->userdata("browse_as_nama_jabatan_original"),
				"foto_profile" => $this->session->userdata("browse_as_foto_profile"),
				"browse_as_mode" => false,
				"browse_as_id_pengguna_original" => "",
				"browse_as_username_original" => "",
				"browse_as_list_id_grup_original" => "",
				"browse_as_list_nama_grup_original" => "",
				"browse_as_list_pengadministrasi_original" => "",
				"browse_as_grup_original" => "",
				"browse_as_no_pokok_original" => "",
				"browse_as_nama_original" => "",
				"browse_as_kode_unit_original" => "",
				"browse_as_nama_unit_original" => "",
				"browse_as_kode_jabatan_original" => "",
				"browse_as_nama_jabatan_original" => "",
				"browse_as_foto_profile" => ""
			);
			// Add user data in session
			$this->session->set_userdata($session_data);

			$id_pengguna_baru = $this->session->userdata("id_pengguna");
			$username_baru = $this->session->userdata("username");

			$log = array(
				"id_pengguna" => $id_pengguna_baru,
				"id_modul" => $this->data['id_modul'],
				"deskripsi" => preg_replace("/_/", " ", __FUNCTION__),
				"kondisi_lama" => "username = $username_lama",
				"kondisi_baru" => "username = $username_baru",
				"alamat_ip" => $this->data["ip_address"],
				"waktu" => date("Y-m-d H:i:s")
			);
			$this->m_log->tambah($log);
		}
	}
}
	/* End of file login.php */
	/* Location: ./application/controllers/login.php */
