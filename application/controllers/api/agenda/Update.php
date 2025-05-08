<?php defined('BASEPATH') or exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

class Update extends REST_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->folder_view = 'sikesper/agenda/';
		$this->folder_model = 'sikesper/';
		$this->folder_ajax_view = $this->folder_view . 'ajax/';

		$this->load->model($this->folder_model . "/M_agenda");
	}

	function index_post()
	{
		$this->load->library('upload');
		$config['upload_path']   = "./uploads/images/sikesper/agenda/";
		$config['allowed_types'] = "png|jpg";
		$config['max_size']      = "1000";
		$config['overwrite']     = true;
		$config['file_name']     = $this->input->post('agenda') . '-' . time();

		$this->upload->initialize($config);
		if ($this->upload->do_upload('image')) {

			$insert['image'] = $this->upload->data('file_name');
		}

		$waktu_tanggal = str_replace('/', '-', $this->input->post('tanggal'));
		$waktu_tanggal = date('Y-m-d', strtotime($waktu_tanggal));

		$insert['id'] = $this->input->post('no');
		$insert['agenda'] = $this->input->post('agenda');
		$insert['id_kategori'] = $this->input->post('id_kategori');
		$insert['deskripsi'] = $this->input->post('deskripsi');
		$insert['tanggal'] = $waktu_tanggal;
		$insert['waktu_mulai'] = $this->input->post('waktu_mulai');
		$insert['waktu_selesai'] = $this->input->post('waktu_selesai');
		$insert['kuota'] = ($this->input->post('kuota') > 0) ? $this->input->post('kuota') : null;
		$insert['lokasi'] = $this->input->post('lokasi');
		$insert['alamat'] = $this->input->post('alamat');
		$insert['provinsi'] = $this->input->post('id_provinsi');
		$insert['kabupaten'] = $this->input->post('id_kabupaten');
		$insert['longitude'] = $this->input->post('longitude');
		$insert['latitude'] = $this->input->post('latitude');
		$insert['status'] = $this->input->post('status');

		if (!strcmp($this->input->post("status"), "1")) {
			$this->data['status'] = true;
		} else if (!strcmp($this->input->post("status"), "0")) {
			$this->data['status'] = false;
		}

		$ubah = $this->ubah($insert);

		if ($ubah["status"]) {
			$this->data['success'] = "Perubahan Agenda Berhasil Dilakukan.";
		} else {
			$this->data['warning'] = $ubah['error_info'];
		}
		$this->data['panel_tambah'] = "";
		$this->data['agenda'] = "";
		$this->data['deskripsi'] = "";
		$this->data['tanggal'] = "";
		$this->data['jam'] = "";
		$this->data['waktu_mulai'] = "";
		$this->data['waktu_selesai'] = "";
		$this->data['kuota'] = "";
		$this->data['lokasi'] = "";
		$this->data['alamat'] = "";
		$this->data['provinsi'] = "";
		$this->data['kabupaten'] = "";
		$this->data['longitude'] = "";
		$this->data['latitude'] = "";
		$this->data['status'] = "";

		if ($ubah["status"]) {
			$this->response([
				'status' => true,
				'message' => 'Berhasil Mengubah data'
			], 200);
		} else {
			$this->response([
				'status' => false,
				'message' => $this->data['warning']
			], 500);
		}
	}

	private function ubah($data_update)
	{
		$return = array("status" => false, "error_info" => "");

		// $cek = $this->M_agenda->cek_daftar_agenda($data_update['id']);
		$this->db->select('a.*, b.nama as nama_lokasi, (select count(*) from ess_agenda_pendaftaran where id_agenda=a.id) as jml_daftar');
		$this->db->from('ess_agenda a');
		$this->db->join('mst_lokasi b', 'a.lokasi = b.id', 'LEFT');
		$this->db->join('mst_kategori_agenda c', 'a.id_kategori = c.id', 'LEFT');
		$this->db->where('a.status', '1');
		$this->db->where('a.tanggal >=', date('Y-m-d'));

		$cek = $this->db->get();
		if ($cek->row()) {

			$arr_data_lama = $cek;

			$data_update['updated'] = date('Y-m-d H:i:s');
			$update = $this->M_agenda->update($data_update, $data_update['id']);
			// print_r($data_update);
			// die;
			if ($update) {
				$return["status"] = true;
			} else {
				$return["status"] = false;
				$return["error_info"] = "Perubahan Agenda <b>Gagal</b> Dilakukan.";
			}
		} else {
			$return["status"] = false;
			$return["error_info"] = $cek->result();
		}

		return $return;
	}
}
