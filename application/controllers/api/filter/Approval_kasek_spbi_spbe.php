<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Approval_kasek_spbi_spbe extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->helper("fungsi_helper");
        $this->load->model("M_approval");
        $this->load->model("master_data/M_karyawan");
    }
    
    function index_get(){
        $data = [];
        try {
            if(empty($this->get('np'))){
                $this->response([
                    'status'=>false,
                    'message'=>"NP harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                $get_data_karyawan = $this->M_karyawan->get_karyawan($this->get('np'));
                $kode_unit = $get_data_karyawan['kode_unit'];
                $level_unit = level_unit($kode_unit);
                
                $get_approver = $this->M_approval->list_atasan_minimal_kasek([$kode_unit],$this->get('np'));
                $data = $get_approver;
                
                $this->response([
                    'status'=>true,
                    'message'=>'Approval minimal Kasek',
                    'data'=>$data
                ], MY_Controller::HTTP_OK);
            }
        } catch(Exception $e){
            $this->response([
                'status'=>false,
                'message'=>'Error Exception',
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
        }
    }
}
