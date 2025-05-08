<?php defined('BASEPATH') OR exit('No direct script access allowed');
include_once( APPPATH . 'core/Group_Controller.php' );
class Edukasi_kesehatan extends Group_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->model("api/sikesper/m_provider");
        
        if(!in_array($this->id_group,[4,5])){
            $this->response([
                'status'=>false,
                'message'=>"Hanya bisa diakses pengguna atau pengadministrasi unit kerja",
                'data'=>[]
            ], MY_Controller::HTTP_FORBIDDEN);
        }
	}
	
    
    function index_get($kota=Null) {
        try {
			$this->response([
				'status'=>true,
				'message'=>'Data Ditemukan !',
				'data'=>$this->feed()
			], MY_Controller::HTTP_OK);
        } catch(Exception $e){
            $this->response([
                'status'=>false,
                'message'=>'Error Exception',
                'data'=>$data
            ], MY_Controller::HTTP_BAD_REQUEST);
        }
	}
	
	public function feed()
	{
		$json   = file_get_contents(base_url('uploads/rss.xml'));
		$xml	= simplexml_load_string($json, 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NOBLANKS);
		$data 	= json_encode($xml->channel);
		$data 	= json_decode($data, TRUE); 
		
		return $data['item'];
	}
}
?>