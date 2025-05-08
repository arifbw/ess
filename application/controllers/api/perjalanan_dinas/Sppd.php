<?php defined('BASEPATH') OR exit('No direct script access allowed');
include_once( APPPATH . 'core/Group_Controller.php' );
class Sppd extends Group_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->helper("tanggal_helper");
        $this->load->model("api/perjalanan_dinas/M_sppd_api","sppd");
    }
    
    function index_get(){
        $data=[];
        $params=[];
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
                
                if($this->id_group==5){
                    $params['np'] = [$this->data_karyawan->np_karyawan];
                } else if($this->id_group==4){
                    $list = [];
                    foreach($this->list_pengadministrasi as $l){
                        $list[] = $l['kode_unit'];
                    }
                    $params['kode_unit'] = $list;
                }
                
                $no=0;
                $get_data_sppd = $this->sppd->get_sppd($params)->result();
                foreach($get_data_sppd as $tampil){
                    $row=[];
                    $no++;
                    $row['no'] = $no;
                    $row['np_karyawan'] = $tampil->np_karyawan;
                    $row['nama'] = $tampil->nama;
                    $row['perihal'] = $tampil->perihal;
                    $row['tipe_perjalanan'] = $tampil->tipe_perjalanan;
                    $row['tgl_berangkat'] = tanggal_indonesia($tampil->tgl_berangkat);
                    $row['tgl_pulang'] = tanggal_indonesia($tampil->tgl_pulang);
                    
                    $data[]=$row;
                }
                
                $this->response([
                    'status'=>true,
                    'message'=>'Data SPPD bulan '.id_to_bulan($bulan)." ".$tahun,
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
    }
}
