<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Get_subproject extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        $this->current_table = 'ess_project_tasklists';
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
                    
                    # jika ada parent
                    if( @$this->get('parent_id')!=null ){
                        $get_sub = $this->db->select('combine.* , ess_project_x_milestones.order_number')
                            ->where( 'combine.parent_id',$this->get('parent_id') )
                            ->where( 'combine.task_type','dir' )
                            ->where( 'combine.deleted_at is null',null,false )
                            ->group_start()
                                ->where('combine.as_member >',0)
                                ->or_where('combine.created_by_np',$this->data_karyawan->np_karyawan)
                            ->group_end()
                            ->join('ess_project_x_milestones','combine.milestone_kode=ess_project_x_milestones.kode','LEFT')
                            ->order_by('combine.created_at,ess_project_x_milestones.order_number')
                            ->get("(SELECT a.id,a.kode,a.project_id,a.task_name,a.task_type,a.parent_id,a.description,a.created_at,a.created_by_np,a.created_by_nama,a.deleted_at,(SELECT COUNT(kode) FROM ess_project_tasklist_members WHERE tasklist_id=a.id AND np='".$this->data_karyawan->np_karyawan."' ) as as_member, a.milestone_kode FROM `ess_project_tasklists` a) combine")
                            ->result();
                    } else{
                        $this->db->select('combine.* , ess_project_x_milestones.order_number')
                            ->where( 'combine.project_id',$this->get('id') )
                            ->where( 'combine.task_type','dir' )
                            ->where( 'combine.parent_id is null',null,false )
                            ->where( 'combine.deleted_at is null',null,false );
                        if($row_get->created_by_np!=$this->data_karyawan->np_karyawan){
                            $this->db
                                ->group_start()
                                    ->where('combine.as_member >',0)
                                    ->or_where('combine.created_by_np',$this->data_karyawan->np_karyawan)
                                ->group_end();
                        }
                        $this->db->join('ess_project_x_milestones','combine.milestone_kode=ess_project_x_milestones.kode','LEFT')
                            ->order_by('combine.created_at,ess_project_x_milestones.order_number');
                        $get_sub = $this->db->get("(SELECT a.id,a.kode,a.project_id,a.task_name,a.task_type,a.parent_id,a.description,a.created_at,a.created_by_np,a.created_by_nama,a.deleted_at,(SELECT COUNT(kode) FROM ess_project_tasklist_members WHERE tasklist_id=a.id AND np='".$this->data_karyawan->np_karyawan."' ) as as_member, a.milestone_kode FROM `ess_project_tasklists` a) combine")
                            ->result();
                        //$get_sub = $this->db->select('id,kode,project_id,task_name,task_type,parent_id,description,created_at,created_by_np,created_by_nama')->where( 'project_id',$this->get('id') )->where( 'task_type','dir' )->where( 'parent_id is null',null,false )->where( 'deleted_at is null',null,false )->get($this->current_table)->result();
                    }
                    
                    $this->response([
                        'status'=>true,
                        'message'=>'Tasklists',
                        'data'=>$get_sub
                    ], MY_Controller::HTTP_OK);
                } else{
                    $this->response([
                        'status'=>false,
                        'message'=>'Tidak ditemukan',
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
