<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pengajuan_lembur extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		$meta = meta_data();
		foreach ($meta as $key => $value) {
			$this->data[$key] = $value;
		}

		$this->folder_view = 'lembur/ajax/';
		$this->folder_model = 'lembur/';
		$this->akses = array();

		$this->load->model($this->folder_model . "/m_pengajuan_lembur");

		$this->data['success'] = "";
		$this->data['warning'] = "";

		$this->data["is_with_sidebar"] = true;
	}

	public function salin($id)
	{
		$data = $this->m_pengajuan_lembur->ambil_pengajuan_lembur_id($id);
		$data["function"] = __FUNCTION__;
		$this->load->view($this->folder_view . "pengajuan_lembur", $data);
	}

	public function ubah($id)
	{
		$data = $this->m_pengajuan_lembur->ambil_pengajuan_lembur_pegawai_id($id);
		$data["function"] = __FUNCTION__;
		$data["no_pokok"] = $data["no_pokok"];
		$data["nama_pegawai"] = $data["nama_pegawai"];
		$data["np_approver"] = $data["approval_pimpinan_np"];
		$data["nama_approver"] = $data["approval_pimpinan_nama"];
		$data["alasan"] = $data["alasan"];
		$data["tgl_dws"] = $data["tgl_dws"];
		$data["tgl_mulai"] = $data["tgl_mulai"];
		$data["tgl_selesai"] = $data["tgl_selesai"];
		$data["jam_mulai"] = date('H:i', strtotime($data["jam_mulai"]));
		$data["jam_selesai"] = date('H:i', strtotime($data["jam_selesai"]));

		$dteStart = date_create(date('Y-m-d H:i', strtotime($data['tgl_mulai'] . ' ' . $data["jam_mulai"])));
		$dteEnd = date_create(date('Y-m-d H:i', strtotime($data['tgl_selesai'] . ' ' . $data["jam_selesai"])));

		$diff = date_diff($dteStart, $dteEnd);
		$hari = $diff->format("%a");
		$jam = $diff->format("%h");

		$total = ($hari * 24) + ($jam);
		$data['jam_diakui'] = $total;

		$data['daftar_approval'] = $this->ajax_getPilihanAtasanLembur($data);
		$data['kategori_lembur'] = $this->get_kategori_lembur($data);

		$data["judul"] = ucwords(preg_replace("/_/", " ", __CLASS__));
		$this->load->view($this->folder_view . "pengajuan_lembur", $data);
	}

	private function get_kategori_lembur($data_lembur)
	{
		$data = $this->db->query("select * from mst_kategori_lembur where status='1'")->result_array();
		$option = "<option value=''>Pilih Jenis Lembur</option>";
		for ($i = 0; $i < count($data); $i++) {
			$option .= "<option value='" . $data[$i]['kategori_lembur'] . "' " . ($data_lembur["alasan"] == $data[$i]['kategori_lembur'] ? 'selected' : '') . ">" . $data[$i]['kategori_lembur'] . "</option>";
		}

		return $option;
	}

	private function ajax_getPilihanAtasanLembur($data_lembur)
	{
		$periode = null;
		$no_pokok	 = $data_lembur['no_pokok'];
		$tgl_mulai	 = $data_lembur['tgl_mulai'];
		$jam_mulai	 = $data_lembur['jam_mulai'];
		$tgl_selesai = $data_lembur['tgl_selesai'];
		$jam_selesai = $data_lembur['jam_selesai'];
		$dteStart = date_create(date('Y-m-d H:i', strtotime($tgl_mulai . ' ' . $jam_mulai)));
		$dteEnd = date_create(date('Y-m-d H:i', strtotime($tgl_selesai . ' ' . $jam_selesai)));
		$diff = date_diff($dteStart, $dteEnd);
		$hari = $diff->format("%a");
		$jam = $diff->format("%h");
		$total = ($hari * 24) + ($jam);
		$np_karyawan = $data_lembur['no_pokok'];

		$periode     = $data_lembur['tgl_dws'];
		$pisah_periode = explode('-', $periode);
		$d = $pisah_periode[2];
		$m = $pisah_periode[1];
		$y = $pisah_periode[0];
		$periode 			= $y . '_' . $m;
		$periode_tanggal 	= $y . '-' . $m . '-' . $d;
		if (!$periode_tanggal) {
			$periode_tanggal = date('Y-m-d');
		}

		$this->load->model("master_data/m_karyawan");
		$karyawan = $this->m_karyawan->get_posisi_karyawan_periode_tanggal($np_karyawan, $periode, $periode_tanggal);

		if (empty($karyawan)) {
			if ($periode == '') {
				$periode = date("Y_m");
			}
			$karyawan = $this->m_karyawan->get_posisi_karyawan_periode($np_karyawan, $periode);

			if (empty($karyawan)) {
				if ($periode == '') {
					$periode = date('Y_m', strtotime(date("Y-m-d") . " -1 months"));
				}
				$karyawan = $this->m_karyawan->get_posisi_karyawan_periode($np_karyawan, $periode);
			}
		}
		$kode = $karyawan["kode_unit"];

		if (strcmp(substr($karyawan["kode_unit"], 1, 1), "0") == 0) {
			$karyawan["kode_unit"] = substr($karyawan["kode_unit"], 0, 3);
		} else {
			$karyawan["kode_unit"] = substr($karyawan["kode_unit"], 0, 2);
		}

		$cek = $this->db->where(array('no_pokok' => $np_karyawan, 'month(tgl_dws)' => date('m'), 'year(tgl_dws)' => date('Y')))->get('ess_lembur_transaksi')->result_array();
		$get_jml = 0;
		foreach ($cek as $dt_lembur) {
			$dteStart = date_create(date('Y-m-d H:i', strtotime($dt_lembur['tgl_mulai'] . ' ' . $dt_lembur['jam_mulai'])));
			$dteEnd = date_create(date('Y-m-d H:i', strtotime($dt_lembur['tgl_selesai'] . ' ' . $dt_lembur['jam_selesai'])));
			$diff = date_diff($dteStart, $dteEnd);
			$hari = $diff->format("%a");
			$jam = $diff->format("%h");
			$get_jml = $get_jml + ($hari * 24) + ($jam);
		}
		$get_jml = $get_jml + $total;

		$this->load->model('M_approval');
		if ($get_jml > 72) {
			$send_data['kode_unit'] = substr($kode, 0, 2);
			$cek_kode = $send_data['kode_unit'] . "000";
			$cek_approval = $this->db->where('divisi', $cek_kode)->get('mst_approval_lembur');
			if ($cek_approval->num_rows() > 0) {
				$set_approval = 'list_atasan_minimal_' . $cek_approval->row()->approval;
				$send_data['atasan'] = $this->M_approval->$set_approval(array($karyawan["kode_unit"]), $np_karyawan);

				if ($cek_approval->row()->approval == 'kasek')
					$send_data['kode_unit'] = substr($kode, 0, 4);
				else if ($cek_approval->row()->approval == 'kadep')
					$send_data['kode_unit'] = substr($kode, 0, 3);
				else
					$send_data['kode_unit'] = substr($kode, 0, 2);
			} else {
				$send_data['atasan'] = $this->M_approval->list_atasan_minimal_kadiv(array($karyawan["kode_unit"]), $np_karyawan);
			}
		} else if ($total <= 4) {
			$send_data['kode_unit'] = substr($kode, 0, 4);
			$send_data['atasan'] = $this->M_approval->list_atasan_minimal_kasek(array($karyawan["kode_unit"]), $np_karyawan);
		} else {
			$send_data['kode_unit'] = substr($kode, 0, 3);
			$send_data['atasan'] = $this->M_approval->list_atasan_minimal_kadep(array($karyawan["kode_unit"]), $np_karyawan);
		}

		$option = "<option value=''>Pilih Atasan</option>";
		for ($i = 0; $i < count($send_data['atasan']); $i++) {
			$option .= "<option value='" . $send_data['atasan'][$i]['no_pokok'] . "' " . ($data_lembur["np_approver"] == $send_data['atasan'][$i]['no_pokok'] ? 'selected' : '') . ">" . $send_data['atasan'][$i]['nama'] . "</option>";
		}

		return $option;
	}
}
	
	/* End of file pengajuan_lembur.php */
	/* Location: ./application/controllers/administrator/pengajuan_lembur.php */