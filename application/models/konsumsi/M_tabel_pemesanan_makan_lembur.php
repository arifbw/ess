<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_tabel_pemesanan_makan_lembur extends CI_Model {

	var $table = 'ess_pemesanan_makan_lembur';
	var $column_order = array(null, 'nomor_pemesanan',null,null,'nama_unit','jumlah_pemesanan','jenis_lembur',null,null); //set column field database for datatable orderable	
	var $column_search = array('nama_unit','jenis_pesanan','jenis_lembur','jumlah'); //set column field database for datatable orderable	
	var $order = array('tanggal_pemesanan' => 'desc'); // default order 
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	private function _get_datatables_query($verif=0, $tampil_bulan_tahun=null)
	{	
		if($tampil_bulan_tahun!=null && $tampil_bulan_tahun!=''){
            //$this->db->like('tanggal_berangkat',$tampil_bulan_tahun);
            $this->db->where('YEAR(tanggal_pemesanan)',explode('_',$tampil_bulan_tahun)[0]);
            $this->db->where('MONTH(tanggal_pemesanan)',explode('_',$tampil_bulan_tahun)[1]);
		}				

		$this->db->select('a.*, a.jumlah_pemesanan as jumlah');	
		$this->db->from($this->table.' a');
		
		if($_SESSION["grup"]==4 && ($verif=='0' || $verif=='2')) { //jika Pengadministrasi Unit Kerja
			$ada_data=0;
			$var=array();
			$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
			foreach ($list_pengadministrasi as $data) //looping list_pengadministrasi
			{	
				array_push($var,$data['kode_unit']);
				$ada_data=1;
			}
			if($ada_data==0)
			{
				$var='';
			}
			$this->db->where_in('kode_unit', $var);								
			if ($verif=='0')
				$this->db->where('(verified is null OR verified = "1")')->where('tanggal_pemesanan >= curdate()');
			elseif ($verif=='2')
				$this->db->where("(verified in ('2','3','4','5','6') OR tanggal_pemesanan < curdate())");
		} else if($_SESSION["grup"]==5) { //jika Pengguna
			if ($verif=='0') {
				$this->db->where('(np_pemesan="'.$_SESSION["no_pokok"].'")');
				$this->db->where('(verified is null OR verified = "1")')->where('tanggal_pemesanan >= curdate()');
			} else if ($verif=='1') {
				$this->db->where('((np_atasan="'.$_SESSION["no_pokok"].'"))');
				$this->db->where('(verified is null OR verified = "1")')->where('tanggal_pemesanan >= curdate()');
			} else if ($verif=='2') {
				$this->db->where('((np_pemesan="'.$_SESSION["no_pokok"].'") OR (np_atasan="'.$_SESSION["no_pokok"].'"))');
				$this->db->where("(verified in ('2','3','4','5','6') OR tanggal_pemesanan < curdate())");
			}
		} else { //jika admin
			if ($verif=='1')
				$this->db->where_in('verified', array('1'))->where('tanggal_pemesanan >= curdate()');
			else if ($verif=='2')
				$this->db->where("(verified in ('3','4','6') OR tanggal_pemesanan < curdate())");
		}
		
		// $this->db->order_by('np_karyawan','ASC');	
				
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
		else
		{
			$this->db->order_by('verified ASC, created ASC');
		}
	}

	function get_datatables($verif=0, $tampil_bulan_tahun=null)
	{
		$this->_get_datatables_query($verif, $tampil_bulan_tahun);
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered($verif=0, $tampil_bulan_tahun=null)
	{
		$this->_get_datatables_query($verif, $tampil_bulan_tahun);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($verif=0, $tampil_bulan_tahun=null)
	{
        $this->_get_datatables_query($verif, $tampil_bulan_tahun);
        return $this->db->count_all_results();
	}
	
}

/* End of file m_perencanaan_jadwal_kerja.php */
/* Location: ./application/models/kehadiran/m_perencanaan_jadwal_kerja.php */