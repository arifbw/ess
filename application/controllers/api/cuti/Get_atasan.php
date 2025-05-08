<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Get_atasan extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->model("master_data/m_karyawan");
        $this->load->model("api/filter/M_filter_api","filter");
    }
    
    function index_post(){
        $data = [];
        
        try {
            if(empty($this->post('np'))){
                $this->response([
                    'status'=>false,
                    'message'=>"NP harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                $np_karyawan = $this->post('np');
                $karyawan = $this->m_karyawan->get_posisi_karyawan($np_karyawan);
                
                if(empty($karyawan) or empty($karyawan["kode_unit"])){
                    $periode_kemarin = date('Y_m', strtotime(date('Y-m-d') . ' -1 months'));
                    $karyawan = $this->m_karyawan->get_posisi_karyawan_periode($np_karyawan,$periode_kemarin);
                }
                
                // unit
                if(strcmp($karyawan["posisi"],"unit")==0){
                    // staf unit
                    if(strcmp($karyawan["jabatan"],"staf")==0){
                        $karyawan["posisi"] = "seksi";
                    }
                    // kepala unit
                    if(strcmp($karyawan["jabatan"],"kepala")==0){
                        $karyawan["jabatan"] = "staf";
                        $karyawan["posisi"] = "seksi";
                    }
                    $karyawan["kode_unit"] = substr($karyawan["kode_unit"],0,strlen($karyawan["kode_unit"])-1);
                }
                
                if(strcmp($karyawan["jabatan"],"kepala")==0){
                    $kode_unit_atasan_1 = str_pad(substr($karyawan["kode_unit"],0,strlen($karyawan["kode_unit"])-1),5,0);
                    $kode_unit_atasan_2 = str_pad(substr($karyawan["kode_unit"],0,strlen($karyawan["kode_unit"])-2),5,0);
                } else{
                    $kode_unit_atasan_1 = str_pad($karyawan["kode_unit"],5,0);
                    if(strlen(preg_replace("/0+$/","",substr($karyawan["kode_unit"],0,strlen($karyawan["kode_unit"])-1)))>1){
                        $kode_unit_atasan_2 = str_pad(substr($karyawan["kode_unit"],0,strlen($karyawan["kode_unit"])-1),5,0);
                    } else{
                        $kode_unit_atasan_2 = "";
                    }
                }
                
                if(strcmp($kode_unit_atasan_1,"00000")==0){
                    $kode_unit_atasan_1 = "10000";
                }

                if(strcmp($kode_unit_atasan_2,"00000")==0){
                    $kode_unit_atasan_2 = "10000";
                }

                if(strcmp($kode_unit_atasan_1,$kode_unit_atasan_2)==0){
                    $kode_unit_atasan_2 = "";
                }

                if(strcmp(str_pad($karyawan["kode_unit"],5,0),$kode_unit_atasan_1)==0 and strlen($karyawan["kode_unit"])==1){
                    $kode_unit_atasan_1 = "";
                }
                
                $np_atasan_1 = $this->m_karyawan->get_atasan($kode_unit_atasan_1);
                $data_atasan_1 = $this->filter->get_data_karyawan_by_np($np_atasan_1)->row();
                
                $np_atasan_2 = $this->m_karyawan->get_atasan($kode_unit_atasan_2);
                $data_atasan_2 = $this->filter->get_data_karyawan_by_np($np_atasan_2)->row();
                
                $data['atasan_1'] = [
                    'np_array'=>$np_atasan_1,
                    'np'=>$data_atasan_1->no_pokok,
                    'nama'=>$data_atasan_1->nama,
                    'jabatan'=>$data_atasan_1->nama_jabatan
                ];
                
                $data['atasan_2'] = [
                    'np_array'=>$np_atasan_2,
                    'np'=>$data_atasan_2->no_pokok,
                    'nama'=>$data_atasan_2->nama,
                    'jabatan'=>$data_atasan_2->nama_jabatan
                ];
                        
                $this->response([
                    'status'=>true,
                    'message'=>'Data atasan',
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
