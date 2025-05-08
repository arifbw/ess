<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Data extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->model('api/tasklist/M_tasklist_api','tasklist');
    }
    
    function index_get(){
        $data = [];
        try {
            if(!empty($this->get('id_assesment'))){
                $id = $this->get('id_assesment');
                $get_assesment = $this->tasklist->self_assesment($id);
                if($get_assesment->num_rows()==1){
                    $data['data_assesment'] = $get_assesment->row_array();
                    
                    $get_tasklist = $this->tasklist->get_tasklist($id);
                    $data['tasklist'] = $get_tasklist;
                    $this->response([
                        'status'=>true,
                        'message'=>'Success',
                        'data'=>$data
                    ], MY_Controller::HTTP_OK);
                } else{
                    $this->response([
                        'status'=>false,
                        'message'=>'ID assesment not found',
                        'data'=>$data
                    ], MY_Controller::HTTP_NOT_FOUND);
                }
            } else{
                $this->response([
                    'status'=>false,
                    'message'=>'ID assesment is required',
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
        $created_by = $this->data_karyawan->np_karyawan;
        
        try {
            if(empty($this->post('id_assesment'))){
                $this->response([
                    'status'=>false,
                    'message'=>"ID assesment harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($this->post('target_pekerjaan'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Target pekerjaan harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                
                $data_insert['kode'] = $this->uuid->v4();
                $data_insert['id_assesment'] = $this->post('id_assesment');
                $data_insert['target_pekerjaan'] = $this->post('target_pekerjaan');
                $data_insert['created_at'] = date('Y-m-d H:i:s');
                $data_insert['created_by'] = $created_by;
                
                $this->db->insert('ess_performance_management', $data_insert);

                if($this->db->affected_rows()>0){
                    $this->response([
                        'status'=>true,
                        'message'=>'Berhasil disimpan',
                        'data'=>$data_insert
                    ], MY_Controller::HTTP_OK);
                } else{
                    $this->response([
                        'status'=>false,
                        'message'=>'Gagal menambahkan list baru',
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
