<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_tabel_data_perizinan extends CI_Model {
    
	//var $table = "pamlek_data_2018_11";
	var $column_order = array(null, 'a.np_karyawan', null, null, 'a.start_date', 'a.end_date', null); //set column field database for datatable orderable
	var $column_search = array('a.np_karyawan','a.nama'); //set column field database for datatable searchable 
	var $order = array('a.np_karyawan', 'a.ordere'); // default order 
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	private function _get_datatables_query($var, $table_name, $jenis=null) {
		$bulan = substr($table_name, -2);
		$tahun = substr($table_name, -7, 4);
        $this->db->select("a.*, (CASE WHEN b.start_date is not null then b.start_date else b.end_date end) as ordere, b.pos, b.approval_pengamanan_np, b.approval_pengamanan_posisi, b.start_date_input, b.end_date_input")->from("$table_name a");
        $this->db->join('ess_request_perizinan b', 'a.id=b.id_perizinan and ((b.start_date is null and month(b.end_date)="'.$bulan.'" and year(b.end_date)="'.$tahun.'") OR (b.start_date is not null and month(b.start_date)="'.$bulan.'" and year(b.start_date)="'.$tahun.'"))');
        //$this->db->join('mst_karyawan b', 'a.np_karyawan=b.no_pokok', 'left');
        if(@$jenis!=null){
            $this->db->where_in("a.kode_pamlek", $jenis);
        }
				
		if($_SESSION["grup"]==4) //jika Pengadministrasi Unit Kerja
		{
			$this->db->where_in('a.kode_unit', $var);								
		} else if($_SESSION["grup"]==5) //jika Pengguna
		{
			$this->db->where_in('a.np_karyawan', $var);	
		} else
		{
		}
        
		$this->db->order_by('(CASE WHEN b.start_date IS NOT NULL THEN b.start_date ELSE b.end_date END)', 'DESC');
		$this->db->order_by('(CASE WHEN b.start_time IS NOT NULL THEN b.start_time ELSE b.end_time END)', 'DESC');
        $this->db->order_by('a.np_karyawan', 'DESC');
		
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
		else if(isset($this->order))
		{
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	function get_datatables($var, $table_name, $jenis=null)
	{
		$this->_get_datatables_query($var, $table_name, @$jenis);
		
		if($_POST['length'] != -1)
		$this->db->limit(@$_POST['length'], @$_POST['start']);
		$query = $this->db->get();


		return $query->result();
	}

	function count_filtered($var, $table_name, $jenis=null)
	{
		$this->_get_datatables_query($var, $table_name, @$jenis);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($var, $table_name, $jenis=null)
	{
        $this->_get_datatables_query($var, $table_name, @$jenis);
		// $this->db->from($this->table);
		// $this->db->join('mst_karyawan', 'mst_karyawan.no_pokok = ess_sppd.id_user', 'left');
		
		// if($_SESSION["grup"]==4) //jika Pengadministrasi Unit Kerja
		// {
		// 	$this->db->where_in('mst_karyawan.kode_unit', $var);								
		// }else
		// if($_SESSION["grup"]==5) //jika Pengguna
		// {
		// 	$this->db->where_in('mst_karyawan.no_pokok', $var);	
		// }else
		// {
		// }	
			
		return $this->db->count_all_results();
	}
	
	public function _get_excel($var, $table_name, $get, $jenis = null) {
		// Extract year and month from the table name
		$bulan = substr($table_name, -2);
		$tahun = substr($table_name, -7, 4);
	
		// Initialize the base query
		$query_addon = '((b.start_date is null and month(b.end_date)="'.$bulan.'" and year(b.end_date)="'.$tahun.'") OR (b.start_date is not null and month(b.start_date)="'.$bulan.'" and year(b.start_date)="'.$tahun.'"))';
		$sql = "SELECT a.*, b.approval_pengamanan_np, b.approval_pengamanan_posisi, b.pos, b.alasan_batal, b.np_batal, b.date_batal, b.id_perizinan, (CASE WHEN a.start_date IS NOT NULL THEN a.start_date ELSE a.end_date END) as ordere
				FROM $table_name a
				JOIN ess_request_perizinan b ON b.id_perizinan = a.id and $query_addon";
	
		// Add conditions for np_karyawan if provided
		$where_clauses = [];
		if (!empty($get['np_karyawan'])) {
			$np = implode("','", $get['np_karyawan']);
			$where_clauses[] = "a.np_karyawan IN ('$np')";
		}
	
		// Add condition for kode_pamlek if provided
		if (!empty($jenis)) {
			$jenis_in = implode("','", $jenis);
			$where_clauses[] = "a.kode_pamlek IN ('$jenis_in')";
		}
	
		// Add group-specific conditions
		if ($_SESSION["grup"] == 4) { // Pengadministrasi Unit Kerja
			$var_in = implode("','", $var);
			$where_clauses[] = "a.kode_unit IN ('$var_in')";
		} else if ($_SESSION["grup"] == 5) { // Pengguna
			$var_in = implode("','", $var);
			$where_clauses[] = "a.np_karyawan IN ('$var_in')";
		}

		// Combine all where clauses
		if (!empty($where_clauses)) {
			$sql .= ' WHERE ' . implode(' AND ', $where_clauses);
		}
	
		// Add order by clauses
		$sql .= " ORDER BY (CASE WHEN a.start_date IS NOT NULL THEN a.start_date ELSE a.end_date END) DESC,
						 (CASE WHEN a.start_time IS NOT NULL THEN a.start_time ELSE a.end_time END) DESC,
						 a.np_karyawan DESC";

		// Execute the query
		$query = $this->db->query($sql);
		$data = $query->result();
		
		// Return the results
		return $data;
	}
	
	
}

/* End of file m_perencanaan_jadwal_kerja.php */
/* Location: ./application/models/kehadiran/m_perencanaan_jadwal_kerja.php */