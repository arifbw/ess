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


}

/* End of file M_payslip.php */
/* Location: ./application/models/kehadiran/M_payslip.php */