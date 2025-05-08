<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Agenda extends CI_Controller
{

	function __construct()
	{
		parent::__construct();

		$meta = meta_data();

		foreach ($meta as $key => $value) {
			$this->data[$key] = $value;
		}

		$this->folder_view = 'ijt/agenda/';
		$this->folder_model = 'ijt/';
		$this->folder_ajax_view = $this->folder_view . 'ajax/';

		$this->load->helper("tanggal_helper");
		$this->load->library('form_validation');

		$this->load->model($this->folder_model . "/M_agenda");

		$this->data['success'] = "";
		$this->data['warning'] = "";

		$this->data["is_with_sidebar"] = true;

		$this->data['judul'] = "Agenda Job Tender";
		$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		$this->akses = akses_helper($this->data['id_modul']);

		izin($this->akses["akses"]);
	}

	public function index()
	{
		// echo '<pre>';
		// var_dump($this->session->userdata());
		// die;

		if (@$this->input->get('kegiatan')) {
			$this->data['kegiatan_name'] = urldecode($this->input->get('kegiatan'));
		}
		if (@$this->input->get('kegiatan_id')) {
			$this->data['kegiatan_id'] = $this->input->get('kegiatan_id');
		}

		// izin($this->akses["akses"]);

		$this->data["akses"] = $this->akses;
		$this->data["navigasi_menu"] = menu_helper();
		$this->data['content'] = $this->folder_view . "index";
		$this->data['np'] = $this->session->userdata("no_pokok");
		$this->data['is_tampilkan_agenda'] = true;

		if ($this->akses["lihat"]) {
			$js_header_script = "
							<script>
								var config = {
									route:'" . base_url() . "'
								}
							</script>
							<script src='" . base_url('asset/js/agenda') . "/datatable.serverside.js'></script>
							<script src='" . base_url('asset/datatables/js') . "/reload.js'></script>
							<script src='" . base_url('asset/select2') . "/select2.min.js'></script>
							<script src='" . base_url('asset/sweetalert2') . "/sweetalert2.js'></script>
							<script>
								$(document).ready(function() {
									$('.select2').select2();
								});
							</script>";

			array_push($this->data["js_header_script"], $js_header_script);

			$this->data["daftar_lokasi"] = $this->M_agenda->daftar_lokasi();
			$this->data["ref_job_tender"] = $this->db->where('deleted_at is null')->get('ijt_data')->result();

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

	public function data()
	{
		$list = $this->M_agenda->get_datatables();
		$data = array();
		$no = $_POST['start'];

		foreach ($list as $row) {
			$no++;

			$aksi = "<div style='display: flex; flex-direction:column; gap:4px; justify-content:center; align-items:center'>
			<button class='btn btn-sm btn-primary btn-detail' type='button' style='width: fit-content;'>Detail</button>";
			if ($this->akses && @$this->akses['konfirmasi'] && (@$row->status == '0' || empty($row->status))) $aksi .=  "<div class='dropdown'>
                <button class='btn btn-sm btn-secondary dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                  Konfirmasi Kehadiran
                </button>
                <div class='dropdown-menu' style='padding:8px'>
				    <button style='margin-bottom:4px;' class='dropdown-item btn mb-2 btn-success btn-hadir' type='button'>Hadir</button>
				    <button style='margin-bottom:4px;' class='dropdown-item btn mb-2 btn-danger  btn-tidak-hadir' type='button'>Tidak Hadir</button>
				    <button style='margin-bottom:4px;' class='dropdown-item btn mb-2 btn-primary  btn-reschedule' type='button'>Reschedule</button>
                </div>
			</div>";

			// if ($this->akses && @$this->akses['konfirmasi'] && @$row->status == '3') $aksi .= "<button style='width: fit-content;' class='btn btn-warning' type='button'>Reschedule</button>";
			// if ($this->akses && @$this->akses['konfirmasi'] && @$row->status == '2') $aksi .= "<button style='width: fit-content;' class='btn btn-danger' type='button'>Tidak Hadir</button>";
			// if ($this->akses && @$this->akses['konfirmasi'] && @$row->status == '1') $aksi .= "<button style='width: fit-content;' class='btn btn-success' type='button'>Hadir</button>";
			if ($this->akses && @$this->akses['ubah']) $aksi .= "<button style='width: fit-content;' class='btn btn-sm btn-warning btn-edit' data-id='$row->id' type='button'>Edit</button>";
			if ($this->akses && @$this->akses['hapus']) $aksi .= "<button style='width: fit-content;' class='btn btn-sm btn-danger btn-delete' type='button'>Hapus</button>";
			$aksi .= "</div>";

			$rows = array();
			$rows['id'] = $row->id;
			$rows['tanggal'] = $row->tanggal;
			$rows['nama_jabatan'] = $row->nama_jabatan;
			$rows['tempat'] = $row->tempat;
			$rows['no'] = $no;
			$rows['kegiatan'] = $row->kegiatan;
			$rows['job_id'] = $row->job_id;
			$rows['applyer'] = @$row->applyer;
			$rows['nama_jabatan'] = @$row->nama_jabatan;
			if ($this->session->userdata('grup') != '11') {
				$rows['status'] = ($row->status == '3') ? 'Reschedule' : (($row->status == '1') ? 'Hadir' : (($row->status == '2') ? 'Tidak Hadir' : 'Belum Konfirmasi'));
				$rows['color'] = ($row->status == '3') ? 'orange' : (($row->status == '1') ? 'green' : (($row->status == '2') ? 'red' : ''));
			}
			$rows['aksi'] = $aksi;
			$data[] = $rows;
		}
		$output = array(
			"draw" => $_POST['draw'],
			// "recordsTotal" => count($data),
			// "recordsFiltered" => count($data),
			"recordsTotal" => $this->M_agenda->count_all(),
			"recordsFiltered" => $this->M_agenda->count_filtered(),
			"data" => $data,
		);

		header('Content-Type: application/json');
		header("Access-Control-Allow-Origin: *");
		//output dalam format JSON
		echo json_encode($output);
	}

	public function detail($id)
	{
		$detail = $this->db->select('a.*,b.status,c.nama_jabatan, b.created_at as waktu_konfirm,b.alasan_reschedule')
			->from('ijt_event a')
			->join('ijt_data c', 'a.job_id = c.id and c.deleted_at is null')
			->join('ijt_konfirmasi_kehadiran b', 'a.id = b.id_agenda and b.deleted_at is null and b.np="' . $this->session->userdata('no_pokok') . '"', 'left')->where(['a.id' => $id])->where('a.deleted_at is null')->get()->row();

		$data = $this->db->select('a.nama,a.no_pokok,coalesce(e.status,0) as status,e.created_at as waktu_konfirm,e.alasan_reschedule')
			->from('mst_karyawan a')
			->join('ijt_apply c', 'a.no_pokok = c.np and c.deleted_at is null')
			->join('ijt_data b', 'b.id= c.job_id and b.deleted_at is null')
			->join('ijt_event d', 'd.job_id= b.id and d.deleted_at is null')
			->join('ijt_konfirmasi_kehadiran e', 'd.id = e.id_agenda and e.np=a.no_pokok and e.deleted_at is null', 'left')
			->join('ijt_verval f', 'f.apply_id =c.id and f.jenis_verval = "administrasi" and f.deleted_at is null')
			->where(['f.is_verval' => '1', 'd.id' => $id])
			->get()->result();

		// echo json_encode($data);

		return $this->load->view($this->folder_view . 'detail', ['applyer' => $data, 'detail' => $detail]);
	}

	public function edit($id)
	{
		$data = $this->db->from('ijt_event')
			->where(['id' => $id])->where('deleted_at is null')->get()->row();

		$ref_job_tender = $this->db->where('deleted_at is null')->get('ijt_data')->result();

		$pelamar = $this->db->select('b.nama,b.no_pokok')->join('mst_karyawan b', 'a.np=b.no_pokok')->join('ijt_verval c', 'c.apply_id =a.id and c.jenis_verval = "administrasi" and c.deleted_at is null')->where(['a.job_id' => $data->job_id, 'c.is_verval' => '1'])->where('a.deleted_at is null')->get('ijt_apply a')->result();

		return $this->load->view($this->folder_view . 'form', ['data' => $data, 'ref_job_tender' => $ref_job_tender, 'pelamar' => $pelamar]);
	}
	public function update_kehadiran($id)
	{
		$rules = [
			[
				'field' => 'status',
				'label' => 'Status',
				'rules' => 'required'
			],
			[
				'field' => 'alasan_reschedule',
				'label' => 'Alasan Reschedule',
				'rules' => $this->input->post('status') == '3' ? 'required' : 'nullable'
			],
		];
		$this->form_validation->set_rules($rules);

		if ($this->form_validation->run() == false) {
			echo json_encode([
				'status' => 'failed',
				'message' => 'Periksa kembali inputan anda',
				'errors' => $this->form_validation->error_array()
			]);
			exit;
		}

		$data = [
			'status' => $this->input->post('status'),
			'id_agenda' => $id,
			'np' => $this->session->userdata('no_pokok')
		];
		if ($this->input->post('status') == '3') $data['alasan_reschedule'] = $this->input->post('alasan_reschedule');

		$row = $this->db->where('id_agenda', $id)->where('np', $this->session->userdata('no_pokok'))->where('deleted_at is null')->get('ijt_konfirmasi_kehadiran')->row();

		$row !== null ? $data['updated_at'] = date('Y-m-d H:i:s') : $data['created_at'] = date('Y-m-d H:i:s');

		$row !== null ? $res = $this->db->where('id_agenda', $id)->where('np', $this->session->userdata('no_pokok'))->update('ijt_konfirmasi_kehadiran', $data) : $res = $this->db->insert('ijt_konfirmasi_kehadiran', $data);

		if ($res) {
			echo json_encode([
				'status' => 'success',
				'message' => 'berhasil update kehadiran'
			]);
			return;
		}
		echo json_encode([
			'status' => 'failed',
			'message' => 'gagal update kehadiran'
		]);
	}
	public function save()
	{
		$this->form_validation->set_rules('job_id', 'Job ID', 'required|numeric');
		$this->form_validation->set_rules('kegiatan', 'Kegiatan', 'required');
		$this->form_validation->set_rules('tanggal', 'Tanggal', 'required|callback_valid_tanggal');
		$this->form_validation->set_rules('tempat', 'Tempat', 'required');
		// $this->form_validation->set_rules('applyer[]', 'Applyer', 'required');

		if ($this->form_validation->run() == false) {
			echo json_encode([
				'status' => 'failed',
				'message' => 'Periksa kembali inputan anda',
				'errors' => $this->form_validation->error_array()
			]);
			exit;
		}

		$ubah = $this->input->post('id') !== '';
		$data = [
			'job_id' => $this->input->post('job_id'),
			'kegiatan' => $this->input->post('kegiatan'),
			'tanggal' => $this->input->post('tanggal'),
			'tempat' => $this->input->post('tempat'),
			// 'applyer' => implode(',', $this->input->post('applyer')),
			($ubah ? 'updated_at' : 'created_at') => date('Y-m-d H:i:s'),
		];

		$res = $ubah ? $this->db->where('id', $this->input->post('id'))->update('ijt_event', $data) : $this->db->insert('ijt_event', $data);
		if ($res) {
			if (!empty($this->input->post('applyer'))) {
				$applyer = [];
				foreach ($this->input->post('applyer') as $item) {
					$applyer[] = [
						'id' => uuid(),
						'event_id' => $ubah ? $this->input->post('id') : $this->db->insert_id(),
						'np' => $item,
						'created_at' => date('Y-m-d H:i:s')
					];
				}
				if ($ubah) {
					$this->db->where('event_id', $this->input->post('id'))->delete('ijt_event_undangan');
				}
				$this->db->insert_batch('ijt_event_undangan', $applyer);
			}
			echo json_encode([
				'status' => 'success',
				'message' => 'Berhasil ' . ($ubah ? 'ubah' : 'tambah') . ' agenda internal job tender'
			]);
			return;
		}
		echo json_encode([
			'status' => 'failed',
			'message' => 'Gagal ' . ($ubah ? 'ubah' : 'tambah') . ' agenda internal job tender'
		]);
	}
	public function find($id_job)
	{
		$data = $this->db->select('b.nama,b.no_pokok')->join('mst_karyawan b', 'a.np=b.no_pokok')->join('ijt_verval c', 'c.apply_id =a.id and c.jenis_verval = "administrasi" and c.deleted_at is null')->where(['a.job_id' => $id_job, 'c.is_verval' => '1'])->where('a.deleted_at is null')->get('ijt_apply a')->result();


		echo json_encode([
			'status' => 'success',
			'message' => 'Data applyer id job ' . $id_job,
			'data' => $data,
		]);
	}
	public function delete($id)
	{
		$data = [
			'deleted_at' => date('Y-m-d H:i:s'),
		];

		$res = $this->db->where('id', $id)->update('ijt_event', $data);
		if ($res) {
			echo json_encode([
				'status' => 'success',
				'message' => 'Berhasil hapus agenda internal job tender'
			]);
			return;
		}
		echo json_encode([
			'status' => 'failed',
			'message' => 'Gagal hapus agenda internal job tender'
		]);
	}
	public function valid_tanggal($tanggal)
	{
		$job_id = $this->input->post('job_id');
		$this->db->select('start_date');
		$this->db->where('id', $job_id);
		$this->db->where('deleted_at is null');
		$job = $this->db->get('ijt_data')->row();

		if ($job) {
			if ($tanggal < $job->start_date) {
				$this->form_validation->set_message('valid_tanggal', 'Tanggal agenda tidak boleh kurang dari ' . format_tanggal($job->start_date) . '.');
				return FALSE;
			}
		}

		return TRUE;
	}
}
