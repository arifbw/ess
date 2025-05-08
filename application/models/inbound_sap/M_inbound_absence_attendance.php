<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class m_inbound_absence_attendance extends CI_Model {

	function check_table_exist($name)
	{
		$name=str_replace("-","_",$name);
		
		//$query = $this->db->query("show tables like '$name'")->row_array();
		
		$nama_db = $this->db->database;
		$query = $this->db->query("SELECT table_name FROM information_schema.tables WHERE table_schema = '$nama_db' AND table_name like '$name%' GROUP BY table_name ORDER BY table_name DESC;")->row_array();
		
		
		return $query;
	}
	
	public function cek_libur($dws_tanggal)
	{
		$cek = $this->db->where('tanggal', $dws_tanggal)->get('mst_hari_libur')->num_rows();
		if ($cek > 0) {
			return false;
		}else{
			return true;			
		}
	}
	
	public function is_cuti_bersama($tanggal)
	{
		$this->db->select('tanggal');
		$this->db->from('mst_cuti_bersama');
		$this->db->where('tanggal', $tanggal);
		
		$query 	= $this->db->get();		
		$data	= $query->row_array();
		
		if($data['tanggal'])
		{
			return true;
		}else
		{
			return false;
		}
		
	}
	
	public function select_cico_by_np_dws($np=null, $dws_tanggal=null)
	{
		//$tahun_bulan	= substr($dws_tanggal,0,7);
		$tahun_bulan	=str_replace("-","_",substr($dws_tanggal,0,7));
		
		$tabel_cico		= "ess_cico_".$tahun_bulan;
		
		if(check_table_exist($tabel_cico)=='belum ada')
		{
			$tabel_cico		= "ess_cico";
		}
				
		$this->db->select('*');
		$this->db->from($tabel_cico);	
		$this->db->where('np_karyawan', $np);
		$this->db->where('dws_tanggal', $dws_tanggal);
		$query = $this->db->get();
		return $query->row_array();
	}
	
	public function get_data_max_dws_tanggal_awal($tabel, $np=null)
	{
		if(!$this->check_table_exist($tabel))
		{
			$tabel		= "ess_cico";
		}
		
		$this->db->select('id');
		$this->db->select('np_karyawan');
		$this->db->select('personel_number');
		$this->db->select('tidak_hadir_tanggal_awal');
		$this->db->select("tidak_hadir_tanggal_awal + INTERVAL '1' MONTH - INTERVAL 1 DAY AS batas");
		$this->db->select('max(dws_tanggal) AS dws_tanggal');
		$this->db->select('max(tidak_hadir_ke) AS tidak_hadir_ke');
		$this->db->select('min(tidak_hadir_ke) AS tidak_hadir_ke_min');
		$this->db->from($tabel);		
		//generate semua
		$where = "tidak_hadir_tanggal_awal IS NOT NULL 
					AND tm_status!='9'
					AND action != 'ZI'
					AND action != 'ZL'
					AND (
						action != 'ZN'
						OR (
							action = 'ZN'
							AND tm_status = '1'
						)
					)"; /*MPP yang tidak dihibahkan*/
		/*
		$where = "tidak_hadir_tanggal_awal IS NOT NULL 
					AND tm_status!='9'
					AND action NOT IN ('ZI','ZL','ZN')";
		*/
		
		
		
		$this->db->where($where);
			
			
			
			
        if(@$np){
            $this->db->where('np_karyawan', $np);
        }
		$this->db->order_by("np_karyawan", "ASC"); 	
		$this->db->order_by("tidak_hadir_tanggal_awal", "ASC"); 	
		
		$this->db->group_by('np_karyawan');
		$this->db->group_by('tidak_hadir_tanggal_awal');
	
		$query = $this->db->get();
		return $query;
	}
	
	
	
	
	public function get_data_cuti($tabel, $date, $np=null)
	{
		$this->db->select('*');
		$this->db->from($tabel);		
		//generate semua
		$this->db->where('SUBSTR(start_date,1,7)', $date);
        if(@$np){
            $this->db->where('np_karyawan', $np);
        }
		$this->db->order_by("np_karyawan", "ASC"); 	
		// $this->db->limit(5,5); 	
		
		$query = $this->db->get();
		return $query;
	}

	public function get_data_perizinan($tabel, $np=null)
	{
		
		if(@$np){
          $query = $this->db->query("SELECT 
											* 
										FROM 
											$tabel 
										WHERE
											np_karyawan = '$np'
										ORDER BY 
											np_karyawan, 
											(
												CASE
												WHEN start_date IS NOT NULL
												AND end_date IS NOT NULL THEN
													CONCAT(start_date,' ', start_time)
												WHEN start_date IS NULL
												AND end_date IS NOT NULL THEN
													CONCAT(end_date,' ', end_time)
												WHEN start_date IS NOT NULL
												AND end_date IS NULL THEN
													CONCAT(start_date,' ', start_time)
												ELSE
													CONCAT(start_date,' ', start_time)
												END
											) ASC");
        }else
		{
			$query = $this->db->query("SELECT 
											* 
										FROM 
											$tabel 
										ORDER BY 
											np_karyawan, 
											(
												CASE
												WHEN start_date IS NOT NULL
												AND end_date IS NOT NULL THEN
													CONCAT(start_date,' ', start_time)
												WHEN start_date IS NULL
												AND end_date IS NOT NULL THEN
													CONCAT(end_date,' ', end_time)
												WHEN start_date IS NOT NULL
												AND end_date IS NULL THEN
													CONCAT(start_date,' ', start_time)
												ELSE
													CONCAT(start_date,' ', start_time)
												END
											) ASC");
		}

		
		return $query;
	}

	public function check_date_absence($date, $np)
	{
		$tahun_bulan = substr($date,0,7);
		
		$tahun_bulan=str_replace("-","_",$tahun_bulan);
		$tabel = 'ess_cico_'.$tahun_bulan;
		
		if(!$this->check_table_exist($tabel))
		{
			$tabel		= "ess_cico";
		}
		
		$query = $this->db->query("
			/* Menutup start_date end_date kosong $np $date*/
			SELECT * FROM 
				(
					SELECT np_karyawan, 
					CONCAT(dws_in_tanggal,' ',dws_in) as start_date,
					CONCAT(dws_in_tanggal_fix,' ',dws_in_fix) as start_date_fix,
					CONCAT(dws_out_tanggal,' ',dws_out) as end_date,
					CONCAT(dws_out_tanggal_fix,' ',dws_out_fix) as end_date_fix
					FROM `$tabel`
				) isi
			WHERE np_karyawan='$np' AND 
			(
				(
					Convert('$date', datetime) >= (CASE WHEN start_date_fix IS NOT NULL THEN DATE_SUB(start_date_fix, INTERVAL 3 HOUR) ELSE DATE_SUB(start_date, INTERVAL 3 HOUR) END) AND Convert('$date', datetime) <= (CASE WHEN end_date_fix IS NOT NULL THEN /*tidak usah diambah 3 kalo end date nya DATE_ADD(end_date_fix, INTERVAL 3 HOUR)*/ end_date_fix ELSE /*tidak usah diambah 3 kalo end date nya DATE_ADD(end_date, INTERVAL 3 HOUR)*/end_date END)
				)
			) LIMIT 1
		")->row();
		return $query;
	}
    
    function get_data_cico($tabel, $np=null){
		
		if(!$this->check_table_exist($tabel))
		{
			$tabel		= "ess_cico";
		}
		
        $string = "SELECT np_karyawan, personel_number
                                /* heru
								, (CASE WHEN dws_in_tanggal_fix IS NOT NULL THEN dws_in_tanggal_fix ELSE dws_in_tanggal END) AS tanggal_in_dws
                                */
								/* bowo pakai dws tanggal */
								, dws_tanggal AS tanggal_in_dws
								, (CASE WHEN tapping_fix_1 IS NOT NULL THEN tapping_fix_1 ELSE tapping_time_1 END) AS tapping_masuk
                                , (CASE WHEN tapping_fix_2 IS NOT NULL THEN tapping_fix_2 ELSE tapping_time_2 END) AS tapping_keluar
								, CONCAT(IFNULL(dws_in_tanggal_fix,dws_in_tanggal),' ',IFNULL(dws_in_fix,dws_in)) AS jadwal_masuk
								, CONCAT(IFNULL(dws_out_tanggal_fix,dws_out_tanggal),' ',IFNULL(dws_out_fix,dws_out)) AS jadwal_keluar
                                FROM $tabel
                                WHERE (id_perizinan IS NULL OR TRIM(id_perizinan)='')
                                AND (id_cuti IS NULL OR TRIM(id_cuti)='')
                                AND (id_sppd IS NULL OR TRIM(id_sppd)='')
                                AND (CASE WHEN dws_name_fix IS NOT NULL THEN dws_name_fix!='OFF' ELSE dws_name!='OFF' END)
                                -- AND dws_tanggal NOT IN (SELECT tanggal FROM mst_cuti_bersama)
                                -- AND dws_tanggal NOT IN (SELECT tanggal FROM mst_hari_libur)
                                AND (
                                    (CASE WHEN tapping_fix_1 IS NOT NULL THEN tapping_fix_1 ELSE tapping_time_1 END) IS NULL 
                                        OR (CASE WHEN tapping_fix_2 IS NOT NULL THEN tapping_fix_2 ELSE tapping_time_2 END) IS NULL
										OR IFNULL(tapping_fix_1,tapping_time_1) > CONCAT(IFNULL(dws_in_tanggal_fix,dws_in_tanggal),' ',IFNULL(dws_in_fix,dws_in))
										OR IFNULL(tapping_fix_2,tapping_time_2) < CONCAT(IFNULL(dws_out_tanggal_fix,dws_out_tanggal),' ',IFNULL(dws_out_fix,dws_out))
                                )
								AND tm_status!='9'
								AND action != 'ZI'
								AND action != 'ZL'
								AND (
									action != 'ZN'
									OR (
										action = 'ZN'
										AND tm_status = '1'
									)
								) /*MPP yang tidak dihibahkan*/								
								/*
								AND action NOT IN ('ZI','ZL','ZN')
								*/";
        if(@$np){
            $string .= " AND np_karyawan='$np'";
        }
        $query = $this->db->query($string);
        return $query;
    }
	
	/*7648 Tri Wibowo, 01 04 2020 - Ambil Data WFH*/
	/*7648 Tri Wibowo, 09 03 2021 - Tambah Select is_dinas_luar*/
	function get_data_cico_wfh($tabel, $np=null){
		
		if(!$this->check_table_exist($tabel))
		{
			$tabel		= "ess_cico";
		}
		
        $string = "SELECT np_karyawan, personel_number,is_dinas_luar
                                /* heru
								, (CASE WHEN dws_in_tanggal_fix IS NOT NULL THEN dws_in_tanggal_fix ELSE dws_in_tanggal END) AS tanggal_in_dws
                                */
								/* bowo pakai dws tanggal */
								, dws_tanggal AS tanggal_in_dws
								, (CASE WHEN tapping_fix_1 IS NOT NULL THEN tapping_fix_1 ELSE tapping_time_1 END) AS tapping_masuk
                                , (CASE WHEN tapping_fix_2 IS NOT NULL THEN tapping_fix_2 ELSE tapping_time_2 END) AS tapping_keluar
								, CONCAT(IFNULL(dws_in_tanggal_fix,dws_in_tanggal),' ',IFNULL(dws_in_fix,dws_in)) AS jadwal_masuk
								, CONCAT(IFNULL(dws_out_tanggal_fix,dws_out_tanggal),' ',IFNULL(dws_out_fix,dws_out)) AS jadwal_keluar
                                FROM $tabel
                                WHERE (wfh='1')
                                AND (id_cuti IS NULL OR TRIM(id_cuti)='')
                                AND (id_sppd IS NULL OR TRIM(id_sppd)='')
                                AND (CASE WHEN dws_name_fix IS NOT NULL THEN dws_name_fix!='OFF' ELSE dws_name!='OFF' END)
                                -- AND dws_tanggal NOT IN (SELECT tanggal FROM mst_cuti_bersama)
                                -- AND dws_tanggal NOT IN (SELECT tanggal FROM mst_hari_libur)
                             	/*
								AND tm_status!='9'
								AND action != 'ZI'
								AND action != 'ZL'
								AND (
									action != 'ZN'
									OR (
										action = 'ZN'
										AND tm_status = '1'
									)
								)
								*/
								/*MPP yang tidak dihibahkan*/								
								/*
								AND action NOT IN ('ZI','ZL','ZN')
								*/";
        if(@$np){
            $string .= " AND np_karyawan='$np'";
        }
        $query = $this->db->query($string);
        return $query;
    }

	// telat tanpa izin
	function get_data_cico_tanpa_izin($cico, $izin, $np=null){
		if(!$this->check_table_exist($cico)) $cico = "ess_cico";
		if(!$this->check_table_exist($izin)) $izin = "ess_perizinan";
		
        $string = "SELECT cico.np_karyawan, cico.personel_number
			, cico.dws_tanggal AS tanggal_in_dws
			, IFNULL(cico.tapping_fix_1, cico.tapping_time_1) AS tapping_masuk
			, IFNULL(cico.tapping_fix_2, cico.tapping_time_2) AS tapping_keluar
			, CONCAT(IFNULL(cico.dws_in_tanggal_fix, cico.dws_in_tanggal),' ',IFNULL(cico.dws_in_fix, cico.dws_in)) AS jadwal_masuk
			, CONCAT(IFNULL(cico.dws_out_tanggal_fix, cico.dws_out_tanggal),' ',IFNULL(cico.dws_out_fix, cico.dws_out)) AS jadwal_keluar
			, izin.id
			, izin.info_type, izin.absence_type, izin.kode_pamlek
			FROM {$cico} cico
			LEFT JOIN {$izin} izin ON izin.np_karyawan = cico.np_karyawan 
				AND (izin.start_date = IFNULL(cico.dws_in_tanggal_fix, cico.dws_in_tanggal))
				AND (izin.end_date = IFNULL(cico.dws_out_tanggal_fix, cico.dws_out_tanggal))
				AND CONCAT(izin.kode_pamlek,'|',izin.info_type,'|',izin.absence_type) IN ('0|2001|5000','H|2001|5030','F|2001|5020','E|2001|5010')
			WHERE (cico.id_perizinan IS NULL OR TRIM(cico.id_perizinan)='')
			AND (cico.id_cuti IS NULL OR TRIM(cico.id_cuti)='')
			AND (cico.id_sppd IS NULL OR TRIM(cico.id_sppd)='')
			AND (CASE WHEN cico.dws_name_fix IS NOT NULL THEN cico.dws_name_fix!='OFF' ELSE cico.dws_name!='OFF' END)
			AND (
				IFNULL(cico.tapping_fix_1, cico.tapping_time_1) > CONCAT(IFNULL(cico.dws_in_tanggal_fix, cico.dws_in_tanggal),' ',IFNULL(cico.dws_in_fix, cico.dws_in))
				OR 
				IFNULL(cico.tapping_fix_2, cico.tapping_time_2) < CONCAT(IFNULL(cico.dws_out_tanggal_fix, cico.dws_out_tanggal),' ',IFNULL(cico.dws_out_fix, cico.dws_out))
			)
			AND cico.tm_status!='9'
			AND cico.action != 'ZI'
			AND cico.action != 'ZL'
			AND (
				cico.action != 'ZN'
				OR (
					cico.action = 'ZN' AND cico.tm_status = '1'
				)
			)
			AND izin.id is null";
        if(@$np){
            $string .= " AND cico.np_karyawan='$np'";
        }
        $query = $this->db->query($string);
        return $query;
    }
	// END telat tanpa izin
    
    public function get_data_cuti_bersama($tabel, $np=null) {
		$this->db->select('np_karyawan, personel_number');
		$this->db->from($tabel);
        if(@$np){
            $this->db->where('np_karyawan', $np);
        }
		$this->db->group_by("np_karyawan");
		
		$query = $this->db->get();
		return $query;
	}
    
    public function get_data_tanggal_cuti_bersama() {
		$this->db->select('tanggal');		
		$query = $this->db->get('mst_cuti_bersama');
		return $query;
	}
	
	public function select_sppd($date, $np=null)
	{
			
		$hari_ini = $date."-01";
		
		$tahun_bulan = str_replace('-','_',$date);
		$tabel_cico = "ess_cico_".$tahun_bulan;
		
		if(!$this->check_table_exist($tabel_cico))
		{
			$tabel_cico		= "ess_cico";
		}

		// Tanggal pertama pada bulan ini
		$tgl_bulan_awal = date('Y-m-01', strtotime($hari_ini));

		// Tanggal terakhir pada bulan ini
		$tgl_bulan_akhir = date('Y-m-t', strtotime($hari_ini));
	
		$this->db->select("$tabel_cico.np_karyawan");
		$this->db->select("$tabel_cico.personel_number");
		$this->db->select("$tabel_cico.dws_tanggal");
		$this->db->select('ess_sppd.tipe_perjalanan');
		$this->db->from("$tabel_cico"); 	
		$this->db->join('ess_sppd', "ess_sppd.id = SUBSTRING_INDEX($tabel_cico.id_sppd, ',', 1)", 'left');
		//yang di bulan itu dan tidak cuti
		$where = "	(
						`$tabel_cico`.dws_tanggal >= '$tgl_bulan_awal' AND
						`$tabel_cico`.dws_tanggal <= '$tgl_bulan_akhir'
					) AND
					(
						`$tabel_cico`.id_cuti = '' OR
						`$tabel_cico`.id_cuti is null
					)
					";
		$this->db->where($where);
		$this->db->where("$tabel_cico.id_sppd!=", '');
		$this->db->where("$tabel_cico.id_sppd!=", null);
		
	/*
		$this->db->select('*');
		$this->db->from("ess_sppd"); 		
		$where = "	(
						(
						`tgl_berangkat` >= '$tgl_bulan_awal' AND
						`tgl_berangkat` <= '$tgl_bulan_akhir'
						) OR
						(
						`tgl_pulang` >= '$tgl_bulan_awal' AND
						`tgl_pulang` <= '$tgl_bulan_akhir'
						)
					) 
					";
		$this->db->where($where);
	*/	
	
        if(@$np){
            $this->db->where("$tabel_cico.np_karyawan", $np);
        }
					
		$this->db->order_by("$tabel_cico.np_karyawan", 'ASC');
		$this->db->order_by("$tabel_cico.dws_tanggal", 'ASC');			
		$query = $this->db->get();
		return $query;	
	}
	
	//7648 - Tri Wibowo - 24 02 2021 - Check Apakah bulan itu dia tidak berangkat full
	public function check_full_tidak_berangkat($np=null, $dws_tanggal=null)
	{
		//$tahun_bulan	= substr($dws_tanggal,0,7);
		$tahun_bulan	=str_replace("-","_",substr($dws_tanggal,0,7));
		
		$tabel_cico		= "ess_cico_".$tahun_bulan;
		
		if(check_table_exist($tabel_cico)=='belum ada')
		{
			$tabel_cico		= "ess_cico";
		}
				
		$query = $this->db->query("
											SELECT 
												* 
											FROM 
												$tabel_cico 
											WHERE 
												np_karyawan='$np' AND
												(
													(tidak_hadir_ke is null OR tidak_hadir_ke='') AND /*dia berangkat*/
													(	
														(dws_name!='OFF' AND (dws_name_fix is null OR dws_name_fix='')) OR /*tidak libur dan tidak ada pergantian dws*/
														dws_name_fix!='OFF' AND (dws_name_fix is not null OR dws_name_fix!='') /*atau ada pergantian dws tidak libur*/
													)														
												)
												
										")->row_array();
		$ada_data_masuk = $query['np_karyawan'];
		
		if(@$ada_data_masuk)
		{
			return 0; //terdapat data keberangkatan
		}else
		{
			return 1; //full tidak berangkat di bulan itu
		}
	}
	//end of 7648 - Tri Wibowo - 24 02 2021
}