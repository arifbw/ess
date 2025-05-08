<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_penggajian extends CI_Model {

	var $table_header = 'erp_payslip_header';
	var $table_karyawan = 'erp_payslip_karyawan';
	var $table_mst_karyawan = 'mst_karyawan';
	var $table_gaji = 'erp_payslip';
	var $table_master_gaji = 'mst_payslip';
	var $table_cetak = 'erp_payslip_cetak';
	var $table_cuti_tahunan = 'erp_absence_quota';
	var $table_cuti_besar = 'cuti_cubes_jatah';
	var $table_hutang_cuti = 'cuti_hutang';
	var $column_order = array(null, 'payment_date'); //set column field database for datatable orderable	
	var $order = array('payment_date' => 'desc'); // default order 
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	private function _get_datatables_query()
	{
		//dipindah kebawah karena tabel nya dinamis
		$column_search = array("a.payment_date"); //set column field database for datatable 
		
		$this->db->select("a.id");
		$this->db->select("a.nama_payslip");
		$this->db->select("a.payment_date");
		$this->db->select("a.start_display");
		$this->db->select("COUNT(b.id) penerima");
		$this->db->select("SUM(b.with_payslip) dengan_slip");
		$this->db->from($this->table_header." a");
		$this->db->join($this->table_karyawan." b","a.id=b.id_payslip_header","left");
		$this->db->group_by("a.id");
		$this->db->order_by("a.payment_date",'desc');
				
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

	public function count_all(){
		$this->db->select("a.payment_date");	
		$this->db->from($this->table_header." a" );
		
		$this->db->order_by("payment_date",'desc');
		$this->db->order_by("np_karyawan", "asc");
		
		return $this->db->count_all_results();
	}
	
	public function get_data_penggajian($id_header){
		return $this->db->select("*")
						->from($this->table_header)
						->where("id",$id_header)
						->get()
						->result_array()[0];
	}
	
	public function get_slip_request(){
		$this->db->select("b.nama_payslip nama_pembayaran")
				 ->select("b.np_karyawan")
				 ->select("b.id_payslip_header")
				 ->select("b.nama nama_karyawan")
				 ->select("b.kode_unit kode_unit")
				 ->select("b.nama_unit nama_unit")
				 ->select("b.nama_jabatan nama_jabatan")
				 ->select("d.jenis")
				 ->select("d.nama_slip")
				 ->select("SUM(AES_DECRYPT(c.amount,md5(concat(c.payment_date,c.wage_type,c.parameter)))) amount",false)
				 ->from($this->table_cetak." a")
				 ->join($this->table_karyawan." b","a.id_payslip_karyawan=b.id", "left")
				 ->join($this->table_gaji." c","b.id=c.id_payslip_karyawan", "left")
				 ->join($this->table_master_gaji." d","c.wage_type=d.kode", "left")
				 ->where("a.id_requester",$_SESSION["id_pengguna"])
				 ->where("a.status","REQUEST")
				 ->where("b.with_payslip","1");
				 
		if(!empty($arr_np)){
			$this->db->where_in("b.np_karyawan",$arr_np);
		}
		
		$this->db->where("d.nama_slip !=","")
				 ->group_by(array("a.id","b.id","d.nama_slip","d.jenis"))
				 ->order_by("ISNULL(b.kode_unit)")
				 ->order_by("b.kode_unit")
				 ->order_by("substr(b.kode_jabatan,-2)")
				 ->order_by("b.np_karyawan")
				 ->order_by("d.jenis")
				 ->order_by("d.nama_slip");

	   $return = $this->db->get()
					   ->result_array();
//echo $this->db->last_query();die();
		return $return;
	}
	
	public function np_karyawan_penggajian($id_header){
		$return = $this->db->select("np_karyawan")
						->from($this->table_karyawan)
						->where("id_payslip_header",$id_header)
						->get()
						->result_array();

		return $return;
	}
	
	public function tambah_request_cetak($data){
		//$this->db->insert("erp_payslip_cetak",array("id_payslip_karyawan"=>$id_payslip_karyawan, "id_requester"=>$np, "status"=>$status, "waktu_permintaan"=>date("Y-m-d H:i:s")));
		$this->db->insert_batch('erp_payslip_cetak',$data);
	}
	
	public function update_request_cetak(){
		$this->db->where("status","REQUEST")->update("erp_payslip_cetak",array("status"=>"TELAH CETAK", "waktu_cetak"=>date("Y-m-d H:i:s")));
	}
	
	public function get_cuti_tahunan(){
		$return = $this->db->select("c.np_karyawan")
						   ->select("date_format(c.start_date,'%Y') tahun",false)
						   ->select("c.number - c.deduction sisa",false)
						   ->select("date_format(c.deduction_to,'%d.%m.%Y') berlaku",false)
						   ->from($this->table_cetak." a")
						   ->join($this->table_karyawan." b","a.id_payslip_karyawan=b.id")
						   ->join($this->table_cuti_tahunan." c","b.np_karyawan=c.np_karyawan")
						   ->where("a.status","REQUEST")
						   ->where("a.id_requester",$_SESSION["id_pengguna"])
						   ->where("c.number !=","c.deduction")
						   ->where("c.deduction_from <=","NOW()",false)
						   ->where("c.deduction_to >=","NOW()",false)
						   ->order_by("c.np_karyawan")
						   ->order_by("tahun")
						   ->get()
						   ->result_array();//die($this->db->last_query());
		return $return;
	}
	
	public function get_cuti_besar(){
		$return = $this->db->select("c.np_karyawan")
						   ->select("c.tahun",false)
						   ->select("c.sisa_bulan")
						   ->select("c.sisa_hari")
						   ->select("date_format(c.tanggal_kadaluarsa,'%d.%m.%Y') berlaku",false)
						   ->from($this->table_cetak." a")
						   ->join($this->table_karyawan." b","a.id_payslip_karyawan=b.id")
						   ->join($this->table_cuti_besar." c","b.np_karyawan=c.np_karyawan")
						   ->where("a.status","REQUEST")
						   ->where("a.id_requester",$_SESSION["id_pengguna"])
						   ->group_start()
							   ->where("c.sisa_bulan >","0")
							   ->or_where("c.sisa_hari >","0")
						   ->group_end()
						   ->where("c.tanggal_timbul <=","NOW()",false)
						   ->where("c.tanggal_kadaluarsa >=","NOW()",false)
						   ->order_by("c.np_karyawan")
						   ->order_by("tahun")
						   ->get()
						   ->result_array();//die($this->db->last_query());
		return $return;
	}
	
	public function get_hutang_cuti(){
		$return = $this->db->select("c.no_pokok")
						   ->select("c.hutang")
						   ->from($this->table_cetak." a")
						   ->join($this->table_karyawan." b","a.id_payslip_karyawan=b.id")
						   ->join($this->table_hutang_cuti." c","b.np_karyawan=c.no_pokok")
						   ->where("a.status","REQUEST")
						   ->where("a.id_requester",$_SESSION["id_pengguna"])
						   ->where("c.hutang >","0")
						   ->order_by("c.no_pokok")
						   ->get()
						   ->result_array();//die($this->db->last_query());
		return $return;
	}
	
	public function get_pesan_slip($id){
		$return = $this->db->select("pesan_1")
						   ->select("pesan_2")
						   ->from($this->table_header)
						   ->where("id",$id)
						   ->get()
						   ->result_array()[0];//echo $this->db->last_query();
		return $return;
	}
	
	public function ubah($set,$id_header){
		$this->db->where('id',$id_header)
				 ->update($this->table_header,$set);
	}
	
	public function cek_hasil_ubah($id_header,$waktu_publikasi,$pesan_baris_1,$pesan_baris_2){
		$data = $this->db->from($this->table_header)
						 ->where('id',$id_header)
						 ->where('start_display',$waktu_publikasi)
						 ->where('pesan_1',$pesan_baris_1)
						 ->where('pesan_2',$pesan_baris_2)
						 ->get();

		if($data->num_rows()==0){
			$return = false;
		}
		else{
			$return = true;
		}

		return $return;
	}
	
	//7648 Tri Wibowo 22 04 2020, slip ditambah wfh
	public function select_payment_date_by_id_payslip_header($id_payslip_header)
	{
		$this->db->select("a.payment_date");
		$this->db->from("erp_payslip_header a");
		$this->db->where('a.id', $id_payslip_header);
		$this->db->limit(1);
		$return = $this->db->get()->row_array();
	
		return $return;
	}
}

/* End of file m_penggajian.php */
/* Location: ./application/models/osdm/m_penggajian.php */