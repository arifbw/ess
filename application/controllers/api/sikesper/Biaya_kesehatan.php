<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Biaya_kesehatan extends MY_Controller {
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
        
        $data = $this->db->select("bill_no,np_karyawan,nama_pegawai,nama_vendor,tgl_berobat,deskripsi_periksa, sum(jumlah_hari) as total_hari, sum(beban_karyawan) as total_beban_karyawan, sum(tanggungan_karyawan) as total_tanggungan_karyawan, sum(tanggungan_perusahaan) as total_tanggungan_perusahaan, (CASE WHEN status='Accounted' THEN 'Disetujui' WHEN status='To be accounted' THEN 'Dalam proses' ELSE '' END) as status")->where('np_karyawan',$np)->group_by('np_karyawan, bill_no, tgl_berobat')->order_by('tgl_berobat','desc')->get('ess_biaya_kesehatan')->result();
        
        $this->response([
            'status'=>true,
            'message'=>'Data Biaya Kesehatan '.$nama,
            'data'=>$data
        ], MY_Controller::HTTP_OK);
    }
}
