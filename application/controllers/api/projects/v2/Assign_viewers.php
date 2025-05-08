<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Assign_viewers extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->model('api/M_karyawan_api');
    }
    
    function index_post(){
        try {
            if(empty($this->post('project_id'))){
                $this->response([
                    'status'=>false,
                    'message'=>"ID Project harus diisi",
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
                        'message'=>'Project tidak ditemukan',
                        'data'=>[]
                    ], MY_Controller::HTTP_BAD_REQUEST);
                } else{
                    
                    $inserted_members=[];
                    $uninserted_members=[];
                    
                    foreach( $this->post('list_np') as $row ){
                        $get_data_karyawan = $this->M_karyawan_api->get_profil($row);
                        $params = [ 'project_id'=>$this->post('project_id'), 'np'=>$row ];
                        $cek = $this->db->where($params)->get('ess_project_members');
                        if($cek->num_rows()==0){
                            
                            $data_insert = [];
                            $data_insert['kode'] = $this->uuid->v4();
                            $data_insert['project_id'] = $this->post('project_id');
                            $data_insert['np'] = $row;
                            $data_insert['nama'] = @$get_data_karyawan['nama'] ? $get_data_karyawan['nama']:null;
                            $data_insert['jabatan'] = @$get_data_karyawan['nama_jabatan_singkat'] ? $get_data_karyawan['nama_jabatan_singkat']:null;
                            $data_insert['kode_unit'] = @$get_data_karyawan['kode_unit'] ? $get_data_karyawan['kode_unit']:null;
                            $data_insert['nama_unit'] = @$get_data_karyawan['nama_unit_singkat'] ? $get_data_karyawan['nama_unit_singkat']:null;
                            
                            $data_insert['is_viewer'] = '1';
                            
                            $data_insert['created_at'] = date('Y-m-d H:i:s');
                            $data_insert['created_by_np'] = $this->data_karyawan->np_karyawan;
                            $data_insert['created_by_nama'] = $this->data_karyawan->nama;
                            $data_insert['created_by_jabatan'] = $this->data_karyawan->nama_jabatan_singkat;
                            $data_insert['created_by_kode_unit'] = $this->data_karyawan->kode_unit;
                            $data_insert['created_by_nama_unit'] = $this->data_karyawan->nama_unit_singkat;
                            
                            $this->db->insert('ess_project_members',$data_insert);
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
                        'status'=>true,
                        'message'=>'Viewer telah disimpan',
                        'data'=>[
                            'inserted'=>$inserted_members,
                            'not_inserted'=>$uninserted_members
                        ]
                    ], MY_Controller::HTTP_OK);
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
