<?php defined('BASEPATH') OR exit('No direct script access allowed');
# api/projects/v2/Delete_task
class Delete_task extends MY_Controller {
    
    function __construct(){
        parent::__construct();
    }
    
    # fungsi ini hanya berlaku untuk satu tingkat , project > tasklist > activity
    function index_post(){
        try {
            if(empty($this->post('id'))){
                $this->response([
                    'status'=>false,
                    'message'=>"ID harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                $get_detail = $this->db->where( 'id',$this->post('id') )->get( 'ess_project_tasklists' );
                if($get_detail->num_rows()==1){
                    $row_detail = $get_detail->row();
                    
                    if($row_detail->task_type=='task'){ # activity
                        $kode_activity = $row_detail->kode;
                        $this->db->where('id',$this->post('id'))->delete('ess_project_tasklists');
                        if($this->db->affected_rows()>0){
                            # delete member
                            $this->db->where('tasklist_id',$this->post('id'))->delete('ess_project_tasklist_members');
                            
                            # delete evidence
                            $this->db->where('tasklist_id',$this->post('id'))->delete('ess_project_tasklist_evidences');
                            
                            # delete file upload
                            if(is_dir('./uploads/tasklist/'.$kode_activity))
                                rmdir('./uploads/tasklist/'.$kode_activity);
                        }
                    } else{ # tasklist
                        $this->db->where('id',$this->post('id'))->delete('ess_project_tasklists');
                        # delete member tasklist
                        $this->db->where('tasklist_id',$this->post('id'))->delete('ess_project_tasklist_members');
                        
                        $get_activities = $this->db->select('GROUP_CONCAT(id) as ids, GROUP_CONCAT(kode) as kodes')->where('parent_id',$this->post('id'))->get('ess_project_tasklists')->row();
                        if($get_activities->ids!=null){
                            $arr_activities = explode(',',$get_activities->ids);
                            $arr_kodes = explode(',',$get_activities->kodes);
                            
                            # delete activity
                            $this->db->where_in('id',$arr_activities)->delete('ess_project_tasklists');
                            
                            # delete member activity
                            $this->db->where_in('tasklist_id',$arr_activities)->delete('ess_project_tasklist_members');
                            
                            # delete evidence
                            $this->db->where_in('tasklist_id',$arr_activities)->delete('ess_project_tasklist_evidences');
                            
                            # delete file upload
                            foreach($arr_kodes as $k){
                                if(is_dir('./uploads/tasklist/'.$k))
                                    rmdir('./uploads/tasklist/'.$k);
                            }
                        }
                    }
                    
                    $this->response([
                        'status'=>true,
                        'message'=>'Berhasil dihapus',
                        'data'=>[]
                    ], MY_Controller::HTTP_OK);
                } else{
                    $this->response([
                        'status'=>false,
                        'message'=>'Tidak ditemukan',
                        'data'=>[]
                    ], MY_Controller::HTTP_BAD_REQUEST);
                }
            }
        } catch(Exception $e){
            $this->response([
                'status'=>false,
                'message'=>$e->getMessage(),
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
        }
    }
}
