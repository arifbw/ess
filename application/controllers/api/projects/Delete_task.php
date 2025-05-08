<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Delete_task extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        $this->current_table = 'ess_project_tasklists';
    }
    
    function index_post(){
        $data = [];
        try {
            if(empty($this->post('id'))){
                $this->response([
                    'status'=>false,
                    'message'=>"ID harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                
                $get_detail = $this->db->where( 'id',$this->post('id') )->get( $this->current_table );
                if($get_detail->num_rows()==1){
                    $row_detail = $get_detail->row();
                    
                    if( $row_detail->created_by_np!=$this->data_karyawan->np_karyawan ){ # cek apakah task dibuat oleh dirinya sendiri
                        $this->response([
                            'status'=>false,
                            'message'=>'Anda tidak bisa menghapus Subtask ini',
                            'data'=>$data
                        ], MY_Controller::HTTP_BAD_REQUEST);
                    } else if( $row_detail->task_type!='task' ){ # cek apakah type = task
                        $this->response([
                            'status'=>false,
                            'message'=>'Anda hanya bisa menghapus Subtask',
                            'data'=>$data
                        ], MY_Controller::HTTP_BAD_REQUEST);
                    }
                    
                    $this->db->where( 'id',$this->post('id') )->update( $this->current_table,['deleted_at'=>date('Y-m-d H:i:s')] );
                    
                    if($this->db->affected_rows()>0){
                        $this->response([
                            'status'=>true,
                            'message'=>'Berhasil dihapus',
                            'data'=>$data
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
