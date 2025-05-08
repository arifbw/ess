<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Tasklist extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->helper('dev_tasklist');
        $this->load->model('api/projects/M_tasklist_only');
    }
    
    function index_get(){
        $data = [];
        try {
            # get projects
            $get = $this->db
                ->group_start()
                    ->where('np',$this->data_karyawan->np_karyawan)
                    ->or_where('created_by_np',$this->data_karyawan->np_karyawan)
                ->group_end()
                ->where('task_type','task')
                ->group_start()
                    ->where('DATE("'.date('Y-m-d').'") BETWEEN start_date AND end_date')
                    ->or_where('progress<',100)
                    ->or_where('progress is null',null,false)
                ->group_end()
                ->where( 'deleted_at is null',null,false )
                ->get('ess_project_tasklists')->result_array();
            
            foreach($get as $field){
                $row = $field;
                $this->M_tasklist_only->reset_data();
                $this->M_tasklist_only->get_parent(['parent_id'=>$field['parent_id'], 'project_id'=>$field['project_id']]);
                $row['parents'] = $this->M_tasklist_only->get_data();
                $data[] = $row;
            }
            
            $this->response([
                'status'=>true,
                'message'=>'Data Activity',
                'data'=>$data
            ], MY_Controller::HTTP_OK);
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
        $np_login = $this->data_karyawan->np_karyawan;
        $table_name = 'ess_project_tasklists';
        try {
            if(empty($this->post('task_name'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Nama Tasklist/Activity harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($this->post('task_type'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Task type harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                
                # validasi task_type
                if( !in_array($this->post('task_type'),['dir','task']) ){
                    $this->response([
                        'status'=>false,
                        'message'=>'Task type harus bernilai "dir"/"task" ',
                        'data'=>$data
                    ], MY_Controller::HTTP_BAD_REQUEST);
                }
                # END validasi task_type
                
                $data_insert['kode'] = $this->uuid->v4();
                $data_insert['np'] = $this->data_karyawan->np_karyawan;
                $data_insert['nama'] = $this->data_karyawan->nama;
                
                $parent_id = @$this->post('parent_id') ? $this->post('parent_id'):null;
                $data_insert['parent_id'] = $parent_id;
                
                if( @$this->post('project_id') ){
                    $data_insert['project_id'] = $this->post('project_id');
                    
                    # check project detail
                    $check_project = $this->db->select('id, project_name, created_by_np')->where('id',$this->post('project_id') )->get('ess_projects');
                    if( $check_project->num_rows()==1 ){
                        $row_project = $check_project->row();
                        if( $this->post('task_type')=='dir' ){
                            if( $row_project->created_by_np==$np_login ){ # project jika dibuat oleh dirinya
                                # cek apakah sudah ada task
                                $get_task = $this->db->where( 'task_type','task' )->where( 'parent_id',$parent_id )->get($table_name);
                                if($get_task->num_rows()!=0){ # jika ada task maka tolak
                                    $this->response([
                                        'status'=>false,
                                        'message'=>'Anda tidak bisa menambahkan Tasklist',
                                        'data'=>$data
                                    ], MY_Controller::HTTP_BAD_REQUEST);
                                }
                                
                                $data_insert['np_atasan'] = @$this->post('np_atasan')?$this->post('np_atasan'):null;
                                $data_insert['nama_atasan'] = @$this->post('nama_atasan')?$this->post('nama_atasan'):null;
                            } else{
                                $this->response([ # jika bukan maka tidak bisa create sub
                                    'status'=>false,
                                    'message'=>'Anda tidak bisa menambahkan Tasklist',
                                    'data'=>$data
                                ], MY_Controller::HTTP_BAD_REQUEST);
                            }
                        } else{ # jika input berupa task
                            # cek apakah sudah ada dir
                            $get_task = $this->db->where( 'task_type','dir' )->where( 'parent_id',$parent_id )->get($table_name);
                            if($get_task->num_rows()!=0){ # jika ada dir maka tolak
                                $this->response([
                                    'status'=>false,
                                    'message'=>'Anda tidak bisa menambahkan Activity',
                                    'data'=>$data
                                ], MY_Controller::HTTP_BAD_REQUEST);
                            } else if( empty($this->post('np_atasan')) ){
                                $this->response([
                                    'status'=>false,
                                    'message'=>'NP Atasan harus diisi',
                                    'data'=>$data
                                ], MY_Controller::HTTP_BAD_REQUEST);
                            } else if( empty($this->post('nama_atasan')) ){
                                $this->response([
                                    'status'=>false,
                                    'message'=>'Nama Atasan harus diisi',
                                    'data'=>$data
                                ], MY_Controller::HTTP_BAD_REQUEST);
                            } else{
                                $data_insert['np_atasan'] = $this->post('np_atasan');
                                $data_insert['nama_atasan'] = $this->post('nama_atasan');
                            }
                        }
                    } else{
                        $this->response([
                            'status'=>false,
                            'message'=>'Project tidak ditemukan',
                            'data'=>$data
                        ], MY_Controller::HTTP_BAD_REQUEST);
                    }
                } else{
                    if( $this->post('task_type')=='dir' ){
                        $this->response([
                            'status'=>false,
                            'message'=>'Tidak bisa menambahkan Tasklist di Daily Project',
                            'data'=>$data
                        ], MY_Controller::HTTP_BAD_REQUEST);
                    } else{
                        $data_insert['project_id'] = 1; # 1: kode default utk Daily Project
                        if( empty($this->post('np_atasan')) ){
                            $this->response([
                                'status'=>false,
                                'message'=>'NP Atasan harus diisi',
                                'data'=>$data
                            ], MY_Controller::HTTP_BAD_REQUEST);
                        } else if( empty($this->post('nama_atasan')) ){
                            $this->response([
                                'status'=>false,
                                'message'=>'Nama Atasan harus diisi',
                                'data'=>$data
                            ], MY_Controller::HTTP_BAD_REQUEST);
                        } else{
                            $data_insert['np_atasan'] = $this->post('np_atasan');
                            $data_insert['nama_atasan'] = $this->post('nama_atasan');
                        }
                    }
                }
                
                $data_insert['task_name'] = $this->post('task_name');
                $data_insert['task_type'] = $this->post('task_type');
                $data_insert['start_date'] = date('Y-m-d');
                $data_insert['end_date'] = date('Y-m-d');
                
                $data_insert['created_at'] = date('Y-m-d H:i:s');
                $data_insert['created_by_np'] = $this->data_karyawan->np_karyawan;
                $data_insert['created_by_nama'] = $this->data_karyawan->nama;
                
                $this->db->insert($table_name, $data_insert);

                if($this->db->affected_rows()>0){
                    $this->response([
                        'status'=>true,
                        'message'=>'Berhasil disimpan',
                        'data'=>$data_insert
                    ], MY_Controller::HTTP_OK);
                } else{
                    $this->response([
                        'status'=>false,
                        'message'=>'Gagal menambahkan '.($this->post('task_type')=='dir'?'Tasklist':'Activity').' baru',
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