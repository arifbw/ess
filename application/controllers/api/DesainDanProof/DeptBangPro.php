<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DeptBangPro extends CI_Controller {
    public function __construct(){
        parent::__construct();
    }
    
    function index_get(){
        header('Content-Type: application/json');
        if(empty( $this->input->get('key',true) )){
            echo json_encode([
                'status'=>false,
                'message'=>'Required key',
                'data'=>[]
            ]);
        } else{
            if( $this->input->get('key') != hash('sha256','divisiDesainDanProof'.date('Y-m-d')) ){
                echo json_encode([
                    'status'=>false,
                    'message'=>'Invalid Key',
                    'data'=>[]
                ]);
                exit;
            }

            $dirFoto = base_url('foto/profile/');
            $this->db->select("mst_karyawan.*, (CASE WHEN foto_karyawan.nama_file IS NOT NULL THEN CONCAT('{$dirFoto}', foto_karyawan.nama_file) ELSE CONCAT('{$dirFoto}', 'default.jpg') END) as nama_file");
            
            $this->db->join('foto_karyawan', 'foto_karyawan.no_pokok = mst_karyawan.no_pokok', 'LEFT');
            $get = $this->db->like('mst_karyawan.kode_unit','35A','after')->get('mst_karyawan')->result_array();
            echo json_encode([
                'status'=>true,
                'message'=>'Data Karyawan Departemen Pengembangan Produk',
                'data'=>$get
            ]);
        }
    }
}