<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_tabel_permohonan_spbi extends CI_Model {
    
	var $table = "ess_permohonan_spbi";
	var $column_order = array('keluar_tanggal');
	var $column_search = array('nomor_surat','np_karyawan','nama','approval_atasan_np','approval_atasan_nama','pengecek1_np','pengecek1_nama','konfirmasi_pengguna_np','konfirmasi_pengguna_nama','danposko_np','danposko_nama');
	var $order = array('keluar_tanggal');
	
	public function __construct(){
		parent::__construct();
	}
	
	private function _get_datatables_query($params) {

		if(@$params['np']){
			$this->db->where('np_karyawan',$params['np']);
		} else if(@$params['kode_unit']){
			$this->db->where_in('kode_unit',$params['kode_unit']);
		}

        $this->db->from($this->table);
        $this->db->where('deleted_at IS NULL',null,false);
        $this->db->where('canceled_at IS NULL',null,false);
        $this->db->where("keluar_tanggal >=",$params['start_date']);
        $this->db->where("keluar_tanggal <=",$params['end_date']);
        $this->db->order_by('keluar_tanggal', 'DESC');
		
		$i = 0;
	
		foreach ($this->column_search as $item)
		{
			if(@$_POST['search']['value'])
			{
				
				if($i===0)
				{
					$this->db->group_start();
					$this->db->like($item, $_POST['search']['value']);
				}
				else
				{
					$this->db->or_like($item, $_POST['search']['value']);
				}

				if(count($this->column_search) - 1 == $i)
					$this->db->group_end();
			}
			$i++;
		}
		
		if(isset($_POST['order']))
		{
			$this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order))
		{
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	function get_datatables($params)
	{
		$this->_get_datatables_query($params);
		if($_POST['length'] != -1) $this->db->limit(@$_POST['length'], @$_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered($params)
	{
		$this->_get_datatables_query($params);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($params)
	{
        $this->_get_datatables_query($params);	
		return $this->db->count_all_results();
	}
}