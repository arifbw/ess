<?php defined('BASEPATH') or exit('No direct script access allowed');

class MyContribution extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        // $this->load->helper("cutoff_helper");
        // $this->load->helper("tanggal_helper");
        $this->load->helper("karyawan_helper");
        // $this->load->helper("reference_helper");
        
		$this->folder_model = 'poin_reward/';
    }

    public function index_post()
    {
		$this->load->model($this->folder_model . "M_mycontribution");
        $id = $this->input->post('id');
        $tabel = 'my_contribution';

        $tanggal = date('Y-m-d H:i:s');

        $approval_alasan = $this->input->post('approval_alasan', true);
        $poin = $this->input->post('poin', true);
        $status = $this->input->post('status_verifikasi', true);

        $set = [
            'status_verifikasi' => $status,
            'approval_np' => $this->data_karyawan->np_karyawan,
            'approval_nama' => $this->data_karyawan->nama,
            'approval_at' => $tanggal,
        ];
        if ($status == '2') {
            $set['approval_alasan'] = $approval_alasan;
        } else {
            $set['poin'] = $poin;
            $data = $this->db->where('id', $id)->get($tabel)->row_array();
            $this->M_mycontribution->add_poin($poin, $data);
        }

        $this->db->where('id', $id)->set($set)->update($tabel);

        if ($this->db->affected_rows() > 0) {
            return $this->response([
                'status' => true,
                'message' => 'Berhasil verifikasi contribution',
            ], MY_Controller::HTTP_OK);
        } else {
            return $this->response([
                'status' => false,
                'message' => 'Gagal verifikasi contribution',
            ], MY_Controller::HTTP_BAD_REQUEST);
        }
    }
}
