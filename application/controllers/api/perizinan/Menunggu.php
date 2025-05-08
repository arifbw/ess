<?php defined('BASEPATH') OR exit('No direct script access allowed');
include_once( APPPATH . 'core/Group_Controller.php' );
class Menunggu extends Group_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->helper("tanggal_helper");
        $this->load->helper("perizinan_helper");
        $this->load->model("api/filter/M_filter_api","filter");
        $this->load->model("api/M_perizinan_api","perizinan");
    }
    
    function index_get(){
        $data=[];
        $params=[];
        try {
            $this->response([
                'status'=>false,
                'message'=>"Akses ditutup",
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
            
            /*if(empty($this->get('jenis_perizinan'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Jenis perizinan harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($this->get('bulan'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Bulan harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{                
                $jenis_perizinan = $this->perizinan->get_jenis_by_id($this->get('jenis_perizinan'))->row();
                
                $params['table_name'] = 'ess_perizinan_'.date('Y_m', strtotime($this->get('bulan')));
                $params['jenis_izin'] = [$jenis_perizinan->kode_pamlek];
                
                if($this->id_group==5){
                    $params['np'] = [$this->data_karyawan->np_karyawan];
                } else if($this->id_group==4){
                    $list = [];
                    foreach($this->list_pengadministrasi as $l){
                        $list[] = $l['kode_unit'];
                    }
                    $params['kode_unit'] = $list;
                }
                
                $no=0;
                $get_data_izin = $this->perizinan->menunggu_persetujuan($params)->result();
                foreach($get_data_izin as $tampil){
                    $row=[];
                    $no++;
                    $row['no'] = $no;
                    $row['id'] = $tampil->id;
                    $row['tanggal'] = $tampil->start_date!=null?$tampil->start_date:($tampil->end_date!=null?$tampil->end_date:null);
                    $row['np_karyawan'] = $tampil->np_karyawan;
                    $row['nama'] = $tampil->nama;
                    $row['nama_perizinan'] = get_perizinan_name($tampil->kode_pamlek)->nama;
                    
                    if($tampil->start_date) {
                        $row['start'] = tanggal_indonesia($tampil->start_date).', '.$tampil->start_time;
                    } else {
                        $row['start'] = '';
                    }

                    if($tampil->end_date) {
                        $row['end'] = tanggal_indonesia($tampil->end_date).', '.$tampil->end_time;
                    } else {
                        $row['end'] = '';
                    }
                    
                    $row['status_perizinan'] = status_perizinan([
                        'kode_pamlek'=>$tampil->kode_pamlek,
                        'approval_1_status'=>$tampil->approval_1_status,
                        'approval_2_status'=>$tampil->approval_2_status,
                        'is_machine'=>$tampil->is_machine,
                        'pengguna_status'=>$tampil->pengguna_status
                    ]);
                    $row['dapat_dibatalkan'] = $tampil->pengguna_status=='3'?false:true;
                    
                    $data[]=$row;
                }
                
                $this->response([
                    'status'=>true,
                    'message'=>'Data '.$jenis_perizinan->nama.' menunggu persetujuan',
                    'data'=>$data
                ], MY_Controller::HTTP_OK);
            }*/
        } catch(Exception $e){
            $this->response([
                'status'=>false,
                'message'=>'Error Exception',
                'data'=>$data
            ], MY_Controller::HTTP_BAD_REQUEST);
        }
    }
}
