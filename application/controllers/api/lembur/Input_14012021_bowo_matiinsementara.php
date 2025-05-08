<?php defined('BASEPATH') OR exit('No direct script access allowed');
include_once( APPPATH . 'core/Group_Controller.php' );
class Data extends Group_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->helper("tanggal_helper");
        $this->load->helper("perizinan_helper");
        $this->load->model("api/lembur/M_lembur_api","lembur");
        if(!in_array($this->id_group,[2,4,5])){
            $this->response([
                'status'=>false,
                'message'=>"Hanya bisa diakses pengguna atau pengadministrasi unit kerja",
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
                $bulan = substr($this->get('bulan'),-2);
				$tahun = substr($this->get('bulan'),0,4);
                
                $params['tahun_bulan'] = $this->get('bulan');
                
                if($this->id_group==5){
                    $params['np'] = [$this->data_karyawan->np_karyawan];
                } else if($this->id_group==4){
                    $list = [];
                    foreach($this->list_pengadministrasi as $l){
                        $list[] = $l['kode_unit'];
                    }
                    $params['kode_unit'] = $list;
                }
                
                $no=0;
                $get_data_lembur = $this->lembur->get_lembur($params)->result();
                foreach($get_data_lembur as $tampil){
                    $row=[];
                    $no++;
                    $row['no'] = $no;
                    $row['id'] = $tampil->id;
                    $row['np_karyawan'] = $tampil->no_pokok;
                    $row['nama'] = $tampil->nama;
                    $row['tertanggal'] = tanggal_indonesia($tampil->tgl_dws);
                    $row['lembur_mulai'] = tanggal_indonesia($tampil->tgl_mulai).' '.date('H:i', strtotime($tampil->jam_mulai));
                    $row['lembur_selesai'] = tanggal_indonesia($tampil->tgl_selesai).' '.date('H:i', strtotime($tampil->jam_selesai));
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
                    
                    if (($tampil->approval_status=='0' || $tampil->approval_status==null) || $tampil->waktu_mulai_fix==null || $tampil->waktu_selesai_fix==null || $tampil->waktu_mulai_fix=='' || $tampil->waktu_selesai_fix=='' || $tampil->waktu_mulai_fix=='00:00:00' || $tampil->waktu_selesai_fix=='00:00:00') {
					   $row['dapat_dihapus'] = true;
				    } else{
					   $row['dapat_dihapus'] = false;
                    }
                    
                    $data[]=$row;
                }
                
                $this->response([
                    'status'=>true,
                    'message'=>'Data Lembur bulan '.id_to_bulan($bulan)." ".$tahun,
                    'data'=>$data
                ], MY_Controller::HTTP_OK);
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
