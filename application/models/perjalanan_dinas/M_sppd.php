<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_sppd extends CI_Model {
	
	public function __construct(){
		parent::__construct();
		$this->table_schema = $this->db->database;
		//$ci =& get_instance();
		// $this->load->helper('tabel_helper');
		//Do your magic here
	}
		
	public function get_sppd_bulan($tahun_bulan) {
	
		$tabel = "ess_sppd";
		
		$this->db->select("*");
		$this->db->from($tabel);	
		
		$this->db->where("DATE_FORMAT(tgl_berangkat,'%Y-%m')", $tahun_bulan);
		
		$this->db->order_by('np_karyawan','ASC');		
		$data = $this->db->order_by("$tabel.tgl_berangkat",'ASC')->get()->result();
		return $data;
	}

	public function get_sppd_bulan_new($tahun_bulan, $np, $jenis_perjalanan) {
	
		$tabel = "ess_sppd";
		
		$this->db->select("*");
		$this->db->from($tabel);	
		
		if($tahun_bulan != 0){
			$this->db->where("DATE_FORMAT(tgl_berangkat,'%Y-%m')", $tahun_bulan);
		}

		if($np != "-"){
			$this->db->where("np_karyawan", $np);
		}

		if($jenis_perjalanan != "-"){
			$this->db->where("catatan", $jenis_perjalanan);
		}
		
		$this->db->order_by('np_karyawan','ASC');		
		$data = $this->db->order_by("$tabel.tgl_berangkat",'ASC')->get()->result();
		return $data;
	}
	
	function insert_error($data)
	{
		return  $this->db->insert('ess_error',$data);		
	}
	
	function setting()
	{
		$this->db->select('*');
		$this->db->from('ess_sppd_setting');		
		$query = $this->db->get();
		
		return $query->row_array();
	}

	public function select_pamlek_files()
	{
		$this->db->select('*');
		$this->db->from('pamlek_files');
		$this->db->order_by("nama_file", "ASC"); 	
		
		$query = $this->db->get();
		return $query;
	}
	
	public function select_pamlek_files_limit($max)
	{
		$this->db->select('*');
		$this->db->from('ess_sppd_files');
		$this->db->where("proses","0");
		$this->db->limit($max);
		$this->db->order_by("nama_file", "ASC"); 	
		
		$query = $this->db->get();
		return $query;
	}	
	
	function insert_files($data)
	{
		return  $this->db->insert('pamlek_files',$data);	
	}
	
	function cek_id_then_insert_data($id, $data) {
        $cek = $this->db->where('id_sppd', $id)->get('ess_sppd')->num_rows();
        if($cek<1){
            $this->db->insert('ess_sppd',$data);
            $insert_id = $this->db->insert_id();
            return $insert_id;
        }
        else {
        	
			
			
			
			
			
        }
	}
	
	function insert_data_batch($data)
	{
		$this->db->insert_batch('ess_sppd',$data);
	}

	function update_files($nama_file,$data)
	{
		$this->db->where('nama_file', $nama_file);
		$this->db->update('ess_sppd_files', $data); 
	}
	
	function select_distinc_tapping_time_pamlek_data()
	{
		$this->db->distinct("tapping_time");
		$this->db->from('pamlek_data');
		
		$query = $this->db->get();
		return $query;		
	}
		
	function create_table_data($name)
	{	
		$this->db->query("CREATE TABLE $name AS SELECT * FROM pamlek_data");
	}
	
	function truncate_table($name)
	{
		$this->db->from($name); 
		$this->db->truncate();
	}
	
	function alter_table($name)
	{
		$this->db->query("ALTER TABLE $name MODIFY id INT AUTO_INCREMENT PRIMARY KEY");
	}
	
	function copy_isi($name,$tahun_bulan)
	{
		$this->db->query("INSERT INTO $name 
		(no_pokok_convert, no_pokok_original, no_pokok, tapping_time, in_out, machine_id, tapping_type, file) 
		SELECT no_pokok_convert, no_pokok_original, no_pokok, tapping_time, in_out, machine_id, tapping_type, file FROM pamlek_data 
		WHERE tapping_time like '$tahun_bulan%'");	
	}
	
	function check_table_exist($name)
	{
		$query = $this->db->query("show tables like '$name'")->row_array();
		
		return $query;
	}
	
	function insert_ess_tabel_cico($data)
	{
		return  $this->db->insert('ess_tabel_cico',$data);		
	}
	
	function update_ess_tabel_cico($nama_tabel, $data)
	{
		$this->db->where('nama_tabel', $nama_tabel);
		$this->db->update('ess_tabel_cico', $data);
	}
	
	public function check_ess_tabel_cico_exist($nama_tabel)
	{
		$this->db->select('*');
		$this->db->from('ess_tabel_cico');	
		$this->db->where('nama_tabel', $nama_tabel);
						
		$query 	= $this->db->get();
		
		return $query->row_array();
	}
	
	public function update_to_cico($id, $data)
	{
 		$period = new DatePeriod(
		    new DateTime($data['tgl_berangkat']),
		    new DateInterval('P1D'),
		    new DateTime(date('Y-m-d', strtotime('+1 day', strtotime($data['tgl_pulang']))))
		);
		foreach ($period as $value) {
			$bulan = $value->format('Y_m');
			$table = $this->db->query('SELECT count(*) as htg FROM information_schema.tables WHERE table_schema = "'.$this->table_schema.'" AND table_name = "ess_cico_'.$bulan.'"')->row_array();
			if($table['htg'] == 1) {
				$table_name = "ess_cico_".$bulan;
			}
			else {
				$table_name = "ess_cico";
			}

			$get_cico = $this->db->from($table_name)
					->where('np_karyawan', $data['np_karyawan'])
					->where('dws_tanggal = "'.$value->format('Y-m-d').'"')
					->get();
			if($get_cico->num_rows() > 0) {
				$cico = $get_cico->row_array();
				$val = count($cico['id_sppd']);
				$id_sppd_array = explode(',', $cico['id_sppd']);
				$id_sppd_array[$val] = strval($id);
				$id_sppd = implode(",", $id_sppd_array);
				$this->db->set('id_sppd', $id_sppd)->where('id', $cico['id'])->update($table_name);
			}
		}
		//return TRUE;
	}
	
	public function insert_to_cico($data)
	{
 		$get_sppd = $this->db->from('ess_sppd')
				->where('np_karyawan', $data['np_karyawan'])
				->where('"'.$data['tgl_dws'].'" between tgl_berangkat and tgl_pulang')
				->get();
				
		$bulan = date('Y_m', strtotime($data['tgl_dws']));
		$table = $this->db->query('SELECT count(*) as htg FROM information_schema.tables WHERE table_schema = "'.$this->table_schema.'" AND table_name = "ess_cico_'.$bulan.'"')->row_array();
		if($table['htg'] == 1) {
			$table_name = "ess_cico_".$bulan;
		}
		else {
			$table_name = "ess_cico";
		}
			
		if($get_sppd->num_rows() > 0) { //jika ada sppd
			$sppd = $get_sppd->result_array();
			
			
			$id_sppd = implode(",", array_column($sppd, 'id'));
			$this->db->set('id_sppd', $id_sppd)->where(array('dws_tanggal'=>$data['tgl_dws'], 'np_karyawan'=>$data['np_karyawan']))->update($table_name);
		}else //jika tidak ada sppd
		{
			$this->db->set('id_sppd', null)->where(array('dws_tanggal'=>$data['tgl_dws'], 'np_karyawan'=>$data['np_karyawan']))->update($table_name);
		}
		//return TRUE;
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

	function select_jenis_perjalanan(){
		$this->db->select('catatan');
		$this->db->from("ess_sppd");
		$this->db->group_by("catatan");
		$result = $this->db->get();

		return $result;
	}
	
	/*
	public function select_pamlek_data($tahun,$bulan)
	{
		$this->db->select('*');
		$this->db->from("pamlek_data_".$tahun."_".$bulan);
		
		$query = $this->db->get();
		return $query;
	}
	
	public function select_ess_tabel_cico_not_dump()
	{
		$this->db->select("nama_tabel");
		$this->db->from("ess_tabel_cico");
		$this->db->where("dump","0");
		
		$query = $this->db->get();
		return $query;
		
	}
	*/
}
