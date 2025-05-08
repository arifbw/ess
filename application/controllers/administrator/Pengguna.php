<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pengguna extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		$meta = meta_data();
		foreach ($meta as $key => $value) {
			$this->data[$key] = $value;
		}

		$this->folder_view = 'administrator/';
		$this->folder_model = 'administrator/';
		$this->folder_ajax_view = $this->folder_view . 'ajax/';
		$this->akses = array();

		$this->load->model($this->folder_model . "m_pengguna");
		$this->load->model("m_keycloak"); //

		$this->data['success'] = "";
		$this->data['warning'] = "";

		$this->data["is_with_sidebar"] = true;
	}

	public function index()
	{
		$this->data['judul'] = "Pengguna";
		$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		$this->akses = akses_helper($this->data['id_modul']);

		izin($this->akses["akses"]);

		$this->data["akses"] = $this->akses;
		$this->data["navigasi_menu"] = menu_helper();
		$this->data['content'] = $this->folder_view . "pengguna";

		array_push($this->data['js_sources'], "administrator/pengguna");

		array_push($this->data['css_plugin_sources'], "select2/select2.min.css");
		array_push($this->data['js_plugin_sources'], "select2/select2.min.js");

			if($this->input->post()){
				if(!strcmp($this->input->post("aksi"),"tambah")){
					izin($this->akses["tambah"]);
					
					$this->data['karyawan'] = $this->input->post("karyawan");
					$this->data['username'] = $this->input->post("username");
					if(!strcmp($this->input->post("status"),"aktif")){
						$this->data['status'] = "1";
					}
					else if(!strcmp($this->input->post("status"),"non aktif")){
						$this->data['status'] = "0";
					}
					
					$tambah = $this->tambah($this->data['karyawan'],$this->data['username'],$this->data['status']);
					
					$this->data['panel_tambah'] = "in";

					if($tambah['status']){
						$this->data['success'] = "Pengguna dengan username <b>".$this->data['username']."</b> berhasil ditambahkan.";

						$this->data["karyawan"] = "";
						$this->data["username"] = "";
						$this->data["status"] = "";
						$this->data['panel_tambah'] = "";
					}
					else{
						$this->data['warning'] = $tambah['error_info'];
					}
				}
				else if(!strcmp($this->input->post("aksi"),"ubah")){
					izin($this->akses["ubah"]);
					
					$this->data['karyawan'] = $this->input->post("karyawan");
					$this->data['karyawan_ubah'] = $this->input->post("karyawan_ubah");
					$this->data['username'] = $this->input->post("username");
					$this->data['username_ubah'] = $this->input->post("username_ubah");
					
					if(!strcmp($this->input->post("status"),"aktif")){
						$this->data['status'] = "1";
					}
					else if(!strcmp($this->input->post("status"),"non aktif")){
						$this->data['status'] = "0";
					}
					
					if(!strcmp($this->input->post("status_ubah"),"aktif")){
						$this->data['status_ubah'] = "1";
					}
					else if(!strcmp($this->input->post("status_ubah"),"non aktif")){
						$this->data['status_ubah'] = "0";
					}

					$ubah = $this->ubah($this->data["karyawan"],$this->data["username"],$this->data["status"],$this->data["karyawan_ubah"],$this->data["username_ubah"],$this->data["status_ubah"]);

					if($ubah["status"]){
						$this->data['success'] = "Perubahan pengguna berhasil dilakukan.";
					}
					else{
						$this->data['warning'] = $ubah['error_info'];
					}

				$this->data["karyawan"] = "";
				$this->data["username"] = "";
				$this->data['status'] = "";
				$this->data['panel_tambah'] = "";
			} else if (!strcmp($this->input->post("aksi"), "ubah_grup")) {
				$username = $this->input->post("username");
				$arr_grup_pengguna = $this->input->post("grup_pengguna");
				$data =	$this->db->select('no_pokok')->where('username', $username)->get('usr_pengguna')->row_array();

				$ubah_grup = $this->simpan_grup($username, $arr_grup_pengguna);
				if ($ubah_grup["status"]) {
					// ubah grup di keycloak
					// $keycloakDeleteRole = $this->m_keycloak->deleteRole($data['no_pokok']);
					// $keycloakAssignRole = $this->m_keycloak->assignRole($arr_grup_pengguna, $data['no_pokok']);

					$this->data['success'] = $ubah_grup["success_info"];
					$res =	$this->ubah_pengadministrasi($username, $this->input->post("is_pilih_unit_kerja"), $this->input->post("admin_unit_kerja"), $this->input->post("admin_unit_kerja_ubah"));
				} else {
					$this->data['warning'] = $ubah['error_info'];
				}

				$this->data['karyawan'] = "";
				$this->data['username'] = "";
				$this->data['status'] = "";
				$this->data['panel_tambah'] = "";
			} else {
				$this->data['karyawan'] = "";
				$this->data['username'] = "";
				$this->data['status'] = "";
				$this->data['panel_tambah'] = "";
			}
		} else {
			$this->data['username'] = "";
			$this->data['karyawan'] = "";
			$this->data['status'] = "";
			$this->data['panel_tambah'] = "";
		}

		if ($this->akses["lihat"]) {
			$js_header_script = "<script>
								$(document).ready(function() {
									$('.select2').select2();
									/* heru mengganti ini 2020-12-03 @04:46
                                    $('#tabel_pengguna').DataTable({
										responsive: true
									});*/
                                    load_table_pengguna();
								});
							</script>";
				
				array_push($this->data["js_header_script"],$js_header_script);
				
				// $this->data["daftar_pengguna"] = $this->m_pengguna->daftar_pengguna(); # heru mengganti ini 2020-12-03 @04:46
				
			}
			
			if($this->akses["tambah"] or $this->akses["ubah"]){
				$this->load->model("master_data/m_karyawan");
				$this->data["daftar_karyawan"] = $this->m_karyawan->daftar_karyawan();
			}
			
			$this->load->view('template',$this->data);
		}
		
		private function simpan_grup($username,$arr_grup_pengguna){
			$this->load->helper("pengguna_helper");
			return simpan_grup($username,$arr_grup_pengguna);
		}
		
		private function tambah($karyawan,$username,$status){
			$this->load->helper("pengguna_helper");
			return tambah_pengguna($karyawan,$username,$status);
		}
	
		private function ubah($karyawan,$username,$status,$karyawan_ubah,$username_ubah,$status_ubah){
			$return = array("status" => false, "error_info" => "");
			
			$cek = $this->m_pengguna->cek_ubah_pengguna($username,$username_ubah);
			if($cek["status"]){
				$set = array('no_pokok'=>$karyawan_ubah,'username'=>$username_ubah,'status'=>$status_ubah);
				$arr_data_lama = $this->m_pengguna->data_pengguna($username);
				
				$log_data_lama = "";
				$log_data_lama .= "nomor pokok = $karyawan<br>";
				$log_data_lama .= "username = $username<br>";
				$log_data_lama .= "status = $status";

				$this->m_pengguna->ubah($set,$karyawan,$username,$status);

				if($this->m_pengguna->cek_hasil_pengguna($karyawan_ubah,$username_ubah,$status_ubah)){
					$return["status"] = true;
					
					$log_data_baru = "";
					foreach($set as $key => $value){
						if(!empty($log_data_baru)){
							$log_data_baru .= "<br>";
						}
						$log_data_baru .= "$key = $value";
					}
					
					$log = array(
						"id_pengguna" => $this->session->userdata("id_pengguna"),
						"id_modul" => $this->data['id_modul'],
						"id_target" => $arr_data_lama["id"],
						"deskripsi" => "ubah ".strtolower(preg_replace("/_/"," ",__CLASS__)),
						"kondisi_lama" => $log_data_lama,
						"kondisi_baru" => $log_data_baru,
						"alamat_ip" => $this->data["ip_address"],
						"waktu" => date("Y-m-d H:i:s")
					);
					$this->m_log->tambah($log);
					
				}
				else{
					$return["status"] = false;
					$return["error_info"] = "Perubahan Pengguna <b>Gagal</b> Dilakukan.";
				}
			}
			else{
				$return["status"] = $cek["status"];
				$return["error_info"] = $cek["error_info"];	
			}

		return $return;
	}

	private function ubah_pengadministrasi($username, $is_pilih_unit_kerja, $admin_unit_kerja, $admin_unit_kerja_ubah)
	{
		if (strcmp($admin_unit_kerja, $admin_unit_kerja_ubah) != 0) {
			$data_pengguna = $this->m_pengguna->data_pengguna($username);
			$id_pengguna = $data_pengguna["id"];

			$this->load->model($this->folder_model . "m_pengadministrasi");

			$data = array(
				"id_pengguna" => $id_pengguna
			);

			$dataUser = $this->db->select('id,username,no_pokok')->where('username', $username)->get('usr_pengguna')->row_array();

			$arr_pengadminstrasi_lama = $this->m_pengadministrasi->data_pengadministrasi($username);
			$log_data_lama = "";
			foreach ($arr_pengadminstrasi_lama as $pengadministrasi_lama) {
				if (!empty($log_data_lama)) {
					$log_data_lama .= "<br>";
				}
				$log_data_lama .= $pengadministrasi_lama["kode_unit"] . " - " . $pengadministrasi_lama["nama_unit"];
			}

			$arr_data_lama = explode(",", $admin_unit_kerja);
			// $this->m_keycloak->unassignKodeUnit($dataUser['no_pokok'], $arr_data_lama);
			// $this->m_pengadministrasi->hapus($data);

			if (strcmp($is_pilih_unit_kerja, "ya") == 0) {
				$arr_data_baru = explode(",", $admin_unit_kerja_ubah);
				$arr_tambah = array();
				foreach ($arr_data_baru as $data_baru) {
					array_push($arr_tambah, array("id_pengguna" => $id_pengguna, "kode_unit" => $data_baru));
				}

				$this->m_pengadministrasi->tambah($arr_tambah);

				if ($this->m_pengadministrasi->cek_pengadministrasi($id_pengguna, $arr_data_baru)) {
					$log_data_baru = "";

					$arr_pengadminstrasi_baru = $this->m_pengadministrasi->data_pengadministrasi($username);

					foreach ($arr_pengadminstrasi_baru as $pengadministrasi_baru) {
						if (!empty($log_data_baru)) {
							$log_data_baru .= "<br>";
						}
						$log_data_baru .= $pengadministrasi_baru["kode_unit"] . " - " . $pengadministrasi_baru["nama_unit"];
					}


					// $this->m_keycloak->assignKodeUnit($dataUser['no_pokok'], $arr_data_baru);


					$log = array(
						"id_pengguna" => $this->session->userdata("id_pengguna"),
						"id_modul" => $this->data['id_modul'],
						"id_target" => $id_pengguna,
						"deskripsi" => "ubah pengadministrasi unit kerja",
						"kondisi_lama" => $log_data_lama,
						"kondisi_baru" => $log_data_baru,
						"alamat_ip" => $this->data["ip_address"],
						"waktu" => date("Y-m-d H:i:s")
					);
					$this->m_log->tambah($log);
				}
			}
		}
	}

	# heru menambahkan ini 2020-12-03 @04:46
	public function tabel_data_pengguna()
	{
		$this->load->model($this->folder_model . "m_pengguna_table");
		$this->data['judul'] = "Pengguna";
		$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		$this->akses = akses_helper($this->data['id_modul']);

		izin($this->akses["akses"]);
		//akses ke menu ubah				
		if (@$this->akses["ubah"]) //jika pengguna
		{
			$disabled_ubah = '';
		} else {
			$disabled_ubah = 'disabled';
		}

		$list = $this->m_pengguna_table->get_datatables();
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $tampil) {
			$btn = '';

			if ($this->akses["lihat grup"] || $this->akses["ubah grup"]) {
				$btn .= "<button class='btn btn-primary btn-xs' data-toggle='modal' data-target='#modal_grup' onclick='grup(this)'>Grup</button> ";
			}

			if ($this->akses["ubah"]) {
				$btn .= "<button class='btn btn-primary btn-xs' data-toggle='modal' data-target='#modal_ubah' onclick='tampil_data_ubah(this)'>Ubah</button> ";
			}

			if ($this->akses["lihat log"]) {
				$btn .= "<button class='btn btn-primary btn-xs' onclick='lihat_log(\"" . $tampil->username . "\"," . $tampil->id . ")'>Lihat Log</button> ";
			}

			if ($this->akses["switch"] && !in_array($tampil->username, array($this->session->userdata("username"), $this->session->userdata("browse_as_username_original")))) {
				$btn .= "<button class='btn btn-primary btn-xs' onclick='switch_to(\"" . $tampil->username . "\")'>Switch to</button>";
			}

			$row = array();
			$row[] = $tampil->username;
			$row[] = $tampil->no_pokok;
			$row[] = $tampil->nama;
			$row[] = $tampil->kode_unit;
			$row[] = $tampil->nama_unit;
			$row[] = (int)$tampil->status == 1 ? 'Aktif' : 'Non Aktif';
			$row[] = $btn;

			$data[] = $row;
		}

			$output = array(
							"draw" => $_POST['draw'],
							"recordsTotal" => $this->m_pengguna_table->count_all(),
							"recordsFiltered" => $this->m_pengguna_table->count_filtered(),
							"data" => $data,
					);
			//output to json format
			echo json_encode($output);
		}
        # END: heru menambahkan ini 2020-12-03 @04:46
	}
	
	/* End of file pengguna.php */
	/* Location: ./application/controllers/administrator/pengguna.php */
