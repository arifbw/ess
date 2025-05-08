<?php defined('BASEPATH') or exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

class Add extends REST_Controller
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
		$config['allowed_types'] = "png|jpg|jpeg";
		$config['max_size']      = "1000";
		$config['overwrite']     = true;
		$config['file_name']     = $this->input->post('agenda') . '-' . time();

		$this->upload->initialize($config);
		if ($this->upload->do_upload('image')) {

			$insert['image'] = $this->upload->data('file_name');
		} else {
			$insert['image'] = "";
		}

		$waktu_tanggal = str_replace('/', '-', $this->input->post('tanggal'));
		$waktu_tanggal = date('Y-m-d', strtotime($waktu_tanggal));

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

		if(@$this->input->post('is_berkala')){
			$insert['is_berkala'] = 1;
			
			if(@$this->input->post('tanggal_berkala')!=[]){
				$array_berkala = [];
				foreach($this->input->post('tanggal_berkala') as $val){
					$waktu_berkala = str_replace('/', '-', $val);
					$waktu_berkala = date('Y-m-d', strtotime($waktu_berkala));
					if($waktu_berkala > $waktu_tanggal){
						$array_berkala[] = $waktu_berkala;
					}
				}
				$send_to_tambah['tanggal_berkala'] = array_unique($this->input->post('tanggal_berkala'));
			}
		} else{
			$insert['is_berkala'] = 0;
		}
		
		$send_to_tambah['insert'] = $insert;

		$tambah = $this->tambah($send_to_tambah);

		if($tambah['status']){
			$this->data['success'] = "Agenda <b>".$insert['agenda']."</b> berhasil ditambahkan.";
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
		}
		else{
			$this->data['warning'] = $tambah['error_info'];
		}
		// print_r($tambah);
		// die;
		if ($tambah["status"]) {
			$this->response([
				'status' => true,
				'message' => 'Berhasil Menambahkan data'
			], 200);
		} else {
			$this->response([
				'status' => false,
				'message' => $tambah["error_info"]
			], 500);
		}
	}

	private function tambah($data)
		{
			$return = array("status" => false, "error_info" => "");

			$data['created'] = date('Y-m-d H:i:s');
			
			$id_ = $this->M_agenda->insert($data['insert']);

			if($this->M_agenda->cek_daftar_agenda($id_)){
                if(@$data['tanggal_berkala']){
                    $data_tanggal_berkala=[];
                    foreach($data['tanggal_berkala'] as $val){
                        $data_tanggal_berkala[] = [
                            'kode'=>$this->uuid->v4(),
                            'ess_agenda_id'=>$id_,
                            'tanggal'=>$val,
                            'created_at'=>date('Y-m-d H:i:s')
                        ];
                    }
                    $this->db->insert_batch('ess_agenda_berkala',$data_tanggal_berkala);
                }
				$return["status"] = true;
			}else{
				$return["status"] = false;
				$return["error_info"] = "Penambahan Agenda <b>Gagal</b> Dilakukan.";
			}

			return $return;
		}
}
