<?php defined('BASEPATH') or exit('No direct script access allowed');

class Daily_quest extends MY_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->load->helper("karyawan_helper");
		$this->load->model("poin_reward/m_manajemen_poin", "poin");
	}

	function index_get()
	{
		$date = date("Y-m-d");
		$np = $this->data_karyawan->np_karyawan;

		$get = $this->db->select("mp.*")
			->from("manajemen_daily_quest mp")
			->where('mp.status', 1)
			->where('mp.start_date <=', $date)
			->where('mp.end_date >=', $date)
			->get()->row();

		if ($get) {
			if ($get->gambar) $get->url = base_url() . "uploads/images/daily_quest/" . $get->gambar;

			$cek_data = $this->db->select('*')
				->from('log_daily_quest')
				->where('daily_quest_id', $get->id)
				->where('created_by_np', $np)
				->where('DATE(created_at) = ', date('Y-m-d'))
				->get()->row();

			if (empty($cek_data)) {
				$get->status_quest = '0';
			} else {
				$get->status_quest = '1';
			}

			$monday = date("Y-m-d", strtotime('monday this week'));
			$friday = date("Y-m-d", strtotime('friday this week'));

			$get->monday = $monday;
			$get->friday = $friday;

			$history_quest = $this->db->select('*')
				->from('log_daily_quest')
				->where('daily_quest_id', $get->id)
				->where('created_by_np', $np)
				->group_start()
				->where("DATE(created_at) <= '$friday'")
				->where("DATE(created_at) >='$monday'")
				->group_end()
				->get()->result();

			$get->history_quest = $history_quest;
		} else {
			$this->response([
				'status' => false,
				'message' => "Daily Quest belum ada!",
			], MY_Controller::HTTP_BAD_REQUEST);
		}

		$this->response([
			'status' => true,
			'data' => $get
		], MY_Controller::HTTP_OK);
	}

	function index_post()
	{
		$daily_quest_id = $this->input->post('id');

		$np_karyawan = $this->data_karyawan->np_karyawan;

		$datetime = !empty($this->input->post('date')) ? (new DateTime($this->input->post('date')))->format('Y-m-d H:i:s') : date('Y-m-d H:i:s');
		$date = !empty($this->input->post('date')) ? $this->input->post('date') : date('Y-m-d');

		$monday = date("Y-m-d", strtotime('monday this week'));
		$friday = date("Y-m-d", strtotime('friday this week'));

		$data_insert = [
			'daily_quest_id' => $daily_quest_id,
			'created_at' => $datetime,
			'created_by_np' => $np_karyawan,
			'created_by_nama' => $this->data_karyawan->nama,
		];

		if ($date > $friday) {
			$this->response([
				'status' => false,
				'message' => "Daily Quest minggu ini sudah berakhir",
			], MY_Controller::HTTP_BAD_REQUEST);
		}

		// if ($date != $monday) {
		// 	$yesterday = date("Y-m-d", strtotime("-1 days"));

		// 	$cek_data = $this->db->select('*')
		// 		->from('log_daily_quest')
		// 		->where('daily_quest_id', $data_insert['daily_quest_id'])
		// 		->where('created_by_np', $data_insert['created_by_np'])
		// 		->where('DATE(created_at) = ', $yesterday)
		// 		->get()->row();

		// 	if (empty($cek_data)) {
		// 		$this->response([
		// 			'status' => false,
		// 			'message' => "Daily Quest minggu ini sudah kadaluarsa karena tidak rutin dilakukan sejak hari senin",
		// 		], MY_Controller::HTTP_BAD_REQUEST);
		// 	}
		// }

		$cek_data = $this->db->select('*')
			->from('log_daily_quest')
			->where('daily_quest_id', $data_insert['daily_quest_id'])
			->where('created_by_np', $data_insert['created_by_np'])
			->where('DATE(created_at) = ', $date)
			->get()->row();


		if (empty($cek_data)) {
			$this->db->insert("log_daily_quest", $data_insert);

			$get = $this->db->select("a.*, (select count(*) from log_daily_quest where daily_quest_id=a.id AND created_by_np = $np_karyawan  AND DATE(created_at) <= '$friday' AND DATE(created_at) >='$monday') as jumlah_daily_quest,(select sum(poin) from log_poin where daily_quest_id=a.id AND created_by_np = $np_karyawan  AND DATE(created_at) <= '$friday' AND DATE(created_at) >='$monday') as jumlah_poin")
				->from('manajemen_daily_quest a')
				->where('a.id', $daily_quest_id)
				->where('a.status', 1)->get()->row();

			$poin_awal = $this->poin->poin_sekarang($data_insert['created_by_np']);



			if ($get->jumlah_daily_quest == 5 && $date == $friday) {
				// Poin bonus
				$plus =  (int)$get->poin;
				$poin_masuk = (int)$get->poin_harian + $plus;
			} else {
				// poin harian
				$poin_masuk = (int)$get->poin_harian;
			}

			$data_insert = [
				'tipe' => 'Debit',
				'poin' => (int)$poin_masuk,
				'poin_awal' => (int)$poin_awal,
				'poin_hasil' => (int)$poin_awal + (int)$poin_masuk,
				'sumber' => 'Daily Quest',
				'daily_quest_id' => $daily_quest_id,
				'jumlah_daily_quest' => $get->jumlah_daily_quest,
				'created_at' => $datetime,
				'created_by_np' => $this->data_karyawan->np_karyawan,
				'created_by_nama' => $this->data_karyawan->nama,
				'created_by_kode_unit' => $this->data_karyawan->kode_unit,
			];

			$cek_data = $this->db->select('*')
				->from('log_poin')
				->where('daily_quest_id', $data_insert['daily_quest_id'])
				->where('created_by_np', $data_insert['created_by_np'])
				->where("DATE(created_at) = '$date'")
				->get()->row();


			if (empty($cek_data)) {
				$this->db->insert("log_poin", $data_insert);
				$params = [
					'np' => $data_insert['created_by_np'],
					'nama' => $data_insert['created_by_nama'],
					'poin' => $data_insert['poin_hasil'],
				];
				$result = $this->poin->tambah_poin($params);
				if ($result) {
					$this->response([
						'status' => true,
						'message' => "Daily Quest berhasil. $poin_masuk Poin ditambahkan",
						'data' => []
					], MY_Controller::HTTP_OK);
				} else {
					$this->response([
						'status' => false,
						'message' => "Daily Quest Gagal. Poin gagal ditambahkan",
						'data' => []
					], MY_Controller::HTTP_BAD_REQUEST);
				}
			} else {
				$this->response([
					'status' => false,
					'message' => "Poin sudah pernah ditambahkan",
					'data' => []
				], MY_Controller::HTTP_BAD_REQUEST);
			}
			$this->response([
				'status' => true,
				'message' => "Daily Quest berhasil ditambahkan",
			], MY_Controller::HTTP_BAD_REQUEST);
		} else {
			$this->response([
				'status' => false,
				'message' => "Daily Quest sudah pernah ditambahkan",
			], MY_Controller::HTTP_BAD_REQUEST);
		}
	}

	function index_post_1()
	{
		$daily_quest_id = $this->post('id');
		$np_karyawan = $this->data_karyawan->np_karyawan;

		$datetime = date('Y-m-d H:i:s');
		$date = date("Y-m-d");

		$monday = date("Y-m-d", strtotime('monday this week'));
		$friday = date("Y-m-d", strtotime('friday this week'));

		$data_insert = [
			'daily_quest_id' => $daily_quest_id,
			'created_at' => $datetime,
			'created_by_np' => $np_karyawan,
			'created_by_nama' => $this->data_karyawan->nama,
		];

		if ($date > $friday) {
			$this->response([
				'status' => false,
				'message' => "Daily Quest minggu ini sudah berakhir",
			], MY_Controller::HTTP_BAD_REQUEST);
		}

		// if ($date != $monday) {
		// 	$yesterday = date("Y-m-d", strtotime("-1 days"));

		// 	$cek_data = $this->db->select('*')
		// 		->from('log_daily_quest')
		// 		->where('daily_quest_id', $data_insert['daily_quest_id'])
		// 		->where('created_by_np', $data_insert['created_by_np'])
		// 		->where('DATE(created_at) = ', $yesterday)
		// 		->get()->row();

		// 	if (empty($cek_data)) {
		// 		$this->response([
		// 			'status' => false,
		// 			'message' => "Daily Quest minggu ini sudah kadaluarsa karena tidak rutin dilakukan sejak hari senin",
		// 		], MY_Controller::HTTP_BAD_REQUEST);
		// 	}
		// }

		$cek_data = $this->db->select('*')
			->from('log_daily_quest')
			->where('daily_quest_id', $data_insert['daily_quest_id'])
			->where('created_by_np', $data_insert['created_by_np'])
			->where('DATE(created_at) = ', $date)
			->get()->row();

		if (empty($cek_data)) {
			$this->db->insert("log_daily_quest", $data_insert);

			if ($date == $friday) {
				$get = $this->db->select("a.*, (select count(*) from log_daily_quest where daily_quest_id=a.id AND created_by_np = $np_karyawan  AND DATE(created_at) <= '$friday' AND DATE(created_at) >='$monday') as jumlah_daily_quest")
					->from('manajemen_daily_quest a')
					->where('a.id', $daily_quest_id)
					->where('a.status', 1)->get()->row();

				$poin_awal = $this->poin->poin_sekarang($data_insert['created_by_np']);

				if ($get->jumlah_daily_quest == 5) {
					$poin_masuk = $get->poin;
				} else {
					$poin_masuk = (int)$get->jumlah_daily_quest * (int)$get->poin_harian;
				}

				$data_insert = [
					'tipe' => 'Debit',
					'poin' => (int)$poin_masuk,
					'poin_awal' => (int)$poin_awal,
					'poin_hasil' => (int)$poin_awal + (int)$poin_masuk,
					'sumber' => 'Daily Quest',
					'daily_quest_id' => $daily_quest_id,
					'jumlah_daily_quest' => $get->jumlah_daily_quest,
					'created_at' => $datetime,
					'created_by_np' => $this->data_karyawan->np_karyawan,
					'created_by_nama' => $this->data_karyawan->nama,
					'created_by_kode_unit' => $this->data_karyawan->kode_unit,
				];

				$cek_data = $this->db->select('*')
					->from('log_poin')
					->where('daily_quest_id', $data_insert['daily_quest_id'])
					->where('created_by_np', $data_insert['created_by_np'])
					->where("DATE(created_at) <= '$friday'")
					->where("DATE(created_at) >='$monday'")
					->get()->row();

				if (empty($cek_data)) {
					$this->db->insert("log_poin", $data_insert);
					$params = [
						'np' => $data_insert['created_by_np'],
						'nama' => $data_insert['created_by_nama'],
						'poin' => $data_insert['poin_hasil'],
					];
					$result = $this->poin->tambah_poin($params);
					if ($result) {
						$this->response([
							'status' => true,
							'message' => "Daily Quest berhasil. $poin_masuk Poin ditambahkan",
							'data' => []
						], MY_Controller::HTTP_OK);
					} else {
						$this->response([
							'status' => false,
							'message' => "Daily Quest Gagal. Poin gagal ditambahkan",
							'data' => []
						], MY_Controller::HTTP_BAD_REQUEST);
					}
				} else {
					$this->response([
						'status' => false,
						'message' => "Poin sudah pernah ditambahkan",
						'data' => []
					], MY_Controller::HTTP_BAD_REQUEST);
				}
			}
			$this->response([
				'status' => true,
				'message' => "Daily Quest berhasil ditambahkan",
			], MY_Controller::HTTP_BAD_REQUEST);
		} else {
			$this->response([
				'status' => false,
				'message' => "Daily Quest sudah pernah ditambahkan",
			], MY_Controller::HTTP_BAD_REQUEST);
		}
	}

	public function index_put()
	{
		//run program selamanya untuk menghindari maximal execution
		set_time_limit('0');


		$date_saturday = $this->put('date_saturday'); // Replace this with the date you want to check. YYYY-MM-DD

		echo "\n=== Mulai Cek Daily Quest pada [" . $date_saturday . "] ===";
		// Convert the date string to a timestamp
		$timestamp = strtotime($date_saturday);

		// Get the day of the week as a number (0 = Sunday, 1 = Monday, ..., 6 = Saturday)
		$day_of_week = date('w', $timestamp);

		// Check if it's Saturday (Saturday corresponds to 6)
		if ($day_of_week != 6) {
			echo "\nIt's not Saturday! $timestamp";
			return;
		}

		// Calculate the timestamp for Monday and Friday of the same week and convert to date strings
		$monday = date('Y-m-d', strtotime('monday this week', $timestamp));
		$friday = date('Y-m-d', strtotime('friday this week', $timestamp));

		// get log daily quest yang belum disimpan ke log_poin
		$history_quest = $this->db->select('a.created_by_np as np, b.nama, b.kode_unit, a.daily_quest_id, c.poin as poin_awal, md.poin_harian, md.poin, COUNT(a.id) as jumlah_daily_quest')
			->from('log_daily_quest a')
			->join('mst_karyawan b', 'a.created_by_np=b.no_pokok', 'LEFT')
			->join('manajemen_poin c', 'a.created_by_np=c.np', 'LEFT')
			->join('manajemen_daily_quest md', 'md.id = a.daily_quest_id', 'LEFT')
			->where("DATE(a.created_at) <= '$friday'")
			->where("DATE(a.created_at) >='$monday'")
			->where("NOT EXISTS (select 1 from log_poin where daily_quest_id=md.id AND created_by_np=a.created_by_np AND DATE(created_at) <= '$date_saturday' AND DATE(created_at) >='$monday')", null, false)
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
						echo "\nDaily Quest berhasil. $poin_masuk Poin ditambahkan. NP: " . $data_insert['created_by_np'];
					} else {
						echo "\nDaily Quest Gagal. Poin gagal ditambahkan. NP: " . $data_insert['created_by_np'];
					}
				}
			}
		}
		echo "\n=== Selesai Cek Daily Quest ===";
		return;
	}
}
