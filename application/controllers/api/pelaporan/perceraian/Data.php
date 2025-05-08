<?php defined('BASEPATH') OR exit('No direct script access allowed');
include_once( APPPATH . 'core/Group_Controller.php' );
class Data extends Group_Controller {
    
    function __construct(){
        parent::__construct();
        if(!in_array($this->id_group,[4,5])){
            $this->response([
                'status'=>false,
                'message'=>"Hanya bisa diakses oleh otoritas: Pengguna dan Pengadministrasi Unit Kerja",
                'data'=>[]
            ], MY_Controller::HTTP_FORBIDDEN);
        }
    }
    
    function index_get(){
        $this->db->select("*")->from("ess_laporan_perceraian a");
        if($this->id_group==5){
            $this->db->where('a.np_karyawan', $this->data_karyawan->np_karyawan);
        } else if($this->id_group==4){
            $list = [];
            foreach($this->list_pengadministrasi as $l){
                $list[] = $l['kode_unit'];
            }
            $this->db->where_in('a.kode_unit', $list);
        }
        $this->db->where('deleted_at is null');
        $data = $this->db->get()->result_array();
        for ($x = 0; $x < count($data); $x++) {
            $status = '-';
            
            if ($data[$x]['surat_cerai'] != null && $data[$x]['surat_cerai'] != '') {
                $surat_cerai = base_url('uploads/pelaporan/perceraian/surat_cerai/').$data[$x]['surat_cerai'];
                $data[$x]['surat_cerai'] = $surat_cerai;
            }

            if ($data[$x]['putusan_pengadilan'] != null && $data[$x]['putusan_pengadilan'] != '') {
                $putusan_pengadilan = base_url('uploads/pelaporan/perceraian/putusan_pengadilan/').$data[$x]['putusan_pengadilan'];
                $data[$x]['putusan_pengadilan'] = $putusan_pengadilan;
            }

            switch($data[$x]['approval_status']){
                case '0':
                    $status = 'Menunggu Persetujuan Atasan';
                    break;
                case '1':
                    $status = 'Disetujui Atasan';
                    break;
                case '2':
                    $status = 'Ditolak Atasan';
                    break;
                case '3':
                    $status = 'Verifikasi KAUN SDM';
                    break;
                case '4':
                    $status = 'Tidak Disetujui SDM';
                    break;
                case '5':
                    $status = 'SUBMIT ERP';
                    break;
                case '6':
                    $status = 'Ditolak Admin SDM';
                    break;
                default:
                    $status = '-';
            }
            $data[$x]['status'] = $status;
        }
        $this->response([
            'status'=>true,
            'message'=>'Laporan Perceraian',
            'data'=>$data
        ], MY_Controller::HTTP_OK);
    }
}
