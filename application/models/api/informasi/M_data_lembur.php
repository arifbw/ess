<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_data_lembur extends CI_Model {

	var $table_payslip_header = "erp_payslip_header";
	var $table_payslip_karyawan = "erp_payslip_karyawan";
	var $table_payslip = "erp_payslip";
	var $table_mst_payslip = "mst_payslip";
	var $column_order = array(null, ""); //set column field database for datatable_lembur orderable	
	var $order = array("a.payment_date" => "ASC"); // default order 
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	private function _get_datatable_lembur_karyawan_query($kode_unit,$np_karyawan,$periode){
		$kode_unit = rtrim($kode_unit,"0");
			
		//dipindah kebawah karena tabel nya dinamis
		$column_search = array(); //set column field database for datatable_pamlek 

		$this->db->select("a1.payment_date");
		$this->db->select("a1.nama_payslip");
		$this->db->select("a2.np_karyawan");
		$this->db->select("a2.kode_unit");
		$this->db->select("a2.kode_jabatan");
		$this->db->select("SUM(AES_DECRYPT(a3.amount,md5(concat(a3.payment_date,a3.wage_type,a3.parameter)))) gapok",false);
		$this->db->from($this->table_payslip_header." a1");
		$this->db->join($this->table_payslip_karyawan." a2","a1.id=a2.id_payslip_header");
		$this->db->join($this->table_payslip." a3","a2.id=a3.id_payslip_karyawan","LEFT");
		$this->db->join($this->table_mst_payslip." a4","a3.wage_type=a4.kode AND a4.nama_slip='Gaji Pokok'");
		$this->db->like("a1.nama_payslip","Gaji","AFTER");
		$this->db->like("a1.nama_payslip",$periode,"BEFORE");
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
		$this->db->like("b1.nama_payslip","Gaji","AFTER");
		$this->db->like("b1.nama_payslip",$periode,"BEFORE");
		if(!empty($np_karyawan)){
			$this->db->where("b2.np_karyawan",$np_karyawan);
		}
		$this->db->group_by("b1.nama_payslip");
		$this->db->group_by("b2.np_karyawan");
		$b = $this->db->get_compiled_select();
		
		$this->db->select("a.nama_payslip");
		$this->db->select("a.np_karyawan");
		$this->db->select("a.kode_unit");
		$this->db->select("a.gapok");
		$this->db->select("IFNULL(b.lembur,0) lembur",false);
		$this->db->select("IFNULL(b.lembur,0)/a.gapok*100 persentase",false);
		$this->db->select("RANK() OVER (PARTITION BY a.kode_unit ORDER BY IFNULL(b.lembur,0))",false);
		$this->db->from("($a) a");
		$this->db->join("($b) b","a.nama_payslip=b.nama_payslip AND a.np_karyawan=b.np_karyawan AND a.kode_unit=b.kode_unit","left");
		$this->db->order_by("a.payment_date");
		$this->db->order_by("a.kode_unit");
		$this->db->order_by("a.kode_jabatan");
		
		
		echo $this->db->get_compiled_select();die();
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

	function get_datatable_lembur_karyawan($kode_unit,$np_karyawan,$periode){
		$this->_get_datatable_lembur_karyawan_query($kode_unit,$np_karyawan,$periode);
		$sql = $this->db->get_compiled_select();
		if($_POST['length'] != -1){
			$this->db->limit($_POST['length'], $_POST['start']);
		}
		
		$query = $this->db->query($sql);//echo __LINE__;var_dump($query);
		//echo $this->db->last_query();
		//echo $sql;
		return $query->result();
	}

	function count_filtered($kode_unit,$np_karyawan,$periode){
		//$this->db->reset_query();
		$this->_get_datatable_lembur_karyawan_query($kode_unit,$np_karyawan,$periode);
		$sql = $this->db->get_compiled_select();
		$this->db->limit($_POST['length'], $_POST['start']);
		$count_filtered = count($this->db->query($sql)->result_array());

		return $count_filtered;
	}

	public function count_all($periode){		
		$this->db->select("id");	
		$this->db->from($this->table_payslip_header);
		$count_all = count($this->db->get()->result_array());
		return $count_all;
	}
	
	
}

/* End of file m_data_lembur.php */
/* Location: ./application/models/informasi/m_data_lembur.php */