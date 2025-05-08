<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_manajemen_poin extends CI_Model
{

	private $table = "manajemen_poin";

	public function __construct()
	{
		parent::__construct();
		$this->load->model("sikesper/M_agenda");
		//Do your magic here
	}

	public function poin_sekarang($np)
	{
		$tahun = date('Y');
		$data = $this->db->select("poin")
			->from($this->table)
			->where("np", $np)
			->where('tahun', $tahun)
			->get();
		$poin = '0';

		if ($data->num_rows() == 1) {
			$poin = $data->result_array()[0]["poin"];
		}
		return $poin;
	}

	public function daftar_manajemen_poin($tahun)
	{
		$data = $this->db->from($this->table)
			->where('tahun', $tahun)
			->order_by("nama asc")
			->get()
			->result_array();
		return $data;
	}

	public function daftar_riwayat_poin($np, $tahun)
	{
		$data = $this->db->select("lp.*, (CASE WHEN ea.id IS NOT NULL THEN ea.agenda WHEN ms.id IS NOT NULL THEN ms.nama WHEN mp.id IS NOT NULL THEN mp.nama WHEN mpr.id IS NOT NULL THEN mpr.nama WHEN mc.id IS NOT NULL THEN mc.perihal WHEN md.id IS NOT NULL THEN md.nama ELSE 'Sumber tidak diketahui' END) as nama")
			->from('log_poin lp')
			->join('ess_agenda ea', 'lp.agenda_id = ea.id', 'LEFT')
			->join('manajemen_survey ms', 'lp.survey_id = ms.id', 'LEFT')
			->join('manajemen_popup mp', 'lp.popup_id = mp.id', 'LEFT')
			->join('manajemen_poin_reward mpr', 'lp.poin_reward_id = mpr.id', 'LEFT')
			->join('manajemen_daily_quest md', 'lp.daily_quest_id = md.id', 'LEFT')
			->join('my_contribution mc', 'lp.mycontribution_id = mc.id', 'LEFT')
			->where('lp.created_by_np', $np)
			->where('YEAR(lp.created_at)', $tahun)
			->order_by("lp.id desc")
			->get()
			->result_array();

		return $data;
	}

	public function data_manajemen_poin($nama)
	{
		$data = $this->db->from($this->table)
			->where('nama', $nama)
			->get()
			->result_array()[0];
		return $data;
	}

	public function cek_hasil_manajemen_poin($nama, $unit = null, $poin = null, $status = null)
	{
		$data = $this->db->from($this->table)
			->where('nama', $nama)
			->get();

		if ($data->num_rows() == 0) {
			$return = false;
		} else {
			$return = true;
		}

		return $return;
	}

	public function cek_tambah_manajemen_poin($nama)
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

	public function cek_ubah_manajemen_poin($nama, $nama_ubah)
	{
		$return = array("status" => false, "error_info" => "");
		$data = $this->db->from($this->table)
			->where('nama', $nama)
			->get();

		if ($data->num_rows() == 0) {
			$return["status"] = false;
			$return["error_info"] = "Poin dengan nama <b>$nama</b> tidak ada pada <i>database</i>.";
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
					$return["error_info"] = "Poin dengan nama <b>$nama_ubah</b> telah digunakan.";
				} else {
					$return["status"] = true;
				}
			}
		}
		return $return;
	}

	public function tambah_poin($params)
	{
		$return = false;

		$data = $this->db->from($this->table)
			->where('np', $params['np'])
			->where('tahun', date('Y'))
			->get();

		if ($data->num_rows() == 0) {
			$params['tahun'] = date('Y');
			$return = $this->db->insert($this->table, $params);
		} else {
			$this->db->set('poin', $params['poin'], false);
			$this->db->where('np', $params['np']);
			$this->db->where('tahun', date('Y'));
			$return = $this->db->update($this->table);
		}
		return $return;
	}

	public function update_poin($params)
	{
		$this->db->set('poin', $params['poin'], false);
		$this->db->where('np', $params['np']);
		$this->db->where('tahun', date('Y'));
		$return = $this->db->update($this->table);

		return $return;
	}

	public function tambah($data)
	{
		$this->db->insert($this->table, $data);
	}

	public function ubah($set, $nama, $unit, $poin)
	{
		$this->db->where('nama', $nama)
			->update($this->table, $set);
	}

	public function scan_kode_poin_reward($kode_scan, $admin)
	{
		$return = array("status" => false, "message" => "");

		$data = explode('-', $kode_scan);
		if (count($data) !== 3) {
			$return["message"] = "Kode scan tidak sesuai";
			return $return;
		}

		$poin_reward_id = (int)$data[1];
		$np_decode = $this->decodeNp($data[2]);

		$karyawan = $this->db->where('no_pokok', $np_decode)->get('mst_karyawan')->row();

		if (empty($karyawan)) {
			$return["message"] = "Karyawan dengan no pokok $np_decode tidak ditemukan";
			return $return;
		}

		$get = $this->db->select("*, (select count(*) from log_poin_reward where poin_reward_id=manajemen_poin_reward.id) as jumlah_klaim")
			->from('manajemen_poin_reward')
			->where('id', $poin_reward_id)
			->where('status', 1)->get()->row();

		if (empty($get)) {
			$return["message"] = "Poin Reward tidak ditemukan";
			return $return;
		} else if ((int)$get->jumlah_klaim >= (int)$get->kuota) {
			$return["message"] = "Kuota Poin Reward sudah habis";
			return $return;
		}

		$data_insert = [
			'poin_reward_id' => $poin_reward_id,
			'created_at' => date('Y-m-d H:i:s'),
			'created_by_np' => $karyawan->no_pokok,
			'created_by_nama' => $karyawan->nama,
			'tukar_at' => date('Y-m-d H:i:s'),
			'tukar_by_np' => $admin->np_karyawan,
			'tukar_by_nama' => $admin->nama,
		];

		$poin_sekarang = $this->poin_sekarang($data_insert['created_by_np']);
		$sisa_poin = $poin_sekarang - (int)$get->poin;

		if ($sisa_poin <= 0) {
			$return["message"] = "Poin anda $poin_sekarang tidak cukup untuk menukar dengan reward ini [$get->poin]";
			return $return;
		}

		$this->db->insert("log_poin_reward", $data_insert);
		$log_poin_reward_id = $this->db->insert_id();

		// $get_lpr = $this->db->where('poin_reward_id', $poin_reward_id)->where('created_by_np', $karyawan->no_pokok)->get('log_poin_reward')->row();

		// if (empty($get_lpr)) {
		// 	$return["message"] = "Tidak ada penukaran [$get->nama] oleh $data_karyawan->nama";
		// 	return $return;
		// } else if ($get_lpr->status_tukar == 1) {
		// 	$return["message"] = "Penukaran [$get->nama] sudah pernah dilakukan oleh $data_karyawan->nama";
		// 	return $return;
		// } else {
		// 	$this->db->where('poin_reward_id', $poin_reward_id)->update('log_poin_reward', [
		// 		'status_tukar' => 1,
		// 		'tukar_at' => date('Y-m-d H:i:s'),
		// 		'tukar_by_np' => $this->data_karyawan->np_karyawan,
		// 		'tukar_by_nama' => $this->data_karyawan->nama,
		// 	]);
		// }

		$data_insert = [
			'tipe' => 'Kredit',
			'poin' => $get->poin,
			'sumber' => 'Poin Reward',
			'poin_reward_id' => $poin_reward_id,
			'log_poin_reward_id' => $log_poin_reward_id,
			'created_at' => date('Y-m-d H:i:s'),
			'created_by_np' => $karyawan->no_pokok,
			'created_by_nama' => $karyawan->nama,
			'created_by_kode_unit' => $karyawan->kode_unit,
		];

		// $cek_data = $this->db->select('*')
		// 	->from('log_poin')
		// 	->where('poin_reward_id', $data_insert['poin_reward_id'])
		// 	->where('created_by_np', $data_insert['created_by_np'])
		// 	->get()->row();

		// if (empty($cek_data)) {
		$poin_sekarang = $this->poin_sekarang($data_insert['created_by_np']);
		$data_insert['poin_awal'] = $poin_sekarang;
		$data_insert['poin_hasil'] = $poin_sekarang - (int)$data_insert['poin'];
		$this->db->insert("log_poin", $data_insert);
		$params = [
			'np' => $data_insert['created_by_np'],
			'nama' => $data_insert['created_by_nama'],
			'poin' => $data_insert['poin_hasil'],
		];
		$result = $this->tambah_poin($params);
		if ($result) {
			$return["status"] = true;
			$return["message"] = "Penukaran [$get->nama] berhasil";

			$fcm_token_penerima = '';
			$fcm = $this->db->where(['np' => $karyawan->no_pokok])->get('mobile_fcm_tokens')->row();

			if (!empty($fcm)) {
				$fcm_token_penerima = $fcm->fcm_token;
			}

			$data = [
				'id' => $this->uuid->v4(),
				'np_pengirim' => $admin->np_karyawan,
				'np_penerima' => $karyawan->no_pokok,
				'fcm_token_penerima' => $fcm_token_penerima,
				'judul' => "Penukaran Poin Reward Berhasil",
				'pesan' => "Poin Reward [$get->nama] berhasil ditukarkan oleh $admin->nama",
				'data' => json_encode([
					'uniqueCode' => rand(100000000, 9999999999),
					'type' => "result_penukaran_poin_reward"
				]),
				'is_read' => '0',
				'type' => 'result_penukaran_poin_reward',
				'created_at' => date('Y-m-d H:i:s'),
			];
			$this->db->insert('mobile_notifikasi_log', $data);

			if (!empty($fcm_token_penerima)) {
				$notification_to_send = '{
					"notification": {
						"title": "' . $data['judul'] . '",
						"body": "' . $data['pesan'] . '",
						"click_action": "FLUTTER_NOTIFICATION_CLICK"
					},
					"priority": "high",
					"data": {},
					"to": "' . $fcm_token_penerima . '"
				}';
				// $this->curPostRequest($notification_to_send);
			}
		} else {
			$return["message"] = "Penukaran [$get->nama] gagal";
		}
		// } else {
		// 	$return["message"] = "Penukaran Poin Reward [$get->nama] sudah pernah dilakukan";
		// 	return $return;
		// }
		return $return;
	}

	public function scan_kode_agenda($kode_scan, $admin)
	{
		$return = array("status" => false, "message" => "");

		$data = explode('-', $kode_scan);
		if (count($data) !== 3) {
			$return["message"] = "Kode scan tidak sesuai";
			return $return;
		}

		$agenda_id = (int)$data[1];
		$np_decode = $this->decodeNp($data[2]);

		$karyawan = $this->db->where('no_pokok', $np_decode)->get('mst_karyawan')->row();

		if (empty($karyawan)) {
			$return["message"] = "Karyawan dengan no pokok $np_decode tidak ditemukan";
			return $return;
		}

		$get = $this->db->where('id', $agenda_id)->where('status', 1)->get('ess_agenda')->row();
		if (empty($get)) {
			$return["message"] = "Agenda tidak ditemukan";
			return $return;
		}

		$pendaftaran = $this->db->where('id_agenda', $agenda_id)->where('np_karyawan', $np_decode)->get('ess_agenda_pendaftaran')->row();

		if (empty($pendaftaran)) {
			$data_agenda = $this->M_agenda->cek_daftar_agenda($agenda_id);
			if ($data_agenda->kuota > 0) {
				$data_insert = [
					'id_agenda' => $agenda_id,
					'np_karyawan' => $np_decode,
					'daftar_at' => date('Y-m-d H:i:s'),
					'created' => date('Y-m-d H:i:s'),
					'verifikasi_hadir' => 1,
					'verifikasi_by' => $admin->np_karyawan,
					'updated_at' => date('Y-m-d H:i:s')
				];

				$this->db->insert("ess_agenda_pendaftaran", $data_insert);
			} else {
				$return["message"] = "Kuota Agenda [$get->agenda] sudah penuh!";
				return $return;
			}
		} else if (!empty($pendaftaran->batal_at)) {
			$this->load->helper("tanggal_helper");
			$return["message"] = "Pendaftaran Agenda [$get->agenda] telah dibatalkan pada " . datetime_indo($pendaftaran->batal_at);
			return $return;
		} else {
			$this->db->where('id_agenda', $agenda_id)
				->where('batal_at is null', null, false)
				->where('np_karyawan', $np_decode)
				->update(
					'ess_agenda_pendaftaran',
					[
						'verifikasi_hadir' => 1,
						'verifikasi_by' => $admin->np_karyawan,
						'updated_at' => date('Y-m-d H:i:s')
					]
				);
		}

		$data_insert = [
			'tipe' => 'Debit',
			'poin' => $get->poin,
			'sumber' => 'Agenda',
			'agenda_id' => $agenda_id,
			'created_at' => date('Y-m-d H:i:s'),
			'created_by_np' => $karyawan->no_pokok,
			'created_by_nama' => $karyawan->nama,
			'created_by_kode_unit' => $karyawan->kode_unit,
		];

		$cek_data = $this->db->select('*')
			->from('log_poin')
			->where('agenda_id', $data_insert['agenda_id'])
			->where('created_by_np', $data_insert['created_by_np'])
			->get()->row();

		if (empty($cek_data)) {
			$poin_sekarang = $this->poin_sekarang($data_insert['created_by_np']);
			$data_insert['poin_awal'] = $poin_sekarang;
			$data_insert['poin_hasil'] = $poin_sekarang + (int)$data_insert['poin'];
			$this->db->insert("log_poin", $data_insert);
			$params = [
				'np' => $data_insert['created_by_np'],
				'nama' => $data_insert['created_by_nama'],
				'poin' => $data_insert['poin_hasil'],
			];
			$result = $this->poin->tambah_poin($params);
			if ($result) {
				$return["status"] = true;
				$return["message"] = "Pendaftaran agenda berhasil. $get->poin Poin ditambahkan";

				$fcm_token_penerima = '';
				$fcm = $this->db->where(['np' => $karyawan->no_pokok])->get('mobile_fcm_tokens')->row();

				if (!empty($fcm)) {
					$fcm_token_penerima = $fcm->fcm_token;
				}

				$data = [
					'id' => $this->uuid->v4(),
					'np_pengirim' => $admin->np_karyawan,
					'np_penerima' => $karyawan->no_pokok,
					'fcm_token_penerima' => $fcm_token_penerima,
					'judul' => "Pendaftaran Agenda Berhasil",
					'pesan' => "Agenda [$get->agenda] berhasil didaftarkan oleh $admin->nama",
					'data' => json_encode([
						'uniqueCode' => rand(100000000, 9999999999),
						'type' => "result_pendaftaran_agenda"
					]),
					'is_read' => '0',
					'type' => 'result_pendaftaran_agenda',
					'created_at' => date('Y-m-d H:i:s'),
				];

				$this->db->insert('mobile_notifikasi_log', $data);

				if (!empty($fcm_token_penerima)) {
					$notification_to_send = '{
						"notification": {
							"title": "' . $data['judul'] . '",
							"body": "' . $data['pesan'] . '",
							"click_action": "FLUTTER_NOTIFICATION_CLICK"
						},
						"priority": "high",
						"data": {},
						"to": "' . $fcm_token_penerima . '"
					}';
					// $this->curPostRequest($notification_to_send);
				}
			} else {
				$return["message"] = "Pendaftaran agenda gagal [$get->agenda]";
			}
		} else {
			$return["message"] = "Anda sudah melakukan scan pada agenda ini [$get->agenda]";
			return $return;
		}
		return $return;
	}

	// (abc-xxx-xxxxxx) (random-no unik agenda-no unik peserta yg telah dikonversi ke random 6 digit)
	public function kode_scan($np, $id)
	{
		$id_new = str_pad($id, 3, '0', STR_PAD_LEFT);
		$np_new = $this->encodeNp($np);

		$kode_scan =  $this->generateRandomString(3) . '-' . $id_new . '-' . $np_new;

		return $kode_scan;
	}

	private function generateRandomString($len)
	{
		$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		return substr(str_shuffle(str_repeat($pool, ceil($len / strlen($pool)))), 0, $len);
	}

	// old 
	// private function encodeNp($val)
	// {
	// 	return ((int)$val * 100) - 21354;
	// }

	// private function decodeNp($val)
	// {
	// 	return ((int)$val + 21354) / 100;
	// }
	// END old 

	function encodeNp($input) {
		// Ensure the input is a 4-digit string
		if (strlen($input) !== 4) {
			return false; // Invalid input
		}

		// Use base64 encoding
		$encoded = base64_encode($input);

		// Generate a 6-digit string by trimming/padding the encoded string
		$encoded = substr($encoded, 0, 6);  // Truncate to 6 characters
		if (strlen($encoded) < 6) {
			$encoded = str_pad($encoded, 6, 'A');  // Pad if less than 6
		}

		return $encoded;
	}

	function decodeNp($input) {
		// Ensure the input is a 6-character string
		if (strlen($input) !== 6) {
			return false; // Invalid input
		}

		// Add padding to make it valid base64 (4-character increments)
		$input .= '==';

		// Decode the base64 encoded string
		$decoded = base64_decode($input);

		// Validate the decoded result to ensure it is a 4-digit string
		if (strlen($decoded) === 4) {
			return $decoded;
		}

		return false; // Invalid decoded result
	}

	public function curPostRequest($_data)
	{
		/* Endpoint */
		$url = 'https://fcm.googleapis.com/fcm/send';

		/* eCurl */
		$curl = curl_init($url);

		/* Data */
		$data = $_data;

		/* Set JSON data to POST */
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));

		/* Define content type */
		curl_setopt($curl, CURLOPT_HTTPHEADER, array(
			'Content-Type:application/json',
			'Authorization: key=AAAAK1KRpvw:APA91bHL2jhnA3FbzRDzIbyQRjxpmqkDPUClO5XxxW1WABAy1_WzfZeV43L71AGo_QDBB0dR-j3QG8fLcB0GiaV94WUeoUP5wf99REFPfpqrxPcxJdKZdwxIjCUuZY-WIvcYrYN2THkm'
		));

		/* Return json */
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		/* make request */
		$result = curl_exec($curl);

		/* close curl */
		curl_close($curl);
	}
}

/* End of file m_manajemen_poin.php */
/* Location: ./application/models/administrator/m_manajemen_poin.php */
