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
	
	public function get_all($kode_unit=null,$np_karyawan,$periode,$keterangan=null,$exp=null){
		$this->table_cico = "ess_cico_";
		$this->table_perizinan = "ess_perizinan_";

		$kode_unit = rtrim($kode_unit,"0");
		$first_day = str_replace("_","-",$periode)."-01";
		
		
		if(!preg_match("/$periode$/",$this->table_cico)){
			$this->table_cico .= $periode;
		}
		if(!preg_match("/$periode$/",$this->table_perizinan)){
			$this->table_perizinan .= $periode;
			$is_table_exist = $this->db->query("SELECT * FROM information_schema.tables WHERE table_schema = 'ess' AND table_name = '".$this->table_perizinan."' LIMIT 1");
			if ($is_table_exist->num_rows()==0) {
				$this->table_perizinan = 'ess_perizinan';
			}
		}

		$this->db->select("a11.np_karyawan");
		$this->db->select("a11.nama");
		$this->db->select("a11.nama_unit");
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

		if ($kode_unit!=null)
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
		
		if ($kode_unit!=null)
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
		
		if ($kode_unit!=null)
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

		if ($kode_unit!=null)
			$this->db->like("f1.kode_unit",$kode_unit,"after");

		if(!empty($np_karyawan)){
			$this->db->where("f1.np_karyawan",$np_karyawan);
		}
		$f = $this->db->get_compiled_select();
		
		$this->db->select("a.np_karyawan");
		$this->db->select("a.nama");
		$this->db->select("a.nama_unit");
		$this->db->select("a.tapping_fix_1");
		$this->db->select("a.tapping_time_1");
		$this->db->select("a.tapping_terminal_1");
		$this->db->select("a.tanggal");
		$this->db->select("b.description jadwal");
		$this->db->select("date_format(a.jadwal_masuk,'%Y-%m-%d %H:%i') jadwal_masuk");
		$this->db->select("date_format(a.datang,'%Y-%m-%d %H:%i') datang");
		$this->db->select("CONCAT(GROUP_CONCAT(IFNULL(d.nama,'') ORDER BY c.id SEPARATOR '|'), '|', IFNULL(e.uraian,''), '|', IFNULL(f.perihal,''), '|', CASE WHEN date_format(a.tapping_fix_1,'%Y-%m-%d %H:%i')!=date_format(a.tapping_time_1,'%Y-%m-%d %H:%i') THEN CONCAT('koreksi kehadiran, semula ',IFNULL(a.tapping_time_1,'')) ELSE '' END) keterangan", false);
		$this->db->from("($a) a");
		$this->db->join($this->table_jadwal_kerja." b","a.jadwal=b.dws AND b.dws_variant=''","LEFT");
		$this->db->join("($c) c","FIND_IN_SET(c.id,a.id_perizinan)>0 AND a.datang=c.waktu_izin","LEFT");
		$this->db->join($this->table_mst_perizinan." d","d.kode_pamlek=c.kode_pamlek","LEFT");
		$this->db->join("($e) e","a.id_cuti=e.id","LEFT");
		$this->db->join("($f) f","a.id_sppd=f.id","LEFT");
		$this->db->group_by("a.np_karyawan");
		$this->db->group_by("a.tanggal");

		if($exp=='export') {
			foreach($this->order as $order_key => $order_value){
				$this->db->order_by($order_key, $order_value);
			}
		} else {
			if(isset($_POST['order'])) { // here order processing
				$this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
			} 
			else if(isset($this->order)) {
				foreach($this->order as $order_key => $order_value){
					$this->db->order_by($order_key, $order_value);
				}
			}
		}
		$sql = $this->db->get_compiled_select();
		$sql = str_replace("::limit::","", $sql);
		// echo $sql;exit;
		if ($keterangan=="all")
			$query = $this->db->query('select * from ('.$sql.') a where a.jadwal!="OFF"');//echo __LINE__;var_dump($query);
		else if ($keterangan=="izin")
			$query = $this->db->query('select * from ('.$sql.') a where a.keterangan like "Izin Datang Terlambat%" and a.jadwal!="OFF"');
		else
			$query = $this->db->query('select * from ('.$sql.') a where a.keterangan not like "Izin Datang Terlambat%" and a.jadwal!="OFF"');
		// echo $this->db->last_query().'<br>';
		return $query->result();
	}
}

/* End of file m_data_keterlambatan.php */
/* Location: ./application/models/informasi/m_data_keterlambatan.php */