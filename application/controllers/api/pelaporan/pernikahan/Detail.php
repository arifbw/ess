<?php defined('BASEPATH') OR exit('No direct script access allowed');
include_once( APPPATH . 'core/Group_Controller.php' );
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
        $id = $this->input->post('id');
        if (empty($id)) {
            $this->response([
                'status'=>false,
                'message'=>"Id harus diisi",
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
        } else {
            $tabel = 'ess_laporan_pernikahan';
            $lap = $this->db->select("*")->where('id', $id)->get($tabel.' a')->row_array();
            if ($lap == []) {
                $this->response([
                    'status'=>false,
                    'message'=>"Data tidak ditemukan.",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else {

                if ($lap['surat_keterangan_nikah'] != null && $lap['surat_keterangan_nikah'] != '') {
                    $surat_keterangan_nikah = base_url('uploads/pelaporan/pernikahan/surat_keterangan_nikah/').$lap['surat_keterangan_nikah'];
                    $lap['surat_keterangan_nikah'] = $surat_keterangan_nikah;
                }

                if ($lap['pas_foto'] != null && $lap['pas_foto'] != '') {
                    $pas_foto = base_url('uploads/pelaporan/pernikahan/pas_foto/').$lap['pas_foto'];
                    $lap['pas_foto'] = $pas_foto;
                }

                if ($lap['ktp'] != null && $lap['ktp'] != '') {
                    $ktp = base_url('uploads/pelaporan/pernikahan/ktp/').$lap['ktp'];
                    $lap['ktp'] = $ktp;
                }

                if ($lap['dokumen_lain'] != null && $lap['dokumen_lain'] != '') {
                    $dokumen_lain = base_url('uploads/pelaporan/pernikahan/dokumen_lain/').$lap['dokumen_lain'];
                    $lap['dokumen_lain'] = $dokumen_lain;
                }
                

                $data['detail'] = $lap;
                if($lap['approval_status']=='1' || $lap['approval_status']=='3' || $lap['approval_status']=='4' || $lap['approval_status']=='5' || $lap['approval_status']=='6') {
                    $data['approval_status'] = "Laporan pendidikan <b>TELAH DISETUJUI Atasan</b> pada <b>".datetime_indo($lap['approval_date'])."</b>";
                    $data['approval_warna'] ='success';
                }else if($lap['approval_status']=='2') {
                    $data['approval_status'] = "Laporan pendidikan <b>TIDAK DISETUJI Atasan</b> pada <b>".datetime_indo($lap['approval_date'])."</b>"; 
                    $data['approval_warna'] ='danger';
                } else if($lap['approval_status']=='0' || $lap['approval_status']==null) {
                    $data['approval_status'] = "Menunggu Persetujuan Atasan"; 
                    $data['approval_warna'] ='info';
                }

                if($lap['approval_status']=='3' || $lap['approval_status']=='5' || $lap['approval_status']=='6') {
                    $data['sdm_status'] = "Laporan pendidikan <b>DIVERIFIKASI KAUN SDM</b> pada <b>".datetime_indo($lap['sdm_verif_date'])."</b>";
                    $data['sdm_warna']= 'success';
                }else if($lap['approval_status']=='4') {
                    $data['sdm_status'] = "Laporan pendidikan <b>TIDAK DISETUJUI KAUN SDM</b> pada <b>".datetime_indo($lap['sdm_verif_date'])."</b>";
                    $data['sdm_warna'] = 'danger';
                }

                if($lap['approval_status']=='5') {
                    $data['submit_status'] = "Laporan pendidikan <b>DISUBMIT SDM ke ERP</b> pada <b>".datetime_indo($lap['sdm_submit_date'])."</b>";
                    $data['submit_warna'] = 'info';
                }
                else if($lap['approval_status']=='6') {
                    $data['submit_status'] = "Laporan pendidikan <b>DITOLAK ADMIN SDM ke ERP</b> pada <b>".datetime_indo($lap['sdm_submit_date'])."</b>";
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