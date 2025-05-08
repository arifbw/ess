<?php defined('BASEPATH') or exit('No direct script access allowed');

class Daily_quest_cronjob extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->load->helper("karyawan_helper");
		$this->load->model("poin_reward/m_manajemen_poin", "poin");
	}
	public function index()
	{
		redirect(base_url('dashboard'));
	}

	public function cek()
	{
		//run program selamanya untuk menghindari maximal execution
		set_time_limit('0');

		$date = date("Y-m-d");
		$saturday = date("Y-m-d", strtotime('saturday this week'));
		if ($date == $saturday) {
			echo "<br>=== Mulai Cek Daily Quest " . date("Y-m-d H:i:s") . " ===";

			$monday = date("Y-m-d", strtotime('monday this week'));
			$friday = date("Y-m-d", strtotime('friday this week'));

			// get log daily quest yang belum disimpan ke log_poin
			$history_quest = $this->db->select('a.created_by_np as np, b.nama, b.kode_unit, a.daily_quest_id, c.poin as poin_awal, md.poin_harian, md.poin, COUNT(a.id) as jumlah_daily_quest')
				->from('log_daily_quest a')
				->join('mst_karyawan b', 'a.created_by_np=b.no_pokok', 'LEFT')
				->join('manajemen_poin c', 'a.created_by_np=c.np', 'LEFT')
				->join('manajemen_daily_quest md', 'md.id = a.daily_quest_id', 'LEFT')
				->where("DATE(a.created_at) <= '$friday'")
				->where("DATE(a.created_at) >='$monday'")
				->where("NOT EXISTS (select 1 from log_poin where daily_quest_id=md.id AND created_by_np=a.created_by_np AND DATE(created_at) <= '$saturday' AND DATE(created_at) >='$monday')", null, false)
				->order_by('a.created_by_np', 'asc')
				->group_by('a.created_by_np')
				->get()->result();

			if (count($history_quest) > 0) {
				for ($i = 0; $i < count($history_quest); $i++) {
					$get = $history_quest[$i];

					//hitung poin, jika full maka ambil dari poin, jika tidak maka dihitung sesuai jumlah hari dari poin_harian
					if ($get->jumlah_daily_quest == 5) {
						$poin_masuk = $get->poin;
					} else {
						$poin_masuk = (int)$get->jumlah_daily_quest * (int)$get->poin_harian;
					}

					$data_insert = [
						'tipe' => 'Debit',
						'poin' => (int)$poin_masuk,
						'poin_awal' => (int)$get->poin_awal,
						'poin_hasil' => (int)$get->poin_awal + (int)$poin_masuk,
						'sumber' => 'Daily Quest',
						'daily_quest_id' => $get->daily_quest_id,
						'jumlah_daily_quest' => $get->jumlah_daily_quest,
						'created_at' => date('Y-m-d H:i:s'),
						'created_by_np' => $get->np,
						'created_by_nama' => $get->nama,
						'created_by_kode_unit' => $get->kode_unit,
					];

					$this->db->insert("log_poin", $data_insert);

					if ($this->db->affected_rows() > 0) {
						$params = [
							'np' => $data_insert['created_by_np'],
							'nama' => $data_insert['created_by_nama'],
							'poin' => $data_insert['poin_hasil'],
						];
						$result = $this->poin->tambah_poin($params);

						if ($result) {
							echo "<br>Daily Quest berhasil. $poin_masuk Poin ditambahkan. NP: " . $data_insert['created_by_np'];
						} else {
							echo "<br>Daily Quest Gagal. Poin gagal ditambahkan. NP: " . $data_insert['created_by_np'];
						}
					}
				}
			}
			echo "<br>=== Selesai Cek Daily Quest " . date("Y-m-d H:i:s") . " ===";
		}
	}
}
