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
                $m = date('Y_m', strtotime($tanggal));
                $table_name = "ess_cico_$m";
                
                # cek table
                $get_table = $this->db->query('SELECT count(*) as count_table FROM information_schema.tables WHERE table_schema = "'.$this->db->database.'" AND table_name = "'.$table_name.'"')->row();
                
                if($get_table->count_table > 0){
                    
                    # cek dws
                    $get_dws = $this->db->select('id,np_karyawan,nama,kode_unit,nama_unit,tapping_fix_1,tapping_fix_2,tapping_fix_approval_status,tapping_fix_approval_ket')->where(['np_karyawan'=>$np_karyawan, 'dws_tanggal'=>$tanggal])->get($table_name);
                    if($get_dws->num_rows()==1){
                        $row_dws = $get_dws->row();
                        
                        if($row_dws->tapping_fix_1==null){
                            $data_cico['tapping_fix_1'] = $tanggal.' '.$this->post('jam');
                            $data_cico['tapping_fix_approval_status'] = '1';
                            $data_cico['tapping_fix_approval_ket'] = 'WFH MOBILE';
                            $data_cico['wfh'] = '1';
                            $data_cico['updated_at'] = date('Y-m-d H:i:s');
                            $data_cico['updated_by'] = $np_karyawan;
                            $update_cico = $this->db->where(['np_karyawan'=>$np_karyawan, 'dws_tanggal'=>$tanggal])->update($table_name, $data_cico);
                            $message='';
                            if($update_cico){
                                $message .= 'Updated to Cico. ';
                            } else{
                                $message .= 'Failed to Updat to Cico. ';
                            }

                            $message .= 'Clock in at '.$this->post('jam');
                            $row_dws->tapping_fix_1 = $tanggal.' '.$this->post('jam');
                            
                        } else if($row_dws->tapping_fix_2==null){
                            $data_cico['tapping_fix_2'] = $tanggal.' '.$this->post('jam');
                            $data_cico['tapping_fix_approval_status'] = '1';
                            $data_cico['tapping_fix_approval_ket'] = 'WFH MOBILE';
                            $data_cico['wfh'] = '1';
                            $data_cico['updated_at'] = date('Y-m-d H:i:s');
                            $data_cico['updated_by'] = $np_karyawan;
                            $update_cico = $this->db->where(['np_karyawan'=>$np_karyawan, 'dws_tanggal'=>$tanggal])->update($table_name, $data_cico);
                            $message='';
                            if($update_cico){
                                $message .= 'Updated to Cico. ';
                            } else{
                                $message .= 'Failed to Updat to Cico. ';
                            }

                            $message .= 'Clock out at '.$this->post('jam');
                            $row_dws->tapping_fix_2 = $tanggal.' '.$this->post('jam');
                            
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
                            'data'=>$row_dws
                        ], MY_Controller::HTTP_OK);
                        
                    } else{
                        $this->response([
                            'status'=>false,
                            'message'=>'Data tanggal '.$tanggal.' belum tersedia',
                            'data'=>$data
                        ], MY_Controller::HTTP_BAD_REQUEST);
                    }
                } else{
                    $this->response([
                        'status'=>false,
                        'message'=>'Data belum tersedia',
                        'data'=>$data
                    ], MY_Controller::HTTP_BAD_REQUEST);
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
