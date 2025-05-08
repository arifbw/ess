<?php defined('BASEPATH') or exit('No direct script access allowed');
include_once(APPPATH . 'core/Group_Controller.php');

class MyContribution extends Group_Controller
{

    function __construct()
    {
        parent::__construct();

        // $this->load->helper("cutoff_helper");
        // $this->load->helper("tanggal_helper");
        $this->load->helper("karyawan_helper");
        // $this->load->helper("reference_helper");
    }

    public function index_get()
    {
        $id = $this->input->get('id');
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');

        $np = $this->data_karyawan->np_karyawan;

        try {
            if ($this->id_group == 5) {
                $this->db->where('np_karyawan', $np);
            }
            if (!empty($start_date) && !empty($end_date)) {
                $this->db->where('tanggal_submit >=', $start_date);
                $this->db->where('tanggal_submit <=', $end_date);
            }
            $this->db->where('deleted_at', null);

            if ($id) {
                $this->db->where('id', $id);
                $data = $this->db->get('my_contribution')->row();
                if (!empty($data->dokumen)) $data->url = base_url() . "uploads/mycontribution/dokumen/" . $data->dokumen;
            } else {
                $this->db->from('my_contribution')->order_by('created_at', 'desc');
                $data = $this->db->get()->result();

                for ($i = 0; $i < count($data); $i++) {
                    if (!empty($data[$i]->dokumen)) $data[$i]->url = base_url() . "uploads/mycontribution/dokumen/" . $data[$i]->dokumen;
                }
            }

            $this->response([
                'status' => true,
                'message' => 'Success',
                'data' => $data
            ], MY_Controller::HTTP_OK);
        } catch (Exception $e) {
            $this->response([
                'status' => false,
                'message' => 'Error Exception',
                'data' => $data
            ], MY_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function index_post()
    {
        $error = "";
        $this->load->library('upload');

        if ($_FILES['dokumen']['tmp_name'] != '') {
            //Surat Keterangan
            $config['upload_path'] = './uploads/mycontribution/dokumen';
            $config['allowed_types'] = 'pdf|jpg|png|jpeg';
            $config['max_size']    = '2148';
            $files = $_FILES;

            $this->upload->initialize($config);

            if ($files['dokumen']['name']) {
                $this->load->helper("file");
                if ($this->upload->do_upload('dokumen')) {
                    $up = $this->upload->data();
                    $dokumen = $up['file_name'];
                } else {
                    $error = $this->upload->display_errors();
                }
            } else {
                $error = "Dokumen Tidak Ditemukan";
            }
        }

        if ($error == "") {
            $np_karyawan        = $this->input->post('np_karyawan', true);

            $start_date            = date('Y-m-d');
            $end_date            = date('Y-m-d');
            $tahun_bulan         = $start_date != null ? str_replace('-', '_', substr("$start_date", 0, 7)) : str_replace('-', '_', substr("$end_date", 0, 7));
            $ref_dokumen = $this->db->where('id', $this->input->post('jenis_dokumen_id'))->get('ref_jenis_dokumen_contribution')->row_array();
            $ref_karyawan = $this->db->where('no_pokok', $np_karyawan)->get('mst_karyawan')->row_array();

            $data_insert = [
                'np_karyawan' => $np_karyawan,
                'nama_karyawan' => erp_master_data_by_np($np_karyawan, $start_date)['nama'],

                'jenis_dokumen' => $ref_dokumen['nama'],
                'perihal' => $this->input->post('perihal'),
                'jenis_dokumen_id' => $this->input->post('jenis_dokumen_id'),
                'tanggal_dokumen' => $this->input->post('tanggal_dokumen'),
                'tanggal_submit' => $this->input->post('tanggal_submit'),
                'poin' => $this->input->post('poin'),
                'kode_unit' => $ref_karyawan['kode_unit'],
                'nama_unit' => $ref_karyawan['nama_unit'],
            ];

            if (@$dokumen)
                $data_insert['dokumen'] = $dokumen;

            if ($this->input->post('edit_id', true) != '') {
                $data_lama = $this->db->where('id', $this->input->post('edit_id', true))->get('my_contribution')->row();

                $data_insert['updated_at'] = date('Y-m-d H:i:s');
                $data_insert['updated_by_np'] = $this->data_karyawan->np_karyawan;
                $data_insert['updated_by_nama'] = $this->data_karyawan->nama;
                $this->db->set($data_insert)->where('id', $this->input->post('edit_id', true))->update('my_contribution');
            } else {
                $data_insert['created_at'] = date('Y-m-d H:i:s');
                $data_insert['created_by_np'] = $this->data_karyawan->np_karyawan;
                $data_insert['created_by_nama'] = $this->data_karyawan->nama;
                $this->db->set($data_insert)->insert('my_contribution');
            }

            if ($this->db->affected_rows() > 0) {
                $message = 'Berhasil tambah my contribution';
                if ($this->input->post('edit_id', true) != '') {
                    if (@$dokumen)
                        unlink('./uploads/mycontribution/dokumen/' . $data_lama->dokumen);
                    $message = 'Berhasil ubah my contribution';
                }
                return $this->response([
                    'status' => true,
                    'message' => $message,
                ], MY_Controller::HTTP_OK);
            } else {
                $message = 'Gagal tambah my contribution';
                if ($this->input->post('edit_id', true) != '') {
                    $message = 'Gagal ubah my contribution';
                }
                return $this->response([
                    'status' => false,
                    'message' => $message,
                    'error' => $this->db->error()
                ], MY_Controller::HTTP_BAD_REQUEST);
            }
        } else {
            return $this->response([
                'status' => true,
                'message' => 'Gagal upload my contribution',
                'error' => $error,
            ], MY_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function index_delete()
    {
        $data_insert = [
            'deleted_at' => date('Y-m-d H:i:s'),
            'deleted_by_np' => $this->data_karyawan->np_karyawan,
            'deleted_by_nama' => $this->data_karyawan->nama
        ];
        $data_lama = $this->db->where('id', $this->input->get('id', true))->get('my_contribution')->row();

        $this->db->set($data_insert)->where('id', $this->input->get('id', true))->update('my_contribution');

        if ($this->db->affected_rows() > 0) {
            if (@$data_lama->dokumen) {
                unlink('./uploads/mycontribution/dokumen/' . $data_lama->dokumen);
            }
            return $this->response([
                'status' => true,
                'message' => 'Berhasil menghapus my contribution',
            ], MY_Controller::HTTP_OK);
        } else {
            return $this->response([
                'status' => true,
                'message' => 'Gagal menghapus my contribution',
                'error' => $this->db->error()
            ], MY_Controller::HTTP_BAD_REQUEST);
        }
    }
}
