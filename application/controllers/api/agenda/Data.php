<?php defined('BASEPATH') or exit('No direct script access allowed');
// include_once( APPPATH . 'core/Group_Controller.php' );
class Data extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model("poin_reward/m_manajemen_poin", "poin");
		$this->load->model("sikesper/M_agenda");
		$this->load->helper("tanggal_helper");
	}

	function index_get()
	{
        # cek id_group
        if(!@$this->input->request_headers()['id_group']){
            $this->response([
                'status'=>false,
                'message'=>'ID group is required.'
            ], MY_Controller::HTTP_BAD_REQUEST); exit;
        }
        $id_group = $this->input->request_headers()['id_group'];

		$np = $this->data_karyawan->np_karyawan;

		$id = $this->get('id');
		if (!empty($id)) {

			$this->db->select("a.*, b.nama as nama_lokasi, (select count(*) from ess_agenda_pendaftaran where id_agenda=a.id) as jml_daftar, (CASE WHEN lp.id IS NOT NULL THEN '1' ELSE '0' END) as status_baca");
			$this->db->from('ess_agenda a');
			$this->db->join('mst_lokasi b', 'a.lokasi = b.id', 'LEFT');
			$this->db->join('mst_kategori_agenda c', 'a.id_kategori = c.id', 'LEFT');
			$this->db->join('log_poin lp', "lp.agenda_id = a.id AND lp.created_by_np = '$np'", 'LEFT');
			$this->db->where('a.status', '1');
			$this->db->where('a.id', $id);
			$this->db->where('a.tanggal >=', date('Y-m-d'));
			$get = $this->db->get()->row();

			if ($get) {
				if ($get->image) $get->url = base_url() . "uploads/images/sikesper/agenda/" . $get->image;
				$get->kode_scan = $this->poin->kode_scan($np, $get->id);
			}

			$this->response([
				'status' => true,
				'data' => $get
			], MY_Controller::HTTP_OK);
		} else {
			$this->db->select("a.*, b.nama as nama_lokasi, (select count(*) from ess_agenda_pendaftaran where id_agenda=a.id) as jml_daftar, (CASE WHEN lp.id IS NOT NULL THEN '1' ELSE '0' END) as status_baca");
			$this->db->from('ess_agenda a');
			$this->db->join('mst_lokasi b', 'a.lokasi = b.id', 'LEFT');
			$this->db->join('mst_kategori_agenda c', 'a.id_kategori = c.id', 'LEFT');
			$this->db->join('log_poin lp', "lp.agenda_id = a.id AND lp.created_by_np = '$np'", 'LEFT');
			$this->db->where('a.status', '1');
			$this->db->where('a.tanggal >=', date('Y-m-d'));
			if(@$id_group){
				if(in_array($id_group, [30])){
					// no filter
				} else{
					$this->db->group_start();
					$this->db->where('a.np_tergabung', 'all');
					$this->db->or_where('a.np_tergabung is null', null, false);
					$this->db->or_where("FIND_IN_SET('{$np}', a.np_tergabung)");
					$this->db->group_end();
				}
			} else{
				$this->db->where("1 = 0", null,false);
			}
			$data = $this->db->get()->result();

			for ($i = 0; $i < count($data); $i++) {
				if ($data[$i]->image) $data[$i]->url = base_url() . "uploads/images/sikesper/agenda/" . $data[$i]->image;
				$data[$i]->kode_scan = $this->poin->kode_scan($np, $data[$i]->id);
			}

			$this->response([
				'status' => true,
				'data' => $data,
			], MY_Controller::HTTP_OK);
		}
	}

	function index_post()
	{
		$kode_scan = $this->post('kode_scan');
		$return = array("status" => false, "message" => "");
		// $data = explode('-', $kode_scan);
		// if (count($data) !== 3) {
		// 	$return["message"] = "Kode scan tidak sesuai";
		// 	$this->response($return, MY_Controller::HTTP_BAD_REQUEST);
		// 	exit;
		// }

		// $agenda_id = (int)$data[1];
		// $np_decode = $this->decodeNp($data[2]);

		// $karyawan = $this->db->where('no_pokok', $np_decode)->get('mst_karyawan')->row();
		// if (empty($karyawan)) {
		// 	$return["message"] = "Karyawan dengan no pokok $np_decode tidak ditemukan";
		// 	$this->response($return, MY_Controller::HTTP_BAD_REQUEST);
		// 	exit;
		// }

		// $get = $this->db->where('id', $agenda_id)->where('status', 1)->get('ess_agenda')->row();
		// if (empty($get)) {
		// 	$return["message"] = "Agenda tidak ditemukan";
		// 	$this->response($return, MY_Controller::HTTP_BAD_REQUEST);
		// 	exit;
		// }

		// $pendaftaran = $this->db->where('id_agenda', $agenda_id)->where('np_karyawan', $np_decode)->get('ess_agenda_pendaftaran')->row();
		// if (empty($pendaftaran)) {
		// 	$data_agenda = $this->M_agenda->api_cek_daftar_agenda($agenda_id, $this->id_group, $this->data_karyawan->np_karyawan);
		// 	if ($data_agenda->kuota > 0) {
		// 		$data_insert = [
		// 			'id_agenda' => $agenda_id,
		// 			'np_karyawan' => $np_decode,
		// 			'daftar_at' => date('Y-m-d H:i:s'),
		// 			'created' => date('Y-m-d H:i:s'),
		// 			'verifikasi_hadir' => 1,
		// 			'verifikasi_by' => $this->data_karyawan->np_karyawan,
		// 			'updated_at' => date('Y-m-d H:i:s')
		// 		];

		// 		$this->db->insert("ess_agenda_pendaftaran", $data_insert);
		// 	} else {
		// 		$return["message"] = "Kuota Agenda [$get->agenda] sudah penuh!";
		// 		$this->response($return, MY_Controller::HTTP_BAD_REQUEST);
		// 		exit;
		// 	}
		// } else if (!empty($pendaftaran->batal_at)) {
		// 	$return["message"] = "Pendaftaran Agenda [$get->agenda] telah dibatalkan pada " . datetime_indo($pendaftaran->batal_at);
		// 	$this->response($return, MY_Controller::HTTP_BAD_REQUEST);
		// 	exit;
		// } else {
		// 	$this->db->where('id_agenda', $agenda_id)
		// 		->where('batal_at is null', null, false)
		// 		->where('np_karyawan', $np_decode)
		// 		->update(
		// 			'ess_agenda_pendaftaran',
		// 			[
		// 				'verifikasi_hadir' => 1,
		// 				'verifikasi_by' => $this->data_karyawan->np_karyawan,
		// 				'updated_at' => date('Y-m-d H:i:s')
		// 			]
		// 		);
		// }

		// $data_insert = [
		// 	'tipe' => 'Debit',
		// 	'poin' => $get->poin,
		// 	'sumber' => 'Agenda',
		// 	'agenda_id' => $agenda_id,
		// 	'created_at' => date('Y-m-d H:i:s'),
		// 	'created_by_np' => $karyawan->no_pokok,
		// 	'created_by_nama' => $karyawan->nama,
		// 	'created_by_kode_unit' => $karyawan->kode_unit,
		// ];

		// $cek_data = $this->db->select('*')
		// 	->from('log_poin')
		// 	->where('agenda_id', $data_insert['agenda_id'])
		// 	->where('created_by_np', $data_insert['created_by_np'])
		// 	->get()->row();

		// if (empty($cek_data)) {
		// 	$poin_sekarang = $this->poin->poin_sekarang($data_insert['created_by_np']);
		// 	$data_insert['poin_awal'] = $poin_sekarang;
		// 	$data_insert['poin_hasil'] = $poin_sekarang + (int)$data_insert['poin'];
		// 	$this->db->insert("log_poin", $data_insert);
		// 	$params = [
		// 		'np' => $data_insert['created_by_np'],
		// 		'nama' => $data_insert['created_by_nama'],
		// 		'poin' => $data_insert['poin_hasil'],
		// 	];
		// 	$result = $this->poin->tambah_poin($params);
		// 	if ($result) {
		// 		$return["status"] = true;
		// 		$return["message"] = "Pendaftaran agenda berhasil. {$get->poin} Poin ditambahkan";

		// 		$fcm_token_penerima = '';
		// 		$fcm = $this->db->where(['np' => $karyawan->no_pokok])->get('mobile_fcm_tokens')->row();

		// 		if (!empty($fcm)) {
		// 			$fcm_token_penerima = $fcm->fcm_token;
		// 		}

		// 		$data = [
		// 			'id' => $this->uuid->v4(),
		// 			'np_pengirim' => $this->data_karyawan->np_karyawan,
		// 			'np_penerima' => $karyawan->no_pokok,
		// 			'fcm_token_penerima' => $fcm_token_penerima,
		// 			'judul' => "Pendaftaran Agenda Berhasil",
		// 			'pesan' => "Agenda [{$get->agenda}] berhasil didaftarkan oleh {$this->data_karyawan->nama}",
		// 			'data' => json_encode([
		// 				'uniqueCode' => rand(100000000, 9999999999),
		// 				'type' => "result_pendaftaran_agenda"
		// 			]),
		// 			'is_read' => '0',
		// 			'type' => 'result_pendaftaran_agenda',
		// 			'created_at' => date('Y-m-d H:i:s'),
		// 		];

		// 		$this->db->insert('mobile_notifikasi_log', $data);

		// 		if (!empty($fcm_token_penerima)) {
		// 			$notification_to_send = '{
		// 				"notification": {
		// 					"title": "' . $data['judul'] . '",
		// 					"body": "' . $data['pesan'] . '",
		// 					"click_action": "FLUTTER_NOTIFICATION_CLICK"
		// 				},
		// 				"priority": "high",
		// 				"data": {},
		// 				"to": "' . $fcm_token_penerima . '"
		// 			}';
		// 			$this->poin->curPostRequest($notification_to_send);
		// 		}
		// 		$this->response($return, MY_Controller::HTTP_OK);
		// 	} else {
		// 		$return["message"] = "Pendaftaran agenda gagal [{$get->agenda}]";
		// 		$this->response($return, MY_Controller::HTTP_BAD_REQUEST);
		// 		exit;
		// 	}
		// } else {
		// 	$return["message"] = "Anda sudah melakukan scan pada agenda ini [{$get->agenda}]";
		// 	$this->response($return, MY_Controller::HTTP_BAD_REQUEST);
		// 	exit;
		// }

		$result = $this->poin->scan_kode_agenda($kode_scan, $this->data_karyawan);

		if ($result['status']) {
			$this->response($result, MY_Controller::HTTP_OK);
		} else {
			$this->response($result, MY_Controller::HTTP_BAD_REQUEST);
		}
	}

	private function encodeNp($input) {
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

	private function decodeNp($input) {
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
}
