<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Delete extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        $this->current_table = 'ess_project_x_milestones';
    }
    
    function index_post(){
        try {
            if(empty($this->post('id'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Kode Milestone harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                $this->db->where('kode',$this->post('id'))->update($this->current_table, [
                    'deleted_at'=>date('Y-m-d H:i:s')
                ]);
                
                if($this->db->affected_rows()>0){
                    $this->response([
                        'status'=>true,
                        'message'=>'Milestone Dihapus',
                        'data'=>$this->db->where('kode',$this->post('id'))->get($this->current_table)->row()
                    ], MY_Controller::HTTP_OK);
                } else{
                    $this->response([
                        'status'=>false,
                        'message'=>'Gagal Menghapus Milestone',
                        'data'=>[]
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
