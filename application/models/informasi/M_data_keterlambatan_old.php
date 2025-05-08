<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_data_keterlambatan extends CI_Model {

	var $table_cico = "ess_cico_";
	var $table_jadwal_kerja = "mst_jadwal_kerja";
	var $table_perizinan = "ess_perizinan_";
	var $table_cuti = "ess_cuti";
	var $table_sppd = "ess_sppd";
	var $table_mst_perizinan = "mst_perizinan";
	var $table_mst_cuti = "mst_cuti";
	var $column_order = array(null, ""); //set column field database for datatable_keterlambatan orderable	
	var $order = array("a.np_karyawan" => "ASC","a.tanggal" => "ASC"); // default order 
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	private function _get_datatable_keterlambatan_query($kode_unit,$np_karyawan,$periode){
		$kode_unit = rtrim($kode_unit,"0");
		$first_day = str_replace("_","-",$periode)."-01";
		
		//dipindah kebawah karena tabel nya dinamis
		$column_search = array("a.np_karyawan","a.nama"); //set column field database for datatable_pamlek 
		
		if(!preg_match("/$periode$/",$this->table_cico)){
			$this->table_cico .= $periode;
		}
		if(!preg_match("/$periode$/",$this->table_perizinan)){
			$this->table_perizinan .= $periode;
		}

		$this->db->select("a11.np_karyawan");
		$this->db->select("a11.nama");
		$this->db->select("a11.dws_tanggal tanggal");
		$this->db->select("IFNULL(a11.dws_name_fix,a11.dws_name) jadwal");
		$this->db->select("CONCAT(IFNULL(a11.dws_in_tanggal_fix, a11.dws_in_tanggal),' ',IFNULL(a11.dws_in_fix,a11.dws_in)) jadwal_masuk");
		$this->db->select("IFNULL(a11.tapping_fix_1, a11.tapping_time_1) datang");
		$this->db->select("a11.tapping_fix_1");
		$this->db->select("a11.tapping_time_1");
		$this->db->select("a11.tapping_terminal_1");
		$this->db->select("a11.id_perizinan");
		$this->db->select("a11.id_cuti");
		$this->db->select("a11.id_sppd");
		//$this->db->select("CASE WHEN a11.id_cuti!='' THEN 'cuti' WHEN a11.id_sppd IS NOT NULL THEN 'perjalanan dinas' END keterangan", false);
		$this->db->from($this->table_cico." a11");
		$this->db->where("a11.dws_name != 'OFF'");
		$this->db->like("a11.kode_unit",$kode_unit,"after");
		if(!empty($np_karyawan)){
			$this->db->where("a11.np_karyawan",$np_karyawan);
		}
		$a1 = $this->db->get_compiled_select();
		
		$this->db->select("*");
		$this->db->from("($a1) a1");
		$this->db->where("date_format(a1.datang,'%Y-%m-%d %H:%i') > date_format(a1.jadwal_masuk,'%Y-%m-%d %H:%i')");
		$this->db->or_where("date_format(a1.tapping_time_1,'%Y-%m-%d %H:%i') > date_format(a1.jadwal_masuk,'%Y-%m-%d %H:%i')");
		$a = $this->db->get_compiled_select();
		$a .= " ::limit::";
		
		$this->db->select("c1.id");
		$this->db->select("CONCAT(c1.end_date,' ',c1.end_time) waktu_izin");
		$this->db->select("c1.kode_pamlek");
		$this->db->from($this->table_perizinan." c1");
		$this->db->like("c1.kode_unit",$kode_unit,"after");
		if(!empty($np_karyawan)){
			$this->db->where("c1.np_karyawan",$np_karyawan);
		}
		$c = $this->db->get_compiled_select();
		
		$this->db->select("e1.id");
		$this->db->select("e2.uraian");
		$this->db->from($this->table_cuti." e1");
		$this->db->join($this->table_mst_cuti." e2","e1.absence_type=e2.kode_erp");
		$this->db->where("e1.start_date <= last_day('$first_day')");
		$this->db->where("e1.end_date >= '$first_day'");
		$this->db->like("e1.kode_unit",$kode_unit,"after");
		if(!empty($np_karyawan)){
			$this->db->where("e1.np_karyawan",$np_karyawan);
		}
		$e = $this->db->get_compiled_select();
		
		$this->db->select("f1.id");
		$this->db->select("f1.perihal");
		$this->db->from($this->table_sppd." f1");
		$this->db->where("f1.tgl_berangkat <= last_day('$first_day')");
		$this->db->where("f1.tgl_pulang >= '$first_day'");
		$this->db->like("f1.kode_unit",$kode_unit,"after");
		if(!empty($np_karyawan)){
			$this->db->where("f1.np_karyawan",$np_karyawan);
		}
		$f = $this->db->get_compiled_select();
		
		$this->db->select("a.np_karyawan");
		$this->db->select("a.nama");
		$this->db->select("a.tanggal");
		$this->db->select("b.description jadwal");
		$this->db->select("date_format(a.jadwal_masuk,'%Y-%m-%d %H:%i') jadwal_masuk");
		$this->db->select("date_format(a.datang,'%Y-%m-%d %H:%i') datang");
		$this->db->select("CONCAT(GROUP_CONCAT(IFNULL(d.nama,'') ORDER BY c.id SEPARATOR '|'), '|', IFNULL(e.uraian,''), '|', IFNULL(f.perihal,''), '|', CASE WHEN date_format(a.tapping_fix_1,'%Y-%m-%d %H:%i')!=date_format(a.tapping_time_1,'%Y-%m-%d %H:%i') THEN CONCAT('koreksi kehadiran, semula ',IFNULL(a.tapping_time_1,'')) ELSE '' END) keterangan", false);
		$this->db->from("($a) a");
		$this->db->join($this->table_jadwal_kerja." b","a.jadwal=b.dws AND b.dws_variant=''","LEFT");
		$this->db->join("($c) c","FIND_IN_SET(c.id,a.id_perizinan)>0 AND (a.datang=c.waktu_izin OR a.tapping_time_1=c.waktu_izin)","LEFT"); //diedit wina karena tidak detect perizinan 2020-10-06
		// $this->db->join("($c) c","FIND_IN_SET(c.id,a.id_perizinan)>0 AND a.datang=c.waktu_izin","LEFT");
		$this->db->join($this->table_mst_perizinan." d","d.kode_pamlek=c.kode_pamlek","LEFT");
		$this->db->join("($e) e","a.id_cuti=e.id","LEFT");
		$this->db->join("($f) f","a.id_sppd=f.id","LEFT");
		$this->db->group_by("a.np_karyawan");
		$this->db->group_by("a.tanggal");
				
		$i = 0;
	
		foreach ($column_search as $item) // loop column 
		{
			if($_POST['search']['value']) // if datatable_pamlek send POST for search
			{
				
				if($i===0) // first loop
				{
					$this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
					$this->db->like($item, $_POST['search']['value']);
				}
				else
				{
					$this->db->or_like($item, $_POST['search']['value']);
				}

				if(count($column_search) - 1 == $i) //last loop
					$this->db->group_end(); //close bracket
			}
			$i++;
		}

		if(isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order)){
			foreach($this->order as $order_key => $order_value){
				$this->db->order_by($order_key, $order_value);
			}
		}
	}

	function get_datatable_keterlambatan($kode_unit,$np_karyawan,$periode){
		$this->_get_datatable_keterlambatan_query($kode_unit,$np_karyawan,$periode);
		$sql = $this->db->get_compiled_select();
		if($_POST['length'] != -1){
			//$this->db->limit($_POST['length'], $_POST['start']);
			$sql = str_replace("::limit::","LIMIT ".$_POST['start'].", ".$_POST['length'], $sql);
		}
		else{
			$sql = str_replace("::limit::","", $sql);
		}
		$query = $this->db->query($sql);//echo __LINE__;var_dump($query);
		//echo $this->db->last_query();
		return $query->result();
	}

	function count_filtered($kode_unit,$np_karyawan,$periode){
		//$this->db->reset_query();
		$this->_get_datatable_keterlambatan_query($kode_unit,$np_karyawan,$periode);
		$sql = $this->db->get_compiled_select();
		$sql = str_replace("::limit::","", $sql);
		$count_filtered = count($this->db->query($sql)->result_array());

		return $count_filtered;
	}

	public function count_all($periode){
		if(!preg_match("/$periode$/",$this->table_perizinan)){
			$this->table_perizinan .= $periode;
		}
		
		$this->db->select("id");	
		$this->db->from($this->table_perizinan);

		$count_all = count($this->db->get()->result_array());
		return $count_all;
	}
	
	public function get_rekap_keterlambatan($kode_unit,$np_karyawan,$periode){
		$kode_unit = rtrim($kode_unit,"0");
		$first_day = str_replace("_","-",$periode)."-01";
		
		//dipindah kebawah karena tabel nya dinamis
		$column_search = array("a.np_karyawan","a.nama"); //set column field database for datatable_pamlek 
		
		if(!preg_match("/$periode$/",$this->table_cico)){
			$this->table_cico .= $periode;
		}
		if(!preg_match("/$periode$/",$this->table_perizinan)){
			$this->table_perizinan .= $periode;
		}

		$this->db->select("a11.np_karyawan");
		$this->db->select("a11.nama");
		$this->db->select("a11.dws_tanggal tanggal");
		$this->db->select("IFNULL(a11.dws_name_fix,a11.dws_name) jadwal");
		$this->db->select("CONCAT(IFNULL(a11.dws_in_tanggal_fix, a11.dws_in_tanggal),' ',IFNULL(a11.dws_in_fix,a11.dws_in)) jadwal_masuk");
		$this->db->select("IFNULL(a11.tapping_fix_1, a11.tapping_time_1) datang");
		$this->db->select("a11.tapping_fix_1");
		$this->db->select("a11.tapping_time_1");
		$this->db->select("a11.tapping_terminal_1");
		$this->db->select("a11.id_perizinan");
		$this->db->select("a11.id_cuti");
		$this->db->select("a11.id_sppd");

		$this->db->from($this->table_cico." a11");
		$this->db->where("a11.dws_name != 'OFF'");
		$this->db->like("a11.kode_unit",$kode_unit,"after");
		if(!empty($np_karyawan)){
			$this->db->where("a11.np_karyawan",$np_karyawan);
		}
		$a1 = $this->db->get_compiled_select();
		
		$this->db->select("*");
		$this->db->from("($a1) a1");
		$this->db->where("date_format(a1.datang,'%Y-%m-%d %H:%i') > date_format(a1.jadwal_masuk,'%Y-%m-%d %H:%i')");
		$this->db->or_where("date_format(a1.tapping_time_1,'%Y-%m-%d %H:%i') > date_format(a1.jadwal_masuk,'%Y-%m-%d %H:%i')");
		$a = $this->db->get_compiled_select();
		$a .= " ::limit::";
		
		$this->db->select("c1.id");
		$this->db->select("CONCAT(c1.end_date,' ',c1.end_time) waktu_izin");
		$this->db->select("c1.kode_pamlek");
		$this->db->from($this->table_perizinan." c1");
		$this->db->like("c1.kode_unit",$kode_unit,"after");
		if(!empty($np_karyawan)){
			$this->db->where("c1.np_karyawan",$np_karyawan);
		}
		$c = $this->db->get_compiled_select();
		
		$this->db->select("e1.id");
		$this->db->select("e2.uraian");
		$this->db->from($this->table_cuti." e1");
		$this->db->join($this->table_mst_cuti." e2","e1.absence_type=e2.kode_erp");
		$this->db->where("e1.start_date <= last_day('$first_day')");
		$this->db->where("e1.end_date >= '$first_day'");
		$this->db->like("e1.kode_unit",$kode_unit,"after");
		if(!empty($np_karyawan)){
			$this->db->where("e1.np_karyawan",$np_karyawan);
		}
		$e = $this->db->get_compiled_select();
		
		$this->db->select("f1.id");
		$this->db->select("f1.perihal");
		$this->db->from($this->table_sppd." f1");
		$this->db->where("f1.tgl_berangkat <= last_day('$first_day')");
		$this->db->where("f1.tgl_pulang >= '$first_day'");
		$this->db->like("f1.kode_unit",$kode_unit,"after");
		if(!empty($np_karyawan)){
			$this->db->where("f1.np_karyawan",$np_karyawan);
		}
		$f = $this->db->get_compiled_select();
		
		$this->db->select("a.np_karyawan");
		$this->db->select("a.nama");
		$this->db->select("count(a.*) as jml");
		$this->db->select("year(a.tanggal) as th");
		/*$this->db->select("b.description jadwal");
		$this->db->select("date_format(a.jadwal_masuk,'%Y-%m-%d %H:%i') jadwal_masuk");
		$this->db->select("date_format(a.datang,'%Y-%m-%d %H:%i') datang");
		$this->db->select("CONCAT(GROUP_CONCAT(IFNULL(d.nama,'') ORDER BY c.id SEPARATOR '|'), '|', IFNULL(e.uraian,''), '|', IFNULL(f.perihal,''), '|', CASE WHEN date_format(a.tapping_fix_1,'%Y-%m-%d %H:%i')!=date_format(a.tapping_time_1,'%Y-%m-%d %H:%i') THEN CONCAT('koreksi kehadiran, semula ',IFNULL(a.tapping_time_1,'')) ELSE '' END) keterangan", false);*/
		$this->db->from("($a) a");
		$this->db->join($this->table_jadwal_kerja." b","a.jadwal=b.dws AND b.dws_variant=''","LEFT");
		$this->db->join("($c) c","FIND_IN_SET(c.id,a.id_perizinan)>0 AND a.datang=c.waktu_izin","LEFT");
		$this->db->join($this->table_mst_perizinan." d","d.kode_pamlek=c.kode_pamlek","LEFT");
		$this->db->join("($e) e","a.id_cuti=e.id","LEFT");
		$this->db->join("($f) f","a.id_sppd=f.id","LEFT");

		$this->db->group_by("a.np_karyawan");
		$this->db->group_by("year(a.tanggal)");
		
		$sql = $this->db->get_compiled_select();
		$sql = str_replace("::limit::","", $sql);
		echo $sql;exit;
		$query = $this->db->query($sql);
		
		return $query->result();
	}
}

/* End of file m_data_keterlambatan.php */
/* Location: ./application/models/informasi/m_data_keterlambatan.php */