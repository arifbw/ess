<?php defined('BASEPATH') OR exit('No direct script access allowed');
include_once( APPPATH . 'core/Group_Controller.php' );
class Atasan extends Group_Controller {
    
    function __construct(){
        parent::__construct();
		if(!in_array($this->id_group,[5])){
            $this->response([
                'status'=>false,
                'message'=>"Otoritas tidak diizinkan",
                'data'=>[]
            ], MY_Controller::HTTP_FORBIDDEN);
        }
    }
    
    function index_get(){
        $data=[];
        try {
            $data = $this->db->select('a.id, a.np_karyawan, a.nama, a.info_type, a.absence_type, a.kode_pamlek, a.start_date, a.end_date, a.approval_1_np, a.approval_1_nama, a.approval_1_status, a.approval_2_np, a.approval_2_nama, a.approval_2_status, a.id_perizinan, b.nama AS nama_izin')
				->from('ess_request_perizinan a')
				->join('mst_perizinan b',"CONCAT(b.kode_pamlek,'|',b.kode_erp) = CONCAT(a.kode_pamlek,'|',a.info_type,'|',a.absence_type)",'LEFT')
				->where('a.date_batal IS NULL',null,false)
				->group_start()
					->group_start()
						->where('a.approval_1_np',$this->data_karyawan->np_karyawan)
						->where('a.approval_1_status IS NULL',null,false)
						->group_start()
							->where('a.approval_2_status IS NULL',null,false)
							->or_where('a.approval_2_status','1')
						->group_end()
					->group_end()
					->or_group_start()
						->where('a.approval_2_np',$this->data_karyawan->np_karyawan)
						->where('a.approval_2_status IS NULL',null,false)
						->group_start()
							->where('a.approval_1_status IS NULL',null,false)
							->or_where('a.approval_1_status','1')
						->group_end()
					->group_end()
				->group_end()
				->get()->result();
            $this->response([
                'status'=>true,
                'message'=>'Permohonan Izin Menunggu persetujuan Anda',
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
