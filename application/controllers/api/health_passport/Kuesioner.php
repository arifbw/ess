<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Kuesioner extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->model('api/health_passport/M_kuesioner_api','kuesioner');
        $this->load->model("master_data/M_karyawan");
        $this->item_kuesioner = [
            ['label'=>'pernah_keluar', 'text'=>'Apakah pernah keluar rumah/tempat umum (pasar, fasyankes, kerumunan orang, dll)?'],
            ['label'=>'transportasi_umum', 'text'=>'Apakah pernah menggunakan transportasi umum?'],
            ['label'=>'luar_kota', 'text'=>'Apakah pernah melakukan perjalanan ke luar kota/internasional (wilayah yang terjangkit/zona merah)?'],
            ['label'=>'kegiatan_orang_banyak', 'text'=>'Apakah anda mengikuti kegiatan yang melibatkan orang banyak?'],
            ['label'=>'kontak_pasien', 'text'=>'Apakah memiliki riwayat kontak erat dengan orang yang dinyatakan ODP, PDP, atau Confirm COVID-19?'],
            ['label'=>'sakit', 'text'=>'Apakah pernah mengalami demam/batuk/pilek/sakit tenggorokan/sesak nafas dalam 14 hari terakhir?'],
        ];
    }
    
	function index_get() {
        $np = $this->data_karyawan->np_karyawan;
        $data = $this->item_kuesioner;
        
        try {
            $this->response([
                'status'=>true,
                'message'=>'Item kuesioner',
                'note'=>'value: (1) Ya, (2) Tidak',
                'data'=>$data
            ], MY_Controller::HTTP_OK);
        } catch(Exception $e){
            $this->response([
                'status'=>false,
                'message'=>'Error Exception',
                'note'=>'',
                'data'=>$data
            ], MY_Controller::HTTP_BAD_REQUEST);
        }
	}
    
    function index_post(){
        $data = [];
        $data_insert = [];
        $np_karyawan = $this->data_karyawan->np_karyawan;
        
        try {
            if(empty($this->post('np'))){
                $this->response([
                    'status'=>false,
                    'message'=>"NP harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($this->post('tanggal'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Tanggal harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($this->post('kuesioner'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Kuesioner harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                $np_input = $this->post('np');
                $data_karyawan = $this->M_karyawan->get_detail_np($np_input);
                
                $tanggal = date('Y-m-d', strtotime($this->post('tanggal')));
                $kuesioner = $this->post('kuesioner');
                
                $data_insert['kode'] = $this->uuid->v4();;
                $data_insert['np_karyawan'] = $np_input;
                $data_insert['personel_number'] = $data_karyawan['personnel_number'];
                $data_insert['nama'] = $data_karyawan['nama'];
                $data_insert['nama_jabatan'] = $data_karyawan['nama_jabatan'];
                $data_insert['kode_unit'] = $data_karyawan['kode_unit'];
                $data_insert['nama_unit'] = $data_karyawan['nama_unit'];
                $data_insert['tanggal'] = $tanggal;
                $data_insert['is_status'] = '1';
                $data_insert['created_at'] = date('Y-m-d H:i:s');
                $data_insert['created_by'] = $np_karyawan;
                
                $params = ['np_karyawan'=>$np_input, 'tanggal'=>$tanggal];
                $cek = $this->kuesioner->check_if_exist($params);

                if($cek->num_rows()==0){
                    # kuesioner harus diisi lengkap
                    if(count($kuesioner)==count($this->item_kuesioner)){
                        foreach($kuesioner as $row){
                            $data_insert[$row['label']] = $row['value'];
                        }

                        $this->db->insert('ess_self_assesment_covid19',$data_insert);

                        $this->response([
                            'status'=>true,
                            'message'=>'Berhasil disimpan',
                            'data'=>$data_insert
                        ], MY_Controller::HTTP_OK);
                    } else{
                        $this->response([
                            'status'=>false,
                            'message'=>"Kuesioner Harus Diisi Lengkap",
                            'data'=>$data
                        ], MY_Controller::HTTP_BAD_REQUEST);
                    }
                } else{
                    $this->response([
                        'status'=>false,
                        'message'=>$data_karyawan['nama']." sudah Mengisi Health Passport pada tanggal $tanggal",
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
