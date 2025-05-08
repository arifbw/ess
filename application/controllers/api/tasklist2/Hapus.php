<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Hapus extends MY_Controller {
    
    function __construct(){
        parent::__construct();
    }
    
    function index_post(){
        $data_insert = [];
        try {
            if(empty($this->post('id'))){
                $this->response([
                    'status'=>false,
                    'message'=>'ID harus diisi'
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                $id = $this->post('id');
                
                $this->db->where('id',$id)->update('ess_performance_management',[
                    'deleted_at'=>date('Y-m-d H:i:s'),
                    'deleted_by'=>$this->data_karyawan->np_karyawan]);
                //$this->db->where('id',$id)->delete('ess_performance_management');
                if($this->db->affected_rows()>0){
                    $this->response([
                        'status'=>true,
                        'message'=>'List Dihapus'
                    ], MY_Controller::HTTP_OK);
                } else{
                    $this->response([
                        'status'=>false,
                        'message'=>'Failed to remove'
                    ], MY_Controller::HTTP_BAD_REQUEST);
                }
            }
        } catch(Exception $e){
            $this->response([
                'status'=>false,
                'message'=>'Error Exception'
            ], MY_Controller::HTTP_BAD_REQUEST);
        }
    }
}
