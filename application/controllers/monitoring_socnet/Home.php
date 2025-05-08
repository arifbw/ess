<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {
    private $api_url;

    public function __construct() {
        parent::__construct();

        $meta = meta_data();
        foreach ($meta as $key => $value) {
            $this->data[$key] = $value;
        }

        $this->folder_view = 'monitoring_socnet/';
        $this->folder_model = 'monitoring_socnet/';
        $this->folder_controller = 'monitoring_socnet/';

        $this->akses = array();

        $this->load->model($this->folder_model . "M_table_monitoring_socnet");
        $this->load->library('user_agent');
        $this->load->config('config');

        $this->data["is_with_sidebar"] = true;

        $this->data['judul'] = "Monitoring Social Network";
        $this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
        $this->akses = akses_helper($this->data['id_modul']);
        $this->nama_db = $this->db->database;

        $this->api_url = $this->config->item('api_url');
        izin($this->akses["akses"]);
    }

    public function index() {
        $this->data["akses"] = $this->akses;
        $this->data["navigasi_menu"] = menu_helper();
        $this->data['api_url'] = $this->api_url;
        $this->data['content'] = $this->folder_view . "home";
        $this->load->view('template', $this->data);
    }

    public function comments($post_id) {
        $this->data['post_id'] = $post_id;
        $this->data['api_url'] = $this->api_url;
        $this->data["is_with_sidebar"] = true;
        $this->data["navigasi_menu"] = menu_helper();
        $this->data['content'] = $this->folder_view . "comments";
        $this->load->view('template', $this->data);
    }

    public function get_posts() {
        $params = $this->input->get();
        $data = $this->M_table_monitoring_socnet->get_posts($params);
        echo json_encode($data);
    }

    public function get_reports($post_id) {
        $data = $this->M_table_monitoring_socnet->get_reports($post_id);
        echo json_encode($data);
    }

    public function toggle_post($post_id) {
        $no_pokok = $this->session->userdata('no_pokok');
        $response = $this->M_table_monitoring_socnet->toggle_post($post_id, $no_pokok);
        echo json_encode($response);
    }

    public function get_comments($post_id) {
        $data = $this->M_table_monitoring_socnet->get_comments($post_id);
        echo json_encode($data);
    }

    public function hide_comment($comment_id) {
        $no_pokok = $this->session->userdata('no_pokok');
        $response = $this->M_table_monitoring_socnet->hide_comment($comment_id, $no_pokok);
        echo json_encode($response);
    }

    public function unhide_comment($comment_id) {
        $response = $this->M_table_monitoring_socnet->unhide_comment($comment_id);
        echo json_encode($response);
    }
}