<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_perizinan extends CI_Model {
	
	public function set_cico($data){
	 	$set = $this->db->from('ess_cico')
	 			->where('dws_tanggal', $data['tgl_dws'])
	 			->where('np_karyawan', $data['np_karyawan'])
	 			->get();

	 	$get = $this->db->from('ess_perizinan')
	 			->where('start_date', $data['tgl_dws'])
	 			->where('np_karyawan', $data['np_karyawan'])
	 			->get()->result_array();

	 	if ($set->num_rows() == 1) {
	 		$cico = $set->row_array();
	 		$id_perizinan = implode(",", array_push(array_column($get, 'id'), $data['id']));
	 		//echo $id_perizinan;exit;
	 		$this->db->set('id_perizinan', $id_perizinan)->where('id', $cico['id'])->update('ess_cico');
	 	}
	}
    
    function update_cico($data){
        $this->load->helper('fungsi');
        if(check_table_exist("ess_cico_".$data['tahun_bulan'])=='ada'){
            $tabel_cico = "ess_cico_".$data['tahun_bulan'];
        } else{
            $tabel_cico = "ess_cico";
        }
        if(check_table_exist("ess_perizinan_".$data['tahun_bulan'])=='ada'){
            $tabel_izin = "ess_perizinan_".$data['tahun_bulan'];
        } else{
            $tabel_izin = "ess_perizinan";
        }
        /*$date_current = $data['date_time_in'];
        
        $date_min_1 = date("Y-m-d", strtotime("-1 day", strtotime($date_current)));
        $str_before = $date_min_1.' 23:59:59';
        $tahun_bulan_before = str_replace('-','_', substr($date_min_1,0,7));
        $table_before = (check_table_exist("ess_cico_$tahun_bulan_before")=='ada' ? "ess_cico_$tahun_bulan_before" : 'ess_cico');
        $get_cico_before = $this->db->select("(CASE WHEN dws_out_tanggal_fix IS NOT NULL THEN dws_out_tanggal_fix ELSE dws_out_tanggal END) AS dws_out_tanggal_before, (CASE WHEN dws_out_fix IS NOT NULL THEN dws_out_fix ELSE dws_out END) AS dws_out_before")->where(['np_karyawan'=>$data['np_karyawan'], 'dws_tanggal'=>$date_min_1])->get($table_before);
        if($get_cico_before->num_rows()==1){
            $get_cico_before_fix = $get_cico_before->row();
            $str_before = $get_cico_before_fix->dws_out_tanggal_before.' '.$get_cico_before_fix->dws_out_before;
        }
        $date_before_out = date('Y-m-d H:i:s', strtotime($str_before));
        
        $date_plus_1 = date("Y-m-d", strtotime("+1 day", strtotime($date_current)));
        $str_after = $date_plus_1.' 00:00:00';
        $tahun_bulan_after = str_replace('-','_', substr($date_plus_1,0,7));
        $table_after = (check_table_exist("ess_cico_$tahun_bulan_after")=='ada' ? "ess_cico_$tahun_bulan_after" : 'ess_cico');
        $get_cico_after = $this->db->select("(CASE WHEN dws_in_tanggal_fix IS NOT NULL THEN dws_in_tanggal_fix ELSE dws_in_tanggal END) AS dws_in_tanggal_after, (CASE WHEN dws_in_fix IS NOT NULL THEN dws_in_fix ELSE dws_in END) AS dws_in_after")->where(['np_karyawan'=>$data['np_karyawan'], 'dws_tanggal'=>$date_plus_1])->get($table_after);
        if($get_cico_after->num_rows()==1){
            $get_cico_after_fix = $get_cico_after->row();
            $str_after = $get_cico_after_fix->dws_in_tanggal_after.' '.$get_cico_after_fix->dws_in_after;
        }
        $date_after_in = date('Y-m-d H:i:s', strtotime($str_after));*/
        
        $get_cico = $this->db->query("SELECT id, id_perizinan
                                    FROM $tabel_cico
                                    WHERE np_karyawan='".$data['np_karyawan']."'
                                    AND (
                                        ((CONCAT(dws_in_tanggal_fix,' ',dws_in_fix))='".$data['date_time_in']."' OR (CONCAT(dws_in_tanggal,' ',dws_in))='".$data['date_time_in']."')
                                        AND
                                        ((CONCAT(dws_out_tanggal_fix,' ',dws_out_fix))='".$data['date_time_out']."' OR (CONCAT(dws_out_tanggal,' ',dws_out))='".$data['date_time_out']."')
                                    )");
        
		
		//jika OFF
		//belum digunakan
		$interval = 3;
		if ((strpos($data['date_time_in'], '00:00:00') !== false) AND (strpos($data['date_time_out'], '00:00:00') !== false)) 
		{
			$pisah_in 	= explode(" ",$data['date_time_in']);
			$date_in	= $pisah_in[0];
			$time_in	= $pisah_in[1];
			
			$data['date_time_in']= $date_in." ".$time_in;
			
			$pisah_out 	= explode(" ",$data['date_time_out']);
			$date_out	= $pisah_out[0];
			$time_out	= $pisah_out[1];
			$date_out	= date('Y-m-d', strtotime($date_out . ' +1 day'));
			
			$data['date_time_out'] = $date_out." ".$time_out;
			
			$interval = 6;
		}

        //$get_perizinan = $this->db->query("SELECT GROUP_CONCAT(id SEPARATOR ', ') AS id_perizinan FROM ess_perizinan_".$data['tahun_bulan']." WHERE np_karyawan=".$data['np_karyawan']." AND ((start_date BETWEEN '".$date_before_out."' AND '".$date_after_in."') OR (end_date BETWEEN '".$date_before_out."' AND '".$date_after_in."'))");

       
        //08 03 2024, Robi Purnomo - J971, Fix: perizinan salah masuk ke dws off berikutnya
        $date_in = $data['date_time_in'];
        $jam_in =  substr($date_in, -8);

        $date_out = $data['date_time_out'];
        $jam_out = substr($date_out, -8);

        $interval_in = 1;
        if($jam_in == "00:00:00" && $jam_out == "00:00:00")
        {
            $interval_in = 0;
        }

     

        $get_perizinan = $this->db->query("SELECT GROUP_CONCAT(id) AS id_perizinan 
                                        FROM $tabel_izin 
                                        WHERE 
                                        (approval_1_status != 2 OR approval_2_status != 2) AND
										np_karyawan='".$data['np_karyawan']."' AND 
										(
											(
												start_date != end_date
												OR (
													(
														start_date IS NULL
														AND end_date IS NOT NULL
													)
													OR (
														(
															start_date IS NOT NULL
															AND end_date IS NULL
														)
													)
												)
											)
											OR (
												(start_date = end_date)
												AND (
													(start_time != end_time)
													OR (
														(
															start_time IS NULL
															AND end_time IS NOT NULL
														)
														OR (
															(
																start_time IS NOT NULL
																AND end_time IS NULL
															)
														)
													)
												)
											)
										)
										AND /*check tidak boleh in=out*/
										(
                                            (kode_pamlek!='0' AND
                                            (
                                                (CONCAT(start_date,' ',start_time) BETWEEN DATE_SUB(CONCAT('".$data['date_time_in']."'), INTERVAL ".$interval_in." HOUR) AND DATE_ADD(CONCAT('".$data['date_time_out']."'), INTERVAL 1 HOUR)) OR 
				                                (CONCAT(end_date,' ',end_time) BETWEEN DATE_SUB(CONCAT('".$data['date_time_in']."'), INTERVAL ".$interval_in." HOUR) AND DATE_ADD(CONCAT('".$data['date_time_out']."'), INTERVAL 1 HOUR))
                                            ))
                                            OR
                                            (kode_pamlek='0' AND
                                            (
                                                (CONCAT(start_date,' ',start_time) BETWEEN '".$data['date_time_in']."' AND '".$data['date_time_out']."') OR 
                                                (CONCAT(end_date,' ',end_time) BETWEEN '".$data['date_time_in']."' AND '".$data['date_time_out']."')
                                            ))
                                        )");
        
        if($get_perizinan->num_rows()==1){
            $data_perizinan = $get_perizinan->row();
            if($get_cico->num_rows() == 1){
                $data_cico = $get_cico->row();
                $str_fix = '';
                $new_element = [];
                
                //str awal diambil dari id_perizinan di cico
                $str_awal = $data_cico->id_perizinan;
                //convert str_awal to array_awal
                // abaikan data yg sudah ada
			    //$arr_awal = explode(',', $str_awal);
			    $arr_awal = explode(',', '');
                
                //concat dari id tabel perizinan
                $str_datang = $data_perizinan->id_perizinan;
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
                /*if($data_cico->id_perizinan!=NULL){
                    $new_data_izin = $data_cico->id_perizinan.', '.$data_perizinan->id_perizinan;
                } else{
                    $new_data_izin = $data_perizinan->id_perizinan;
                }*/
                $this->db->where('id', $data_cico->id)->update($tabel_cico, ['id_perizinan'=>$str_fix]);
            }
        }
    }
	
	public function perizinan_karyawan_per_bulan($no_pokok,$periode){
		$data = $this->db->select("a.id")
						 ->select("b.nama")
						 ->select("concat(a.start_date,' ',a.start_time) mulai",false)
						 ->select("concat(a.end_date,' ',a.end_time) selesai",false)
						 ->from("ess_perizinan_".$periode." a")
						 ->join("mst_perizinan b", "concat(a.info_type,'|',a.absence_type) = b.kode_erp AND a.kode_pamlek=b.kode_pamlek", "left")
						 ->where("np_karyawan",$no_pokok)
						 ->get()->result_array();
		//echo $this->db->last_query();
		return $data;
	}
}
