<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Pembatalan extends MY_Controller {
    
    function __construct(){
        parent::__construct();
    }
    
    function index_post(){
        $data = [];
        $data_insert = [];
        
        try {
            if(empty($this->post('id'))){
                $this->response([
                    'status'=>false,
                    'message'=>"ID harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                $data_insert['status_1'] = '3';
                $data_insert['status_2'] = '3';
                $data_insert['approval_1_date'] = date('Y-m-d H:i:s');
                $data_insert['approval_2_date'] = date('Y-m-d H:i:s');
                $data_insert['updated_at'] = date('Y-m-d H:i:s');
                //$data_insert['updated_by'] = $this->data_karyawan->np_karyawan;
                
                # cek exist
                $cek_cuti = $this->db->where(['id'=>$this->post('id')])->get('ess_cuti');
                
                if($cek_cuti->num_rows()>0){
                    # update mjd pengguna_status=3
                    $this->db->where(['id'=>$this->post('id')])->update('ess_cuti', $data_insert);
                    
                    if($this->db->affected_rows()>0){
                        
                        # remove from cico
                        $row_cuti = $cek_cuti->row();
                        
                        $this->response([
                            'status'=>true,
                            'message'=>'Cuti telah dibatalkan',
                            'data'=>'Updated: '.$data_insert['updated_at']
                        ], MY_Controller::HTTP_OK);
                    } else{
                        $this->response([
                            'status'=>false,
                            'message'=>'Tidak dapat mengupdate ke database.',
                            'data'=>[]
                        ], MY_Controller::HTTP_INTERNAL_SERVER_ERROR);
                    }
                } else{
                    $this->response([
                        'status'=>false,
                        'message'=>'Data cuti tidak ditemukan.',
                        'data'=>[]
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
