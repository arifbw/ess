<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_gambar_dinamis extends CI_Model
{

	private $table = "gambar_dinamis";

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

	public function daftar_gambar_dinamis()
	{
		$data = $this->db->from($this->table)
			->order_by("id desc")
			->get()
			->result_array();
		return $data;
	}

	public function data_gambar_dinamis($nama)
	{
		$data = $this->db->from($this->table)
			->where("nama", $nama)
			->get()
			->result_array()[0];
		return $data;
	}

	public function cek_hasil_gambar_dinamis($nama, $gambar, $status)
	{
		$this->db->from($this->table);
		$this->db->where('nama', $nama);
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

	public function cek_tambah_gambar_dinamis($nama)
	{
		$data = $this->db->from($this->table)
			->where('nama', $nama)
			->get();

		if ($data->num_rows() == 0) {
			$return["status"] = true;
		} else {
			$return["status"] = false;
			$return["error_info"] = "Gambar dinamis dengan nama <b>$nama</b> sudah ada pada <i>database</i>.";
		}
		return $return;
	}

	public function cek_ubah_gambar_dinamis($nama, $nama_ubah)
	{
		$return = array("status" => false, "error_info" => "");
		$data = $this->db->from($this->table)
			->where('nama', $nama)
			->get();

		if ($data->num_rows() == 0) {
			$return["status"] = false;
			$return["error_info"] = "Gambar Dinamis dengan nama <b>$nama</b> tidak ada pada <i>database</i>.";
		} else if ($data->num_rows() == 1) {
			if (strcmp($nama, $nama_ubah) == 0) {
				$return["status"] = true;
			} else {
				if ($nama == 'Logo App Bar ESS Mobile') {
					$return["status"] = false;
					$return["error_info"] = "nama Gambar Dinamis <b>$nama</b> tidak dapat diubah karena digunakan di ESS Mobile.";
				} else {
					$data = $this->db->from($this->table)
						->where('lower(nama)', strtolower($nama_ubah))
						->where_not_in('lower(nama)', strtolower($nama))
						->get();

					if ($data->num_rows() > 0) {
						$return["status"] = false;
						$return["error_info"] = "nama Gambar Dinamis <b>$nama_ubah</b> telah digunakan.";
					} else {
						$return["status"] = true;
					}
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

/* End of file m_gambar_dinamis.php */
/* Location: ./application/models/master_data/m_gambar_dinamis.php */
