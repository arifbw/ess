<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Tasklist_bawahan extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->helper("tanggal_helper");
        $this->load->model('api/tasklist_atasan/M_tasklist_api','tasklist');
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
            } else if(empty($this->get('tanggal'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Tanggal harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                
                $params['np'] = $this->get('np');
                $params['tanggal'] = $this->get('tanggal');
                
                $no=0;
                $get_tasklist = $this->tasklist->get_task_by_date($params);
                foreach($get_tasklist as $tampil){
                    $row = $tampil;
                    $row['url_gambar'] = is_file('./'.$tampil['evidence']) ? base_url($tampil['evidence']):null;
                    $data[] = $row;
                }
                
                $this->response([
                    'status'=>true,
                    'message'=>'Data Tasklist',
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
