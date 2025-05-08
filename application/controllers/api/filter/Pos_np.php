<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Pos_np extends MY_Controller {
    
    function __construct(){
        parent::__construct();
    }
    
	function index_get() {
        $np = $this->data_karyawan->np_karyawan;
       //$data = $this->db->where('status','1')->where("FIND_IN_SET('$np',no_pokok) !=", 0)->get('mst_pos')->result_array();
        //open
		$data = $this->db->where('status','1')->get('mst_pos')->result_array();

        $this->response([
            'status'=>true,
            'message'=>"List Pos untuk NP {$np}",
            'data'=>$data
        ], MY_Controller::HTTP_OK);
	}
}
