<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Clock extends MY_Controller {
    
    function __construct(){
        parent::__construct();
    }
    
    function index_get(){
        $data = [];
        $data_insert = [];
        $data_cico = [];
        $np_karyawan = $this->data_karyawan->np_karyawan;
        try {
            if(!empty($this->get('tanggal'))){
                $tanggal = date('Y-m-d', strtotime($this->get('tanggal')));
                $m = date('Y_m', strtotime($tanggal));
                $get_assesment = $this->db->select('DATE_FORMAT(tapping_fix_1, "%H:%i:%s") as clock_in, DATE_FORMAT(tapping_fix_2, "%H:%i:%s") as clock_out')->where(['np_karyawan'=>$np_karyawan, 'dws_tanggal'=>$tanggal])->get('ess_cico_'.$m);
                
                if($get_assesment->num_rows()==1){
                    $this->response([
                        'status'=>true,
                        'message'=>'Success',
                        'data'=>$get_assesment->row()
                    ], MY_Controller::HTTP_OK);
                } else{
                    $this->response([
                        'status'=>false,
                        //'message'=>'Belum input self assesment tanggal '.$tanggal,
                        'message'=>'Anda belum clock in/out di tanggal '.$tanggal,
                        'data'=>$data
                    ], MY_Controller::HTTP_NOT_FOUND);
                }
            } else{
                $this->response([
                    'status'=>false,
                    'message'=>'Tanggal is required',
                    'data'=>$data
                ], MY_Controller::HTTP_BAD_REQUEST);
            }
        } catch(Exception $e){
            $this->response([
                'status'=>false,
                'message'=>'Error Exception',
                'data'=>$data
            ], MY_Controller::HTTP_BAD_REQUEST);
        }
    }
    
    function index_post(){
        $data = [];
        $data_insert = [];
        $data_cico = [];
        $np_karyawan = $this->data_karyawan->np_karyawan;
        
        try {
            if(empty($this->post('tanggal'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Tanggal harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($this->post('jam'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Tanggal harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                $tanggal = date('Y-m-d', strtotime($this->post('tanggal')));
                $get_assesment = $this->db->select('id,clock_in,clock_out')->where(['np_karyawan'=>$np_karyawan, 'tanggal'=>$tanggal])->get('ess_self_assesment_covid19');
                
                if($get_assesment->num_rows()==1){
                    $row_assesment = $get_assesment->row();
                    
                    $m = date('Y_m', strtotime($tanggal));
                    if($row_assesment->clock_in==null){
                        //$data_insert['clock_in'] = date('H:i:s');
                        $data_insert['clock_in'] = $this->post('jam');
                        $this->db->where('id',$row_assesment->id)->update('ess_self_assesment_covid19', $data_insert);

                        $data_cico['tapping_fix_1'] = $tanggal.' '.$data_insert['clock_in'];
                        $data_cico['tapping_fix_approval_status'] = '1';
                        $data_cico['tapping_fix_approval_ket'] = 'WFH MOBILE';
                        $data_cico['wfh'] = '1';
                        $data_cico['updated_at'] = date('Y-m-d H:i:s');
                        $data_cico['updated_by'] = $np_karyawan;
                        $update_cico = $this->db->where(['np_karyawan'=>$np_karyawan, 'dws_tanggal'=>$tanggal])->update('ess_cico_'.$m, $data_cico);
                        $message='';
                        if($update_cico){
                            $message .= 'Updated to Cico. ';
                        } else{
                            $message .= 'Failed to Updat to Cico. ';
                        }

                        $message .= 'Clock in at '.$data_insert['clock_in'];
                        $row_assesment->clock_in = $data_insert['clock_in'];
                    } else if($row_assesment->clock_out==null){
                        //$data_insert['clock_out'] = date('H:i:s');
                        $data_insert['clock_out'] = $this->post('jam');
                        $this->db->where('id',$row_assesment->id)->update('ess_self_assesment_covid19', $data_insert);

                        $data_cico['tapping_fix_2'] = $tanggal.' '.$data_insert['clock_out'];
                        $data_cico['tapping_fix_approval_status'] = '1';
                        $data_cico['tapping_fix_approval_ket'] = 'WFH MOBILE';
                        $data_cico['wfh'] = '1';
                        $data_cico['updated_at'] = date('Y-m-d H:i:s');
                        $data_cico['updated_by'] = $np_karyawan;
                        $update_cico = $this->db->where(['np_karyawan'=>$np_karyawan, 'dws_tanggal'=>$tanggal])->update('ess_cico_'.$m, $data_cico);
                        $message='';
                        if($update_cico){
                            $message .= 'Updated to Cico. ';
                        } else{
                            $message .= 'Failed to Updat to Cico. ';
                        }

                        $message .= 'Clock out at '.$data_insert['clock_out'];
                        $row_assesment->clock_out = $data_insert['clock_out'];
                    } else{
                        $this->response([
                            'status'=>false,
                            'message'=>'Terminated',
                            'data'=>$data
                        ], MY_Controller::HTTP_BAD_REQUEST); exit;
                    }
                    
                    $this->response([
                        'status'=>true,
                        'message'=>$message,
                        'data'=>$get_assesment->row()
                    ], MY_Controller::HTTP_OK);
                } else{
                    $this->response([
                        'status'=>false,
                        'message'=>'Data tanggal '.$tanggal.' balum ada',
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
