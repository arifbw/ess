<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Ambil_bawahan extends MY_Controller {
    
    function __construct(){
        parent::__construct();
    }
    
    function index_get(){
        $np = $this->data_karyawan->np_karyawan;
        $kode_unit = $this->data_karyawan->kode_unit;
        $kode_jabatan_3digit_terakhir = substr($this->data_karyawan->kode_jabatan, -3);
        $arr_atasan = ['100','200','300','400','600','700'];
        
        # heru PDS menambahkan filter jabatan, 2021-02-12
        if( !in_array($kode_jabatan_3digit_terakhir,$arr_atasan) ){
            $this->response([
                'status'=>true,
                'message'=>'List bawahan',
                'data'=>$data
            ], MY_Controller::HTTP_OK);
        }
        
        if(substr($kode_unit,4,1)!='0'){
            $pos = 5;
        } else if(substr($kode_unit,3,1)!='0' && substr($kode_unit,4,1)=='0'){
            $pos = 4;
        } else if(substr($kode_unit,2,1)!='0' && substr($kode_unit,3,2)=='00'){
            $pos = 3;
        } else if(substr($kode_unit,1,1)!='0' && substr($kode_unit,2,3)=='000'){
            $pos = 2;
        } else if(substr($kode_unit,0,1)!='0' && substr($kode_unit,1,4)=='0000'){
            $pos = 1;
        } else{
            $pos = 0;
        }
        
        if($pos!=0)
            $data = $this->db->select('np_karyawan, nama, kode_unit, nama_unit')
            //->where('kode_unit <>',$kode_unit)
            ->where('np_karyawan <>',$np) # heru PDS ganti where by NP, 2021-02-12
            ->like('kode_unit',substr($kode_unit,0,$pos),'AFTER')->group_by('np_karyawan')->get('erp_master_data_'.date('Y_m'))->result();
        else
            $data = [];
        
        $this->response([
            'status'=>true,
            'message'=>'List bawahan',
            'data'=>$data
        ], MY_Controller::HTTP_OK);
    }
}
