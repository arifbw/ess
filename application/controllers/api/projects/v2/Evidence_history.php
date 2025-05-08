<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Evidence_history extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        $this->current_table = 'ess_project_tasklist_evidences';
    }
    
    function index_get(){
        $data = [];
        try {
            if(empty($this->get('id'))){
                $this->response([
                    'status'=>false,
                    'message'=>"ID Activity harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                $get = $this->db->select('kode,tasklist_id as activity_id,task_date,progress,note,created_at,
                created_by_np,created_by_nama,created_by_jabatan,created_by_kode_unit,created_by_nama_unit,
                updated_by_np,updated_by_nama,updated_by_jabatan,updated_by_kode_unit,updated_by_nama_unit, evidence')
                    ->where( 'tasklist_id',$this->get('id') )
                    ->order_by('created_at','DESC')
                    ->get($this->current_table)
                    ->result_array();
                foreach($get as $row){
                    $fix = $row;
                    $evidence = $row['evidence'];
                    array_pop($fix);
                    if(is_file('./'.$evidence))
                        $fix['evidence_url'] = base_url($evidence);
                    else
                        $fix['evidence_url'] = null;
                    
                    $data[] = $fix;
                }

                $this->response([
                    'status'=>true,
                    'message'=>'Evidence history',
                    'data'=>$data
                ], MY_Controller::HTTP_OK);
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
