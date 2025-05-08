<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Persetujuan_cuti_sdm extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		$meta = meta_data();
		foreach ($meta as $key => $value) {
			$this->data[$key] = $value;
		}

		$this->folder_view = 'osdm/';
		$this->folder_model = 'osdm/';
		$this->folder_controller = 'osdm/';

		$this->akses = array();

		$this->load->helper("cutoff_helper");
		$this->load->helper("tanggal_helper");
		$this->load->helper("karyawan_helper");
		$this->load->helper("reference_helper");

		$this->load->model($this->folder_model . "m_persetujuan_cuti_sdm");
		$this->load->model("cuti/m_permohonan_cuti");

		$this->data["is_with_sidebar"] = true;

		$this->data['judul'] = "Persetujuan Cuti SDM";
		$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		$this->akses = akses_helper($this->data['id_modul']);
		$this->nama_db = $this->db->database;
		izin($this->akses["akses"]);
	}

	public function index()
	{
		//echo __FILE__ . __LINE__;die(var_dump($this->akses));			
		$this->data["akses"] 					= $this->akses;
		$this->data["navigasi_menu"] 			= menu_helper();
		$this->data['content'] 					= $this->folder_view . "persetujuan_cuti_sdm";

		//ambil tahun bulan tabel yang tersedia
		$array_tahun_bulan = array();
		$query = $this->db->query("SELECT table_name FROM information_schema.tables WHERE table_schema='$this->nama_db' AND table_name like '%ess_cico_%';");
		foreach ($query->result_array() as $data) {
			$bulan = substr($data['table_name'], -2);
			$tahun = substr($data['table_name'], 9, 4);

			$bulan_tahun = $bulan . "-" . $tahun;

			$array_tahun_bulan[] = $bulan_tahun;
		}

		$this->data['array_tahun_bulan'] 		= $array_tahun_bulan;

		$this->load->view('template', $this->data);
	}

	public function tabel_ess_cuti_sdm()
	{

		$filter_belum 			= $this->uri->segment(4);
		$filter_atasan_1 		= $this->uri->segment(5);
		$filter_atasan_2 		= $this->uri->segment(6);
		$filter_sdm 			= $this->uri->segment(7);
		$filter_belum_sdm 		= $this->uri->segment(8);
		$filter_batal 			= $this->uri->segment(9);
		$filter_tolak_atasan 	= $this->uri->segment(10);
		$filter_tolak_sdm 		= $this->uri->segment(11);
		// $filter_tanggal 		= $this->uri->segment(12);	

		$filter['filter_belum'] 		= $filter_belum;
		$filter['filter_atasan_1']		= $filter_atasan_1;
		$filter['filter_atasan_2'] 		= $filter_atasan_2;
		$filter['filter_sdm'] 			= $filter_sdm;
		$filter['filter_belum_sdm'] 	= $filter_belum_sdm;
		$filter['filter_batal'] 		= $filter_batal;
		$filter['filter_tolak_atasan'] 	= $filter_tolak_atasan;
		$filter['filter_tolak_sdm'] 	= $filter_tolak_sdm;
		// $filter['filter_tanggal'] 		= $filter_tanggal;			

		$this->load->model($this->folder_model . "M_tabel_persetujuan_cuti_sdm");
		// if($this->input->post('daterange') != NULL){
		// 	$daterange = $this->input->post('daterange');
		// 	$list 	= $this->M_tabel_persetujuan_cuti_sdm->get_datatables($filter, $daterange);
		// }else{

		// }
		$list 	= $this->M_tabel_persetujuan_cuti_sdm->get_datatables($filter);

		$data = array();
		$no = $_POST['start'];

		foreach ($list as $tampil) {
			$no++;
			$row = array();
			$row[] = $no;
			$row[] = $tampil->np_karyawan;
			$row[] = $tampil->nama;
			$row[] = $tampil->uraian;
			$row[] = tanggal_indonesia($tampil->start_date);
			$row[] = tanggal_indonesia($tampil->end_date);

			if ($tampil->jumlah_bulan) {
				$row[] = $tampil->jumlah_bulan . " bulan" . $tampil->jumlah_hari . " hari";
			} else {
				$row[] = $tampil->jumlah_hari . " hari";
			}

			$row[] = $tampil->alasan;

			$id				= $tampil->id;
			$np_karyawan	= $tampil->np_karyawan;
			$nama			= $tampil->nama;
			$approval_1		= $tampil->approval_1;
			$approval_2		= $tampil->approval_2;
			$status_1		= $tampil->status_1;
			$status_2		= $tampil->status_2;
			$approval_1_date = $tampil->approval_1_date;
			$approval_2_date = $tampil->approval_2_date;
			$created_at		= $tampil->created_at;
			$jumlah_cuti    = $tampil->jumlah_hari;
			$created_by		= nama_karyawan_by_np($tampil->created_by);

			$approval_sdm			= $tampil->approval_sdm;
			$np_sdm 				= $_SESSION["no_pokok"];
			$approval_nama_sdm		=  $np_sdm . " | " . nama_karyawan_by_np($np_sdm);

			if ($status_1 == '1') {
				$approval_1_nama 	= $approval_1 . " | " . nama_karyawan_by_np($approval_1);
				$approval_1_status 	= "Cuti Telah Disetujui pada $approval_1_date.";
			} else
				if ($status_1 == '2') {
				$approval_1_nama 	= $approval_1 . " | " . nama_karyawan_by_np($approval_1);
				$approval_1_status 	= "Cuti TIDAK disetujui pada $approval_1_date.";
			} else
				if ($status_1 == '3') {
				$approval_1_nama 	= $approval_1 . " | " . nama_karyawan_by_np($approval_1);
				$approval_1_status 	= "Permohonan Cuti Dibatalkan oleh pemohon pada $approval_1_date.";
			} else
				if ($status_1 == '' || $status_1 == '0') {
				$status_1 = '0';
				$approval_1_nama 	= $approval_1 . " | " . nama_karyawan_by_np($approval_1);
				$approval_1_status 	= "Cuti BELUM disetujui.";
			}

			if ($status_2 == '1') {
				$approval_2_nama 	= $approval_2 . " | " . nama_karyawan_by_np($approval_2);
				$approval_2_status 	= "Cuti Telah Disetujui pada $approval_2_date.";
			} else
				if ($status_2 == '2') {
				$approval_2_nama 	= $approval_2 . " | " . nama_karyawan_by_np($approval_2);
				$approval_2_status 	= "Cuti TIDAK disetujui pada $approval_2_date.";
			} else
				if ($status_2 == '3') {
				$approval_2_nama 	= $approval_2 . " | " . nama_karyawan_by_np($approval_2);
				$approval_2_status 	= "Permohonan Cuti Dibatalkan oleh pemohon pada $approval_2_date.";
			} else
				if ($status_2 == '' || $status_2 == '0') {
				$status_2 = '0';
				$approval_2_nama 	= $approval_2 . " | " . nama_karyawan_by_np($approval_2);
				$approval_2_status 	= "Cuti BELUM disetujui.";
			}

			if (($status_1 == '0' && $status_2 == '0') || ($status_1 == '' && $status_2 == '')) {
				$btn_warna	= 'btn-default';
				$btn_text	= 'menunggu persetujuan';
			}
			if ($status_1 == '1' || $status_2 == '1') {
				if ($tampil->approval_2 == null || $tampil->approval_2 == '') //jika tidak ada atasan 2
				{
					$btn_warna = 'btn-success';
					$btn_text	= 'Disetujui Atasan 1';
				} else //jika ada atasan 2
				{
					$btn_warna = 'btn-warning';
					$btn_text	= 'proses persetujuan';
				}
			}
			if ($status_1 == '1' && $status_2 == '1') {
				$btn_warna = 'btn-success';
				$btn_text	= 'disetujui';
			}
			if ($status_1 == '2' || $status_2 == '2') {
				$btn_warna	= 'btn-danger';
				$btn_text	= 'tidak disetujui';
			}

			if ($status_1 == '3' || $status_2 == '3') //dibatalkan
			{
				$btn_warna		= 'btn-danger';
				$btn_text		= 'dibatalkan';
				$btn_disabled 	= 'disabled';
			} else
				if ($status_1 == '2' || $status_2 == '2') //tidak disetujui
			{
				$btn_disabled 	= 'disabled';
			} else
				if ($status_1 == '1' && $status_2 == '1') //telah disetujui
			{
				$btn_disabled 	= 'disabled';
			} else {
				$btn_disabled	= '';
			}





			$row[] = "<button class='btn " . $btn_warna . " btn-xs status_button' data-toggle='modal' data-target='#modal_status'
					data-np-karyawan='$np_karyawan'
					data-nama='$nama'
					data-created-at='$created_at'
					data-created-by='$created_by'
					data-approval-1-nama='$approval_1_nama'
					data-approval-2-nama='$approval_2_nama'
					data-approval-1-status='$approval_1_status'
					data-approval-2-status='$approval_2_status'					
				>$btn_text</button>";


			if ($approval_sdm == '0' || $approval_sdm == '') {
				$btn_disabled 	= '';
				$row[] = "<button class='btn btn-default btn-xs'><i class='fa fa-minus'></i></button>";
			} else
				if ($approval_sdm == '1') {
				$btn_disabled 	= 'disabled';
				$row[] = "<button class='btn btn-success btn-xs'><i class='fa fa-check'></i></button>";
			} else
				if ($approval_sdm == '2') {
				$btn_disabled 	= 'disabled';
				$row[] = "<button class='btn btn-danger btn-xs'><i class='fa fa-times'></i></button>";
				$this->db->where('id', $id);
				$this->db->where_in('absence_type', '2001|2080'); // Pastikan format 'absence_type' sesuai database
				$this->db->where_in('batal_hutang_cuti', '0'); // Pastikan format 'absence_type' sesuai database
				$this->db->where_in('approval_sdm', [0,1]);
				$query = $this->db->get('ess_cuti');

				if ($query->num_rows() > 0) {
					$this->db->set('batal_hutang_cuti', 1, FALSE); // Mengurangi nilai 'hutang'
					$this->db->where('id', $id);
					$this->db->update('ess_cuti');

					// Jika absence_type adalah 2001|2080, kurangi hutang
					$this->db->set('hutang', 'hutang - 1', FALSE); // Mengurangi nilai 'hutang'
					$this->db->where('no_pokok', $np_karyawan);
					$this->db->update('cuti_hutang');
				}
			}

			//jika superadmin bisa tolak dan approve walaupun sudah di approve			
			if ($_SESSION["grup"] == 1) //jika superadmin
			{
				$btn_disabled 	= '';
			}

			//jika administrator SDM bisa tolak dan approve walaupun sudah di approve			
			if ($_SESSION["grup"] == 3) //jika superadmin
			{
				$btn_disabled 	= '';
			}






			//cutoff ERP
			$sudah_cutoff = sudah_cutoff($tampil->start_date);

			if ($sudah_cutoff) //jika sudah lewat masa cutoff
			{
				$row[] = "<button class='btn btn-primary btn-xs persetujuan_button'   data-toggle='tooltip' title='Tidak bisa melakukan aksi setelah Cutoff data di ERP pada $sudah_cutoff' disabled>Submit ERP</button>";
			} else {
				$row[] = "<button class='btn btn-primary btn-xs persetujuan_button' data-toggle='modal'  data-target='#modal_persetujuan'
					data-id='$id'
					data-np-karyawan='$np_karyawan'
					data-nama='$nama'
					data-created-at='$created_at'
					data-created-by='$created_by'				
					data-approval-nama-sdm='$approval_nama_sdm'						
					data-approval-sdm='$approval_sdm'
					data-jumlah-cuti='$jumlah_cuti'		
					$btn_disabled>Persetujuan SDM</button>";
			}





			$data[] = $row;
		}

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->M_tabel_persetujuan_cuti_sdm->count_all($filter),
			"recordsFiltered" => $this->M_tabel_persetujuan_cuti_sdm->count_filtered($filter),
			"data" => $data,
		);
		//output to json format
		echo json_encode($output);
	}

	private function persetujuan($id, $approval_sdm, $approval_sdm_by, $alasan_sdm = null)
	{
		//===== Log Start =====
		$arr_data_lama = $this->m_persetujuan_cuti_sdm->select_cuti_by_id($id);
		$log_data_lama = "";
		foreach ($arr_data_lama as $key => $value) {
			if (strcmp($key, "id") != 0) {
				if (!empty($log_data_lama)) {
					$log_data_lama .= "<br>";
				}
				$log_data_lama .= "$key = $value";
			}
		}
		//===== Log end =====

		//insert
		$data_persetujuan['id']				= $id;
		$data_persetujuan['approval_sdm']	= $approval_sdm;
		$data_persetujuan['approval_sdm_by'] = $approval_sdm_by;
		if (trim($alasan_sdm) != '') {
			$data_persetujuan['alasan_sdm'] = $alasan_sdm;
		} else {
			$data_persetujuan['alasan_sdm'] = '';
		}

		$persetujuan_cuti_sdm = $this->m_persetujuan_cuti_sdm->persetujuan_cuti_sdm($data_persetujuan);

		if ($persetujuan_cuti_sdm != "0") {
			$this->session->set_flashdata('success', "Aksi Persetujuan/tolak Cuti oleh SDM, berhasil.");

			//===== Log Start =====
			$arr_data_baru = $this->m_persetujuan_cuti_sdm->select_cuti_by_id($id);
			$log_data_baru = "";
			foreach ($arr_data_baru as $key => $value) {
				if (strcmp($key, "id") != 0) {
					if (!empty($log_data_baru)) {
						$log_data_baru .= "<br>";
					}
					$log_data_baru .= "$key = $value";
				}
			}
			$log = array(
				"id_pengguna" => $this->session->userdata("id_pengguna"),
				"id_modul" => $this->data['id_modul'],
				"deskripsi" => "Setuju " . strtolower(preg_replace("/_/", " ", __CLASS__)),
				"kondisi_lama" => $log_data_lama,
				"kondisi_baru" => $log_data_baru,
				"alamat_ip" => $this->data["ip_address"],
				"waktu" => date("Y-m-d H:i:s")
			);
			$this->m_log->tambah($log);
			//===== Log end =====

			//JIKA CUTI BESAR
			$data_cuti = $this->m_persetujuan_cuti_sdm->select_cuti_by_id($id);

			if ($data_cuti['absence_type'] == '2001|1010' &&  $approval_sdm == '1') {
				$start_date		= $data_cuti['start_date'];
				$end_date		= $data_cuti['end_date'];
				$jumlah_bulan 	= $data_cuti['jumlah_bulan'];
				$jumlah_hari	= $data_cuti['jumlah_hari'];
				$np_karyawan	= $data_cuti['np_karyawan'];

				$jatah_cubes = $this->m_persetujuan_cuti_sdm->select_jatah_cubes($np_karyawan, $start_date);
				$pakai_bulan = $jatah_cubes['pakai_bulan'];
				$pakai_hari	 = $jatah_cubes['pakai_hari'];
				$sisa_bulan	 = $jatah_cubes['sisa_bulan'];
				$sisa_hari	 = $jatah_cubes['sisa_hari'];

				$hasil_pakai_bulan	= $pakai_bulan + $jumlah_bulan;
				$hasil_pakai_hari	= $pakai_hari + $jumlah_hari;
				$hasil_sisa_bulan	= $sisa_bulan - $jumlah_bulan;
				$hasil_sisa_hari	= $sisa_hari - $jumlah_hari;

				//menghitung tanggal bantuan cuti besar
				if (($jatah_cubes['bantuan_cuti_besar_tanggal'] == null || $jatah_cubes['bantuan_cuti_besar_tanggal'] == '') && $jumlah_bulan >= '1') {
					//tgl diapprove
					$date_approve = date('Y-m-d');
					$pisah 	= explode('-', $date_approve);
					$tahun 	= $pisah[0];
					$bulan 	= $pisah[1];
					$hari 	= $pisah[2];



					if ($hari <= '10') //jika di approve <= tgl 10 
					{
						$bantuan_cuti_besar_tanggal	= $tahun . "-" . $bulan . "-25";
					} else //jika di approve > tgl 10
					{
						$bulan_depan = date('Y-m-d', strtotime("+1 months", strtotime($start_date)));

						$pisah 	= explode('-', $bulan_depan);
						$tahun 	= $pisah[0];
						$bulan 	= $pisah[1];
						$hari 	= $pisah[2];

						$bantuan_cuti_besar_tanggal	= $tahun . "-" . $bulan . "-25";
					}



					$bantuan_cuti_besar_id_cuti	= $id;

					$data_update = array(
						'id'						=> $jatah_cubes['id'],
						'bantuan_cuti_besar_tanggal' => $bantuan_cuti_besar_tanggal,
						'bantuan_cuti_besar_id_cuti' => $bantuan_cuti_besar_id_cuti
					);

					$this->m_persetujuan_cuti_sdm->update_jatah_cubes($data_update);
				}


				$data_update = array(
					'id'						=> $jatah_cubes['id'],
					'pakai_bulan'				=> $hasil_pakai_bulan,
					'pakai_hari'				=> $hasil_pakai_hari,
					'sisa_bulan'				=> $hasil_sisa_bulan,
					'sisa_hari'					=> $hasil_sisa_hari
				);

				$this->m_persetujuan_cuti_sdm->update_jatah_cubes($data_update);
			}
		} else {
			$this->session->set_flashdata('warning', "Aksi Persetujuan/tolak Gagal");
		}
	}

	public function action_persetujuan_cuti_sdm()
	{

		$submit = $this->input->post('submit');

		$persetujuan_filter_belum			= $this->input->post('persetujuan_filter_belum');
		$persetujuan_filter_atasan_1		= $this->input->post('persetujuan_filter_atasan_1');
		$persetujuan_filter_atasan_2		= $this->input->post('persetujuan_filter_atasan_2');
		$persetujuan_filter_sdm				= $this->input->post('persetujuan_filter_sdm');
		$persetujuan_filter_belum_sdm		= $this->input->post('persetujuan_filter_belum_sdm');
		$persetujuan_filter_batal			= $this->input->post('persetujuan_filter_batal');
		$persetujuan_filter_tolak_atasan	= $this->input->post('persetujuan_filter_tolak_atasan');
		$persetujuan_filter_tolak_sdm		= $this->input->post('persetujuan_filter_tolak_sdm');

		$this->session->set_flashdata('persetujuan_filter_belum', $persetujuan_filter_belum);
		$this->session->set_flashdata('persetujuan_filter_atasan_1', $persetujuan_filter_atasan_1);
		$this->session->set_flashdata('persetujuan_filter_atasan_2', $persetujuan_filter_atasan_2);
		$this->session->set_flashdata('persetujuan_filter_sdm', $persetujuan_filter_sdm);
		$this->session->set_flashdata('persetujuan_filter_belum_sdm', $persetujuan_filter_belum_sdm);
		$this->session->set_flashdata('persetujuan_filter_batal', $persetujuan_filter_batal);
		$this->session->set_flashdata('persetujuan_filter_tolak_atasan', $persetujuan_filter_tolak_atasan);
		$this->session->set_flashdata('persetujuan_filter_tolak_sdm', $persetujuan_filter_tolak_sdm);

		if ($submit) {
			//echo json_encode($this->input->post()); exit();
			$id				= $this->input->post('persetujuan_id_sdm');
			$approval_sdm	= $this->input->post('persetujuan_approval_sdm');
			$alasan_sdm	= $this->input->post('persetujuan_alasan_sdm', true);
			$approval_sdm_by = $_SESSION["no_pokok"];

			//===== Log Start =====
			$arr_data_lama = $this->m_permohonan_cuti->select_cuti_by_id($id);
			$log_data_lama = "";
			foreach ($arr_data_lama as $key => $value) {
				if (strcmp($key, "id") != 0) {
					if (!empty($log_data_lama)) {
						$log_data_lama .= "<br>";
					}
					$log_data_lama .= "$key = $value";
				}
			}
			//===== Log end =====

			$this->persetujuan($id, $approval_sdm, $approval_sdm_by, $alasan_sdm);

			//===== Log Start =====
			$arr_data_baru = $this->m_permohonan_cuti->select_cuti_by_id($id);
			$log_data_baru = "";
			foreach ($arr_data_baru as $key => $value) {
				if (strcmp($key, "id") != 0) {
					if (!empty($log_data_baru)) {
						$log_data_baru .= "<br>";
					}
					$log_data_baru .= "$key = $value";
				}
			}
			$log = array(
				"id_pengguna" => $this->session->userdata("id_pengguna"),
				"id_modul" => $this->data['id_modul'],
				"deskripsi" => "batal " . strtolower(preg_replace("/_/", " ", __CLASS__)),
				"kondisi_lama" => $log_data_lama,
				"kondisi_baru" => $log_data_baru,
				"alamat_ip" => $this->data["ip_address"],
				"waktu" => date("Y-m-d H:i:s")
			);
			$this->m_log->tambah($log);
			//===== Log end =====

			// Update Mozes Kurangi data Cuti
			if ($this->input->post('persetujuan_approval_sdm') == 2 && ($arr_data_lama['absence_type']=='2001|1010' && $arr_data_lama['approval_sdm']=='1') ) {

				$data_cuti = $this->m_persetujuan_cuti_sdm->select_cuti_by_id($id);

				$start_date		= $data_cuti['start_date'];
				$end_date		= $data_cuti['end_date'];
				$jumlah_bulan 	= $data_cuti['jumlah_bulan'];
				$jumlah_hari	= $data_cuti['jumlah_hari'];
				$np_karyawan	= $data_cuti['np_karyawan'];

				$jatah_cubes = $this->m_persetujuan_cuti_sdm->select_jatah_cubes($np_karyawan, $start_date);
				$pakai_bulan = $jatah_cubes['pakai_bulan'];
				$pakai_hari	 = $jatah_cubes['pakai_hari'];
				$sisa_bulan	 = $jatah_cubes['sisa_bulan'];
				$sisa_hari	 = $jatah_cubes['sisa_hari'];

				$hasil_pakai_bulan	= $pakai_bulan - $jumlah_bulan;
				$hasil_pakai_hari	= $pakai_hari - $jumlah_hari;
				$hasil_sisa_bulan	= $sisa_bulan + $jumlah_bulan;
				$hasil_sisa_hari	= $sisa_hari + $jumlah_hari;

				$data_update = array(
					'id'						=> $jatah_cubes['id'],
					'pakai_bulan'				=> $hasil_pakai_bulan,
					'pakai_hari'				=> $hasil_pakai_hari,
					'sisa_bulan'				=> $hasil_sisa_bulan,
					'sisa_hari'					=> $hasil_sisa_hari
				);

				$this->m_persetujuan_cuti_sdm->update_jatah_cubes($data_update);
			}


			redirect(base_url($this->folder_controller . 'persetujuan_cuti_sdm'));
		} else {
			$this->session->set_flashdata('warning', "Terjadi Kesalahan");
			redirect(base_url($this->folder_controller . 'persetujuan_cuti_sdm'));
		}
	}

	public function action_persetujuan_cuti_sdm_all()
	{

		$submit 		= $this->input->post('submit');
		$bulan_tahun	= $this->input->post('bulan_tahun'); //mm-yyyy

		$persetujuan_filter_belum			= $this->input->post('persetujuan_filter_belum');
		$persetujuan_filter_atasan_1		= $this->input->post('persetujuan_filter_atasan_1');
		$persetujuan_filter_atasan_2		= $this->input->post('persetujuan_filter_atasan_2');
		$persetujuan_filter_sdm				= $this->input->post('persetujuan_filter_sdm');
		$persetujuan_filter_belum_sdm		= $this->input->post('persetujuan_filter_belum_sdm');
		$persetujuan_filter_batal			= $this->input->post('persetujuan_filter_batal');
		$persetujuan_filter_tolak_atasan	= $this->input->post('persetujuan_filter_tolak_atasan');
		$persetujuan_filter_tolak_sdm		= $this->input->post('persetujuan_filter_tolak_sdm');

		$this->session->set_flashdata('persetujuan_filter_belum', $persetujuan_filter_belum);
		$this->session->set_flashdata('persetujuan_filter_atasan_1', $persetujuan_filter_atasan_1);
		$this->session->set_flashdata('persetujuan_filter_atasan_2', $persetujuan_filter_atasan_2);
		$this->session->set_flashdata('persetujuan_filter_sdm', $persetujuan_filter_sdm);
		$this->session->set_flashdata('persetujuan_filter_belum_sdm', $persetujuan_filter_belum_sdm);
		$this->session->set_flashdata('persetujuan_filter_batal', $persetujuan_filter_batal);
		$this->session->set_flashdata('persetujuan_filter_tolak_atasan', $persetujuan_filter_tolak_atasan);
		$this->session->set_flashdata('persetujuan_filter_tolak_sdm', $persetujuan_filter_tolak_sdm);

		if ($submit) {
			$pisah 			= explode('-', $bulan_tahun);
			$bulan			= $pisah[0];
			$tahun			= $pisah[1];
			$tahun_bulan	= $tahun . "-" . $bulan;
			$query = $this->m_persetujuan_cuti_sdm->select_cuti_siap_approve_all($tahun_bulan);

			//$this->output->enable_profiler(TRUE);
			//var_dump($query);
			//die();
			foreach ($query->result_array() as $data) {
				$id				= $data['id'];
				$approval_sdm	= '1';
				$approval_sdm_by = $_SESSION["no_pokok"];

				//===== Log Start =====
				$arr_data_lama = $this->m_permohonan_cuti->select_cuti_by_id($id);
				$log_data_lama = "";
				foreach ($arr_data_lama as $key => $value) {
					if (strcmp($key, "id") != 0) {
						if (!empty($log_data_lama)) {
							$log_data_lama .= "<br>";
						}
						$log_data_lama .= "$key = $value";
					}
				}
				//===== Log end =====

				$this->persetujuan($id, $approval_sdm, $approval_sdm_by);

				//===== Log Start =====
				$arr_data_baru = $this->m_permohonan_cuti->select_cuti_by_id($id);
				$log_data_baru = "";
				foreach ($arr_data_baru as $key => $value) {
					if (strcmp($key, "id") != 0) {
						if (!empty($log_data_baru)) {
							$log_data_baru .= "<br>";
						}
						$log_data_baru .= "$key = $value";
					}
				}
				$log = array(
					"id_pengguna" => $this->session->userdata("id_pengguna"),
					"id_modul" => $this->data['id_modul'],
					"deskripsi" => "batal " . strtolower(preg_replace("/_/", " ", __CLASS__)),
					"kondisi_lama" => $log_data_lama,
					"kondisi_baru" => $log_data_baru,
					"alamat_ip" => $this->data["ip_address"],
					"waktu" => date("Y-m-d H:i:s")
				);
				$this->m_log->tambah($log);
				//===== Log end =====
			}

			redirect(base_url($this->folder_controller . 'persetujuan_cuti_sdm'));
		} else {
			$this->session->set_flashdata('warning', "Terjadi Kesalahan");
			redirect(base_url($this->folder_controller . 'persetujuan_cuti_sdm'));
		}
	}
}
	
	/* End of file persetujuan_cuti_sdm.php */
	/* Location: ./application/controllers/cuti/persetujuan_cuti_sdm.php */
