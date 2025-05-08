<?php defined('BASEPATH') OR exit('No direct script access allowed');
include_once( APPPATH . 'core/Group_Controller.php' );
class Approve extends Group_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->helper("tanggal_helper");
        $this->load->helper("perizinan_helper");
        $this->load->model("api/lembur/M_lembur_api","lembur");

        $this->load->helper("karyawan_helper");
        $this->load->helper("cutoff_helper");

        $this->load->model("lembur/m_pengajuan_lembur");
        $this->load->model("lembur/m_persetujuan_lembur");
        $this->load->model("lembur/m_tabel_pengajuan_lembur");
        
        if(!in_array($this->id_group,[5])){
            $this->response([
                'status'=>false,
                'message'=>"Hanya bisa diakses pengguna",
                'data'=>[]
            ], MY_Controller::HTTP_FORBIDDEN);
        }
    }
    
    function index_post(){
        $data=[];
        $params=[];
        try {
            if(empty($this->post('id_pengajuan'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Id pengajuan harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                $id_ = $this->post('id_pengajuan');
                $pengajuan = $this->m_pengajuan_lembur->ambil_pengajuan_lembur_pegawai_id($id_);
                $mulai = date('Y-m-d', strtotime($pengajuan['tgl_mulai'])).' '.date('H:i', strtotime($pengajuan['jam_mulai']));
                $selesai = date('Y-m-d', strtotime($pengajuan['tgl_selesai'])).' '.date('H:i', strtotime($pengajuan['jam_selesai']));
                $data['approval_pimpinan_status'] = $this->post('persetujuan_approval_pimpinan');
                if ($data['approval_pimpinan_status'] == '2') {
                    $data['approval_pimpinan_alasan'] = $this->post('persetujuan_alasan_pimpinan');
                }
                else {
                    $data['approval_alasan'] = null;
                }
                $kry = erp_master_data_by_np($this->data_karyawan->np_karyawan, $pengajuan['tgl_dws']);
                $data['approval_pimpinan_date'] = date('Y-m-d H:i:s');
                $data['updated_by'] = $this->data_karyawan->np_karyawan; # heru menambahkan ini, 2020-11-14 @15:02
                // $approve = $this->m_persetujuan_lembur->save_approval($id_, $data);
                $approve = $this->lembur->save_approval(['id'=>$id_, 'approval_pimpinan_np'=>$this->data_karyawan->np_karyawan], $data); # heru ubah jadi ini, 2020-11-14 @15:02

                if ($data['approval_pimpinan_status'] == '1') {
                    $get_lembur['no_pokok'] = $pengajuan['no_pokok'];
                    $get_lembur['tgl_dws'] = $pengajuan['tgl_dws'];
                    $get_lembur['id'] = $id_;
                    $this->m_pengajuan_lembur->set_cico($get_lembur);
                }

                if ($approve == true) {
                    $this->response([
                        'status'=>true,
                        'message'=>'Berhasil Melakukan Persetujuan Lembur Kepada '.$pengajuan['nama_pegawai'].' ('.$pengajuan['no_pokok'].') pada '.tanggal_waktu($mulai).' - '.tanggal_waktu($selesai),
                        'data'=>$data
                    ], MY_Controller::HTTP_OK);
                }
                else {
                    $this->response([
                        'status'=>false,
                        'message'=>'Gagal Melakukan Persetujuan Lembur Kepada '.$pengajuan['nama_pegawai'].' ('.$pengajuan['no_pokok'].') pada '.tanggal_waktu($mulai).' - '.tanggal_waktu($selesai),
                        'data'=>$data
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
