<?php defined('BASEPATH') OR exit('No direct script access allowed');
include_once( APPPATH . 'core/Group_Controller.php' );
class Data extends Group_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->model('api/health_passport/M_kuesioner_api','kuesioner');
    }
    
	function index_get() {
        $np = $this->data_karyawan->np_karyawan;
        $_get_month = $this->get('bulan');
        $month = @$_get_month!='' ? $_get_month : date('Y-m');
        $data = [];
        
        try {
            
            if($this->id_group==4) {
                $var = [];
                foreach ($this->list_pengadministrasi as $row) {
                    $var[] = $row['kode_unit'];
                }
                $get = $this->kuesioner->get_data(['grup'=>4, 'var'=>$var, 'month'=>$month]);
            } else if($this->id_group==5) {
                $get = $this->kuesioner->get_data(['grup'=>5, 'var'=>$np, 'month'=>$month]);
            } else{
                $get = $this->kuesioner->get_data(['grup'=>null, 'var'=>$np, 'month'=>$month]);
            }
            
            foreach($get->result_array() as $row){
                $row['status'] = $row['is_status']=='1' ? 'Aktif':'Dibatalkan';
                
                if(date('Y-m-d',strtotime($row['created_at']))==date('Y-m-d')){
                    $row['editable'] = $row['is_status']=='1' ? true : false;
                } else{
                    $row['editable'] = false;
                }
                $data[] = $row;
            }
            
            $this->response([
                'status'=>true,
                'message'=>'Success',
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
