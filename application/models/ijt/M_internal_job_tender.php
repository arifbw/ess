<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_internal_job_tender extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		//Do your magic here
	}

	function get_data()
	{
		$this->db->select('*');
		$this->db->from('ijt_data');
		$this->db->where('deleted_at', NULL);

		$data = $this->db->get();

		return $data;
	}

	public function get_data_by_id($id)
	{
		$this->db->select('*');
		$this->db->from('ijt_data');
		$this->db->where('id', $id);

		$query = $this->db->get();

		return $query->row();
	}

	function get_data_verval()
	{
		$this->db->select('c.nama, a.file_cv');
		$this->db->from('apply_dokumen_job a');
		$this->db->join('usr_pengguna b', 'b.user_id = a.id', 'left');
		$this->db->join('mst_karyawan c', 'b.no_pokok = c.no_pokok', 'left');
		$this->db->get();
	}

	function insert_data($data)
	{
		$data = [
			'kode_jabatan' => $data['kode_jabatan'],
			'kode_unit' => $data['kode_unit'],
			'nama_jabatan' => $data['nama_jabatan'],
			// 'gambar' => $data['gambar'],
			'deskripsi' => $data['deskripsi'],
			'start_date' => $data['start_date'],
			'end_date' => $data['end_date'],
			'created_at' => date('Y-m-d H:i:s')
		];

		$this->db->insert('ijt_data', $data);

		if ($this->db->affected_rows() > 0) {
			return $this->db->insert_id();
		} else {
			return "0";
		}
	}
	function apply_data($data)
	{
		$data = [
			'np' => $data['np'],
			'job_id' => $data['job_id'],
			'file_cv' => $data['file_cv'],
			'motivasi' => $data['motivasi'],
			'created_at' => date('Y-m-d H:i:s')
		];

		$this->db->insert('ijt_apply', $data);

		if ($this->db->affected_rows() > 0) {
			return $this->db->insert_id();
		} else {
			return "0";
		}
	}
	public function insert_apply($data)
	{
		$this->db->insert('ijt_apply', $data);
		if ($this->db->affected_rows() > 0) {
			return $this->db->insert_id();
		}
		return false;
	}
	public function apply_dokumen($data_dokumen)
	{
		foreach ($data_dokumen as $dokumen) {
			$this->db->insert('ijt_apply_dokumen', $dokumen);
			if ($this->db->affected_rows() <= 0) {
				log_message('error', 'Gagal menyimpan data: ' . json_encode($dokumen));
				return false; // Jika satu gagal, hentikan proses
			}
		}
		return true;
	}


	function insert_verval_administrasi($data)
	{
		$data = [
			'apply_id' => $data['apply_id'],
			'is_verval' => $data['is_verval'],
			'keterangan' => $data['keterangan'],
			'jenis_verval' => $data['jenis_verval'],
			'verif_at' => date('Y-m-d H:i:s'),
			'verif_by' => $data['verif_by']
		];

		$this->db->insert('ijt_verval', $data);

		if ($this->db->affected_rows() > 0) {
			return $this->db->insert_id();
		} else {
			return "0";
		}
	}

	function update_status($id, $data)
	{
		$this->db->where('id', $id);
		return $this->db->update('ijt_apply', $data);
	}

	function update_data($where, $data)
	{
		if (empty($where) || empty($data)) {
			return false;
		}

		$this->db->where('id', $where);
		$result = $this->db->update('ijt_data', $data);

		if ($result) {
			return $this->db->affected_rows() > 0 ? true : "Data tidak berubah.";
		} else {
			return false;
		}
	}

	public function is_applied($no_pokok, $job_id)
	{
		$this->db->where('np', $no_pokok);
		$this->db->where('job_id', $job_id);
		$this->db->where('deleted_at IS NULL', null, false);
		$query = $this->db->get('ijt_apply');

		return $query->num_rows() > 0;
	}

	public function is_applied_info($no_pokok, $job_id)
	{
		$this->db->select('a.id, a.np, a.job_id, a.motivasi, v.is_verval, v.jenis_verval, v.keterangan');
		$this->db->where('a.np', $no_pokok);
		$this->db->where('a.job_id', $job_id);
		$this->db->where('a.deleted_at IS NULL', null, false);
		$this->db->join('ijt_verval v', "v.apply_id = a.id AND v.jenis_verval = 'administrasi' AND v.deleted_at IS NULL", 'LEFT');
		return $this->db->get('ijt_apply a')->row();
	}

	public function get_dokumen_by_apply($apply_id)
	{
		$this->db->select('ijt_apply_dokumen.*, ijt_apply.np, mst_karyawan.nama');
		$this->db->from('ijt_apply_dokumen');
		$this->db->join('ijt_apply', 'ijt_apply.id = ijt_apply_dokumen.ijt_apply_id');
		$this->db->join('mst_karyawan', 'mst_karyawan.no_pokok = ijt_apply.np', 'left');
		$this->db->where('ijt_apply_dokumen.ijt_apply_id', $apply_id);

		// if ($where !== null) {
		// 	$this->db->like('ijt_apply_dokumen.nama_dokumen', 'cv');
		// }

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			return $query->result_array();
		}
		return [];
	}
}
