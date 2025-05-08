<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Riwayat_kacamata extends MY_Controller {
    public function __construct(){
        parent::__construct();
    }

    function index_get() {
        if(@$this->get('np')){
            $data_karyawan = $this->db->where('no_pokok',$this->get('np'))->get('mst_karyawan');
            if($data_karyawan->num_rows()!=1){
                $this->response([
                    'status'=>false,
                    'message'=>"NP tidak ditemukan",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            }
            $np = $this->get('np');
            $nama = $data_karyawan->row()->nama;
        } else{
            $np = $this->data_karyawan->np_karyawan;
            $nama = $this->data_karyawan->nama;
        }
        
        $data = $this->db->where('np_karyawan',$np)->where('task_type', 'Klaim Kacamata Kary')->get('ess_riwayat_kacamata')->result();
        
        $this->response([
            'status'=>true,
            'message'=>'Data Riwayat Kacamata '.$nama,
            'data'=>$data
        ], MY_Controller::HTTP_OK);
    }
}
