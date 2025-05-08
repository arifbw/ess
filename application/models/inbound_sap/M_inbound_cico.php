<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_inbound_cico extends CI_Model {
    
    public function __construct(){
		parent::__construct();
		$this->table_schema = $this->db->database;
	}
	
	
	public function is_cuti_bersama($tanggal)
	{
		$this->db->select('tanggal');
		$this->db->from('mst_cuti_bersama');
		$this->db->where('tanggal', $tanggal);
		
		$query 	= $this->db->get();		
		$data	= $query->row_array();
		
		if($data['tanggal'])
		{
			return true;
		}else
		{
			return false;
		}
		
	}
	
	public function ambil_tidak_hadir_awal_bulan($np_karyawan,$tanggal)
	{		
				
		$tahun_bulan = substr($tanggal,0,7);
		
		$tahun_bulan = str_replace("-","_",$tahun_bulan);
		
		$tabel_cico = "ess_cico_".$tahun_bulan;
		
		if(!$this->check_table_exist($tabel_cico))
		{
			$tabel_cico = 'ess_cico';
		}
		
		$ambil = $this->db->query("
										SELECT 
											* 
										FROM 
											$tabel_cico
										WHERE
											np_karyawan = '$np_karyawan' AND						
											dws_tanggal = 	(
																SELECT 
																	min(dws_tanggal) 
																FROM 
																	$tabel_cico 
																WHERE 
																	np_karyawan = '$np_karyawan' AND
																	dws_name_fix!='OFF' OR (dws_name!='OFF' AND (dws_name_fix='' OR dws_name_fix is null))
															)
										")->row_array();

		return $ambil;
	}
	
	public function ambil_tidak_hadir_ke_sebelum($np_karyawan,$tanggal)
	{		
		$tanggal_sebelum =  date('Y-m-d', strtotime(date($tanggal) . '- 1 month'));
		
		$tahun_bulan = substr($tanggal_sebelum,0,7);
		
		$tahun_bulan = str_replace("-","_",$tahun_bulan);
		
		$tabel_cico = "ess_cico_".$tahun_bulan;
		
		if(!$this->check_table_exist($tabel_cico))
		{
			$tabel_cico = 'ess_cico';
		}
		
		$ambil = $this->db->query("
										SELECT 
											* 
										FROM 
											$tabel_cico
										WHERE
											np_karyawan = '$np_karyawan' AND						
											dws_tanggal = 	(
																SELECT 
																	max(dws_tanggal) 
																FROM 
																	$tabel_cico 
																WHERE 
																	np_karyawan = '$np_karyawan' AND
																	dws_name_fix!='OFF' OR (dws_name!='OFF' AND (dws_name_fix='' OR dws_name_fix is null))
															)
										")->row_array();

		return $ambil;
	}
	
	public function update_tidak_hadir_ke($np_karyawan,$dws_tanggal,$tidak_hadir_ke,$tidak_hadir_tanggal_awal,$tabel_cico)
	{
		if(!$this->check_table_exist($tabel_cico))
		{
			$tabel_cico = 'ess_cico';
		}
		
		$data = array(
               'tidak_hadir_ke' 			=> $tidak_hadir_ke,
               'tidak_hadir_tanggal_awal' 	=> $tidak_hadir_tanggal_awal
            );

		$this->db->where('np_karyawan', $np_karyawan);
		$this->db->where('dws_tanggal', $dws_tanggal);
		
		$this->db->update($tabel_cico, $data); 
	}
	
	public function insert_absence_tidak_hadir($tabel, $data)
	{
		$this->db->insert($tabel,$data);
		return $this->db->insert_id();
	}
	
	
	public function get_data_cico($tabel, $np)
	{
		$this->db->select('*');
		$this->db->from($tabel);		
		//generate semua
		if (strcmp($np,"all")!=0) {
			$this->db->where('np_karyawan', $np);
		}
		
		$this->db->where('tm_status!=', '9');
		$this->db->where('action!=', 'ZI');
		$this->db->where('action!=', 'ZL');
		$this->db->where("(action != 'ZN' OR (action = 'ZN' AND tm_status = '1'))"); //MPP yang tidak dihibahkan
		
		$this->db->order_by("np_karyawan", "ASC"); 	
		$this->db->order_by("dws_tanggal", "ASC"); 	
		// $this->db->limit(5,5); 	
		
		$query = $this->db->get();
		return $query;
	}
	
	public function inbound_setting($name)
	{
		$this->db->select('*');
		$this->db->from('inbound_setting');
		$this->db->where('name',$name);
		
		$query 	= $this->db->get();
		
		return $query->row_array();
	}
	
	public function check_absence($data, $tabel)
	{
		$cek = $this->db->where("info_type in ('2001', '2010')")->where("absence_type in ('4000', '7020', '7021')")->where($data)->get($tabel)->num_rows();
		if ($cek > 0) {
			return false;
		}else{
			return true;			
		}
	}
	
	public function cek_libur($dws_tanggal)
	{
		$cek = $this->db->where('tanggal', $dws_tanggal)->get('mst_hari_libur')->num_rows();
		if ($cek > 0) {
			return false;
		}else{
			return true;			
		}
	}
	
	public function update_overtime($id_overtime)
	{
		return $this->db->set('is_manual_by_sdm', '1')->where('id', $id_overtime)->update('ess_lembur_transaksi');
	}
	
	public function get_id_perizinan($id, $tabel)
	{
		return $this->db->where('id', $id)->get($tabel)->row()->id_perizinan;
	}
	
	public function update_id_perizinan($id_perizinan, $id, $tabel)
	{
		return $this->db->set('id_perizinan', $id_perizinan)->where('id', $id)->update($tabel);
	}
	
	public function insert_inbound($tabel, $data)
	{
		$this->db->insert($tabel,$data);
		return $this->db->insert_id();
	}
	
	public function update_inbound($tabel, $data, $where)
	{
		return $this->db->where($where)->update($tabel,$data);
	}
	
	public function delete_inbound($tabel, $where)
	{
		$id = $this->db->where("info_type in ('2001', '2010')")->where("absence_type in ('4000', '7020', '7021')")->where($where)->get($tabel)->row()->id;
		$this->db->where("info_type in ('2001', '2010')")->where("absence_type in ('4000', '7020', '7021')")->where($where)->delete($tabel);
		return $id;
	}
	
	public function insert_inbound_cico($tabel, $data)
	{
		return  $this->db->insert($tabel,$data);	
	}
	
	public function select_inbound_cico_unproses($tabel)
	{
		$this->db->select('*');
		$this->db->from($tabel);
		//generate semua
		//$this->db->where('proses','0');
		$this->db->order_by("np_karyawan", "ASC"); 	
		$this->db->order_by("date", "ASC"); 
		$this->db->order_by("time_type", "ASC"); 
		
		$query = $this->db->get();
		return $query;	
	}
	
	public function complete_data_cico($tabel_cico, $np_karyawan,$dws_tanggal)
	{			
		$data = array(									
					'proses'			=> '1',	
					'waktu_proses'		=>  date("Y-m-d H:i:s")
				);	
				
		$this->db->where('np_karyawan', $np_karyawan);
		$this->db->where('dws_tanggal', $dws_tanggal);
		
		$this->db->update($tabel_cico, $data);		
	}
	
	function check_table_exist($name)
	{
		$query = $this->db->query("show tables like '$name'")->row_array();
		
		return $query;
	}
	
	function create_table_inbound_cico($name)
	{	
		$this->db->query("CREATE TABLE $name AS SELECT * FROM inbound_cico");
	}
	
	function create_table_inbound_perizinan($name)
	{	
		$this->db->query("CREATE TABLE $name AS SELECT * FROM ess_perizinan");
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

	function get_dws_time($jenis, $np, $tgl)
	{
		$bulan = date('Y_m', strtotime($tgl));
		$table = $this->db->query('SELECT count(*) as htg FROM information_schema.tables WHERE table_schema = "'.$this->table_schema.'" AND table_name = "ess_cico_'.$bulan.'"')->row_array();
		if($table['htg'] == 1) {
			$table_name = "ess_cico_".$bulan;
		}
		else {
			$table_name = "ess_cico";
		}

		$this->db->where(array('np_karyawan'=>$np,'tanggal_dws'=>$tgl));
		if ($jenis == 'in') 
			$this->db->select('(case when dws_out_fix is null then dws_out else dws_out_fix end) as dws_time');
		else if ($jenis == 'out')
			$this->db->select('case when dws_out_fix is null then dws_out else dws_out_fix end) as dws_time');
		
		$get_dws = $this->db->get($table_name)->row();
		return $get_dws;
	}

	function check_absence_ijin($np, $tgl, $in, $out)
	{
		$bulan = date('Y_m', strtotime($tgl));
		$table = $this->db->query('SELECT count(*) as htg FROM information_schema.tables WHERE table_schema = "'.$this->table_schema.'" AND table_name = "ess_perizinan_'.$bulan.'"')->row_array();
		if($table['htg'] == 1) {
			$table_name = "ess_perizinan_".$bulan;
		}
		else {
			$table_name = "ess_perizinan";
		}

		$start_dws = $tgl.' '.$in;
		$end_dws = $tgl.' '.$out;
		$this->db->where(array('np_karyawan'=>$np,'tanggal_dws'=>$tgl));
		$this->db->where('absence_type="5010" or absence_type="5020" or absence_type="5030" or absence_type="5040" or absence_type="5050"');
		$this->db->where('("'.$start_dws.'" between concat(start_date," ",start_time) and concat(end_date," ",end_time))');
		$this->db->where('("'.$end_dws.'" between concat(start_date," ",start_time) and concat(end_date," ",end_time))');
		$this->db->where('start_time!="" and start_time is not null and start_time!="" and start_time is not null and end_time!="" and end_time is not null');
		$get_dws = $this->db->get($table_name);
		if($get_dws->num_rows() > 0) {
			return $get_dws->row_array();
		} else {
			return false;
		}
	}

	function check_absence_ijin_without_time($np, $tgl, $in, $out)
	{
		$bulan = date('Y_m', strtotime($tgl));
		$table = $this->db->query('SELECT count(*) as htg FROM information_schema.tables WHERE table_schema = "'.$this->table_schema.'" AND table_name = "ess_perizinan_'.$bulan.'"')->row_array();
		if($table['htg'] == 1) {
			$table_name = "ess_perizinan_".$bulan;
		}
		else {
			$table_name = "ess_perizinan";
		}

		$start_dws = $tgl.' '.$in;
		$end_dws = $tgl.' '.$out;
		$this->db->where(array('np_karyawan'=>$np,'tanggal_dws'=>$tgl));
		$this->db->where('absence_type="5010" or absence_type="5020" or absence_type="5030" or absence_type="5040" or absence_type="5050"');
		$this->db->where('("'.$start_dws.'" between concat(start_date," ",start_time) and concat(end_date," ",end_time))');
		$this->db->where('(("'.$end_dws.'" between concat(start_date," ",start_time) and concat(end_date," ",end_time))');
		$this->db->where('(((start_time!="" or start_time is not null) and (end_time="" or end_time is not null)) or ((end_time!="" or end_time is not null) and (start_time="" or start_time is not null)))');
		$get_dws = $this->db->get($table_name);
		if($get_dws->num_rows() > 0) {
			return $get_dws->row_array();
		}
		else {
			return false;
		}
	}
	
	function check_lembur_rate_selanjutnya($np,$date,$time_out)
	{
		$ambil_user = $this->db->query("SELECT
											*
										FROM
											ess_lembur_transaksi
										WHERE
											no_pokok = '$np'
										AND waktu_mulai_fix >= CONCAT(
											'$date',
											' ',
											'00:00:00'
										)
										AND waktu_mulai_fix <= CONCAT(
											'$date',
											' ',
											'$time_out'
										)
		")->row_array();
		
		if($ambil_user['id'])
		{
			return $ambil_user['id'];
		}else
		{
			return false;
		}
		
	}
	
	public function select_cico($tahun_bulan,$tanggal_dws,$np_karyawan)
	{
		$tahun_bulan=str_replace("-","_",$tahun_bulan);
		$nama_tabel = 'ess_cico_'.$tahun_bulan;
		
		if(!$this->check_table_exist($nama_tabel))
		{
			$nama_tabel = 'ess_cico';
		}
		
		$this->db->select('*');
		$this->db->from($nama_tabel);
		$this->db->where('dws_tanggal',$tanggal_dws);
		
		$this->db->where('np_karyawan',$np_karyawan);
				
		$this->db->order_by("np_karyawan", "ASC"); 	
		
		$query = $this->db->get();
		return $query;
	}

	function check_kehadiran_cuti($np, $tgl, $in, $out)
	{
		// $bulan = date('Y_m', strtotime($tgl));
		// $table = $this->db->query('SELECT count(*) as htg FROM information_schema.tables WHERE table_schema = "'.$this->table_schema.'" AND table_name = "ess_perizinan_'.$bulan.'"')->row_array();
		// if($table['htg'] == 1) {
		// 	$table_name = "ess_perizinan_".$bulan;
		// }
		// else {
		// 	$table_name = "ess_perizinan";
		// }

		// $start_dws = $tgl.' '.$in;
		// $end_dws = $tgl.' '.$out;
		// $this->db->where(array('np_karyawan'=>$np,'tanggal_dws'=>$tgl);
		// $this->db->where('absence_type="1000"'));
		// $this->db->where('((start_time="" or start_time is null) and (end_time="" or end_time is null))');
		// $get_dws = $this->db->get($table_name);
		// if($get_dws->num_rows() > 0) {
		// 	return $get_dws->row_array();
		// }
		// else {
		// 	return false;
		// }
	}

}

?>
