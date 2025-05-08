<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_menu_api extends CI_Model
{

	private $table_master_menu = "sys_master_menu";
	private $table_posisi_menu = "sys_posisi_menu";
	private $table_isi_menu = "sys_isi_menu";
	private $table_menu_grup_pengguna = "sys_menu_grup_pengguna";
	private $table_modul = "sys_modul";

	public function __construct()
	{
		parent::__construct();
		//Do your magic here
	}

	public function daftar_menu_grup_pengguna($id_grup_pengguna)
	{
		$data = $this->db->select("pm.shortcode shortcode_posisi_menu")
			->select("mm.id id_master_menu")
			->from($this->table_posisi_menu . " pm")
			->join($this->table_menu_grup_pengguna . " mgp", "pm.id=mgp.id_posisi_menu AND mgp.id_grup_pengguna='$id_grup_pengguna'", "left")
			->join($this->table_master_menu . " mm", "mgp.id_master_menu=mm.id AND mgp.id_grup_pengguna='$id_grup_pengguna'", "left")
			->get()
			->result_array();
		return $data;
	}

	public function get_menu($id_menu)
	{
		$data = $this->db->select("i.level, i.urutan_induk, i.urutan")
			->select("m.nama")
			->select_max("p.urutan", "sebelum")
			->select_min("n.urutan", "setelah")
			->from($this->table_master_menu . " mm")
			->join($this->table_isi_menu . " i", "i.id_master_menu = mm.id", "left")
			->join($this->table_modul . " m", "i.id_modul = m.id AND m.status=1", "left")
			->join($this->table_isi_menu . " p", "i.urutan_induk=p.urutan_induk AND i.urutan > p.urutan", "left")
			->join($this->table_isi_menu . " n", "i.urutan_induk=n.urutan_induk AND i.urutan < n.urutan", "left")
			->where("i.id_master_menu", $id_menu)
			->group_by("i.id_modul")
			->order_by("i.urutan asc")
			->get()
			->result_array(); //echo $this->db->last_query();
		return $data;
	}

	public function cari_menu($id_menu, $cari_menu, $arr_urutan)
	{
		$this->db->select("i.level, i.urutan_induk, i.urutan")
			->select("m.nama")
			->select_max("p.urutan", "sebelum")
			->select_min("n.urutan", "setelah")
			->from($this->table_master_menu . " mm")
			->join($this->table_isi_menu . " i", "i.id_master_menu = mm.id", "left")
			->join($this->table_modul . " m", "i.id_modul = m.id AND m.status=1", "left")
			->join($this->table_isi_menu . " p", "i.urutan_induk=p.urutan_induk AND i.urutan > p.urutan", "left")
			->join($this->table_isi_menu . " n", "i.urutan_induk=n.urutan_induk AND i.urutan < n.urutan", "left")
			->where("i.id_master_menu", $id_menu);
		$this->db->group_start()
			->where_in("i.urutan", $arr_urutan);
		for ($i = 0; $i < count($arr_urutan); $i++) {
			$this->db->or_group_start()
				->like("i.urutan", $arr_urutan[$i], "after")
				->like("m.nama", $cari_menu)
				->group_end();
		}
		$this->db->group_end()
			->group_by("i.id_modul");
		$this->db->order_by("i.urutan asc");

		$data = $this->db->get(); //echo $this->db->last_query()."<br>";
		return $data;
	}

	public function cari_urutan_menu($id_menu, $cari_menu)
	{
		$data = $this->db->select("i.urutan")
			->from($this->table_isi_menu . " i")
			->join($this->table_modul . " m", "i.id_modul = m.id AND m.status=1")
			->where("i.id_master_menu", $id_menu)
			->like("m.nama", $cari_menu)
			->order_by("i.urutan ASC")
			->get()
			->result_array();
		//echo $this->db->last_query();
		return $data;
	}
}

/* End of file m_menu.php */
/* Location: ./application/models/m_menu.php */