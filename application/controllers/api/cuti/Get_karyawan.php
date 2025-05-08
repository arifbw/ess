<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Get_karyawan extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->model("master_data/m_karyawan");
        $this->load->model("api/filter/M_filter_api","filter");
        $this->load->helper("karyawan_helper");
    }
    
    function index_post(){
        $data = [];
        
        try {
            if(empty($this->post('np'))){
                $this->response([
                    'status'=>false,
                    'message'=>"NP harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                $np_karyawan = $this->post('np');      
                $data = mst_karyawan_by_np($np_karyawan);
                
                if ($data) {
                    $return = [
                        'status'=>true,
                        'data'=>[
                            'nama'=>$data['nama'],
                            'jabatan'=>$data['nama_jabatan']
                        ]
                    ];
                }else{              
                    $return = [
                        'status'=>false,
                        'data'=>[
                            'nama'=>'',
                            'jabatan'=>''
                        ]
                    ];
                }   
                // echo json_encode($return);
                // echo 'a';
                $this->response($return, MY_Controller::HTTP_OK);
            }
        } catch(Exception $e){
            $this->response([
                'status'=>false,
                'message'=>'Error Exception',
                'data'=>$data
            ], MY_Controller::HTTP_BAD_REQUEST);
        }
    }

    function by_np(){
        $data = [];
        
        try {
            if(empty($this->post('np'))){
                $this->response([
                    'status'=>false,
                    'message'=>"NP harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                $np_karyawan = $this->post('np');      
                $data = mst_karyawan_by_np($np_karyawan);
                
                if ($data) {
                    $return = [
                        'status'=>true,
                        'data'=>[
                            'nama'=>$data['nama'],
                            'jabatan'=>$data['nama_jabatan']
                        ]
                    ];
                }else{              
                    $return = [
                        'status'=>false,
                        'data'=>[
                            'nama'=>'',
                            'jabatan'=>''
                        ]
                    ];
                }   
                // echo json_encode($return);
                // echo 'a';
                $this->response($return, MY_Controller::HTTP_OK);
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
