<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_manajemen_popup extends CI_Model
{

	private $table = "manajemen_popup";

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

	public function daftar_manajemen_popup()
	{
		$data = $this->db->select('*, (select count(*) from log_popup where popup_id=manajemen_popup.id) as jumlah_baca')
			->from($this->table)
			->order_by("id desc")
			->get()
			->result_array();
		return $data;
	}

	public function data_manajemen_popup($nama)
	{
		$data = $this->db->from($this->table)
			->where("nama", $nama)
			->get()
			->result_array()[0];
		return $data;
	}

	public function cek_hasil_manajemen_popup($nama, $link, $poin, $start_date, $end_date, $gambar, $status)
	{
		$this->db->from($this->table);
		$this->db->where('nama', $nama);
		$this->db->where('link', $link);
		$this->db->where('poin', $poin);
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

	public function cek_date_manajemen_popup($start_date, $end_date, $nama = null)
	{
		$return = array("status" => false, "error_info" => "");
		$this->db->from($this->table);

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
		} else {
			$return["status"] = false;
			$return["error_info"] = "Popup dengan tanggal yang mencakup <b>$start_date</b> - <b>$end_date</b> sudah ada [" . $data->row()->nama . "]";
		}
		return $return;
	}

	public function cek_tambah_manajemen_popup($nama)
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

	public function cek_ubah_manajemen_popup($nama, $nama_ubah)
	{
		$return = array("status" => false, "error_info" => "");
		$data = $this->db->from($this->table)
			->where('nama', $nama)
			->get();

		if ($data->num_rows() == 0) {
			$return["status"] = false;
			$return["error_info"] = "Popup dengan nama <b>$nama</b> tidak ada pada <i>database</i>.";
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
					$return["error_info"] = "nama Popup <b>$nama_ubah</b> telah digunakan.";
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

/* End of file m_manajemen_popup.php */
/* Location: ./application/models/master_data/m_manajemen_popup.php */
