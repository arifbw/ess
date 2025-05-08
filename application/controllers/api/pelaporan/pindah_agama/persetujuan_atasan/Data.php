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
        $this->db->select("*")->from("ess_laporan_pindah_agama a");
        $this->db->where('deleted_at is null');
        $this->db->where('approval_np', $this->data_karyawan->np_karyawan);
        $data = $this->db->get()->result_array();
        for ($x = 0; $x < count($data); $x++) {

            if ($data[$x]['surat_keterangan'] != null && $data[$x]['surat_keterangan'] != '') {
                $surat_keterangan = base_url('uploads/pelaporan/pindah_agama/surat_keterangan/').$data[$x]['surat_keterangan'];
                $data[$x]['surat_keterangan'] = $surat_keterangan;
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
            'message'=>'Laporan Pindah Agama',
            'data'=>$data
        ], MY_Controller::HTTP_OK);
    }
}
