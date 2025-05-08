<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Get_by_project extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        $this->current_table = 'ess_project_x_milestones';
    }
    
    function index_get(){
        $data = [];
        try {
            if(empty($this->get('id'))){
                $this->response([
                    'status'=>false,
                    'message'=>"ID Project harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                # get dir
                $get = $this->db->where( 'id',$this->get('id') )->get( 'ess_projects' );
                if( $get->num_rows()==1 ){
                    $row_get = $get->row();
                    
                    $milestones = $this->db->where('project_id',$this->get('id'))->where('deleted_at is null',null,false)->order_by('order_number')->get($this->current_table)->result();
                    
                    $this->response([
                        'status'=>true,
                        'message'=>'Milestones',
                        'project'=>$row_get->project_name,
                        'data'=>$milestones
                    ], MY_Controller::HTTP_OK);
                } else{
                    $this->response([
                        'status'=>false,
                        'message'=>'Project Tidak ditemukan',
                        'project'=>'',
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
