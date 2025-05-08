<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Data extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->helper("tanggal_helper");
    }
    
    function index_post(){ # tambah project baru
        $data = [];
        $data_insert = [];
        
        try {
            if(empty($this->post('project_name'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Nama Project harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if( empty($this->post('project_type_id')) ){
                $this->response([
                    'status'=>false,
                    'message'=>"Jenis Project harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                
                if( in_array($this->post('project_type_id'),[3]) ){
                    $this->response([
                        'status'=>false,
                        'message'=>'Tambah "Additional task" masih dalam pengembangan',
                        'data'=>[]
                    ], MY_Controller::HTTP_BAD_REQUEST);
                }
                
                # START: collect data
                $data_insert['kode'] = $this->uuid->v4();
                $data_insert['project_name'] = $this->post('project_name');
                $data_insert['start_date'] = @$this->post('start_date') ? $this->post('start_date') : date('Y-m-d');
                $data_insert['end_date'] = @$this->post('end_date') ? $this->post('end_date') : date('Y-m-d');
                
                if(@$this->post('description'))
                    $data_insert['description'] = $this->post('description');
                
                $data_insert['created_at'] = date('Y-m-d H:i:s');
                $data_insert['created_by_np'] = $this->data_karyawan->np_karyawan;
                $data_insert['created_by_nama'] = $this->data_karyawan->nama;
                $data_insert['created_by_jabatan'] = $this->data_karyawan->nama_jabatan_singkat;
                $data_insert['created_by_kode_unit'] = $this->data_karyawan->kode_unit;
                
                $data_insert['project_type_id'] = $this->post('project_type_id');
                
                /* milestone dipindah ke tasklist
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
                */
                
                if(@$this->post('category_id'))
                    $data_insert['category_id'] = $this->post('category_id');
                # END: collect data
                
                # insert
                $this->db->insert('ess_projects', $data_insert);

                if($this->db->affected_rows()>0){
                    
                    $new_id = $this->db->insert_id();
                    # insert to member
                    $this->db->insert('ess_project_members',[
                        'kode'=>$this->uuid->v4(),
                        'project_id'=>$new_id,
                        'np'=>$this->data_karyawan->np_karyawan,
                        'nama'=>$this->data_karyawan->nama,
                        'jabatan'=>$this->data_karyawan->nama_jabatan_singkat,
                        'kode_unit'=>$this->data_karyawan->kode_unit,
                        'nama_unit'=>$this->data_karyawan->nama_unit_singkat,
                        
                        'created_at'=>date('Y-m-d H:i:s'),
                        'created_by_np'=>$this->data_karyawan->np_karyawan,
                        'created_by_nama'=>$this->data_karyawan->nama,
                        'created_by_jabatan'=>$this->data_karyawan->nama_jabatan_singkat,
                        'created_by_kode_unit'=>$this->data_karyawan->kode_unit,
                        'created_by_nama_unit'=>$this->data_karyawan->nama_unit_singkat,
                        
                        'is_pic'=>'1'
                    ]);
                    
                    # add milestones where project_type_id=1 (project based)
                    if( $this->post('project_type_id')==1 ){
                        $get_milestones = $this->db->select('id,nama')->where('deleted_at is null',null,false)->get('ess_project_milestones')->result();
                        foreach($get_milestones as $ms){
                            $this->db->insert('ess_project_x_milestones',[
                                'kode'=>$this->uuid->v4(),
                                'project_id'=>$new_id,
                                'nama'=>$ms->nama,
                                'order_number'=>$ms->id,
                                'created_at'=>date('Y-m-d H:i:s')
                            ]);
                        }
                    }
                    
                    $data_insert['id'] = (string)$new_id;
                    $this->response([
                        'status'=>true,
                        'message'=>'Project Baru telah dibuat',
                        'data'=>$data_insert
                    ], MY_Controller::HTTP_OK);
                } else{
                    $this->response([
                        'status'=>false,
                        'message'=>'Gagal menambahkan Project Baru',
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
