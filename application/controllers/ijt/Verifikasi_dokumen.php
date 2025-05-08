<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Verifikasi_dokumen extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();

		$meta = meta_data();
		foreach ($meta as $key => $value) {
			$this->data[$key] = $value;
		}

		$this->folder_view = 'ijt/';
		$this->folder_model = 'ijt/';
		$this->folder_controller = 'ijt/';

		$this->akses = array();

		$this->load->helper("tanggal_helper");

		$this->load->model($this->folder_model . "m_internal_job_tender");

		$this->data["is_with_sidebar"] = true;

		$this->data['judul'] = "Verifikasi Dokumen";
		$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		$this->akses = akses_helper($this->data['id_modul']);
		izin($this->akses["akses"]);
	}

	public function index()
	{
		$this->data["akses"] 					= $this->akses;
		$this->data["navigasi_menu"] 			= menu_helper();
		$this->data['content'] 					= $this->folder_view . "verifikasi_dokumen";
		$this->data['get_data'] 				= $this->m_internal_job_tender->get_data();

		$this->load->view('template', $this->data);
	}

	public function action_insert_data()
	{
		$submit = $this->input->post('submit');

		if ($submit) {
			$posisi = html_escape($this->input->post('posisi'));
			$deskripsi = html_escape($this->input->post('deskripsi'));
			$start_date = date('Y-m-d', strtotime($this->input->post('start_date')));
			$end_date = date('Y-m-d', strtotime($this->input->post('end_date')));

			$data_insert['posisi']				= $posisi;
			$data_insert['deskripsi']			= $deskripsi;
			$data_insert['start_date']			= $start_date;
			$data_insert['end_date']			= $end_date;

			$insert_data = $this->m_internal_job_tender->insert_data($data_insert);

			if ($insert_data != "0") {
				$this->session->set_flashdata('success', "Data berhasil ditambahkan.");
			} else {
				$this->session->set_flashdata('warning', "Data Gagal ditambahkan");
			}

			redirect(base_url($this->folder_controller . 'data'));
		} else {
			$this->session->set_flashdata('warning', "Terjadi Kesalahan");
			redirect(base_url($this->folder_controller . 'data'));
		}
	}
	public function action_update_data()
	{
		$submit = $this->input->post('submit');

		if ($submit) {
			try {
				$id = $this->input->post('id');

				if (empty($id)) {
					throw new Exception("ID tidak valid.");
				}

				$posisi = html_escape($this->input->post('posisi'));
				$deskripsi = html_escape($this->input->post('deskripsi'));
				$start_date = date('Y-m-d', strtotime($this->input->post('start_date')));
				$end_date = date('Y-m-d', strtotime($this->input->post('end_date')));

				$data_update = [
					'posisi' => $posisi,
					'deskripsi' => $deskripsi,
					'start_date' => $start_date,
					'end_date' => $end_date
				];

				$update_status = $this->m_internal_job_tender->update_data($id, $data_update);

				if (!$update_status) {
					throw new Exception("Gagal mengupdate data, silakan coba lagi.");
				}

				$this->session->set_flashdata('success', "Data berhasil diupdate.");
			} catch (Exception $e) {
				$this->session->set_flashdata('warning', $e->getMessage());
			}

			redirect(base_url($this->folder_controller . 'data'));
		} else {
			$this->session->set_flashdata('warning', "Terjadi Kesalahan.");
			redirect(base_url($this->folder_controller . 'data'));
		}
	}


	public function table_data()
	{
		$this->load->model($this->folder_model . "M_tabel_verval");

		$list 	= $this->M_tabel_verval->get_datatables();

		$data = array();
		$no = @$_POST['start'];

		foreach ($list as $tampil) {
			$no++;
			$file_data = json_decode($tampil->file, true);
			$row = array(
				'no' => $no,
				'id' => $tampil->id,
				'np' => $tampil->np,
				'nama_jabatan' => $tampil->nama_jabatan,
				'user_id' => $tampil->user_id,
				'nama' => $tampil->nama,
				'file_name' => $file_data['file_name'],
				'is_verval' => $tampil->is_verval,
				'keterangan' => $tampil->keterangan,
			);

			$actions = [];
			$actions = [];

			if ($this->akses['persetujuan'] && $tampil->is_verval == NULL) {
				$actions[] = "<button class='btn btn-primary btn-sm btn-verval' onclick=\"showVerif(" . $row['id'] . ")\">Verifikasi</button>";
			}


			$row['actions'] = $actions;

			$data[] = $row;
		}

		$output = array(
			"draw" => @$_POST['draw'],
			"recordsTotal" => $this->M_tabel_verval->count_all(),
			"recordsFiltered" => $this->M_tabel_verval->count_filtered(),
			"data" => $data,
		);
		echo json_encode($output);
	}

	public function get_files()
	{
		$apply_id = $this->input->post('apply_id');		

		if (!$apply_id) {
			echo json_encode(['status' => false, 'message' => 'Data tidak ditemukan']);
		}

		$this->load->model('m_internal_job_tender');
		$files = $this->m_internal_job_tender->get_dokumen_by_apply($apply_id);

		if (!empty($files)) {
			echo json_encode(['status' => true, 'files' => $files]);
		} else {
			echo json_encode(['status' => false, 'message' => 'Tidak ada file ditemukan']);
		}
	}

	function destroy($id)
	{
		$this->db->trans_begin();
		$data['deleted_at'] = date('Y-m-d H:i:s');

		$this->db->where('id', $id)->update('job_tender', $data);

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();

			$this->session->set_flashdata('warning', "Gagal menghapus data!");
			redirect(base_url($this->folder_controller . 'ijt/data'));
		} else {
			$this->db->trans_commit();

			$this->session->set_flashdata('success', "Berhasil menghapus data!");
			redirect(base_url($this->folder_controller . 'ijt/data'));
		}
	}

	function action_verval()
	{
		$this->load->library('form_validation');		

		$this->form_validation->set_rules('id', 'ID', 'required');
		$this->form_validation->set_rules('is_verval', 'Status Verifikasi', 'required');
		$this->form_validation->set_rules('keterangan', 'Keterangan', 'required');

		if ($this->form_validation->run() == FALSE) {
			echo json_encode([
				'status' => 'error',
				'message' => validation_errors()
			]);
			return;
		}

		$id = $this->input->post('id');
		$is_verval = $this->input->post('is_verval');
		$keterangan = $this->input->post('keterangan');
		$jenis_verval = 'administrasi';


		$this->db->trans_begin();

		try {
			$data_insert = [
				'apply_id' => $id,
				'is_verval' => $is_verval,
				'keterangan' => $keterangan,
				'jenis_verval' => $jenis_verval,
				'verif_by' => $this->session->userdata('no_pokok')
			];

			$update = $this->m_internal_job_tender->insert_verval_administrasi($data_insert);

			if ($update) {
				$this->db->trans_commit();
				echo json_encode(['status' => 'success', 'message' => 'Berhasil melakukan verifikasi']);
			} else {
				throw new Exception('Gagal');
			}
		} catch (Exception $e) {
			$this->db->trans_rollback();
			echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
		}
	}
}
