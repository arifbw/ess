<?php defined('BASEPATH') OR exit('No direct script access allowed');
include_once( APPPATH . 'core/Group_Controller.php' );
class Hak_akses extends Group_Controller {
    
    function __construct(){
        parent::__construct();
        if(!in_array($this->id_group,[15])){
            $this->response([
                'status'=>false,
                'message'=>"Hanya bisa diakses oleh otoritas: Admin Pamsiknilmat Masterdata",
                'data'=>[]
            ], MY_Controller::HTTP_FORBIDDEN);
        }
    }

    function index_get(){
        if( empty($this->get('id')) ){
            $this->response([
                'status'=>false,
                'message'=>"ID harus diisi",
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
        }

        $data = $this->db
            ->select('mst_karyawan.no_pokok, mst_karyawan.nama, mst_karyawan.kode_unit, mst_karyawan.nama_unit')
            ->join('mst_karyawan', 'FIND_IN_SET(mst_karyawan.no_pokok, mst_pos.no_pokok)')
            ->where('mst_pos.id', $this->get('id'))
            ->get('mst_pos')->result_array();
        
        $this->response([
            'status'=>true,
            'message'=>"Data Karyawan yang memiliki Hak Akses",
            'data'=>$data
        ], MY_Controller::HTTP_OK);
    }
}
