<?php defined('BASEPATH') OR exit('No direct script access allowed');
include_once( APPPATH . 'core/Group_Controller.php' );
class Persetujuan extends Group_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->helper("tanggal_helper");
        $this->load->helper("perizinan_helper");
        $this->load->model("api/filter/M_filter_api","filter");
        $this->load->model("api/M_perizinan_api","perizinan");
    }
    
//    function index_get(){
//        $data=[];
//        $params=[];
//        try {
//            $this->response([
//                'status'=>false,
//                'message'=>"Akses ditutup",
//                'data'=>[]
//            ], MY_Controller::HTTP_BAD_REQUEST);
            
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
                $bulan = substr($this->get('bulan'),-2);
				$tahun = substr($this->get('bulan'),0,4);
                
                $jenis_perizinan = $this->perizinan->get_jenis_by_id($this->get('jenis_perizinan'))->row();
                
                $params['table_name'] = 'ess_perizinan_'.$tahun.'_'.$bulan;
                $params['jenis_izin'] = [$jenis_perizinan->kode_pamlek];
                $params['np'] = $this->data_karyawan->np_karyawan;; 
                //$this->data_karyawan->np_karyawan;
                
                $no=0;
                $get_data_izin = $this->perizinan->get_persetujuan($params)->result();
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
                    
                    $row['sbg_approval_ke'] = $tampil->field_approval;
                    
                    switch ($tampil->field_approval) {
                        case "1":
                            $row['dapat_melakukan_approval'] = $row['status_perizinan']=='Menunggu Atasan 1' ? true:false;
                            break;
                        case "2":
                            $row['dapat_melakukan_approval'] = $row['status_perizinan']=='Menunggu Atasan 2' ? true:false;
                            break;
                    }
                    
                    $data[]=$row;
                }
                
                $this->response([
                    'status'=>true,
                    'message'=>'Permohonan Persetujuan '.$jenis_perizinan->nama.' bulan '.id_to_bulan($bulan)." ".$tahun,
                    'data'=>$data
                ], MY_Controller::HTTP_OK);
            }*/
