<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Delete_tasklist_member extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->model('api/M_karyawan_api');
        $this->load->model('api/projects/M_delete_tasklist_member');
    }
    
    function index_post(){
        $data=[];
        try {
            if(empty($this->post('tasklist_id'))){
                $this->response([
                    'status'=>false,
                    'message'=>"ID Tasklist harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($this->post('list_np'))){
                $this->response([
                    'status'=>false,
                    'message'=>"NP harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if( $this->post('list_np')==[] ){
                $this->response([
                    'status'=>false,
                    'message'=>"NP harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                
                # cek apakah anda sebagai PM atau created
                $cek_tasklist = $this->db->select('a.id,a.project_id,a.task_type, b.np,b.is_pm,b.is_pic,b.is_viewer,(SELECT np FROM ess_project_members WHERE project_id=a.project_id AND is_pm="1") as np_pm,(SELECT created_by_np FROM ess_projects WHERE id=a.project_id) as np_created')
                    ->where('a.id',$this->post('tasklist_id'))->where('a.task_type','dir')
                    ->from('ess_project_tasklists a')
                    ->join('ess_project_members b','a.project_id=b.project_id AND b.np="'.$this->data_karyawan->np_karyawan.'"','LEFT')
                    ->get();
                
                if($cek_tasklist->num_rows()==1){
                    $row_tasklist = $cek_tasklist->row();
                    if( $this->data_karyawan->np_karyawan!=$row_tasklist->np_pm && $this->data_karyawan->np_karyawan!=$row_tasklist->np_created ){
                        $this->response([
                            'status'=>false,
                            'message'=>'Tidak bisa menghapus member. Anda bukan PM atau Pembuat Project.'
                        ], MY_Controller::HTTP_BAD_REQUEST);
                    }
                    
                    $list_np = array_diff($this->post('list_np'), [$row_tasklist->np_pm,$row_tasklist->np_created]);
                    $this->M_delete_tasklist_member->reset_data();
                    $this->M_delete_tasklist_member->get_child(['id'=>$row_tasklist->id, 'task_type'=>$row_tasklist->task_type]);
                    $list_id = $this->M_delete_tasklist_member->get_data();
                    
                    $this->db->where_in( 'tasklist_id',$list_id )->where_in( 'np',$list_np )->delete('ess_project_tasklist_members');
                    if($this->db->affected_rows()>0){
                        $this->response([
                            'status'=>true,
                            'message'=>'Member telah dihapus dari Tasklist'
                        ], MY_Controller::HTTP_OK);
                    } else{
                        $this->response([
                            'status'=>false,
                            'message'=>'Gagal menghapus member'
                        ], MY_Controller::HTTP_BAD_REQUEST);
                    }
                } else{
                    $this->response([
                        'status'=>false,
                        'message'=>'Activity tidak ditemukan'
                    ], MY_Controller::HTTP_BAD_REQUEST);
                }
            }
            
        } catch(Exception $e){
            $this->response([
                'status'=>false,
                'message'=>'Error Exception',
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
        }
    }
}
