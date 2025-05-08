<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_persetujuan_cuti_sdm extends CI_Model {

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
	
	//check apakah ada data cuti
	function data_cuti($np_karyawan,$date)
	{
		$this->db->select('*');
		$this->db->from('ess_cuti');
	
		$this->db->where('np_karyawan',$np_karyawan);
		$this->db->where('start_date<=',$date);		
		$this->db->where('end_date>=',$date);
		
		$data = $this->db->get();
		
		return $data;
	}
	
	
	//untuk update cico di id_cuti
	function update_cico_cuti($np_karyawan,$date)
	{
		$bulan = substr($date,5,2);
		$tahun = substr($date,0,4);
		
		$tabel_cico = "ess_cico_".$tahun."_".$bulan;
		
		if(!$this->check_table_exist($tabel_cico))
		{
			$tabel_cico = 'ess_cico';
		}
		
		//ambil cuti yang sudah di batalkan		
		$query1 = $this->db->query("SELECT
										id_cuti
									FROM
										ess_pembatalan_cuti
									WHERE
										np_karyawan = '$np_karyawan'
									AND date = '$date'");
		$query1_result = $query1->result();
		$batal= array();
		foreach($query1_result as $row){
			$batal[] = $row->id_cuti;
		}
		$gabung = implode(",",$batal);
		$id_batal = explode(",", $gabung);	
								
		$set = $this->db->from($tabel_cico)
	 			->where('dws_tanggal', $date)
	 			->where('np_karyawan', $np_karyawan)
	 			->get();
		$get_all = $this->db->from('ess_cuti')
	 			->where('np_karyawan', $np_karyawan)
	 			->where('start_date<=', $date)
				->where('end_date>=', $date)
				->where('approval_sdm', '1')
				->where_not_in('id', $id_batal) //where not id_cuti yang sudah di batalkan
	 			->get()->result_array();
			
	 	if ($set->num_rows() == 1) {
	 		$cico = $set->row_array();
	 		$id_cuti = implode(",", array_column($get_all, 'id'));;
	 		$this->db->set('id_cuti', $id_cuti)->where('id', $cico['id'])->update($tabel_cico);
	 	}
		
		if($this->db->affected_rows() > 0)
			return true; 
		else
			return false;
			
	}
/*	
	//delete field cico di id_cuti
	function delete_cico_cuti($np_karyawan,$start_date, $end_date,$delete)
	{
		$date = $start_date;
		
		while (strtotime($date) <= strtotime($end_date)) {
			
			$bulan = substr($date,5,2);
			$tahun = substr($date,0,4);
			
			$tabel_cico = "ess_cico_".$tahun."_".$bulan;
			
			$ambil_cico = $this->db->query("SELECT id_cuti FROM $tabel_cico WHERE np_karyawan='$np_karyawan' AND dws_tanggal='$date'")->row_array();
			$ambil = $ambil_cico['id_cuti'];
			
			$cuti=explode(",",$ambil);
			
			$data_sama='0';
			$array = array();
			foreach($cuti as $val)
			{
				
				array_push($array,$val);
				
				
				if($val==$delete)
				{
					$data_sama='1';
				}
				
			}
			
			if($data_sama=='1')
			{
				if (($key = array_search($delete, $array)) !== false) {
						unset($array[$key]);
					}
			}
			
			//hapus data kosong
			if (($key = array_search('', $array)) !== false) {
				unset($array[$key]);
			}
			
			$id_cuti=implode(',',$array);
			
			$data_update = array(               
					'id_cuti' => $id_cuti,
					);
			
			$this->db->where('np_karyawan',$np_karyawan);
			$this->db->where('dws_tanggal',$date);		
			
			$this->db->update($tabel_cico, $data_update); 
			
			
			 $date = date ("Y-m-d", strtotime("+1 day", strtotime($date)));
		}
	
	}
*/	
	
	
	
	
	
	
	function persetujuan_cuti_sdm($data)
	{
		
		$setuju = array(				
			'approval_sdm'		=> $data['approval_sdm'],
			'alasan_sdm'		=> $data['alasan_sdm'],
			'approval_sdm_by'	=> $data['approval_sdm_by'],			
			'approval_sdm_date'	=> date('Y-m-d H:i:s'),
			'updated_at'		=> date('Y-m-d H:i:s'),
			'updated_by'		=> $this->session->userdata('no_pokok')
		);
		
		$this->db->where('id', $data['id']);
		$this->db->update('ess_cuti', $setuju); 
		
		$id = $data['id'];
		if($this->db->affected_rows() > 0)
		{			
			$data_cuti 		= $this->select_cuti_by_id($id);
			$np_karyawan	= $data_cuti['np_karyawan'];
			$start_date		= $data_cuti['start_date'];
			$end_date		= $data_cuti['end_date'];
			
			
			$tanggal_proses=$start_date;
			while (strtotime($tanggal_proses) <= strtotime($end_date))
			{
				$this->update_cico_cuti($np_karyawan,$tanggal_proses);
				
				$tanggal_proses = date ("Y-m-d", strtotime("+1 day", strtotime($tanggal_proses)));//looping tambah 1 date
			}
			
			return $id; 
		}else
		{
			return "0";
		}
	}		
		
	function select_cuti_by_id($id)
	{
		$this->db->select('*');
		$this->db->from('ess_cuti');	
		$this->db->where('id',$id);
		
		$query = $this->db->get();
		
		return $query->row_array();
	}
	
	function select_cuti_siap_approve_all($tahun_bulan)
	{
		$this->db->select('*');
		$this->db->from('ess_cuti');
		$this->db->like('start_date', $tahun_bulan); 
		$this->db->where('status_1!=','2');
		$this->db->where('status_1!=','3');
		$this->db->where('status_2!=','2');
		$this->db->where('status_2!=','3');
		
		$where = "(status_1='1' AND (approval_2='' || approval_2 IS NULL)) OR (status_1='1' AND status_2='1')";
		$this->db->where($where);
		
		
		$this->db->where("approval_sdm='0' OR approval_sdm='' OR approval_sdm is null");	

		$query = $this->db->get();
		
		return $query;
	}
	
	function select_jatah_cubes($np_karyawan,$date_cuti)
	{
		$this->db->select('*');
		$this->db->from('cuti_cubes_jatah');	
		$this->db->where('np_karyawan',$np_karyawan);
		$this->db->where('tanggal_timbul<=',$date_cuti);
		$this->db->where('tanggal_kadaluarsa>=',$date_cuti);
		$this->db->limit(1);
				
		$query = $this->db->get();
		
		return $query->row_array();
	}
	
	function update_jatah_cubes($data_update)
	{
		$this->db->where('id', $data_update['id']);
		$this->db->update('cuti_cubes_jatah', $data_update); 
	}
	
}

/* End of file m_persetujuan_cuti_sdm.php */
/* Location: ./application/models/osdm/m_persetujuan_cuti_sdm.php */