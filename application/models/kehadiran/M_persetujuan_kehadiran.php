<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Persetujuan_kehadiran extends CI_Model {

	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	function check_table_exist($name)
	{
		$name=str_replace("-","_",$name);
		$query = $this->db->query("show tables like '$name'")->row_array();
		
		return $query;
	}
	
	function persetujuan_kehadiran($data)
	{
		
		if($data['persetujuan_tahun_bulan']=='')
		{
			$tabel = 'ess_cico';
		}else
		{
			$tabel = 'ess_cico_'.$data['persetujuan_tahun_bulan'];
		}
		
		if($data['status_1']==2) //jika tidak disetujui
		{
			$setuju = array(				
				'tapping_fix_approval_status'	=> $data['status_1'],			
				'tapping_fix_approval_alasan'	=> $data['alasan_1'],
				'tapping_fix_approval_date'		=> date('Y-m-d H:i:s'),				
				'updated_at'					=> date('Y-m-d H:i:s'),
				'updated_by'					=> $this->session->userdata('no_pokok'),
				'wfh'							=> '0'
			);
		}else
		{
			$setuju = array(				
				'tapping_fix_approval_status'	=> '1', //disetujui			
				'tapping_fix_approval_alasan'	=> $data['alasan_1'],
				'tapping_fix_approval_date'		=> date('Y-m-d H:i:s'),
				/*'tapping_fix_1_temp'			=> null,
				'tapping_fix_2_temp'			=> null,
				'tapping_fix_1'					=> $data['tapping_fix_1_temp'],
				'tapping_fix_2'					=> $data['tapping_fix_2_temp'],*/
				'updated_at'					=> date('Y-m-d H:i:s'),
				'updated_by'					=> $this->session->userdata('no_pokok')
			);

			if (($data['tapping_fix_1_temp']!='' && $data['tapping_fix_1_temp']!=null && $data['tapping_fix_1_temp']!='0000-00-00 00:00:00') && ($data['tapping_fix_1']=='' || $data['tapping_fix_1']==null || $data['tapping_fix_1']=='0000-00-00 00:00:00')) {
    			$setuju['tapping_fix_1'] = $data['tapping_fix_1_temp'];
    			$setuju['tapping_fix_1_temp'] = null;
    		}
    		if (($data['tapping_fix_2_temp']!='' && $data['tapping_fix_2_temp']!=null && $data['tapping_fix_2_temp']!='0000-00-00 00:00:00') && ($data['tapping_fix_2']=='' || $data['tapping_fix_2']==null || $data['tapping_fix_2']=='0000-00-00 00:00:00')) {
    			$setuju['tapping_fix_2'] = $data['tapping_fix_2_temp'];
    			$setuju['tapping_fix_2_temp'] = null;
    		}
		}
		
		$this->db->where('id', $data['id']);
		$this->db->update($tabel, $setuju); 
		
		if($this->db->affected_rows() > 0)
		{			
			return $data['id']; 
		}else
		{
			return "0";
		}
	}		
			
	function select_kehadiran_by_id($id,$tahun_bulan)
	{
		if($tampil_bulan_tahun=='')
		{
			$tabel = 'ess_cico';
		}else
		{
			$tabel = 'ess_cico_'.$tahun_bulan;
		}
		
		$this->db->select('*');
		$this->db->from($tabel);	
		$this->db->where('id',$id);
		
		$query = $this->db->get();
		
		return $query->row_array();
	}
	
	function referensi_pamlek_by_tanggal($tanggal,$np_karyawan)
	{
		
		$date_now		= $tanggal;
		$date_kemarin 	= date('Y-m-d', strtotime($tanggal . ' -1 days'));
		$date_besok 	= date('Y-m-d', strtotime($tanggal . ' +1 days'));
		
		$pisah_date_now		= explode('-',$date_now);
		$tahun1				= $pisah_date_now[0];
		$bulan1				= $pisah_date_now[1];
		$tahun_bulan		= $tahun1."_".$bulan1;
		
		$pisah_date_kemarin	= explode('-',$date_kemarin);
		$tahun2				= $pisah_date_kemarin[0];
		$bulan2				= $pisah_date_kemarin[1];
		$tahun_bulan_kemarin= $tahun2."_".$bulan2;
		
		$pisah_date_besok	= explode('-',$date_besok);
		$tahun3				= $pisah_date_besok[0];
		$bulan3				= $pisah_date_besok[1];
		$tahun_bulan_besok	= $tahun3."_".$bulan3;
		
		$tabel_pamlek = 'pamlek_data_'.$tahun_bulan;
		if(!$this->check_table_exist($tabel_pamlek))
		{
			$tabel_pamlek = 'pamlek_data';
		}
		
		$tabel_pamlek_kemarin = 'pamlek_data_'.$tahun_bulan_kemarin;
		if(!$this->check_table_exist($tabel_pamlek_kemarin))
		{
			$tabel_pamlek_kemarin = 'pamlek_data';
		}
		
		$tabel_pamlek_besok = 'pamlek_data_'.$tahun_bulan_besok;
		if(!$this->check_table_exist($tabel_pamlek_besok))
		{
			$tabel_pamlek_besok = 'pamlek_data';
		}
		
		$where 			= "WHERE no_pokok='$np_karyawan' AND tapping_time like '$date_now%'";
		$where_kemarin 	= "WHERE no_pokok='$np_karyawan' AND tapping_time like '$date_kemarin%'";
		$where_besok 	= "WHERE no_pokok='$np_karyawan' AND tapping_time like '$date_besok%'";
		
		
		
		$data = $this->db->query("SELECT d.no_pokok, d.tapping_time, d.in_out,d.machine_id, e.nama FROM 
									(SELECT a.no_pokok, a.tapping_time, a.in_out,a.machine_id, a.tapping_type FROM $tabel_pamlek a $where
									union all
									SELECT b.no_pokok, b.tapping_time, b.in_out,b.machine_id, b.tapping_type FROM $tabel_pamlek_kemarin b $where_kemarin
									union all
									SELECT c.no_pokok, c.tapping_time, c.in_out,c.machine_id, c.tapping_type FROM $tabel_pamlek_besok c $where_besok) d
									left join mst_perizinan e ON d.tapping_type=e.kode_pamlek AND e.status='1' ORDER BY d.tapping_time ASC");
		
		
		
									
		return $data;
		
	}

}

/* End of file m_perencanaan_jadwal_kerja.php */
/* Location: ./application/models/kehadiran/m_perencanaan_jadwal_kerja.php */