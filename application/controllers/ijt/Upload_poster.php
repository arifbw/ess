<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Upload_poster extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();

		$meta = meta_data();
		foreach ($meta as $key => $value) {
			$this->data[$key] = $value;
		}

		$this->folder_view = 'ijt/';
		$this->folder_model = 'ijt/';
		$this->folder_controller = 'ijt/';

		$this->akses = array();

		$this->load->helper("tanggal_helper");

		$this->load->model($this->folder_model . "M_poster_tender", 'm_poster_tender');

		$this->data["is_with_sidebar"] = true;

		$this->data['judul'] = "Upload Poster";
		$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		$this->akses = akses_helper($this->data['id_modul']);
		izin($this->akses["akses"]);

        $this->path_poster = 'uploads/poster_job_tender';
	}

	public function index()
	{
		$this->data["akses"] = $this->akses;
		$this->data["navigasi_menu"] = menu_helper();
		$this->data['content'] = $this->folder_view . "upload_poster";

		$this->load->view('template', $this->data);
	}

	public function action_insert(){
		$this->load->library('form_validation');

		if ($_FILES['gambar']['name']=='') {
			$this->session->set_flashdata('warning', 'Gambar belum disertakan');
			redirect(base_url($this->folder_controller . 'upload_poster'));
		} else {
			$keterangan = html_escape($this->input->post('keterangan',true));
			$gambar_data = $this->upload_image();

			$gambar_json = json_encode($gambar_data);
			$data_insert['keterangan'] = $keterangan;
			$data_insert['poster_file'] = $gambar_json;
			$data_insert['created_at'] = date('Y-m-d H:i:s');

			$insert_data = $this->m_poster_tender->insert_data($data_insert);

			if ($insert_data != "0") {
				$this->session->set_flashdata('success', "Data berhasil ditambahkan.");
			} else {
				$this->session->set_flashdata('warning', "Data Gagal ditambahkan");
			}

			redirect(base_url($this->folder_controller . 'upload_poster'));
		}
	}

	private function upload_image(){
        if(!is_dir($this->path_poster)) mkdir($this->path_poster, 0777);
		$config['upload_path'] = $this->path_poster;
		$config['allowed_types'] = 'jpg|jpeg|png';
		$config['max_size'] = 8192;
		$config['encrypt_name'] = TRUE;

		$this->load->library('upload', $config);

		if ($this->upload->do_upload('gambar')) {
			$data = $this->upload->data();
			return [
				'file_name' => $data['file_name'],
				'file_size' => $data['file_size'],
				'file_type' => $data['file_type'],
			];
		} else {
			return ['error' => $this->upload->display_errors()];
		}
	}

	public function action_update(){
		$this->load->library('form_validation');

        $id = html_escape($this->input->post('id',true));
        $keterangan = html_escape($this->input->post('keterangan',true));
        $data_insert = [
            'keterangan' => $keterangan,
            'created_at' => date('Y-m-d H:i:s')
        ];
        if ($_FILES['gambar']['name']!='') {
            $gambar_data = $this->upload_image();
            $gambar_json = json_encode($gambar_data);
            $data_insert['poster_file'] = $gambar_json;
        }

        $insert_data = $this->m_poster_tender->update_data($id, $data_insert);

        if ($insert_data == true) {
            $this->session->set_flashdata('success', "Data berhasil diperbarui.");
        } else {
            $this->session->set_flashdata('warning', "Data Gagal diperbarui");
        }

        redirect(base_url($this->folder_controller . 'upload_poster'));
	}

	public function table_data(){
		$list 	= $this->m_poster_tender->get_datatables();
		$data = array();
		$no = @$_POST['start'];

		foreach ($list as $tampil) {
			$no++;
            $row = $tampil;
            $row->no = $no;

            $poster_file = json_decode($tampil->poster_file, true);
            if(@$poster_file['file_name'] && is_file($this->path_poster . "/{$poster_file['file_name']}")){
                $row->full_path = base_url($this->path_poster . "/{$poster_file['file_name']}");
                $img = '<img src="'.base_url($this->path_poster . "/{$poster_file['file_name']}").'" style="width: 100%;">';
            } else{
                $row->full_path = 'javascript:;';
                $img = 'File tidak ditemukan';
            }
            $row->img = $img;

            $actions = array();
            if (@$this->akses['ubah']) {
                $actions[] = "<button type='button' class='btn btn-primary btn-sm btn-update'>Ubah</button>";
            }
            if (@$this->akses['hapus']) {
                $actions[] = "<button type='button' class='btn btn-danger btn-sm btn-hapus'>Hapus</button>";
            }
            $row->actions = implode(' ', $actions);

			$data[] = $row;
		}

		$output = array(
			"draw" => @$_POST['draw'],
			"recordsTotal" => $this->m_poster_tender->count_all(),
			"recordsFiltered" => $this->m_poster_tender->count_filtered(),
			"data" => $data,
		);
		echo json_encode($output);
	}

	function destroy($id){
		$this->db->trans_begin();
		$data['deleted_at'] = date('Y-m-d H:i:s');

		$this->db->where('id', $id)->update('ijt_poster', $data);

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();

			$this->session->set_flashdata('warning', "Gagal menghapus data!");
			redirect(base_url($this->folder_controller . 'upload_poster'));
		} else {
			$this->db->trans_commit();

			$this->session->set_flashdata('success', "Berhasil menghapus data!");
			redirect(base_url($this->folder_controller . 'upload_poster'));
		}
	}
}
