<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Edit_project extends MY_Controller {
    
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
                    if( $row_project['created_by_np']==$this->data_karyawan->np_karyawan ){
                        $this->response([
                            'status'=>true,
                            'message'=>'Detail project',
                            'data'=>$row_project
                        ], MY_Controller::HTTP_OK);
                    } else{
                        $this->response([
                            'status'=>false,
                            'message'=>'Anda tidak bisa mengganti nama Project.',
                            'data'=>$data
                        ], MY_Controller::HTTP_BAD_REQUEST);
                    }
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
    
    function index_post(){
        $data = [];
        $data_insert = [];
        
        try {
            if(empty($this->post('id'))){
                $this->response([
                    'status'=>false,
                    'message'=>"ID project harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($this->post('project_name'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Nama project harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if( $this->post('id')==1 ){
                $this->response([
                    'status'=>false,
                    'message'=>"Daily project tidak bisa diganti nama",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                
                $data_insert['project_name'] = $this->post('project_name');
                $data_insert['updated_at'] = date('Y-m-d H:i:s');
                
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
