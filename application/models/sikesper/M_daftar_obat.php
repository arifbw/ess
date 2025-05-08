<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_daftar_obat extends CI_Model {

	var $table = 'ess_daftar_obat';
	var $column_order = array(null,'kode_obat','jenis','zat_aktif_obat','merek_obat','sediaan','keterangan',null); 
	var $order = array('created_at' => 'desc'); // default order 
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	private function _get_datatable_query($jenis=0,$ktg=0,$tgl=0) {
		//dipindah kebawah karena tabel nya dinamis
		$column_search = array('kode_obat','jenis','kategori','zat_aktif_obat','merek_obat','sediaan','dosis','farmasi','keterangan'); //set column field database for datatable_skep 
				
		$this->db->select("*");
		$this->db->from($this->table);
		if($jenis!='0')
			$this->db->where("jenis", urldecode($jenis));
		if($ktg!='0')
			$this->db->where("kategori", urldecode($ktg));		
		if($tgl!='0')
			$this->db->where("created_at", urldecode($tgl));		
				
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

	function get_datatable($jenis=0,$ktg=0,$tgl=0){
		$this->_get_datatable_query($jenis,$ktg,$tgl);
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered($jenis=0,$ktg=0,$tgl=0){
		$this->_get_datatable_query($jenis,$ktg,$tgl);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($jenis=0,$ktg=0,$tgl=0){
		$this->db->select("*");	
		$this->db->from($this->table);
		
		if($jenis!='0')
			$this->db->where("jenis", urldecode($jenis));
		if($ktg!='0')
			$this->db->where("kategori", urldecode($ktg));
		if($tgl!='0')
			$this->db->where("created_at", urldecode($tgl));
		
		return $this->db->count_all_results();
	}
	
	public function daftar_upload(){
		$this->db->select('created_at as tanggal');
		$this->db->from($this->table);
		$this->db->group_by('created_at');
		
		return $this->db->get();
	}

	public function daftar_jenis_obat(){
		$this->db->select('jenis');
		$this->db->from($this->table);
		$this->db->group_by('jenis');
		
		return $this->db->get()->result_array();
	}

	public function daftar_kategori_obat(){
		$this->db->select('kategori');
		$this->db->from($this->table);
		$this->db->group_by('kategori');
		
		return $this->db->get()->result_array();
	}
}

/* End of file m_skep.php */
/* Location: ./application/models/informasi/m_skep.php */