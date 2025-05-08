<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Add extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->model('api/filter/M_filter_api','filter');
        $this->load->model('api/tasklist_atasan/M_tasklist_api','tasklist');
    }
    
    function index_post(){
        $data = [];
        $data_insert = [];
        $created_by = $this->data_karyawan->np_karyawan;
        
        try {
            if(empty($this->post('tanggal'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Tanggal harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($this->post('target_pekerjaan'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Target pekerjaan harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($this->post('np'))){
                $this->response([
                    'status'=>false,
                    'message'=>"NP atasan harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                
                # validasi np
                $get_data_karyawan = $this->filter->get_data_karyawan_by_np($this->post('np'));
                if($get_data_karyawan->num_rows()<1){
                    $this->response([
                        'status'=>false,
                        'message'=>"NP tidak ditemukan",
                        'data'=>[]
                    ], MY_Controller::HTTP_BAD_REQUEST); exit;
                }
                $data_karyawan = $get_data_karyawan->result()[0];                                                     
                
                # validasi tanggal
                if(date('Y-m-d')>$this->post('tanggal')){
                    $this->response([
                        'status'=>false,
                        'message'=>"Tanggal tidak bisa back date",
                        'data'=>[]
                    ], MY_Controller::HTTP_BAD_REQUEST); exit;
                }
                
                $data_insert['kode'] = $this->uuid->v4();
                $data_insert['np_karyawan'] = $data_karyawan->no_pokok;
                $data_insert['personel_number'] = $data_karyawan->personnel_number;
                $data_insert['nama'] = $data_karyawan->nama;
                $data_insert['nama_jabatan'] = $data_karyawan->nama_jabatan;
                $data_insert['kode_unit'] = $data_karyawan->kode_unit;
                $data_insert['nama_unit'] = $data_karyawan->nama_unit;
                $data_insert['tanggal'] = $this->post('tanggal');
                $data_insert['target_pekerjaan'] = $this->post('target_pekerjaan');
                $data_insert['created_at'] = date('Y-m-d H:i:s');
                $data_insert['created_by'] = $created_by;
                $data_insert['tipe'] = 'task';
                $data_insert['np_atasan'] = $this->data_karyawan->np_karyawan;
                $data_insert['nama_atasan'] = $this->data_karyawan->nama;
                $data_insert['kode_unit_atasan'] = $this->data_karyawan->kode_unit;
                $data_insert['nama_jabatan_atasan'] = $this->data_karyawan->nama_jabatan;
                
                $this->db->insert('ess_performance_management', $data_insert);

                if($this->db->affected_rows()>0){
                    $this->response([
                        'status'=>true,
                        'message'=>'Berhasil disimpan',
                        'data'=>$data_insert
                    ], MY_Controller::HTTP_OK);
                } else{
                    $this->response([
                        'status'=>false,
                        'message'=>'Gagal menambahkan list baru',
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
