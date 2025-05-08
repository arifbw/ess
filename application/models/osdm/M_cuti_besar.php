<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_cuti_besar extends CI_Model {

	private $table="cuti_cubes_jatah";
	private $table_karyawan="mst_karyawan";
	var $column_order = array(null, 'tahun','np_karyawan'); //set column field database for datatable orderable
	var $column_search = array('np_karyawan','nama','tahun'); //set column field database for datatable searchable 
	var $order = array('tahun' => 'desc','np_karyawan' => 'asc'); // default order 
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	private function _get_datatables_query(){
		$this->db->select("b.nama");
		$this->db->select("a.*");
		//jika mau ada kondisi kapan mau dikompensasiin
		//$this->db->select("case when TIMESTAMPDIFF(MONTH, NOW(), tanggal_kadaluarsa) <= 2 and a.sisa_bulan>0 then 'ya' else 'tidak' end bisa_konversi");
		$this->db->select("case when a.sisa_bulan>0 then 'ya' else 'tidak' end bisa_konversi");
		$this->db->from($this->table." a");	
		$this->db->join('mst_karyawan b', 'b.no_pokok = a.np_karyawan', 'left');
		$this->db->where('b.nama is not null');
		$this->db->where('a.tanggal_kadaluarsa >=', date('Y-m-d'));
		$this->db->order_by("tahun", "desc");
		$this->db->order_by("np_karyawan",'asc');
				
		$i = 0;
	
		foreach ($this->column_search as $item) // loop column 
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

				if(count($this->column_search) - 1 == $i) //last loop
					$this->db->group_end(); //close bracket
			}
			$i++;
		}
		
		if(isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order))
		{
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	function get_datatables()
	{
		$this->_get_datatables_query();
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered()
	{
		$this->_get_datatables_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all()
	{
		$this->_get_datatables_query();
		return $this->db->count_all_results();
	}
	
	public function data_cuti_besar($np_karyawan,$tahun){
		$data = $this->db->select("a.*")
						 ->select("b.nama")
						 ->from($this->table." a")
						 ->join($this->table_karyawan." b","b.no_pokok=a.np_karyawan","left")
						 ->where('np_karyawan',$np_karyawan)
						 ->where('a.tahun',$tahun)
						 ->get()
						 ->result_array()[0];
		return $data;
	}
	
	public function konversi($id_cuti_besar,$konversi_bulan,$konversi_hari){
		$this->db->where("id", $id_cuti_besar);
		$this->db->set("konversi_bulan", "konversi_bulan+".$konversi_bulan, FALSE);
		$this->db->set("konversi_hari", "konversi_hari+".$konversi_hari, FALSE);
		$this->db->set("total_bulan", "total_bulan-".$konversi_bulan, FALSE);
		$this->db->set("total_hari", "total_hari+".$konversi_hari, FALSE);
		$this->db->set("sisa_bulan", "sisa_bulan-".$konversi_bulan, FALSE);
		$this->db->set("sisa_hari", "sisa_hari+".$konversi_hari, FALSE);
		$this->db->update($this->table);
	}
	
	public function perpanjang_kadaluarsa($id_cuti_besar,$perpanjang_kadaluarsa){
		$this->db->where("id", $id_cuti_besar);
		$this->db->set("tanggal_kadaluarsa", $perpanjang_kadaluarsa);
	
		$this->db->update($this->table);
	}
	
	public function maintenance_kuota($id_cuti_besar,$no_pokok,$tahun,$sisa_edit,$sisa_bulan_asli,$sisa_hari_asli,$sisa_bulan,$sisa_hari,$sisa_edit_alasan){
		$this->db->where("id", $id_cuti_besar);
		$this->db->set("sisa_edit", $sisa_edit);
		$this->db->set("sisa_bulan_asli", $sisa_bulan_asli);
		$this->db->set("sisa_hari_asli", $sisa_hari_asli);
		$this->db->set("sisa_bulan", $sisa_bulan);
		$this->db->set("sisa_hari", $sisa_hari);
		$this->db->set("sisa_edit_alasan", $sisa_edit_alasan);
		$this->db->set("sisa_edit_by", $this->session->userdata('no_pokok'));
		$this->db->set("sisa_edit_at",  date('Y-m-d H:i:s'));
		
		$this->db->update($this->table);
	}
	
	public function catatan_ubcb($id_cuti_besar,$no_pokok,$tahun,$ubcb_tanggal_keluar,$ubcb_tanggal_cuti){
		$this->db->where("id", $id_cuti_besar);
		$this->db->set("ubcb_tanggal_keluar", $ubcb_tanggal_keluar);
		$this->db->set("ubcb_tanggal_cuti", $ubcb_tanggal_cuti);		
		$this->db->set("ubcb_update_by", $this->session->userdata('no_pokok'));
		$this->db->set("ubcb_update_at",  date('Y-m-d H:i:s'));
		
		$this->db->update($this->table);
	}
	
	public function kompensasi_bulan($id_cuti_besar,$no_pokok,$tahun,$hasil_sisa_bulan,$hasil_kompensasi_bulan,$hasil_kompensasi_at){
		$this->db->where("id", $id_cuti_besar);
		$this->db->set("sisa_bulan", $hasil_sisa_bulan);
		$this->db->set("kompensasi_bulan", $hasil_kompensasi_bulan);
		$this->db->set("kompensasi_at", $hasil_kompensasi_at);		
		$this->db->set("sisa_edit_by", $this->session->userdata('no_pokok'));
		$this->db->set("sisa_edit_at",  date('Y-m-d H:i:s'));
		
		$this->db->update($this->table);
	}
	
	public function generate_cuti_besar($data){
		$this->db->insert($this->table,$data);
	}
	
	public function cek_cuti_besar($np_karyawan,$tahun){
		$data = $this->db->from($this->table)
						 ->where('np_karyawan',$np_karyawan)
						 ->where('tahun',$tahun)
						 ->get();
		
		if($data->num_rows()==1){
			$return = true;
		}
		else{
			$return = false;
		}
		return $return;
	}
	
	public function cek_masih_cuti_besar($np_karyawan){
		$data = $this->db->select("sum(sisa_hari) AS sisa_hari")
						 ->from($this->table)
						 ->where('np_karyawan',$np_karyawan)
						 ->where('sisa_hari>','0')
						 ->get()->row_array();
		
		
		return $data;
	}
	
	public function cuti_besar_menunggu_submit_erp($np_karyawan)
	{
		 $data = $this->db->query("SELECT COUNT(*) AS menunggu FROM ess_cuti_bersama WHERE np_karyawan='$np_karyawan' AND enum='1' AND (submit_erp='' OR submit_erp is null)")->row_array();
		 
			
		return $data;
	}
}

/* End of file m_cuti_besar.php */
/* Location: ./application/models/osdm/m_cuti_besar.php */