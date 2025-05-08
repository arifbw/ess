<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_hasil_mcu extends CI_Model {

	var $table = 'ess_hasil_mcu';
	var $column_order = array(null,'no_reg','np_karyawan','nama_karyawan','departemen','usia','sex',null); //set column field database for datatable_skep orderable	
	var $order = array('created_at' => 'desc'); // default order 
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	private function _get_datatable_query($np=0,$tgl=0,$vendor=0,$tahun=0) {
		//dipindah kebawah karena tabel nya dinamis
		$column_search = array('tanggal_mcu','vendor','np_karyawan','nama_karyawan','departemen','no_reg'); //set column field database for datatable_skep 
				
		$this->db->select("*");
		$this->db->from($this->table);
		
		if($np!='0')
			$this->db->where("np_karyawan", $np);
		if($tgl='0')
			$this->db->where("tanggal_mcu", urldecode($tgl));
		if($vendor!='0')
			$this->db->where("vendor", urldecode($vendor));	
		if($tahun!='0')
			$this->db->where("tahun", $tahun);	
				
		$i = 0;
	
		foreach ($column_search as $item) // loop column 
		{
			if($_POST['search']['value']) // if datatable send POST for search
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

				if(count($column_search) - 1 == $i) //last loop
					$this->db->group_end(); //close bracket
			}
			$i++;
		}
		
		if(isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order)){
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	function get_datatable($np=0,$tgl=0,$vendor=0,$tahun=0){
		$this->_get_datatable_query($np,$tgl,$vendor,$tahun);
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered($np=0,$tgl=0,$vendor=0,$tahun=0){
		$this->_get_datatable_query($np,$tgl,$vendor,$tahun);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($np=0,$tgl=0,$vendor=0,$tahun=0){
		$this->db->select("*");	
		$this->db->from($this->table);
		
		if($np!='0')
			$this->db->where("np_karyawan", $np);
		if($tgl!='0')
			$this->db->where("tanggal_mcu", urldecode($tgl));
		if($vendor!='0')
			$this->db->where("vendor", urldecode($vendor));
        if($tahun!='0')
            $this->db->where("tahun", $tahun);
		
		return $this->db->count_all_results();
	}
	
	public function daftar_upload(){
		$this->db->select('tanggal_mcu as tanggal');
		$this->db->from($this->table);
		$this->db->group_by('tanggal_mcu');
		
		return $this->db->get();
	}
	
	public function daftar_tahun(){
		$this->db->select('tahun');
		$this->db->from($this->table);
		$this->db->group_by('tahun');
		
		return $this->db->get();
	}

	public function daftar_vendor(){
		$this->db->select('vendor');
		$this->db->from($this->table);
		$this->db->group_by('vendor');
		
		return $this->db->get();
	}
}

/* End of file m_skep.php */
/* Location: ./application/models/informasi/m_skep.php */