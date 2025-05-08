<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Edit_project extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        $this->current_table = 'ess_projects';
    }
    
    /*function index_get(){
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
                    $row_project = $project->row();
                    if( $row_project->created_by_np==$this->data_karyawan->np_karyawan ){
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
    }*/
    
    function index_post(){
        $data = [];
        $data_insert = [];
        
        try {
            if(empty($this->post('id'))){
                $this->response([
                    'status'=>false,
                    'message'=>"ID Project harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($this->post('project_name'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Nama Project harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($this->post('project_type_id'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Jenis Project harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                # START: collect data
                $data_insert['project_name'] = $this->post('project_name');
                $data_insert['start_date'] = @$this->post('start_date') ? $this->post('start_date') : date('Y-m-d');
                $data_insert['end_date'] = @$this->post('end_date') ? $this->post('end_date') : date('Y-m-d');
                
                if(@$this->post('description'))
                    $data_insert['description'] = $this->post('description');
                
                $data_insert['updated_at'] = date('Y-m-d H:i:s');
                $data_insert['updated_by_np'] = $this->data_karyawan->np_karyawan;
                $data_insert['updated_by_nama'] = $this->data_karyawan->nama;
                $data_insert['updated_by_jabatan'] = $this->data_karyawan->nama_jabatan_singkat;
                $data_insert['updated_by_kode_unit'] = $this->data_karyawan->kode_unit;
                
                $data_insert['project_type_id'] = $this->post('project_type_id');
                
                if( @$this->post('project_milestone_id') ){ # sementara masih required ambil dari dropdown
                    $data_insert['project_milestone_id'] = $this->post('project_milestone_id');
                    if( empty($this->post('project_milestone_name')) ){
                        $this->response([
                            'status'=>false,
                            'message'=>"Nama Milestone harus diisi",
                            'data'=>[]
                        ], MY_Controller::HTTP_BAD_REQUEST);
                    }
                    $data_insert['project_milestone_name'] = $this->post('project_milestone_name');
                }
                
                if(@$this->post('category_id'))
                    $data_insert['category_id'] = $this->post('category_id');
                # END: collect data
                
                # update
                $this->db->where( 'id',$this->post('id') )->update( $this->current_table, $data_insert );

                if($this->db->affected_rows()>0){
                    $this->response([
                        'status'=>true,
                        'message'=>'Update telah disimpan',
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
