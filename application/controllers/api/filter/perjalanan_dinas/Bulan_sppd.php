<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Bulan_sppd extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->helper("tanggal_helper");
        $this->load->model("api/filter/M_filter_api","filter");
    }
    
	function index_get() {
        $data = [];
        
        try {

			$query = $this->filter->sppd_bulan()->result_array();
			foreach ($query as $row) {
				$bulan = substr($row['tahun_bulan'],-2);
				$tahun = substr($row['tahun_bulan'],0,4);				
				
				$data[] = [
                    'label'=>id_to_bulan($bulan)." ".$tahun,
                    'value'=>$row['tahun_bulan']
                ];
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
