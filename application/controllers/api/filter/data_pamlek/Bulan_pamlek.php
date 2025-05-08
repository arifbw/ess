<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Bulan_pamlek extends MY_Controller {
    
    function __construct(){
        parent::__construct();
    }
    
	function index_get() {
        $data = [];
        try {
			$query = periode();
			foreach ($query as $row) {
				$data[] = [
                    'label'=>$row['text'],
                    'value'=>str_replace('_','-',$row['value'])
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
