<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_pemesanan_makan_siang extends CI_Model {

	public function __construct(){
		parent::__construct();
		//Do your magic here
	}

	function get_kota($arr_prov=null){
        $this->db->select('a.kode_wilayah,a.nama as kota,b.nama as prov')
            ->from('kabupaten a')
            ->join('provinsi b','a.kode_prop=b.kode_wilayah','LEFT');
        if(@$arr_prov!=[]){
            $this->db->where_in('a.kode_prop',$arr_prov);
        }
        $this->db->order_by('b.kode_wilayah','ASC');
        return $this->db->get();
    }
    
    function get_nama_kota_by_kode($kode=null){
        $return='';
        if(@$kode){
            $get = $this->db->select('a.kode_wilayah,a.nama as kota,b.nama as prov')
                ->from('kabupaten a')
                ->join('provinsi b','a.kode_prop=b.kode_wilayah','LEFT')
                ->where('a.kode_wilayah',$kode)
                ->get();
            if($get->num_rows()==1){
                $row = $get->row();
                $return .= $row->kota.', '.str_replace('Prop. ','',$row->prov);
            }
        }
        return $return;
    }
	
	function check_table_exist($name)
	{
		$name=str_replace("-","_",$name);
		$query = $this->db->query("show tables like '$name'")->row_array();
		
		return $query;
	}
	
	function create_table_cico($name)
	{	
		$name=str_replace("-","_",$name);
		$this->db->query("CREATE TABLE $name like ess_cico");
		
	}
	
	function select_np_by_kode_unit($list_kode_unit)
	{
		$this->db->select('*');
		$this->db->from('mst_karyawan');
		$this->db->where_in('kode_unit', $list_kode_unit);
		$this->db->order_by('no_pokok','ASC');
		$data = $this->db->get();
		
		return $data;
	}
	
	function select_pemesanan_by_id($id)
	{
		$tabel = "ess_pemesanan_kendaraan";
		
		$this->db->select('*');
		$this->db->from($tabel);	
		$this->db->where('id',$id);
		
		$query = $this->db->get();
		
		return $query->row_array();
	}
	
	function insert_data_pemesanan($data_insert) {
			
		$this->db->insert('ess_pemesanan_kendaraan', $data_insert); 
		
		if($this->db->affected_rows() > 0) {
			return $this->db->insert_id();
		} else {
			return '0';
		}
	}
	
	function update_data_kehadiran($data_update)
	{
		$id 						= $data_update['id'];
		$tahun_bulan 				= $data_update['tahun_bulan'];
		$tapping_1	 				= $data_update['tapping_1'];
		$tapping_2	 				= $data_update['tapping_2'];
		$np_karyawan 				= $data_update['np_karyawan'];
		$nama		 				= $data_update['nama'];
		$dws_tanggal 				= $data_update['dws_tanggal'];
				
		$tapping_fix_approval_status		= $data_update['tapping_fix_approval_status'];
		$tapping_fix_approval_ket 	= $data_update['tapping_fix_approval_ket'];
		$tapping_fix_approval_np			= $data_update['tapping_fix_approval_np'];
		$tapping_fix_approval_nama			= $data_update['tapping_fix_approval_nama'];
		$tapping_fix_approval_nama_jabatan	= $data_update['tapping_fix_approval_nama_jabatan'];
		$tapping_fix_approval_date 			= $data_update['tapping_fix_approval_date'];
		
		//16 03 2020 - Tri Wibowo, WORK FROM HOME
		$wfh 			= $data_update['wfh'];
		$wfh_foto_1 	= $data_update['wfh_foto_1'];
		$wfh_foto_2		= $data_update['wfh_foto_2'];
		
		$tabel = "ess_cico_".$tahun_bulan;
		
		
		$data = array(               
				'tapping_fix_1_temp' 				=> $tapping_1, //masukan ke temp dulu sebelum di approve
				'tapping_fix_2_temp' 				=> $tapping_2, //masukan ke temp dulu sebelum di approve
				'tapping_fix_approval_status' 		=> $tapping_fix_approval_status,
				'tapping_fix_approval_ket' 			=> $tapping_fix_approval_ket,
				'tapping_fix_approval_np' 			=> $tapping_fix_approval_np,
				'tapping_fix_approval_nama' 		=> $tapping_fix_approval_nama,
				'tapping_fix_approval_nama_jabatan' => $tapping_fix_approval_nama_jabatan,
				'tapping_fix_approval_date'			=> $tapping_fix_approval_date,	
				'wfh'								=> $wfh, //16 03 2020 - Tri Wibowo, WORK FROM HOME
				'wfh_foto_1'						=> $wfh_foto_1, //16 03 2020 - Tri Wibowo, WORK FROM HOME
				'wfh_foto_2'						=> $wfh_foto_2, //16 03 2020 - Tri Wibowo, WORK FROM HOME
				'updated_at'	=> date('Y-m-d H:i:s'),
				'updated_by'	=> $this->session->userdata('no_pokok')
            );
	
		$this->db->where('id',$id);	
		$this->db->update($tabel, $data); 
		
		
		if($this->db->affected_rows() > 0)
		{			
			return $np_karyawan." | ".$nama.", Pada jadwal kerja tanggal ".$dws_tanggal; 
		}else
		{
			return '0';
		}
		
	}
	
	function update_kode_unit($data_update)
	{
		$id 					= $data_update['id'];
		$np_karyawan 			= $data_update['np_karyawan'];
		$nama 					= $data_update['nama'];
		$dws_tanggal 			= $data_update['dws_tanggal'];
		$kode_unit 				= $data_update['kode_unit'];
		$tahun_bulan 			= $data_update['tahun_bulan'];
		
						
		$tabel = "ess_cico_".$tahun_bulan;
		
		
		$ambil_kd = $this->db->query("SELECT * FROM mst_satuan_kerja WHERE kode_unit='$kode_unit'")->row_array();
		$nama_unit = $ambil_kd['nama_unit'];
	
		$data = array(               
				'kode_unit' 	=> $kode_unit, 
				'nama_unit' 	=> $nama_unit, 					
				'updated_at'	=> date('Y-m-d H:i:s'),
				'updated_by'	=> $this->session->userdata('no_pokok')
            );
	
		$this->db->where('id',$id);	
		$this->db->update($tabel, $data); 
		
		
		if($this->db->affected_rows() > 0)
		{			
			return $np_karyawan." | ".$nama.", Pada jadwal kerja tanggal ".$dws_tanggal; 
		}else
		{
			return '0';
		}
		
	}
	
	
	function select_mst_karyawan_aktif()
	{
		$this->db->select('*');
		$this->db->from('mst_jadwal_kerja');
		$this->db->where('status','1');
		
		$data = $this->db->get();
		
		return $data;
	}
	
	function select_mst_jadwal_kerja_by_id($id)
	{
			
		$this->db->select('*');
		$this->db->from('mst_jadwal_kerja');	
		$this->db->where('id',$id);
		
		$query = $this->db->get();
		
		return $query->row_array();
	}
	
	function check_cico_exist($data)
	{
		$np_karyawan = $data['np_karyawan'];
		$dws_tanggal = $data['dws_tanggal'];
		$tahun_bulan = $data['tahun_bulan'];
			
		$tabel = "ess_cico_".$tahun_bulan;
		
		if(!$this->check_table_exist($tabel))
		{
			$tabel = 'ess_cico';
		}

		
		$this->db->where('np_karyawan',$np_karyawan);
		$this->db->where('dws_tanggal',$dws_tanggal);
		
		$this->db->from($tabel);
		
		return $this->db->count_all_results();

	}
	
	function select_daftar_karyawan()
	{
		if($_SESSION["grup"]==4) //jika Pengadministrasi Unit Kerja
		{			
			$ada_data=0;
			$var=array();
			$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
			foreach ($list_pengadministrasi as $data) //looping list_pengadministrasi
			{	
				array_push($var,$data['kode_unit']);
				$ada_data=1;
			}
			if($ada_data==0)
			{
				$var='';
			}
			
		}else
		if($_SESSION["grup"]==5) //jika Pengguna
		{
			$var 	= $_SESSION["no_pokok"];
			
		}else
		{
			$var = '';				
		}	
			
		$this->db->select('*');
		$this->db->from('mst_karyawan');
		
		if($_SESSION["grup"]==4) //jika Pengadministrasi Unit Kerja
		{
			$this->db->where_in('mst_karyawan.kode_unit', $var);								
		}else
		if($_SESSION["grup"]==5) //jika Pengguna
		{
			$this->db->where_in('mst_karyawan.no_pokok', $var);	
		}else
		{
		}			
		
		$data = $this->db->get();
		
		return $data;
	}
	
	function select_daftar_unit()
	{
		if($_SESSION["grup"]==4) //jika Pengadministrasi Unit Kerja
		{			
			$ada_data=0;
			$var=array();
			$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
			foreach ($list_pengadministrasi as $data) //looping list_pengadministrasi
			{	
				array_push($var,$data['kode_unit']);
				$ada_data=1;
			}
			if($ada_data==0)
			{
				$var='';
			}
			
		}else
		if($_SESSION["grup"]==5) //jika Pengguna
		{
			$var 	= $_SESSION["no_pokok"];
			
		}else
		{
			$var = '';				
		}	
			
		$this->db->select('*');
		$this->db->from('mst_karyawan');
		
		if($_SESSION["grup"]==4) //jika Pengadministrasi Unit Kerja
		{
			$this->db->where_in('mst_karyawan.kode_unit', $var);								
		}else
		if($_SESSION["grup"]==5) //jika Pengguna
		{
			$this->db->where_in('mst_karyawan.no_pokok', $var);	
		}else
		{
		}			
		
		$this->db->group_by('kode_unit'); 

		
		$data = $this->db->get();
		
		return $data;
	}
	
	function select_cuti_by_id($id)
	{
		$this->db->select('a.*');
		$this->db->select('b.uraian');
		$this->db->from('ess_cuti a');	
		$this->db->join('mst_cuti b', 'a.absence_type = b.kode_erp', 'left');
		$this->db->where('a.id',$id);
		
		$query = $this->db->get();
		
		return $query->row_array();
	}
    
    function get_apv($arr_unit_kerja,$np_karyawan){//var_dump($arr_unit_kerja);var_dump($np_karyawan);
        $data=[];
		$date_now			= '2019-04-01'; //date('Y-m-d');
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
			/*$where .= "grup_jabatan IN ('SEKIV')"; //Permintaan Mba dini 23 Maret 2020
			$where .= " OR ";
			$where .= "grup_jabatan IN ('AHLIMUDA','AHLIMDYA','KASEK','KADEP')";
			$where .= " OR ";*/
			$where .= "substr(kode_jabatan,-3) IN ('500','400','300','200','100')";
		$where .= ")";
		
		$unit_kerja_dir = array();
		$unit_kerja_div = array();
		$unit_kerja_dep = array();
			
		if(!empty($arr_unit_kerja)){
			
			foreach($arr_unit_kerja as $unit_kerja){
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

			$where .= "($where_unit_kerja)";
		}
		
		if(!empty($where))
		{
			$where = "WHERE ".$where;
		}
		
		/* All POH ditampilkan	
		$data = $this->db->query("SELECT c.np_karyawan as no_pokok, c.nama FROM 
									(SELECT a.np_karyawan, a.nama FROM $tabel_master a $where
									GROUP BY a.np_karyawan
									union all
									SELECT b.np_karyawan, b.nama FROM $tabel_master_kemarin b $where
									GROUP BY b.np_karyawan
									union all
									SELECT d.np_karyawan, d.nama FROM $tabel_master d where d.np_karyawan like 'D%' group by d.np_karyawan) c 
									where (c.np_karyawan!='' OR c.np_karyawan is not null) group by c.np_karyawan
									union all
									SELECT np_poh AS np_karyawan, nama_poh AS nama FROM poh WHERE
									curdate()>=tanggal_mulai AND curdate()<=tanggal_selesai")->result_array();
		
		*/
		
		//filter POH unit kerja
		if(!empty($unit_kerja_dir)){
				
				$data = $this->db->query("SELECT c.np_karyawan as no_pokok, c.nama FROM 
									(SELECT a.np_karyawan, a.nama FROM $tabel_master a $where
									GROUP BY a.np_karyawan
									union all
									SELECT b.np_karyawan, b.nama FROM $tabel_master_kemarin b $where
									GROUP BY b.np_karyawan
									union all
									SELECT d.np_karyawan, d.nama FROM $tabel_master d where d.np_karyawan like 'D%' group by d.np_karyawan) c 
									where (c.np_karyawan!='' OR c.np_karyawan is not null) group by c.np_karyawan
									union all
									SELECT np_poh AS np_karyawan, nama_poh AS nama FROM poh WHERE
									curdate()>=tanggal_mulai AND curdate()<=tanggal_selesai AND
									(
										substr(kode_unit,1,1) IN (".implode(",",$unit_kerja_dir).") 
									)")->result_array();
				
			}
			if(!empty($unit_kerja_div)){
				
				$data = $this->db->query("SELECT c.np_karyawan as no_pokok, c.nama FROM 
									(SELECT a.np_karyawan, a.nama FROM $tabel_master a $where
									GROUP BY a.np_karyawan
									union all
									SELECT b.np_karyawan, b.nama FROM $tabel_master_kemarin b $where
									GROUP BY b.np_karyawan
									union all
									SELECT d.np_karyawan, d.nama FROM $tabel_master d where d.np_karyawan like 'D%' group by d.np_karyawan) c 
									where (c.np_karyawan!='' OR c.np_karyawan is not null) group by c.np_karyawan
									union all
									SELECT np_poh AS np_karyawan, nama_poh AS nama FROM poh WHERE
									curdate()>=tanggal_mulai AND curdate()<=tanggal_selesai AND
									(
										substr(kode_unit,1,2) IN (".implode(",",$unit_kerja_div).")
									)")->result_array();
						
			}
			if(!empty($unit_kerja_dep)){
			
				$data = $this->db->query("SELECT c.np_karyawan as no_pokok, c.nama FROM 
									(SELECT a.np_karyawan, a.nama FROM $tabel_master a $where
									GROUP BY a.np_karyawan
									union all
									SELECT b.np_karyawan, b.nama FROM $tabel_master_kemarin b $where
									GROUP BY b.np_karyawan
									union all
									SELECT d.np_karyawan, d.nama FROM $tabel_master d where d.np_karyawan like 'D%' group by d.np_karyawan) c 
									where (c.np_karyawan!='' OR c.np_karyawan is not null) group by c.np_karyawan
									union all
									SELECT np_poh AS np_karyawan, nama_poh AS nama FROM poh WHERE
									curdate()>=tanggal_mulai AND curdate()<=tanggal_selesai AND
									(
										substr(kode_unit,1,3) IN (".implode(",",$unit_kerja_dep).")
									)")->result_array();
			
			}	
		
		//echo $this->db->last_query();
		return $data;
	}
	
}

/* End of file M_data_kehadiran.php */
/* Location: ./application/models/konsumsi/M_pemesanan_makan_siang.php */