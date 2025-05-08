<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Clock extends MY_Controller {
    
    function __construct(){
        parent::__construct();
    }
    
    function index_get(){
        $data = [];
        $data_insert = [];
        $data_cico = [];
        $np_karyawan = $this->data_karyawan->np_karyawan;
        try {
            if(!empty($this->get('tanggal'))){
                $tanggal = date('Y-m-d', strtotime($this->get('tanggal')));
                $m = date('Y_m', strtotime($tanggal));
                /* is_dinas_luar dipakai dimana?? di table cico gak ada fieldnya
                $get_assesment = $this->db->select('tapping_fix_approval_status as status_approval, tapping_fix_approval_np as np_approval, is_dinas_luar, (CASE WHEN tapping_fix_1 is null THEN DATE_FORMAT(tapping_fix_1_temp, "%H:%i:%s") ELSE DATE_FORMAT(tapping_fix_1, "%H:%i:%s") END) as clock_in, (CASE WHEN tapping_fix_2 is null THEN DATE_FORMAT(tapping_fix_2_temp, "%H:%i:%s") ELSE DATE_FORMAT(tapping_fix_2, "%H:%i:%s") END) as clock_out')->where(['np_karyawan'=>$np_karyawan, 'dws_tanggal'=>$tanggal])->get('ess_cico_'.$m);*/
                $get_assesment = $this->db->select('is_dinas_luar, tapping_fix_approval_status as status_approval, tapping_fix_approval_alasan as alasan_approval, tapping_fix_approval_np as np_approval, (CASE WHEN tapping_fix_1 is null THEN DATE_FORMAT(tapping_fix_1_temp, "%H:%i:%s") ELSE DATE_FORMAT(tapping_fix_1, "%H:%i:%s") END) as clock_in, (CASE WHEN tapping_fix_2 is null THEN DATE_FORMAT(tapping_fix_2_temp, "%H:%i:%s") ELSE DATE_FORMAT(tapping_fix_2, "%H:%i:%s") END) as clock_out')->where(['np_karyawan'=>$np_karyawan, 'dws_tanggal'=>$tanggal])->get('ess_cico_'.$m);


                if($get_assesment->num_rows()==1){
                    $get_ass = $get_assesment->row_array();
                    if ($get_ass['np_approval']==null)
                        $get_ass['np_approval'] = '';
                    /*if ($get_ass['status_approval']=='0' && $get_ass['clock_in']!=null && $get_ass['clock_out']==null)
                        $get_ass['status_approval'] = '1';*/
                    $this->response([
                        'status'=>true,
                        'message'=>'Success',
                        'data'=>$get_ass
                    ], MY_Controller::HTTP_OK);
                } else{
                    $this->response([
                        'status'=>false,
                        //'message'=>'Belum input self assesment tanggal '.$tanggal,
                        'message'=>'Anda belum clock in/out di tanggal '.$tanggal,
                        'data'=>$data
                    ], MY_Controller::HTTP_NOT_FOUND);
                }
            } else{
                $this->response([
                    'status'=>false,
                    'message'=>'Tanggal is required',
                    'data'=>$data
                ], MY_Controller::HTTP_BAD_REQUEST);
            }
        } catch(Exception $e){
            $this->response([
                'status'=>false,
                'message'=>'Error Exception',
                'data'=>$data
            ], MY_Controller::HTTP_BAD_REQUEST);
        }
    }
    
    function index_post(){
        $data = [];
        $data_insert = [];
        $data_cico = [];
        $np_karyawan = $this->data_karyawan->np_karyawan;
        
        try {
            $approval_np = $this->post('np');
            $check_dinas = $this->post('dinas_luar');
            $mst_kry = $this->db->where('no_pokok', $approval_np)->get('mst_karyawan')->row();
            $approval_nama = $mst_kry->nama;
            $approval_nama_jabatan = $mst_kry->nama_jabatan;
            if(empty($this->post('tanggal'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Tanggal harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($this->post('jam'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Tanggal harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            /*} else if(empty($this->post('np'))){
                $this->response([
                    'status'=>false,
                    'message'=>"NP Atasan harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);*/
            } else{
                $tanggal = date('Y-m-d', strtotime($this->post('tanggal')));
                $m = date('Y_m', strtotime($tanggal));
                $table_name = "ess_cico_$m";
                
                # cek table
                $get_table = $this->db->query('SELECT count(*) as count_table FROM information_schema.tables WHERE table_schema = "'.$this->db->database.'" AND table_name = "'.$table_name.'"')->row();
                
                if($get_table->count_table > 0){
                    
                    # cek dws
                    $get_dws = $this->db->select('id,np_karyawan,nama,kode_unit,nama_unit,tapping_fix_1,tapping_fix_2,tapping_fix_1_temp,tapping_fix_2_temp,tapping_fix_approval_status,tapping_fix_approval_ket')->where(['np_karyawan'=>$np_karyawan, 'dws_tanggal'=>$tanggal])->get($table_name);
                    if($get_dws->num_rows()==0){
                        $id = $this->db->where(['np_karyawan'=>$np_karyawan])
                            ->where('dws_tanggal < ',$tanggal) # heru nambah where ini, 2021-03-04
                            ->select('max(id) as id')->get($table_name)->row()->id;
                        $set_cico = $this->db->select('np_karyawan,nama,nama_jabatan,kode_unit,nama_unit,personel_number,dws_name,dws_name_fix,dws_in,dws_out,dws_break_start,dws_break_end,action,dws_tanggal,dws_in_tanggal,dws_out_tanggal')->where(['id'=>$id])->get($table_name)->row_array();

                        $dws_tanggal = new DateTime($set_cico['dws_tanggal']);
                        $dws_in_tanggal = new DateTime($set_cico['dws_in_tanggal']);
                        $dws_out_tanggal = new DateTime($set_cico['dws_out_tanggal']);

                        $dws_tanggal->modify('+1 day');
                        $dws_in_tanggal->modify('+1 day');
                        $dws_out_tanggal->modify('+1 day');

                        $set_cico["dws_tanggal"] = $dws_tanggal->format('Y-m-d');
                        $set_cico["dws_in_tanggal"] = $dws_in_tanggal->format('Y-m-d');
                        $set_cico["dws_out_tanggal"] = $dws_out_tanggal->format('Y-m-d');
                        $set_cico["created_at"] = date('Y-m-d H:i:s');
                        
                        //$this->db->set($set_cico)->insert($table_name);
                        $this->db->insert($table_name, $set_cico);
                        // $get_id = $this->db->set(array('dws_tanggal', )->update($table_name);

                        $get_dws = $this->db->select('id,np_karyawan,nama,kode_unit,nama_unit,tapping_fix_1,tapping_fix_2,tapping_fix_1_temp,tapping_fix_2_temp,tapping_fix_approval_status,tapping_fix_approval_ket')->where(['np_karyawan'=>$np_karyawan, 'dws_tanggal'=>$tanggal])->get($table_name);
                    }
                    
                    if($get_dws->num_rows()==1){
                        $row_dws = $get_dws->row();
                        
                        if((($row_dws->tapping_fix_1==null || $row_dws->tapping_fix_1=='' || $row_dws->tapping_fix_1=='0000-00-00 00:00:00') && ($row_dws->tapping_fix_1_temp==null || $row_dws->tapping_fix_1_temp=='' || $row_dws->tapping_fix_1_temp=='0000-00-00 00:00:00')) || (($row_dws->tapping_fix_1!=null && $row_dws->tapping_fix_1!='' && $row_dws->tapping_fix_1!='0000-00-00 00:00:00') && ($row_dws->tapping_fix_2==null || $row_dws->tapping_fix_2=='' || $row_dws->tapping_fix_2=='0000-00-00 00:00:00') && $row_dws->tapping_fix_approval_status!='1')) {
                            // $data_cico['tapping_fix_1'] = $tanggal.' '.$this->post('jam');
                            // $data_cico['tapping_fix_approval_status'] = '1';
                            //diganti wina masuk ke approval dulu 28-01-21
                            if ($approval_np!='' && $approval_np!=null) {
                                // $data_cico['tapping_fix_approval_status'] = '0';
                                $data_cico['tapping_fix_approval_np'] = $approval_np;
                                $data_cico['tapping_fix_approval_nama'] = $approval_nama;
                                $data_cico['tapping_fix_approval_nama_jabatan'] = $approval_nama_jabatan;
                                $data_cico['tapping_fix_1_temp'] = $tanggal.' '.$this->post('jam');
                            // $data_cico['tapping_fix_1'] = null;
                            } else {
                                // $data_cico['tapping_fix_approval_status'] = '1';
                                $data_cico['tapping_fix_1'] = $tanggal.' '.$this->post('jam');
                            }
                            $data_cico['is_dinas_luar'] = ($check_dinas=='1' ? '1' : '0');

                            $data_cico['tapping_fix_approval_ket'] = 'WFH MOBILE';
                            $data_cico['wfh'] = '1';
                            $data_cico['updated_at'] = date('Y-m-d H:i:s');
                            $data_cico['updated_by'] = $np_karyawan;
                            $update_cico = $this->db->where(['np_karyawan'=>$np_karyawan, 'dws_tanggal'=>$tanggal])->update($table_name, $data_cico);
                            $message='';
                            if($update_cico){
                                $message .= 'Updated to Cico. ';
                            } else{
                                $message .= 'Failed to Updat to Cico. '.$this->db->last_query();
                            }

                            $message .= 'Clock in at '.$this->post('jam');
                            $row_dws->tapping_fix_1 = $tanggal.' '.$this->post('jam');
                            
                        } else if((($row_dws->tapping_fix_2==null || $row_dws->tapping_fix_2=='' || $row_dws->tapping_fix_2=='0000-00-00 00:00:00') && ($row_dws->tapping_fix_2_temp==null || $row_dws->tapping_fix_2_temp=='' || $row_dws->tapping_fix_2_temp=='0000-00-00 00:00:00')) || (($row_dws->tapping_fix_2!=null && $row_dws->tapping_fix_2!='' && $row_dws->tapping_fix_2!='0000-00-00 00:00:00') && $row_dws->tapping_fix_approval_status!='1')) {
                            # heru nambahkan ini, kalau clock in 2 kali biar gak keupdate ke tapping_fix_2_temp, 2021-04-07
                            if( $tanggal.' '.$this->post('jam')==$row_dws->tapping_fix_1_temp ){
                                $this->response([
                                    'status'=>true,
                                    'message'=>'Clock in at '.$this->post('jam'),
                                    'data'=>$row_dws
                                ], MY_Controller::HTTP_OK);
                                exit;
                            }
                            # END heru nambahkan ini, kalau clock in 2 kali biar gak keupdate ke tapping_fix_2_temp, 2021-04-07
                            
                            // $data_cico['tapping_fix_2'] = $tanggal.' '.$this->post('jam');
                            // $data_cico['tapping_fix_approval_status'] = '1';
                            //diganti wina masuk ke approval dulu 28-01-21
                            if ($approval_np!='' && $approval_np!=null) {
                                // $data_cico['tapping_fix_approval_status'] = '0';
                                $data_cico['tapping_fix_approval_np'] = $approval_np;
                                $data_cico['tapping_fix_approval_nama'] = $approval_nama;
                                $data_cico['tapping_fix_approval_nama_jabatan'] = $approval_nama_jabatan;
                                $data_cico['tapping_fix_2_temp'] = $tanggal.' '.$this->post('jam');
                            } else {
                                // $data_cico['tapping_fix_approval_status'] = '1';
                                $data_cico['tapping_fix_2'] = $tanggal.' '.$this->post('jam');
                            }
                            /* else {
                                $data_cico['tapping_fix_approval_status'] = '1';
                            }*/
                            
                            # 2021-04-26
                            // $data_cico['tapping_fix_2_temp'] = $tanggal.' '.$this->post('jam'); # line ini diganti bawahnya
                            if( $row_dws->tapping_fix_approval_status=='1' ){ # jika sudah approve langsung masuk ke fix
                                $data_cico['tapping_fix_2'] = $tanggal.' '.$this->post('jam');
                                $data_cico['tapping_fix_2_temp'] = null;
                            } else{ # jika belum approve masuk dulu ke temp
                                $data_cico['tapping_fix_2_temp'] = $tanggal.' '.$this->post('jam');
                            }
                            # END: 2021-04-26

                            $data_cico['tapping_fix_approval_ket'] = 'WFH MOBILE';
                            $data_cico['wfh'] = '1';
                            $data_cico['updated_at'] = date('Y-m-d H:i:s');
                            $data_cico['updated_by'] = $np_karyawan;
                            $update_cico = $this->db->where(['np_karyawan'=>$np_karyawan, 'dws_tanggal'=>$tanggal])->update($table_name, $data_cico);
                            $message='';
                            if($update_cico){
                                $message .= 'Updated to Cico. ';
                            } else{
                                $message .= 'Failed to Updat to Cico. ';
                            }

                            $message .= 'Clock out at '.$this->post('jam');
                            $row_dws->tapping_fix_2 = $tanggal.' '.$this->post('jam');
                            
                        } else{
                            $this->response([
                                'status'=>false,
                                'message'=>'Sudah Clock Out',
                                'data'=>$data
                            ], MY_Controller::HTTP_BAD_REQUEST); exit;
                        }
                        
                        $this->response([
                            'status'=>true,
                            'message'=>$message,
                            'data'=>$row_dws
                        ], MY_Controller::HTTP_OK);
                        
                    } else{
                        $this->response([
                            'status'=>false,
                            'message'=>'Data tanggal '.$tanggal.' belum tersedia',
                            'data'=>$data
                        ], MY_Controller::HTTP_BAD_REQUEST);
                    }
                } else{
                    $this->response([
                        'status'=>false,
                        'message'=>'Data belum tersedia',
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
