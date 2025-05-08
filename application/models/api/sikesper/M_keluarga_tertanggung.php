<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_keluarga_tertanggung extends CI_Model {

	//var $table = 'ess_kesehatan_keluarga_tertanggung';
	var $table = 'mst_karyawan';
	var $column_order = array(null,'no_pokok','b.tempat_lahir','usia','jenis_kelamin','bpjs_id','class_bpjs','jumlah');
	var $order = array('id' => 'desc'); // default order 
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	private function _get_datatable_query($np=0, $unit=0) {
		//dipindah kebawah karena tabel nya dinamis
		$column_search = array('no_pokok','b.nama','nama_unit_singkat','b.tempat_lahir','TIMESTAMPDIFF(YEAR , b.tanggal_lahir, CURDATE())','jenis_kelamin','bpjs_id','class_bpjs'); //set column field database for datatable_skep 
				
		$this->db->select("b.*, start_date, TIMESTAMPDIFF(YEAR , b.tanggal_lahir, CURDATE()) AS usia, bpjs_id, class_bpjs, a.id, sum(case when (tipe_keluarga is not null and tipe_keluarga != '') then 1 else 0 end) as jumlah, (select bpjs_kesehatan from ess_data_karyawan_bpjs where np_karyawan=b.no_pokok LIMIT 1) as bpjs_kesehatan, d.kelas");
		//$this->db->join("mst_karyawan b", "a.np_karyawan=b.no_pokok",'left');
		$this->db->join("ess_kesehatan_keluarga_tertanggung a", "a.np_karyawan=b.no_pokok",'left');
        $this->db->join("mst_kelas_perawatan d", "find_in_set(b.nama_pangkat, d.nama_pangkat)", "left");
		//$this->db->group_by('a.np_karyawan');
		$this->db->group_by('b.no_pokok');
		$this->db->from($this->table.' b');
        
        $this->db->where('b.kode_unit',$unit);
        if($np!='all'){
            $this->db->where("b.no_pokok", $np);
        }
		
		/*if($_SESSION["grup"]!=5) {
			if($np!=0)
				$this->db->where("np_karyawan", $np);
			if($unit!=0)
				$this->db->or_where("kode_unit", $unit);
		} else if ($_SESSION["grup"]==5) {
			$this->db->where("np_karyawan", $_SESSION['no_pokok']);
		}*/
				
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

	function get_datatable($np=0, $unit=0){
		$this->_get_datatable_query($np, $unit);
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered($np=0, $unit=0){
		$this->_get_datatable_query($np, $unit);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($np=0, $unit=0){	
		$this->_get_datatable_query($np, $unit);
		return $this->db->count_all_results();
	}

	public function detailKaryawan($np) 
	{
		$this->db->select("start_date, TIMESTAMPDIFF(YEAR , b.tanggal_lahir, CURDATE()) AS usia, bpjs_id, class_bpjs, a.id, b.nama as nama_pegawai, b.no_pokok as np, b.nama_unit_singkat as unit, c.nama_file, b.tanggal_lahir, b.tempat_lahir, b.jenis_kelamin, b.agama, d.kelas, (select bpjs_kesehatan from ess_data_karyawan_bpjs where np_karyawan='".$np."' LIMIT 1) as bpjs_kesehatan");
		//$this->db->join("mst_karyawan b", "a.np_karyawan=b.no_pokok", "left");
		$this->db->join("ess_kesehatan_keluarga_tertanggung a", "a.np_karyawan=b.no_pokok", "left");
		$this->db->join('foto_karyawan c', 'b.no_pokok=c.no_pokok', 'left');
		$this->db->join("mst_kelas_perawatan d", "find_in_set(b.nama_pangkat, d.nama_pangkat)", "left");
		$this->db->from($this->table.' b');

		//$this->db->where('np_karyawan', $np);
		$this->db->where('b.no_pokok', $np);

		return $this->db->get()->row();
	}
	
	public function detail_keluarga($np){		
		$this->db->select("*, TIMESTAMPDIFF(YEAR , tanggal_lahir, CURDATE()) as usia");	
		//$this->db->from($this->table);
		$this->db->from('ess_kesehatan_keluarga_tertanggung');
		$this->db->where("np_karyawan", $np);
		$this->db->where("tipe_keluarga is not null and tipe_keluarga != ''");
		
		return $this->db->get();
	}
	
	
}

/* End of file m_skep.php */
/* Location: ./application/models/informasi/m_skep.php */