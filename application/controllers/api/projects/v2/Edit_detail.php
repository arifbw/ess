<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Edit_detail extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        $this->current_table = 'ess_project_tasklists';
    }
    
    function index_get(){
        $data = [];
        try {
            if(empty($this->get('id'))){
                $this->response([
                    'status'=>false,
                    'message'=>"ID harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                # get task/dir
                $get = $this->db->where( 'id',$this->get('id') )->get( $this->current_table );
                if( $get->num_rows()==1 ){
                    $row_get = $get->row();
                    if( $row_get->created_by_np==$this->data_karyawan->np_karyawan ){
                        $this->response([
                            'status'=>true,
                            'message'=>'Detail',
                            'data'=>$row_get
                        ], MY_Controller::HTTP_OK);
                    } else{
                        $this->response([
                            'status'=>false,
                            'message'=>'Anda bukan pembuat '.($row_get->task_type=='dir'?'Tasklist':'Activity'),
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
    
    function index_post(){
        $data = [];
        $data_insert = [];
        
        try {
            if(empty($this->post('id'))){
                $this->response([
                    'status'=>false,
                    'message'=>"ID harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($this->post('task_name'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Nama Tasklist/Activity harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                
                $get_detail = $this->db->where( 'id',$this->post('id') )->get( $this->current_table );
                if($get_detail->num_rows()==1){
                    $row_detail = $get_detail->row();
                    $data_insert['task_name'] = $this->post('task_name');
                    $data_insert['description'] = @$this->post('description')?$this->post('description'):null;
                    $data_insert['updated_at'] = date('Y-m-d H:i:s');
                    $data_insert['updated_by_np'] = $this->data_karyawan->np_karyawan;
                    $data_insert['updated_by_nama'] = $this->data_karyawan->nama;
                    $data_insert['updated_by_jabatan'] = $this->data_karyawan->nama_jabatan_singkat;
                    $data_insert['updated_by_kode_unit'] = $this->data_karyawan->kode_unit;
                    
                    if(@$this->post('milestone_kode'))
                        $data_insert['milestone_kode'] = $this->post('milestone_kode');
                    
                    if(@$this->post('start_date')!='')
                        $data_insert['start_date'] = $this->post('start_date');
                    
                    if(@$this->post('end_date')!='')
                        $data_insert['end_date_fix'] = $this->post('end_date');
                    
                    $this->db->where( 'id',$this->post('id') )->update( $this->current_table, $data_insert );
                    
                    if($this->db->affected_rows()>0){
                        $this->response([
                            'status'=>true,
                            'message'=>'Berhasil disimpan',
                            'data'=>$data_insert
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
