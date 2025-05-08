<?php defined('BASEPATH') or exit('No direct script access allowed');

class Agenda_survey_hari_ini extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model("poin_reward/m_manajemen_poin", "poin");
    }

    function index_get()
    {
        try {
            $np = $this->data_karyawan->np_karyawan;
            $hari_ini = date('Y-m-d');

            $agenda = $this->db->select("a.*, b.nama as nama_lokasi, (select count(*) from ess_agenda_pendaftaran where id_agenda=a.id) as jml_daftar, (CASE WHEN lp.id IS NOT NULL THEN '1' ELSE '0' END) as status_baca")
                ->from('ess_agenda a')
                ->join('mst_lokasi b', 'a.lokasi = b.id', 'LEFT')
                ->join('mst_kategori_agenda c', 'a.id_kategori = c.id', 'LEFT')
                ->join('log_poin lp', "lp.agenda_id = a.id AND lp.created_by_np = '$np'", 'LEFT')
                ->where('a.status', '1')
                ->where('a.tanggal', $hari_ini)
                ->get()->result();

            for ($i = 0; $i < count($agenda); $i++) {
                if ($agenda[$i]->image) $agenda[$i]->url = base_url() . "uploads/images/sikesper/agenda/" . $agenda[$i]->image;
                $agenda[$i]->kode_scan = $this->poin->kode_scan($np, $agenda[$i]->id);
            }

            $survey = $this->db->select("a.*, (CASE WHEN lp.id IS NOT NULL THEN '1' ELSE '0' END) as status_baca")
                ->from('manajemen_survey a')
                ->join('log_poin lp', 'lp.survey_id = a.id AND lp.created_by_np = ' . $np, 'LEFT')
                ->where('a.status', '1')
                ->where('a.start_date <=', $hari_ini)
                ->where('a.end_date >=', $hari_ini)
                ->get()->result();

            for ($i = 0; $i < count($survey); $i++) {
                if (!empty($survey[$i]->gambar)) $survey[$i]->url = base_url() . "uploads/images/survey/" . $survey[$i]->gambar;
            }

            $data = ['survey' => $survey, 'agenda' => $agenda,];
            $this->response([
                'status' => true,
                'message' => 'Success',
                'data' => $data
            ], MY_Controller::HTTP_OK);
        } catch (Exception $e) {
            $this->response([
                'status' => false,
                'message' => 'Not found',
                'data' => []
            ], MY_Controller::HTTP_BAD_REQUEST);
        }
    }
}
