<?php defined('BASEPATH') OR exit('No direct script access allowed');
include_once( APPPATH . 'core/Group_Controller.php' );
class Persetujuan extends Group_Controller {
    
    function __construct(){
        parent::__construct();
        if(!in_array($this->id_group,[7,15])){
            $this->response([
                'status'=>false,
                'message'=>"Hanya bisa diakses oleh otoritas: Admin Pamsiknilmat",
                'data'=>[]
            ], MY_Controller::HTTP_FORBIDDEN);
        }
        $this->load->helper("tanggal_helper");
        $this->load->helper("perizinan_helper");
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
            } else if(empty($this->post('pos'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Pos harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($this->post('waktu'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Waktu harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($this->post('posisi'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Posisi (keluar/masuk) harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                $izin = $this->db->where('id', $this->post('id'))->get('ess_request_perizinan');
                if($izin->num_rows()==1){
                    $simpan = array();
                    $set_date_real = array();
                    $row = $izin->row();
                    $tgl = $row->start_date!=null ? $row->start_date : $row->end_date;
                    $bulan = date('Y_m', strtotime($tgl));
                    $tabel_bulan = 'ess_perizinan_'.$bulan;
                    $tabel_cico = 'ess_cico_'.$bulan;
                    $tabel = 'ess_request_perizinan';

                    $pengamanan = $row->approval_pengamanan_posisi;
					if ($pengamanan!=null) {
						$get_posisi = json_decode($pengamanan);
						foreach ($get_posisi as $val) {
							$simpan[] = $val;
							if ($val->status=="1")
								$set_date_real[] = $val;
						}
					}

                    $set['pos'] = $this->post('pos');
					$set['nama_pos'] = $this->db->where('id', $this->post('pos'))->get('mst_pos')->row()->nama;
					$set['waktu'] = $this->post('waktu');
					$set['posisi'] = $this->post('posisi');
					$set['np_approver'] = $this->data_karyawan->np_karyawan;
					$set['nama_approver'] = $this->data_karyawan->nama;
                    $set['created'] = date('Ymd-His');
					$set['status'] = '1';

                    $simpan[] = $set;
                    $save = [];
                    $save_bln = [];
					$save['approval_pengamanan_posisi'] = json_encode($simpan);
					$save['approval_pengamanan_np'] = $set['np_approver'];
					$set_date_real[] = $set;
					$set_date_realisasi = json_encode($set_date_real);

                    $get_date = array_column(json_decode($set_date_realisasi, true), 'waktu');
					sort($get_date);
					$jml_date = count($get_date);

                    if( $row->kode_pamlek=='0' ){
                        $start_date = null;
                        $start_time = null;
                        
                        $end_date = date('Y-m-d', strtotime($get_date[($jml_date-1)]));
                        $end_time = date('H:i:s', strtotime($get_date[($jml_date-1)]));
                        
                        $save['start_date'] = $start_date;
                        $save['start_time'] = $start_time;
                        $save['end_date'] = $end_date;
                        $save['end_time'] = $end_time;

                        $save_bln['start_date'] = $start_date;
                        $save_bln['start_time'] = $start_time;
                        $save_bln['end_date'] = $end_date;
                        $save_bln['end_time'] = $end_time;
                    } else{
                        if ($jml_date>0) {
			                $start_date = date('Y-m-d', strtotime($get_date[0]));
			                $start_time = date('H:i:s', strtotime($get_date[0]));
			                $end_date = date('Y-m-d', strtotime($get_date[($jml_date-1)]));
			                $end_time = date('H:i:s', strtotime($get_date[($jml_date-1)]));
			            } else {
			            	$start_date = date('Y-m-d', strtotime($row->start_date_input));
			                $start_time = date('H:i:s', strtotime($row->start_date_input));
			                $end_date = date('Y-m-d', strtotime($row->end_date_input));
			                $end_time = date('H:i:s', strtotime($row->end_date_input));
			            }

		                if ($jml_date>1 || $jml_date==0) {
		                    $save['start_date'] = $start_date;
		                    $save['start_time'] = $start_time;
		                    $save['end_date'] = $end_date;
		                    $save['end_time'] = $end_time;

		                    $save_bln['start_date'] = $start_date;
		                    $save_bln['start_time'] = $start_time;
		                    $save_bln['end_date'] = $end_date;
		                    $save_bln['end_time'] = $end_time;
		                } else if ($jml_date==1) {
		                    $save['start_date'] = $start_date;
		                    $save['start_time'] = $start_time;
		                    $save_bln['start_date'] = $start_date;
		                    $save_bln['start_time'] = $start_time;

                            $end_date_realisasi = date('Y-m-d H:i:s', strtotime($get_date[($jml_date-1)]));
		                    if ($end_date_realisasi > $row->end_date_input) {
		                        $save['end_date'] = null;
		                        $save['end_time'] = null;
		                        $save_bln['end_date'] = null;
		                        $save_bln['end_time'] = null;
		                    } else {
			                	$end_date = date('Y-m-d', strtotime($row->end_date_input));
			                	$end_time = date('H:i:s', strtotime($row->end_date_input));

		                    	$save['end_date'] = $end_date;
		                    	$save['end_time'] = $end_time;
		                    	$save_bln['end_date'] = $end_date;
		                    	$save_bln['end_time'] = $end_time;
		                    }
		                }
                    }

                    if( $row->id_perizinan!=null ){
						$this->db->where('id', $row->id_perizinan)->set($save_bln)->update($tabel_bulan);
                    }
					$this->db->where('id', $this->post('id'))->set($save)->update('ess_request_perizinan');

                    if ($this->db->affected_rows() > 0) {
                        $izin_new = $this->db->where('id', $this->post('id'))->get('ess_request_perizinan')->row_array();
                        $check_id_perizinan = $izin_new["id_perizinan"];

                        if ($check_id_perizinan=='' || $check_id_perizinan==null) {
					
                            unset($izin_new["id"]);
                            unset($izin_new["id_perizinan"]);
                            unset($izin_new["alasan_batal"]);
                            unset($izin_new["np_batal"]);
                            unset($izin_new["date_batal"]); 
                            unset($izin_new["pos"]);
                            unset($izin_new["approval_pengamanan_np"]);
                            unset($izin_new["approval_pengamanan_posisi"]);
                            
    
                            $this->db->query("CREATE TABLE IF NOT EXISTS $tabel_bulan LIKE ess_perizinan");
                            $this->db->set($izin_new)->insert($tabel_bulan);
                            $id_perizinan = $this->db->insert_id();
                            $this->db->where('id', $this->post('id'))->set('id_perizinan', $id_perizinan)->update('ess_request_perizinan');
    
                            $parameter_perizinan = [
                                'id_row_baru'=>$id_perizinan,
                                'np_karyawan'=>$izin_new['np_karyawan'],
                                'date_start'=>$izin_new['start_date'],
                                'date_end'=>$izin_new['end_date']
                            ];
                            $this->update_cico($parameter_perizinan);
                        }
                    }

                    $this->response([
                        'status'=>true,
                        'message'=>'Berhasil melakukan approval',
                        'data'=>[]
                    ], MY_Controller::HTTP_OK);
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

    function update_cico($data_lempar){
        $tanggal_proses = ($data_lempar['date_start']==null || $data_lempar['date_start']=='') ? $data_lempar['date_end'] : $data_lempar['date_start'];
        
        while($tanggal_proses <= $data_lempar['date_end']){
            $tahun_bulan = str_replace('-','_',substr("$tanggal_proses", 0, 7));
            
            //cek table exist
            $get_table = $this->db->select('TABLE_NAME')->where('table_schema', $this->db->database)->where('TABLE_NAME', 'ess_cico_'.$tahun_bulan)->get('information_schema.`TABLES`');
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
            
            $tanggal_proses = date ("Y-m-d", strtotime("+1 day", strtotime($tanggal_proses)));
        }
    }

    function process_update_cico($data_lempar){
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
