<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_approval extends CI_Model {

	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	function check_table_exist($name)
	{
		$name=str_replace("-","_",$name);
		$query = $this->db->query("show tables like '$name'")->row_array();
		
		return $query;
	}
	
	function list_atasan_minimal_kadep($arr_unit_kerja,$np_karyawan, $tanggal = null){
		$date_now			= date('Y-m-d');
		$date_kemarin 		= date('Y-m-d', strtotime($date_now . ' -1 months'));
		
		$pisah_date_now		= explode('-',$date_now);
		$tahun1				= $pisah_date_now[0];
		$bulan1				= $pisah_date_now[1];
		$tahun_bulan		= $tahun1."_".$bulan1;
		
		$pisah_date_kemarin	= explode('-',$date_kemarin);
		$tahun2				= $pisah_date_kemarin[0];
		$bulan2				= $pisah_date_kemarin[1];
		$tahun_bulan_kemarin= $tahun2."_".$bulan2;
		
		$tabel_master = 'erp_master_data_'.$tahun_bulan;
		if(!$this->check_table_exist($tabel_master)){
			$tabel_master = 'erp_master_data';
		}
		
		$tabel_master_kemarin = 'erp_master_data_'.$tahun_bulan_kemarin;
		if(!$this->check_table_exist($tabel_master_kemarin)){
			$tabel_master_kemarin = 'erp_master_data';
		}
		
		$where = "";
		
		if(!empty($np_karyawan)){
			$where = "np_karyawan NOT IN ('$np_karyawan')";
		}
		
		if(!empty($where)){
			$where .= " AND ";
		}
		
		$where .= "(";
			$where .= "grup_jabatan IN ('KADEP')";
			$where .= " OR ";
			$where .= "substr(kode_jabatan,-3) IN ('500','400','300','200','100')";
		$where .= ")";
		
		$unit_kerja_dir = array();
		$unit_kerja_div = array();
		$unit_kerja_dep = array();
				
		if(!empty($arr_unit_kerja)){
			
			foreach($arr_unit_kerja as $unit_kerja){
				
				//29 04 2020 | 7648 | Tri Wibowo, Pemisahan 0 untuk Check apakah kode unit divisi / departemen
				if(strcmp(substr($unit_kerja,1,1),"0")==0){
					$unit_kerja = substr($unit_kerja,0,3);
				}
				else{
					$unit_kerja = substr($unit_kerja,0,2);
				}			
				
				if(strlen($unit_kerja)==1){
					array_push($unit_kerja_div,$unit_kerja);
				}
				if(strlen($unit_kerja)==2){
					array_push($unit_kerja_div,$unit_kerja);
				}
				else if(strlen($unit_kerja)==3){
					if(strcmp(substr($unit_kerja,1,1),"0")==0){
						array_push($unit_kerja_dir,substr($unit_kerja,0,1));
					}
					else{
						array_push($unit_kerja_dep,$unit_kerja);
					}
				}
			}
			
			$where .= " AND ";

			$where_unit_kerja = "";
			if(!empty($unit_kerja_dir)){
				//2-12-2020 - 7648 Tri Wibowo menambah periode sehingga atasanya pas saat periode tersebut
				//$where_unit_kerja .= "substr(kode_unit,1,1) IN ('".implode(",",$unit_kerja_dir)."')";
				$where_unit_kerja .= "substr(kode_unit,1,1) IN (".implode(",",$unit_kerja_dir).")";
			}
			if(!empty($unit_kerja_div)){
				if(!empty($where_unit_kerja)){
					$where_unit_kerja .= " OR ";
				}
				//2-12-2020 - 7648 Tri Wibowo menambah periode sehingga atasanya pas saat periode tersebut
				//$where_unit_kerja .= "substr(kode_unit,1,2) IN ('".implode(",",$unit_kerja_div)."')";
				$where_unit_kerja .= "substr(kode_unit,1,2) IN (".implode(",",$unit_kerja_div).")";
			}
			if(!empty($unit_kerja_dep)){
				if(!empty($where_unit_kerja)){
					$where_unit_kerja .= " OR ";
				}
				//2-12-2020 - 7648 Tri Wibowo menambah periode sehingga atasanya pas saat periode tersebut
				//$where_unit_kerja .= "substr(kode_unit,1,3) IN ('".implode(",",$unit_kerja_dep)."')";
				$where_unit_kerja .= "substr(kode_unit,1,3) IN (".implode(",",$unit_kerja_dep).")";
			}
			//zanna 22/09/2021 comment jika sudah ada nde (list_atasan_minimal_kadep, list_atasan_minimal_kadiv, list_atasan_minimal_kadiv)
			//04 02 2022 Tri Wibowo 7648, Permintaan NDE terkait buka seluruh approval unit kerja
			//$where .= "($where_unit_kerja)";
			$where .= "(1=1)";
		}
		
		if(!empty($where))
		{
			$where = "WHERE ".$where;
		}
		
		/* All POH ditampilkan	
		$data = $this->db->query("SELECT c.np_karyawan as no_pokok, c.nama,c.kode_unit FROM 
									(SELECT a.np_karyawan, a.nama, a.kode_unit FROM $tabel_master a $where
									GROUP BY a.np_karyawan
									union all
									SELECT b.np_karyawan, b.nama, b.kode_unit FROM $tabel_master_kemarin b $where
									GROUP BY b.np_karyawan
									union all
									SELECT d.np_karyawan, d.nama, d.kode_unit FROM $tabel_master d where d.np_karyawan like 'D%' group by d.np_karyawan) c 
									where (c.np_karyawan!='' OR c.np_karyawan is not null) group by c.np_karyawan
									union all
									SELECT np_poh AS np_karyawan, nama_poh AS nama, kode_unit AS kode_unit FROM poh WHERE
									curdate()>=tanggal_mulai AND curdate()<=tanggal_selesai")->result_array();
		
		*/
		
		//filter POH unit kerja
		if(!empty($unit_kerja_dir)){
				
				$data = $this->db->query("SELECT c.np_karyawan as no_pokok, c.nama, c.kode_unit, c.nama_unit, c.nama_unit_singkat, c.nama_jabatan, c.tanggal_pensiun, c.grup_jabatan, c.kode_jabatan FROM 
									(SELECT a.np_karyawan, a.nama, a.kode_unit, a.nama_unit, a.nama_unit_singkat, a.nama_jabatan, a.tanggal_pensiun, a.grup_jabatan, a.kode_jabatan FROM $tabel_master a $where
									GROUP BY a.np_karyawan
									union all
									SELECT b.np_karyawan, b.nama, b.kode_unit, b.nama_unit, b.nama_unit_singkat, b.nama_jabatan, b.tanggal_pensiun, b.grup_jabatan, b.kode_jabatan FROM $tabel_master_kemarin b $where
									GROUP BY b.np_karyawan
									union all
									SELECT d.np_karyawan, d.nama, d.kode_unit, d.nama_unit, d.nama_unit_singkat, d.nama_jabatan, d.tanggal_pensiun, d.grup_jabatan, d.kode_jabatan FROM $tabel_master d where d.np_karyawan like 'D%' group by d.np_karyawan) c 
									where (c.np_karyawan!='' OR c.np_karyawan is not null) group by c.np_karyawan
									union all
									SELECT np_poh AS np_karyawan, nama_poh AS nama, kode_unit AS kode_unit, nama_unit, nama_unit AS nama_unit_singkat, nama_jabatan, tanggal_selesai AS tanggal_pensiun, null AS grup_jabatan, kode_jabatan FROM poh WHERE
									curdate()>=tanggal_mulai AND curdate()<=tanggal_selesai AND
									(
										substr(kode_unit,1,1) IN (".implode(",",$unit_kerja_dir).") 
									)")->result_array();
				
			}
			if(!empty($unit_kerja_div)){
				
				$data = $this->db->query("SELECT c.np_karyawan as no_pokok, c.nama, c.kode_unit, c.nama_unit, c.nama_unit_singkat, c.nama_jabatan, c.tanggal_pensiun, c.grup_jabatan, c.kode_jabatan FROM 
									(SELECT a.np_karyawan, a.nama, a.kode_unit, a.nama_unit, a.nama_unit_singkat, a.nama_jabatan, a.tanggal_pensiun, a.grup_jabatan, a.kode_jabatan FROM $tabel_master a $where
									GROUP BY a.np_karyawan
									union all
									SELECT b.np_karyawan, b.nama, b.kode_unit, b.nama_unit, b.nama_unit_singkat, b.nama_jabatan, b.tanggal_pensiun, b.grup_jabatan, b.kode_jabatan FROM $tabel_master_kemarin b $where
									GROUP BY b.np_karyawan
									union all
									SELECT d.np_karyawan, d.nama, d.kode_unit, d.nama_unit, d.nama_unit_singkat, d.nama_jabatan, d.tanggal_pensiun, d.grup_jabatan, d.kode_jabatan FROM $tabel_master d where d.np_karyawan like 'D%' group by d.np_karyawan) c 
									where (c.np_karyawan!='' OR c.np_karyawan is not null) group by c.np_karyawan
									union all
									SELECT np_poh AS np_karyawan, nama_poh AS nama, kode_unit AS kode_unit, nama_unit, nama_unit AS nama_unit_singkat, nama_jabatan, tanggal_selesai AS tanggal_pensiun, null AS grup_jabatan, kode_jabatan FROM poh WHERE
									curdate()>=tanggal_mulai AND curdate()<=tanggal_selesai AND
									(
										substr(kode_unit,1,2) IN (".implode(",",$unit_kerja_div).")
									)")->result_array();
						
			}
			if(!empty($unit_kerja_dep)){
			
				$data = $this->db->query("SELECT c.np_karyawan as no_pokok, c.nama, c.kode_unit, c.nama_unit, c.nama_unit_singkat, c.nama_jabatan, c.tanggal_pensiun, c.grup_jabatan, c.kode_jabatan FROM 
									(SELECT a.np_karyawan, a.nama, a.kode_unit, a.nama_unit, a.nama_unit_singkat, a.nama_jabatan, a.tanggal_pensiun, a.grup_jabatan, a.kode_jabatan FROM $tabel_master a $where
									GROUP BY a.np_karyawan
									union all
									SELECT b.np_karyawan, b.nama, b.kode_unit, b.nama_unit, b.nama_unit_singkat, b.nama_jabatan, b.tanggal_pensiun, b.grup_jabatan, b.kode_jabatan FROM $tabel_master_kemarin b $where
									GROUP BY b.np_karyawan
									union all
									SELECT d.np_karyawan, d.nama, d.kode_unit, d.nama_unit, d.nama_unit_singkat, d.nama_jabatan, d.tanggal_pensiun, d.grup_jabatan, d.kode_jabatan FROM $tabel_master d where d.np_karyawan like 'D%' group by d.np_karyawan) c 
									where (c.np_karyawan!='' OR c.np_karyawan is not null) group by c.np_karyawan
									union all
									SELECT np_poh AS np_karyawan, nama_poh AS nama, kode_unit AS kode_unit, nama_unit, nama_unit AS nama_unit_singkat, nama_jabatan, tanggal_selesai AS tanggal_pensiun, null AS grup_jabatan, kode_jabatan FROM poh WHERE
									curdate()>=tanggal_mulai AND curdate()<=tanggal_selesai AND
									(
										substr(kode_unit,1,3) IN (".implode(",",$unit_kerja_dep).")
									)")->result_array();
			
			}	
		
		return $data;
	}
	
	function list_atasan_minimal_kasek($arr_unit_kerja,$np_karyawan, $tanggal = null){
		$date_now			= date('Y-m-d');
		$date_kemarin 		= date('Y-m-d', strtotime($date_now . ' -1 months'));
		
		$pisah_date_now		= explode('-',$date_now);
		$tahun1				= $pisah_date_now[0];
		$bulan1				= $pisah_date_now[1];
		$tahun_bulan		= $tahun1."_".$bulan1;
		
		$pisah_date_kemarin	= explode('-',$date_kemarin);
		$tahun2				= $pisah_date_kemarin[0];
		$bulan2				= $pisah_date_kemarin[1];
		$tahun_bulan_kemarin= $tahun2."_".$bulan2;
		
		$tabel_master = 'erp_master_data_'.$tahun_bulan;
		if(!$this->check_table_exist($tabel_master)){
			$tabel_master = 'erp_master_data';
		}
		
		$tabel_master_kemarin = 'erp_master_data_'.$tahun_bulan_kemarin;
		if(!$this->check_table_exist($tabel_master_kemarin)){
			$tabel_master_kemarin = 'erp_master_data';
		}
		
		$where = "";
		
		if(!empty($np_karyawan)){
			$where = "np_karyawan NOT IN ('$np_karyawan')";
		}
		
		if(!empty($where)){
			$where .= " AND ";
		}
		
		$where .= "(";
		$where .= "grup_jabatan IN ('AHLIMUDA','AHLIMDYA','KASEK','KADEP')";
		$where .= " OR ";
		$where .= "substr(kode_jabatan,-3) IN ('600','500','400','300','200','100')";
		$where .= ")";
		
		$unit_kerja_dir = array();
		$unit_kerja_div = array();
		$unit_kerja_dep = array();
				
		if(!empty($arr_unit_kerja)){
			
			foreach($arr_unit_kerja as $unit_kerja){
				
				//29 04 2020 | 7648 | Tri Wibowo, Pemisahan 0 untuk Check apakah kode unit divisi / departemen
				if(strcmp(substr($unit_kerja,1,1),"0")==0){
					$unit_kerja = substr($unit_kerja,0,3);
				}
				else{
					$unit_kerja = substr($unit_kerja,0,2);
				}			
				
				if(strlen($unit_kerja)==1){
					array_push($unit_kerja_div,$unit_kerja);
				}
				if(strlen($unit_kerja)==2){
					array_push($unit_kerja_div,$unit_kerja);
				}
				else if(strlen($unit_kerja)==3){
					if(strcmp(substr($unit_kerja,1,1),"0")==0){
						array_push($unit_kerja_dir,substr($unit_kerja,0,1));
					}
					else{
						array_push($unit_kerja_dep,$unit_kerja);
					}
				}
			}
			
			$where .= " AND ";

			$where_unit_kerja = "";
			if(!empty($unit_kerja_dir)){
				//2-12-2020 - 7648 Tri Wibowo menambah periode sehingga atasanya pas saat periode tersebut
				//$where_unit_kerja .= "substr(kode_unit,1,1) IN ('".implode(",",$unit_kerja_dir)."')";
				$where_unit_kerja .= "substr(kode_unit,1,1) IN (".implode(",",$unit_kerja_dir).")";
			}
			if(!empty($unit_kerja_div)){
				if(!empty($where_unit_kerja)){
					$where_unit_kerja .= " OR ";
				}
				//2-12-2020 - 7648 Tri Wibowo menambah periode sehingga atasanya pas saat periode tersebut
				//$where_unit_kerja .= "substr(kode_unit,1,2) IN ('".implode(",",$unit_kerja_div)."')";
				$where_unit_kerja .= "substr(kode_unit,1,2) IN (".implode(",",$unit_kerja_div).")";
			}
			if(!empty($unit_kerja_dep)){
				if(!empty($where_unit_kerja)){
					$where_unit_kerja .= " OR ";
				}
				//2-12-2020 - 7648 Tri Wibowo menambah periode sehingga atasanya pas saat periode tersebut
				//$where_unit_kerja .= "substr(kode_unit,1,3) IN ('".implode(",",$unit_kerja_dep)."')";
				$where_unit_kerja .= "substr(kode_unit,1,3) IN (".implode(",",$unit_kerja_dep).")";
			}
			
			//04 02 2022 Tri Wibowo 7648, Permintaan NDE terkait buka seluruh approval unit kerja
			//$where .= "($where_unit_kerja)";
			$where .= "(1=1)";
		}
		
		if(!empty($where))
		{
			$where = "WHERE ".$where;
		}
		
		/* All POH ditampilkan	
		$data = $this->db->query("SELECT c.np_karyawan as no_pokok, c.nama,c.kode_unit FROM 
									(SELECT a.np_karyawan, a.nama, a.kode_unit FROM $tabel_master a $where
									GROUP BY a.np_karyawan
									union all
									SELECT b.np_karyawan, b.nama, b.kode_unit FROM $tabel_master_kemarin b $where
									GROUP BY b.np_karyawan
									union all
									SELECT d.np_karyawan, d.nama, d.kode_unit FROM $tabel_master d where d.np_karyawan like 'D%' group by d.np_karyawan) c 
									where (c.np_karyawan!='' OR c.np_karyawan is not null) group by c.np_karyawan
									union all
									SELECT np_poh AS np_karyawan, nama_poh AS nama, kode_unit AS kode_unit FROM poh WHERE
									curdate()>=tanggal_mulai AND curdate()<=tanggal_selesai")->result_array();
		
		*/
		
		//filter POH unit kerja
		if(!empty($unit_kerja_dir)){
				
				$data = $this->db->query("SELECT c.np_karyawan as no_pokok, c.nama, c.kode_unit, c.nama_unit, c.nama_unit_singkat, c.nama_jabatan, c.tanggal_pensiun, c.grup_jabatan, c.kode_jabatan FROM 
									(SELECT a.np_karyawan, a.nama, a.kode_unit, a.nama_unit, a.nama_unit_singkat, a.nama_jabatan, a.tanggal_pensiun, a.grup_jabatan, a.kode_jabatan FROM $tabel_master a $where
									GROUP BY a.np_karyawan
									union all
									SELECT b.np_karyawan, b.nama, b.kode_unit, b.nama_unit, b.nama_unit_singkat, b.nama_jabatan, b.tanggal_pensiun, b.grup_jabatan, b.kode_jabatan FROM $tabel_master_kemarin b $where
									GROUP BY b.np_karyawan
									union all
									SELECT d.np_karyawan, d.nama, d.kode_unit, d.nama_unit, d.nama_unit_singkat, d.nama_jabatan, d.tanggal_pensiun, d.grup_jabatan, d.kode_jabatan FROM $tabel_master d where d.np_karyawan like 'D%' group by d.np_karyawan) c 
									where (c.np_karyawan!='' OR c.np_karyawan is not null) group by c.np_karyawan
									union all
									SELECT np_poh AS np_karyawan, nama_poh AS nama, kode_unit AS kode_unit, nama_unit, nama_unit AS nama_unit_singkat, nama_jabatan, tanggal_selesai AS tanggal_pensiun, null AS grup_jabatan, kode_jabatan FROM poh WHERE
									curdate()>=tanggal_mulai AND curdate()<=tanggal_selesai AND
									(
										substr(kode_unit,1,1) IN (".implode(",",$unit_kerja_dir).") 
									)")->result_array();
				
			}
			if(!empty($unit_kerja_div)){
				
				$data = $this->db->query("SELECT c.np_karyawan as no_pokok, c.nama, c.kode_unit, c.nama_unit, c.nama_unit_singkat, c.nama_jabatan, c.tanggal_pensiun, c.grup_jabatan, c.kode_jabatan FROM 
									(SELECT a.np_karyawan, a.nama, a.kode_unit, a.nama_unit, a.nama_unit_singkat, a.nama_jabatan, a.tanggal_pensiun, a.grup_jabatan, a.kode_jabatan FROM $tabel_master a $where
									GROUP BY a.np_karyawan
									union all
									SELECT b.np_karyawan, b.nama, b.kode_unit, b.nama_unit, b.nama_unit_singkat, b.nama_jabatan, b.tanggal_pensiun, b.grup_jabatan, b.kode_jabatan FROM $tabel_master_kemarin b $where
									GROUP BY b.np_karyawan
									union all
									SELECT d.np_karyawan, d.nama, d.kode_unit, d.nama_unit, d.nama_unit_singkat, d.nama_jabatan, d.tanggal_pensiun, d.grup_jabatan, d.kode_jabatan FROM $tabel_master d where d.np_karyawan like 'D%' group by d.np_karyawan) c 
									where (c.np_karyawan!='' OR c.np_karyawan is not null) group by c.np_karyawan
									union all
									SELECT np_poh AS np_karyawan, nama_poh AS nama, kode_unit AS kode_unit, nama_unit, nama_unit AS nama_unit_singkat, nama_jabatan, tanggal_selesai AS tanggal_pensiun, null AS grup_jabatan, kode_jabatan FROM poh WHERE
									curdate()>=tanggal_mulai AND curdate()<=tanggal_selesai AND
									(
										substr(kode_unit,1,2) IN (".implode(",",$unit_kerja_div).")
									)")->result_array();
						
			}
			if(!empty($unit_kerja_dep)){
			
				$data = $this->db->query("SELECT c.np_karyawan as no_pokok, c.nama, c.kode_unit, c.nama_unit, c.nama_unit_singkat, c.nama_jabatan, c.tanggal_pensiun, c.grup_jabatan, c.kode_jabatan FROM 
									(SELECT a.np_karyawan, a.nama, a.kode_unit, a.nama_unit, a.nama_unit_singkat, a.nama_jabatan, a.tanggal_pensiun, a.grup_jabatan, a.kode_jabatan FROM $tabel_master a $where
									GROUP BY a.np_karyawan
									union all
									SELECT b.np_karyawan, b.nama, b.kode_unit, b.nama_unit, b.nama_unit_singkat, b.nama_jabatan, b.tanggal_pensiun, b.grup_jabatan, b.kode_jabatan FROM $tabel_master_kemarin b $where
									GROUP BY b.np_karyawan
									union all
									SELECT d.np_karyawan, d.nama, d.kode_unit, d.nama_unit, d.nama_unit_singkat, d.nama_jabatan, d.tanggal_pensiun, d.grup_jabatan, d.kode_jabatan FROM $tabel_master d where d.np_karyawan like 'D%' group by d.np_karyawan) c 
									where (c.np_karyawan!='' OR c.np_karyawan is not null) group by c.np_karyawan
									union all
									SELECT np_poh AS np_karyawan, nama_poh AS nama, kode_unit AS kode_unit, nama_unit, nama_unit AS nama_unit_singkat, nama_jabatan, tanggal_selesai AS tanggal_pensiun, null AS grup_jabatan, kode_jabatan FROM poh WHERE
									curdate()>=tanggal_mulai AND curdate()<=tanggal_selesai AND
									(
										substr(kode_unit,1,3) IN (".implode(",",$unit_kerja_dep).")
									)")->result_array();
			
			}	
		
		return $data;
	}
    
    # minimal kadiv
	function list_atasan_minimal_kadiv($arr_unit_kerja,$np_karyawan, $tanggal = null){
		$date_now			= date('Y-m-d');
		$date_kemarin 		= date('Y-m-d', strtotime($date_now . ' -1 months'));
		
		$pisah_date_now		= explode('-',$date_now);
		$tahun1				= $pisah_date_now[0];
		$bulan1				= $pisah_date_now[1];
		$tahun_bulan		= $tahun1."_".$bulan1;
		
		$pisah_date_kemarin	= explode('-',$date_kemarin);
		$tahun2				= $pisah_date_kemarin[0];
		$bulan2				= $pisah_date_kemarin[1];
		$tahun_bulan_kemarin= $tahun2."_".$bulan2;
		
		$tabel_master = 'erp_master_data_'.$tahun_bulan;
		if(!$this->check_table_exist($tabel_master)){
			$tabel_master = 'erp_master_data';
		}
		
		$tabel_master_kemarin = 'erp_master_data_'.$tahun_bulan_kemarin;
		if(!$this->check_table_exist($tabel_master_kemarin)){
			$tabel_master_kemarin = 'erp_master_data';
		}
		
		$where = "";
		
		if(!empty($np_karyawan)){
			$where = "np_karyawan NOT IN ('$np_karyawan')";
		}
		
		if(!empty($where)){
			$where .= " AND ";
		}
		
		$where .= "(";
			$where .= "grup_jabatan IN ('KADIV')";
			$where .= " OR ";
			$where .= "substr(kode_jabatan,-3) IN ('300','200','100')";
		$where .= ")";
		
		$unit_kerja_dir = array();
		$unit_kerja_div = array();
		$unit_kerja_dep = array();
				
		if(!empty($arr_unit_kerja)){
			
			foreach($arr_unit_kerja as $unit_kerja){
				
				//29 04 2020 | 7648 | Tri Wibowo, Pemisahan 0 untuk Check apakah kode unit divisi / departemen
				if(strcmp(substr($unit_kerja,1,1),"0")==0){
					$unit_kerja = substr($unit_kerja,0,3);
				}
				else{
					$unit_kerja = substr($unit_kerja,0,2);
				}			
				
				if(strlen($unit_kerja)==1){
					array_push($unit_kerja_div,$unit_kerja);
				}
				if(strlen($unit_kerja)==2){
					array_push($unit_kerja_div,$unit_kerja);
				}
				else if(strlen($unit_kerja)==3){
					if(strcmp(substr($unit_kerja,1,1),"0")==0){
						array_push($unit_kerja_dir,substr($unit_kerja,0,1));
					}
					else{
						array_push($unit_kerja_dep,$unit_kerja);
					}
				}
			}
			
			$where .= " AND ";

			$where_unit_kerja = "";
			if(!empty($unit_kerja_dir)){
				//2-12-2020 - 7648 Tri Wibowo menambah periode sehingga atasanya pas saat periode tersebut
				//$where_unit_kerja .= "substr(kode_unit,1,1) IN ('".implode(",",$unit_kerja_dir)."')";
				$where_unit_kerja .= "substr(kode_unit,1,1) IN (".implode(",",$unit_kerja_dir).")";
			}
			if(!empty($unit_kerja_div)){
				if(!empty($where_unit_kerja)){
					$where_unit_kerja .= " OR ";
				}
				//2-12-2020 - 7648 Tri Wibowo menambah periode sehingga atasanya pas saat periode tersebut
				//$where_unit_kerja .= "substr(kode_unit,1,2) IN ('".implode(",",$unit_kerja_div)."')";
				$where_unit_kerja .= "substr(kode_unit,1,2) IN (".implode(",",$unit_kerja_div).")";
			}
			if(!empty($unit_kerja_dep)){
				if(!empty($where_unit_kerja)){
					$where_unit_kerja .= " OR ";
				}
				//2-12-2020 - 7648 Tri Wibowo menambah periode sehingga atasanya pas saat periode tersebut
				//$where_unit_kerja .= "substr(kode_unit,1,3) IN ('".implode(",",$unit_kerja_dep)."')";
				$where_unit_kerja .= "substr(kode_unit,1,3) IN (".implode(",",$unit_kerja_dep).")";
			}

			//04 02 2022 Tri Wibowo 7648, Permintaan NDE terkait buka seluruh approval unit kerja
			//$where .= "($where_unit_kerja)";
			$where .= "(1=1)";
		}
		
		if(!empty($where))
		{
			$where = "WHERE ".$where;
		}
		
		/* All POH ditampilkan	
		$data = $this->db->query("SELECT c.np_karyawan as no_pokok, c.nama,c.kode_unit FROM 
									(SELECT a.np_karyawan, a.nama, a.kode_unit FROM $tabel_master a $where
									GROUP BY a.np_karyawan
									union all
									SELECT b.np_karyawan, b.nama, b.kode_unit FROM $tabel_master_kemarin b $where
									GROUP BY b.np_karyawan
									union all
									SELECT d.np_karyawan, d.nama, d.kode_unit FROM $tabel_master d where d.np_karyawan like 'D%' group by d.np_karyawan) c 
									where (c.np_karyawan!='' OR c.np_karyawan is not null) group by c.np_karyawan
									union all
									SELECT np_poh AS np_karyawan, nama_poh AS nama, kode_unit AS kode_unit FROM poh WHERE
									curdate()>=tanggal_mulai AND curdate()<=tanggal_selesai")->result_array();
		
		*/
		
		//filter POH unit kerja
		if(!empty($unit_kerja_dir)){
				
				$data = $this->db->query("SELECT c.np_karyawan as no_pokok, c.nama, c.kode_unit, c.nama_unit, c.nama_unit_singkat, c.nama_jabatan, c.tanggal_pensiun, c.grup_jabatan, c.kode_jabatan FROM 
									(SELECT a.np_karyawan, a.nama, a.kode_unit, a.nama_unit, a.nama_unit_singkat, a.nama_jabatan, a.tanggal_pensiun, a.grup_jabatan, a.kode_jabatan FROM $tabel_master a $where
									GROUP BY a.np_karyawan
									union all
									SELECT b.np_karyawan, b.nama, b.kode_unit, b.nama_unit, b.nama_unit_singkat, b.nama_jabatan, b.tanggal_pensiun, b.grup_jabatan, b.kode_jabatan FROM $tabel_master_kemarin b $where
									GROUP BY b.np_karyawan
									union all
									SELECT d.np_karyawan, d.nama, d.kode_unit, d.nama_unit, d.nama_unit_singkat, d.nama_jabatan, d.tanggal_pensiun, d.grup_jabatan, d.kode_jabatan FROM $tabel_master d where d.np_karyawan like 'D%' group by d.np_karyawan) c 
									where (c.np_karyawan!='' OR c.np_karyawan is not null) group by c.np_karyawan
									union all
									SELECT np_poh AS np_karyawan, nama_poh AS nama, kode_unit AS kode_unit, nama_unit, nama_unit AS nama_unit_singkat, nama_jabatan, tanggal_selesai AS tanggal_pensiun, null AS grup_jabatan, kode_jabatan FROM poh WHERE
									curdate()>=tanggal_mulai AND curdate()<=tanggal_selesai AND
									(
										substr(kode_unit,1,1) IN (".implode(",",$unit_kerja_dir).") 
									)")->result_array();
				
			}
			if(!empty($unit_kerja_div)){
				
				$data = $this->db->query("SELECT c.np_karyawan as no_pokok, c.nama, c.kode_unit, c.nama_unit, c.nama_unit_singkat, c.nama_jabatan, c.tanggal_pensiun, c.grup_jabatan, c.kode_jabatan FROM 
									(SELECT a.np_karyawan, a.nama, a.kode_unit, a.nama_unit, a.nama_unit_singkat, a.nama_jabatan, a.tanggal_pensiun, a.grup_jabatan, a.kode_jabatan FROM $tabel_master a $where
									GROUP BY a.np_karyawan
									union all
									SELECT b.np_karyawan, b.nama, b.kode_unit, b.nama_unit, b.nama_unit_singkat, b.nama_jabatan, b.tanggal_pensiun, b.grup_jabatan, b.kode_jabatan FROM $tabel_master_kemarin b $where
									GROUP BY b.np_karyawan
									union all
									SELECT d.np_karyawan, d.nama, d.kode_unit, d.nama_unit, d.nama_unit_singkat, d.nama_jabatan, d.tanggal_pensiun, d.grup_jabatan, d.kode_jabatan FROM $tabel_master d where d.np_karyawan like 'D%' group by d.np_karyawan) c 
									where (c.np_karyawan!='' OR c.np_karyawan is not null) group by c.np_karyawan
									union all
									SELECT np_poh AS np_karyawan, nama_poh AS nama, kode_unit AS kode_unit, nama_unit, nama_unit AS nama_unit_singkat, nama_jabatan, tanggal_selesai AS tanggal_pensiun, null AS grup_jabatan, kode_jabatan FROM poh WHERE
									curdate()>=tanggal_mulai AND curdate()<=tanggal_selesai AND
									(
										substr(kode_unit,1,2) IN (".implode(",",$unit_kerja_div).")
									)")->result_array();
						
			}
			if(!empty($unit_kerja_dep)){
			
				$data = $this->db->query("SELECT c.np_karyawan as no_pokok, c.nama, c.kode_unit, c.nama_unit, c.nama_unit_singkat, c.nama_jabatan, c.tanggal_pensiun, c.grup_jabatan, c.kode_jabatan FROM 
									(SELECT a.np_karyawan, a.nama, a.kode_unit, a.nama_unit, a.nama_unit_singkat, a.nama_jabatan, a.tanggal_pensiun, a.grup_jabatan, a.kode_jabatan FROM $tabel_master a $where
									GROUP BY a.np_karyawan
									union all
									SELECT b.np_karyawan, b.nama, b.kode_unit, b.nama_unit, b.nama_unit_singkat, b.nama_jabatan, b.tanggal_pensiun, b.grup_jabatan, b.kode_jabatan FROM $tabel_master_kemarin b $where
									GROUP BY b.np_karyawan
									union all
									SELECT d.np_karyawan, d.nama, d.kode_unit, d.nama_unit, d.nama_unit_singkat, d.nama_jabatan, d.tanggal_pensiun, d.grup_jabatan, d.kode_jabatan FROM $tabel_master d where d.np_karyawan like 'D%' group by d.np_karyawan) c 
									where (c.np_karyawan!='' OR c.np_karyawan is not null) group by c.np_karyawan
									union all
									SELECT np_poh AS np_karyawan, nama_poh AS nama, kode_unit AS kode_unit, nama_jabatan, tanggal_selesai AS tanggal_pensiun, null AS grup_jabatan, kode_jabatan FROM poh WHERE
									curdate()>=tanggal_mulai AND curdate()<=tanggal_selesai AND
									(
										substr(kode_unit,1,3) IN (".implode(",",$unit_kerja_dep).")
									)")->result_array();
			
			}	
		
		return $data;
	}

	function list_atasan_minimal_kaun($arr_unit_kerja,$np_karyawan, $tanggal = null){
		$date_now			= date('Y-m-d');
		$date_kemarin 		= date('Y-m-d', strtotime($date_now . ' -1 months'));
		
		$pisah_date_now		= explode('-',$date_now);
		$tahun1				= $pisah_date_now[0];
		$bulan1				= $pisah_date_now[1];
		$tahun_bulan		= $tahun1."_".$bulan1;
		
		$pisah_date_kemarin	= explode('-',$date_kemarin);
		$tahun2				= $pisah_date_kemarin[0];
		$bulan2				= $pisah_date_kemarin[1];
		$tahun_bulan_kemarin= $tahun2."_".$bulan2;
		
		$tabel_master = 'erp_master_data_'.$tahun_bulan;
		if(!$this->check_table_exist($tabel_master)){
			$tabel_master = 'erp_master_data';
		}
		
		$tabel_master_kemarin = 'erp_master_data_'.$tahun_bulan_kemarin;
		if(!$this->check_table_exist($tabel_master_kemarin)){
			$tabel_master_kemarin = 'erp_master_data';
		}
		
		$where = "";
		
		if(!empty($np_karyawan)){
			$where = "np_karyawan NOT IN ('$np_karyawan')";
		}
		
		if(!empty($where)){
			$where .= " AND ";
		}
		
		$where .= "(";
		$where .= "grup_jabatan IN ('AHLIMUDA','AHLIMDYA','KASEK','KADEP','KAUN','AHLIPTMA')";
		$where .= " OR ";
		$where .= "substr(kode_jabatan,-3) IN ('700','600','500','400','300','200','100')";
		$where .= ")";
		
		$unit_kerja_dir = array();
		$unit_kerja_div = array();
		$unit_kerja_dep = array();
				
		if(!empty($arr_unit_kerja)){
			
			foreach($arr_unit_kerja as $unit_kerja){
				
				//29 04 2020 | 7648 | Tri Wibowo, Pemisahan 0 untuk Check apakah kode unit divisi / departemen
				if(strcmp(substr($unit_kerja,1,1),"0")==0){
					$unit_kerja = substr($unit_kerja,0,3);
				}
				else{
					$unit_kerja = substr($unit_kerja,0,2);
				}			
				
				if(strlen($unit_kerja)==1){
					array_push($unit_kerja_div,$unit_kerja);
				}
				if(strlen($unit_kerja)==2){
					array_push($unit_kerja_div,$unit_kerja);
				}
				else if(strlen($unit_kerja)==3){
					if(strcmp(substr($unit_kerja,1,1),"0")==0){
						array_push($unit_kerja_dir,substr($unit_kerja,0,1));
					}
					else{
						array_push($unit_kerja_dep,$unit_kerja);
					}
				}
			}
			
			$where .= " AND ";

			$where_unit_kerja = "";
			if(!empty($unit_kerja_dir)){
				//2-12-2020 - 7648 Tri Wibowo menambah periode sehingga atasanya pas saat periode tersebut
				//$where_unit_kerja .= "substr(kode_unit,1,1) IN ('".implode(",",$unit_kerja_dir)."')";
				$where_unit_kerja .= "substr(kode_unit,1,1) IN (".implode(",",$unit_kerja_dir).")";
			}
			if(!empty($unit_kerja_div)){
				if(!empty($where_unit_kerja)){
					$where_unit_kerja .= " OR ";
				}
				//2-12-2020 - 7648 Tri Wibowo menambah periode sehingga atasanya pas saat periode tersebut
				//$where_unit_kerja .= "substr(kode_unit,1,2) IN ('".implode(",",$unit_kerja_div)."')";
				$where_unit_kerja .= "substr(kode_unit,1,2) IN (".implode(",",$unit_kerja_div).")";
			}
			if(!empty($unit_kerja_dep)){
				if(!empty($where_unit_kerja)){
					$where_unit_kerja .= " OR ";
				}
				//2-12-2020 - 7648 Tri Wibowo menambah periode sehingga atasanya pas saat periode tersebut
				//$where_unit_kerja .= "substr(kode_unit,1,3) IN ('".implode(",",$unit_kerja_dep)."')";
				$where_unit_kerja .= "substr(kode_unit,1,3) IN (".implode(",",$unit_kerja_dep).")";
			}
			
			//04 02 2022 Tri Wibowo 7648, Permintaan NDE terkait buka seluruh approval unit kerja
			//$where .= "($where_unit_kerja)";
			$where .= "(1=1)";
		}
		
		if(!empty($where))
		{
			$where = "WHERE ".$where;
		}
		
		/* All POH ditampilkan	
		$data = $this->db->query("SELECT c.np_karyawan as no_pokok, c.nama,c.kode_unit FROM 
									(SELECT a.np_karyawan, a.nama, a.kode_unit FROM $tabel_master a $where
									GROUP BY a.np_karyawan
									union all
									SELECT b.np_karyawan, b.nama, b.kode_unit FROM $tabel_master_kemarin b $where
									GROUP BY b.np_karyawan
									union all
									SELECT d.np_karyawan, d.nama, d.kode_unit FROM $tabel_master d where d.np_karyawan like 'D%' group by d.np_karyawan) c 
									where (c.np_karyawan!='' OR c.np_karyawan is not null) group by c.np_karyawan
									union all
									SELECT np_poh AS np_karyawan, nama_poh AS nama, kode_unit AS kode_unit FROM poh WHERE
									curdate()>=tanggal_mulai AND curdate()<=tanggal_selesai")->result_array();
		
		*/
		
		//filter POH unit kerja
		if(!empty($unit_kerja_dir)){
				
				$data = $this->db->query("SELECT c.np_karyawan as no_pokok, c.nama, c.kode_unit, c.nama_unit, c.nama_unit_singkat, c.nama_jabatan, c.tanggal_pensiun, c.grup_jabatan, c.kode_jabatan FROM 
									(SELECT a.np_karyawan, a.nama, a.kode_unit, a.nama_unit, a.nama_unit_singkat, a.nama_jabatan, a.tanggal_pensiun, a.grup_jabatan, a.kode_jabatan FROM $tabel_master a $where
									GROUP BY a.np_karyawan
									union all
									SELECT b.np_karyawan, b.nama, b.kode_unit, b.nama_unit, b.nama_unit_singkat, b.nama_jabatan, b.tanggal_pensiun, b.grup_jabatan, b.kode_jabatan FROM $tabel_master_kemarin b $where
									GROUP BY b.np_karyawan
									union all
									SELECT d.np_karyawan, d.nama, d.kode_unit, d.nama_unit, d.nama_unit_singkat, d.nama_jabatan, d.tanggal_pensiun, d.grup_jabatan, d.kode_jabatan FROM $tabel_master d where d.np_karyawan like 'D%' group by d.np_karyawan) c 
									where (c.np_karyawan!='' OR c.np_karyawan is not null) group by c.np_karyawan
									union all
									SELECT np_poh AS np_karyawan, nama_poh AS nama, kode_unit AS kode_unit, nama_unit, nama_unit AS nama_unit_singkat, nama_jabatan, tanggal_selesai AS tanggal_pensiun, null AS grup_jabatan, kode_jabatan FROM poh WHERE
									curdate()>=tanggal_mulai AND curdate()<=tanggal_selesai AND
									(
										substr(kode_unit,1,1) IN (".implode(",",$unit_kerja_dir).") 
									)")->result_array();
				
			}
			if(!empty($unit_kerja_div)){
				
				$data = $this->db->query("SELECT c.np_karyawan as no_pokok, c.nama, c.kode_unit, c.nama_unit, c.nama_unit_singkat, c.nama_jabatan, c.tanggal_pensiun, c.grup_jabatan, c.kode_jabatan FROM 
									(SELECT a.np_karyawan, a.nama, a.kode_unit, a.nama_unit, a.nama_unit_singkat, a.nama_jabatan, a.tanggal_pensiun, a.grup_jabatan, a.kode_jabatan FROM $tabel_master a $where
									GROUP BY a.np_karyawan
									union all
									SELECT b.np_karyawan, b.nama, b.kode_unit, b.nama_unit, b.nama_unit_singkat, b.nama_jabatan, b.tanggal_pensiun, b.grup_jabatan, b.kode_jabatan FROM $tabel_master_kemarin b $where
									GROUP BY b.np_karyawan
									union all
									SELECT d.np_karyawan, d.nama, d.kode_unit, d.nama_unit, d.nama_unit_singkat, d.nama_jabatan, d.tanggal_pensiun, d.grup_jabatan, d.kode_jabatan FROM $tabel_master d where d.np_karyawan like 'D%' group by d.np_karyawan) c 
									where (c.np_karyawan!='' OR c.np_karyawan is not null) group by c.np_karyawan
									union all
									SELECT np_poh AS np_karyawan, nama_poh AS nama, kode_unit AS kode_unit, nama_unit, nama_unit AS nama_unit_singkat, nama_jabatan, tanggal_selesai AS tanggal_pensiun, null AS grup_jabatan, kode_jabatan FROM poh WHERE
									curdate()>=tanggal_mulai AND curdate()<=tanggal_selesai AND
									(
										substr(kode_unit,1,2) IN (".implode(",",$unit_kerja_div).")
									)")->result_array();
						
			}
			if(!empty($unit_kerja_dep)){
			
				$data = $this->db->query("SELECT c.np_karyawan as no_pokok, c.nama, c.kode_unit, c.nama_unit, c.nama_unit_singkat, c.nama_jabatan, c.tanggal_pensiun, c.grup_jabatan, c.kode_jabatan FROM 
									(SELECT a.np_karyawan, a.nama, a.kode_unit, a.nama_unit, a.nama_unit_singkat, a.nama_jabatan, a.tanggal_pensiun, a.grup_jabatan, a.kode_jabatan FROM $tabel_master a $where
									GROUP BY a.np_karyawan
									union all
									SELECT b.np_karyawan, b.nama, b.kode_unit, b.nama_unit, b.nama_unit_singkat, b.nama_jabatan, b.tanggal_pensiun, b.grup_jabatan, b.kode_jabatan FROM $tabel_master_kemarin b $where
									GROUP BY b.np_karyawan
									union all
									SELECT d.np_karyawan, d.nama, d.kode_unit, d.nama_unit, d.nama_unit_singkat, d.nama_jabatan, d.tanggal_pensiun, d.grup_jabatan, d.kode_jabatan FROM $tabel_master d where d.np_karyawan like 'D%' group by d.np_karyawan) c 
									where (c.np_karyawan!='' OR c.np_karyawan is not null) group by c.np_karyawan
									union all
									SELECT np_poh AS np_karyawan, nama_poh AS nama, kode_unit AS kode_unit, nama_unit, nama_unit AS nama_unit_singkat, nama_jabatan, tanggal_selesai AS tanggal_pensiun, null AS grup_jabatan, kode_jabatan FROM poh WHERE
									curdate()>=tanggal_mulai AND curdate()<=tanggal_selesai AND
									(
										substr(kode_unit,1,3) IN (".implode(",",$unit_kerja_dep).")
									)")->result_array();
			
			}	
		
		return $data;
	}

	# minimal direktur
	function list_atasan_minimal_dir($arr_unit_kerja,$np_karyawan, $tanggal = null){
		$date_now			= date('Y-m-d');
		$date_kemarin 		= date('Y-m-d', strtotime($date_now . ' -1 months'));
		
		$pisah_date_now		= explode('-',$date_now);
		$tahun1				= $pisah_date_now[0];
		$bulan1				= $pisah_date_now[1];
		$tahun_bulan		= $tahun1."_".$bulan1;
		
		$pisah_date_kemarin	= explode('-',$date_kemarin);
		$tahun2				= $pisah_date_kemarin[0];
		$bulan2				= $pisah_date_kemarin[1];
		$tahun_bulan_kemarin= $tahun2."_".$bulan2;
		
		$tabel_master = 'erp_master_data_'.$tahun_bulan;
		if(!$this->check_table_exist($tabel_master)){
			$tabel_master = 'erp_master_data';
		}
		
		$tabel_master_kemarin = 'erp_master_data_'.$tahun_bulan_kemarin;
		if(!$this->check_table_exist($tabel_master_kemarin)){
			$tabel_master_kemarin = 'erp_master_data';
		}
		
		$where = "";
		
		if(!empty($np_karyawan)){
			$where = "np_karyawan NOT IN ('$np_karyawan')";
		}
		
		if(!empty($where)){
			$where .= " AND ";
		}
		
		$where .= "(";		
			// $where .= "substr(no_pokok,0,1) IN ('D')";
			$where .= "substr(np_karyawan,0,1) IN ('D')";
		$where .= ")";
		
		$unit_kerja_dir = array();
		$unit_kerja_div = array();
		$unit_kerja_dep = array();
				
		if(!empty($arr_unit_kerja)){
			
			foreach($arr_unit_kerja as $unit_kerja){
				
				//29 04 2020 | 7648 | Tri Wibowo, Pemisahan 0 untuk Check apakah kode unit divisi / departemen
				if(strcmp(substr($unit_kerja,1,1),"0")==0){
					$unit_kerja = substr($unit_kerja,0,3);
				}
				else{
					$unit_kerja = substr($unit_kerja,0,2);
				}			
				
				if(strlen($unit_kerja)==1){
					array_push($unit_kerja_div,$unit_kerja);
				}
				if(strlen($unit_kerja)==2){
					array_push($unit_kerja_div,$unit_kerja);
				}
				else if(strlen($unit_kerja)==3){
					if(strcmp(substr($unit_kerja,1,1),"0")==0){
						array_push($unit_kerja_dir,substr($unit_kerja,0,1));
					}
					else{
						array_push($unit_kerja_dep,$unit_kerja);
					}
				}
			}
			
			$where .= " AND ";

			$where_unit_kerja = "";
			if(!empty($unit_kerja_dir)){
				//2-12-2020 - 7648 Tri Wibowo menambah periode sehingga atasanya pas saat periode tersebut
				//$where_unit_kerja .= "substr(kode_unit,1,1) IN ('".implode(",",$unit_kerja_dir)."')";
				$where_unit_kerja .= "substr(kode_unit,1,1) IN (".implode(",",$unit_kerja_dir).")";
			}
			if(!empty($unit_kerja_div)){
				if(!empty($where_unit_kerja)){
					$where_unit_kerja .= " OR ";
				}
				//2-12-2020 - 7648 Tri Wibowo menambah periode sehingga atasanya pas saat periode tersebut
				//$where_unit_kerja .= "substr(kode_unit,1,2) IN ('".implode(",",$unit_kerja_div)."')";
				$where_unit_kerja .= "substr(kode_unit,1,2) IN (".implode(",",$unit_kerja_div).")";
			}
			if(!empty($unit_kerja_dep)){
				if(!empty($where_unit_kerja)){
					$where_unit_kerja .= " OR ";
				}
				//2-12-2020 - 7648 Tri Wibowo menambah periode sehingga atasanya pas saat periode tersebut
				//$where_unit_kerja .= "substr(kode_unit,1,3) IN ('".implode(",",$unit_kerja_dep)."')";
				$where_unit_kerja .= "substr(kode_unit,1,3) IN (".implode(",",$unit_kerja_dep).")";
			}

			//04 02 2022 Tri Wibowo 7648, Permintaan NDE terkait buka seluruh approval unit kerja
			//$where .= "($where_unit_kerja)";
			$where .= "(1=1)";
		}
		
		if(!empty($where))
		{
			$where = "WHERE ".$where;
		}
		
		/* All POH ditampilkan	
		$data = $this->db->query("SELECT c.np_karyawan as no_pokok, c.nama,c.kode_unit FROM 
									(SELECT a.np_karyawan, a.nama, a.kode_unit FROM $tabel_master a $where
									GROUP BY a.np_karyawan
									union all
									SELECT b.np_karyawan, b.nama, b.kode_unit FROM $tabel_master_kemarin b $where
									GROUP BY b.np_karyawan
									union all
									SELECT d.np_karyawan, d.nama, d.kode_unit FROM $tabel_master d where d.np_karyawan like 'D%' group by d.np_karyawan) c 
									where (c.np_karyawan!='' OR c.np_karyawan is not null) group by c.np_karyawan
									union all
									SELECT np_poh AS np_karyawan, nama_poh AS nama, kode_unit AS kode_unit FROM poh WHERE
									curdate()>=tanggal_mulai AND curdate()<=tanggal_selesai")->result_array();
		
		*/
		
		//filter POH unit kerja
		if(!empty($unit_kerja_dir)){
				
				$data = $this->db->query("SELECT c.np_karyawan as no_pokok, c.nama, c.kode_unit, c.nama_unit, c.nama_unit_singkat, c.nama_jabatan, c.tanggal_pensiun, c.grup_jabatan, c.kode_jabatan FROM 
									(SELECT a.np_karyawan, a.nama, a.kode_unit, a.nama_unit, a.nama_unit_singkat, a.nama_jabatan, a.tanggal_pensiun, a.grup_jabatan, a.kode_jabatan FROM $tabel_master a $where
									GROUP BY a.np_karyawan
									union all
									SELECT b.np_karyawan, b.nama, b.kode_unit, b.nama_unit, b.nama_unit_singkat, b.nama_jabatan, b.tanggal_pensiun, b.grup_jabatan, b.kode_jabatan FROM $tabel_master_kemarin b $where
									GROUP BY b.np_karyawan
									union all
									SELECT d.np_karyawan, d.nama, d.kode_unit, d.nama_unit, d.nama_unit_singkat, d.nama_jabatan, d.tanggal_pensiun, d.grup_jabatan, d.kode_jabatan FROM $tabel_master d where d.np_karyawan like 'D%' group by d.np_karyawan) c 
									where (c.np_karyawan!='' OR c.np_karyawan is not null) group by c.np_karyawan
									union all
									SELECT np_poh AS np_karyawan, nama_poh AS nama, kode_unit AS kode_unit, nama_unit, nama_unit AS nama_unit_singkat, nama_jabatan, tanggal_selesai AS tanggal_pensiun, null AS grup_jabatan, kode_jabatan FROM poh WHERE
									curdate()>=tanggal_mulai AND curdate()<=tanggal_selesai AND
									(
										substr(kode_unit,1,1) IN (".implode(",",$unit_kerja_dir).") 
									)")->result_array();
				
			}
			if(!empty($unit_kerja_div)){
				
				$data = $this->db->query("SELECT c.np_karyawan as no_pokok, c.nama, c.kode_unit, c.nama_unit, c.nama_unit_singkat, c.nama_jabatan, c.tanggal_pensiun, c.grup_jabatan, c.kode_jabatan FROM 
									(SELECT a.np_karyawan, a.nama, a.kode_unit, a.nama_unit, a.nama_unit_singkat, a.nama_jabatan, a.tanggal_pensiun, a.grup_jabatan, a.kode_jabatan FROM $tabel_master a $where
									GROUP BY a.np_karyawan
									union all
									SELECT b.np_karyawan, b.nama, b.kode_unit, b.nama_unit, b.nama_unit_singkat, b.nama_jabatan, b.tanggal_pensiun, b.grup_jabatan, b.kode_jabatan FROM $tabel_master_kemarin b $where
									GROUP BY b.np_karyawan
									union all
									SELECT d.np_karyawan, d.nama, d.kode_unit, d.nama_unit, d.nama_unit_singkat, d.nama_jabatan, d.tanggal_pensiun, d.grup_jabatan, d.kode_jabatan FROM $tabel_master d where d.np_karyawan like 'D%' group by d.np_karyawan) c 
									where (c.np_karyawan!='' OR c.np_karyawan is not null) group by c.np_karyawan
									union all
									SELECT np_poh AS np_karyawan, nama_poh AS nama, kode_unit AS kode_unit, nama_unit, nama_unit AS nama_unit_singkat, nama_jabatan, tanggal_selesai AS tanggal_pensiun, null AS grup_jabatan, kode_jabatan FROM poh WHERE
									curdate()>=tanggal_mulai AND curdate()<=tanggal_selesai AND
									(
										substr(kode_unit,1,2) IN (".implode(",",$unit_kerja_div).")
									)")->result_array();
						
			}
			if(!empty($unit_kerja_dep)){
			
				$data = $this->db->query("SELECT c.np_karyawan as no_pokok, c.nama, c.kode_unit, c.nama_unit, c.nama_unit_singkat, c.nama_jabatan, c.tanggal_pensiun, c.grup_jabatan, c.kode_jabatan FROM 
									(SELECT a.np_karyawan, a.nama, a.kode_unit, a.nama_unit, a.nama_unit_singkat, a.nama_jabatan, a.tanggal_pensiun, a.grup_jabatan, a.kode_jabatan FROM $tabel_master a $where
									GROUP BY a.np_karyawan
									union all
									SELECT b.np_karyawan, b.nama, b.kode_unit, b.nama_unit, b.nama_unit_singkat, b.nama_jabatan, b.tanggal_pensiun, b.grup_jabatan, b.kode_jabatan FROM $tabel_master_kemarin b $where
									GROUP BY b.np_karyawan
									union all
									SELECT d.np_karyawan, d.nama, d.kode_unit, d.nama_unit, d.nama_unit_singkat, d.nama_jabatan, d.tanggal_pensiun, d.grup_jabatan, d.kode_jabatan FROM $tabel_master d where d.np_karyawan like 'D%' group by d.np_karyawan) c 
									where (c.np_karyawan!='' OR c.np_karyawan is not null) group by c.np_karyawan
									union all
									SELECT np_poh AS np_karyawan, nama_poh AS nama, kode_unit AS kode_unit, nama_jabatan, tanggal_selesai AS tanggal_pensiun, null AS grup_jabatan, kode_jabatan FROM poh WHERE
									curdate()>=tanggal_mulai AND curdate()<=tanggal_selesai AND
									(
										substr(kode_unit,1,3) IN (".implode(",",$unit_kerja_dep).")
									)")->result_array();
			
			}	
		
		return $data;
	}

	// cari jabatan karyawan
	function get_jabatan_karyawan($np, $tanggal_dws){
		$table_fix = 'mst_karyawan';
		$bulan = date('Y_m', strtotime($tanggal_dws));
		$tabel_tahun_bulan = "erp_master_data_{$bulan}";

		$this->db->select('1');
		$this->db->from('information_schema.tables');
		$this->db->where('table_schema', $this->db->database);
		$this->db->where('table_name', $tabel_tahun_bulan);
		$cek_tabel_exist = $this->db->get();
		$tabel_exists = $cek_tabel_exist->num_rows() > 0;
		if($tabel_exists) $table_fix = $tabel_tahun_bulan;

		$return_data = $this->db
			->select('no_pokok, nama, kode_unit, grup_jabatan, kode_jabatan, nama_jabatan')
			->where('no_pokok',$np)
			->get('mst_karyawan')->row();

		if($table_fix=="erp_master_data_{$bulan}"){
			$this->db->select('np_karyawan AS no_pokok, nama, kode_unit, grup_jabatan, kode_jabatan, nama_jabatan');
			$this->db->where('np_karyawan',$np);
			$this->db->where('tanggal_dws',$tanggal_dws);
			$get = $this->db->get($table_fix);
			if($get->num_rows()>0) $return_data = $get->row();
		}
		return $return_data;
	}
	
	
}

/* End of file M_approval.php */
/* Location: ./application/models/M_approval.php */