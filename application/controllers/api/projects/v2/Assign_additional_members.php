<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Assign_additional_members extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->model('api/M_karyawan_api');
    }
    
    function index_post(){
        try {
            if(empty($this->post('tasklist_id'))){
                $this->response([
                    'status'=>false,
                    'message'=>"ID Tasklist harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($this->post('list_np'))){
                $this->response([
                    'status'=>false,
                    'message'=>"NP harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                $cek_tasklist = $this->db->select('a.id,a.project_id, a.created_by_np, b.np,b.is_pm,b.is_pic,b.is_viewer')
                    ->where('a.id',$this->post('tasklist_id'))
                    ->from('ess_project_tasklists a')
                    ->join('ess_project_members b','a.project_id=b.project_id AND b.np="'.$this->data_karyawan->np_karyawan.'"','LEFT')
                    ->get();
                
                if($cek_tasklist->num_rows()==0){
                    $this->response([
                        'status'=>false,
                        'message'=>'Tasklist tidak ditemukan',
                        'data'=>[]
                    ], MY_Controller::HTTP_BAD_REQUEST);
                } else{
                    $row_tasklist = $cek_tasklist->row();
                    if($row_tasklist->is_pm!='1' && $row_tasklist->created_by_np!=$this->data_karyawan->np_karyawan){
                        $this->response([
                            'status'=>false,
                            'message'=>'Tidak bisa menambahkan member. Anda bukan PM atau Pembuat Tasklist ',
                            'data'=>[]
                        ], MY_Controller::HTTP_BAD_REQUEST);
                    }
                    
                    # get member project
                    $project_members = $this->db->select('GROUP_CONCAT(np) as nps')->where('project_id',$row_tasklist->project_id)->where('deleted_at is null',null,false)->get('ess_project_members')->row();
                    $arr_project_members = explode(',',$project_members->nps);
                    
                    $inserted_members=[];
                    $uninserted_members=[];
                    
                    foreach( $this->post('list_np') as $row ){  
                        $get_data_karyawan = $this->M_karyawan_api->get_profil($row);
                        $params = [ 'tasklist_id'=>$this->post('tasklist_id'), 'np'=>$row ];
                        $cek = $this->db->where($params)->get('ess_project_tasklist_members');
                        if($cek->num_rows()==0){
                            
                            $data_insert = [];
                            $data_insert['kode'] = $this->uuid->v4();
                            $data_insert['tasklist_id'] = $this->post('tasklist_id');
                            $data_insert['np'] = $row;
                            $data_insert['nama'] = @$get_data_karyawan['nama'] ? $get_data_karyawan['nama']:null;
                            $data_insert['jabatan'] = @$get_data_karyawan['nama_jabatan_singkat'] ? $get_data_karyawan['nama_jabatan_singkat']:null;
                            $data_insert['kode_unit'] = @$get_data_karyawan['kode_unit'] ? $get_data_karyawan['kode_unit']:null;
                            $data_insert['nama_unit'] = @$get_data_karyawan['nama_unit_singkat'] ? $get_data_karyawan['nama_unit_singkat']:null;
                            
                            $data_insert['is_additional_member'] = in_array($row, $arr_project_members) ? '0':'1';
                            
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
                        'status'=>true,
                        'message'=>'Member tambahan telah disimpan',
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
