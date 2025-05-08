<?php defined('BASEPATH') OR exit('No direct script access allowed');
include_once( APPPATH . 'core/Group_Controller.php' );
class Np_karyawan extends Group_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->model("api/filter/M_filter_api","filter");
    }
    
	function index_get() {
        $np = $this->data_karyawan->np_karyawan;
        $data = [];
        
        try {
            if($this->id_group==4) {
                $var = [];
                foreach ($this->list_pengadministrasi as $row) {
                    $var[] = $row['kode_unit'];
                }				
                $data = $this->filter->getKaryawan(['grup'=>4, 'var'=>$var, 'fields'=>'no_pokok,nama,kode_unit,nama_unit,nama_unit_singkat,nama_jabatan']);
            } else if($this->id_group==5) {
                $data = $this->filter->getKaryawan(['grup'=>5, 'var'=>$np, 'fields'=>'no_pokok,nama,kode_unit,nama_unit,nama_unit_singkat,nama_jabatan']);
            } else{
                $data = $this->filter->getKaryawan(['grup'=>null, 'var'=>null, 'fields'=>'no_pokok,nama,kode_unit,nama_unit,nama_unit_singkat,nama_jabatan']);
            }
            
            $this->response([
                'status'=>true,
                'message'=>'Success',
                'data'=>$data
            ], MY_Controller::HTTP_OK);
        } catch(Exception $e){
            $this->response([
                'status'=>false,
                'message'=>'Error Exception',
                'data'=>$data
            ], MY_Controller::HTTP_BAD_REQUEST);
        }
	}
}
