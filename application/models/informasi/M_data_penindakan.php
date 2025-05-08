<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_data_penindakan extends CI_Model {

	var $table = "ess_penindakan";
	var $column_order = array(null); //set column field database for datatable_keterlambatan orderable	
	var $column_search = array("np_karyawan","nama","nama_unit","tahun"); //set column field database for datatable_pamlek 
	var $order = array("np_karyawan" => "ASC"); // default order 
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	private function _get_datatable_keterlambatan_query($set){
		if ($set['np_karyawan']!="")
			$this->db->where('np_karyawan', $set['np_karyawan']);
		if ($set['periode']!="")
			$this->db->where('tahun', $set['periode']);
		
		$this->db->select("*");
		$this->db->from($this->table);

		if($_POST['search']['value']!="") {
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
		}

		if(isset($_POST['order'])) { // here order processing
			$this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order)) {
			foreach($this->order as $order_key => $order_value){
				$this->db->order_by($order_key, $order_value);
			}
		}
	}

	public function get_datatable_keterlambatan($set){
		$this->_get_datatable_keterlambatan_query($set);
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function count_filtered($set){
		$this->_get_datatable_keterlambatan_query($set);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($set){
		if ($set['np_karyawan']!="")
			$this->db->where('np_karyawan', $set['np_karyawan']);
		if ($set['periode']!="")
			$this->db->where('tahun', $set['periode']);

		$this->db->from($this->table);
		$count_all = $this->db->count_all_results();

		return $count_all;
	}
	
	function export_data_keterlambatan($kode_unit,$np_karyawan,$periode){
		$this->_get_datatable_keterlambatan_query($kode_unit,$np_karyawan,$periode);
		$sql = $this->db->get_compiled_select();
		$sql = str_replace("::limit::","", $sql);
		
		// if ($keterangan=="all")
			$query = $this->db->query('select *, count(*) as jml from ('.$sql.') a where a.keterangan not like "Izin Datang Terlambat%" and a.jadwal!="OFF" group by np_karyawan ')->result_array();
		/*else if ($keterangan=="izin")
			$count_filtered = count($this->db->query('select *, count(*) as jml from ('.$sql.') a where a.keterangan="Izin Datang Terlambat|||" and a.jadwal!="OFF group by np_karyawan "')->result_array());
		else
			$count_filtered = count($this->db->query('select *, count(*) as jml from ('.$sql.') a where a.keterangan!="Izin Datang Terlambat|||" and a.jadwal!="OFF group by np_karyawan "')->result_array());*/

		return $query;
	}
	
	public function hitung_rekap_keterlambatan($value,$periode){
		$this->table_cico = "ess_cico_";
		$this->table_perizinan = "ess_perizinan_";

		// $kode_unit = rtrim($kode_unit,"0");
		$np_karyawan = $value[0];
		$first_day = str_replace("_","-",$periode)."-01";
		
		//dipindah kebawah karena tabel nya dinamis
		$column_search = array("a.np_karyawan","a.nama"); //set column field database for datatable_pamlek 
		
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

		$this->db->from($this->table_cico." a11");
		$this->db->where("a11.dws_name != 'OFF'");
		// $this->db->like("a11.kode_unit",$kode_unit,"after"); nonaktif
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
		// $this->db->like("c1.kode_unit",$kode_unit,"after"); nonaktif
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
		// $this->db->like("e1.kode_unit",$kode_unit,"after"); nonaktif
		if(!empty($np_karyawan)){
			$this->db->where("e1.np_karyawan",$np_karyawan);
		}
		$e = $this->db->get_compiled_select();
		
		$this->db->select("f1.id");
		$this->db->select("f1.perihal");
		$this->db->from($this->table_sppd." f1");
		$this->db->where("f1.tgl_berangkat <= last_day('$first_day')");
		$this->db->where("f1.tgl_pulang >= '$first_day'");
		// $this->db->like("f1.kode_unit",$kode_unit,"after"); nonaktif
		if(!empty($np_karyawan)){
			$this->db->where("f1.np_karyawan",$np_karyawan);
		}
		$f = $this->db->get_compiled_select();
		
		$this->db->select("a.np_karyawan");
		$this->db->select("a.nama");
		$this->db->select("a.nama_unit");
		// $this->db->select("count(a.np_karyawan) as jml");
		/*$this->db->select("b.description jadwal");
		$this->db->select("date_format(a.jadwal_masuk,'%Y-%m-%d %H:%i') jadwal_masuk");
		$this->db->select("date_format(a.datang,'%Y-%m-%d %H:%i') datang");
		$this->db->select("CONCAT(GROUP_CONCAT(IFNULL(d.nama,'') ORDER BY c.id SEPARATOR '|'), '|', IFNULL(e.uraian,''), '|', IFNULL(f.perihal,''), '|', CASE WHEN date_format(a.tapping_fix_1,'%Y-%m-%d %H:%i')!=date_format(a.tapping_time_1,'%Y-%m-%d %H:%i') THEN CONCAT('koreksi kehadiran, semula ',IFNULL(a.tapping_time_1,'')) ELSE '' END) keterangan", false);*/
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

		// $this->db->group_by("a.np_karyawan");
		// $this->db->group_by("year(a.tanggal)");

		$this->db->group_by("a.np_karyawan");
		$this->db->group_by("a.tanggal");
		
		$sql = $this->db->get_compiled_select();
		$sql = str_replace("::limit::","", $sql);
		// echo $sql.'<br><br>';
		$query = $this->db->query('select *, count(*) as jml from ('.$sql.') a where a.keterangan! like "Izin Datang Terlambat%" group by np_karyawan');
		return $query->result_array();
	}
}

/* End of file m_data_keterlambatan.php */
/* Location: ./application/models/informasi/m_data_keterlambatan.php */