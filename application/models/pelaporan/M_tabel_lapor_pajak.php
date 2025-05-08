<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_tabel_lapor_pajak extends CI_Model {
    
	var $column_order = array(null, 'np_karyawan', 'tahun','keterangan', null, null);
	var $column_search = array('np_karyawan','nama_karyawan','tahun','keterangan');
	var $order = array('a.np_karyawan', 'a.id');
	
	public function __construct(){
		parent::__construct();
	}
	
	private function _get_datatables_query($var=null, $get_tbl=null) {
        $this->db->select("*")->from("laporan_bukti_lapor_pajak a");
		$this->db->where('deleted_at is null');

		if($_SESSION["grup"]==4){
			if($var!=[]) $this->db->where_in('kode_unit', $var);								
			else $this->db->where_in('kode_unit', ['']);								
		} else if($_SESSION["grup"]==5)
			$this->db->where('np_karyawan', $var);

		$i = 0;
	
		foreach ($this->column_search as $item) // loop column 
		{
			if(@$_POST['search']['value']) // if datatable send POST for search
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

				if(count($this->column_search) - 1 == $i) //last loop
					$this->db->group_end(); //close bracket
			}
			$i++;
		}
		
		if(isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
	}

	function get_datatables($var=null, $get_tbl=null) {
		$this->_get_datatables_query($var, $get_tbl);
		
		if($_POST['length'] != -1)
		$this->db->limit(@$_POST['length'], @$_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered($var=null, $get_tbl=null) {
		$this->_get_datatables_query($var, $get_tbl);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($var=null, $get_tbl=null) {
        $this->_get_datatables_query($var, $get_tbl);
		return $this->db->count_all_results();
	}
	
	public function _get_excel($var, $table_name, $get, $jenis=null) {
		if ($get['np_karyawan'] != '') {
			$np = implode("','", $get['np_karyawan']);
			$this->db->where("np_karyawan in ('".$np."')");
		}

        $this->db->select("a.*, (CASE WHEN start_date is not null then start_date else end_date end) as ordere")->from("$table_name a");
        if(@$jenis!=null)
            $this->db->where_in("a.kode_pamlek", $jenis);
				
		if($_SESSION["grup"]==4) //jika Pengadministrasi Unit Kerja
			$this->db->where_in('a.kode_unit', $var);								
		else if($_SESSION["grup"]==5) //jika Pengguna
			$this->db->where_in('a.np_karyawan', $var);
        
		$this->db->order_by('(CASE WHEN a.start_date IS NOT NULL THEN a.start_date ELSE a.end_date END)', 'DESC');
		$this->db->order_by('(CASE WHEN a.start_time IS NOT NULL THEN a.start_time ELSE a.end_time END)', 'DESC');
        $this->db->order_by('a.np_karyawan', 'DESC');
        $data = $this->db->get()->result();
        return $data;
	}
}
