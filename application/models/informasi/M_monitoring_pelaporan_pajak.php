<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_monitoring_pelaporan_pajak extends CI_Model
{
	var $table = 'laporan_bukti_lapor_pajak';
	var $karyawan = 'mst_karyawan';
	var $column_order = array(null, null, null,null, null, null, null);
	var $column_search = array('kry.no_pokok','kry.nama','pajak.no_tanda_terima_elektronik','pajak.tahun'); 
	var $order = array('kry.np_karyawan', 'pajak.id'); 

	public function __construct()
	{
		parent::__construct();
	}

	private function _get_datatables_query()
	{
		$tahun = @$this->input->post('tahun', true) ?: date('Y');
		$this->db->select("kry.no_pokok, kry.nama, kry.kode_unit, kry.nama_unit, pajak.id, pajak.tahun, pajak.status_spt, pajak.no_tanda_terima_elektronik, pajak.surat_keterangan");
		$this->db->from("{$this->karyawan} kry");
		$this->db->join("{$this->table} pajak", "pajak.np_karyawan = kry.no_pokok AND pajak.deleted_at IS NULL AND pajak.tahun = {$tahun}", 'LEFT');

		if(@$this->input->post('unit', true)!='00000') $this->db->like('kry.kode_unit', rtrim($this->input->post('unit', true), '0'), 'LEFT');

		$i = 0;

		foreach ($this->column_search as $item) // loop column 
		{
			if (@$_POST['search']['value']) // if datatable send POST for search
			{

				if ($i === 0) // first loop
				{
					$this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
					$this->db->like($item, $_POST['search']['value']);
				} else {
					$this->db->or_like($item, $_POST['search']['value']);
				}

				if (count($this->column_search) - 1 == $i) //last loop
					$this->db->group_end(); //close bracket
			}
			$i++;
		}

		if (isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		}
	}

	public function get_filtered_data($tahun, $unit)
    {
        $this->db->select("kry.no_pokok, kry.nama, kry.kode_unit, kry.nama_unit, pajak.tahun, pajak.status_spt, pajak.no_tanda_terima_elektronik");
        $this->db->from("{$this->karyawan} kry");
        $this->db->join("{$this->table} pajak", "pajak.np_karyawan = kry.no_pokok AND pajak.deleted_at IS NULL AND pajak.tahun = {$tahun}", 'LEFT');
        
        if ($unit != '00000') {
			$this->db->like('kry.kode_unit', rtrim($unit, '0'), 'LEFT');
        }

        return $this->db->get()->result();
    }

	function get_datatables() {
		$this->_get_datatables_query();
		
		if($_POST['length'] != -1)
		$this->db->limit(@$_POST['length'], @$_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered() {
		$this->_get_datatables_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all() {
        $this->_get_datatables_query();
		return $this->db->count_all_results();
	}

	function get_rekap_lapor(){
		$unit = @$this->input->post('unit', true) ?: '00000';
		$tahun = @$this->input->post('tahun', true) ?: date('Y');

		$this->db->select('COUNT(CASE WHEN pajak.np_karyawan IS NOT NULL AND pajak.no_tanda_terima_elektronik IS NOT NULL THEN 1 END) AS "Sudah Lapor", COUNT(CASE WHEN pajak.np_karyawan IS NULL OR pajak.no_tanda_terima_elektronik IS NULL THEN 1 END) AS "Belum Lapor"');
		$this->db->from("{$this->karyawan} kry");
		$this->db->join("{$this->table} pajak", "pajak.np_karyawan = kry.no_pokok AND pajak.deleted_at IS NULL AND pajak.tahun = {$tahun}", 'LEFT');
		if($unit!='00000') $this->db->like('kry.kode_unit', rtrim($unit, '0'), 'LEFT');
		return $this->db->get()->row();
	}
}
