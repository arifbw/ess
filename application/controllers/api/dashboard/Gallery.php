<?php defined('BASEPATH') or exit('No direct script access allowed');

class Gallery extends MY_Controller
{

	function __construct()
	{
		parent::__construct();
	}

	function index_get()
	{
		try {
			$date = date("Y-m-d");

			$this->db->select("id, agenda as nama, image as gambar, 'Agenda' as kategori, deskripsi as isi, tanggal");
			$this->db->distinct();
			$this->db->from("ess_agenda");
			$this->db->where('tanggal >=', $date);
			$this->db->where('status', '1');
			$this->db->get();
			$query1 = $this->db->last_query();

			$this->db->select("id, nama, gambar, 'Survey' as kategori, konten as isi, start_date as tanggal");
			$this->db->distinct();
			$this->db->from("manajemen_survey");
			$this->db->where('end_date >=', $date);
			$this->db->where('status', '1');

			$this->db->get();

			$query2 =  $this->db->last_query();
			$query = $this->db->query($query1 . " UNION " . $query2);

			$get = $query->result();

			//urut asc, tukar a dengan b jika ingin desc
			usort($get, function($a, $b) {
				if ($a->tanggal > $b->tanggal) {
					return 1;
				} elseif ($a->tanggal < $b->tanggal) {
					return -1;
				}
				return 0;
			});
			array_slice($get, 0, 6); // ambil 6 data saja

			for ($i = 0; $i < count($get); $i++) {
				if (!empty($get[$i]->gambar)) {
					if ($get[$i]->kategori == 'Agenda') {
						$get[$i]->url_gambar = base_url() . "uploads/images/sikesper/agenda/" . $get[$i]->gambar;
					} else if ($get[$i]->kategori == 'Survey') {
						$get[$i]->url_gambar = base_url() . "uploads/images/survey/" . $get[$i]->gambar;
					}
				}
			}

			$this->response([
				'status' => true,
				'data' => $get
			], MY_Controller::HTTP_OK);
		} catch (Exception $e) {
			$this->response([
				'status' => true,
				'message' => $e
			], MY_Controller::HTTP_BAD_REQUEST);
		}
	}
}
