<?php defined('BASEPATH') OR exit('No direct script access allowed');

class List_bawahan extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->model('api/tasklist_atasan/M_tasklist_api','tasklist');
    }
    
    function index_get(){
        $data = [];
        try {
            if(!empty($this->get('tanggal')))
                $params['tanggal'] = $this->get('tanggal');
            else
                $params['tanggal'] = date('Y-m-d');
            $params['np_atasan'] = $this->data_karyawan->np_karyawan;
            
            $no=0;
            $get_bawahan = $this->tasklist->get_bawahan($params);
            foreach($get_bawahan as $tampil){
                $row=[];
                $no++;
                $row['no'] = $no;
                $row['np_karyawan'] = $tampil->np_karyawan;
                $row['nama'] = $tampil->nama;

                $data[]=$row;
            }
            
            $this->response([
                'status'=>true,
                'message'=>'Data Bawahan tanggal '.$params['tanggal'],
                'data'=>$data
            ], MY_Controller::HTTP_OK);
        } catch(Exception $e){
            $this->response([
                'status'=>false,
                'message'=>'Error Exception',
                'data'=>$data
            ], MY_Controller::HTTP_BAD_REQUEST);
        }
    }
    
}
