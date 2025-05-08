<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Delete_member extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->model('api/M_karyawan_api');
    }
    
    function index_post(){
        $data=[];
        try {
            if(empty($this->post('project_id'))){
                $this->response([
                    'status'=>false,
                    'message'=>"ID Project harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($this->post('np'))){
                $this->response([
                    'status'=>false,
                    'message'=>"NP harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                
                $this->db->where([ 'project_id'=>$this->post('project_id'), 'np'=>$this->post('np') ] )->delete('ess_project_members');
                if($this->db->affected_rows()>0){
                    # delete member project otomatis menghapus member di tasklist dan activity
                    $this->db->where("np='".$this->post('np')."' AND tasklist_id in (SELECT id FROM ess_project_tasklists WHERE project_id=".$this->post('project_id').")")->delete('ess_project_tasklist_members');
                    
                    $this->response([
                        'status'=>true,
                        'message'=>'Member telah dihapus'
                    ], MY_Controller::HTTP_OK);
                } else{
                    $this->response([
                        'status'=>false,
                        'message'=>'Gagal menghapus member',
                        'process'=>$this->db->last_query()
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
