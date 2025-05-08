<?php defined('BASEPATH') OR exit('No direct script access allowed');
include_once( APPPATH . 'core/Group_Controller.php' );
class Pembatalan extends Group_Controller {
    
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
                    $set = [];
					$set['alasan_batal'] = $this->post('alasan_batal');
					$set['np_batal'] = $this->data_karyawan->np_karyawan;
					$set['date_batal'] = date('Y-m-d H:i:s');
					$this->db->set($set)->where('id', $id)->update('ess_request_perizinan');
                    
                    $row_izin = $izin->row();
					if ( $row_izin->id_perizinan!=null ) {
                        $tgl = $row_izin->start_date ?: $row_izin->end_date;
                        $bulan = date('Y_m', strtotime($tgl));
                        $tabel_bulan = 'ess_perizinan_'.$bulan;
						$this->db->where('id', $row_izin->id_perizinan)->delete($tabel_bulan);

                        $tabel_cico = 'ess_cico_'.$bulan;
						$cek_is_cico = $this->db->where('find_in_set('.$row_izin->id_perizinan.', id_perizinan)')->get($tabel_cico);
						if ($cek_is_cico->num_rows()) {
							$get_cico = $cek_is_cico->row();
							$array_id_cico = explode(',', $get_cico->id_perizinan);
							$index = array_search($row_izin->id_perizinan, $array_id_cico);
							unset($array_id_cico[$index]);
							$set_id_cico = implode(',', $array_id_cico);
							$this->db->where('id', $get_cico->id)->set('id_perizinan', $set_id_cico)->update($tabel_cico);
						}
					}

                    $this->response([
                        'status'=>true,
                        'message'=>'Berhasil Membatalkan Perizinan',
                        'data'=>[]
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
