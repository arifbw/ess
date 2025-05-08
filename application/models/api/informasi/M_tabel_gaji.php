<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_tabel_gaji extends CI_Model {

	var $table_header = 'erp_payslip_header';
	var $table_karyawan = 'erp_payslip_karyawan';
	var $column_order = array(null, 'payment_date'); //set column field database for datatable orderable	
	var $order = array('payment_date' => 'desc'); // default order 
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	public function get_all($np)
	{		
		$this->db->select("b.nama_payslip");
		$this->db->select("b.id id_payslip_karyawan");
		$this->db->from($this->table_header." a");
		$this->db->join($this->table_karyawan." b","a.id=b.id_payslip_header");
		$this->db->where("b.np_karyawan", $np);
		$this->db->where("a.start_display <= ", "NOW()",false);
		$this->db->order_by("a.payment_date",'desc');
		
		return $this->db->get()->result();
	}
	
	
}

/* End of file m_payslip.php */
/* Location: ./application/models/kehadiran/m_payslip.php */