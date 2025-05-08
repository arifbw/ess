<?php defined('BASEPATH') OR exit('No direct script access allowed');
include_once( APPPATH . 'core/Group_Controller.php' );
class Data_pamlek extends Group_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->model("api/informasi/m_data_pamlek");
        $this->load->model("m_setting");
        
        if(!in_array($this->id_group,[5])){
            $this->response([
                'status'=>false,
                'message'=>"Hanya bisa diakses pengguna",
                'data'=>[]
            ], MY_Controller::HTTP_FORBIDDEN);
        }
    }

    function index_post() {
        $data=[];
        $params=[];
        try {
            if(empty($this->post())){
                $this->response([
                    'status'=>false,
                    'message'=>"Data harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                $periode = $this->post('periode'); //2020_11
                $np = $this->post('np'); //7648

                $mesin_perizinan = "'".str_replace(",","','",$this->m_setting->ambil_pengaturan("mesin perizinan"))."'";

                $list = $this->m_data_pamlek->get_all($periode,$np,$mesin_perizinan);
                $data = array();
                $no = 0;
                foreach ($list as $tampil) {
                    $no++;
                    $row = array();
                    $row[] = $no;           
                    $row[] = $tampil->jenis;
                    $row[] = $tampil->tipe;
                    $row[] = $tampil->machine_id;
                    $row[] = $tampil->tapping_time;
                    $row[] = tanggal_waktu($tampil->tapping_time);
                                    
                    $data[] = $row;
                }

                $this->response([
                    'status'=>true,
                    'message'=>'Data Pamlek',
                    'data'=>$data
                ], MY_Controller::HTTP_OK);
            
                // $this->load->view($this->folder_view."ajax_rekapitulasi_bulanan",$this->data);
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
