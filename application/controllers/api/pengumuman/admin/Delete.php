<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Delete extends MY_Controller {
    
    function __construct(){
        parent::__construct();
    }
    
    function index_post(){
        if(empty($this->post('kode_pengumuman'))){
            $this->response([
                'status'=>false,
                'message'=>"Kode Pengumuman harus diisi",
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
        } else{
            $data=[];
            $data['deleted_at'] = date('Y-m-d H:i:s');
            $this->db->where('kode', $this->post('kode_pengumuman'))->update('mobile_pengumuman', $data);
            $row = $this->db->where('kode', $this->post('kode_pengumuman'))->get('mobile_pengumuman')->row();
            
            $this->response([
                'status'=>true,
                'message'=>'Pengumuman telah dihapus.',
                'data'=>$row
            ], MY_Controller::HTTP_OK);
        }
    }
}
