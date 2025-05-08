<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_skep extends CI_Model {

	var $table_skep = 'ess_skep';
	var $column_order = array(null, 'np_karyawan','nama_karyawan','nomor_skep','aktif_tanggal_skep'); //set column field database for datatable_skep orderable	
	var $order = array('aktif_tanggal_skep' => 'desc'); // default order 
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	public function get_all($np=0){
		//dipindah kebawah karena tabel nya dinamis
		$column_search = array('np_karyawan','nama_karyawan','nomor_skep','aktif_tanggal_skep'); //set column field database for datatable_skep 
		
		$date = date('Y-m-d');
				
		$this->db->select("*");
		$this->db->from($this->table_skep);
		$this->db->where("tanggal_tampil <= '$date'");
		
		if($np!='0')
			$this->db->where("np_karyawan", $np);
		
		$this->db->order_by('aktif_tanggal_skep','DESC');
		$query = $this->db->get();
		return $query->result();
	}
}

/* End of file m_skep.php */
/* Location: ./application/models/informasi/m_skep.php */