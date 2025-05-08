<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_tabel_persetujuan extends CI_Model {

	var $table = 'ess_cuti';
	var $column_order = array(null, 'np_karyawan', 'uraian', 'start_date', 'end_date', 'jumlah_hari', 'np_karyawan', 'np_karyawan','created_at'); //set column field database for datatable orderable
	var $column_search = array('np_karyawan','uraian','start_date','end_date','jumlah_hari','alasan'); //set column field database for datatable searchable 
	var $order = array("start_date"=> "desc", "end_date"=> "desc", "np_karyawan" => "asc"); // default order 
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	private function _get_datatables_query($month=null,$filter='all')
	{ 
		$this->db->select('ess_cuti.id');
		$this->db->select('ess_cuti.np_karyawan');
		$this->db->select('ess_cuti.absence_type');
		$this->db->select('ess_cuti.start_date');
		$this->db->select('ess_cuti.end_date');
		$this->db->select('ess_cuti.jumlah_bulan');
		$this->db->select('ess_cuti.jumlah_hari');
		$this->db->select('ess_cuti.alasan');
		$this->db->select('ess_cuti.approval_1');
		$this->db->select('ess_cuti.approval_2');
		$this->db->select('ess_cuti.status_1');
		$this->db->select('ess_cuti.status_2');
		$this->db->select('ess_cuti.approval_1_date');
		$this->db->select('ess_cuti.approval_2_date');
		$this->db->select('ess_cuti.approval_sdm');
		$this->db->select('ess_cuti.created_at');
		$this->db->select('ess_cuti.created_by');		
		$this->db->select('ess_cuti.is_cuti_bersama');
		$this->db->select('nama');
		$this->db->select('mst_cuti.uraian');
		$this->db->from($this->table);	
		$this->db->join('mst_cuti', 'mst_cuti.kode_erp = ess_cuti.absence_type AND mst_cuti.id = ess_cuti.mst_cuti_id', 'left');
				
		
		if($_SESSION["grup"]==4 || $_SESSION["grup"]==5) //jika dia pengguna dan pengadministrasi unit kerja
		{
			$np = $_SESSION["no_pokok"];
			$this->db->where("(approval_1 = '$np' OR approval_2 = '$np')");
		}
		
		if(@$month!=0){
            $this->db->where("DATE_FORMAT(start_Date,'%Y-%m')", $month);
        }
		
		if($filter=='all') //filter all
		{	
			//do nothing
		}else
		if($filter=='0') //filter Menunggu Persetujuan
		{
			$this->db->where("(
								(
									((ess_cuti.status_1='0' OR ess_cuti.status_2='0') AND ess_cuti.approval_1 is not null AND ess_cuti.approval_1!='' AND ess_cuti.approval_2 is not null AND ess_cuti.approval_2!='') /*jika ada approval 1 dan approval 2*/
									OR 
									((ess_cuti.status_1='0') AND (ess_cuti.approval_2 is null OR ess_cuti.approval_2='') AND (ess_cuti.approval_2 is null OR ess_cuti.approval_2='')) /*jika hanya approval 1*/
								) 
								AND (ess_cuti.approval_sdm!='1' AND ess_cuti.approval_sdm!='2') /*belum disetujui sdm dan tidak dibatalkan sdm*/
							)");
			
		}else
		if($filter=='1') //filter Disetujui Atasan
		{
			$this->db->where("(
								(
									((ess_cuti.status_1='1' AND ess_cuti.status_2='1') AND ess_cuti.approval_1 is not null AND ess_cuti.approval_1!='' AND ess_cuti.approval_2 is not null AND ess_cuti.approval_2!='') /*jika ada approval 1 dan approval 2*/
									OR 
									((ess_cuti.status_1='1') AND ess_cuti.approval_2 is null AND ess_cuti.approval_2='') /*jika hanya approval 1*/
								) 
								AND (ess_cuti.approval_sdm!='1' AND ess_cuti.approval_sdm!='2') /*belum disetujui sdm dan tidak dibatalkan sdm*/
							)");
		}else
		if($filter=='2') //filter Disetujui SDM
		{
			$this->db->where("(ess_cuti.approval_sdm='1') /*disetujui SDM*/");
		}else
		if($filter=='3') //filter Ditolak Atasan
		{
			$this->db->where("(ess_cuti.status_1='2' OR ess_cuti.status_2='2') /*ditolak atasan*/");
		}else
		if($filter=='4')  //filter Dibatalkan Pemohon
		{
			$this->db->where("(ess_cuti.status_1='3' OR ess_cuti.status_2='3') /*dibatalkan pemohon*/");
		}else
		if($filter=='5') //filter Ditolak SDM
		{
			$this->db->where("(ess_cuti.approval_sdm='2') /*disetujui SDM*/");
		}
		
		
		
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

	function get_datatables($month=null,$filter='all')
	{
		$this->_get_datatables_query($month,$filter);
		
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered($month=null,$filter='all')
	{
		$this->_get_datatables_query($month,$filter);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($month=null,$filter='all')
	{
		$this->db->from($this->table);
		$this->db->join('mst_cuti', 'mst_cuti.kode_erp = ess_cuti.absence_type', 'left');
		
		if($_SESSION["grup"]==4 || $_SESSION["grup"]==5) //jika dia pengguna dan pengadministrasi unit kerja
		{
			$np = $_SESSION["no_pokok"];
			$this->db->where("(approval_1 = '$np' OR approval_2 = '$np')");
		}
		
		if(@$month!=0){
            $this->db->where("DATE_FORMAT(start_Date,'%Y-%m')", $month);
        }
		
		if($filter=='all') //filter all
		{	
			//do nothing
		}else
		if($filter=='0') //filter Menunggu Persetujuan
		{
			$this->db->where("(
								(
									((ess_cuti.status_1='0' OR ess_cuti.status_2='0') AND ess_cuti.approval_1 is not null AND ess_cuti.approval_1!='' AND ess_cuti.approval_2 is not null AND ess_cuti.approval_2!='') /*jika ada approval 1 dan approval 2*/
									OR 
									((ess_cuti.status_1='0') AND ess_cuti.approval_2 is null AND ess_cuti.approval_2='') /*jika hanya approval 1*/
								) 
								AND (ess_cuti.approval_sdm!='1' OR ess_cuti.approval_sdm!='2') /*belum disetujui sdm dan tidak dibatalkan sdm*/
							)");
			
		}else
		if($filter=='1') //filter Disetujui Atasan
		{
			$this->db->where("(
								(
									((ess_cuti.status_1='1' AND ess_cuti.status_2='1') AND ess_cuti.approval_1 is not null AND ess_cuti.approval_1!='' AND ess_cuti.approval_2 is not null AND ess_cuti.approval_2!='') /*jika ada approval 1 dan approval 2*/
									OR 
									((ess_cuti.status_1='1') AND ess_cuti.approval_2 is null AND ess_cuti.approval_2='') /*jika hanya approval 1*/
								) 
								AND (ess_cuti.approval_sdm!='1' OR ess_cuti.approval_sdm!='2') /*belum disetujui sdm dan tidak dibatalkan sdm*/
							)");
		}else
		if($filter=='2') //filter Disetujui SDM
		{
			$this->db->where("(ess_cuti.approval_sdm='1') /*disetujui SDM*/");
		}else
		if($filter=='3') //filter Ditolak Atasan
		{
			$this->db->where("(ess_cuti.status_1='2' OR ess_cuti.status_2='2') /*ditolak atasan*/");
		}else
		if($filter=='4')  //filter Dibatalkan Pemohon
		{
			$this->db->where("(ess_cuti.status_1='3' OR ess_cuti.status_2='3') /*dibatalkan pemohon*/");
		}else
		if($filter=='5') //filter Ditolak SDM
		{
			$this->db->where("(ess_cuti.approval_sdm='2') /*disetujui SDM*/");
		}
			
		return $this->db->count_all_results();
	}
	
	
}

/* End of file m_perencanaan_jadwal_kerja.php */
/* Location: ./application/models/kehadiran/m_perencanaan_jadwal_kerja.php */