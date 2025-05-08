<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Pembatalan extends MY_Controller {
    
    function __construct(){
        parent::__construct();
    }
    
    function index_post(){
        $data = [];
        $data_insert = [];
        
        try {
            $this->response([
                'status'=>false,
                'message'=>"Akses ditutup",
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
            
            /*if(empty($this->post('id'))){
                $this->response([
                    'status'=>false,
                    'message'=>"ID harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($this->post('tanggal'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Tanggal harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                $date = $this->post('tanggal');
                $data_insert['pengguna_status'] = '3';
                $data_insert['pengguna_updated_at'] = date('Y-m-d H:i:s');
                
                # cek exist
                $cek_izin = $this->db->where(['id'=>$this->post('id')])->get('ess_perizinan_'.date('Y_m', strtotime($date)));
                
                if($cek_izin->num_rows()>0){
                    # update mjd pengguna_status=3
                    $this->db->where(['id'=>$this->post('id')])->update('ess_perizinan_'.date('Y_m', strtotime($date)), $data_insert);
                    
                    if($this->db->affected_rows()>0){
                        
                        # remove from cico
                        $row_izin = $cek_izin->row();
                        
                        $this->response([
                            'status'=>true,
                            'message'=>'Izin telah dibatalkan',
                            'data'=>'Updated: '.$data_insert['pengguna_updated_at']
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
                        'message'=>'Data perizinan tidak ditemukan.',
                        'data'=>[]
                    ], MY_Controller::HTTP_NOT_FOUND);
                }
            }*/
        } catch(Exception $e){
            $this->response([
                'status'=>false,
                'message'=>'Error Exception',
                'data'=>$data
            ], MY_Controller::HTTP_BAD_REQUEST);
        }
    }
}
