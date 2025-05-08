<?php defined('BASEPATH') OR exit('No direct script access allowed');
include_once( APPPATH . 'core/Group_Controller.php' );
class Data extends Group_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->helper("tanggal_helper");
        $this->load->model("m_setting");
        $this->load->model("api/data_pamlek/M_pamlek_api","pamlek");
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
            } else if(empty($this->get('np'))){
                $this->response([
                    'status'=>false,
                    'message'=>"NP harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                $bulan = substr($this->get('bulan'),-2);
				$tahun = substr($this->get('bulan'),0,4);
                
                $params['tahun_bulan'] = str_replace('-','_',$this->get('bulan'));
                $params['np'] = $this->get('np');
                
                $mesin_perizinan = "'".str_replace(",","','",$this->m_setting->ambil_pengaturan("mesin perizinan"))."'";
                $params['mesin_perizinan'] = $mesin_perizinan;
                
                $no=0;
                $get_data_pamlek = $this->pamlek->get_pamlek($params)->result();
                foreach($get_data_pamlek as $tampil){
                    $row=[];
                    $no++;
                    $row['no'] = $no;
                    $row['jenis'] = $tampil->jenis;
                    $row['tipe'] = $tampil->tipe;
                    $row['machine_id'] = $tampil->machine_id;
                    $row['waktu'] = tanggal_waktu($tampil->tapping_time);
                    
                    $data[]=$row;
                }
                
                $this->response([
                    'status'=>true,
                    'message'=>'Data Pamlek bulan '.id_to_bulan($bulan)." ".$tahun,
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
