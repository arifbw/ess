<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Data extends MY_Controller {
    
    function __construct(){
        parent::__construct();
    }
    
    function index_get(){
        $this->db->select("*")->from("ess_laporan_anak_tidak_tertanggung a");
        $this->db->where('deleted_at is null');
        $this->db->where('approval_np', $this->data_karyawan->np_karyawan);
        $data = $this->db->get()->result_array();
        for ($x = 0; $x < count($data); $x++) {

            $status = '-';
            switch($data[$x]['approval_status']){
                case '0':
                    $status = 'Menunggu Persetujuan Atasan';
                    $keterangan = 'approval'; 
                    break;
                case '1':
                    $status = 'Disetujui Atasan';
                    $keterangan = null;
                    break;
                case '2':
                    $status = 'Ditolak Atasan';
                    $keterangan = null;
                    break;
                case '3':
                    $status = 'Verifikasi KAUN SDM';
                    $keterangan = null;
                    break;
                case '4':
                    $status = 'Tidak Disetujui SDM';
                    $keterangan = null;
                    break;
                case '5':
                    $status = 'SUBMIT ERP';
                    $keterangan = null;
                    break;
                case '6':
                    $status = 'Ditolak Admin SDM';
                    $keterangan = null;
                    break;
                default:
                    $status = '-';
                    $keterangan = null;
            }
            $data[$x]['status'] = $status;
            $data[$x]['aksi'] = $keterangan;
        }
        $this->response([
            'status'=>true,
            'message'=>'Laporan Anak Tidak Teratanggung',
            'data'=>$data
        ], MY_Controller::HTTP_OK);
    }
}
