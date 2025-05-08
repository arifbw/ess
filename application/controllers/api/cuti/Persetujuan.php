<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Persetujuan extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->helper("tanggal_helper");
        $this->load->helper("cuti_helper");
        $this->load->model("api/filter/M_filter_api","filter");
        $this->load->model("api/cuti/M_cuti_api","cuti");
    }
    
    function index_get(){
        $data=[];
        $params=[];
        try {
            if(empty($this->get('bulan'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Bulan harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                $bulan = substr($this->get('bulan'),-2);
				$tahun = substr($this->get('bulan'),0,4);
                
                $params['tahun_bulan'] = $this->get('bulan');
                $params['np'] = $this->data_karyawan->np_karyawan; //$params['np'] = '6826';
                
                $no=0;
                $get_data_cuti = $this->cuti->get_persetujuan($params)->result();
                foreach($get_data_cuti as $tampil){
                    $row=[];
                    $no++;
                    $row['no'] = $no;
                    $row['id'] = $tampil->id;
                    $row['tanggal'] = $tampil->start_date!=null?$tampil->start_date:($tampil->end_date!=null?$tampil->end_date:null);
                    $row['np_karyawan'] = $tampil->np_karyawan;
                    $row['nama'] = $tampil->nama;
                    $row['uraian'] = $tampil->uraian;
                    
                    if($tampil->start_date) {
                        $row['start_date'] = tanggal_indonesia($tampil->start_date);
                    } else {
                        $row['start_date'] = '';
                    }

                    if($tampil->end_date) {
                        $row['end_date'] = tanggal_indonesia($tampil->end_date);
                    } else {
                        $row['end_date'] = '';
                    }
                    
                    if($tampil->jumlah_bulan) {
                        $row['durasi'] = $tampil->jumlah_bulan." bulan ".$tampil->jumlah_hari." hari";		
                    } else {
                        $row['durasi'] = $tampil->jumlah_hari." hari ";
                    }
                    $row['alasan'] = $tampil->alasan;
                    $row['keterangan'] = $tampil->keterangan=='1' ? 'Dalam Kota':($tampil->keterangan=='2'?'Luar Kota':'');
                    
                    $status_cuti = status_cuti([
                        'status_1'=>$tampil->status_1,
                        'status_2'=>$tampil->status_2,
                        'approval_2'=>$tampil->approval_2,
                        'approval_sdm'=>$tampil->approval_sdm
                    ]);

                    $row['status_cuti'] = status_cuti_web([
                        'status_1'=>$tampil->status_1,
                        'status_2'=>$tampil->status_2,
                        'approval_2'=>$tampil->approval_2,
                        'approval_sdm'=>$tampil->approval_sdm,
                        'approval_1_date'=>$tampil->approval_1_date,
                        'approval_2_date'=>$tampil->approval_2_date
                    ]);
                    
                    $row['sbg_approval_ke'] = $tampil->field_approval;
                    
                    switch ($tampil->field_approval) {
                        case "1":
                            $row['dapat_melakukan_approval'] = $status_cuti=='Menunggu Atasan 1' ? true:false;
                            break;
                        case "2":
                            $row['dapat_melakukan_approval'] = $status_cuti=='Menunggu Atasan 2' ? true:false;
                            break;
                    }
                    
                    $data[]=$row;
                }
                
                $this->response([
                    'status'=>true,
                    'message'=>'Permohonan Persetujuan Cuti bulan '.id_to_bulan($bulan)." ".$tahun,
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
    
    function index_post(){
        $data = [];
        $data_insert = [];
        
        try {
            if(empty($this->post('id'))){
                $this->response([
                    'status'=>false,
                    'message'=>"ID harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($this->post('posisi'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Posisi harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($this->post('status'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Status harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                $posisi = $this->post('posisi');
                
                # cek exist
                $cek_cuti = $this->db->where(['id'=>$this->post('id')])->get('ess_cuti');
                
                if($cek_cuti->num_rows()>0){
                    switch ($this->post('status')) {
                        case "1":
                            $data_insert['status_'.$posisi] = '1';
                            break;
                        case "2":
                            $data_insert['status_'.$posisi] = '2';
                            if(empty($this->post('keterangan'))){
                                $this->response([
                                    'status'=>false,
                                    'message'=>"Penolakan harus disertai keterangan",
                                    'data'=>[]
                                ], MY_Controller::HTTP_BAD_REQUEST);exit;
                            } else{
                                $data_insert['approval_alasan_'.$posisi] = $this->post('keterangan');
                            }
                            break;
                    }
                    $data_insert['approval_'.$posisi.'_date'] = date('Y-m-d H:i:s');
                    $data_insert['updated_at'] = date('Y-m-d H:i:s');
                    //$data_insert['updated_by'] = $this->data_karyawan->np_karyawan;
                    
                    # update
                    $this->db->where(['id'=>$this->post('id')])->update('ess_cuti', $data_insert);
                    
                    if($this->db->affected_rows()>0){
                        
                        # update ke cico jika sudah setuju semua
                        //$row_cuti = $cek_cuti->row();
                        
                        $this->response([
                            'status'=>true,
                            'message'=>'Berhasil melakukan approval',
                            'data'=>$data_insert
                        ], MY_Controller::HTTP_OK);
                    } else{
                        $this->response([
                            'status'=>false,
                            'message'=>'Tidak dapat mengupdate ke database.',
                            'data'=>[]
                        ], MY_Controller::HTTP_INTERNAL_SERVER_ERROR);
                    }
                } else{
                    $this->response([
                        'status'=>false,
                        'message'=>'Data cuti tidak ditemukan.',
                        'data'=>[]
                    ], MY_Controller::HTTP_NOT_FOUND);
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
