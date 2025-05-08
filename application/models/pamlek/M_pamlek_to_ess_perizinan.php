<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_pamlek_to_ess_perizinan extends CI_Model {
    
    public function __construct(){
		parent::__construct();
		$this->table_schema = $this->db->database;
	}
	
	function update_files($nama_file, $data) {
		$this->db->where('nama_file', $nama_file);
		$this->db->update('ess_approver_files', $data); 
	}
	
	function check_id_then_insert_data($data, $table_name) {
        $where = [
            'np_karyawan'=>$data['np_karyawan'],
            'start_date'=>$data['start_date'],
            'start_time'=>$data['start_time'],
            'end_date'=>$data['end_date'],
            'end_time'=>$data['end_time'],
            'kode_pamlek'=>$data['kode_pamlek']
        ];
        $cek = $this->db->where($where)->get($table_name)->num_rows();
        if($cek<1){
            $data['created_at'] = date('Y-m-d H:i:s'); # heru menambahkan line ini 2020-11-18 @14:51
            $proses = $this->db->insert($table_name, $data);
            
            $new_id = $this->db->insert_id();
            $date = ($data['start_date']!=NULL ? $data['start_date']:$data['end_date']);
            $time = ($data['start_time']!=NULL ? $data['start_time']:$data['end_time']);
            
            $arr_update = [
                'new_id'=>$new_id,
                'start_date'=>$data['start_date'],
                'end_date'=>$data['end_date'],
                'np_karyawan'=>$data['np_karyawan']
            ];
            $this->update_cico($arr_update);
            
            return true;
        } else{
            $proses = $this->db->where($where)->update($table_name, $data);
            return true;
        }
	}
    
    function update_cico($data_lempar){
        if($data_lempar['start_date']!=NULL && $data_lempar['end_date']!=NULL){
            $tanggal_proses = $data_lempar['start_date'];
			# Tri Wibowo 7648 Menambah validasi dws out karena ada dws out tanggal yang sama, jadi sidt nyasar, 2021-01-27 @11:27
			$end_time = $data_lempar['end_time'];
			 
            while($tanggal_proses <= $data_lempar['end_date']){
                $tahun_bulan = str_replace('-','_',substr("$tanggal_proses", 0, 7));

                //cek table exist
                $get_table = $this->db->select('TABLE_NAME')->where('TABLE_SCHEMA', $this->table_schema)->where('TABLE_NAME', 'ess_cico_'.$tahun_bulan)->get('information_schema.`TABLES`');
                if($get_table->num_rows()>0){
                    //get cico
                    # $get_cico = $this->db->select('id, np_karyawan, id_perizinan, dws_tanggal')->where('np_karyawan', $data_lempar['np_karyawan'])->where('dws_tanggal', $tanggal_proses)->get('ess_cico_'.$tahun_bulan);
                    # heru mengganti query jadi ini, pertimbangan ada jam kerja yg lintas hari (gilir III), 2020-12-04 @09:33
					# Tri Wibowo 7648 Menambah validasi dws out karena ada dws out tanggal yang sama, jadi sidt nyasar, 2021-01-27 @11:27
                    $get_cico = $this->db->select('id, np_karyawan, id_perizinan, dws_tanggal')
                        ->where('np_karyawan', $data_lempar['np_karyawan'])
                        ->where("((CASE WHEN dws_out_tanggal_fix IS NOT NULL THEN dws_out_tanggal_fix ELSE dws_out_tanggal END)='$tanggal_proses')")
						->where("((CASE WHEN dws_out_fix IS NOT NULL THEN dws_out_fix ELSE dws_out END)>='$end_time')")
                        ->where("((CASE WHEN dws_name_fix IS NOT NULL THEN dws_name_fix ELSE dws_name END)!='OFF')")
                        ->get('ess_cico_'.$tahun_bulan);
                    # END: heru mengganti query jadi ini, pertimbangan ada jam kerja yg lintas hari (gilir III), 2020-12-04 @09:33

                    if($get_cico->num_rows()>0){
                        $data_to_process = array();
                        $row = $get_cico->result_array()[0];

                        $data_to_process = [
                            'id'=>$row['id'],
                            'id_perizinan'=>$row['id_perizinan'],
                            'tahun_bulan'=>str_replace('-','_',substr($row['dws_tanggal'], 0, 7)),
                            'id_row_baru'=>$data_lempar['new_id']
                        ];
                        $this->process_update_cico($data_to_process);

                    }
                }

                $tanggal_proses = date ("Y-m-d", strtotime("+1 day", strtotime($tanggal_proses)));
            }
        } else {
            if($data_lempar['start_date']!=NULL){
                $tahun_bulan = str_replace('-','_',substr($data_lempar['start_date'], 0, 7));
                $tanggal_proses = $data_lempar['start_date'];
            } else{
                $tahun_bulan = str_replace('-','_',substr($data_lempar['end_date'], 0, 7));
                $tanggal_proses = $data_lempar['end_date'];
            }
            
			# Tri Wibowo 7648 Menambah validasi dws out karena ada dws out tanggal yang sama, jadi sidt nyasar, 2021-01-27 @11:27
			$end_time = $data_lempar['end_time'];
			
            //cek table exist
            $get_table = $this->db->select('TABLE_NAME')->where('TABLE_SCHEMA', $this->table_schema)->where('TABLE_NAME', 'ess_cico_'.$tahun_bulan)->get('information_schema.`TABLES`');
            if($get_table->num_rows()>0){
                //get cico
                # $get_cico = $this->db->select('id, np_karyawan, id_perizinan, dws_tanggal')->where('np_karyawan', $data_lempar['np_karyawan'])->where('dws_tanggal', $tanggal_proses)->get('ess_cico_'.$tahun_bulan);
                # heru mengganti query jadi ini, pertimbangan ada jam kerja yg lintas hari (gilir III), 2020-12-04 @09:33
                $get_cico = $this->db->select('id, np_karyawan, id_perizinan, dws_tanggal')
                    ->where('np_karyawan', $data_lempar['np_karyawan'])
                    ->where("((CASE WHEN dws_out_tanggal_fix IS NOT NULL THEN dws_out_tanggal_fix ELSE dws_out_tanggal END)='$tanggal_proses')")
					->where("((CASE WHEN dws_out_fix IS NOT NULL THEN dws_out_fix ELSE dws_out END)>='$end_time')")
                    ->where("((CASE WHEN dws_name_fix IS NOT NULL THEN dws_name_fix ELSE dws_name END)!='OFF')")
                    ->get('ess_cico_'.$tahun_bulan);
                # END: heru mengganti query jadi ini, pertimbangan ada jam kerja yg lintas hari (gilir III), 2020-12-04 @09:33

                if($get_cico->num_rows()>0){
                    $data_to_process = array();
                    $row = $get_cico->result_array()[0];

                    $data_to_process = [
                        'id'=>$row['id'],
                        'id_perizinan'=>$row['id_perizinan'],
                        'tahun_bulan'=>str_replace('-','_',substr($row['dws_tanggal'], 0, 7)),
                        'id_row_baru'=>$data_lempar['new_id']
                    ];
                    $this->process_update_cico($data_to_process);

                }
            }
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
    
    /*function update_cico($data){
        $get_cico = $this->db->query("SELECT a.id, a.id_perizinan
                                FROM 
                                (
                                    SELECT id, id_perizinan,
                                    (CASE WHEN dws_in_tanggal_fix is not null then dws_in_tanggal_fix ELSE dws_in_tanggal END) as tanggal_in,
                                    (CASE WHEN dws_in_fix is not null then dws_in_fix ELSE dws_in END) as time_in,
                                    (CASE WHEN dws_out_tanggal_fix is not null then dws_out_tanggal_fix ELSE dws_out_tanggal END) as tanggal_out,
                                    (CASE WHEN dws_out_fix is not null then dws_out_fix ELSE dws_out END) as time_out
                                    FROM ess_cico_".$data['tahun_bulan']." 
                                    WHERE np_karyawan='".$data['np_karyawan']."'
                                ) a
                                WHERE '".$data['tanggal_izin']."' BETWEEN DATE_SUB(CONCAT(a.tanggal_in,' ',a.time_in), INTERVAL 5 HOUR) AND DATE_ADD(CONCAT(a.tanggal_out,' ',a.time_out), INTERVAL 6 HOUR)");
        
        $data_perizinan = $data['new_id'];
        if($get_cico->num_rows() == 1){
            $data_cico = $get_cico->row();
            $str_fix = '';
            $new_element = [];

            //str awal diambil dari id_perizinan di cico
            $str_awal = $data_cico->id_perizinan;
            //convert str_awal to array_awal
            $arr_awal = explode(',', $str_awal);

            //concat dari id tabel perizinan
            $str_datang = $data['new_id'];
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

            $this->db->where('id', $data_cico->id)->update('ess_cico_'.$data['tahun_bulan'], ['id_perizinan'=>$str_fix]);
        }
    }*/
}
