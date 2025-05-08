<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_tabel_persetujuan_kehadiran extends CI_Model {

	var $table = 'ess_cico_';
	var $column_order = array(null, 'kode_unit', 'np_karyawan','nama','dws_tanggal','dws_name','dws_name_fix','tapping_time_1','tapping_fix_1','tapping_time_2','tapping_fix_2'); //set column field database for datatable orderable	
	var $order = array('np_karyawan' => 'asc','dws_tanggal' => 'asc'); // default order 
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	private function _get_datatables_query($var,$tampil_bulan_tahun)
	{		
		if($tampil_bulan_tahun=='' || $tampil_bulan_tahun=='0')
		{
			$tabel = 'ess_cico';
		}else
		{
			$tabel = $this->table."".$tampil_bulan_tahun;
		}
		
		//dipindah kebawah karena tabel nya dinamis
		$column_search = array("$tabel.kode_unit","$tabel.np_karyawan","$tabel.nama","$tabel.dws_name","$tabel.dws_name_fix","$tabel.dws_tanggal","$tabel.tapping_time_1","$tabel.tapping_time_2","$tabel.tapping_fix_1","$tabel.tapping_fix_2"); //set column field database for datatable 
				
		$this->db->select("$tabel.id");		
		$this->db->select("$tabel.kode_unit");
		$this->db->select("$tabel.np_karyawan");
		$this->db->select("$tabel.nama");	
		$this->db->select("$tabel.dws_tanggal");	
		$this->db->select("$tabel.dws_name");
		$this->db->select("$tabel.dws_name_fix");
		$this->db->select("$tabel.tapping_time_1");
		$this->db->select("$tabel.tapping_time_2");
		$this->db->select("$tabel.tapping_fix_1");
		$this->db->select("$tabel.tapping_fix_2");
		
		$this->db->select("$tabel.tapping_fix_approval_status");
		$this->db->select("$tabel.tapping_fix_approval_ket");
		$this->db->select("$tabel.tapping_fix_approval_np");
		$this->db->select("$tabel.tapping_fix_approval_nama");
		$this->db->select("$tabel.tapping_fix_approval_date");
		$this->db->select("$tabel.tapping_fix_1_temp");
		$this->db->select("$tabel.tapping_fix_2_temp");
		
		$this->db->select("$tabel.tapping_terminal_1");
		$this->db->select("$tabel.tapping_terminal_2");
		
		$this->db->select("$tabel.id_perizinan");
		$this->db->select("$tabel.id_cuti");
		$this->db->select("$tabel.id_sppd");
		$this->db->select("$tabel.is_dinas_luar");
		if ($tabel=='ess_cico_2021_02') {
			$this->db->select("$tabel.is_dinas_luar");
		} else {
			$this->db->select("'0' as id_dinas_luar");
		}
		$this->db->select("$tabel.wfh");
		$this->db->select("$tabel.wfh_foto_1");
		$this->db->select("$tabel.wfh_foto_2");
		
		
		//(($tabel.tapping_time_1 IS null || $tabel.tapping_time_1='0000-00-00 00:00:00') && ($tabel.tapping_fix_1 IS null || $tabel.tapping_fix_1='0000-00-00 00:00:00'))		
		//(($tabel.tapping_time_2 IS null || $tabel.tapping_time_2='0000-00-00 00:00:00') && ($tabel.tapping_fix_2 IS null || $tabel.tapping_fix_2='0000-00-00 00:00:00'))		
		//($tabel.tapping_time_1 || $tabel.tapping_fix_1)		
		//($tabel.tapping_time_2 || $tabel.tapping_fix_2)		
		// IF(judges.judge_did_accept = 1 , True , False)
		
		$this->db->select("
		IF(((($tabel.dws_name!='OFF' && ($tabel.dws_name_fix='' || $tabel.dws_name_fix is null)) || $tabel.dws_name_fix!='OFF' && ($tabel.dws_name_fix!='' && $tabel.dws_name_fix is not null))), 
			IF((($tabel.tapping_time_1 IS null || $tabel.tapping_time_1='0000-00-00 00:00:00') && ($tabel.tapping_fix_1 IS null || $tabel.tapping_fix_1='0000-00-00 00:00:00')) && (($tabel.tapping_time_2 IS null || $tabel.tapping_time_2='0000-00-00 00:00:00') && ($tabel.tapping_fix_2 IS null || $tabel.tapping_fix_2='0000-00-00 00:00:00')) && ($tabel.dws_name='OFF' || $tabel.dws_name!='' || $tabel.dws_name is not null), 'AB',
				IF((($tabel.tapping_time_1 IS null || $tabel.tapping_time_1='0000-00-00 00:00:00') && ($tabel.tapping_fix_1 IS null || $tabel.tapping_fix_1='0000-00-00 00:00:00')) && ($tabel.tapping_time_2 || $tabel.tapping_fix_2) , 'TM',
					IF(($tabel.tapping_time_1 || $tabel.tapping_fix_1)&&(($tabel.tapping_time_2 IS null || $tabel.tapping_time_2='0000-00-00 00:00:00') && ($tabel.tapping_fix_2 IS null || $tabel.tapping_fix_2='0000-00-00 00:00:00')), 'TK' , 
					''))) 
		, '') AS keterangan");
				
		
		$this->db->from($tabel);	
		
		//pilih hanya yang ada butuh persetujuan
		$this->db->where("(tapping_fix_1_temp is not null OR tapping_fix_1_temp != '0000-00-00 00:00:00' OR tapping_fix_2_temp is not null OR tapping_fix_2_temp != '0000-00-00 00:00:00') AND tapping_fix_approval_status = '0'");	
				
		if($_SESSION["grup"]==5) //jika Pengguna
		{
			$this->db->where_in('tapping_fix_approval_np', $var);	
		}else
		{
		}			
		
		$this->db->order_by('np_karyawan','ASC');		
		$this->db->order_by("$tabel.dws_tanggal",'ASC');	
				
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

	function get_datatables($var,$tampil_bulan_tahun)
	{
		$this->_get_datatables_query($var,$tampil_bulan_tahun);
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered($var,$tampil_bulan_tahun)
	{
		$this->_get_datatables_query($var,$tampil_bulan_tahun);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($var,$tampil_bulan_tahun)
	{
		if($tampil_bulan_tahun=='' || $tampil_bulan_tahun=='0')
		{
			$tabel = 'ess_cico';
		}else
		{
			$tabel = $this->table."".$tampil_bulan_tahun;
		}
		
		$this->db->from($tabel);	
				
		//pilih hanya yang ada butuh persetujuan
		$this->db->where('((tapping_fix_1_temp is not null OR tapping_fix_1_temp != "0000-00-00 00:00:00") OR (tapping_fix_2_temp is not null OR tapping_fix_2_temp != "0000-00-00 00:00:00")) OR (tapping_fix_approval_status != "2" OR tapping_fix_approval_status != "3")');	
				
		if($_SESSION["grup"]==5) //jika Pengguna
		{
			$this->db->where_in('tapping_fix_approval_np', $var);	
		}else
		{
		}			
		
				
		return $this->db->count_all_results();
	}
	
	
}

/* End of file m_perencanaan_jadwal_kerja.php */
/* Location: ./application/models/kehadiran/m_perencanaan_jadwal_kerja.php */