<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

use Restserver\Libraries\REST_Controller;

class MY_Controller extends REST_Controller
{

    function __construct()
    {
        parent::__construct();
        if (!@$this->input->request_headers()['token']) {
            $this->response([
                'status' => false,
                'message' => 'Token is required.'
            ], REST_Controller::HTTP_BAD_REQUEST);
            exit;
        } else {
            # cek token
            $cek = $this->m_login_api->auth_by_token($this->input->request_headers()['token']);
            if ($cek->num_rows() == 0) {
                $this->response([
                    'status' => false,
                    'message' => 'Token is expired. Please login.'
                ], REST_Controller::HTTP_BAD_REQUEST);
                exit;
            }
        }

        $this->load->model('api/M_profil_api', 'profil');
        $this->token = $this->input->request_headers()['token'];
        $this->data_karyawan = [];
        $get_data_karyawan = $this->profil->get_profil($this->input->request_headers()['token']);
        if ($get_data_karyawan->num_rows() > 0) {
            $this->data_karyawan = $get_data_karyawan->row();
        }

        # pengadministrasi
        $this->load->model("m_login");
        $list_pengadministrasi = $this->m_login->list_pengadministrasi($cek->row()->user_id);
        $this->list_pengadministrasi = $list_pengadministrasi;

        # data user
        $this->account = $this->db->select('id,username,no_pokok,face_id')->where('id', $cek->row()->user_id)->get('usr_pengguna')->row();

        $this->nama_db = $this->db->database;
    }
}
