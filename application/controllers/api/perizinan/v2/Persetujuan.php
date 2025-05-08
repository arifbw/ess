<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Persetujuan extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->helper("tanggal_helper");
        $this->load->helper("perizinan_helper");
        $this->load->model("api/filter/M_filter_api","filter");
        $this->load->model("api/M_perizinan_api","perizinan");
    }
    
    function index_post(){
        $data = [];
        $data_insert = [];
        
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
        } else{
            $id_ = $this->post('id');
            $tgl = $this->post('tanggal');
            $izin = $this->db->where('id', $id_)->get('ess_request_perizinan');
            $bulan = date('Y_m', strtotime($tgl));
            $tabel = 'ess_perizinan_'.$bulan;
            
            $set = [];
            if($izin->num_rows() == 1){
                if($izin->row()->np_batal != null){
                    $this->response([
                        'status'=>false,
                        'message'=>"Perizinan sudah dibatalkan",
                        'data'=>[]
                    ], MY_Controller::HTTP_BAD_REQUEST);
                    exit();
                }
                
                $tanggal = date('Y-m-d H:i:s');
				$status_1 = $this->post('status_1',true);
				$status_2 = $this->post('status_2',true);
                
                if ($status_1!='') {
					$set['approval_1_updated_at'] = $tanggal;
					$set['approval_1_status'] = $status_1;
					if($status_1=='2') {
						$set['approval_1_keterangan'] = $this->post('alasan_1');
					}
				}
				if ($status_2!='') {
					$set['approval_2_updated_at'] = $tanggal;
					$set['approval_2_status'] = $status_2;
					if($status_2=='2') {
						$set['approval_2_keterangan'] = $this->post('alasan_2');
					}
				}
                
                $this->db->where('id', $id_)->update('ess_request_perizinan', $set);
                if ($this->db->affected_rows() > 0) {
                    $izin_new = $this->db->where('id', $id_)->get('ess_request_perizinan')->row_array();
                    if ( ($izin_new['approval_2_np']!=null && $izin_new['approval_1_status']=='1' && $izin_new['approval_2_status']=='1') || ($izin_new['approval_2_np']==null && $izin_new['approval_1_status']=='1' ) ) {
                    // if ($izin_new['approval_1_status']=='1' && ($izin_new['approval_2_status']=='1' || ($izin_new['approval_2_status']==null && $izin_new['kode_pamlek']=='G'))) {
						unset($izin_new["id"]);
                        unset($izin_new["id_perizinan"]);
                        unset($izin_new["alasan_batal"]);
                        unset($izin_new["np_batal"]);
                        unset($izin_new["date_batal"]); 
                        unset($izin_new["pos"]);
                        unset($izin_new["approval_pengamanan_np"]);
                        unset($izin_new["approval_pengamanan_posisi"]);

                        $this->db->query("CREATE TABLE IF NOT EXISTS $tabel LIKE ess_perizinan");
						$this->db->insert($tabel, $izin_new);
						$id_perizinan = $this->db->insert_id();
						$this->db->where('id', $id_)->update('ess_request_perizinan', ['id_perizinan'=>$id_perizinan]);

						$parameter_perizinan = [
	                        'id_row_baru'=>$id_perizinan,
	                        'np_karyawan'=>$izin_new['np_karyawan'],
	                        'date_start'=>$izin_new['start_date'],
	                        'date_end'=>$izin_new['end_date']
	                    ];
                    	$this->update_cico($parameter_perizinan);
					}
                    
                    $this->response([
                        'status'=>true,
                        'message'=>'Berhasil Memberikan Approval',
                        'data'=>$data_insert
                    ], MY_Controller::HTTP_OK);
                } else{
                    $this->response([
                        'status'=>false,
                        'message'=>'Gagal Memberikan Approval! Cek Koneksi Anda.',
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
    }
    
    private function update_cico($data_lempar){
        // $tanggal_proses = $data_lempar['date_start']; # disamakan dg yg di web @2022-04-01
        $tanggal_proses = ($data_lempar['date_start']==null || $data_lempar['date_start']=='') ? $data_lempar['date_end'] : $data_lempar['date_start'];
        
        while($tanggal_proses <= $data_lempar['date_end']){ # disamakan dg yg di web @2022-04-01
            $tahun_bulan = str_replace('-','_',substr("$tanggal_proses", 0, 7));
            
            //cek table exist
            $get_table = $this->db->select('TABLE_NAME')->where('table_schema', $this->nama_db)->where('TABLE_NAME', 'ess_cico_'.$tahun_bulan)->get('information_schema.`TABLES`');
            if($get_table->num_rows()>0){
                //get cico
                $get_cico = $this->db->select('id, np_karyawan, id_perizinan, dws_tanggal')->where('np_karyawan', $data_lempar['np_karyawan'])->where('dws_tanggal', $tanggal_proses)->get('ess_cico_'.$tahun_bulan);
                
                if($get_cico->num_rows()>0){
                    $data_to_process = array();
                    $row = $get_cico->row_array();

                    $data_to_process = [
                        'id'=>$row['id'],
                        'id_perizinan'=>$row['id_perizinan'],
                        'tahun_bulan'=>str_replace('-','_',substr($row['dws_tanggal'], 0, 7)),
                        'id_row_baru'=>$data_lempar['id_row_baru']
                    ];
                    $this->process_update_cico($data_to_process);
                    
                }
            }
            
            $tanggal_proses = date ("Y-m-d", strtotime("+1 day", strtotime($tanggal_proses))); # disamakan dg yg di web @2022-04-01
        }
    }
    
    private function process_update_cico($data_lempar){
        //$data_cico = $get_cico->row();
        $str_fix = '';
        $new_element = [];

        //str awal diambil dari id_perizinan di cico
        $str_awal = $data_lempar['id_perizinan'];
        //convert str_awal to array_awal
        $arr_awal = explode(',', $str_awal);

        //concat dari id tabel perizinan
        $str_datang = $data_lempar['id_row_baru'];
        //convert str_datang to array_datang
        $arr_datang = explode(',', $str_datang);

        //found elements of arr_datang where not in arr_awal
        $new_elements=array_diff($arr_datang, $arr_awal);

        foreach($new_elements as $value){
            //push new element to arr_awal
            $arr_awal[] = $value;
        }

        //convert arr_awal to str
        $str_awal = implode(',', $arr_awal);
        $str_fix = trim($str_awal,',');

        $this->db->where('id', $data_lempar['id'])->update('ess_cico_'.$data_lempar['tahun_bulan'], ['id_perizinan'=>$str_fix]);
    }
}
