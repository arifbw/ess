<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Tasklist extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        //$this->load->helper('dev_tasklist');
        $this->load->model('api/projects/M_tasklist_only');
    }
    
    function index_get(){
        $data = [];
        try {
            $np = $this->data_karyawan->np_karyawan;
            if(!empty($this->get('np'))){
                $np = $this->get('np');
            }
            # get projects
            $get = $this->db->select('a.id,a.kode,a.project_id,a.task_name,a.task_type,a.parent_id,a.description,a.start_date,(CASE WHEN a.end_date_fix IS NOT NULL THEN a.end_date_fix ELSE a.end_date END) as end_date,a.progress,a.note,a.uploaded_at,a.evidence,a.created_at,a.created_by_np,a.created_by_nama,(SELECT COUNT(kode) FROM ess_project_tasklist_members WHERE tasklist_id=a.id AND np="'.$np.'" ) as as_member,a.updated_at')
                ->where('a.task_type','task')
                ->group_start()
                    ->where("(DATE('".date('Y-m-d')."') BETWEEN a.start_date AND (CASE WHEN a.end_date_fix IS NOT NULL THEN a.end_date_fix ELSE a.end_date END))")
                    ->or_where('a.progress<',100)
                    ->or_where('a.progress is null',null,false)
                    ->or_where('DATE(a.updated_at)',date('Y-m-d'))
                ->group_end()
                ->where( 'a.deleted_at is null',null,false )
                ->order_by('a.task_name')
                ->get('ess_project_tasklists a');
            
            foreach($get->result_array() as $field){
                if($field['as_member']>0){
                    $row = $field;
                    $this->M_tasklist_only->reset_data();
                    $this->M_tasklist_only->get_parent(['parent_id'=>$field['parent_id'], 'project_id'=>$field['project_id']]);
                    $row['parents'] = $this->M_tasklist_only->get_data();
                    $data[] = $row;
                }
            }
            
            $this->response([
                'status'=>true,
                'message'=>'Data Activity',
                'rows'=>$get->num_rows(),
                'data'=>$data,
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
            if(empty($this->post('project_id'))){
                $this->response([
                    'status'=>false,
                    'message'=>"ID Project harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($this->post('task_name'))){
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
                
                # START: collect data
                $data_insert['kode'] = $this->uuid->v4();
                $data_insert['project_id'] = $this->post('project_id');
                
                $parent_id = @$this->post('parent_id') ? $this->post('parent_id'):null;
                $data_insert['parent_id'] = $parent_id;
                
                if(@$this->post('description'))
                    $data_insert['description'] = $this->post('description');

                # check project detail
                $check_project = $this->db->select('id, project_name, created_by_np')->where('id',$this->post('project_id') )->get('ess_projects');
                if( $check_project->num_rows()==1 ){
                    $row_project = $check_project->row();
                    
                    if( $this->post('task_type')=='dir' ){
                        # cek apakah bagian dari member
                        if($parent_id!=null)
                            $is_member = $this->db->where( ['tasklist_id'=>$parent_id, 'np'=>$np_login] )->where('deleted_at is null',null,false)->get('ess_project_tasklist_members');
                        else
                            $is_member = $this->db->where( ['project_id'=>$this->post('project_id'), 'np'=>$np_login] )->where('deleted_at is null',null,false)->get('ess_project_members');
                        
                        if($is_member->num_rows()!=1 && $row_project->created_by_np!=$this->data_karyawan->np_karyawan){
                            $this->response([
                                'status'=>false,
                                'message'=>'Tidak bisa membuat tasklist. Anda harus menjadi Member atau Pembuat Project',
                                'data'=>[]
                            ], MY_Controller::HTTP_BAD_REQUEST);
                        } else{
                            # cek apakah sudah ada activity
                            $get_task = $this->db->where( 'task_type','task' )->where( 'parent_id',$parent_id )->get($table_name);
                            if($get_task->num_rows()!=0){ # jika ada activity maka tolak
                                $this->response([
                                    'status'=>false,
                                    'message'=>'Anda tidak bisa menambahkan Tasklist',
                                    'data'=>[]
                                ], MY_Controller::HTTP_BAD_REQUEST);
                            } else{
                                # ambil member
                                if($parent_id!=null)
                                    $ambil_member = $this->db->select('np,nama,jabatan,kode_unit,nama_unit')->where( ['tasklist_id'=>$parent_id, 'is_additional_member'=>'0'] )->where('deleted_at is null',null,false)->get('ess_project_tasklist_members');
                                else
                                    $ambil_member = $this->db->select('np,nama,jabatan,kode_unit,nama_unit')->where( ['project_id'=>$this->post('project_id'), 'is_viewer'=>'0'] )->where('deleted_at is null',null,false)->get('ess_project_members');
                                
                                # ditambahkan karna milesote ada di tasklist
                                if(@$this->post('milestone_kode'))
                                    $data_insert['milestone_kode'] = $this->post('milestone_kode');
                            }
                        }
                    } else{ # jika input berupa activity
                        
                        # cek apakah sudah ada dir
                        $get_task = $this->db->where( 'task_type','dir' )->where( 'parent_id',$parent_id )->get($table_name);
                        if($get_task->num_rows()!=0){ # jika ada dir maka tolak
                            $this->response([
                                'status'=>false,
                                'message'=>'Anda tidak bisa menambahkan Activity',
                                'data'=>[]
                            ], MY_Controller::HTTP_BAD_REQUEST);
                        }
                        
                        if( empty($this->post('parent_id')) ){ # tasklist is required, activity harus di dalam tasklist
                            $this->response([
                                'status'=>false,
                                'message'=>'Anda belum memilih Tasklist',
                                'data'=>[]
                            ], MY_Controller::HTTP_BAD_REQUEST);
                        }
                        
                        # cek apakah bagian dari member tasklist
                        $is_member = $this->db->where( ['tasklist_id'=>$this->post('parent_id'), 'np'=>$np_login] )->where('deleted_at is null',null,false)->get('ess_project_tasklist_members');
                        if($is_member->num_rows()!=1){
                            $this->response([
                                'status'=>false,
                                'message'=>'Anda bukan member dari Tasklist',
                                'data'=>[]
                            ], MY_Controller::HTTP_BAD_REQUEST);
                        }
                        
                        $ambil_member = $this->db->select('np,nama,jabatan,kode_unit,nama_unit')->where( ['tasklist_id'=>$parent_id, 'is_additional_member'=>'0'] )->where('deleted_at is null',null,false)->get('ess_project_tasklist_members');
                    }
                } else{
                    $this->response([
                        'status'=>false,
                        'message'=>'Project tidak ditemukan',
                        'data'=>$data
                    ], MY_Controller::HTTP_BAD_REQUEST);
                }
                
                $data_insert['task_name'] = $this->post('task_name');
                $data_insert['task_type'] = $this->post('task_type');
                $data_insert['start_date'] = @$this->post('start_date') ? $this->post('start_date') : date('Y-m-d');
                $data_insert['end_date'] = @$this->post('end_date') ? $this->post('end_date') : date('Y-m-d');
                
                $data_insert['created_at'] = date('Y-m-d H:i:s');
                $data_insert['created_by_np'] = $np_login;
                $data_insert['created_by_nama'] = $this->data_karyawan->nama;
                $data_insert['created_by_jabatan'] = $this->data_karyawan->nama_jabatan_singkat;
                $data_insert['created_by_kode_unit'] = $this->data_karyawan->kode_unit;
                # END: collect data
                
                # insert
                $this->db->insert($table_name, $data_insert);

                if($this->db->affected_rows()>0){
                    $new_id = $this->db->insert_id();
                    # copy member
                    $array_member=[];
                    foreach($ambil_member->result_array() as $field){
                        $row = $field;
                        $row['kode'] = $this->uuid->v4();
                        $row['tasklist_id'] = $new_id;
                        $row['created_at'] = date('Y-m-d H:i:s');
                        $row['created_by_np'] = $np_login;
                        $row['created_by_nama'] = $this->data_karyawan->nama;
                        $row['created_by_jabatan'] = $this->data_karyawan->nama_jabatan_singkat;
                        $row['created_by_kode_unit'] = $this->data_karyawan->kode_unit;
                        $row['created_by_nama_unit'] = $this->data_karyawan->nama_unit_singkat;
                        $row['is_pic'] = ($np_login==$field['np'] ? '1':'0');
                        $array_member[] = $row;
                    }
                    $this->db->insert_batch('ess_project_tasklist_members', $array_member);
                    
                    $this->response([
                        'status'=>true,
                        'message'=>($this->post('task_type')=='dir'?'Tasklist':'Activity').' baru telah disimpan',
                        'data'=>$data_insert
                    ], MY_Controller::HTTP_OK);
                } else{
                    $this->response([
                        'status'=>false,
                        'message'=>'Gagal menambahkan '.($this->post('task_type')=='dir'?'Tasklist':'Activity').' baru',
                        'data'=>[]
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