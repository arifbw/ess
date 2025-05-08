<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Hasil extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->model('api/tasklist/M_tasklist_api','tasklist');
    }
    
    function index_put(){
        $data_insert = [];
        try {
            if(empty($this->put('id'))){
                $this->response([
                    'status'=>false,
                    'message'=>'ID harus diisi'
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($this->put('hasil_pekerjaan'))){
                $this->response([
                    'status'=>false,
                    'message'=>'Hasil pekerjaan harus diisi'
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                $id = $this->put('id');
                $hasil_pekerjaan = $this->put('hasil_pekerjaan');
                
                $data_insert['hasil_pekerjaan'] = $hasil_pekerjaan;
                $data_insert['updated_at'] = date('Y-m-d H:i:s');
                $data_insert['checked'] = '1';
                $data_insert['checked_at'] = date('Y-m-d H:i:s');
                
                $this->db->where('id',$id)->update('ess_performance_management',$data_insert);
                if($this->db->affected_rows()>0){
                    $this->response([
                        'status'=>true,
                        'message'=>'Hasil Updated'
                    ], MY_Controller::HTTP_OK);
                } else{
                    $this->response([
                        'status'=>false,
                        'message'=>'Failed to update'
                    ], MY_Controller::HTTP_BAD_REQUEST);
                }
            }
        } catch(Exception $e){
            $this->response([
                'status'=>false,
                'message'=>'Error Exception'
            ], MY_Controller::HTTP_BAD_REQUEST);
        }
    }
}
