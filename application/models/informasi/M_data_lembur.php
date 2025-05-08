<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_data_lembur extends CI_Model {

	var $table_payslip_header = "erp_payslip_header";
	var $table_payslip_karyawan = "erp_payslip_karyawan";
	var $table_payslip = "erp_payslip";
	var $table_mst_payslip = "mst_payslip";
	var $table_rank_lembur_karyawan = "rank_lembur_karyawan";
	var $table_rank_lembur_karyawan_tahunan = "rank_lembur_karyawan_tahunan";
	var $table_rank_lembur_unit_kerja = "rank_lembur_unit_kerja";
	var $column_order = array(null, ""); //set column field database for datatable_lembur orderable	
	var $order = array("" => ""); // default order 
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	private function _get_datatable_nominal_lembur_karyawan_query($kode_unit,$np_karyawan,$tahun,$bulan,$akumulasi){
		$kode_unit = rtrim($kode_unit,"0");
			
		//dipindah kebawah karena tabel nya dinamis
		$column_search = array(); //set column field database for datatable 

		$this->db->select("a1.payment_date");
		$this->db->select("a1.nama_payslip");
		$this->db->select("a2.np_karyawan");
		$this->db->select("a2.nama");
		$this->db->select("a2.kode_unit");
		$this->db->select("a2.nama_unit");
		$this->db->select("a2.kode_jabatan");
		$this->db->select("SUM(AES_DECRYPT(a3.amount,md5(concat(a3.payment_date,a3.wage_type,a3.parameter)))) gapok",false);
		$this->db->from($this->table_payslip_header." a1");
		$this->db->join($this->table_payslip_karyawan." a2","a1.id=a2.id_payslip_header");
		$this->db->join($this->table_payslip." a3","a2.id=a3.id_payslip_karyawan","LEFT");
		$this->db->join($this->table_mst_payslip." a4","a3.wage_type=a4.kode AND a4.nama_slip='Gaji Pokok'");
		$this->db->where("a1.start_display <= ","NOW()",false);
		$this->db->like("a1.nama_payslip","Gaji","AFTER");
		$this->db->like("a1.nama_payslip",$tahun,"BEFORE");
		$this->db->like("a2.kode_unit",$kode_unit,"AFTER");
		if(!empty($bulan)){
			$this->db->like("a1.payment_date",$tahun."-".$bulan,"AFTER");
		}
		if(!empty($np_karyawan)){
			$this->db->where("a2.np_karyawan",$np_karyawan);
		}
		$this->db->group_by("a1.nama_payslip");
		$this->db->group_by("a2.np_karyawan");
		$a = $this->db->get_compiled_select();
		
		$this->db->select("b1.nama_payslip");
		$this->db->select("b2.np_karyawan");
		$this->db->select("b2.kode_unit");
		$this->db->select("SUM(AES_DECRYPT(b3.amount,md5(concat(b3.payment_date,b3.wage_type,b3.parameter)))) lembur",false);
		$this->db->from($this->table_payslip_header." b1");
		$this->db->join($this->table_payslip_karyawan." b2","b1.id=b2.id_payslip_header");
		$this->db->join($this->table_payslip." b3","b2.id=b3.id_payslip_karyawan","LEFT");
		$this->db->join($this->table_mst_payslip." b4","b3.wage_type=b4.kode AND b4.nama_slip='Uang Lembur'");
		$this->db->where("b1.start_display <= ","NOW()",false);
		$this->db->like("b1.nama_payslip","Gaji","AFTER");
		$this->db->like("b1.nama_payslip",$tahun,"BEFORE");
		$this->db->like("b2.kode_unit",$kode_unit,"AFTER");
		if(!empty($bulan)){
			$this->db->like("b1.payment_date",$tahun."-".$bulan,"AFTER");
		}
		if(!empty($np_karyawan)){
			$this->db->where("b2.np_karyawan",$np_karyawan);
		}
		$this->db->group_by("b1.nama_payslip");
		$this->db->group_by("b2.np_karyawan");
		$b = $this->db->get_compiled_select();
		
		if(strcmp($akumulasi,"akumulasi karyawan")!=0){
			$this->db->select("a.nama_payslip");
		}
		if(strcmp($akumulasi,"akumulasi bulan")!=0){
			$this->db->select("a.np_karyawan");
			$this->db->select("a.nama");
			$this->db->select("a.kode_unit");
			$this->db->select("a.nama_unit");
		}
		
		if(strcmp($akumulasi,"rincian")==0){
			$this->db->select("IFNULL(b.lembur,0) lembur",false);
			$this->db->select("IFNULL(b.lembur,0)/a.gapok*100 persentase",false);
		}
		else{
			$this->db->select("IFNULL(SUM(b.lembur),0) lembur",false);
			$this->db->select("IFNULL(SUM(b.lembur),0)/SUM(a.gapok)*100 persentase",false);
		}
		
		$this->db->from("($a) a");
		$this->db->join("($b) b","a.nama_payslip=b.nama_payslip AND a.np_karyawan=b.np_karyawan AND a.kode_unit=b.kode_unit","left");
		
		if(strcmp($akumulasi,"akumulasi bulan")==0){
			$this->db->group_by("a.nama_payslip");
		}
		if(strcmp($akumulasi,"akumulasi karyawan")==0){
			$this->db->group_by("a.np_karyawan");
			$this->db->group_by("a.nama");
			$this->db->group_by("a.kode_unit");
			$this->db->group_by("a.nama_unit");
		}
		
		if(strcmp($akumulasi,"akumulasi karyawan")==0){
			$this->db->order_by("a.kode_unit");
			$this->db->order_by("a.kode_jabatan");
			$this->db->order_by("a.np_karyawan");
		}
		else{
			$this->db->order_by("a.payment_date");
		}
		
		//echo $this->db->get_compiled_select();die();
		$i = 0;
	
		foreach ($column_search as $item) // loop column 
		{
			if($_POST['search']['value']) // if datatable_pamlek send POST for search
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
		else if(isset($this->order)){//var_dump($this->order);
			foreach($this->order as $order_key => $order_value){
				$this->db->order_by($order_key, $order_value);
			}
		}
	}

	private function _get_datatable_peringkat_lembur_karyawan_query($kode_unit,$np_karyawan,$tahun,$bulan,$akumulasi,$tab){
		$kode_unit = rtrim($kode_unit,"0");
		$tabel = "";
		
		if(strcmp($akumulasi,"rincian")==0){
			$tabel = $this->table_rank_lembur_karyawan;
		}
		else if(strcmp($akumulasi,"akumulasi karyawan")==0){
			$tabel = $this->table_rank_lembur_karyawan_tahunan;
		}
		else if(strcmp($akumulasi,"akumulasi bulan")==0){
			$tabel = $this->table_rank_lembur_unit_kerja;
		}
		
		if(strcmp($tab,"peringkat persentase")==0){
			$peringkat = "persen";
		}
		else if(strcmp($tab,"peringkat nominal")==0){
			$peringkat = "nominal";
		}
		
		//dipindah kebawah karena tabel nya dinamis
		$column_search = array(); //set column field database for datatable 

		if(strcmp($akumulasi,"akumulasi karyawan")!=0){
			$this->db->select("a.nama_payslip");
		}
		if(strcmp($akumulasi,"akumulasi bulan")==0){
			$this->db->select("a.level");
		}
		else{
			$this->db->select("a.np_karyawan");
			$this->db->select("a.nama");
		}
		$this->db->select("a.kode_unit");
		$this->db->select("a.nama_unit");
		if(strcmp(substr($kode_unit,4,1),"0")!=0){
			$this->db->select("a.rank_".$peringkat."_unit rank_unit");
		}
		if(strcmp(substr($kode_unit,3,1),"0")!=0){
			$this->db->select("a.rank_".$peringkat."_seksi rank_seksi");
		}
		if(strcmp(substr($kode_unit,2,1),"0")!=0){
			$this->db->select("a.rank_".$peringkat."_departemen rank_departemen");
		}
		if(strcmp(substr($kode_unit,1,1),"0")!=0){
			$this->db->select("a.rank_".$peringkat."_divisi rank_divisi");
		}
		if(strcmp(substr($kode_unit,0,1),"0")!=0){
			$this->db->select("a.rank_".$peringkat."_direktorat rank_direktorat");
		}
		$this->db->select("a.rank_".$peringkat."_perusahaan rank_perusahaan");
		
		$this->db->from("$tabel a");
		
		$this->db->like("a.kode_unit",$kode_unit,"AFTER");
		if(!empty($np_karyawan)){
			$this->db->where("a.np_karyawan",$np_karyawan);
		}
		
		if(!empty($bulan)){
			$this->db->like("a.payment_date",$tahun."-".$bulan,"AFTER");
		}
		
		if(strcmp($akumulasi,"akumulasi karyawan")==0){
			$this->db->where("a.tahun",$tahun);
		}
		else{
			$this->db->like("a.nama_payslip",$tahun,"BEFORE");
		}
		
		if(strcmp($akumulasi,"akumulasi karyawan")!=0){
			$this->db->order_by("a.payment_date");
		}
		if(strcmp($akumulasi,"akumulasi bulan")==0){
			$this->db->order_by("a.level");
		}
		$this->db->order_by("a.rank_nominal_perusahaan");
		
		//echo $this->db->get_compiled_select();die();
		$i = 0;
	
		foreach ($column_search as $item) // loop column 
		{
			if($_POST['search']['value']) // if datatable_pamlek send POST for search
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
		else if(isset($this->order)){//var_dump($this->order);
			foreach($this->order as $order_key => $order_value){
				$this->db->order_by($order_key, $order_value);
			}
		}
	}

	function get_datatable_nominal_lembur_karyawan($kode_unit,$np_karyawan,$tahun,$bulan,$akumulasi){//var_dump($_POST);
		$this->_get_datatable_nominal_lembur_karyawan_query($kode_unit,$np_karyawan,$tahun,$bulan,$akumulasi);
		//$sql = $this->db->get_compiled_select();
		if($_POST['length'] != -1){
			$this->db->limit($_POST['length'], $_POST['start']);
		}
		
		//$query = $this->db->query($sql);//echo __LINE__;var_dump($query);
		$query = $this->db->get();//echo __LINE__;var_dump($query);
		//echo $this->db->last_query();
		//echo $sql;
		return $query->result();
	}

	function get_datatable_peringkat_lembur_karyawan($kode_unit,$np_karyawan,$tahun,$bulan,$akumulasi,$tab){//var_dump($_POST);
		$this->_get_datatable_peringkat_lembur_karyawan_query($kode_unit,$np_karyawan,$tahun,$bulan,$akumulasi,$tab);
		//$sql = $this->db->get_compiled_select();
		if($_POST['length'] != -1){
			$this->db->limit($_POST['length'], $_POST['start']);
		}
		
		//$query = $this->db->query($sql);//echo __LINE__;var_dump($query);
		$query = $this->db->get();//echo __LINE__;var_dump($query);
		//echo $this->db->last_query();
		//echo $sql;
		return $query->result();
	}

	function count_filtered_nominal($kode_unit,$np_karyawan,$tahun,$bulan,$akumulasi){
		$this->_get_datatable_nominal_lembur_karyawan_query($kode_unit,$np_karyawan,$tahun,$bulan,$akumulasi);
		$query = $this->db->get();
		return $query->num_rows();
	}
	
	function count_filtered_peringkat($kode_unit,$np_karyawan,$tahun,$bulan,$akumulasi,$tab){
		$this->_get_datatable_peringkat_lembur_karyawan_query($kode_unit,$np_karyawan,$tahun,$bulan,$akumulasi,$tab);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all_nominal(){
		$this->db->select("id");
		$this->db->from($this->table_payslip_karyawan);
;
		$count_all = count($this->db->get()->result_array());
		return $count_all;
	}
	
	public function count_all_peringkat(){
		$this->db->select("*");
		$this->db->from($this->table_rank_lembur_karyawan);
;
		$count_all = count($this->db->get()->result_array());
		return $count_all;
	}
	
	public function get_bulan_gaji($tahun){
		return $this->db->select("date_format(payment_date,'%m') bulan")
						->from($this->table_payslip_header)
						->where("date_format(payment_date,'%Y')",$tahun,false)
						->where("start_display <= ","NOW()",false)
						->like("nama_payslip","Gaji","AFTER")
						->like("nama_payslip",$tahun,"BEFORE")
						->order_by("payment_date","ASC")
						->get()
						->result_array();
						//echo $this->db->last_query();
	}
}

/* End of file m_data_lembur.php */
/* Location: ./application/models/informasi/m_data_lembur.php */