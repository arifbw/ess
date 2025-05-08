<?php defined('BASEPATH') OR exit('No direct script access allowed');
include_once( APPPATH . 'core/Group_Controller.php' );
class Data extends Group_Controller {
    
    function __construct(){
        parent::__construct();
        if(!in_array($this->id_group,[7,15])){
            $this->response([
                'status'=>false,
                'message'=>"Hanya bisa diakses oleh otoritas: Admin Pamsiknilmat",
                'data'=>[]
            ], MY_Controller::HTTP_FORBIDDEN);
        }
        $this->load->helper("cutoff_helper");
        $this->load->helper("tanggal_helper");
        $this->load->model("api/perizinan_keamanan/M_data_perizinan","perizinan");
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
                $params['bulan'] = $this->get('bulan');

                if( $this->get('jenis_perizinan') ){
                    $jenis = $this->db->where('id',$this->get('jenis_perizinan'))->get('mst_perizinan')->row();
                    if(@$jenis->id!=null){
                        $kode_pamlek = $jenis->kode_pamlek;
                        $absence_type = explode('|',$jenis->kode_erp)[1];
                        $info_type = explode('|',$jenis->kode_erp)[0];
                        
                        $params['kode_pamlek'] = $kode_pamlek;
                        $params['absence_type'] = $absence_type;
                        $params['info_type'] = $info_type;
                    }
                }

                if( $this->get('pos') ){
                    $params['pos'] = $this->get('pos');
                }

                if( $this->get('start_date') ){
                    $params['start_date'] = $this->get('start_date');
                    if( $this->get('end_date') ) $params['end_date'] = $this->get('end_date');
                    else $params['end_date'] = $this->get('start_date');
                }

                $data = $this->perizinan->get_data($params)->result_array();
                for ($i=0; $i < count($data) ; $i++) { 
                    $data[$i]['approval_pengamanan_posisi'] = json_decode($data[$i]['approval_pengamanan_posisi']);
                    $data[$i]['pos'] = json_decode($data[$i]['pos']);

                    //cutoff ERP
					if($data[$i]['start_date']) {
						$tanggal_check = $data[$i]['start_date'];
					} else {
						$tanggal_check = $data[$i]['end_date'];
					}
					
					$sudah_cutoff = sudah_cutoff($tanggal_check);
                    if($sudah_cutoff) {
						$is_keamanan = false;
					} else{
                        if ($data[$i]['np_batal']==null || $data[$i]['np_batal']=='') {
							$is_keamanan = true;
						} else {
							$is_keamanan = false;
						}
                    }
                    $data[$i]['is_keamanan'] = $is_keamanan;

                    # date input
                    if($data[$i]['start_date_input']) {
                        $data[$i]['start_date_input'] = tanggal_indonesia(date('Y-m-d', strtotime($data[$i]['start_date_input']))).', '.date('H:i:s', strtotime($data[$i]['start_date_input']));
                    } else {
                        $data[$i]['start_date_input'] = '';
                    }
                    
                    if($data[$i]['end_date_input']) {
                        $data[$i]['end_date_input'] = tanggal_indonesia(date('Y-m-d', strtotime($data[$i]['end_date_input']))).', '.date('H:i:s', strtotime($data[$i]['end_date_input']));
                    } else {
                        $data[$i]['end_date_input'] = '';
                    }
                    # date input
                    
                    # date realisasi
                    if($data[$i]['start_date']) {
                        $data[$i]['start_date_realisasi'] = tanggal_indonesia($data[$i]['start_date']).', '.$data[$i]['start_time'];
                    } else {
                        $data[$i]['start_date_realisasi'] = '';
                    }

                    if($data[$i]['end_date']) {
                        $data[$i]['end_date_realisasi'] = tanggal_indonesia($data[$i]['end_date']).', '.$data[$i]['end_time'];
                    } else {
                        $data[$i]['end_date_realisasi'] = '';
                    }
                    # date realisasi
                    
                    $data[$i]['sumber_data'] = $data[$i]['is_machine']=='1'?'Mesin Pamlek':'ESS';
                }
                
                $this->response([
                    'status'=>true,
                    'message'=>"Persetujuan Keamanan Bulan ".id_to_bulan($bulan)." $tahun",
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
