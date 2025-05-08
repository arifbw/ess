<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Data extends MY_Controller {
    
    function __construct(){
        parent::__construct();
    }
    
    function index_get(){
        $data = $this->db
            ->where('created_by_np', $this->data_karyawan->np_karyawan)
            ->where('deleted_at IS NULL', null, false)
            ->order_by('created_at','DESC')
            ->get('mobile_pengumuman')
            ->result();
        
        $this->response([
            'status'=>true,
            'message'=>'List Pengumuman.',
            'data'=>$data
        ], MY_Controller::HTTP_OK);
    }
}
