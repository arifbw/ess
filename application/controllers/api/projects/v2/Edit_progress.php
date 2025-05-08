<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Edit_progress extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        $this->current_table = 'ess_project_tasklist_evidences';
    }
    
    function index_post(){
        $data = [];
        $data_insert = [];
        
        try {
            if(empty($this->post('tasklist_id'))){
                $this->response([
                    'status'=>false,
                    'message'=>"ID Project harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($this->post('progress'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Progress harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                
                # cek apakah pic dari activity
                $cek_activity = $this->db->select('a.id,a.kode, (CASE WHEN end_date_fix is not null THEN end_date_fix ELSE end_date END ) as end_date, b.np,b.is_pic')
                    ->where('a.id',$this->post('tasklist_id'))
                    ->from('ess_project_tasklists a')
                    ->join('ess_project_tasklist_members b','a.id=b.tasklist_id AND b.np="'.$this->data_karyawan->np_karyawan.'"','LEFT')
                    ->get();
                
                if($cek_activity->num_rows()==0){
                    $this->response([
                        'status'=>false,
                        'message'=>'Activity tidak ditemukan',
                        'data'=>[]
                    ], MY_Controller::HTTP_BAD_REQUEST);
                } else{
                    $row_activity = $cek_activity->row();
                    if($row_activity->is_pic!='1'){
                        $this->response([
                            'status'=>false,
                            'message'=>'Anda bukan PIC Activity',
                            'data'=>[]
                        ], MY_Controller::HTTP_BAD_REQUEST);
                    } /*else if( date('Y-m-d') > date('Y-m-d', strtotime($row_activity->end_date)) ){
                        $this->response([
                            'status'=>false,
                            'message'=>'Tanggal sudah terlewat',
                            'data'=>[]
                        ], MY_Controller::HTTP_BAD_REQUEST);
                    } */
                }
                
                # cek apakah row progres/evidence sudah ada
                $cek_evidence = $this->db->where(['tasklist_id'=>$this->post('tasklist_id'), 'task_date'=>date('Y-m-d')])->get('ess_project_tasklist_evidences');
                if($cek_evidence->num_rows()==0)
                    $action = 'insert';
                else
                    $action = 'update';
                
                # START: collect data
                $data_insert['progress'] = $this->post('progress');
                $data_insert['note'] = @$this->post('note') ? $this->post('note'):null;
                
                $uploaded_file = false;
                if( @$_FILES['evidence']['name']!='' ){
                    if( $_FILES['evidence']['size'] > 5242880 ){
                        $this->response([
                            'status'=>false,
                            'message'=>'The file you are attempting to upload is larger than the permitted size.'
                        ], MY_Controller::HTTP_BAD_REQUEST);
                    }
                    
                    $dir = 'uploads/tasklist/';
                    $real_dir = '/home/file/kehadiran/tasklist/';
                    
                    if(!is_dir('./'.$dir.$row_activity->kode)){
                        // mkdir('./'.$dir.$row_activity->kode, 0777, true);
                        mkdir($real_dir.$row_activity->kode, 0775, true);
                    }
                    
                    $explode_file_name = explode('.',$_FILES['evidence']['name']);
                    $ext = $explode_file_name[(count($explode_file_name)-1)];
                    
                    $filename = 'Evidence_'.date('Ymd_His').'.'.$ext;
                    
                    $this->load->library('upload');
                    // $config['upload_path'] = './'.$dir.$row_activity->kode;
                    $config['upload_path'] = $real_dir.$row_activity->kode;
                    $config['file_name'] = $filename;
                    $config['allowed_types'] = 'jpg|jpeg|png';
                    $config['overwrite'] = true;
                    $config['max_size'] = 5*1024; // 5Mb
                    $this->upload->initialize($config);
                    if ($this->upload->do_upload('evidence')) {
                        if($action=='update'){
                            if(is_file('./'.$cek_evidence->row()->evidence)){
                                // unlink('./'.$cek_evidence->row()->evidence);
                                unlink($real_dir.$row_activity->kode.'/'.basename($cek_evidence->row()->evidence));
                            }
                        }
                        $data_insert['evidence'] = $dir.$row_activity->kode.'/'.$filename;
                        $uploaded_file = true;
                    } else{
                        $this->response([
                            'status'=>false,
                            'message'=>$this->upload->display_errors(),
                            'data'=>[]
                        ], MY_Controller::HTTP_BAD_REQUEST);
                    }
                }
                # END: collect data
                
                if($cek_evidence->num_rows()==0){
                    # insert
                    $data_insert['kode'] = $this->uuid->v4();
                    $data_insert['tasklist_id'] = $this->post('tasklist_id');
                    $data_insert['task_date'] = date('Y-m-d');
                    $data_insert['created_at'] = date('Y-m-d H:i:s');
                    $data_insert['created_by_np'] = $this->data_karyawan->np_karyawan;
                    $data_insert['created_by_nama'] = $this->data_karyawan->nama;
                    $data_insert['created_by_jabatan'] = $this->data_karyawan->nama_jabatan_singkat;
                    $data_insert['created_by_kode_unit'] = $this->data_karyawan->kode_unit;
                    $data_insert['created_by_nama_unit'] = $this->data_karyawan->nama_unit_singkat;
                    
                    $this->db->insert( $this->current_table, $data_insert );
                } else{
                    # update
                    $data_insert['updated_at'] = date('Y-m-d H:i:s');
                    $data_insert['updated_by_np'] = $this->data_karyawan->np_karyawan;
                    $data_insert['updated_by_nama'] = $this->data_karyawan->nama;
                    $data_insert['updated_by_jabatan'] = $this->data_karyawan->nama_jabatan_singkat;
                    $data_insert['updated_by_kode_unit'] = $this->data_karyawan->kode_unit;
                    $data_insert['updated_by_nama_unit'] = $this->data_karyawan->nama_unit_singkat;
                    
                    $this->db->where( 'kode',$cek_evidence->row()->kode )->update( $this->current_table, $data_insert );
                }

                if($this->db->affected_rows()>0){
                    # update progress ke table ess_project_tasklists
                    $update_activity = [];
                    $update_activity['progress'] = $this->post('progress');
                    $update_activity['note'] = @$this->post('note')?$this->post('note'):null;
                    if($uploaded_file==true){
                        $update_activity['evidence'] = $data_insert['evidence'];
                        $update_activity['uploaded_at'] = $action == 'insert' ? $data_insert['created_at']:$data_insert['updated_at'] ;
                    }
                    $update_activity['updated_at'] = date('Y-m-d H:i:s');
                    $update_activity['updated_by_np'] = $this->data_karyawan->np_karyawan;
                    $update_activity['updated_by_nama'] = $this->data_karyawan->nama;
                    $update_activity['updated_by_jabatan'] = $this->data_karyawan->nama_jabatan_singkat;
                    $update_activity['updated_by_kode_unit'] = $this->data_karyawan->kode_unit;
                    
                    if( $this->post('progress')<100 && date('Y-m-d', strtotime($row_activity->end_date))<=date('Y-m-d') )
                        $update_activity['end_date_fix'] = date('Y-m-d', strtotime("+1 day"));
                    
                    $this->db->where('id', $this->post('tasklist_id') )->update('ess_project_tasklists',$update_activity);
                    
                    $this->response([
                        'status'=>true,
                        'message'=>'Update progress telah disimpan',
                        'data'=>$data_insert
                    ], MY_Controller::HTTP_OK);
                } else{
                    $this->response([
                        'status'=>false,
                        'message'=>'Gagal',
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
