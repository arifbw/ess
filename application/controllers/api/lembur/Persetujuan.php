<?php defined('BASEPATH') OR exit('No direct script access allowed');
include_once( APPPATH . 'core/Group_Controller.php' );
class Persetujuan extends Group_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->helper("tanggal_helper");
        $this->load->helper("perizinan_helper");
        $this->load->model("api/lembur/M_lembur_api","lembur");

        $this->load->helper("karyawan_helper");
        $this->load->helper("cutoff_helper");

        $this->load->model("lembur/m_pengajuan_lembur");
        $this->load->model("lembur/m_tabel_pengajuan_lembur");
        
        if(!in_array($this->id_group,[2,5])){
            $this->response([
                'status'=>false,
                'message'=>"Hanya bisa diakses pengguna",
                'data'=>[]
            ], MY_Controller::HTTP_FORBIDDEN);
        }
    }
    
    function index_get(){
        $data=[];
        $params=[];
        try {
            if(empty($this->get('bulan'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Bulan harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                $params['tgl'] = $this->get('bulan');
                if($this->id_group==5){
                    $params['np'] = [$this->data_karyawan->np_karyawan];
                    if( @$this->get('status') ) $params['approve'] = $this->get('status');

                    $list  = $this->lembur->data_persetujuan($params)->result();
                    foreach ($list as $tampil) {
                        $no++;
                        $row['no'] = $no;
                        $row['id'] = $tampil->id;
                        $row['np_karyawan'] = $tampil->no_pokok;
                        $row['approver_np'] = $tampil->approval_pimpinan_np;
                        $row['nama'] = $tampil->nama;
                        $row['tertanggal'] = $tampil->tgl_dws;
                        $row['lembur_mulai'] = $tampil->tgl_mulai;
                        $row['jam_mulai'] = date('H:i:s', strtotime($tampil->jam_mulai));
                        $row['lembur_selesai'] = $tampil->tgl_selesai;
                        $row['jam_selesai'] = date('H:i:s', strtotime($tampil->jam_selesai));
                        $row['lembur_diakui'] = ($tampil->waktu_mulai_fix==null || $tampil->waktu_selesai_fix==null || $tampil->waktu_mulai_fix=='' || $tampil->waktu_selesai_fix=='') ? '-' : datetime_indo($tampil->waktu_mulai_fix).' s/d '.datetime_indo($tampil->waktu_selesai_fix);
                        
                        if($tampil->waktu_mulai_fix==null || $tampil->waktu_selesai_fix==null || $tampil->waktu_mulai_fix=='' || $tampil->waktu_selesai_fix=='' || $tampil->waktu_mulai_fix=='00:00:00' || $tampil->waktu_selesai_fix=='00:00:00') {
                            $row['status'] = "Tidak Diakui";
                        } else if($tampil->approval_status=='1') {
                            $row['status'] = "Disetujui SDM";
                        } else if($tampil->approval_status=='2') {
                            $row['status'] = "Ditolak SDM";
                        } else if($tampil->approval_status=='0' || $tampil->approval_status==null || $tampil->approval_status=='') {
                            if($tampil->approval_pimpinan_status=='1') {
                                $row['status'] = "Disetujui Atasan";
                            } else if($tampil->approval_pimpinan_status=='2') {
                                $row['status'] = "Ditolak Atasan";
                            } else if($tampil->approval_pimpinan_status=='0' || $tampil->approval_pimpinan_status==null || $tampil->approval_pimpinan_status=='') {
                                $row['status'] = "Menunggu Persetujuan";
                            }
                        } else {
                            $row['status'] = "Tidak Valid";
                        }

                        if($tampil->approval_pimpinan_status=='0' || $tampil->approval_pimpinan_status==null || $tampil->approval_pimpinan_status=='') {
                            $row['persetujuan_atasan'] = true;
                        } else {
                            $row['persetujuan_atasan'] = false;
                        }
                        
                        $data[]=$row;    
                        
                    }

                    $this->response([
                        'status'=>true,
                        'message'=>'Data Lembur Pada '.$this->get('bulan'),
                        'data'=>$data
                    ], MY_Controller::HTTP_OK);
                }
                else {
                    $this->response([
                        'status'=>false,
                        'message'=>"Tidak Ada Izin Mengakses",
                        'data'=>[]
                    ], MY_Controller::HTTP_BAD_REQUEST); 
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
