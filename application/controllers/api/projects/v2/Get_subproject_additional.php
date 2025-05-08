<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Get_subproject_additional extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        $this->current_table = 'ess_project_tasklists';
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
            
            $filter_end_date = date('Y-m-d');
            if(!empty($this->get('filter_end_date'))){
                $filter_end_date = $this->get('filter_end_date');
            }
            
            # get dir
            $this->db->select('a.id,a.kode,a.project_id,b.project_name,b.project_type_id,a.task_name,a.task_type,a.parent_id,a.description,a.created_at,a.created_by_np,a.created_by_nama,a.deleted_at, c.kode as kode_row_member, c.is_additional_member')
                ->from( 'ess_project_tasklists a' )
                ->join( 'ess_projects b','a.project_id=b.id' )
                ->join( 'ess_project_tasklist_members c','a.id=c.tasklist_id AND c.np="'.$this->data_karyawan->np_karyawan.'"','LEFT' )
                //->where( 'b.project_type_id',3 )
                ->where( 'a.task_type','dir' )
                ->where( 'a.deleted_at is null',null,false )
                ->where( 'b.deleted_at is null',null,false )
                ->group_start()
                    ->group_start()
                        ->where('a.created_by_np',$this->data_karyawan->np_karyawan)
                        ->where('b.project_type_id',3)
                    ->group_end()
                    ->or_where('is_additional_member','1')
                ->group_end();
            if( empty($this->get('filter_start_date')) && empty($this->get('filter_end_date')) ){}
                
            else{
                # tambahan filter date range
                $this->db->group_start()
                    ->where("a.start_date BETWEEN '$filter_start_date' AND '$filter_end_date'")
                    ->or_where("((CASE WHEN a.end_date_fix IS NOT NULL THEN a.end_date_fix ELSE a.end_date END) BETWEEN '$filter_start_date' AND '$filter_end_date')")
                ->group_end();
                # tambahan filter date range
            }
                //->where('a.created_by_np',$this->data_karyawan->np_karyawan)
                $get_sub = $this->db->order_by('a.created_at')
                ->get()
                ->result_array();
            
            $count=0;
            foreach($get_sub as $field){
                $row = $field;
                $search_under100 = $this->db->select('id,task_name,description,start_date,(CASE WHEN end_date_fix IS NOT NULL THEN end_date_fix ELSE end_date END) as end_date,progress,evidence,uploaded_at')
                    ->where('parent_id',$field['id'])
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
                
                $count_task = $this->db->select('count(id) as ids')->where('parent_id',$field['id'])->where('task_type','task')->where('deleted_at is null',null,false)->get('ess_project_tasklists')->row_array();
                
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
                'message'=>'Additional Tasklists',
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
