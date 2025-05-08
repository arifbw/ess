<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Get_subproject extends MY_Controller {
    
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
                # get dir
                $get = $this->db->where( 'id',$this->get('id') )->get( 'ess_projects' );
                if( $get->num_rows()==1 ){
                    $row_get = $get->row();
                    
                    # jika ada parent
                    if( @$this->get('parent_id')!=null )
                        $get_sub = $this->db->where( 'parent_id',$this->get('parent_id') )->where( 'task_type','dir' )->where( 'deleted_at is null',null,false )->get($this->current_table)->result();
                    else
                        $get_sub = $this->db->where( 'project_id',$this->get('id') )->where( 'task_type','dir' )->where( 'parent_id is null',null,false )->where( 'deleted_at is null',null,false )->get($this->current_table)->result();
                    
                    $this->response([
                        'status'=>true,
                        'message'=>'Tasklists',
                        'data'=>$get_sub
                    ], MY_Controller::HTTP_OK);
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
