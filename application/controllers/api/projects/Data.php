<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Data extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->helper("tanggal_helper");
        //$this->load->model('api/tasklist2/M_tasklist_api','tasklist');
    }
    
    /*function index_get(){
        $data = [];
        try {
            if(empty($this->get('bulan'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Bulan harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                $bulan = substr($this->get('bulan'),-2);
				$tahun = substr($this->get('bulan'),0,4);
                
                $params['tahun_bulan'] = $this->get('bulan');
                
                $no=0;
                $get_tasklist = $this->tasklist->get_tasklist($params);
                foreach($get_tasklist as $tampil){
                    $row=[];
                    $no++;
                    $row['no'] = $no;
                    $row['tanggal'] = $tampil->tanggal;
                    $row['np_karyawan'] = $tampil->np_karyawan;
                    $row['nama'] = $tampil->nama;
                    $row['nama_jabatan'] = $tampil->nama_jabatan;
                    $row['nama_unit'] = $tampil->nama_unit;
                    $row['total_task'] = $tampil->total_task;
                    $row['total_selesai'] = $tampil->total_selesai;
                    $row['persentase_progres'] = ($tampil->total_selesai/$tampil->total_task * 100).'%';
                    
                    $data[]=$row;
                }
                
                $this->response([
                    'status'=>true,
                    'message'=>'Data Tasklist bulan '.id_to_bulan($bulan)." ".$tahun,
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
    }*/
    
    function index_post(){
        $data = [];
        $data_insert = [];
        $created_by = $this->data_karyawan->np_karyawan;
        
        try {
            if(empty($this->post('project_name'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Nama project harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if( strtolower(trim($this->post('project_name')))=='daily project' ){
                $this->response([
                    'status'=>false,
                    'message'=>"Silakan gunakan nama project selain 'Daily Project' ",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                
                $data_insert['kode'] = $this->uuid->v4();
                $data_insert['project_name'] = $this->post('project_name');
                $data_insert['start_date'] = date('Y-m-d');
                $data_insert['end_date'] = date('Y-m-d');
                $data_insert['created_by_np'] = $this->data_karyawan->np_karyawan;
                $data_insert['created_by_nama'] = $this->data_karyawan->nama;
                $data_insert['created_at'] = date('Y-m-d H:i:s');
                
                $this->db->insert('ess_projects', $data_insert);

                if($this->db->affected_rows()>0){
                    $this->response([
                        'status'=>true,
                        'message'=>'Berhasil disimpan',
                        'data'=>$data_insert
                    ], MY_Controller::HTTP_OK);
                } else{
                    $this->response([
                        'status'=>false,
                        'message'=>'Gagal menambahkan project baru',
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
