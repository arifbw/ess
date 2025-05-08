<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Assign_activity_member extends MY_Controller {
    
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
                $cek_activity = $this->db->select('a.id,a.parent_id,a.task_name,b.np,b.is_pic,b.is_additional_member, (SELECT np FROM ess_project_tasklist_members WHERE tasklist_id=a.id AND is_pic="1") as np_pic')
                    ->where('a.id',$this->post('activity_id'))->where('a.task_type','task')
                    ->from('ess_project_tasklists a')
                    ->join('ess_project_tasklist_members b','a.id=b.tasklist_id AND b.np="'.$this->data_karyawan->np_karyawan.'"','LEFT')
                    ->get();
                
                if($cek_activity->num_rows()==1){
                    $row_activity = $cek_activity->row();
                    if($row_activity->is_pic!='1'){
                        $this->response([
                            'status'=>false,
                            'message'=>'Tidak bisa menambahkan member. Anda bukan PIC.'
                        ], MY_Controller::HTTP_BAD_REQUEST);
                    }
                    
                    $tasklist_members = $this->db->select('GROUP_CONCAT(np) as np')->where('tasklist_id',$row_activity->parent_id)->get('ess_project_tasklist_members')->row_array();
                    $tasklist_members = explode(",",$tasklist_members['np']);
                    
                    $inserted_members=[];
                    $uninserted_members=[];
                    
                    foreach( $this->post('list_np') as $row ){  
                        $get_data_karyawan = $this->M_karyawan_api->get_profil($row);
                        $params = [ 'tasklist_id'=>$this->post('activity_id'), 'np'=>$row ];
                        $cek = $this->db->where($params)->get('ess_project_tasklist_members');
                        if(!in_array($row,$tasklist_members)){
                            $uninserted_members[]=[
                                'np'=>$row, 'nama'=>@$get_data_karyawan['nama'] ? $get_data_karyawan['nama']:null
                            ];
                        } else if($cek->num_rows()==0){
                            $data_insert = [];
                            $data_insert['kode'] = $this->uuid->v4();
                            $data_insert['tasklist_id'] = $this->post('activity_id');
                            $data_insert['np'] = $row;
                            $data_insert['nama'] = @$get_data_karyawan['nama'] ? $get_data_karyawan['nama']:null;
                            $data_insert['jabatan'] = @$get_data_karyawan['nama_jabatan_singkat'] ? $get_data_karyawan['nama_jabatan_singkat']:null;
                            $data_insert['kode_unit'] = @$get_data_karyawan['kode_unit'] ? $get_data_karyawan['kode_unit']:null;
                            $data_insert['nama_unit'] = @$get_data_karyawan['nama_unit_singkat'] ? $get_data_karyawan['nama_unit_singkat']:null;
                            
                            $data_insert['created_at'] = date('Y-m-d H:i:s');
                            $data_insert['created_by_np'] = $this->data_karyawan->np_karyawan;
                            $data_insert['created_by_nama'] = $this->data_karyawan->nama;
                            $data_insert['created_by_jabatan'] = $this->data_karyawan->nama_jabatan_singkat;
                            $data_insert['created_by_kode_unit'] = $this->data_karyawan->kode_unit;
                            $data_insert['created_by_nama_unit'] = $this->data_karyawan->nama_unit_singkat;
                            
                            $this->db->insert('ess_project_tasklist_members',$data_insert);
                            $inserted_members[] = [
                                'np'=>$row, 'nama'=>@$get_data_karyawan['nama'] ? $get_data_karyawan['nama']:null
                            ];
                        } else{
                            $uninserted_members[]=[
                                'np'=>$row, 'nama'=>@$get_data_karyawan['nama'] ? $get_data_karyawan['nama']:null
                            ];
                        }
                    }
                    
                    $this->response([
                        'status'=>$inserted_members!=[]?true:false,
                        'message'=>$inserted_members!=[]?'Member telah ditambahkan ke Activity':'Member yang Anda tambahkan bukan member tasklist',
                        'data'=>[
                            'inserted'=>$inserted_members,
                            'not_inserted'=>$uninserted_members
                        ]
                    ], MY_Controller::HTTP_OK);
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
