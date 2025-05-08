<?php defined('BASEPATH') OR exit('No direct script access allowed');
include_once( APPPATH . 'core/Group_Controller.php' );
class Pemohon extends Group_Controller {
    
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
				->where('a.np_karyawan',$this->data_karyawan->np_karyawan)
				->where('a.date_batal IS NULL',null,false)
				->where("(
							CASE 
								WHEN a.approval_2_np IS NOT NULL THEN (a.approval_1_np!='' AND a.approval_1_np IS NOT NULL AND a.approval_2_np!='' AND a.approval_2_np IS NOT NULL)
								WHEN a.approval_2_np IS NULL THEN (a.approval_1_np!='' AND a.approval_1_np IS NOT NULL)
							END
						)")
				->where("(
							CASE 
								WHEN a.approval_2_np IS NOT NULL THEN (a.approval_1_status!='2' OR a.approval_2_status!='2')
								WHEN a.approval_2_np IS NULL THEN a.approval_1_status!='2'
							END
						)")
				->where("(
							CASE 
								WHEN a.approval_2_np IS NOT NULL THEN (a.approval_1_status IS NULL OR a.approval_2_status IS NULL)
								WHEN a.approval_2_np IS NULL THEN a.approval_1_status IS NULL
							END
						)")
				->get()->result();
            $this->response([
                'status'=>true,
                'message'=>'Permohonan Izin belum disetujui Atasan',
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
