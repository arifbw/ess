<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Edit_detail extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        $this->current_table = 'ess_project_tasklists';
    }
    
    function index_get(){
        $data = [];
        try {
            if(empty($this->get('id'))){
                $this->response([
                    'status'=>false,
                    'message'=>"ID harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                # get task/dir
                $get = $this->db->where( 'id',$this->get('id') )->get( $this->current_table );
                if( $get->num_rows()==1 ){
                    $row_get = $get->row();
                    if( $row_get->created_by_np==$this->data_karyawan->np_karyawan ){
                        $this->response([
                            'status'=>true,
                            'message'=>'Detail',
                            'data'=>$row_get
                        ], MY_Controller::HTTP_OK);
                    } else{
                        $this->response([
                            'status'=>false,
                            'message'=>'Anda tidak bisa mengganti nama Tasklist/Subtask.',
                            'data'=>$data
                        ], MY_Controller::HTTP_BAD_REQUEST);
                    }
                } else{
                    $this->response([
                        'status'=>false,
                        'message'=>'Tidak ditemukan',
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
            } else if(empty($this->post('task_name'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Nama Tasklist/Subtask harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                
                $get_detail = $this->db->where( 'id',$this->post('id') )->get( $this->current_table );
                if($get_detail->num_rows()==1){
                    $row_detail = $get_detail->row();
                    $data_insert['task_name'] = $this->post('task_name');
                    $data_insert['updated_at'] = date('Y-m-d H:i:s');
                    
                    if( !empty($this->post('progress')) ){
                        $data_insert['progress'] = $this->post('progress');
                        if( $this->post('progress')<100 && date('Y-m-d', strtotime($row_detail->end_date))<=date('Y-m-d') ){
                            $data_insert['end_date'] = date('Y-m-d', strtotime("+1 day"));
                        }
                    }
                    
                    $this->db->where( 'id',$this->post('id') )->update( $this->current_table, $data_insert );
                    
                    if($this->db->affected_rows()>0){
                        $this->response([
                            'status'=>true,
                            'message'=>'Berhasil disimpan',
                            'data'=>$data_insert
                        ], MY_Controller::HTTP_OK);
                    } else{
                        $this->response([
                            'status'=>false,
                            'message'=>'Gagal',
                            'data'=>$data
                        ], MY_Controller::HTTP_BAD_REQUEST);
                    }
                } else{
                    $this->response([
                        'status'=>false,
                        'message'=>'Tidak ditemukan',
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
