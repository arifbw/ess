<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_riwayat_kacamata extends CI_Model {

	var $table = 'ess_riwayat_kacamata';
	var $column_order = array(null,'np_karyawan','task_type',null,null,'nama_keluarga');
	var $order = array('id' => 'desc'); // default order 
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	private function _get_datatable_query($np=0) {
		//dipindah kebawah karena tabel nya dinamis
		$column_search = array('np_karyawan','nama','nama_unit_singkat','task_type','nama_keluarga'); //set column field database for datatable_skep 
				
		$this->db->select("a.*, nama, nama_unit_singkat");
		$this->db->join("mst_karyawan b", "a.np_karyawan=b.no_pokok", "left");
		$this->db->from($this->table.' a');
		// $this->db->where('task_type', 'Klaim Kacamata Kary');
		$this->db->where_in('tt', ['9N', '9O', '9P', '9Q', '9R']); # ubah ke ini untuk ambil karyawan, pasangan, anak
		
		if($np!=0 && $_SESSION["grup"]!=5) {
			$this->db->where("np_karyawan", $np);
		} else if ($_SESSION["grup"]==5) {
			$this->db->where("np_karyawan", $_SESSION['no_pokok']);
		}
				
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
		$this->db->select("*");
		$this->db->join("mst_karyawan b", "a.np_karyawan=b.no_pokok", "left");
		$this->db->from($this->table.' a');
		$this->db->where('task_type', 'Klaim Kacamata Kary');
		
		if($np!=0 && $_SESSION["grup"]!=5) {
			$this->db->where("np_karyawan", $np);
		} else if ($_SESSION["grup"]==5) {
			$this->db->where("np_karyawan", $_SESSION['no_pokok']);
		}

		return $this->db->count_all_results();
	}

	public function detailKaryawan($np) 
	{
		$this->db->select("b.np_karyawan as np, b.nama_pegawai, a.nama_unit_singkat as unit, c.nama_file");
		$this->db->from('mst_karyawan a');
		$this->db->join($this->table.' b', 'a.no_pokok=b.np_karyawan', 'left');
		$this->db->join('foto_karyawan c', 'a.no_pokok=c.no_pokok', 'left');

		$this->db->where('a.no_pokok', $np);

		return $this->db->get()->row();
	}
	
	public function riwayatPemeriksaan($np, $bill, $tgl)
	{
		$this->db->select("nama_pasien, nama_vendor, jumlah_hari, jumlah_pengobatan, tgl_berobat, deskripsi_periksa, tagihan, beban_karyawan, tanggungan_karyawan, tanggungan_perusahaan, melebihi_batas, catatan, referral");
		$this->db->from($this->table);

		$this->db->where('np_karyawan', $np);
		$this->db->where('bill_no', $bill);
		$this->db->where('tgl_berobat', date('Y-m-d', strtotime($tgl)));

		return $this->db->get()->result();
	}	
}

/* End of file m_skep.php */
/* Location: ./application/models/informasi/m_skep.php */