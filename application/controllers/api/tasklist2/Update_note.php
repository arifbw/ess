<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Update_note extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->model('api/tasklist/M_tasklist_api','tasklist');
    }
    
    function index_post(){
        $data_insert = [];
        try {
            if(empty($this->post('id_assesment'))){
                $this->response([
                    'status'=>false,
                    'message'=>'ID assesment harus diisi'
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($this->post('catatan'))){
                $this->response([
                    'status'=>false,
                    'message'=>'Catatan harus diisi'
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                $data_insert['target_pekerjaan'] = $this->post('catatan');
                $cek = $this->db->where('id_assesment',$this->post('id_assesment'))->where('tipe','note')->get('ess_performance_management');
                
                if($cek->num_rows()==0){
                    # insert
                    $data_insert['kode'] = $this->uuid->v4();
                    $data_insert['id_assesment'] = $this->post('id_assesment');
                    $data_insert['tipe'] = 'note';
                    $data_insert['created_at'] = date('Y-m-d H:i:s');
                    $data_insert['created_by'] = $this->data_karyawan->np_karyawan;
                    $message = 'Ditambahkan';
                    $this->db->insert('ess_performance_management',$data_insert);
                } else{
                    # update
                    $data_insert['updated_at'] = date('Y-m-d H:i:s');
                    $message = 'Diperbarui';
                    $this->db->where('id_assesment',$this->post('id_assesment'))->where('tipe','note')->update('ess_performance_management',$data_insert);
                }
                
                if($this->db->affected_rows()>0){
                    $this->response([
                        'status'=>true,
                        'message'=>'Catatan '.$message
                    ], MY_Controller::HTTP_OK);
                } else{
                    $this->response([
                        'status'=>false,
                        'message'=>'Failed'
                    ], MY_Controller::HTTP_BAD_REQUEST);
                }
            }
        } catch(Exception $e){
            $this->response([
                'status'=>false,
                'message'=>'Error Exception'
            ], MY_Controller::HTTP_BAD_REQUEST);
        }
    }
}
