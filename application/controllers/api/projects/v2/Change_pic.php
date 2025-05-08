<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Change_pic extends MY_Controller {
    
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
            } else if(empty($this->post('np'))){
                $this->response([
                    'status'=>false,
                    'message'=>"NP PIC harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                $cek_tasklist = $this->db->where('id',$this->post('activity_id'))->get('ess_project_tasklists');
                
                if($cek_tasklist->num_rows()==0){
                    $this->response([
                        'status'=>false,
                        'message'=>'Activity tidak ditemukan',
                        'data'=>[]
                    ], MY_Controller::HTTP_BAD_REQUEST);
                } else{
                    $data_insert = [];
                    $np = $this->post('np');
                    $get_data_karyawan = $this->M_karyawan_api->get_profil($np);
                    
                    $cek_exist = $this->db->where( [ 'tasklist_id'=>$this->post('activity_id'), 'np'=>$np ] )->get('ess_project_tasklist_members');
                    if($cek_exist->num_rows()==0){ # jika input baru
                        $data_insert['kode'] = $this->uuid->v4();
                        $data_insert['tasklist_id'] = $this->post('activity_id');
                        $data_insert['np'] = $np;
                        $data_insert['nama'] = @$get_data_karyawan['nama'] ? $get_data_karyawan['nama']:null;
                        $data_insert['jabatan'] = @$get_data_karyawan['nama_jabatan_singkat'] ? $get_data_karyawan['nama_jabatan_singkat']:null;
                        $data_insert['kode_unit'] = @$get_data_karyawan['kode_unit'] ? $get_data_karyawan['kode_unit']:null;
                        $data_insert['nama_unit'] = @$get_data_karyawan['nama_unit_singkat'] ? $get_data_karyawan['nama_unit_singkat']:null;
                        $data_insert['is_pic'] = '1';
                        
                        $data_insert['created_at'] = date('Y-m-d H:i:s');
                        $data_insert['created_by_np'] = $this->data_karyawan->np_karyawan;
                        $data_insert['created_by_nama'] = $this->data_karyawan->nama;
                        $data_insert['created_by_jabatan'] = $this->data_karyawan->nama_jabatan_singkat;
                        $data_insert['created_by_kode_unit'] = $this->data_karyawan->kode_unit;
                        $data_insert['created_by_nama_unit'] = $this->data_karyawan->nama_unit_singkat;
                        
                        $this->db->insert('ess_project_tasklist_members',$data_insert);
                        $data=$data_insert;
                        
                    } else{ # jika pic diambil dari member
                        $row_exist = $cek_exist->row();
                        
                        # delete pic sebelumnya, jadikan member
                        $this->db->where( ['tasklist_id'=>$this->post('activity_id'),'is_pic'=>'1'] )->update('ess_project_tasklist_members', [
                            'is_pic'=>'0',
                            'updated_at'=>date('Y-m-d H:i:s'),
                            'updated_by_np'=>$this->data_karyawan->np_karyawan,
                            'updated_by_nama'=>$this->data_karyawan->nama,
                            'updated_by_jabatan'=>$this->data_karyawan->nama_jabatan_singkat,
                            'updated_by_kode_unit'=>$this->data_karyawan->kode_unit,
                            'updated_by_nama_unit'=>$this->data_karyawan->nama_unit
                        ]);
                        
                        # assign new pic
                        $this->db->where( ['tasklist_id'=>$this->post('activity_id'),'np'=>$np] )->update('ess_project_tasklist_members', [
                            'is_pic'=>'1',
                            'updated_at'=>date('Y-m-d H:i:s'),
                            'updated_by_np'=>$this->data_karyawan->np_karyawan,
                            'updated_by_nama'=>$this->data_karyawan->nama,
                            'updated_by_jabatan'=>$this->data_karyawan->nama_jabatan_singkat,
                            'updated_by_kode_unit'=>$this->data_karyawan->kode_unit,
                            'updated_by_nama_unit'=>$this->data_karyawan->nama_unit
                        ]);
                        
                        $data = [
                            'np'=>$np, 'nama'=>$row_exist->nama, 'jabatan'=>$row_exist->jabatan, 'kode_unit'=>$row_exist->kode_unit, 'nama_unit'=>$row_exist->nama_unit
                        ];
                    }
                    
                    $this->response([
                        'status'=>true,
                        'message'=>'PIC telah disimpan',
                        'data'=>$data
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
