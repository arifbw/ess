<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_tabel_persetujuan_lembur_sdm extends CI_Model {

	var $table = 'ess_lembur_transaksi';
	var $column_order = array(null, 'no_pokok', 'ess_lembur_transaksi.nama', 'tgl_dws', 'input_mulai', 'input_selesai', 'waktu_diakui', 'approval_status', ''); //set column field database for datatable orderable
	var $column_search = array('ess_lembur_transaksi.no_pokok', 'ess_lembur_transaksi.nama', 'tgl_mulai', 'tgl_selesai', 'waktu_mulai_fix', 'waktu_selesai_fix', 'tgl_dws'); //set column field database for datatable searchable 
	var $order = array('tgl_dws' => 'desc'); // default order 
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	private function _get_datatables_query($approve, $tgl='')
	{
		//jika Pengadministrasi Unit Kerja
		if($_SESSION["grup"]==4) {
			$ada_data=0;
			$var=array();
			$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
			//looping list_pengadministrasi
			foreach ($list_pengadministrasi as $data) {
				array_push($var,$data['kode_unit']);
				$ada_data=1;
			}
			if($ada_data==0)
				$var='';
		}
		else if($_SESSION["grup"]==5) //jika Pengguna
			$var = $_SESSION["no_pokok"];
		else
			$var = 1;

		if($_SESSION["grup"]==4) //jika Pengadministrasi Unit Kerja
			$this->db->where_in('kode_unit', $var);
		else if($_SESSION["grup"]==5) //jika Pengguna
			$this->db->where_in('no_pokok', $var);

		//$this->db->join('mst_karyawan', 'mst_karyawan.no_pokok = ess_lembur_transaksi.no_pokok', 'left');
		$this->db->select('ess_lembur_transaksi.*, concat_ws(" ",tgl_mulai,jam_mulai) as input_mulai, concat_ws(" ",tgl_selesai,jam_selesai) as input_selesai, concat(waktu_mulai_fix," s/d ",waktu_selesai_fix) as waktu_diakui');
		$this->db->from($this->table);
		$this->db->where('is_manual_by_sdm != "1"');
		if ($approve == '3') {
			$this->db->where('((waktu_mulai_fix is null or waktu_mulai_fix = "") OR (waktu_selesai_fix is null or waktu_selesai_fix = ""))');
		}
		else if ($approve == '0') {
			$this->db->where('((approval_status="0" or approval_status is null) AND ((waktu_mulai_fix is not null or waktu_mulai_fix != "") AND (waktu_selesai_fix is not null or waktu_selesai_fix != "")))');
		}
		else if ($approve != 'all') {
			$this->db->where('approval_status', $approve);
		}

		if ($tgl != '') {
			$tgl_mulai = date('Y-m-d', strtotime(substr($tgl,0,10))).' 00:00:00';
			$tgl_selesai = date('Y-m-d', strtotime(substr($tgl,-10))).' 23:59:59';
			$this->db->where('(waktu_mulai_fix between "'.$tgl_mulai.'" and "'.$tgl_selesai.'")');
		}

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

	function get_datatables($approve, $tgl='') {
		$this->_get_datatables_query($approve, $tgl);
		
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered($approve, $tgl='') {
		$this->_get_datatables_query($approve, $tgl);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($approve, $tgl='') {
		//jika Pengadministrasi Unit Kerja
		if($_SESSION["grup"]==4) {
			$ada_data=0;
			$var=array();
			$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
			//looping list_pengadministrasi
			foreach ($list_pengadministrasi as $data) {
				array_push($var,$data['kode_unit']);
				$ada_data=1;
			}
			if($ada_data==0)
				$var='';
		}
		else if($_SESSION["grup"]==5) //jika Pengguna
			$var = $_SESSION["no_pokok"];
		else
			$var = 1;

		if($_SESSION["grup"]==4) //jika Pengadministrasi Unit Kerja
			$this->db->where_in('kode_unit', $var);
		else if($_SESSION["grup"]==5) //jika Pengguna
			$this->db->where_in('no_pokok', $var);

		//$this->db->join('mst_karyawan', 'mst_karyawan.no_pokok = ess_lembur_transaksi.no_pokok', 'left');
		$this->db->where('is_manual_by_sdm = "0"');
		$this->db->select('ess_lembur_transaksi.*');
		$this->db->from($this->table);
		if ($approve == '3') {
			$this->db->where('((waktu_mulai_fix is null or waktu_mulai_fix = "") OR (waktu_selesai_fix is null or waktu_selesai_fix = ""))');
		}
		else if ($approve == '0') {
			$this->db->where('((approval_status="0" or approval_status is null) AND ((waktu_mulai_fix is not null or waktu_mulai_fix != "") AND (waktu_selesai_fix is not null or waktu_selesai_fix != "")))');
		}
		else if ($approve != 'all') {
			$this->db->where('approval_status', $approve);
		}

		if ($tgl != '') {
			$tgl_mulai = date('Y-m-d', strtotime(substr($tgl,0,10))).' 00:00:00';
			$tgl_selesai = date('Y-m-d', strtotime(substr($tgl,-10))).' 23:59:59';
			$this->db->where('(waktu_mulai_fix between "'.$tgl_mulai.'" and "'.$tgl_selesai.'")');
		}
		return $this->db->count_all_results();
	}
	
	
}

/* End of file m_tabel_persetujuan_cuti_sdm.php */
/* Location: ./application/models/kehadiran/m_tabel_persetujuan_cuti_sdm.php */