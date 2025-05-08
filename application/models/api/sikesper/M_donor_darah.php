<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_donor_darah extends CI_Model {

	var $table = 'ess_donor_darah';
	var $column_order = array(null,'np_karyawan','position','examination_type','count(1)',null);
	var $order = array('id' => 'desc'); // default order 
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	private function _get_datatable_query($np=0) {
		//dipindah kebawah karena tabel nya dinamis
		$column_search = array('np_karyawan','personal_number','nama_pegawai','nama_unit','position','examination_type'); //set column field database for datatable_skep 
				
		$this->db->select("*, count(1) as jumlah");
		$this->db->group_by('np_karyawan, examination_type');
		$this->db->from($this->table);
		
		if($np!=0) {
			$this->db->where("np_karyawan", $np);
		}		
				
		$i = 0;
		foreach ($column_search as $item) // loop column 
		{
			if($_POST['search']['value']) // if datatable send POST for search
			{
				
				if($i===0) // first loop
				{
					$this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
					$this->db->like($item, $_POST['search']['value'], false);
				}
				else
				{
					$this->db->or_like($item, $_POST['search']['value'], false);
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

	function get_datatable($np=0){
		$this->_get_datatable_query($np);
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered($np=0){
		$this->_get_datatable_query($np);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($np=0){		
		$this->_get_datatable_query($np);
		return $this->db->count_all_results();
	}

	public function detailKaryawan($np) 
	{
		$this->db->select("no_pokok as np, nama as nama_pegawai, nama_unit as unit");
		$this->db->from('mst_karyawan');

		$this->db->where('no_pokok', $np);

		return $this->db->get()->row();
	}
	
	public function riwayatDonor($np)
	{
		$this->db->select("examination_type, diagnosa, exam_date, last_exam");
		$this->db->from($this->table);

		$this->db->where('np_karyawan', $np);
		// $this->db->where('examination_type', $type);

		return $this->db->get()->result();
	}	
}

/* End of file m_skep.php */
/* Location: ./application/models/informasi/m_skep.php */