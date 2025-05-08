<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Delete_activity_member extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->model('api/M_karyawan_api');
    }
    
    function index_post(){
        $data=[];
        try {
            if(empty($this->post('activity_id'))){
                $this->response([
                    'status'=>false,
                    'message'=>"ID Activity harus diisi",
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
                
                # cek apakah anda sebagai PIC activity
                $cek_activity = $this->db->select('a.id,a.task_name,b.np,b.is_pic,b.is_additional_member, (SELECT np FROM ess_project_tasklist_members WHERE tasklist_id=a.id AND is_pic="1") as np_pic')
                    ->where('a.id',$this->post('activity_id'))->where('a.task_type','task')
                    ->from('ess_project_tasklists a')
                    ->join('ess_project_tasklist_members b','a.id=b.tasklist_id AND b.np="'.$this->data_karyawan->np_karyawan.'"','LEFT')
                    ->get();
                if($cek_activity->num_rows()==1){
                    $row_activity = $cek_activity->row();
                    if($row_activity->is_pic!='1'){
                        $this->response([
                            'status'=>false,
                            'message'=>'Tidak bisa menghapus member. Anda bukan PIC.'
                        ], MY_Controller::HTTP_BAD_REQUEST);
                    }
                    
                    $this->db->where( 'tasklist_id',$this->post('activity_id') );
                    if($row_activity->np_pic!=null)
                        $this->db->where( 'np!=',$row_activity->np_pic );
                    $this->db->where_in('np', $this->post('list_np') );
                    $this->db->delete('ess_project_tasklist_members');
                    
                    if($this->db->affected_rows()>0){
                        $this->response([
                            'status'=>true,
                            'message'=>'Member telah dihapus dari Activity'
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
