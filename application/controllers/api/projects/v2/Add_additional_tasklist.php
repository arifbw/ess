<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Add_additional_tasklist extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->helper('dev_tasklist');
        $this->load->model('api/M_karyawan_api');
        $this->load->model('api/projects/M_tasklist_only');
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
                if( !in_array($this->post('task_type'),['dir']) ){
                    $this->response([
                        'status'=>false,
                        'message'=>'Hanya bisa menambahkan Tasklist',
                        'data'=>$data
                    ], MY_Controller::HTTP_BAD_REQUEST);
                }
                # END validasi task_type
                
                /*$project_members = [$np_login, $this->get_atasan($np_login)];
                $this->response([
                    'status'=>true,
                    'message'=>'Error debugging...',
                    'data'=>$project_members
                ], MY_Controller::HTTP_OK);
                exit;*/
                
                # FISRT: make a project "Additional Task"
                # START make a project
                $this->db->insert('ess_projects', [
                    'kode'=>$this->uuid->v4(),
                    'project_name'=>'Additional Task',
                    'description'=>'Additional Task yg ditambahkan pada tanggal '.date('Y-m-d'),
                    'start_date'=>date('Y-m-d'),
                    'end_date'=>date('Y-m-d'),
                    'created_at'=>date('Y-m-d H:i:s'),
                    'created_by_np'=>$this->data_karyawan->np_karyawan,
                    'created_by_nama'=>$this->data_karyawan->nama,
                    'created_by_jabatan'=>$this->data_karyawan->nama_jabatan_singkat,
                    'created_by_kode_unit'=>$this->data_karyawan->kode_unit,
                    'project_type_id'=>3
                ]);
                $new_project_id = $this->db->insert_id();
                # END make a project
                
                # SECOND: insert to project_member (np login & atasan as pm)
                # START insert project_member
                $project_members = [$np_login, $this->get_atasan($np_login)];
                $index=0;
                foreach($project_members as $member){
                    $get_data_karyawan = $this->M_karyawan_api->get_profil($member);
                    $this->db->insert('ess_project_members',[
                        'kode'=>$this->uuid->v4(),
                        'project_id'=>$new_project_id,
                        'np'=>$member,
                        'nama'=>@$get_data_karyawan['nama'] ? $get_data_karyawan['nama']:null,
                        'jabatan'=>@$get_data_karyawan['nama_jabatan_singkat'] ? $get_data_karyawan['nama_jabatan_singkat']:null,
                        'kode_unit'=>@$get_data_karyawan['kode_unit'] ? $get_data_karyawan['kode_unit']:null,
                        'nama_unit'=>@$get_data_karyawan['nama_unit_singkat'] ? $get_data_karyawan['nama_unit_singkat']:null,
                        'created_at'=>date('Y-m-d H:i:s'),
                        'created_by_np'=>$this->data_karyawan->np_karyawan,
                        'created_by_nama'=>$this->data_karyawan->nama,
                        'created_by_jabatan'=>$this->data_karyawan->nama_jabatan_singkat,
                        'created_by_kode_unit'=>$this->data_karyawan->kode_unit,
                        'created_by_nama_unit'=>$this->data_karyawan->nama_unit_singkat,
                        'is_pm'=>$index==1?'1':'0'
                    ]);
                    $index++;
                }
                # END insert project_member
                
                # START: collect data
                $data_insert['kode'] = $this->uuid->v4();
                $data_insert['project_id'] = $new_project_id;
                
                $parent_id = @$this->post('parent_id') ? $this->post('parent_id'):null;
                $data_insert['parent_id'] = $parent_id;
                
                if(@$this->post('description'))
                    $data_insert['description'] = $this->post('description');

                # check project detail
                $check_project = $this->db->select('id, project_name')->where('id',$new_project_id )->get('ess_projects');
                if( $check_project->num_rows()==1 ){
                    $row_project = $check_project->row();
                    
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
                        $ambil_member = $this->db->select('np,nama,jabatan,kode_unit,nama_unit')->where( ['project_id'=>$new_project_id, 'is_viewer'=>'0'] )->where('deleted_at is null',null,false)->get('ess_project_members');
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
                $data_insert['start_date'] = date('Y-m-d');
                $data_insert['end_date'] = date('Y-m-d');
                
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
                        'message'=>'Additional Tasklist baru telah disimpan',
                        'data'=>$data_insert
                    ], MY_Controller::HTTP_OK);
                } else{
                    $this->response([
                        'status'=>false,
                        'message'=>'Gagal menambahkan Tasklist baru',
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
    
    private function get_atasan($np_karyawan){
        $this->load->model("master_data/m_karyawan");
        $karyawan = $this->m_karyawan->get_posisi_karyawan($np_karyawan);

        if(empty($karyawan) or empty($karyawan["kode_unit"])){
            $periode_kemarin = date('Y_m', strtotime(date('Y-m-d') . ' -1 months'));
            $karyawan = $this->m_karyawan->get_posisi_karyawan_periode($np_karyawan,$periode_kemarin);
        }
        
        // unit
        if(strcmp($karyawan["posisi"],"unit")==0){
            // staf unit
            if(strcmp($karyawan["jabatan"],"staf")==0)
                $karyawan["posisi"] = "seksi";
            
            // kepala unit
            if(strcmp($karyawan["jabatan"],"kepala")==0){
                $karyawan["jabatan"] = "staf";
                $karyawan["posisi"] = "seksi";
            }
            $karyawan["kode_unit"] = substr($karyawan["kode_unit"],0,strlen($karyawan["kode_unit"])-1);
        }

        if(strcmp($karyawan["jabatan"],"kepala")==0)
            $kode_unit_atasan_1 = str_pad(substr($karyawan["kode_unit"],0,strlen($karyawan["kode_unit"])-1),5,0);
        else
            $kode_unit_atasan_1 = str_pad($karyawan["kode_unit"],5,0);

        if(strcmp($kode_unit_atasan_1,"00000")==0)
            $kode_unit_atasan_1 = "10000";

        if(strcmp(str_pad($karyawan["kode_unit"],5,0),$kode_unit_atasan_1)==0 and strlen($karyawan["kode_unit"])==1)
            $kode_unit_atasan_1 = "";
        
        $np_atasan_1 = $this->m_karyawan->get_atasan($kode_unit_atasan_1);
        return $np_atasan_1['np'];
    }
}