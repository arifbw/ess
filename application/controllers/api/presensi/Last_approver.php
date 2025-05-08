<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Last_approver extends MY_Controller {
    
    function __construct(){
        parent::__construct();
    }
    
    function index_get(){
        $data = [];
        $np_karyawan = $this->data_karyawan->np_karyawan;
        try {
            $get_last_bulan = $this->db->select('TABLE_NAME')
                ->like('TABLE_NAME','ess_cico_','after')
                ->order_by('TABLE_NAME','DESC')
                ->limit(1)
                ->get('information_schema.TABLES')
                ->row();
            if( $get_last_bulan->TABLE_NAME!=null ){
                $get = $this->db->select('tapping_fix_approval_np, tapping_fix_approval_nama, tapping_fix_approval_nama_jabatan, dws_tanggal')->where('np_karyawan',$np_karyawan)->where('tapping_fix_approval_np IS NOT NULL',null,false)->order_by('id','DESC')->limit(1)->get($get_last_bulan->TABLE_NAME)->row();
            } else{
                $get = [];
            }
            $this->response([
                'status'=>true,
                'message'=>'Atasan terakhir untuk presensi',
                'data'=>$get
            ], MY_Controller::HTTP_OK);
        } catch(Exception $e){
            $this->response([
                'status'=>false,
                'message'=>'Error Exception',
                'data'=>$data
            ], MY_Controller::HTTP_BAD_REQUEST);
        }
    }
}
