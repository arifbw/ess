<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_inbound_absence_cuti extends CI_Model {
	
	public function check_table_exist($name)
	{
		$name=str_replace("-","_",$name);
		$query = $this->db->query("show tables like '$name'")->row_array();
		
		return $query;
	}
	
	public function get_data_cuti($date, $np=null){
		$this->db->select('np_karyawan, personel_number, absence_type, start_date, end_date, approval_sdm, approval_sdm_date');
		$this->db->from('ess_cuti');		
		$this->db->where('approval_sdm', '1');
		
		//$this->db->where('DATE(approval_sdm_date)', $date);
		$this->db->where('start_date<=', $date);
		$this->db->where('end_date>=', $date);
		
        if(@$np){
            $this->db->where('np_karyawan', $np);
        }
		$this->db->order_by("np_karyawan", "ASC"); 	
		$this->db->order_by("start_date", "ASC"); 	
		$query = $this->db->get();
		return $query;
	}
	
	public function get_data_pembatalan_cuti($date, $np=null){
		$this->db->select('np_karyawan, personel_number, absence_type, date, date_submit');
		$this->db->from('ess_pembatalan_cuti');		
		
		//$this->db->where('DATE(date_submit)', $date);
        $this->db->where('date', $date);
		
		if(@$np){
            $this->db->where('np_karyawan', $np);
        }
		$this->db->order_by("date", "ASC"); 		
		$query = $this->db->get();
		return $query;
	}
	
	public function get_tanggal_cuti_bersama($tahun)
	{
		$this->db->select('*');
		$this->db->from('mst_cuti_bersama');		
		$this->db->like('tanggal', $tahun); 
		$this->db->order_by("tanggal", "ASC"); 		
		$query = $this->db->get();
		return $query;
	}
	
	//06 01 2021, 7648 - Tri Wibowo, Agar bisa backdate input cuti ketika NP nya berubah
	public function data_karyawan_by_date($tanggal_dws)
	{
		/*
		//$pisah 				= explode('-',$tanggal_dws);
		//$tahun_sekarang		= $pisah[0];
		//$bulan_sekarang		= $pisah[1];
		//$hari_sekarang		= $pisah[2];
		//$tahun_bulan		= $tahun_sekarang."-".$bulan_sekarang;
				
		$master_data = 'mst_karyawan';
		
					
		$this->db->select('*');
		$this->db->from($master_data);		
		//$this->db->where('tanggal_dws', $tanggal_dws); 
		$this->db->order_by("no_pokok", "ASC"); 		
		$query = $this->db->get();
		return $query;
		*/
				
		
		$date_now			= $tanggal_dws;
				
		$pisah_date_now		= explode('-',$date_now);
		$tahun1				= $pisah_date_now[0];
		$bulan1				= $pisah_date_now[1];
		$tahun_bulan		= $tahun1."_".$bulan1;
		
		$master_data = 'erp_master_data_'.$tahun_bulan;				
		if(!$this->check_table_exist($master_data))
		{
			$master_data = 'erp_master_data';
		}		
					
		$this->db->select('*');
		$this->db->from($master_data);		
		$this->db->where('tanggal_dws', $tanggal_dws); 
		$this->db->order_by("np_karyawan", "ASC"); 		
		$query = $this->db->get();
		return $query;
	}
	
	public function get_data_pembayaran($np_karyawan,$date)
	{
		$query = $this->db->query("SELECT * FROM ess_cuti_bersama WHERE np_karyawan='$np_karyawan' AND tanggal_cuti_bersama='$date'")->row_array();
		
		return $query;
	}
	
	public function update_submit_cuti_bersama($id)
	{
		$data = array(
               'submit_erp' => '1',
               'submit_erp_at' => date('Y-m-d H:i:s')              
        );
			
		$this->db->where('id', $id);			
		$this->db->update('ess_cuti_bersama', $data); 
	}
	
	public function insert_submit_cuti_bersama($np_karyawan,$tanggal_cuti_bersama,$jenis_pembayaran)
	{
		$data = array(
		   'np_karyawan' => $np_karyawan,
		   'tanggal_cuti_bersama' => $tanggal_cuti_bersama,
		   'enum' => $jenis_pembayaran,
		   'submit_erp' => '1',
           'submit_erp_at' => date('Y-m-d H:i:s')    
		);

		$this->db->insert('ess_cuti_bersama', $data); 
	}
	
	public function select_cico_by_np_dws($np=null, $dws_tanggal=null)
	{
		//$tahun_bulan	= substr($dws_tanggal,0,7);
		$tahun_bulan	=str_replace("-","_",substr($dws_tanggal,0,7));
		
		$tabel_cico		= "ess_cico_".$tahun_bulan;
		
		if(check_table_exist($tabel_cico)=='belum ada')
		{
			$tabel_cico		= "ess_cico";
		}
				
		$this->db->select('*');
		$this->db->from($tabel_cico);	
		$this->db->where('np_karyawan', $np);
		$this->db->where('dws_tanggal', $dws_tanggal);
		$query = $this->db->get();
		return $query->row_array();
	}
	
	public function check_hari_libur($tanggal)
	{
		$this->db->select('*');
		$this->db->from('mst_hari_libur');	
		$this->db->where('tanggal', $tanggal);
		$query = $this->db->get();
		$libur = $query->row_array();
		
		if($libur['id'])
		{
			return true;
		}else
		{
			return false;
		}
	}
	
}
