<?php

	function nama_unit_by_kode_unit($kode_unit)
	{			
		$ci =& get_instance();
		// $ambil_data = $ci->db->query("SELECT nama_unit FROM mst_satuan_kerja WHERE kode_unit='$kode_unit'")->row_array();
		// $ambil = $ambil_data['nama_unit'];

		# 2021-05-25, heru pds ubah query, karna table mst_satuan_kerja tidak ada datanya
		$ambil_data = $ci->db->select('object_name_lengkap as nama_unit')->where('object_abbreviation',$kode_unit)->get('ess_sto');
		if($ambil_data->num_rows()>0)
			$ambil = $ambil_data->row_array()['nama_unit'];
		else
			$ambil = '';

		return $ambil ;
	}

	function nama_karyawan_by_np($np)
	{			
		if(!$np){
			return $ambil = '';
		}
		$ci =& get_instance();	
		$ambil_data = $ci->db->query("SELECT nama FROM mst_karyawan WHERE no_pokok='$np'")->row_array();
		$ambil = @$ambil_data['nama'] ?: '';

		return $ambil ;
	}
	
	//06 01 2021, 7648 - Tri Wibowo, Agar bisa backdate input cuti ketika NP nya berubah
	function nama_karyawan_by_np_bulan_sebelumnya($np)
	{			
		$ci =& get_instance();	
			
		$date_now			= date('Y-m-d');
		$date_kemarin 		= date('Y-m-d', strtotime($date_now . ' -1 months'));
				
		$pisah_date_kemarin	= explode('-',$date_kemarin);
		$tahun2				= $pisah_date_kemarin[0];
		$bulan2				= $pisah_date_kemarin[1];
		$tahun_bulan_kemarin= $tahun2."_".$bulan2;
		
		$tabel_master_kemarin = 'erp_master_data_'.$tahun_bulan_kemarin;
		
		$check_table_exist=str_replace("-","_",$tabel_master_kemarin);
		$query_check = $ci->db->query("show tables like '$check_table_exist'")->row_array();
		
		if(!$query_check)
		{
			$tabel_master_kemarin = 'erp_master_data';
		}
						
		if(!$np){
			return $ambil = '';
		}		
		$ci =& get_instance();	
		$ambil_data = $ci->db->query("SELECT nama FROM $tabel_master_kemarin WHERE np_karyawan='$np'")->row_array();
		$ambil = $ambil_data['nama'];

		return $ambil ;				
	}

	function raw_karyawan_by_np_bulan_sebelumnya($np)
	{			
		$ci =& get_instance();	
			
		$date_now			= date('Y-m-d');
		$date_kemarin 		= date('Y-m-d', strtotime($date_now . ' -1 months'));
				
		$pisah_date_kemarin	= explode('-',$date_kemarin);
		$tahun2				= $pisah_date_kemarin[0];
		$bulan2				= $pisah_date_kemarin[1];
		$tahun_bulan_kemarin= $tahun2."_".$bulan2;
		
		$tabel_master_kemarin = 'erp_master_data_'.$tahun_bulan_kemarin;
		
		$check_table_exist=str_replace("-","_",$tabel_master_kemarin);
		$query_check = $ci->db->query("show tables like '$check_table_exist'")->row_array();
		
		if(!$query_check)
		{
			$tabel_master_kemarin = 'erp_master_data';
		}				
				
		$ci =& get_instance();	
		$ambil_data = $ci->db->query("SELECT np_karyawan AS no_pokok, nama, tanggal_masuk, kontrak_kerja FROM $tabel_master_kemarin WHERE np_karyawan='$np'")->row();
		return $ambil_data;				
	}
	
	function nama_unit_by_np($np)
	{			
		$ci =& get_instance();	
		$ambil_data = $ci->db->query("SELECT nama_unit FROM mst_karyawan WHERE no_pokok='$np'")->row_array();
		$ambil = $ambil_data['nama_unit'];

		return $ambil ;
	}
	
	function nama_jabatan_by_np($np)
	{			
		$ci =& get_instance();	
		$ambil_data = $ci->db->query("SELECT nama_jabatan FROM mst_karyawan WHERE no_pokok='$np'")->row_array();
		$ambil = $ambil_data['nama_jabatan'];

		return $ambil ;
	}

	function nama_karyawan_by_id($id)
	{			
		$ci =& get_instance();	
		$ambil_data = $ci->db->query("SELECT nama FROM mst_karyawan WHERE no_pokok=(select no_pokok from usr_pengguna where id='$id')")->row_array();
		$ambil = $ambil_data['nama'];

		return $ambil ;
	}	
	
	function np_karyawan_by_id($id)
	{			
		$ci =& get_instance();	
		$ambil_data = $ci->db->query("SELECT no_pokok FROM mst_karyawan WHERE no_pokok=(select no_pokok from usr_pengguna where id='$id')")->row_array();
		$ambil = $ambil_data['no_pokok'];

		return $ambil ;
	}	
	
	function personnel_number_by_np($np)
	{			
		$ci =& get_instance();	
		$ambil_data = $ci->db->query("SELECT personnel_number FROM mst_karyawan WHERE no_pokok='$np'")->row_array();
		$ambil = $ambil_data['personnel_number'];

		return $ambil ;
	}	
	
	function mst_karyawan_by_np($np){
		$ci =& get_instance();
        //cek
        if(check_table_exist("mst_karyawan")=='ada'){
            $ambil_data = $ci->db->query("SELECT personnel_number, nama, kode_unit, nama_unit, nama_jabatan, nama_unit_poh, nama_jabatan_poh, kontrak_kerja, grup_jabatan FROM mst_karyawan WHERE no_pokok='$np' AND no_pokok!=''")->row_array();
            $ambil = $ambil_data;

            return $ambil ;
        }
		else{
            return NULL;
        }
	}
	
	function get_poh_data($np_poh) {
		$ci =& get_instance();
		// Cek apakah tabel 'poh' ada
		if (check_table_exist("poh") === 'ada') {
			// Mengambil data dari tabel
			$query = "SELECT tanggal_mulai, tanggal_selesai, np_definitif, np_poh 
					  FROM poh 
					  WHERE np_poh = ?
					  ORDER BY tanggal_mulai DESC 
                  	LIMIT 1";
			$ambil_data = $ci->db->query($query, [$np_poh])->row_array();
			
			return $ambil_data;
		}
	
		return null; // Tabel 'poh' tidak ada
	}
	
	function get_access_poh($np, $np_poh) {
		$ci =& get_instance();
	
		// Cek apakah tabel 'mst_poh' dan 'mst_karyawan' ada
		if (check_table_exist("mst_poh") === 'ada' && check_table_exist("mst_karyawan") === 'ada') {
	
			// Mendapatkan data approval (np_poh)
			$query = "SELECT kode_jabatan, kode_jabatan_poh, no_pokok 
					  FROM mst_karyawan 
					  WHERE no_pokok = ?";
			$np_poh_data = $ci->db->query($query, [$np_poh])->row_array();
	
			// Pastikan data $np_poh_data ditemukan
			if (!$np_poh_data || !isset($np_poh_data['kode_jabatan_poh'])) {
				return false; // Data approval tidak valid
			}
	
			// Ambil 3 angka terakhir dari kode_jabatan_poh
			$np_poh_kode_jabatan_poh = substr($np_poh_data['kode_jabatan_poh'], -3);
	
			// Mendapatkan data pengajuan (np)
			$query = "SELECT kode_jabatan, kode_jabatan_poh, no_pokok 
					  FROM mst_karyawan 
					  WHERE no_pokok = ?";
			$np_data = $ci->db->query($query, [$np])->row_array();
	
			// Pastikan data $np_data ditemukan
			if (!$np_data || !isset($np_data['kode_jabatan'])) {
				return false; // Data pengajuan tidak valid
			}
	
			// Ambil 3 angka terakhir dari kode_jabatan
			$np_kode_jabatan = substr($np_data['kode_jabatan'], -3);
	
			// Membandingkan data pada tabel 'mst_poh'
			$query_poh = "SELECT * 
						  FROM mst_poh 
						  WHERE kode_kelompok_jabatan = ? 
						  AND kode_kelompok_jabatan_poh = ?";
			$poh_data = $ci->db->query($query_poh, [$np_poh_kode_jabatan_poh, $np_kode_jabatan])->row_array();
	
			// Jika data ditemukan di 'mst_poh', return true
			if ($poh_data) {
				return true;
			}
	
			// Data tidak sesuai dengan tabel 'mst_poh'
			return false;
		}
	
		// Tabel 'mst_poh' atau 'mst_karyawan' tidak ada
		return false;
	}
	
	
	
	function tm_status_erp_master_data($np,$tanggal)
	{
		$tahun_bulan = str_replace('-','_',substr(date('Y-m-d',strtotime($tanggal)), 0, 7));
		$ci =& get_instance();
		
		//cek		
		$tabel_master_data = "erp_master_data_$tahun_bulan";
		$name=str_replace("-","_",$tabel_master_data);
		$check_tabel = $ci->db->query("show tables like '$name'")->row_array();		
		
        if(!$check_tabel){
            $table_fix = "erp_master_data";
            $field_np_fix = 'np_karyawan';
        } else{            
			$table_fix = "erp_master_data_$tahun_bulan";
            $field_np_fix = 'np_karyawan';
        }
		
		$ci->db->select("*");
        $ci->db->where($field_np_fix, $np);
        if($table_fix!='mst_karyawan'){
            $ci->db->where('tanggal_dws', $tanggal);
        }
        $ambil_data = $ci->db->get($table_fix)->row_array();
		
		$return = $ambil_data;

		
		return $return ;
	}
	
	function erp_master_data_by_np($np, $tanggal){
        $tahun_bulan = str_replace('-','_',substr(date('Y-m-d',strtotime($tanggal)), 0, 7));
		$ci =& get_instance();
        //cek
        if(check_table_exist("erp_master_data_$tahun_bulan")=='ada'){
            $table_fix = "erp_master_data_$tahun_bulan";
            $field_np_fix = 'np_karyawan';
        } else{
            $table_fix = 'mst_karyawan';
            $field_np_fix = 'no_pokok';
        }
		//old $ambil_data = $ci->db->query("SELECT personnel_number, nama, kode_unit, nama_unit, nama_jabatan FROM $table_fix WHERE $field_np_fix='$np'")->row_array();
        $ci->db->select("personnel_number, nama, kode_unit, nama_unit, nama_jabatan");
        $ci->db->where($field_np_fix, $np);
        if($table_fix!='mst_karyawan'){
            $ci->db->where('tanggal_dws', $tanggal);
        }
        $ambil_data = $ci->db->get($table_fix)->row_array();
		
		
		//jika tidak data maka makai mst karyawan
		if($ambil_data==null)
		{
			$table_fix = 'mst_karyawan';
            $field_np_fix = 'no_pokok';
			
			$ci->db->select("personnel_number, nama, kode_unit, nama_unit, nama_jabatan");
			$ci->db->where($field_np_fix, $np);
						
			$ambil_data = $ci->db->get($table_fix)->row_array();
			
		}
		
		// jika masih tidak ada 
		if($ambil_data==null){
			$table_fix = 'ess_karyawan_outsource';
			$field_np_fix= 'np_karyawan';
			
			$ci->db->select("nama, kode_unit, nama_unit");
			$ci->db->where($field_np_fix, $np);
						
			$ambil_data = $ci->db->get($table_fix)->row_array();
		}
		// note falonez 


		
		
		
		$return = $ambil_data;

		
		return $return ;
	}	
		
	function kode_unit_by_np($np)
	{			
		$ci =& get_instance();	
		$ambil_data = $ci->db->query("SELECT kode_unit FROM mst_karyawan WHERE no_pokok='$np'")->row_array();
		$ambil = $ambil_data['kode_unit'];

		if($ambil)
		{
			
		}else
		{
			$ambil_data = $ci->db->query("SELECT kode_unit FROM ess_karyawan_outsource WHERE np_karyawan='$np' ORDER BY id desc limit 1")->row_array();
			$ambil = $ambil_data['kode_unit'];
		}
		


		return $ambil ;
	}
	
	//06 01 2021, 7648 - Tri Wibowo, Agar bisa backdate input cuti ketika NP nya berubah
	function kode_unit_by_np_sebelumnya($np)
	{			
		$ci =& get_instance();	
				
		$date_now			= date('Y-m-d');
		$date_kemarin 		= date('Y-m-d', strtotime($date_now . ' -1 months'));
			
		$pisah_date_kemarin	= explode('-',$date_kemarin);
		$tahun2				= $pisah_date_kemarin[0];
		$bulan2				= $pisah_date_kemarin[1];
		$tahun_bulan_kemarin= $tahun2."_".$bulan2;
		
		$tabel_master_kemarin = 'erp_master_data_'.$tahun_bulan_kemarin;
		
		$check_table_exist=str_replace("-","_",$tabel_master_kemarin);
		$query_check = $ci->db->query("show tables like '$check_table_exist'")->row_array();
		
		if(!$query_check)
		{
			$tabel_master_kemarin = 'erp_master_data';
		}				
			
		$ambil_data = $ci->db->query("SELECT kode_unit FROM $tabel_master_kemarin WHERE np_karyawan='$np'")->row_array();
		$ambil = $ambil_data['kode_unit'];

		return $ambil ;
	}
	
	function sisa_cuti_tahunan_expired($np)
	{			
		$ci =& get_instance();
		$ci->db->select('sum(number - deduction) as sisa');
		$ci->db->where('np_karyawan', $np);
		$ci->db->where("deduction_to < CURDATE()", null,false);
		return $ci->db->get('erp_absence_quota')->row_array();
	}
	
	function sisa_cuti_tahunan($np)
	{			
		$ci =& get_instance();	
		$ambil_data = $ci->db->query("select sum(number-deduction) as sisa from erp_absence_quota where np_karyawan='$np' AND deduction_from<=CURDATE() AND deduction_to>=CURDATE()")->row_array();
		$ambil = $ambil_data['sisa'];

		return $ambil ;
	}
	
	function sisa_cuti_tahunan_untuk_hutang($np)
	{			
		$ci =& get_instance();	
		$ambil_data = $ci->db->query("select sum(number-deduction) as sisa from erp_absence_quota where np_karyawan='$np' AND deduction_from<=CURDATE() AND deduction_to>=CURDATE() AND YEAR(deduction_from) = '2023'")->row_array();
		$ambil = $ambil_data['sisa'];

		return $ambil ;
	}
	
	function sisa_cuti_tahunan_tahun_berjalan($np)
	{			
		$ci =& get_instance();	
		$ambil_data = $ci->db->query("select sum(number-deduction) as sisa from erp_absence_quota where np_karyawan='$np' AND deduction_from<=CURDATE() AND deduction_to>=CURDATE() AND YEAR(deduction_from) = '2024'")->row_array();
		$ambil = $ambil_data['sisa'];

		return $ambil ;
	}
	
	function sisa_cuti_tahunan_boleh_digunakan($np)
	{			
		$ci =& get_instance();	
		$ambil_data = $ci->db->query(" SELECT
											deduction_from,
											sum(number - deduction) AS sisa
										FROM
											erp_absence_quota
										WHERE
											np_karyawan = '$np'
										AND deduction_from <= CURDATE()
										AND deduction_to >= CURDATE()
										AND number - deduction > '0'
										ORDER BY
											deduction_from ASC
										LIMIT 1")->row_array();
		
		$ambil = $ambil_data['deduction_from'];

		return $ambil ;
	}
	
	function sisa_cuti_besar($np)
	{
		$ci =& get_instance();
		$ambil_data = $ci->db->query("select sum(sisa_bulan) as bulan, sum(sisa_hari) as hari from cuti_cubes_jatah where np_karyawan='$np' AND tanggal_timbul<=CURDATE() AND tanggal_kadaluarsa>=CURDATE()")->row_array();

		return $ambil_data ;
	}

	function cuti_tahunan_menunggu_sdm($np)
	{			
		$ci =& get_instance();	 
		$ci->load->model("administrator/m_pengaturan");
			
		$cutoff_erp_tanggal  	= $ci->m_pengaturan->ambil_isi_pengaturan('cutoff_erp_tanggal');	
		
		$date = date("Y-m-d");
		$pisah 		= explode('-',$date);
		$tahun 		= $pisah[0];
		$bulan 		= $pisah[1];
		$tanggal 	= $pisah[2];
		
		//HAPUS JIKA SUDAH, keperluan cutoff pertama karena GAGAL terus jadi mundur
		//$cutoff_erp_tanggal = '13';
		
		$cutoff = $tahun."-".$bulan."-".$cutoff_erp_tanggal;
		if($date<=$cutoff) //jika belum cutoff
		{
			$date_tahun_bulan 	= $tahun.'-'.$bulan;
			$date_sebelum		= date('Y-m',strtotime('-1 months',strtotime($date_tahun_bulan)));
			$pisah_sebelum 		= explode('-',$date_sebelum);
			$tahun_sebelum 		= $pisah_sebelum[0];
			$bulan_sebelum 		= $pisah_sebelum[1];
		
			$tanggal_awal	= $tahun_sebelum."-".$bulan_sebelum."-"."01";
		}else
		{
			$date_setelah		= $date;
			$pisah_setelah 		= explode('-',$date_setelah);
			$tahun_setelah 		= $pisah_setelah[0];
			$bulan_setelah 		= $pisah_setelah[1];
			$tanggal_setelah 	= $pisah_setelah[2];
			
			$tanggal_awal	= $tahun_setelah."-".$bulan_setelah."-"."01";
		}
		
		//21 12 2020, 7648 Tri Wibowo, menambah kondisi start_date>='$tanggal_awal' agar yang di liat yg lebih dari cutoff sehingga data data lalu tidak ikut
		$ambil_data = $ci->db->query("select sum(datediff(end_date, start_date)+1) AS menunggu_sdm from ess_cuti where np_karyawan='$np' AND (status_1  NOT IN ('2','3') AND status_2 NOT IN ('2','3') AND approval_sdm NOT IN ('1','2') AND absence_type='2001|1000' )AND (start_date>='$tanggal_awal')")->row_array();
		$ambil = $ambil_data['menunggu_sdm'];

		return $ambil ;
	}
	
	function cuti_tahunan_menunggu_cutoff($np)
	{		
		$ci =& get_instance();	
		$ci->load->model("administrator/m_pengaturan");
		 	
		$cutoff_erp_tanggal  	= $ci->m_pengaturan->ambil_isi_pengaturan('cutoff_erp_tanggal');	
		
		$date = date("Y-m-d");
		$pisah 		= explode('-',$date);
		$tahun 		= $pisah[0];
		$bulan 		= $pisah[1];
		$tanggal 	= $pisah[2];
		
		//HAPUS JIKA SUDAH, keperluan cutoff pertama karena GAGAL terus jadi mundur
		//$cutoff_erp_tanggal = '13';
		
		$cutoff = $tahun."-".$bulan."-".$cutoff_erp_tanggal;
		if($date<=$cutoff) //jika belum cutoff
		{
			$date_tahun_bulan 	= $tahun.'-'.$bulan;
			$date_sebelum		= date('Y-m',strtotime('-1 months',strtotime($date_tahun_bulan)));
			$pisah_sebelum 		= explode('-',$date_sebelum);
			$tahun_sebelum 		= $pisah_sebelum[0];
			$bulan_sebelum 		= $pisah_sebelum[1];
		
			$tanggal_awal	= $tahun_sebelum."-".$bulan_sebelum."-"."01";
		}else
		{
			$date_setelah		= $date;
			$pisah_setelah 		= explode('-',$date_setelah);
			$tahun_setelah 		= $pisah_setelah[0];
			$bulan_setelah 		= $pisah_setelah[1];
			$tanggal_setelah 	= $pisah_setelah[2];
			
			$tanggal_awal	= $tahun_setelah."-".$bulan_setelah."-"."01";
		}
		
		$jumlah = 0;
		
		//7648 Tri WIbowo 08-12-2020, 5593 pada tanggal 8 12 2020 masih kebawa padahal sudah cutoff tanggal 6 12 2020
		
		//$query = $ci->db->query("SELECT * FROM ess_cuti where approval_sdm='1' AND (start_date>=$tanggal_awal OR end_date<=$tanggal_awal) AND np_karyawan='$np' AND absence_type='2001|1000'");		
		$query = $ci->db->query("SELECT * FROM ess_cuti where approval_sdm='1' AND (start_date>='$tanggal_awal' OR end_date>='$tanggal_awal') AND np_karyawan='$np' AND absence_type='2001|1000'");
		
		foreach ($query->result_array() as $data) 
		{
			$date_start = $data['start_date'];
			$date_end	= $data['end_date'];
			  while (strtotime($date_start) <= strtotime($date_end)) {
                $date_cuti=$date_start;
				
				if($date_cuti>=$tanggal_awal)
				{
					$jumlah=$jumlah+1;
				}
				
				
				
				$date_start = date ("Y-m-d", strtotime("+1 day", strtotime($date_start)));
			  }
		
		
		}
	
	/*
		$ambil_data = $ci->db->query("select sum(datediff(end_date, start_date)+1) AS menunggu_cutoff from ess_cuti where np_karyawan='$np' AND (status_1  NOT IN ('2','3') AND status_2 NOT IN ('2','3') AND approval_sdm = '1' AND absence_type='2001|1000' AND ((start_date>'$tanggal_awal' AND start_date<='$tanggal_akhir') OR (end_date>'$tanggal_awal' AND end_date<='$tanggal_akhir')))")->row_array();
		$ambil = $ambil_data['menunggu_cutoff'];
	*/
	
		return $jumlah ;
	}
	
	
	
	//belum selesai
	function cuti_besar_menunggu_sdm($np)
	{			
		$ci =& get_instance();	
		$ambil_data = $ci->db->query("select sum(jumlah_bulan) AS menunggu_sdm_bulan, sum(jumlah_hari) AS menunggu_sdm_hari from ess_cuti where np_karyawan='$np' AND (status_1  NOT IN ('2','3') AND status_2 NOT IN ('2','3') AND approval_sdm NOT IN ('1','2') AND absence_type='2001|1010' )")->row_array();
		

		return $ambil_data ;
	}
	
	function nama_dws_by_kode($kode)
	{			
		$ci =& get_instance();	
		$ambil_data = $ci->db->query("SELECT description FROM mst_jadwal_kerja WHERE dws='$kode'")->row_array();
		$ambil = $ambil_data['description'];

		return $ambil ;
	}

    function get_ket_lembur($np, $dws){
        $ci =& get_instance();
        $return = '-';
        //cari di ess_cuti
        $get_cuti = $ci->db->select('alasan')->where('np_karyawan',$np)->where('start_date <=', $dws)->where('end_date >=', $dws)->where('approval_sdm', '1')->get('ess_cuti');
        if($get_cuti->num_rows()>0){
            $return = $get_cuti->row()->alasan;
        } else{
            //cari di sppd
            $get_sppd = $ci->db->select('perihal')->where('np_karyawan', $np)->where('tgl_berangkat <=', $dws)->where('tgl_pulang >=', $dws)->get('ess_sppd');
            if($get_sppd->num_rows()>0){
                $return = $get_sppd->row()->perihal;
            } else{
                //cari di ess_cuti_bersama
                $get_cuti_bersama = $ci->db->select('deskripsi')->where('tanggal', $dws)->get('mst_cuti_bersama');
                if($get_cuti_bersama->num_rows()>0){
                    $return = $get_cuti_bersama->row()->deskripsi;
                }
            }
        }
        
        return $return;
    }
	
?>