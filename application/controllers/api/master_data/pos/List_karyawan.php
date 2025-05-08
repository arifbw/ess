<?php defined('BASEPATH') OR exit('No direct script access allowed');
include_once( APPPATH . 'core/Group_Controller.php' );
class List_karyawan extends Group_Controller {
    
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

    function index_get(){
        if( empty($this->get('id')) ){
            $this->response([
                'status'=>false,
                'message'=>"ID harus diisi",
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
        }

        $this->db->select('p.id,p.no_pokok,p.username,k.nama,k.kode_unit,k.nama_unit,p.status')
			 ->from("usr_pengguna p")
			 ->join("mst_karyawan k","p.no_pokok=k.no_pokok","right");
        
        $this->db->where('p.status', '1')->where('((not FIND_IN_SET(k.no_pokok, (SELECT GROUP_CONCAT(no_pokok SEPARATOR ",") AS no_pokok FROM mst_pos WHERE status="1" AND id="'.$this->get('id').'"))) OR ((SELECT GROUP_CONCAT(no_pokok SEPARATOR ",") AS no_pokok FROM mst_pos WHERE status="1" AND id="'.$this->get('id').'") IS NULL))');
        
        $this->db->order_by("k.no_pokok");
        $data = $this->db->get()->result_array();
        
        $this->response([
            'status'=>true,
            'message'=>"Data User/Karyawan yang bisa ditambahkan ke Pos",
            'data'=>$data
        ], MY_Controller::HTTP_OK);
    }
}
