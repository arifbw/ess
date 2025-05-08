<?php defined('BASEPATH') OR exit('No direct script access allowed');
include_once( APPPATH . 'core/Group_Controller.php' );
class Persetujuan extends Group_Controller {
    
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
    
    function index_post(){
        if(empty($this->post('id'))){ 
            $this->response([
                'status'=>false,
                'message'=>"Id harus diisi",
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
        } else if(empty($this->post('approval_status'))){ # untuk approval valuenya 1 atau 2
            $this->response([
                'status'=>false,
                'message'=>"Persetujuan harus diisi",
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
        } else if(empty($this->post('tanggal'))){
            $this->response([
                'status'=>false,
                'message'=>"Tanggal harus diisi",
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
        } else if(empty($this->post('jam'))){
            $this->response([
                'status'=>false,
                'message'=>"Jam harus diisi",
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
        } else{
            if( $this->post('approval_status')=='2' ){
                if(empty($this->post('keterangan'))){
                    $this->response([
                        'status'=>false,
                        'message'=>"Penolakan harus disertai keterangan",
                        'data'=>[]
                    ], MY_Controller::HTTP_BAD_REQUEST);
                }
            }

            if( !in_array($this->post('approval_status'), ['1','2']) ){
                $this->response([
                    'status'=>false,
                    'message'=>"Input persetujuan tidak valid",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            }

            $spbe = $this->db->where('id',$this->post('id'))->get('ess_permohonan_spbe')->row();
		    $approval_status = $this->post('approval_status');

            if( $spbe->approval_pengamanan_keluar=='1' && $approval_status=='2' ){
                $this->response([
                    'status'=>false,
                    'message'=>"Pembatalan hanya lewat Petugas Pos karena barang sudah diapprove Petugas Pos",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                $data_insert = [
                    'konfirmasi_pengguna'=>$this->post('approval_status'),
                    'konfirmasi_pengguna_keterangan'=>$this->post('approval_status')=='1' ? null:$this->post('keterangan'),
                    'konfirmasi_pengguna_tanggal'=>$this->post('tanggal'),
                    'konfirmasi_pengguna_jam'=>$this->post('jam'),
                    'konfirmasi_pengguna_at'=>date('Y-m-d H:i:s'),
                ];
        
                $this->db->where('id', $this->post('id'))->update('ess_permohonan_spbe',$data_insert);

                $this->response([
                    'status'=>true,
                    'message'=>"Konfirmasi SPBE Keluar Perusahaan telah disimpan",
                    'data'=>$data_insert
                ], MY_Controller::HTTP_OK);
            }
        }
    }
}
