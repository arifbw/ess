<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Clock_karyawan extends MY_Controller {
    
    function __construct(){
        parent::__construct();
    }
    
    function index_get(){
        $data = [];
        try {
            if(empty($this->get('tanggal'))){
                $this->response([
                    'status'=>false,
                    'message'=>'Tanggal is required',
                    'data'=>$data
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($this->get('np'))){
                $this->response([
                    'status'=>false,
                    'message'=>'NP is required',
                    'data'=>$data
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                $tanggal = date('Y-m-d', strtotime($this->get('tanggal')));
                $np_karyawan = $this->get('np');
                // $get_assesment = $this->db->select('clock_in,clock_out')->where(['np_karyawan'=>$np_karyawan, 'tanggal'=>$tanggal])->get('ess_self_assesment_covid19');
                $m = date('Y_m', strtotime($tanggal));
                $get_assesment = $this->db->select('is_dinas_luar, tapping_fix_approval_status as status_approval, tapping_fix_approval_alasan as alasan_approval, tapping_fix_approval_np as np_approval, (CASE WHEN tapping_fix_1 is null THEN DATE_FORMAT(tapping_fix_1_temp, "%H:%i:%s") ELSE DATE_FORMAT(tapping_fix_1, "%H:%i:%s") END) as clock_in, (CASE WHEN tapping_fix_2 is null THEN DATE_FORMAT(tapping_fix_2_temp, "%H:%i:%s") ELSE DATE_FORMAT(tapping_fix_2, "%H:%i:%s") END) as clock_out')->where(['np_karyawan'=>$np_karyawan, 'dws_tanggal'=>$tanggal])->get('ess_cico_'.$m);
                
                if($get_assesment->num_rows()==1){
                    $get_ass = $get_assesment->row_array();
                    if ($get_ass['np_approval']==null)
                        $get_ass['np_approval'] = '';
                    $this->response([
                        'status'=>true,
                        'message'=>'Success',
                        'data'=>$get_ass
                    ], MY_Controller::HTTP_OK);
                } else{
                    $this->response([
                        'status'=>false,
                        //'message'=>'Belum input self assesment tanggal '.$tanggal,
                        'message'=>'Anda belum clock in/out di tanggal '.$tanggal,
                        'data'=>$data
                    ], MY_Controller::HTTP_NOT_FOUND);
                }
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
