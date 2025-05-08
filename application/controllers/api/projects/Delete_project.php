<?php defined('BASEPATH') OR exit('No direct script access allowed');
# api/projects/delete_project
class Delete_project extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        $this->current_table = 'ess_projects';
    }
    
    function index_post(){
        $data = [];
        try {
            if(empty($this->post('id'))){
                $this->response([
                    'status'=>false,
                    'message'=>"ID harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } /*else if( $this->post('id')==1 ){
                $this->response([
                    'status'=>false,
                    'message'=>"Daily project tidak bisa dihapus",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            }*/ else{
                
                $get_detail = $this->db->where( 'id',$this->post('id') )->get( $this->current_table );
                if($get_detail->num_rows()==1){
                    $row_detail = $get_detail->row();
                    
                    if( $row_detail->created_by_np!=$this->data_karyawan->np_karyawan ){ # cek apakah project dibuat oleh dirinya sendiri
                        $this->response([
                            'status'=>false,
                            'message'=>'Gagal menghapus. Anda bukan pembuat Project',
                            'data'=>$data
                        ], MY_Controller::HTTP_BAD_REQUEST);
                    }
                    
                    $this->db->where( 'id',$this->post('id') )->delete( $this->current_table );
                    
                    if($this->db->affected_rows()>0){
                        # delete member project
                        $this->db->where('project_id',$this->post('id'))->update('ess_project_members');
                        
                        # delete all member
                        $get_all_task = $this->db->select('GROUP_CONCAT(id) as ids')->where('project_id',$this->post('id'))->get('ess_project_tasklists')->row();
                        if($get_all_task->ids!=null){
                            $arr_all_task = explode(',',$get_all_task);
                            $this->db->where_in('tasklist_id',$arr_all_task)->delete('ess_project_tasklist_members');
                        }
                        
                        # delete tasklists
                        $this->db->where( 'project_id',$this->post('id') )->where('task_type','dir')->delete( 'ess_project_tasklists' );
                        
                        $get_activities = $this->db->select('GROUP_CONCAT(id) as ids, GROUP_CONCAT(kode) as kodes')->where('project_id',$this->post('id'))->where('task_type','task')->get('ess_project_tasklists')->row();
                        if($get_activities->ids!=null){
                            $arr_activities = explode(',',$get_activities->ids);
                            $arr_kodes = explode(',',$get_activities->kodes);
                            
                            # delete activity
                            $this->db->where_in('id',$arr_activities)->delete('ess_project_tasklists');
                            
                            # delete evidence
                            $this->db->where_in('tasklist_id',$arr_activities)->delete('ess_project_tasklist_evidences');
                            
                            # delete file upload
                            foreach($arr_kodes as $k){
                                if(is_dir('./uploads/tasklist/'.$k))
                                    rmdir('./uploads/tasklist/'.$k);
                            }
                        }
                        
                        # delete all dir/task where project_id=post(id)
                        $this->db->where( 'project_id',$this->post('id') )->delete( 'ess_project_tasklists' );
                        
                        $this->response([
                            'status'=>true,
                            'message'=>'Berhasil dihapus',
                            'data'=>$data
                        ], MY_Controller::HTTP_OK);
                    } else{
                        $this->response([
                            'status'=>false,
                            'message'=>'Gagal',
                            'data'=>$data
                        ], MY_Controller::HTTP_BAD_REQUEST);
                    }
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
