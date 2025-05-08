<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Ganti_atasan extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->model('api/filter/M_filter_api','filter');
    }
    
    function index_post(){
        $data_insert = [];
        try {
            if(empty($this->post('tanggal'))){
                $this->response([
                    'status'=>false,
                    'message'=>'Tanggal harus diisi'
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($this->post('np_atasan'))){
                $this->response([
                    'status'=>false,
                    'message'=>'NP atasan harus diisi'
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                
                # validasi np atasan
                $get_data_karyawan = $this->filter->get_data_karyawan_by_np($this->post('np_atasan'));
                if($get_data_karyawan->num_rows()<1){
                    $this->response([
                        'status'=>false,
                        'message'=>"NP atasan tidak ditemukan",
                        'data'=>[]
                    ], MY_Controller::HTTP_BAD_REQUEST); exit;
                }
                $data_karyawan = $get_data_karyawan->result()[0];  
                
                $data_insert['np_atasan'] = $data_karyawan->no_pokok;
                $data_insert['nama_atasan'] = $data_karyawan->nama;
                $data_insert['kode_unit_atasan'] = $data_karyawan->kode_unit;
                $data_insert['nama_jabatan_atasan'] = $data_karyawan->nama_jabatan;
                $data_insert['updated_at'] = date('Y-m-d H:i:s');
                
                $this->db->where(['tanggal'=>$this->post('tanggal'), 'np_karyawan'=>$this->data_karyawan->np_karyawan])->update('ess_performance_management',$data_insert);
                if($this->db->affected_rows()>0){
                    $this->response([
                        'status'=>true,
                        'message'=>'Atasan telah berhasil diganti',
                        'data'=>$data_insert
                    ], MY_Controller::HTTP_OK);
                } else{
                    $this->response([
                        'status'=>false,
                        'message'=>'Failed',
                        'data'=>$data_insert
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
