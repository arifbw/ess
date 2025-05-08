<?php defined('BASEPATH') OR exit('No direct script access allowed');
include_once( APPPATH . 'core/Group_Controller.php' );
class Delete extends Group_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->helper("tanggal_helper");
        $this->load->helper("perizinan_helper");
        $this->load->model("api/lembur/M_lembur_api","lembur");

        $this->load->helper("karyawan_helper");
        $this->load->helper("cutoff_helper");

        $this->load->model("lembur/m_pengajuan_lembur");
        $this->load->model("lembur/m_tabel_pengajuan_lembur");
        
        if(!in_array($this->id_group,[4,5])){
            $this->response([
                'status'=>false,
                'message'=>"Hanya bisa diakses pengguna atau pengadministrasi unit kerja",
                'data'=>[]
            ], MY_Controller::HTTP_FORBIDDEN);
        }
    }

    function index_get() {
        $data=[];
        $params=[];
        try {
            if(empty($this->get('id'))) {
                $this->response([
                    'status'=>false,
                    'message'=>"Id harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else {
                $id = $this->get('id');
                $get = $this->lembur->hapus_lembur_id($id);
                if ($get->num_rows() > 0) {
                    $hps = $this->lembur->hapus($id);
                    if($hps > 0) {
                        $log_data_lama = "";
                        foreach($get->row_array() as $key => $value){
                            if(strcmp($key,"id")!=0){
                                if(!empty($log_data_lama)){
                                    $log_data_lama .= "<br>";
                                }
                                $log_data_lama .= "$key = $value";
                            }
                        }
                        
                        $log = array(
                            //"id_pengguna" => $this->session->userdata("id_pengguna"),
                            "id_pengguna" => $this->account->id, # heru menambahkan row ini 2020-11-16 @14:29
                            "id_modul" => $this->data['id_modul'],
                            "id_target" => $get->row()->id,
                            "deskripsi" => "hapus ".strtolower(preg_replace("/_/"," ",__CLASS__)),
                            "kondisi_lama" => $log_data_lama,
                            "kondisi_baru" => '',
                            "alamat_ip" => $this->data["ip_address"],
                            "waktu" => date("Y-m-d H:i:s")
                        );
                        $this->m_log->tambah($log);
                        $this->response([
                            'status'=>true,
                            'message'=>'Data Lembur Berhasil Dihapus.',
                            'data'=>[]
                        ], MY_Controller::HTTP_OK);
                    }
                    else {
                        $this->response([
                            'status'=>false,
                            'message'=>'Data Lembur Gagal Dihapus',
                            'data'=>$data
                        ], MY_Controller::HTTP_BAD_REQUEST);
                    }
                }
                else {
                    $this->response([
                        'status'=>false,
                        'message'=>'Data Lembur Tidak Diizinkan Untuk Dihapus',
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
