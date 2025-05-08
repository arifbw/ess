<?php defined('BASEPATH') OR exit('No direct script access allowed');
include_once( APPPATH . 'core/Group_Controller.php' );
class Data extends Group_Controller {
    
    function __construct(){
        parent::__construct();
        if(!in_array($this->id_group,[5])){
            $this->response([
                'status'=>false,
                'message'=>"Otoritas tidak diizinkan",
                'data'=>[]
            ], MY_Controller::HTTP_FORBIDDEN);
        }
        $this->load->helper('spbe_spbi_helper');
    }
    
    function index_get(){
        $end_date = $this->get('end_date') ?: date('Y-m-d');
        $start_date = $this->get('start_date') ?: date("Y-m-d", strtotime("-7 days"));

        $this->db
            ->where('deleted_at IS NULL',null,false)
            ->where('canceled_at IS NULL',null,false)
            ->where("keluar_tanggal >=",$start_date)
            ->where("keluar_tanggal <=",$end_date)
            ->where('approval_atasan_status','1')
            ->where('approval_pengamanan_keluar','1')
            ->where('konfirmasi_pengguna_np',$this->data_karyawan->np_karyawan)
            ->from('ess_permohonan_spbi')
            ->order_by('konfirmasi_pembawa_status', 'ASC')
            ->order_by('keluar_tanggal','DESC');
        $data = $this->db->get()->result_array();

        for ($i=0; $i < count($data) ; $i++) { 
            $data[$i]['barang'] = $data[$i]['barang']!=null ? json_decode($data[$i]['barang']) : null;
            $data[$i]['pos_keluar'] = $data[$i]['pos_keluar']!=null ? json_decode($data[$i]['pos_keluar']) : null;
            $data[$i]['pos_masuk'] = $data[$i]['pos_masuk']!=null ? json_decode($data[$i]['pos_masuk']) : null;
            $data[$i]['approval_pengamanan_posisi'] = $data[$i]['approval_pengamanan_posisi']!=null ? json_decode($data[$i]['approval_pengamanan_posisi']) : null;

            $data[$i]['status_barang'] = spbi_status($data[$i]);

            $proses = '-';
            if( $data[$i]['approval_pengamanan_keluar']==null ){
                if( $data[$i]['approval_atasan_status']!='2' ) $proses = 'Proses Persetujuan';
            } else if( $data[$i]['approval_pengamanan_keluar']!=null ){
                $proses = 'Sudah Keluar Perusahaan';
                if( $data[$i]['approval_pengamanan_masuk']!=null ){
                    $proses = 'Sudah Kembali ke Perusahaan';
                }
            }
            $data[$i]['posisi_barang'] = $proses;
        }
        
        $this->response([
            'status'=>true,
            'message'=>'Konfirmasi Pembawa Barang Kembali ke Perusahaan',
            'data'=>$data
        ], MY_Controller::HTTP_OK);
    }
}
