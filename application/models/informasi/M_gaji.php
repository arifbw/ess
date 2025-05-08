<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_gaji extends CI_Model {

	public function __construct(){
		parent::__construct();
		//Do your magic here
		
		$this->tabel_master_gaji = "mst_payslip";
		$this->tabel_gaji = "erp_payslip";
	}
	

	public function select_payment_by_np($id_payslip_karyawan)
	{
		$this->db->select("a.jenis");
		$this->db->select("a.nama_slip");
		$this->db->select("SUM(AES_DECRYPT(b.amount,md5(concat(b.payment_date,b.wage_type,b.parameter)))) amount",false);
		$this->db->from($this->tabel_master_gaji." a");
		$this->db->join($this->tabel_gaji." b", "a.kode = b.wage_type", 'left');
		$this->db->where('b.id_payslip_karyawan', $id_payslip_karyawan);
		$this->db->where("a.nama_slip != ", "");
		$this->db->group_by(array("a.jenis","a.nama_slip"));
		$return = $this->db->get()->result_array();
		//echo $this->db->last_query();
		return $return;
	}
	
	//7648 Tri Wibowo 22 04 2020, slip ditambah wfh
	public function select_payment_date_by_id_payslip($id_payslip_karyawan)
	{
		$this->db->select("a.payment_date");
		$this->db->from($this->tabel_gaji." a");
		$this->db->where('a.id_payslip_karyawan', $id_payslip_karyawan);
		$this->db->limit(1);
		$return = $this->db->get()->row_array();
	
		return $return;
	}
	
	public function select_wfh_by_date($np_karyawan,$date)
	{	

		$pisah	=explode("-",$date);
		$tahun	=$pisah[0];
		$bulan	=$pisah[1];
		$tanggal=$pisah[2];
		
		$tahun_bulan = $tahun."_".$bulan;				
		$nama_tabel = 'ess_cico_'.$tahun_bulan;
		
		if(!$this->check_table_exist($nama_tabel))
		{
			$nama_tabel = 'ess_cico';
		}
		
		$ambil_user = $this->db->query("SELECT 
											count(id) as jumlah
										FROM 
											$nama_tabel
										WHERE 
										`np_karyawan` = '" . $np_karyawan . "' AND
										wfh = '1'")->row_array();
		$ambil = $ambil_user['jumlah'];
	
		return $ambil;
	}
	
	function check_table_exist($name)
	{
		$name=str_replace("-","_",$name);
		$query = $this->db->query("show tables like '$name'")->row_array();
		
		return $query;
	}


}

/* End of file M_payslip.php */
/* Location: ./application/models/kehadiran/M_payslip.php */