<?php defined('BASEPATH') OR exit('No direct script access allowed');
include_once( APPPATH . 'core/Group_Controller.php' );
class Data extends Group_Controller {
    
    function __construct(){
        parent::__construct();
        if(!in_array($this->id_group,[18,22])){
            $this->response([
                'status'=>false,
                'message'=>"Hanya bisa diakses oleh otoritas: KAUN SDM - HRIS dan Admin SDM - HRIS",
                'data'=>[]
            ], MY_Controller::HTTP_FORBIDDEN);
        }
    }
    
    function index_get(){
        $status = $this->input->get('status');
        $this->db->select("*")->from("ess_laporan_anak_tidak_tertanggung a");
        $this->db->where('deleted_at is null');
        
        if ($status !='all' && $status !='' && $status != null) {
            $this->db->where('approval_status',$this->input->get('status'));
        }
        
        $data = $this->db->get()->result_array();
        for ($x = 0; $x < count($data); $x++) {

            $status = '-';
            switch($data[$x]['approval_status']){
                case '0':
                    $status = 'Menunggu Persetujuan Atasan';
                    $keterangan = null; 
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
                    $keterangan = 'approval';
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
            'message'=>'Persetujuan Laporan Anak Tidak Tertanggung',
            'data'=>$data
        ], MY_Controller::HTTP_OK);
    }
}
