<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Jenis_cuti_by_np extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->model("api/filter/M_filter_api","filter");
    }
    
	function index_post() {
        try {
            if(empty($this->post('np'))){
                $this->response([
                    'status'=>false,
                    'message'=>"NP harus diisi",
                    'data_karyawan'=>[],
                    'ref_cuti_bersama'=>[],
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                // mst cuti all
                $get_cuti = $this->filter->jenis_cuti()->result();
			    $mst_cuti = [];
			    $ref_cuti_bersama = [];

                // karyawan
                $np_karyawan = $this->post('np');
                $data_mst_karyawan = $this->db->select('no_pokok, nama, tanggal_masuk, kontrak_kerja')->where('no_pokok', $np_karyawan)->get('mst_karyawan')->row();
			    $allowed_cuti = [];
                $givenDate = new DateTime($data_mst_karyawan->tanggal_masuk);
                $now = new DateTime();
				$thirtyDaysAgo = $now->sub(new DateInterval('P30D'));
				if($givenDate > $thirtyDaysAgo || $data_mst_karyawan->kontrak_kerja=='PKWT'){
					$data_mst_karyawan->is_pkwt = true;
					$data = $data. "\n==================\n"."Pengajuan cuti bisa dilakukan di bulan berikutnya terhitung tanggal masuk : {$data_mst_karyawan->tanggal_masuk}";
					if($givenDate > $thirtyDaysAgo) $allowed_cuti = ['2001|2040','2001|2065','2001|2064','2001|2068','2001|2030'];
					else if($data_mst_karyawan->kontrak_kerja=='PKWT') $allowed_cuti = ['2001|2040','2001|2065','2001|2064','2001|2068','2001|2030','2001|1000','2001|1020'];

                    $filtered_cuti = array_filter($get_cuti, function($e) use($allowed_cuti){
                        return in_array($e->kode_erp, $allowed_cuti);
                    });

                    $mst_cuti = array_values($filtered_cuti);
                    $ref_cuti_bersama = array_values(array_filter($get_cuti, function($e) {
                        return in_array($e->kode_erp, ['2001|1000']);
                    }));
				} else{
					$data_mst_karyawan->is_pkwt = false;
                    $mst_cuti = $get_cuti;
                    $ref_cuti_bersama = array_values(array_filter($get_cuti, function($e) {
                        return in_array($e->kode_erp, ['2001|1000','2001|1010','2001|2080']);
                    }));
				}

                $this->response([
                    'status'=>true,
                    'message'=>'Jenis Cuti',
                    'data_karyawan'=>$data_mst_karyawan,
                    'ref_cuti_bersama'=>$ref_cuti_bersama,
                    'data'=>$mst_cuti
                ], MY_Controller::HTTP_OK);
            }
        } catch(Exception $e){
            $this->response([
                'status'=>false,
                'message'=>'Error Exception',
                'data_karyawan'=>[],
                'ref_cuti_bersama'=>[],
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
        }
	}
}
