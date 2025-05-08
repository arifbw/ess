<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Detail_project extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        $this->current_table = 'ess_projects';
    }
    
    function index_get(){
        $data = [];
        try {
            if(empty($this->get('id'))){
                $this->response([
                    'status'=>false,
                    'message'=>"ID project harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                # get project
                $project = $this->db->where( 'id',$this->get('id') )->get( $this->current_table );
                if( $project->num_rows()==1 ){
                    $row_project = $project->row_array();
                    $row_project['all_partisipants'] = $this->db->select('np,nama,jabatan,kode_unit,nama_unit,is_pic,is_pm,is_viewer')->where('project_id',$row_project['id'])->where('deleted_at is null',null,false)->get('ess_project_members')->result_array();
                    $this->response([
                        'status'=>true,
                        'message'=>'Detail project',
                        'data'=>$row_project
                    ], MY_Controller::HTTP_OK);
                } else{
                    $this->response([
                        'status'=>false,
                        'message'=>'Project tidak ditemukan',
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
