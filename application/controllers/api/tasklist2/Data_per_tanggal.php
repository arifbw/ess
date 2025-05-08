<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Data_per_tanggal extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->helper("tanggal_helper");
        $this->load->model('api/tasklist2/M_tasklist_api','tasklist');
    }
    
    function index_get(){
        $data = [];
        try {
            if(empty($this->get('tanggal'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Tanggal harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                
                $params['tanggal'] = $this->get('tanggal');
                $params['np'] = $this->data_karyawan->np_karyawan;
                
                $no=0;
                $get_tasklist = $this->tasklist->get_task_by_date($params)->result_array();
                foreach($get_tasklist as $tampil){
                    $row=$tampil;
                    $no++;
                    $row['no'] = $no;
                    $row['dapat_dicek'] = date('Y-m-d')==$tampil['created_at'] ? true:false;
                    $row['url_gambar'] = is_file('./'.$tampil['evidence']) ? base_url($tampil['evidence']):null;
                    
                    $data[]=$row;
                }
                
                $this->response([
                    'status'=>true,
                    'message'=>'Data Tasklist tanggal '.$this->get('tanggal'),
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
