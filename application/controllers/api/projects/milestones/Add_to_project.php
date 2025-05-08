<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Add_to_project extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        $this->current_table = 'ess_project_x_milestones';
    }
    
    function index_post(){
        $data = [];
        try {
            if(empty($this->post('project_id'))){
                $this->response([
                    'status'=>false,
                    'message'=>"ID Project harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($this->post('milestone'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Nama Milestone harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                # get project
                $get = $this->db->where( 'id',$this->post('project_id') )->where( 'project_type_id',1 )->get( 'ess_projects' );
                if( $get->num_rows()==1 ){
                    $row_get = $get->row();
                    
                    $last_milestone_number = $this->db->select('order_number')
                        ->where('project_id',$this->post('project_id'))
                        ->order_by('order_number','DESC')
                        ->limit(1)
                        ->get($this->current_table)
                        ->row();
                    
                    if(@$last_milestone_number->order_number!=null)
                        $last_number = $last_milestone_number->order_number;
                    else
                        $last_number = 0;
                    
                    $data_insert = [];
                    $data_insert['kode'] = $this->uuid->v4();
                    $data_insert['project_id'] = $this->post('project_id');
                    $data_insert['nama'] = $this->post('milestone');
                    $data_insert['order_number'] = ($last_number+1);
                    $data_insert['created_at'] = date('Y-m-d H:i:s');
                    $this->db->insert($this->current_table, $data_insert);
                    
                    $this->response([
                        'status'=>true,
                        'message'=>'Milestone Baru',
                        'project'=>$row_get->project_name,
                        'data'=>$data_insert
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
