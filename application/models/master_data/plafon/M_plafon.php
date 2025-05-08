<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_plafon extends CI_Model {
    
    public function __construct(){
		parent::__construct();
		$this->table_schema = $this->db->database;
	}
    
    function get_tabel_perizinan_from_schema(){
        return $this->db->select('TABLE_NAME')->where('TABLE_SCHEMA', $this->table_schema)->like('TABLE_NAME','ess_perizinan_','after')->group_by('TABLE_NAME')->order_by('TABLE_NAME','DESC')->get('information_schema.TABLES');
    }
    
    function get_mst_perizinan(){
        $not_in = ['AB','ATU','TK','TM'];
        return $this->db->select('nama, kode_pamlek, kode_erp')->where('status','1')->where_not_in('kode_pamlek',$not_in)->order_by('kode_pamlek')->get('mst_perizinan');
    }

    function get_mst_pos(){
        return $this->db->select('id, nama, kode_pos')->where('status','1')->order_by('kode_pos')->get('mst_pos');
    }
    
    function select_np_by_kode_unit($list_kode_unit){
		$this->db->select('*');
		$this->db->from('mst_karyawan');
		$this->db->where_in('kode_unit', $list_kode_unit);
		$this->db->order_by('no_pokok','ASC');
		$data = $this->db->get();
		
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
		}		
		
		$data = $this->db->get();
		
		return $data;
	}
	
	public function ambil_by_id($id, $table){
		$data = $this->db->from($table)
						 ->where("id",$id)
						 ->get()
						 ->row_array();
		return $data;
	}

	function nama_kelurahan($kode){
		$data = $this->db->where('kode_wilayah',$kode)->get('kelurahan')->row();
		return $data->nama;
	}

	function nama_kecamatan($kode){
		$data = $this->db->where('kode_wilayah',$kode)->get('kecamatan')->row();
		return $data->nama;
	}

	function nama_kabupaten($kode){
		$data = $this->db->where('kode_wilayah',$kode)->get('kabupaten')->row();
		return $data->nama;
	}

	function nama_provinsi($kode){
		$data = $this->db->where('kode_wilayah',$kode)->get('provinsi')->row();
		return $data->nama;
	}
}