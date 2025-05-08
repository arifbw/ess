<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_manajemen_daily_quest extends CI_Model
{

	private $table = "manajemen_daily_quest";

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

	public function daftar_manajemen_daily_quest()
	{
		$date = date("Y-m-d");
		$data = $this->db->select("*, (select count(*) from log_daily_quest where daily_quest_id=manajemen_daily_quest.id AND DATE(created_at) = " . $date . ") as jumlah_klaim")
			->from($this->table)
			->order_by("id desc")
			->get()
			->result_array();
		return $data;
	}

	public function daftar_riwayat_daily_quest($np)
	{
		$data = $this->db->select("md.nama, md.poin, md.poin_harian, lp.jumlah_daily_quest, lp.poin as poin_dapat, lp.created_at, lp.created_by_nama")
			->from('log_poin lp')
			->join('manajemen_daily_quest md', 'lp.daily_quest_id = md.id', 'LEFT')
			->where('sumber', 'Daily Quest')
			->order_by("lp.id desc")
			->get()
			->result_array();

		return $data;
	}

	public function data_manajemen_daily_quest($nama)
	{
		$data = $this->db->from($this->table)
			->where("nama", $nama)
			->get()
			->result_array()[0];
		return $data;
	}

	public function cek_hasil_manajemen_daily_quest($nama, $link, $poin, $poin_harian, $start_date, $end_date, $gambar, $status)
	{
		$this->db->from($this->table);
		$this->db->where('nama', $nama);
		// $this->db->where('link', $link);
		$this->db->where('poin', $poin);
		$this->db->where('poin_harian', $poin_harian);
		$this->db->where('start_date', $start_date);
		$this->db->where('end_date', $end_date);
		$this->db->where('status', $status);

		// if (!empty($gambar)) $this->db->where('gambar', $gambar);

		$data = $this->db->get();

		if ($data->num_rows() == 0) {
			$return = false;
		} else {
			$return = true;
		}

		return $return;
	}

	public function cek_poin_manajemen_daily_quest($nama, $poin, $poin_ubah, $poin_harian, $poin_harian_ubah)
	{
		$return = array("status" => false, "error_info" => "");

		if ($poin != $poin_ubah || $poin_harian != $poin_harian_ubah) {
			$history = $this->db->select('a.id')
				->from('log_daily_quest a')
				->join('manajemen_daily_quest md', 'md.id = a.daily_quest_id', 'LEFT')
				->join('log_poin lp', 'lp.agenda_id = a.id', 'LEFT')
				->where('md.nama !=', $nama)
				->where('lp.id IS NOT NULL', null, false)
				->get()->num_rows();

			if ($history == 0) {
				$return["status"] = true;
			} else {
				$return["error_info"] = "Anda tidak dapat mengubah jumlah poin karna sudah ada yang mendapatkan poin dari daily quest ini";
			}
		}
		return $return;
	}

	public function cek_date_manajemen_daily_quest($start_date, $end_date, $nama = null)
	{
		$return = array("status" => false, "error_info" => "");
		$this->db->from($this->table);
		$this->db->where('status', true);

		if (!empty($nama)) {
			$this->db->where('nama !=', $nama);
		}

		$this->db->group_start();
		$this->db->group_start();
		$this->db->where("DATE(start_date) <= '$start_date'");
		$this->db->where("DATE(end_date) >='$start_date'");
		$this->db->group_end();
		$this->db->or_group_start();
		$this->db->where("DATE(start_date) <= '$end_date'");
		$this->db->where("DATE(end_date) >='$end_date'");
		$this->db->group_end();
		$this->db->group_end();
		$data = $this->db->get();

		if ($data->num_rows() == 0) {
			$return["status"] = true;
		} else if ($data->num_rows() > 1) {
			$return["error_info"] = "Hanya boleh ada 1 Daily Quest dengan Status Aktif";
		} else {
			$return["error_info"] = "Daily Quest dengan tanggal yang mencakup <b>$start_date</b> - <b>$end_date</b> sudah ada [" . $data->row()->nama . "]";
		}
		return $return;
	}

	public function cek_tambah_manajemen_daily_quest($nama)
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

	public function cek_ubah_manajemen_daily_quest($nama, $nama_ubah)
	{
		$return = array("status" => false, "error_info" => "");
		$data = $this->db->from($this->table)
			->where('nama', $nama)
			->get();

		if ($data->num_rows() == 0) {
			$return["status"] = false;
			$return["error_info"] = "Daily Quest dengan nama <b>$nama</b> tidak ada pada <i>database</i>.";
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
					$return["error_info"] = "nama Daily Quest <b>$nama_ubah</b> telah digunakan.";
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

/* End of file m_manajemen_daily_quest.php */
/* Location: ./application/models/master_data/m_manajemen_daily_quest.php */
