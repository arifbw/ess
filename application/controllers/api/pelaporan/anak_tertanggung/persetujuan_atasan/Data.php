<?php defined('BASEPATH') OR exit('No direct script access allowed');
include_once( APPPATH . 'core/Group_Controller.php' );
class Data extends Group_Controller {
    
    function __construct(){
        parent::__construct();
        if(!in_array($this->id_group,[5])){
            $this->response([
                'status'=>false,
                'message'=>"Hanya bisa diakses oleh otoritas: Pengguna",
                'data'=>[]
            ], MY_Controller::HTTP_FORBIDDEN);
        }
    }
    
    function index_get(){
        $this->db->select("*")->from("ess_laporan_anak_tertanggung a");
        $this->db->where('deleted_at is null');
        $this->db->where('approval_np', $this->data_karyawan->np_karyawan);
        $data = $this->db->get()->result_array();
        for ($x = 0; $x < count($data); $x++) {

            if ($data[$x]['akta_kelahiran'] != null && $data[$x]['akta_kelahiran'] != '') {
                $akta_kelahiran = base_url('uploads/pelaporan/anak_tertanggung/akta_kelahiran/').$data[$x]['akta_kelahiran'];
                $data[$x]['akta_kelahiran'] = $akta_kelahiran;
            }

            if ($data[$x]['surat_keterangan'] != null && $data[$x]['surat_keterangan'] != '') {
                $surat_keterangan = base_url('uploads/pelaporan/anak_tertanggung/surat_keterangan/').$data[$x]['surat_keterangan'];
                $data[$x]['surat_keterangan'] = $surat_keterangan;
            }

            if ($data[$x]['kk'] != null && $data[$x]['kk'] != '') {
                $kk = base_url('uploads/pelaporan/anak_tertanggung/kk/').$data[$x]['kk'];
                $data[$x]['kk'] = $kk;
            }

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
                    $status = 'Laporan Pendidikan TIDAK DISETUJUI SDM';
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
            'message'=>'Laporan Anak Tertanggung',
            'data'=>$data
        ], MY_Controller::HTTP_OK);
    }
}
