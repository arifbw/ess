<?php defined('BASEPATH') OR exit('No direct script access allowed');
include_once( APPPATH . 'core/Group_Controller.php' );
class Info_provider extends Group_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->model("api/sikesper/m_provider");
        
        if(!in_array($this->id_group,[12,4,5])){
            $this->response([
                'status'=>false,
                'message'=>"Hanya bisa diakses pengguna atau pengadministrasi unit kerja",
                'data'=>[]
            ], MY_Controller::HTTP_FORBIDDEN);
        }
	}
	
    
    function index_get($kota=Null) {
        try {
			$data = $this->m_provider->index($kota);
			
			$this->response([
				'status'=>true,
				'message'=>'Data Ditemukan !',
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
