<?php defined('BASEPATH') OR exit('No direct script access allowed');
include_once( APPPATH . 'core/Group_Controller.php' );
class Input extends Group_Controller {
    
    function __construct(){
        parent::__construct();
        if(!in_array($this->id_group,[15])){
            $this->response([
                'status'=>false,
                'message'=>"Hanya bisa diakses oleh otoritas: Admin Pamsiknilmat Masterdata",
                'data'=>[]
            ], MY_Controller::HTTP_FORBIDDEN);
        }
    }
    
    function index_post(){
        if( empty($this->post('kode_pos')) ){
            $this->response([
                'status'=>false,
                'message'=>"Kode Pos harus diisi",
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
        } else if( empty($this->post('nama')) ){
            $this->response([
                'status'=>false,
                'message'=>"Nama Pos harus diisi",
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
        } else if( empty($this->post('status')) ){
            $this->response([
                'status'=>false,
                'message'=>"Status harus diisi",
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
        }

        $cek = $this->db->from('mst_pos')->where('nama',trim($this->post('nama')))->or_where('kode_pos',trim($this->post('kode_pos')))->get();
        if( $cek->num_rows()>0 ){
            $this->response([
                'status'=>false,
                'message'=>"Pos dengan nama {$this->post('nama')} atau kode {$this->post('kode_pos')} sudah ada.",
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
        }

        $data_insert = [
            'kode_pos'=>trim($this->post('kode_pos')),
            'nama'=>trim($this->post('nama')),
            'status'=>trim($this->post('status'))=='1' ? '1':'0'
        ];
        $this->db->insert('mst_pos', $data_insert);

        $this->response([
            'status'=>true,
            'message'=>"Data Pos berhasil ditambahkan",
            'data'=>$data_insert
        ], MY_Controller::HTTP_OK);
    }
}
