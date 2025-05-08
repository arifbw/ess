<?php defined('BASEPATH') OR exit('No direct script access allowed');
include_once( APPPATH . 'core/Group_Controller.php' );
class Hapus extends Group_Controller {
    
    function __construct(){
        parent::__construct();
        if(!in_array($this->id_group,[7,15])){
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
                'message'=>"ID perizinan harus diisi",
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
        } else if( empty($this->post('created')) ){
            $this->response([
                'status'=>false,
                'message'=>"Created harus diisi",
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
        }

        $cek = $this->db->where('id', $this->post('id'))->get('ess_request_perizinan');
        if($cek->num_rows()==1){
            $izin = $cek->row();
            $pengamanan = $izin->approval_pengamanan_posisi;
            $kode_pamlek = $izin->kode_pamlek;

            $tgl = $izin->start_date!=null ? $izin->start_date : $izin->end_date;
            $bulan = date('Y_m', strtotime($tgl));
            $tabel_bulan = 'ess_perizinan_'.$bulan;

            $set_date_real = [];
            $new_pengamanan_posisi = [];
            if ($pengamanan!=null) {
                $get_posisi =  json_decode($pengamanan);
                foreach ($get_posisi as $val) {
                    if($val->created==$this->post('created')){
                        $val->status = '0';
                    }
                    if($val->status=="1") $set_date_real[] = $val;

                    $new_pengamanan_posisi[] = $val;
                }

                $this->db->where('id', $izin->id)->update('ess_request_perizinan', ['approval_pengamanan_posisi'=>json_encode($new_pengamanan_posisi)]);

                $set_date_realisasi = json_encode($set_date_real);
                $get_date = array_column(json_decode($set_date_realisasi, true), 'waktu');
                sort($get_date);
                $jml_date = count($get_date);

                $save = [];
                $save_bln = [];
                if( $kode_pamlek=='0' ) {
                    $start_date = null;
                    $start_time = null;
                    
                    $end_date = date('Y-m-d', strtotime($get_date[($jml_date-1)]));
                    $end_time = date('H:i:s', strtotime($get_date[($jml_date-1)]));
                    
                    $save['start_date'] = $start_date;
                    $save['start_time'] = $start_time;
                    $save['end_date'] = $end_date;
                    $save['end_time'] = $end_time;
    
                    $save_bln['start_date'] = $start_date;
                    $save_bln['start_time'] = $start_time;
                    $save_bln['end_date'] = $end_date;
                    $save_bln['end_time'] = $end_time;
                } else {
                    if ($jml_date>0) {
                        $start_date = date('Y-m-d', strtotime($get_date[0]));
                        $start_time = date('H:i:s', strtotime($get_date[0]));
                        $end_date = date('Y-m-d', strtotime($get_date[($jml_date-1)]));
                        $end_time = date('H:i:s', strtotime($get_date[($jml_date-1)]));
                    } else {
                        $start_date = date('Y-m-d', strtotime($izin->start_date_input));
                        $start_time = date('H:i:s', strtotime($izin->start_date_input));
                        $end_date = date('Y-m-d', strtotime($izin->end_date_input));
                        $end_time = date('H:i:s', strtotime($izin->end_date_input));
                    }

                    if ($jml_date>1 || $jml_date==0) {
                        $save['start_date'] = $start_date;
                        $save['start_time'] = $start_time;
                        $save['end_date'] = $end_date;
                        $save['end_time'] = $end_time;

                        $save_bln['start_date'] = $start_date;
                        $save_bln['start_time'] = $start_time;
                        $save_bln['end_date'] = $end_date;
                        $save_bln['end_time'] = $end_time;
                    } else if ($jml_date==1) {
                        $save['start_date'] = $start_date;
                        $save['start_time'] = $start_time;
                        $save_bln['start_date'] = $start_date;
                        $save_bln['start_time'] = $start_time;
                        
                        $end_date_realisasi = date('Y-m-d H:i:s', strtotime($get_date[($jml_date-1)]));
                        if ($end_date_realisasi > $izin->end_date_input) {
                            $save['end_date'] = null;
                            $save['end_time'] = null;
                            $save_bln['end_date'] = null;
                            $save_bln['end_time'] = null;
                        } else {
                            $end_date = date('Y-m-d', strtotime($izin->end_date_input));
                            $end_time = date('H:i:s', strtotime($izin->end_date_input));

                            $save['end_date'] = $end_date;
                            $save['end_time'] = $end_time;
                            $save_bln['end_date'] = $end_date;
                            $save_bln['end_time'] = $end_time;
                        }
                    }
                }

                if( $izin->id_perizinan!=null ){
                    $this->db->where('id', $izin->id_perizinan)->set($save_bln)->update($tabel_bulan);
                }
                $this->db->where('id', $izin->id)->set($save)->update('ess_request_perizinan');
            }

            $this->response([
                'status'=>true,
                'message'=>'Approval keamanan telah dihapus',
                'data'=>[]
            ], MY_Controller::HTTP_OK);
        } else{
            $this->response([
                'status'=>false,
                'message'=>'Data perizinan tidak ditemukan.',
                'data'=>[]
            ], MY_Controller::HTTP_NOT_FOUND);
        }
    }
}
