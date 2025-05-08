<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Pilih_pelaksana extends MY_Controller {
    
    function __construct(){
        parent::__construct();
    }
    
    function index_post(){
        $data = [];        
        try {
            if(empty($this->post('project_id'))){
                $this->response([
                    'status'=>false,
                    'message'=>"ID project harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($this->post('list_np'))){
                $this->response([
                    'status'=>false,
                    'message'=>"NP harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                $cek_project = $this->db->where('id',$this->post('project_id'))->get('ess_projects');
                
                if($cek_project->num_rows()==0){
                    $this->response([
                        'status'=>false,
                        'message'=>'ID not found',
                        'data'=>$data
                    ], MY_Controller::HTTP_BAD_REQUEST);
                } else{
                    if( $cek_project->row()->id==1 ){
                        $this->response([
                            'status'=>false,
                            'message'=>'Daily Project bisa diisi semua karyawan',
                            'data'=>$data
                        ], MY_Controller::HTTP_BAD_REQUEST);
                        exit;
                    }
                    
                    foreach( $this->post('list_np') as $row ){
                        $params = [ 'project_id'=>$this->post('project_id'), 'np'=>$row['np'] ];
                        $cek = $this->db->where($params)->get('ess_project_members');
                        if($cek->num_rows()==0){
                            $data_insert = [];
                            $data_insert['kode'] = $this->uuid->v4();
                            $data_insert['project_id'] = $this->post('project_id');
                            $data_insert['np'] = $row['np'];
                            $data_insert['name'] = $row['nama'];
                            $data_insert['created_at'] = date('Y-m-d H:i:s');
                            
                            $this->db->insert('ess_project_members',$data_insert);
                            $data[] = $data_insert;
                        }
                    }
                    
                    $this->response([
                        'status'=>true,
                        'message'=>'Berhasil disimpan',
                        'data'=>$data
                    ], MY_Controller::HTTP_OK);
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
