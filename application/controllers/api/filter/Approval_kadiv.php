<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Approval_kadiv extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->helper("fungsi_helper");
        $this->load->model("M_approval");
        $this->load->model("master_data/M_karyawan");
    }
    
    function index_get(){
        $data = [];
        try {
            if(empty($this->get('np'))){
                $this->response([
                    'status'=>false,
                    'message'=>"NP harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                $get_data_karyawan = $this->M_karyawan->get_karyawan($this->get('np'));
                $kode_unit = $get_data_karyawan['kode_unit'];
                $level_unit = level_unit($kode_unit);
                
                $get_approver = $this->M_approval->list_atasan_minimal_kadiv([$kode_unit],$this->get('np'));
                
                if(in_array($level_unit,[3,4,5])){
                    $data = $get_approver;
                } else{
                    foreach($get_approver as $r){
                        $push = false;
                        if($level_unit==2){
                            if($get_data_karyawan['grup_jabatan']=='KADIV' || substr($get_data_karyawan['kode_jabatan'],-3)=='300'){
                                $push = level_unit($r['kode_unit'])<=1 ? true:false;
                            } else{
                                $push = level_unit($r['kode_unit'])<=2 ? true:false;
                            }
                        } else if($level_unit==1){
                            if(in_array(substr($get_data_karyawan['kode_jabatan'],-3),['200','100'])){
                                $push = level_unit($r['kode_unit'])<=1 ? true:false;
                            }
                        } 
                        
                        if($push==true){
                            $row=[];
                            $row['no_pokok'] = $r['no_pokok'];
                            $row['nama'] = $r['nama'];
                            $row['kode_unit'] = $r['kode_unit'];
                            $row['nama_jabatan'] = $r['nama_jabatan'];
                            $row['nama_unit'] = $r['nama_unit'];
                            $row['nama_unit_singkat'] = $r['nama_unit_singkat'];
                            $data[] = $row;
                        }
                    }
                }
                
                $this->response([
                    'status'=>true,
                    'message'=>'Approval minimal Kadiv',
                    'data'=>$data
                ], MY_Controller::HTTP_OK);
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
