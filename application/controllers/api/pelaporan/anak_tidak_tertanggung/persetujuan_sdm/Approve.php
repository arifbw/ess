<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Approve extends MY_Controller {
    
    function __construct(){
        parent::__construct();
    }
    
    function index_post(){
        if(empty($this->post('id'))){ 
            $this->response([
                'status'=>false,
                'message'=>"Id harus diisi",
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
        } else if(empty($this->post('approval_status'))){ # untuk approval atasan valuenya 5 atau 6
            $this->response([
                'status'=>false,
                'message'=>"Persetujuan harus diisi",
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
        } else{
            
            if( $this->post('approval_status')=='6' ){
                if(empty($this->post('alasan'))){
                    $this->response([
                        'status'=>false,
                        'message'=>"Penolakan harus disertai alasan",
                        'data'=>[]
                    ], MY_Controller::HTTP_BAD_REQUEST);
                }
            }

            $data_insert = [
                'approval_status'=>$this->post('approval_status'),
                'sdm_submit_alasan'=>($this->post('approval_status')=='5' ? null : $this->post('alasan')),
                'sdm_submit_date'=>date('Y-m-d H:i:s'),
                'sdm_submit_np'=>$this->data_karyawan->np_karyawan
            ];
    
            $this->db->where('id', $this->post('id'))->update('ess_laporan_anak_tidak_tertanggung',$data_insert);

            $this->response([
                'status'=>true,
                'message'=>"Berhasil melakukan persetujuan",
                'data'=>$data_insert
            ], MY_Controller::HTTP_OK);
        }
        
    }
}
