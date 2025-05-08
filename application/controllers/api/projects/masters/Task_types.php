<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Task_types extends MY_Controller {
    
    function __construct(){
        parent::__construct();
    }
    
    function index_get(){
        try {
            $this->response([
                'status'=>true,
                'message'=>'Pilihan jenis task',
                'data'=>[
                    ['value'=>'dir', 'label'=>'Tasklist'],
                    ['value'=>'task', 'label'=>'Activity']
                ]
            ], MY_Controller::HTTP_OK);
        } catch(Exception $e){
            $this->response([
                'status'=>false,
                'message'=>'Error Exception',
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
        }
    }
}
