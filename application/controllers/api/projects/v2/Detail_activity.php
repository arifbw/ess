<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Detail_activity extends MY_Controller {
    
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
                    'message'=>"ID Tasklist harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                # get activity
                $activity = $this->db->select('id,kode,project_id,task_name,description,parent_id,start_date,(CASE WHEN end_date_fix IS NOT NULL THEN end_date_fix ELSE end_date END) as end_date,progress,task_type,uploaded_at,evidence,created_by_np')->where( 'id',$this->get('id') )->get( $this->current_table );
                if( $activity->num_rows()==1 ){
                    $row_activity = $activity->row_array();
                    $row_activity['all_partisipants'] = $this->db->select('np,nama,jabatan,kode_unit,nama_unit,is_pic,is_additional_member')->where('tasklist_id',$row_activity['id'])->where('deleted_at is null',null,false)->get('ess_project_tasklist_members')->result_array();
                    $this->response([
                        'status'=>true,
                        'message'=>'Detail',
                        'data'=>$row_activity
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
