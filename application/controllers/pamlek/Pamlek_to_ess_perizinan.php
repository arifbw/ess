<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pamlek_to_ess_perizinan extends CI_Controller {
	
	 
	function __construct()
	{
		parent::__construct();
		$this->load->model('pamlek/M_pamlek_to_ess_perizinan');
		$this->load->helper(['fungsi_helper','tanggal_helper','perizinan_helper']);
		
	}
    
    function get_data($start_date=null, $end_date=null, $np=null){
		//$this->output->enable_profiler(TRUE); 
        $machine = get_machine('string');
        $query_fix = '';
        $array_insert = [];
        
        set_time_limit('0');
        
        if($start_date==null && $end_date==null){
            echo 'Need parameters: Start date (Y-m-d) and End date (Y-m-d)';
        } else{
            /*if((validateDate($start_date)==false || validateDate($end_date)==false) && ($start_date!='today' || $end_date!='today')){
                echo 'Date not valid.';
                exit();
            }*/
            
            if($start_date=='today'){
                $start_date=date('Y-m-d');
            }
			
            if($end_date=='today'){
                $end_date=date('Y-m-d');
            }
			
			//02 03 2021 Tri Wibowo, Mengantisipasi belum ke running
			if($start_date=='today' && $end_date=='today')
			{
				$date_asli = date('Y-m-d');
				$time  = strtotime($date_asli);
				$day   = date('d',$time);
				$month = date('m',$time);
				$year  = date('Y',$time);
				$start_date	= $year.'-'.$month.'-03'; //karena akan di kurangi 2 hari jadi hasilnya tanggal 1
				$start_date = strtotime($start_date);
				$start_date = date('Y-m-d',$start_date);
				
				$end_date= date('Y-m-d');
				
				if($start_date>$end_date)
				{
					$start_date = date('Y-m-d');
				}
			}
			
			$start_date = date('Y-m-d', strtotime($start_date . ' -2 day'));
			$end_date = date('Y-m-d', strtotime($end_date . ' -2 day'));
            
            /*$start_date_min2 = date_minus_days($start_date, 2);
            $end_date_min2 = date_minus_days($end_date, 2);*/
            $bulan_tahun_start = substr(str_replace('-','_',$start_date), 0,7);
            $bulan_tahun_end = substr(str_replace('-','_',$end_date), 0,7);
            if($end_date<$start_date){
                echo 'End date harus lebih besar atau sama dengan Start date';
            } else if($bulan_tahun_start != $bulan_tahun_end){
                echo 'Hanya dalam bulan yang sama.';
            } else{
                if(check_table_exist("erp_master_data_$bulan_tahun_start")=='ada'){
                    $table_kry = "erp_master_data_$bulan_tahun_start";
                    $field_kry = 'np_karyawan';
                } else{
                    $table_kry = 'mst_karyawan';
                    $field_kry = 'no_pokok';
                }
                
                if(check_table_exist("ess_cico_$bulan_tahun_start")=='ada'){
                    $table_cico = "ess_cico_$bulan_tahun_start";
                } else{
                    $table_cico = 'ess_cico';
                }
                
                if(check_table_exist("pamlek_data_$bulan_tahun_start")=='ada'){
                    $table_pamlek = "pamlek_data_$bulan_tahun_start";
                } else{
                    $table_pamlek = 'pamlek_data';
                }
                
                $msc = microtime(true);
                echo '<br>Scanning data...<br>';
                echo 'Start : '.date('Y-m-d H:i:s').'<br><br>';
                $query_string_first = 
                    "SELECT a.no_pokok, b.nama, b.nama_jabatan, b.personnel_number, b.kode_unit, b.nama_unit, a.id, 
                            (CASE WHEN a.tapping_type!='0' THEN CONCAT(a.tapping_time,' ',a.machine_id)
                        /*  Bowo, Untuk Tapping SIDT tidak perlu ada from           
									ELSE (
									SELECT CONCAT(m.dws_in_tanggal,' ',m.dws_in,' ')
                                            FROM $table_cico m WHERE m.np_karyawan=a.no_pokok AND (a.tapping_time BETWEEN (CONCAT(m.dws_in_tanggal,' ',m.dws_in)) AND (CONCAT(m.dws_out_tanggal,' ',m.dws_out)))
						   ) 
						END of Bowo */    
							END
						   ) as date_from,
                            (CASE WHEN a.tapping_type='0' THEN CONCAT(a.tapping_time,' ',a.machine_id)
                                    ELSE (SELECT (CASE WHEN aa.in_out='1' THEN CONCAT(aa.tapping_time,' ',aa.machine_id)
                                                    WHEN aa.in_out='0' THEN CONCAT(DATE_SUB(aa.tapping_time, INTERVAL 1 MINUTE),' ',aa.machine_id)
                                                    ELSE (SELECT CONCAT(bb.dws_out_tanggal,' ',bb.dws_out,' ')
                                                        FROM $table_cico bb WHERE bb.np_karyawan=a.no_pokok AND (a.tapping_time BETWEEN (CONCAT((CASE WHEN bb.dws_in_tanggal_fix IS NOT NULL THEN bb.dws_in_tanggal_fix ELSE bb.dws_in_tanggal END),' ',(CASE WHEN bb.dws_in_fix IS NOT NULL THEN bb.dws_in_fix ELSE bb.dws_in END))) AND (CONCAT((CASE WHEN bb.dws_out_tanggal_fix IS NOT NULL THEN bb.dws_out_tanggal_fix ELSE bb.dws_out_tanggal END),' ',(CASE WHEN bb.dws_out_fix IS NOT NULL THEN bb.dws_out_fix ELSE bb.dws_out END)))))
                                                    END ) 
                                    FROM $table_pamlek aa 
									WHERE aa.no_pokok=a.no_pokok 
									AND DATE_FORMAT(aa.tapping_time,'%Y-%m-%d')=DATE_FORMAT(a.tapping_time,'%Y-%m-%d') 
									/*BOWO Belum tentu saat tapping balik ke kantor pake izin yg sama
									AND aa.tapping_type=a.tapping_type 
									END OF BOWO*/
									AND aa.tapping_time > a.tapping_time ORDER BY aa.tapping_time ASC LIMIT 1)
                             END) as date_to,
                            a.tapping_time, 
                            a.in_out, a.tapping_type, a.machine_id
                    FROM $table_pamlek a
                    LEFT JOIN $table_kry b ON a.no_pokok=b.$field_kry
                    WHERE 
                            (a.tapping_type!='0' OR (a.tapping_type='0' AND a.machine_id in ($machine)))
                            AND (CASE WHEN a.tapping_type!='0' then a.in_out='0' ELSE a.in_out='1' END)";
                    
                    /*"SELECT a.no_pokok, b.nama, b.nama_jabatan, b.personnel_number, b.nama_unit, a.id, 
                        (CASE WHEN a.tapping_type!='0' THEN a.tapping_time 
                            ELSE (SELECT CONCAT(m.dws_in_tanggal,' ',m.dws_in) as get_dws_in 
                                FROM $table_cico m WHERE a.no_pokok=m.np_karyawan AND DATE_FORMAT(a.tapping_time,'%Y-%m-%d')=m.dws_in_tanggal) 
                            END
                        ) as date_from,
                        (case when a.tapping_type='0' THEN a.tapping_time ELSE 
                            (SELECT (CASE WHEN aa.in_out='1' AND aa.no_pokok=a.no_pokok AND aa.tapping_type=a.tapping_type AND DATE_FORMAT(aa.tapping_time,'%Y-%m-%d')=DATE_FORMAT(a.tapping_time,'%Y-%m-%d') AND aa.tapping_time > a.tapping_time THEN aa.tapping_time ELSE 
                                (SELECT (CASE WHEN bb.dws_out_tanggal IS NOT NULL AND bb.dws_out IS NOT NULL THEN CONCAT(bb.dws_out_tanggal,' ',bb.dws_out) ELSE NULL END) as get_dws_out 
                                FROM $table_cico bb WHERE bb.np_karyawan=a.no_pokok AND bb.dws_in_tanggal=DATE_FORMAT(a.tapping_time,'%Y-%m-%d'))
                            END ) 
                            FROM $table_pamlek aa WHERE aa.no_pokok=a.no_pokok AND DATE_FORMAT(aa.tapping_time,'%Y-%m-%d')=DATE_FORMAT(a.tapping_time,'%Y-%m-%d') AND aa.tapping_type=a.tapping_type AND aa.tapping_time > a.tapping_time ORDER BY aa.tapping_time ASC LIMIT 1)
                         END) as date_to,
                        a.tapping_time, 
                        a.in_out, a.tapping_type, a.machine_id
                    FROM $table_pamlek a
                    LEFT JOIN 
                        (SELECT x.no_pokok, x.nama, x.nama_jabatan, x.personnel_number, x.nama_unit FROM mst_karyawan x) b
                        ON a.no_pokok=b.no_pokok
                    WHERE 
                        (a.tapping_type!='0' OR (a.tapping_type='0' AND a.machine_id in ($machine)))
                        and CASE WHEN a.tapping_type!='0' then a.in_out='0' ELSE a.in_out='1' END";*/
                $query_string_last = " ORDER BY a.no_pokok, a.tapping_time";
                
                $query_fix .= $query_string_first;
                $query_fix .= " AND (DATE_FORMAT(a.tapping_time,'%Y-%m-%d') BETWEEN '$start_date' and '$end_date')";
                if(@$np){
                    $query_fix .= " AND a.no_pokok='$np'";
                }
                $query_fix .= " GROUP BY a.id";
                $query_fix .= $query_string_last;
                
                $get = $this->db->query($query_fix);
                //echo json_encode($get->result()); exit();
                //$array_insert = array();
                foreach($get->result() as $row){
                    $insert_start_date = null;
                    $insert_start_time = null;
                    $insert_end_date = null;
                    $insert_end_time = null;
                    $insert_start_machine = null;
                    $insert_end_machine = null;
                    
                    //if(validateDateTime($row->date_from)==true){
                    if($row->date_from!=NULL){
                        $insert_start_date = explode(' ', $row->date_from)[0];
                        $insert_start_time = explode(' ', $row->date_from)[1];
                        $insert_start_machine = explode(' ', $row->date_from)[2];
                    } 
                    
                    //if(validateDateTime($row->date_to)==true){
                    if($row->date_to!=NULL){
                        $insert_end_date = explode(' ', $row->date_to)[0];
                        $insert_end_time = explode(' ', $row->date_to)[1];
                        $insert_end_machine = explode(' ', $row->date_to)[2];
                    } 
                        
                    try {
                        $this->db->query("CREATE TABLE IF NOT EXISTS ess_perizinan_$bulan_tahun_start LIKE ess_perizinan");
                        if($row->personnel_number!=NULL && trim($row->personnel_number)!=''){
                            if($row->tapping_type=='0'){
                                $tahun_bulan = str_replace('-','_',substr($row->tapping_time,0,7));
                                if(check_table_exist("ess_cico_$tahun_bulan")=='ada') {
									
									//22 01 2021, 7648 - Tri Wibowo, Gilir 3 izin nya lintas hari
									$day =  explode(' ',$row->tapping_time)[0];
									$min_one_day = date('Y-m-d', strtotime('-1 days', strtotime($day))); 								
									$where_dws_in = "(`dws_tanggal`='$day' OR `dws_tanggal`='$min_one_day')";
									
                                    $cek_row = $this->db->select("(CASE WHEN dws_in_tanggal_fix IS NOT NULL THEN dws_in_tanggal_fix ELSE dws_in_tanggal END) as tanggal_dws_in, (CASE WHEN dws_in_fix IS NOT NULL THEN dws_in_fix ELSE dws_in END) as time_dws_in, (CASE WHEN dws_out_tanggal_fix IS NOT NULL THEN dws_out_tanggal_fix ELSE dws_out_tanggal END) as tanggal_dws_out, (CASE WHEN dws_out_fix IS NOT NULL THEN dws_out_fix ELSE dws_out END) as time_dws_out")->where(['np_karyawan'=>$row->no_pokok])->where($where_dws_in)->get("ess_cico_$tahun_bulan");
									/*
                                    $cek_row = $this->db->select("(CASE WHEN dws_in_tanggal_fix IS NOT NULL THEN dws_in_tanggal_fix ELSE dws_in_tanggal END) as tanggal_dws_in, (CASE WHEN dws_in_fix IS NOT NULL THEN dws_in_fix ELSE dws_in END) as time_dws_in, (CASE WHEN dws_out_tanggal_fix IS NOT NULL THEN dws_out_tanggal_fix ELSE dws_out_tanggal END) as tanggal_dws_out, (CASE WHEN dws_out_fix IS NOT NULL THEN dws_out_fix ELSE dws_out END) as time_dws_out")->where(['np_karyawan'=>$row->no_pokok, 'dws_tanggal'=>explode(' ',$row->tapping_time)[0]])->get("ess_cico_$tahun_bulan");
                                    */
									
									//22 01 2021, 7648 - Tri Wibowo, Gilir 3 izin nya lintas hari
									foreach ($cek_row->result_array() as $data) 
									{								 
										$get_cico_dws->tanggal_dws_in	= $data['tanggal_dws_in'];
										$get_cico_dws->time_dws_in 	= $data['time_dws_in'];
										$get_cico_dws->tanggal_dws_out	= $data['tanggal_dws_out'];
										$get_cico_dws->time_dws_out	= $data['time_dws_out'];
										 								                                       
                                        if((date('Y-m-d H:i', strtotime($row->tapping_time)) > date('Y-m-d H:i', strtotime($get_cico_dws->tanggal_dws_in.' '.$get_cico_dws->time_dws_in))) && (date('Y-m-d H:i', strtotime($row->tapping_time)) < date('Y-m-d H:i', strtotime($get_cico_dws->tanggal_dws_out.' '.$get_cico_dws->time_dws_out)))){
                                            $array_insert = [
                                                'np_karyawan'=>$row->no_pokok,
                                                'nama'=>$row->nama,
                                                'personel_number'=>$row->personnel_number,
                                                'nama_jabatan'=>$row->nama_jabatan,
                                                'kode_unit'=>$row->kode_unit,
                                                'nama_unit'=>$row->nama_unit,
                                                'info_type'=>convert_pamlek_to_erp($row->tapping_type)['info_type'],
                                                'absence_type'=>convert_pamlek_to_erp($row->tapping_type)['absence_type'],
                                                'kode_pamlek'=>$row->tapping_type,
                                                'start_date'=>$insert_start_date,
                                                'end_date'=>$insert_end_date,
                                                'start_time'=>$insert_start_time,
                                                'end_time'=>$insert_end_time,
                                                'machine_id_start'=>$insert_start_machine,
                                                'machine_id_end'=>$insert_end_machine,
                                                'is_machine'=>'1'
                                            ];

                                            $this->M_pamlek_to_ess_perizinan->check_id_then_insert_data($array_insert, "ess_perizinan_$bulan_tahun_start");
                                            //$this->M_pamlek_to_ess_perizinan->update_cico($array_insert);
                                        } else {
                                            $job['table'] = 'ess_perizinan_'.$bulan_tahun_start;
                                            $job['np'] = $row->no_pokok;
                                            $job['personnel_number'] = $row->personnel_number;
                                            $job['start_date'] = $insert_start_date.' '.$insert_start_time;
                                            $job['end_date'] = $insert_end_date.' '.$insert_end_time;
                                            $job['machine_id_start'] = $insert_start_machine;
                                            $job['machine_id_end'] = $insert_end_machine;
                                            $job['message'] = 'Cek Tapping Time';

                                            $this->db->set($job)->insert('ess_log_job');
                                        }
									}

								
									//22 01 2021, 7648 - Tri Wibowo, Gilir 3 izin nya lintas hari	[MATIIN SCRIPT LAMA]	
									/*
                                    if($cek_row->num_rows()>0){
                                        $get_cico_dws = $cek_row->row();
                                        //old: if(($row->tapping_time > date('Y-m-d H:i:s', strtotime($get_cico_dws->tanggal_dws_in.' '.$get_cico_dws->time_dws_in))) && ($row->tapping_time < date('Y-m-d H:i:s', strtotime($get_cico_dws->tanggal_dws_out.' '.$get_cico_dws->time_dws_out)))){
                                        if((date('Y-m-d H:i', strtotime($row->tapping_time)) > date('Y-m-d H:i', strtotime($get_cico_dws->tanggal_dws_in.' '.$get_cico_dws->time_dws_in))) && (date('Y-m-d H:i', strtotime($row->tapping_time)) < date('Y-m-d H:i', strtotime($get_cico_dws->tanggal_dws_out.' '.$get_cico_dws->time_dws_out)))){
                                            $array_insert = [
                                                'np_karyawan'=>$row->no_pokok,
                                                'nama'=>$row->nama,
                                                'personel_number'=>$row->personnel_number,
                                                'nama_jabatan'=>$row->nama_jabatan,
                                                'kode_unit'=>$row->kode_unit,
                                                'nama_unit'=>$row->nama_unit,
                                                'info_type'=>convert_pamlek_to_erp($row->tapping_type)['info_type'],
                                                'absence_type'=>convert_pamlek_to_erp($row->tapping_type)['absence_type'],
                                                'kode_pamlek'=>$row->tapping_type,
                                                'start_date'=>$insert_start_date,
                                                'end_date'=>$insert_end_date,
                                                'start_time'=>$insert_start_time,
                                                'end_time'=>$insert_end_time,
                                                'machine_id_start'=>$insert_start_machine,
                                                'machine_id_end'=>$insert_end_machine,
                                                'is_machine'=>'1'
                                            ];

                                            $this->M_pamlek_to_ess_perizinan->check_id_then_insert_data($array_insert, "ess_perizinan_$bulan_tahun_start");
                                            //$this->M_pamlek_to_ess_perizinan->update_cico($array_insert);
                                        } else {
                                            $job['table'] = 'ess_perizinan_'.$bulan_tahun_start;
                                            $job['np'] = $row->no_pokok;
                                            $job['personnel_number'] = $row->personnel_number;
                                            $job['start_date'] = $insert_start_date.' '.$insert_start_time;
                                            $job['end_date'] = $insert_end_date.' '.$insert_end_time;
                                            $job['machine_id_start'] = $insert_start_machine;
                                            $job['machine_id_end'] = $insert_end_machine;
                                            $job['message'] = 'Cek Tapping Time';

                                            $this->db->set($job)->insert('ess_log_job');
                                        }
                                    } else {
                                        $job['table'] = 'ess_perizinan_'.$bulan_tahun_start;
                                        $job['np'] = $row->no_pokok;
                                        $job['personnel_number'] = $row->personnel_number;
                                        $job['start_date'] = $insert_start_date.' '.$insert_start_time;
                                        $job['end_date'] = $insert_end_date.' '.$insert_end_time;
                                        $job['machine_id_start'] = $insert_start_machine;
                                        $job['machine_id_end'] = $insert_end_machine;
                                        $job['message'] = "Row in ess_cico_$tahun_bulan Not Found";

                                        $this->db->set($job)->insert('ess_log_job');
                                    }
									END OF MATIIN */
									
                                } else {
                                    $job['table'] = 'ess_perizinan_'.$bulan_tahun_start;
                                    $job['np'] = $row->no_pokok;
                                    $job['personnel_number'] = $row->personnel_number;
                                    $job['start_date'] = $insert_start_date.' '.$insert_start_time;
                                    $job['end_date'] = $insert_end_date.' '.$insert_end_time;
                                    $job['machine_id_start'] = $insert_start_machine;
                                    $job['machine_id_end'] = $insert_end_machine;
                                    $job['message'] = 'Table Not Found';

                                    $this->db->set($job)->insert('ess_log_job');
                                }
                            } else{
                                $array_insert = [
                                    'np_karyawan'=>$row->no_pokok,
                                    'nama'=>$row->nama,
                                    'personel_number'=>$row->personnel_number,
                                    'nama_jabatan'=>$row->nama_jabatan,
                                    'kode_unit'=>$row->kode_unit,
                                    'nama_unit'=>$row->nama_unit,
                                    'info_type'=>convert_pamlek_to_erp($row->tapping_type)['info_type'],
                                    'absence_type'=>convert_pamlek_to_erp($row->tapping_type)['absence_type'],
                                    'kode_pamlek'=>$row->tapping_type,
                                    'start_date'=>$insert_start_date,
                                    'end_date'=>$insert_end_date,
                                    'start_time'=>$insert_start_time,
                                    'end_time'=>$insert_end_time,
                                    'machine_id_start'=>$insert_start_machine,
                                    'machine_id_end'=>$insert_end_machine,
                                    'is_machine'=>'1'
                                ];

                                $this->M_pamlek_to_ess_perizinan->check_id_then_insert_data($array_insert, "ess_perizinan_$bulan_tahun_start");
                                //$this->M_pamlek_to_ess_perizinan->update_cico($array_insert);
                            }
                        } else {
                            $job['table'] = 'ess_perizinan_'.$bulan_tahun_start;
                            $job['np'] = $row->no_pokok;
                            $job['personnel_number'] = $row->personnel_number;
                            $job['start_date'] = $insert_start_date.' '.$insert_start_time;
                            $job['end_date'] = $insert_end_date.' '.$insert_end_time;
                            $job['machine_id_start'] = $insert_start_machine;
                            $job['machine_id_end'] = $insert_end_machine;
                            $job['message'] = 'Personnel Number NULL';

                            $this->db->set()->insert('ess_log_job');
                        }
                    } catch(Exception $e) {
                        $job['table'] = 'ess_perizinan_'.$bulan_tahun_start;
                        $job['np'] = $row->no_pokok;
                        $job['personnel_number'] = $row->personnel_number;
                        $job['start_date'] = $insert_start_date.' '.$insert_start_time;
                        $job['end_date'] = $insert_end_date.' '.$insert_end_time;
                        $job['machine_id_start'] = $insert_start_machine;
                        $job['machine_id_end'] = $insert_end_machine;
                        $job['message'] = json_encode($e->getMessage());

                        $this->db->set()->insert('ess_log_job');
                    }
                }
                
                //echo json_encode($array_insert); exit();
                
                $msc = microtime(true)-$msc;
                echo "Done. Execution time: $msc seconds.<br>";
                echo "Inserted to database.<br>";
                echo "Table name: <b>ess_perizinan_$bulan_tahun_start</b><br>";
                echo "Total rows: <b>".$get->num_rows()."</b>";
                
                //insert ke tabel 'ess_status_proses_input', id proses = 8
                $this->db->insert('ess_status_proses_input', ['id_proses'=>8, 'waktu'=>date('Y-m-d H:i:s')]);
            }
            
            //update field 'id_perizinan' di tabel ess_cico_$tahun_bulan diambil dari 'id' di tabel ess_perizinan_$tahun_bulan
            /*$this->db->query("UPDATE $table_cico x
                            INNER JOIN 
                                (SELECT GROUP_CONCAT(a.id SEPARATOR ', ') as id_izin, b.id
                                FROM ess_perizinan_$bulan_tahun_start a
                                INNER JOIN 
                                    (SELECT c.id, c.np_karyawan,
                                        (CASE WHEN c.dws_in_tanggal_fix IS NOT NULL THEN c.dws_in_tanggal_fix ELSE c.dws_in_tanggal END) as dws_in_date,
                                        (CASE WHEN c.dws_in_fix IS NOT NULL THEN c.dws_in_fix ELSE c.dws_in END) as dws_in_time,
                                        (CASE WHEN c.dws_out_tanggal_fix IS NOT NULL THEN c.dws_out_tanggal_fix ELSE c.dws_out_tanggal END) as dws_out_date,
                                        (CASE WHEN c.dws_out_fix IS NOT NULL THEN c.dws_out_fix ELSE c.dws_out END) as dws_out_time
                                    FROM $table_cico c
                                ) b ON a.np_karyawan=b.np_karyawan AND
                                    (CASE WHEN a.start_date IS NOT NULL THEN (CONCAT(a.start_date,' ',a.start_time) BETWEEN CONCAT(b.dws_in_date,' ',b.dws_in_time) AND CONCAT(b.dws_out_date,' ',b.dws_out_time)) 
                                    ELSE (CONCAT(a.end_date,' ',a.end_time) BETWEEN CONCAT(b.dws_in_date,' ',b.dws_in_time) AND CONCAT(b.dws_out_date,' ',b.dws_out_time)) END)
                            GROUP BY b.id) y ON x.id = y.id
                            SET x.id_perizinan = (CASE WHEN x.id_perizinan IS NULL THEN y.id_izin ELSE CONCAT(x.id_perizinan,', ',y.id_izin) END)");*/
        }
        
    }
	
	public function index()
	{
		redirect(base_url('dashboard'));
	}
}
