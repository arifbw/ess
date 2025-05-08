<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_time_record_negatif extends CI_Model {

	public function __construct(){
		parent::__construct();
		//Do your magic here
		
	}
	
	public function bulan_periode(){
		$nama_db = $this->db->database;			
		$data = $this->db->query("SELECT table_name FROM information_schema.tables WHERE table_schema = '$nama_db' AND table_name like '%ess_cico_%' GROUP BY table_name ORDER BY table_name DESC;")->result_array();

		return $data;
	}
	
	public function daftar_karyawan_tm_negatif($tanggal_awal,$tanggal_akhir){
		$data = $this->db->select("a.np_karyawan")
						 ->select("a.nama")
						 ->select("a.tanggal_dws")
						 ->select("a.dws")
						 ->select("ifnull(b.wfh,'') wfh")
						 ->select("ifnull(b.id_cuti,'') id_cuti")
						 ->select("ifnull(b.id_sppd,'') id_sppd")
						 ->from("erp_master_data_".substr($tanggal_awal,0,4)."_".substr($tanggal_awal,5,2)." a")
						 ->join("ess_cico_".substr($tanggal_awal,0,4)."_".substr($tanggal_awal,5,2)." b","a.np_karyawan=b.np_karyawan AND a.tanggal_dws=b.dws_tanggal","left")
						 ->where("a.tanggal_dws >=",$tanggal_awal)
						 ->where("a.tanggal_dws <=",$tanggal_akhir)
						 ->where("a.tm_status","9")
						 ->like("a.kode_jabatan","00","before")
						 ->group_start()
							 ->where("a.kontrak_kerja","TPBW")
							 ->or_where("a.grup_jabatan","Kadiv")
						 ->group_end()
						 ->order_by("a.np_karyawan")
						 ->order_by("a.tanggal_dws")
						 ->get()
						 ->result_array();//echo $this->db->last_query();
		return $data;
	}
	
	public function update_wfh($input,$np_karyawan,$date)
	{
		$pisah	=explode("-",$date);
		$tahun	=$pisah[0];
		$bulan	=$pisah[1];
		$tanggal=$pisah[2];
		
		$tahun_bulan = $tahun."_".$bulan;				
		$tabel_cico = 'ess_cico_'.$tahun_bulan;
		
		if(!$this->check_table_exist($tabel_cico))
		{
			$tabel_cico = 'ess_cico';
		}
		
		$data = [
				'wfh' 			=> $input,
				'np_wfh_by_sdm' => $this->session->userdata('no_pokok'),
			];
		
		$this->db->where('dws_tanggal', $date);
		$this->db->where('np_karyawan', $np_karyawan);
		$this->db->update($tabel_cico, $data);	
	}
	
	function check_table_exist($name)
	{
		$name=str_replace("-","_",$name);
		$query = $this->db->query("show tables like '$name'")->row_array();
		
		return $query;
	}


}

/* End of file M_time_record_negatif.php */
/* Location: ./application/models/osdm/M_time_record_negatif.php */