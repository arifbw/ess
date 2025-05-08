<?php defined('BASEPATH') OR exit('No direct script access allowed');

class All_karyawan extends MY_Controller {
    
    function __construct(){
        parent::__construct();
    }
    
	function index_get() {
        $get = $this->db->select('no_pokok,nama,nama_unit_singkat')->group_by('no_pokok,nama,nama_unit_singkat')->get('mst_karyawan')->result();
        try {
            $this->response([
                'status'=>true,
                'message'=>'Success',
                'data'=>$get
            ], MY_Controller::HTTP_OK);
        } catch(Exception $e){
            $this->response([
                'status'=>false,
                'message'=>'Not found',
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
        }
	}
}
