<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Reset_clock extends MY_Controller {
    
    function __construct(){
        parent::__construct();
    }
    
    function index_post(){
        $data = [];
        try {
            if(empty($this->post('tanggal'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Tanggal harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($this->post('np'))){
                $this->response([
                    'status'=>false,
                    'message'=>"NP harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                $tanggal = date('Y-m-d', strtotime($this->post('tanggal')));
                $this->db->where(['np_karyawan'=>$this->post('np'), 'tanggal'=>$tanggal])->update('ess_self_assesment_covid19',['clock_in'=>null, 'clock_out'=>null]);
                
                # reset cico
                $m = date('Y_m', strtotime($tanggal));
                $this->db->where(['np_karyawan'=>$this->post('np'), 'dws_tanggal'=>$tanggal])->update('ess_cico_'.$m, ['tapping_fix_1'=>null, 'tapping_fix_2'=>null, 'tapping_fix_approval_status'=>null, 'tapping_fix_approval_ket'=>null, 'wfh'=>null]);
                
                $this->response([
                    'status'=>true,
                    'message'=>'Direset',
                    'data'=>$data
                ], MY_Controller::HTTP_OK);
            }
        } catch(Exception $e){
            $this->response([
                'status'=>false,
                'message'=>'Error Exception',
                'data'=>$data
            ], MY_Controller::HTTP_BAD_REQUEST);
        }
    }
}
