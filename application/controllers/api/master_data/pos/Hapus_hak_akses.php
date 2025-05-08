<?php defined('BASEPATH') OR exit('No direct script access allowed');
include_once( APPPATH . 'core/Group_Controller.php' );
class Hapus_hak_akses extends Group_Controller {
    
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
        if( empty($this->post('id')) ){
            $this->response([
                'status'=>false,
                'message'=>"ID harus diisi",
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
        } else if( empty($this->post('no_pokok')) ){
            $this->response([
                'status'=>false,
                'message'=>"NP Karyawan harus diisi",
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
        }

        $cek = $this->db->from('mst_pos')->where('id', $this->post('id'))->where('status', '1')->get();
        
        if( $cek->num_rows()>0 ){
            $row = $cek->row_array();

            $arr_np = explode(',', $row['no_pokok']);
            $arr_np = array_diff($arr_np, [$this->post('no_pokok')]);
            $set_np = implode(',', $arr_np);
            $this->db->where('id', $this->post('id'))->set('no_pokok', $set_np)->update('mst_pos');

            $this->response([
                'status'=>true,
                'message'=>"Berhasil Dihapus",
                'data'=>$this->db->from('mst_pos')->where('id', $this->post('id'))->get()->row_array()
            ], MY_Controller::HTTP_OK);
        } else{
            $this->response([
                'status'=>false,
                'message'=>"Data Pos tidak ditemukan.",
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
        }

    }
}
