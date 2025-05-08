<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Get_project extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        $this->current_table = 'ess_projects';
    }
    
    function index_get(){
        $data = [];
        try {
            # get projects
            $get = $this->db
                ->where( 'deleted_at is null',null,false )
                ->group_start()
                    ->where('as_partisipan >',0)
                    ->or_where('created_by_np',$this->data_karyawan->np_karyawan)
                ->group_end()
                ->get("(SELECT *, (SELECT COUNT(ess_project_members.kode) FROM ess_project_members WHERE project_id=ess_projects.id AND ess_project_members.np='".$this->data_karyawan->np_karyawan."') AS as_partisipan FROM ess_projects) combine")->result();
            
            $this->response([
                'status'=>true,
                'message'=>'Projects',
                'data'=>$get
            ], MY_Controller::HTTP_OK);
        } catch(Exception $e){
            $this->response([
                'status'=>false,
                'message'=>'Error Exception',
                'data'=>$data
            ], MY_Controller::HTTP_BAD_REQUEST);
        }
    }
}