//        } catch(Exception $e){
//            $this->response([
//                'status'=>false,
//                'message'=>'Error Exception',
//                'data'=>$data
//            ], MY_Controller::HTTP_BAD_REQUEST);
//        }
//    }

    # index_get() diedit tgl 2021-05-10
    function index_get(){
        $data = [];
        $this->db->select("a.*, (CASE WHEN start_date is not null then start_date else end_date end) as ordere")->from("ess_request_perizinan a");

        if(@$this->get('bulan')){
            $this->db->group_start();
            $this->db->where("(DATE_FORMAT(start_date,'%Y-%m')='{$this->get('bulan')}')");
            $this->db->or_where("(DATE_FORMAT(end_date,'%Y-%m')='{$this->get('bulan')}')");
            $this->db->group_end();
        }

        $kode_pamlek = @$this->get('kode_pamlek') ? $this->get('kode_pamlek'):'0';
        if( @$this->get('kode_erp') ){
            $this->db->where('a.kode_pamlek', $kode_pamlek);
            $this->db->where('CONCAT(a.info_type,"|",a.absence_type)', $this->get('kode_erp'));
        }

        if( $this->id_group=='5' ){ # pengguna
            $this->db->where('((approval_1_np="'.$this->data_karyawan->np_karyawan.'" AND approval_1_status is null) OR (approval_2_np="'.$this->data_karyawan->np_karyawan.'" AND approval_2_status is null))');
		} else if( $this->id_group=='4' ){ # pengadministrasi
			$list_pengadministrasi = array_column($this->list_pengadministrasi, 'kode_unit');
			$this->db->where_in('a.kode_unit', $list_pengadministrasi);
		} else if( $this->id_group=='1' ){ # superadmin
			# gak ada filter
		} else{
			$this->db->where('a.id', null);
		}

        $this->db->where('a.np_batal IS NULL',null,false);

        $this->db->order_by('(CASE WHEN a.start_date IS NOT NULL THEN a.start_date ELSE a.end_date END)', 'DESC');
		$this->db->order_by('(CASE WHEN a.start_time IS NOT NULL THEN a.start_time ELSE a.end_time END)', 'DESC');
        $this->db->order_by('a.np_karyawan', 'DESC');
        $query = $this->db->get()->result_array();

        foreach($query as $field){
            $row = $field;

            # pos
            if($field['pos']!=null){
                $pos = json_decode($field['pos']);
            } else{
                $pos = null;
            }
            $row['pos'] = $pos;

            # approval_pengamanan_posisi
            if($field['approval_pengamanan_posisi']!=null){
                $approval_pengamanan_posisi = json_decode($field['approval_pengamanan_posisi']);
            } else{
                $approval_pengamanan_posisi = null;
            }
            $row['approval_pengamanan_posisi'] = $approval_pengamanan_posisi;

            $data[] = $row;
        }

        $this->response([
			'status'=>true,
			'message'=>'Success',
			'data'=>$data
		], MY_Controller::HTTP_OK);
    }
    
    // function index_get(){ function ini diganti atasnya
    //     $data=[];
    //     $params=[];
    //     try {
    //         if(empty($this->get('bulan'))){
    //             $this->response([
    //                 'status'=>false,
    //                 'message'=>"Bulan harus diisi",
    //                 'data'=>[]
    //             ], MY_Controller::HTTP_BAD_REQUEST);
    //         } else{
    //             $bulan = substr($this->get('bulan'),-2);
	// 			$tahun = substr($this->get('bulan'),0,4);
                
    //             $params['table_name'] = 'ess_perizinan_'.$tahun.'_'.$bulan;
    //             if(@$this->get('jenis_perizinan')){
    //                 $jenis_perizinan = $this->perizinan->get_jenis_by_id($this->get('jenis_perizinan'))->row();
    //                 $params['jenis_izin'] = [$jenis_perizinan->kode_pamlek];
    //             }
    //             $params['np'] = $this->data_karyawan->np_karyawan;; 
    //             //$this->data_karyawan->np_karyawan;
                
    //             $no=0;
    //             $get_data_izin = $this->perizinan->get_persetujuan($params)->result();
    //             // $a = $this->db->last_query();
    //             foreach($get_data_izin as $tampil){
    //                 $row=[];
    //                 $no++;
    //                 $row['no'] = $no;
    //                 $row['id'] = $tampil->id;
    //                 $row['tanggal'] = $tampil->start_date!=null?$tampil->start_date:($tampil->end_date!=null?$tampil->end_date:null);
    //                 $row['np_karyawan'] = $tampil->np_karyawan;
    //                 $row['nama'] = $tampil->nama;
    //                 $row['nama_perizinan'] = get_perizinan_name($tampil->kode_pamlek)->nama;
                    
    //                 if($tampil->start_date) {
    //                     $row['start'] = tanggal_indonesia($tampil->start_date).', '.$tampil->start_time;
    //                 } else {
    //                     $row['start'] = '';
    //                 }

    //                 if($tampil->end_date) {
    //                     $row['end'] = tanggal_indonesia($tampil->end_date).', '.$tampil->end_time;
    //                 } else {
    //                     $row['end'] = '';
    //                 }
    //                 $row['status_perizinan'] = status_perizinan([
    //                     'kode_pamlek'=>$tampil->kode_pamlek,
    //                     'approval_1_status'=>$tampil->approval_1_status,
    //                     'approval_2_status'=>$tampil->approval_2_status,
    //                     'is_machine'=>$tampil->is_machine,
    //                     'pengguna_status'=>$tampil->pengguna_status
    //                 ]);
                    
    //                 $row['sbg_approval_ke'] = $tampil->field_approval;
                    
    //                 /*switch ($tampil->field_approval) {
    //                     case "1":
    //                         $row['dapat_melakukan_approval'] = $row['status_perizinan']=='Menunggu Atasan 1' ? true:false;
    //                         break;
    //                     case "2":
    //                         $row['dapat_melakukan_approval'] = $row['status_perizinan']=='Menunggu Atasan 2' ? true:false;
    //                         break;
    //                 }*/
    //                 $row['dapat_melakukan_approval'] = true;
    //                 $data[]=$row;
    //             }
                
    //             $this->response([
    //                 'status'=>true,
    //                 'message'=>'Permohonan Persetujuan '.(@$jenis_perizinan->nama ? $jenis_perizinan->nama:'Perizinan').' bulan '.id_to_bulan($bulan)." ".$tahun,
    //                 'data'=>$data
    //             ], MY_Controller::HTTP_OK);
    //         }
    //     } catch(Exception $e){
    //         $this->response([
    //             'status'=>false,
    //             'message'=>'Error Exception',
    //             'data'=>$data
    //         ], MY_Controller::HTTP_BAD_REQUEST);
    //     }
    // }
    
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
            } else if(empty($this->post('tanggal'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Tanggal harus diisi",
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
                $date = $this->post('tanggal');
                $posisi = $this->post('posisi');
                
                # cek exist
                $cek_izin = $this->db->where(['id'=>$this->post('id')])->get('ess_perizinan_'.date('Y_m', strtotime($date)));
                
                if($cek_izin->num_rows()>0){
                    switch ($this->post('status')) {
                        case "1":
                            $data_insert['approval_'.$posisi.'_status'] = '1';
                            break;
                        case "2":
                            $data_insert['approval_'.$posisi.'_status'] = '2';
                            if(empty($this->post('keterangan'))){
                                $this->response([
                                    'status'=>false,
                                    'message'=>"Penolakan harus disertai keterangan",
                                    'data'=>[]
                                ], MY_Controller::HTTP_BAD_REQUEST);exit;
                            } else{
                                $data_insert['approval_'.$posisi.'_keterangan'] = $this->post('keterangan');
                            }
                            break;
                    }
                    $data_insert['approval_'.$posisi.'_updated_at'] = date('Y-m-d H:i:s');
                    # update
                    $this->db->where(['id'=>$this->post('id')])->update('ess_perizinan_'.date('Y_m', strtotime($date)), $data_insert);
                    
                    if($this->db->affected_rows()>0){
                        
                        # update ke cico jika sudah setuju semua
                        //$row_izin = $cek_izin->row();
                        
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
                        'message'=>'Data perizinan tidak ditemukan.',
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
