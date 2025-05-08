<?php defined('BASEPATH') OR exit('No direct script access allowed');
include_once( APPPATH . 'core/Group_Controller.php' );
class Persetujuan extends Group_Controller {
    
    function __construct(){
        parent::__construct();
        if(!in_array($this->id_group,[27])){
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
        } else if(empty($this->post('pos_id'))){
            $this->response([
                'status'=>false,
                'message'=>"Pos harus diisi",
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
        } else if(empty($this->post('pos_nama'))){
            $this->response([
                'status'=>false,
                'message'=>"Nama Pos harus diisi",
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
            $new_id = $this->uuid->v4();
            $data_pos = [
                'id'=>$new_id,
                'ess_permohonan_spbe_id'=>$this->post('id'),
                'pos_id'=>$this->post('pos_id'),
                'pos_nama'=>$this->post('pos_nama'),
                'tanggal'=>$this->post('tanggal'),
                'jam'=>$this->post('jam'),
                'keterangan'=>$this->post('keterangan') ?: null,
                'posisi'=>'masuk',
                // 'barang_sesuai'=>null, ???
                'approval_np'=>$this->data_karyawan->np_karyawan,
                'approval_nama'=>$this->data_karyawan->nama,
                'created_at'=>date('Y-m-d H:i:s')
            ];
            $this->db->insert('ess_permohonan_spbe_pos',$data_pos);

            if($this->db->affected_rows()>0){
                $count_tidak_lengkap = 0;
                $kondisi_barang = json_decode($this->post('kondisi_barang'));
                for ($i=0; $i < count($kondisi_barang) ; $i++) { 
                    $kondisi_barang[$i]->created_at = date('Y-m-d H:i:s');
                    $kondisi_barang[$i]->ess_permohonan_spbe_pos_id = $new_id;
                    $kondisi_barang[$i]->id = $this->uuid->v4();

                    if( $kondisi_barang[$i]->kondisi!='lengkap' ) $count_tidak_lengkap++;
                }
                if( count($kondisi_barang) > 0 ) $this->db->insert_batch('ess_permohonan_spbe_kondisi_barang',(array) $kondisi_barang);

                # update _pos
                $this->db->where('id',$new_id)->update('ess_permohonan_spbe_pos',['kondisi_barang_json'=>json_encode($kondisi_barang), 'barang_sesuai'=>($count_tidak_lengkap > 0 ? '2':'1')]);

                $approval_pengamanan_posisi = $this->db->where('ess_permohonan_spbe_id',$this->post('id'))->order_by('tanggal','DESC')->order_by('jam','DESC')->get('ess_permohonan_spbe_pos')->result_array();
                $this->db->where('id',$this->post('id'))->update('ess_permohonan_spbe',[
                    'approval_pengamanan_posisi'=>json_encode($approval_pengamanan_posisi),
                    'approval_pengamanan_masuk'=>'1',
                    'approval_pengamanan_updated_at'=>date('Y-m-d H:i:s')
                ]);

                $this->response([
                    'status'=>true,
                    'message'=>"Pemeriksaan SPBE Kembali ke Perusahaan telah disimpan",
                    'data'=>$data_pos
                ], MY_Controller::HTTP_OK);
            } else{
                $this->response([
                    'status'=>false,
                    'message'=>'Gagal melakukan update data',
                    'data'=>[]
                ], MY_Controller::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }
}
