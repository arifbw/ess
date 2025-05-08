<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_tabel_sppd extends CI_Model {

	var $table = 'ess_sppd';
	var $column_order = array(null, 'ess_sppd.np_karyawan', null, null, null, 'ess_sppd.tgl_berangkat', 'ess_sppd.tgl_pulang'); //set column field database for datatable orderable
	var $column_search = array('ess_sppd.np_karyawan', 'ess_sppd.nama', 'ess_sppd.perihal', 'ess_sppd.tipe_perjalanan', 'ess_sppd.tgl_berangkat', 'ess_sppd.tgl_pulang'); //set column field database for datatable searchable 
	var $order = array('ess_sppd.tgl_berangkat' => 'desc'); // default order 
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	private function _get_datatables_query($var=null, $month=null, $np=null, $jenis_perjalanan=null)
	{
        $this->db->select('ess_sppd.*');

        if(@$month!=0){
            $this->db->where("DATE_FORMAT(ess_sppd.tgl_berangkat,'%Y-%m')", $month);
        }
        if(@$np!=''){
            $this->db->where("ess_sppd.np_karyawan", $np);
        }
		if(@$jenis_perjalanan!=''){
            $this->db->where("ess_sppd.catatan", $jenis_perjalanan);
        } 
        $this->db->from($this->table);
        //$this->db->join('mst_karyawan', 'mst_karyawan.no_pokok = ess_sppd.id_user', 'left');
        /*else{
            $this->db->select('ess_sppd.*');
            $this->db->where('YEAR(STR_TO_DATE(tgl_selesai, "%Y-%m-%d")) <=', date('Y'));
            $this->db->where('YEAR(STR_TO_DATE(tgl_selesai, "%Y-%m-%d")) >=', date('Y')-1);
            $this->db->from($this->table);
            $this->db->join('mst_karyawan', 'mst_karyawan.no_pokok = ess_sppd.id_user', 'left');
        }*/
		
		/*$this->db->join('mst_karyawan', 'mst_karyawan.no_pokok = ess_sppd.np_karyawan', 'left');
		$this->db->join('mst_cuti', 'mst_cuti.kode_erp = ess_sppd.absence_type', 'left');*/
				
		if($_SESSION["grup"]==4) //jika Pengadministrasi Unit Kerja
		{
			$this->db->where_in('ess_sppd.kode_unit', $var);								
		} else if($_SESSION["grup"]==5) //jika Pengguna
		{
			$this->db->where_in('ess_sppd.np_karyawan', $var);	
		} else
		{
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

	function get_datatables($var=null, $month=null, $np=null, $jenis_perjalanan=null)
	{
		$this->_get_datatables_query($var, $month, $np, $jenis_perjalanan);
		
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered($var=null, $month=null, $np=null, $jenis_perjalanan=null)
	{
		$this->_get_datatables_query($var, $month, $np, $jenis_perjalanan);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($var=null, $month=null, $np=null, $jenis_perjalanan=null)
	{
        $this->_get_datatables_query($var, $month, $np, $jenis_perjalanan);
//		$this->db->from($this->table);
//		$this->db->join('mst_karyawan', 'mst_karyawan.no_pokok = ess_sppd.id_user', 'left');
//		
//		if($_SESSION["grup"]==4) //jika Pengadministrasi Unit Kerja
//		{
//			$this->db->where_in('mst_karyawan.kode_unit', $var);								
//		}else
//		if($_SESSION["grup"]==5) //jika Pengguna
//		{
//			$this->db->where_in('mst_karyawan.no_pokok', $var);	
//		}else
//		{
//		}	
			
		return $this->db->count_all_results();
	}
	
	
}

/* End of file m_perencanaan_jadwal_kerja.php */
/* Location: ./application/models/kehadiran/m_perencanaan_jadwal_kerja.php */