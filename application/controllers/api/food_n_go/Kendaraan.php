<?php


defined('BASEPATH') or exit('No direct script access allowed');


class Kendaraan extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        // $this->folder_model = 'kendaraan/';

        // $this->load->model($this->folder_model . "m_data_pemesanan");
        // $this->load->model("M_dashboard", "dashboard");
    }


    public function daftar_get()
    {
        // Input
        $tampil_bulan_tahun = $this->input->post('tampil_bulan_tahun');
        $list_pengadministrasi = $this->input->post('list_pengadministrasi'); //array
        $group = $this->input->post('grup');
        $no_pokok = $this->input->post('no_pokok');

        $this->load->model($this->folder_model . "/M_tabel_data_pemesanan");

        if ($tampil_bulan_tahun != '') {
            $bulan = substr($tampil_bulan_tahun, 0, 2);
            $tahun = substr($tampil_bulan_tahun, 3, 4);
            $tampil_bulan_tahun = $tahun . "_" . $bulan;
        }

        if ($group == 4) //jika Pengadministrasi Unit Kerja
        {
            $var = array();
            $var_unit = array();
            foreach ($list_pengadministrasi as $data) {
                $var_unit[] = $data['kode_unit'];
            }

            if ($var_unit != []) {
                $get_list = $this->db->select('GROUP_CONCAT(no_pokok) as np')->where_in('kode_unit', $var_unit)->get('mst_karyawan')->row();
                if ($get_list->np != null) {
                    $var = explode(',', $get_list->np);
                    $var[] = $no_pokok;
                } else {
                    $var = 1;
                }
            } else {
                $var = 1;
            }
        } else if ($group == 5) {
            $var     = $no_pokok;
        } else {
            $var = 1;
        }

        // $data = [
        //     'status' => 'sucess',
        //     'data' => 'no data'
        // ];
        // $this->response($data, 200);
    }
}

/* End of file Test.php */
