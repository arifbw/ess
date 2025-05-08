<?php defined('BASEPATH') or exit('No direct script access allowed');

class Mycontribution extends CI_Controller
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
		$this->folder_controller = 'poin_reward/';

		$this->akses = array();
		// $this->akses = array('persetujuan' => true, 'lihat' => true, 'hapus' => true);

		$this->load->helper("cutoff_helper");
		$this->load->helper("tanggal_helper");
		$this->load->helper("karyawan_helper");
		$this->load->helper("reference_helper");

		// $this->load->model($this->folder_model . "M_tabel_mycontribution");

		$this->data["is_with_sidebar"] = true;

		$this->data['judul'] = "Verifikasi My Contribution";
		$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		$this->akses = akses_helper($this->data['id_modul']);
		$this->nama_db = $this->db->database;
		izin($this->akses["akses"]);
	}

	public function index()
	{
		$this->load->model($this->folder_model . "M_mycontribution");

		$array_daftar_karyawan	= $this->M_mycontribution->select_daftar_karyawan();

		// echo json_encode($outsource); exit;
		$this->data["akses"] = $this->akses;
		$this->data["navigasi_menu"] = menu_helper();
		$this->data['content'] = $this->folder_view . "verifikasi/mycontribution";
		$this->data['array_daftar_karyawan'] = $array_daftar_karyawan;
		$this->data['ref_satuan_kerja'] = $this->db->distinct()->select('kode_unit,nama_unit')->where('kode_unit!=', null)->where('kode_unit!=', '')->get('my_contribution')->result_array();
		$this->data['ref_karyawan'] = $this->db->select('nama_karyawan as nama,np_karyawan as no_pokok')->distinct()->get('my_contribution')->result_array();

		$this->load->view('template', $this->data);
	}

	public function get_karyawan_by_satuan_kerja($kode_unit = 'all')
	{
		if ($kode_unit !== 'all') {
			$this->db->where('kode_unit', $kode_unit);
		}
		$res =  $this->db->select('nama_karyawan as nama,np_karyawan as no_pokok')->distinct()->get('my_contribution')->result_array();

		echo json_encode($res);
	}

	public function tabel_mycontribution()
	{
		$this->load->model($this->folder_model . "M_tabel_mycontribution");
		if ($_SESSION["grup"] == 4) { //jika Pengadministrasi Unit Kerja
			$var = array();
			$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
			foreach ($list_pengadministrasi as $data) { //looping list_pengadministrasi
				array_push($var, $data['kode_unit']);
			}
		} else if ($_SESSION["grup"] == 5) { //jika Pengguna
			$var 	= $_SESSION["no_pokok"];
		} else {
			$var = 1;
		}

		$list 	= $this->M_tabel_mycontribution->get_datatables(null, 'verifikasi');
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $tampil) {
			$no++;
			$get_status            = trim($tampil->status_verifikasi);

			$row = array();
			$row[] = $no;
			$row[] = $tampil->np_karyawan . ' - ' . $tampil->nama_karyawan;
			$row[] = $tampil->kode_unit . ' - ' . $tampil->nama_unit;
			$row[] = $tampil->perihal;
			$row[] = $tampil->jenis_dokumen;
			$row[] = $tampil->tanggal_dokumen;

			//DETAIL
			if ($get_status == '1') {
				$btn_warna        = 'btn-success';
				$btn_text        = 'Disetujui';
			} else if ($get_status == '2') {
				$btn_warna        = 'btn-danger';
				$btn_text        = 'Ditolak';
			} else if ($get_status == '0' || $get_status == null) {
				$btn_warna        = 'btn-default';
				$btn_text        = 'Proses';
			};


			$row[] = "<span class='btn btn-xs $btn_warna'" . ">$btn_text</span>";

			if ($tampil->status_verifikasi == '0') {
				if ($this->akses["persetujuan"])
					$aksi = "<button class='btn btn-warning btn-xs persetujuan_button' data-toggle='modal' data-target='#modal_persetujuan' data-id=" . $tampil->id . ">Verifikasi</button>";
				else
					$aksi = "<button class='btn btn-primary btn-xs detail_button' data-toggle='modal' data-target='#modal_detail' data-id=" . $tampil->id . ">Detail</button>";
			} else {
				$aksi = "<button class='btn btn-primary btn-xs detail_button' data-toggle='modal' data-target='#modal_detail' data-id=" . $tampil->id . ">Detail</button>";
			}
			// $aksi .= '<a target="_blank" href="' . base_url('poin_reward/verifikasi/mycontribution/export_pdf/') . $tampil->id . '" class="btn btn-success btn-xs"><i class="fa fa-print"></i> Cetak PDF</a>';

			$row[] = $aksi;

			$data[] = $row;
		}

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->M_tabel_mycontribution->count_all(null, 'verifikasi'),
			"recordsFiltered" => $this->M_tabel_mycontribution->count_filtered(null, 'verifikasi'),
			"data" => $data,
		);
		//output to json format
		echo json_encode($output);
	}

	public function view_detail()
	{
		$id = $this->input->post('id_');
		$tabel = 'my_contribution';

		$lap = $this->db->select("*")->where('id', $id)->get($tabel . ' a')->row_array();
		$data['detail'] = $lap;

		//DETAIL
		if ($lap['status_verifikasi'] == '1') {
			$data['status_verifikasi'] = "My Contrbution <b>TELAH DISETUJUI</b> pada <b>" . datetime_indo($lap['approval_at']) . "</b>";
			$data['approval_warna'] = 'success';
		} else if ($lap['status_verifikasi'] == '2') {
			$data['status_verifikasi'] = "My Contrbution <b>TIDAK DISETUJI</b> pada <b>" . datetime_indo($lap['approval_at']) . "</b>";
			$data['approval_warna'] = 'danger';
		} else if ($lap['status_verifikasi'] == '0' || $lap['status_verifikasi'] == null) {
			$data['status_verifikasi'] = "Proses";
			$data['approval_warna'] = 'info';
		}
		$data["judul"] = ucwords(preg_replace("/_/", " ", __CLASS__));

		$this->load->view($this->folder_view . "detail_mycontribution", $data);
	}

	public function view_approve()
	{
		$id = $this->input->post('id_');
		$tabel = 'my_contribution';

		$lap = $this->db->select("*")->where('id', $id)->get($tabel . ' a')->row_array();
		$data['detail'] = $lap;

		//DETAIL
		if ($lap['status_verifikasi'] == '1') {
			$data['status_verifikasi'] = "My Contrbution <b>TELAH DISETUJUI</b> pada <b>" . datetime_indo($lap['approval_at']) . "</b>";
			$data['approval_warna'] = 'success';
		} else if ($lap['status_verifikasi'] == '2') {
			$data['status_verifikasi'] = "My Contrbution <b>TIDAK DISETUJuI</b> pada <b>" . datetime_indo($lap['approval_at']) . "</b>";
			$data['approval_warna'] = 'danger';
		} else if ($lap['status_verifikasi'] == '0' || $lap['status_verifikasi'] == null) {
			$data['status_verifikasi'] = "Proses";
			$data['approval_warna'] = 'info';
		}
		$data["judul"] = ucwords(preg_replace("/_/", " ", __CLASS__));

		$this->load->view($this->folder_view . "verifikasi/approve_mycontribution", $data);
	}

	public function save_approve()
	{
		$this->load->model($this->folder_model . "M_mycontribution");

		$tabel = 'my_contribution';
		$this->load->helper('form');
		$this->load->library('form_validation');

		$simpan = array();
		$this->form_validation->set_message('required', 'The {field} field is required.');

		$this->form_validation->set_rules('id_', 'My Contribution', 'required');
		$this->form_validation->set_rules('status_verifikasi', 'Verifikasi', 'required');
		if ($this->input->post('status_verifikasi', true) == '1') {
			$this->form_validation->set_rules('poin', 'Poin', 'required');
		} else {
			$this->form_validation->set_rules('approval_alasan', 'Alasan', 'required');
		}

		if ($this->form_validation->run() == TRUE) {
			$id_ = $this->input->post('id_', true);

			$pdd = $this->db->where('id', $id_)->get($tabel);
			if ($pdd->num_rows() == 1) {
				$tanggal = date('Y-m-d H:i:s');

				$approval_alasan = $this->input->post('approval_alasan', true);
				$poin = $this->input->post('poin', true);
				$status = $this->input->post('status_verifikasi', true);

				$set = [
					'status_verifikasi' => $status,
					'approval_np' => $_SESSION['no_pokok'],
					'approval_nama' => $_SESSION['nama'],
					'approval_at' => $tanggal,
				];
				if ($status == '2') {
					$set['approval_alasan'] = $approval_alasan;
				} else {
					$set['poin'] = $poin;
					$data = $this->db->where('id', $id_)->get($tabel)->row_array();
					$this->M_mycontribution->add_poin($poin, $data);
				}

				$this->db->where('id', $id_)->set($set)->update($tabel);

				if ($this->db->affected_rows() > 0) {
					$this->session->set_flashdata('success', 'Berhasil Memberikan Verifikasi My Contribution');
				} else {
					$this->session->set_flashdata('warning', 'Gagal Memberikan Verifikasi My Contribution! Cek Koneksi Anda.');
				}
			} else {
				$this->session->set_flashdata('warning', 'Data My Contribution tidak valid!');
			}
		} else {
			$this->session->set_flashdata('warning', 'Data verifikasi My Contribution belum lengkap!');
		}

		redirect(site_url($this->folder_controller . 'verifikasi/mycontribution'));
	}

	function export_pdf($id)
	{
		$this->load->library('pdf');
		$mpdf = $this->pdf->load_custom();
		$lap = $this->db->select("*")->where('id', $id)->get('my_contribution')->row_array();
		$data = array(
			'data' => $lap,
			'title' => "Verifikasi My Contribution"
		);

		//DETAIL
		if ($lap['status_verifikasi'] == '1') {
			$data['status_verifikasi'] = "My Contrbution <b>TELAH DISETUJUI</b> pada <b>" . datetime_indo($lap['approval_at']) . "</b>";
			$data['approval_warna'] = 'success';
		} else if ($lap['status_verifikasi'] == '2') {
			$data['status_verifikasi'] = "My Contrbution <b>TIDAK DISETUJI</b> pada <b>" . datetime_indo($lap['approval_at']) . "</b>";
			$data['approval_warna'] = 'danger';
		} else if ($lap['status_verifikasi'] == '0' || $lap['status_verifikasi'] == null) {
			$data['status_verifikasi'] = "Proses";
			$data['approval_warna'] = 'info';
		}

		$html = $this->load->view($this->folder_view . "verifikasi/cetak_mycontribution", $data, true);
		$mpdf->WriteHTML($html);
		$mpdf->Output();
	}
}
