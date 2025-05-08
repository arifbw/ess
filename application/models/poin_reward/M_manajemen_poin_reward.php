<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_manajemen_poin_reward extends CI_Model
{

	private $table = "manajemen_poin_reward";

	public function __construct()
	{
		parent::__construct();
		//Do your magic here
	}

	public function ambil_perizinan_id($id)
	{
		$data = $this->db->from($this->table)
			->where("id", $id)
			->get()
			->result_array()[0];
		return $data;
	}

	public function daftar_manajemen_poin_reward()
	{
		$data = $this->db->select('*, (select count(*) from log_poin_reward where poin_reward_id=manajemen_poin_reward.id) as jumlah_klaim')
			->from($this->table)
			->order_by("id desc")
			->get()
			->result_array();
		return $data;
	}

	public function daftar_riwayat_poin_reward($np)
	{
		$data = $this->db->select("lpr.*, mpr.nama, mpr.poin")
			->from('log_poin_reward lpr')
			->join('manajemen_poin_reward mpr', 'lpr.poin_reward_id = mpr.id', 'LEFT')
			->order_by("lpr.id desc")
			->get()
			->result_array();

		return $data;
	}

	public function data_manajemen_poin_reward($nama)
	{
		$data = $this->db->from($this->table)
			->where("nama", $nama)
			->get()
			->result_array()[0];
		return $data;
	}

	public function cek_hasil_manajemen_poin_reward($nama, $konten, $poin, $kuota, $start_date, $end_date, $gambar, $status)
	{
		$this->db->from($this->table);
		$this->db->where('nama', $nama);
		$this->db->where('konten', $konten);
		$this->db->where('poin', $poin);
		$this->db->where('kuota', $kuota);
		$this->db->where('start_date', $start_date);
		$this->db->where('end_date', $end_date);
		$this->db->where('status', $status);

		if (!empty($gambar)) $this->db->where('gambar', $gambar);

		$data = $this->db->get();

		if ($data->num_rows() == 0) {
			$return = false;
		} else {
			$return = true;
		}

		return $return;
	}

	public function cek_tambah_manajemen_poin_reward($nama)
	{
		$data = $this->db->from($this->table)
			->where('nama', $nama)
			->get();

		if ($data->num_rows() == 0) {
			$return = true;
		} else {
			$return = false;
		}
		return $return;
	}

	public function cek_ubah_manajemen_poin_reward($nama, $nama_ubah)
	{
		$return = array("status" => false, "error_info" => "");
		$data = $this->db->from($this->table)
			->where('nama', $nama)
			->get();

		if ($data->num_rows() == 0) {
			$return["status"] = false;
			$return["error_info"] = "Poin Reward dengan nama <b>$nama</b> tidak ada pada <i>database</i>.";
		} else if ($data->num_rows() == 1) {
			if (strcmp($nama, $nama_ubah) == 0) {
				$return["status"] = true;
			} else {
				$data = $this->db->from($this->table)
					->where('lower(nama)', strtolower($nama_ubah))
					->where_not_in('lower(nama)', strtolower($nama))
					->get();

				if ($data->num_rows() > 0) {
					$return["status"] = false;
					$return["error_info"] = "nama Poin Reward <b>$nama_ubah</b> telah digunakan.";
				} else {
					$return["status"] = true;
				}
			}
		}
		return $return;
	}

	public function tambah($data)
	{
		$this->db->insert($this->table, $data);
	}

	public function ubah($set, $nama)
	{
		$this->db->where('nama', $nama)
			->update($this->table, $set);
	}
}

/* End of file m_manajemen_poin_reward.php */
/* Location: ./application/models/master_data/m_manajemen_poin_reward.php */
