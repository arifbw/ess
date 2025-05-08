<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Upload_evidence extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->model('api/tasklist/M_tasklist_api','tasklist');
    }
    
    function index_post(){
        $data_insert = [];
        try {
            if(empty($this->post('id'))){
                $this->response([
                    'status'=>false,
                    'message'=>'ID harus diisi'
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($_FILES['evidence'])){
                $this->response([
                    'status'=>false,
                    'message'=>'Anda belum menyertakan gambar/foto'
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                $id = $this->post('id');
                $get_data = $this->db->select('id,kode,np_karyawan')->where('id',$id)->get('ess_performance_management');
                if($get_data->num_rows()!=1){
                    $this->response([
                        'status'=>false,
                        'message'=>'Tasklist not found.'
                    ], MY_Controller::HTTP_BAD_REQUEST);
                }
                
                $row = $get_data->row();
                
                if($_FILES['evidence']['size'] > 5242880){
                    $this->response([
                        'status'=>false,
                        'message'=>'The file you are attempting to upload is larger than the permitted size.'
                    ], MY_Controller::HTTP_BAD_REQUEST);
                } else{
                    $dir = 'uploads/tasklist/';
                    
                    if(!is_dir('./'.$dir.$row->np_karyawan)){
                        mkdir('./'.$dir.$row->np_karyawan, 0777, true);
                    }
                    
                    $explode_file_name = explode('.',$_FILES['evidence']['name']);
                    $ext = $explode_file_name[(count($explode_file_name)-1)];
                    
                    $filename = $row->kode.'_'.date('Ymd_His').'.'.$ext; //.'_'.$_FILES['evidence']['name'];
                    
                    $this->load->library('upload');
                    $config['upload_path'] = './'.$dir.$row->np_karyawan;
                    $config['file_name'] = $filename;
                    $config['allowed_types'] = 'jpg|jpeg|png';
                    $config['overwrite'] = true;
                    $config['max_size'] = 5*1024; // 5Mb
                    $this->upload->initialize($config);
                    if ($this->upload->do_upload('evidence')) {
                        
                        $data_insert['evidence'] = $dir.$row->np_karyawan.'/'.$filename;
                        $data_insert['uploaded_at'] = date('Y-m-d H:i:s');
                        
                        $this->db->where('id',$id)->update('ess_performance_management',$data_insert);
                        
                        $this->response([
                            'status'=>true,
                            'message'=>'Uploaded',
                            'file_name_asli'=>$_FILES['evidence']['name'],
                            'file_name_simpan'=>$filename
                        ], MY_Controller::HTTP_OK);
                    } else{
                        $this->response([
                            'status'=>false,
                            'message'=>$this->upload->display_errors()
                        ], MY_Controller::HTTP_BAD_REQUEST);
                    }
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
