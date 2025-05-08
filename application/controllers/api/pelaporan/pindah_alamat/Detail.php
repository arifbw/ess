<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Detail extends MY_Controller {
    
    function __construct(){
        parent::__construct();

        $this->load->helper("cutoff_helper");
        $this->load->helper("tanggal_helper");
        $this->load->helper("karyawan_helper");
        $this->load->helper("reference_helper");
    }

    function index_post()
    {
        if (empty($this->post('id'))) {
            $this->response([
                'status'=>false,
                'message'=>"Id harus diisi",
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
        } else {
            $id = $this->post('id');
            $tabel = 'ess_laporan_pindah_alamat';
            $lap = $this->db->select("*")->where('id', $id)->get($tabel.' a')->row_array();
            if ($lap == []) {
                $this->response([
                    'status'=>false,
                    'message'=>"Data tidak ditemukan.",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else {

                if ($lap['ktp'] != null && $lap['ktp'] != '') {
                    $ktp = base_url('uploads/pelaporan/pindah_alamat/ktp/').$lap['ktp'];
                    $lap['ktp'] = $ktp;
                }

                $data['detail'] = $lap;
                if($lap['approval_status']=='1' || $lap['approval_status']=='3' || $lap['approval_status']=='4' || $lap['approval_status']=='5' || $lap['approval_status']=='6') {
                    $data['approval_status'] = "Laporan pindah alamat TELAH DISETUJUI Atasan pada ".datetime_indo($lap['approval_date']);
                    $data['approval_warna'] ='success';
                }else if($lap['approval_status']=='2') {
                    $data['approval_status'] = "Laporan pindah alamat TIDAK DISETUJI Atasan pada ".datetime_indo($lap['approval_date']); 
                    $data['approval_warna'] ='danger';
                } else if($lap['approval_status']=='0' || $lap['approval_status']==null) {
                    $data['approval_status'] = "Menunggu Persetujuan Atasan"; 
                    $data['approval_warna'] ='info';
                }

                if($lap['approval_status']=='3' || $lap['approval_status']=='5' || $lap['approval_status']=='6') {
                    $data['sdm_status'] = "Laporan pindah alamat DIVERIFIKASI KAUN SDM pada ".datetime_indo($lap['sdm_verif_date']);
                    $data['sdm_warna']= 'success';
                }else if($lap['approval_status']=='4') {
                    $data['sdm_status'] = "Laporan pindah alamat TIDAK DISETUJUI KAUN SDM pada ".datetime_indo($lap['sdm_verif_date']);
                    $data['sdm_warna'] = 'danger';
                }

                if($lap['approval_status']=='5') {
                    $data['submit_status'] = "Laporan pindah alamat DISUBMIT SDM ke ERP pada ".datetime_indo($lap['sdm_submit_date']);
                    $data['submit_warna'] = 'info';
                }
                else if($lap['approval_status']=='6') {
                    $data['submit_status'] = "Laporan pindah alamat DITOLAK ADMIN SDM ke ERP pada ".datetime_indo($lap['sdm_submit_date']);
                    $data['submit_warna'] = 'danger';
                }
                $this->response([
                    'status'=>true,
                    'message'=>"Data ditemukan",
                    'data'=>$data
                ], MY_Controller::HTTP_OK);
            }
        }
    }
    
}

?>