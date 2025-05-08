<?php defined('BASEPATH') OR exit('No direct script access allowed');
include_once( APPPATH . 'core/Group_Controller.php' );
class Pos extends Group_Controller {
    
    function __construct(){
        parent::__construct();
        if(!in_array($this->id_group,[7,15])){
            $this->response([
                'status'=>false,
                'message'=>"Hanya bisa diakses oleh otoritas: Admin Pamsiknilmat",
                'data'=>[]
            ], MY_Controller::HTTP_FORBIDDEN);
        }
    }
    
    function index_post(){
        $data = [];
        $data_insert = [];
        
        try {
            if(empty($this->post('id'))){
                $this->response([
                    'status'=>false,
                    'message'=>"ID harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                $id = $this->post('id');
                $izin = $this->db->where('id', $id)->get('ess_request_perizinan');
                if ($izin->num_rows() == 1) {
                    $row_izin = $izin->row();
                    $arr_pos = json_decode($row_izin->pos);
                    $data = $this->db->where('status', '1')
                        // ->where("find_in_set('".$this->data_karyawan->np_karyawan."', no_pokok)")
                        ->get('mst_pos')->result();

                    $this->response([
                        'status'=>true,
                        'message'=>'List Pos',
                        'data'=>$data
                    ], MY_Controller::HTTP_OK);
				} else {
					$this->response([
                        'status'=>false,
                        'message'=>'Data perizinan tidak ditemukan.',
                        'data'=>[]
                    ], MY_Controller::HTTP_NOT_FOUND);
				}
            }
        } catch(Exception $e){
            $this->response([
                'status'=>false,
                'message'=>'Error Exception',
                'data'=>$data
            ], MY_Controller::HTTP_BAD_REQUEST);
        }
    }
}
