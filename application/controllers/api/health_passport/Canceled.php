<?php defined('BASEPATH') OR exit('No direct script access allowed');
include_once( APPPATH . 'core/Group_Controller.php' );
class Canceled extends Group_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->model('api/health_passport/M_kuesioner_api','kuesioner');
    }
    
    function index_get(){
        $np = $this->data_karyawan->np_karyawan;
        try {
            if(!empty($this->get('id'))){
                $id = $this->get('id');
                $get = $this->kuesioner->edit($id);
                if($get->num_rows()==1){
                    # cancel
                    $this->db->where('id',$id)->update('ess_self_assesment_covid19',['is_status'=>'0', 'canceled_at'=>date('Y-m-d H:i:s'), 'canceled_by'=>$np]);
                    if($this->db->affected_rows()>0){
                        $this->response([
                            'status'=>true,
                            'message'=>'Success. Data canceled'
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
                        'message'=>'ID not found'
                    ], MY_Controller::HTTP_NOT_FOUND);
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
