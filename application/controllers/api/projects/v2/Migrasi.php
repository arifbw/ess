<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migrasi extends MY_Controller {
    
    function __construct(){
        parent::__construct();
    }
    
    function index_get(){
        $data = [];
        try {
            $get_all = $this->db
                ->where('id>=',110)
                ->where_not_in('id',array(112,117,118))
                ->get('ess_performance_management')
                ->result();
            
            foreach($get_all as $a){
                # insert ke project
                $data_project = [
                    'kode'=>$this->uuid->v4(),
                    'project_name'=>'Routine work: '.$a->target_pekerjaan,
                    'description'=>'Project Migrasi dari tasklist versi lama',
                    'start_date'=>$a->tanggal,
                    'end_date'=>$a->tanggal,
                    'created_at'=>$a->created_at,
                    'created_by_np'=>$a->created_by,
                    'created_by_nama'=>$a->nama,
                    'created_by_jabatan'=>$a->nama_jabatan,
                    'created_by_kode_unit'=>$a->kode_unit,
                    'project_type_id'=>2
                ];
                $this->db->insert('migrasi_ess_projects',$data_project);
                if($this->db->affected_rows()>0){
                    $new_project_id = $this->db->insert_id();
                    
                    # START: insert member project
                    $data_project_member = [
                        # pembuat
                        [
                            'kode'=>$this->uuid->v4(),
                            'project_id'=>$new_project_id,
                            'np'=>$a->created_by,
                            'nama'=>$a->nama,
                            'jabatan'=>$a->nama_jabatan,
                            'kode_unit'=>$a->kode_unit,
                            'nama_unit'=>$a->nama_unit,
                            'created_at'=>$a->created_at,
                            'created_by_np'=>$a->created_by,
                            'created_by_nama'=>$a->nama,
                            'created_by_jabatan'=>$a->nama_jabatan,
                            'created_by_kode_unit'=>$a->kode_unit,
                            'created_by_nama_unit'=>$a->nama_unit,
                            'is_pic'=>'1',
                            'is_pm'=>'0',
                        ],

                        # PM
                        [
                            'kode'=>$this->uuid->v4(),
                            'project_id'=>$new_project_id,
                            'np'=>$a->np_atasan,
                            'nama'=>$a->nama_atasan,
                            'jabatan'=>$a->nama_jabatan_atasan,
                            'kode_unit'=>$a->kode_unit_atasan,
                            'nama_unit'=>null,
                            'created_at'=>$a->created_at,
                            'created_by_np'=>$a->created_by,
                            'created_by_nama'=>$a->nama,
                            'created_by_jabatan'=>$a->nama_jabatan,
                            'created_by_kode_unit'=>$a->kode_unit,
                            'created_by_nama_unit'=>$a->nama_unit,
                            'is_pic'=>'0',
                            'is_pm'=>'1'
                        ]
                    ];
                    $this->db->insert_batch('migrasi_ess_project_members',$data_project_member);
                    # END: insert member project
                    
                    # START: create tasklist
                    $data_tasklist = [
                        'kode'=>$this->uuid->v4(),
                        'project_id'=>$new_project_id,
                        'task_name'=>'Tasklist: '.$a->target_pekerjaan,
                        'task_type'=>'dir',
                        'start_date'=>$a->tanggal,
                        'end_date'=>$a->tanggal,
                        'description'=>'Migrasi dari tasklist versi lama',
                        'created_at'=>$a->created_at,
                        'created_by_np'=>$a->created_by,
                        'created_by_nama'=>$a->nama,
                        'created_by_jabatan'=>$a->nama_jabatan,
                        'created_by_kode_unit'=>$a->kode_unit
                    ];
                    $this->db->insert('migrasi_ess_project_tasklists',$data_tasklist);
                    # END: create tasklist
                    
                    if($this->db->affected_rows()>0){
                        $new_tasklist_id = $this->db->insert_id();
                        
                        # START: insert member tasklist
                        $data_tasklist_member = [
                            # pembuat
                            [
                                'kode'=>$this->uuid->v4(),
                                'tasklist_id'=>$new_tasklist_id,
                                'np'=>$a->created_by,
                                'nama'=>$a->nama,
                                'jabatan'=>$a->nama_jabatan,
                                'kode_unit'=>$a->kode_unit,
                                'nama_unit'=>$a->nama_unit,
                                'created_at'=>$a->created_at,
                                'created_by_np'=>$a->created_by,
                                'created_by_nama'=>$a->nama,
                                'created_by_jabatan'=>$a->nama_jabatan,
                                'created_by_kode_unit'=>$a->kode_unit,
                                'created_by_nama_unit'=>$a->nama_unit,
                                'is_pic'=>'1'
                            ],

                            # member
                            [
                                'kode'=>$this->uuid->v4(),
                                'tasklist_id'=>$new_tasklist_id,
                                'np'=>$a->np_atasan,
                                'nama'=>$a->nama_atasan,
                                'jabatan'=>$a->nama_jabatan_atasan,
                                'kode_unit'=>$a->kode_unit_atasan,
                                'nama_unit'=>null,
                                'created_at'=>$a->created_at,
                                'created_by_np'=>$a->created_by,
                                'created_by_nama'=>$a->nama,
                                'created_by_jabatan'=>$a->nama_jabatan,
                                'created_by_kode_unit'=>$a->kode_unit,
                                'created_by_nama_unit'=>$a->nama_unit,
                                'is_pic'=>'0'
                            ]
                        ];
                        $this->db->insert_batch('migrasi_ess_project_tasklist_members',$data_tasklist_member);
                        # END: insert member tasklist
                        
                        # START: create activity
                        $data_activity = [
                            'kode'=>$this->uuid->v4(),
                            'project_id'=>$new_project_id,
                            'task_name'=>$a->target_pekerjaan,
                            'task_type'=>'task',
                            'start_date'=>$a->tanggal,
                            'end_date'=>$a->tanggal,
                            'parent_id'=>$new_tasklist_id,
                            'note'=>$a->hasil_pekerjaan,
                            'description'=>'Migrasi activity dari versi lama',
                            'progress'=>$a->progress,
                            
                            'created_at'=>$a->created_at,
                            'created_by_np'=>$a->created_by,
                            'created_by_nama'=>$a->nama,
                            'created_by_jabatan'=>$a->nama_jabatan,
                            'created_by_kode_unit'=>$a->kode_unit,
                            
                            'evidence'=>$a->evidence!=null ? $a->evidence:null,
                            'uploaded_at'=>$a->uploaded_at!=null ? $a->uploaded_at:null
                        ];
                        if($a->updated_at!=null){
                            $data_activity['updated_at'] = $a->updated_at;
                            $data_activity['updated_by_np'] = $a->created_by;
                            $data_activity['updated_by_nama'] = $a->nama;
                            $data_activity['updated_by_jabatan'] = $a->nama_jabatan;
                            $data_activity['updated_by_kode_unit'] = $a->kode_unit;
                        }
                        $this->db->insert('migrasi_ess_project_tasklists',$data_activity);
                        # END: create activity
                        
                        if($this->db->affected_rows()>0){
                            $new_activity_id = $this->db->insert_id();
                            
                            # START: insert member activity
                            $data_activity_member = [
                                # PIC
                                [
                                    'kode'=>$this->uuid->v4(),
                                    'tasklist_id'=>$new_activity_id,
                                    'np'=>$a->created_by,
                                    'nama'=>$a->nama,
                                    'jabatan'=>$a->nama_jabatan,
                                    'kode_unit'=>$a->kode_unit,
                                    'nama_unit'=>$a->nama_unit,
                                    'created_at'=>$a->created_at,
                                    'created_by_np'=>$a->created_by,
                                    'created_by_nama'=>$a->nama,
                                    'created_by_jabatan'=>$a->nama_jabatan,
                                    'created_by_kode_unit'=>$a->kode_unit,
                                    'created_by_nama_unit'=>$a->nama_unit,
                                    'is_pic'=>'1'
                                ],

                                # member
                                [
                                    'kode'=>$this->uuid->v4(),
                                    'tasklist_id'=>$new_activity_id,
                                    'np'=>$a->np_atasan,
                                    'nama'=>$a->nama_atasan,
                                    'jabatan'=>$a->nama_jabatan_atasan,
                                    'kode_unit'=>$a->kode_unit_atasan,
                                    'nama_unit'=>null,
                                    'created_at'=>$a->created_at,
                                    'created_by_np'=>$a->created_by,
                                    'created_by_nama'=>$a->nama,
                                    'created_by_jabatan'=>$a->nama_jabatan,
                                    'created_by_kode_unit'=>$a->kode_unit,
                                    'created_by_nama_unit'=>$a->nama_unit,
                                    'is_pic'=>'0'
                                ]
                            ];
                            $this->db->insert_batch('migrasi_ess_project_tasklist_members',$data_activity_member);
                            # END: insert member activity
                            
                            # START: insert evidence
                            if($a->evidence!=null){
                                $data_evidence = [
                                    'kode'=>$this->uuid->v4(),
                                    'tasklist_id'=>$new_activity_id,
                                    'task_date'=>$a->tanggal,
                                    'progress'=>$a->progress,
                                    'note'=>$a->hasil_pekerjaan,
                                    'evidence'=>$a->evidence,
                                    'created_at'=>$a->created_at,
                                    'created_by_np'=>$a->created_by,
                                    'created_by_nama'=>$a->nama,
                                    'created_by_jabatan'=>$a->nama_jabatan,
                                    'created_by_kode_unit'=>$a->kode_unit,
                                    'created_by_nama_unit'=>$a->nama_unit
                                ];
                                $this->db->insert('migrasi_ess_project_tasklist_evidences',$data_evidence);
                            }
                            # END: insert evidence
                        }
                    }
                }
            }
            
            $this->response([
                'status'=>true,
                'message'=>'All',
                'data'=>$get_all
            ], MY_Controller::HTTP_OK);
        } catch(Exception $e){
            $this->response([
                'status'=>false,
                'message'=>$e->getMessage(),
                'data'=>$data
            ], MY_Controller::HTTP_BAD_REQUEST);
        }
    }
}
