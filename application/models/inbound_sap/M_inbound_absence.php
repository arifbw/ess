<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_inbound_absence extends CI_Model {

	function check_table_exist($name)
	{
		$name=str_replace("-","_",$name);
		$query = $this->db->query("show tables like '$name'")->row_array();
		
		return $query;
	}
	
	public function select_cico_by_np_dws($np=null, $dws_tanggal=null)
	{
		$tahun_bulan	= substr($dws_tanggal,0,7);
		$tahun_bulan	=str_replace("-","_",$tahun_bulan);
		
		$tabel_cico		= "ess_cico_".$tahun_bulan;
		
		if(!$this->check_table_exist($tabel_cico))
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
		$this->db->select('id');
		$this->db->select('np_karyawan');
		$this->db->select('personel_number');
		$this->db->select('tidak_hadir_tanggal_awal');
		$this->db->select("tidak_hadir_tanggal_awal + INTERVAL '1' MONTH - INTERVAL 1 DAY AS batas");
		$this->db->select('max(dws_tanggal) AS dws_tanggal');
		$this->db->select('max(tidak_hadir_ke) AS tidak_hadir_ke');
		$this->db->from($tabel);		
		//generate semua
		$where = "tidak_hadir_tanggal_awal IS NOT NULL";	
		$this->db->where($where);
			
        if(@$np){
            $this->db->where('np_karyawan', $np);
        }
		$this->db->order_by("np_karyawan", "ASC"); 	
		
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
		$this->db->select('*');
		$this->db->from($tabel);		
		//generate semua
		//$this->db->where('proses','0');
        if(@$np){
            $this->db->where('np_karyawan', $np);
        }
		$this->db->order_by("np_karyawan", "ASC"); 	
		// $this->db->limit(5,5); 	
		
		$query = $this->db->get();
		return $query;
	}

	public function check_date_absence($date, $np)
	{
        $tahun_bulan = str_replace('-','_',substr($date, 0, 7));
		$query = $this->db->query("
			SELECT * FROM 
				(
					SELECT np_karyawan, 
					CONCAT(dws_in_tanggal,' ',dws_in) as start_date,
					CONCAT(dws_in_tanggal_fix,' ',dws_in_fix) as start_date_fix,
					CONCAT(dws_out_tanggal,' ',dws_out) as end_date,
					CONCAT(dws_out_tanggal_fix,' ',dws_out_fix) as end_date_fix
					FROM `ess_cico_$tahun_bulan`
				) isi
			WHERE np_karyawan='$np' AND 
			(
				(
					Convert('$date', datetime) >= start_date AND Convert('$date', datetime) <= end_date
				)
				OR
				(	
					Convert('$date', datetime) >= start_date_fix AND Convert('$date', datetime) <= end_date_fix
				)
			) LIMIT 1
		")->row();
		return $query;
	}
    
    function get_data_cico($tabel, $np=null){
        $string = "SELECT np_karyawan, personel_number
                                , (CASE WHEN dws_in_tanggal_fix IS NOT NULL THEN dws_in_tanggal_fix ELSE dws_in_tanggal END) AS tanggal_in_dws
                                , (CASE WHEN tapping_fix_1 IS NOT NULL THEN tapping_fix_1 ELSE tapping_time_1 END) AS tapping_masuk
                                , (CASE WHEN tapping_fix_2 IS NOT NULL THEN tapping_fix_2 ELSE tapping_time_2 END) AS tapping_keluar
                                FROM $tabel
                                WHERE (id_cuti IS NULL OR TRIM(id_cuti)='')
                                AND (id_sppd IS NULL OR TRIM(id_sppd)='')
                                AND (CASE WHEN dws_name_fix IS NOT NULL THEN dws_name_fix!='OFF' ELSE dws_name!='OFF' END)
                                AND dws_tanggal NOT IN (SELECT tanggal FROM mst_cuti_bersama)
                                AND dws_tanggal NOT IN (SELECT tanggal FROM mst_hari_libur)
                                AND (
                                    (CASE WHEN tapping_fix_1 IS NOT NULL THEN tapping_fix_1 ELSE tapping_time_1 END) IS NULL 
                                        OR (CASE WHEN tapping_fix_2 IS NOT NULL THEN tapping_fix_2 ELSE tapping_time_2 END) IS NULL
                                )";
        if(@$np){
            $string .= " AND np_karyawan='$np'";
        }
        $query = $this->db->query($string);
        return $query;
    }
    
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
}