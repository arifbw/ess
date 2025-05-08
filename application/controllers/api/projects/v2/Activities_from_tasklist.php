<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Activities_from_tasklist extends MY_Controller {
    
    function __construct(){
        parent::__construct();
    }
    
    function index_get(){
        $data = [];
        try {
            if(empty($this->get('tasklist_id'))){
                $this->response([
                    'status'=>false,
                    'message'=>"ID Tasklist harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                # get activity
                $data = $this->db
                    ->where('created_by_np',$this->data_karyawan->np_karyawan)
                    ->or_where('as_member >',0)
                    ->get("(SELECT a.id,a.kode,a.project_id,a.task_name,a.description,a.progress,a.start_date,(CASE WHEN a.end_date_fix IS NOT NULL THEN a.end_date_fix ELSE a.end_date END) as end_date,a.note,a.uploaded_at,a.evidence,a.parent_id,a.created_at,a.created_by_np,a.created_by_nama, (SELECT COUNT(x.kode) FROM ess_project_tasklist_members x WHERE x.tasklist_id=a.id AND x.np='".$this->data_karyawan->np_karyawan."' AND x.deleted_at IS NULL) as as_member FROM `ess_project_tasklists` a WHERE a.parent_id=".$this->get('tasklist_id')." AND a.task_type='task' AND a.deleted_at IS NULL) combine")->result_array();
            
                $this->response([
                    'status'=>true,
                    'message'=>'Data Activity',
                    'data'=>$data
                ], MY_Controller::HTTP_OK);
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