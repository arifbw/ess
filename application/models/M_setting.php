<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_setting extends CI_Model
{

	private $table_modul = "sys_modul";
	private $table_kelompok_modul = "sys_kelompok_modul";
	private $table_pengaturan = "sys_pengaturan";
	private $table_hak_akses_grup_pengguna = "sys_hak_akses_grup_pengguna";
	private $table_aksi_modul = "sys_aksi_modul";

	public function __construct()
	{
		parent::__construct();
		//Do your magic here
	}

	public function ambil_hak_akses($id_grup_pengguna, $id_modul)
	{
		$data = $this->db->select("a.nama,IF(h.id_grup_pengguna IS NULL,false,true) akses", false)
			->from($this->table_aksi_modul . " a")
			->join($this->table_hak_akses_grup_pengguna . " h", "h.id_aksi_modul=a.id AND h.id_grup_pengguna=$id_grup_pengguna", "left")
			->where("a.id_modul", $id_modul)
			->where("a.status", 1)
			->get()
			->result_array();

		return $data;
	}

	public function ambil_id_modul($nama, $url = null)
	{
		$this->db->select("id")
			->from($this->table_modul)
			->where("nama", $nama)
			->where_not_in("url", array("", "#"));

		if ($url !== null) {
			$this->db->where("url", $url);
		}
		$data = $this->db->get();

		$return = "";

		if ($data->num_rows() == 1) {
			$return = $data->result_array()[0]["id"];
		}

		return $return;
	}

	public function ambil_id_kelompok_modul($id_modul)
	{
		$data = $this->db->select("id_kelompok_modul")
			->from($this->table_modul)
			->where("id", $id_modul)
			->get();
		$id_kelompok_modul = "";

		if ($data->num_rows() == 1) {
			$id_kelompok_modul = $data->result_array()[0]["id_kelompok_modul"];
		}
		return $id_kelompok_modul;
	}

	public function ambil_nama_kelompok_modul($id_kelompok_modul)
	{
		$data = $this->db->select("nama")
			->from($this->table_modul)
			->where("id", $id_kelompok_modul)
			->get();
		$nama = "";

		if ($data->num_rows() == 1) {
			$nama = $data->result_array()[0]["nama"];
		}
		return $nama;
	}

	public function ambil_pengaturan($nama)
	{
		$data = "";
		$data = $this->db->select("isi")
			->from($this->table_pengaturan)
			->where("nama", $nama)
			->get()
			->result_array()[0]["isi"];
		return $data;
	}

	public function ambil_url_modul($nama)
	{
		$data = $this->db->select("url")
			->from($this->table_modul)
			->where("nama", $nama)
			->get();
		$url = "";

		if ($data->num_rows() == 1) {
			$url = $data->result_array()[0]["url"];
		}
		return $url;
	}
}

/* End of file m_setting.php */
/* Location: ./application/models/m_setting.php */