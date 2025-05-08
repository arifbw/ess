<?php defined('BASEPATH') OR exit('No direct script access allowed');
include_once( APPPATH . 'core/Group_Controller.php' );
class Edit extends Group_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->model('api/health_passport/M_kuesioner_api','kuesioner');
    }
    
    function index_get(){
        $data = [];
        try {
            if(!empty($this->get('id'))){
                $id = $this->get('id');
                $get = $this->kuesioner->edit($id);
                if($get->num_rows()==1){
                    $this->response([
                        'status'=>true,
                        'message'=>'Success. Data updated',
                        'data'=>$get->row()
                    ], MY_Controller::HTTP_FOUND);
                } else{
                    $this->response([
                        'status'=>false,
                        'message'=>'ID not found',
                        'data'=>$data
                    ], MY_Controller::HTTP_NOT_FOUND);
                }
            } else{
                $this->response([
                    'status'=>false,
                    'message'=>'ID is required',
                    'data'=>$data
                ], MY_Controller::HTTP_BAD_REQUEST);
            }
        } catch(Exception $e){
            $this->response([
                'status'=>false,
                'message'=>'Error Exception',
                'data'=>$data
            ], MY_Controller::HTTP_BAD_REQUEST);
        }
    }
    
    function index_post(){
        $data_insert = [];
        try {
            if(!empty($this->post('id'))){
                $id = $this->post('id');
                $data_insert['updated_at'] = date('Y-m-d H:i:s');
                $kuesioner = ['pernah_keluar','transportasi_umum','luar_kota','kegiatan_orang_banyak','kontak_pasien','sakit'];
                
                foreach($kuesioner as $item){
                    if(empty($this->post($item))){
                        $this->response([
                            'status'=>false,
                            'message'=>'Kuesioner belum lengkap'
                        ], MY_Controller::HTTP_BAD_REQUEST);
                        exit;
                    } else{
                        $data_insert[$item] = $this->post($item);
                    }
                }
                
                $this->db->where('id',$id)->update('ess_self_assesment_covid19',$data_insert);
                if($this->db->affected_rows()>0){
                    $this->response([
                        'status'=>true,
                        'message'=>'Success'
                    ], MY_Controller::HTTP_OK);
                } else{
                    $this->response([
                        'status'=>false,
                        'message'=>'Failed to update'
                    ], MY_Controller::HTTP_BAD_REQUEST);
                }
            } else{
                $this->response([
                    'status'=>false,
                    'message'=>'ID is required'
                ], MY_Controller::HTTP_BAD_REQUEST);
            }
        } catch(Exception $e){
            $this->response([
                'status'=>false,
                'message'=>'Error Exception'
            ], MY_Controller::HTTP_BAD_REQUEST);
        }
    }
}
