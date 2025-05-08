<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Get_projects extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        $this->current_table = 'ess_projects';
    }
    
    function index_get(){
        $data = [];
        try {
            
            $status = '';
            if(!empty($this->get('status'))){
                $status = $this->get('status');
            }
            
            $filter_start_date = date('Y-01-01');
            if(!empty($this->get('filter_start_date'))){
                $filter_start_date = $this->get('filter_start_date');
            }
            $filter_start_date = date('Y-m-d', strtotime($filter_start_date . ' -1 day'));
            
            $filter_end_date = date('Y-m-d');
            if(!empty($this->get('filter_end_date'))){
                $filter_end_date = $this->get('filter_end_date');
            }
            $filter_end_date = date('Y-m-d', strtotime($filter_end_date . ' +1 day'));
            
            # get projects
            $this->db
                ->where( 'deleted_at is null',null,false )
                ->group_start()
                    ->where('as_member >',0)
                    ->or_where('created_by_np',$this->data_karyawan->np_karyawan)
                ->group_end();
            if( empty($this->get('filter_start_date')) && empty($this->get('filter_end_date')) ){}
                
            else{
                $this->db->group_start()
                    ->where("start_date BETWEEN '$filter_start_date' AND '$filter_end_date'")
                    ->or_where("end_date BETWEEN '$filter_start_date' AND '$filter_end_date'")
                ->group_end();
            }
            $get = $this->db->get("(SELECT id,kode,project_name,description,start_date,end_date,created_at,created_by_np,created_by_nama,project_type_id,category_id,deleted_at, (SELECT COUNT(ess_project_members.kode) FROM ess_project_members WHERE project_id=ess_projects.id AND ess_project_members.np='".$this->data_karyawan->np_karyawan."') AS as_member FROM ess_projects) combine")->result_array();
            
            $count=0;
            foreach($get as $field){
                $row = $field;
                $search_under100 = $this->db->select('id,task_name,description,start_date,(CASE WHEN end_date_fix IS NOT NULL THEN end_date_fix ELSE end_date END) as end_date,progress,evidence,uploaded_at')
                    ->where('project_id',$field['id'])
                    ->where('task_type','task')
                    ->group_start()
                        ->group_start()
                            ->where('progress <',100)
                            ->or_where('progress IS NULL',null,false)
                        ->group_end()
                        ->or_where("((CASE WHEN end_date_fix IS NOT NULL THEN end_date_fix ELSE end_date END)>='".date('Y-m-d')."')",null,false)
                    ->group_end()
                    ->where('deleted_at IS NULL',null,false)
                    ->get('ess_project_tasklists')
                    ->result_array();
                
                $count_task = $this->db->select('count(id) as ids')->where('project_id',$field['id'])->where('task_type','task')->where('deleted_at is null',null,false)->get('ess_project_tasklists')->row_array();
                
                $row['activity_belum_selesai']=$search_under100;
                
                if($status==1){
                    if($search_under100!=[] || $count_task['ids']==0){
                        $data[] = $row;
                        $count++;
                    }
                } else if($status==2){
                    if($search_under100==[] && $count_task['ids']>0){
                        $data[] = $row;
                        $count++;
                    }
                } else{
                    $data[] = $row;
                    $count++;
                }
            }
            
            $this->response([
                'status'=>true,
                'message'=>'Projects',
                'count'=>$count,
                'data'=>$data
            ], MY_Controller::HTTP_OK);
        } catch(Exception $e){
            $this->response([
                'status'=>false,
                'message'=>'Error Exception',
                'count'=>0,
                'data'=>$data
            ], MY_Controller::HTTP_BAD_REQUEST);
        }
    }
}
