<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_data_vaksin_keluarga extends CI_Model {
	
	var $table = 'ess_kesehatan_keluarga_tertanggung';
	var $column_order = array();
	var $column_search = array('ess_kesehatan_keluarga_tertanggung.np_karyawan', 'data_vaksin_keluarga.nama_lengkap');
	var $order = array('ess_kesehatan_keluarga_tertanggung.id' => 'asc');
	
	public function __construct(){
		parent::__construct();
	}
	
	private function _get_datatables_query($params){
		$this->db->select('ess_kesehatan_keluarga_tertanggung.id
		, ess_kesehatan_keluarga_tertanggung.np_karyawan
		, ess_kesehatan_keluarga_tertanggung.nama_karyawan
		, ess_kesehatan_keluarga_tertanggung.tipe_keluarga
		, ess_kesehatan_keluarga_tertanggung.tempat_lahir_keluarga
		, ess_kesehatan_keluarga_tertanggung.tanggal_lahir
		, ess_kesehatan_keluarga_tertanggung.nama_lengkap
		, ess_kesehatan_keluarga_tertanggung.no_urut
		, ess_kesehatan_keluarga_tertanggung.jenis_kelamin
		, data_vaksin_keluarga.nik
		, data_vaksin_keluarga.no_hp
		, data_vaksin_keluarga.email
		, data_vaksin_keluarga.status_kawin
		, data_vaksin_keluarga.alamat
		, data_vaksin_keluarga.status_vaksin
		, data_vaksin_keluarga.created_at
		, data_vaksin_keluarga.mst_klinik_id
		, data_vaksin_keluarga.usia
		, data_vaksin_keluarga.dibatalkan_admin
		, data_vaksin_keluarga.dibatalkan_ket
		, mst_klinik.kelurahan
		, mst_klinik.kecamatan
		, mst_klinik.kabupaten
		, mst_klinik.provinsi
		');

		/* 
		$params = [
			grup => val,
			list_np => [],
		];
		*/
		if( $params['grup']!='12' ){
			if( $params['grup']=='5' ){
				$this->db->where_in('ess_kesehatan_keluarga_tertanggung.np_karyawan', $params['list_np']);
			} else if( $params['grup']=='4' ){
				if( $params['list_np']!=[] ){
					$this->db->where_in('ess_kesehatan_keluarga_tertanggung.np_karyawan', $params['list_np']);
				} else{
					$this->db->where('ess_kesehatan_keluarga_tertanggung.id is null', null, false);
				}
			} else
				$this->db->where('ess_kesehatan_keluarga_tertanggung.id is null', null, false);
		}

		if( $params['jenis']!='all' ){
			switch ($params['jenis']) {
				case "1":
					$this->db->where('data_vaksin_keluarga.created_at is not null', null, false);
				  	break;
				case "2":
					$this->db->where('data_vaksin_keluarga.created_at is null', null, false);
				  	break;
				default:
				  	
			}
		}

		$this->db
			->where('ess_kesehatan_keluarga_tertanggung.status_tanggungan','Ditanggung');
			// ->where('ess_kesehatan_keluarga_tertanggung.no_urut >',0);
		$this->db
			->from($this->table)
			->join('data_vaksin_keluarga', 'data_vaksin_keluarga.np_karyawan=ess_kesehatan_keluarga_tertanggung.np_karyawan AND data_vaksin_keluarga.tipe_keluarga=ess_kesehatan_keluarga_tertanggung.tipe_keluarga AND data_vaksin_keluarga.nama_lengkap=ess_kesehatan_keluarga_tertanggung.nama_lengkap AND data_vaksin_keluarga.tanggal_lahir=ess_kesehatan_keluarga_tertanggung.tanggal_lahir', 'LEFT')
			->join('mst_klinik', 'mst_klinik.id=data_vaksin_keluarga.mst_klinik_id', 'LEFT');
		$i = 0;
	
		foreach ($this->column_search as $item) { // loop column 
			if($_POST['search']['value']) { // if datatable send POST for search
				if($i===0) { // first loop
					$this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
					$this->db->like($item, $_POST['search']['value']);
				}
				else {
					$this->db->or_like($item, $_POST['search']['value']);
				}

				if(count($this->column_search) - 1 == $i) //last loop
					$this->db->group_end(); //close bracket
			}
			$i++;
		}
		
		if(isset($_POST['order'])) { // here order processing
			$this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order)) {
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	function get_datatables($params) {
		$this->_get_datatables_query($params);
		if($_POST['length'] != -1)
			$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered($params) {
		$this->_get_datatables_query($params);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($params) {
		$this->_get_datatables_query($params);
		return $this->db->count_all_results();
	}
}