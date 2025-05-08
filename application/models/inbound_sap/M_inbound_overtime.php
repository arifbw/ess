<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_inbound_overtime extends CI_Model {

	function check_table_exist($name)
	{
		$query = $this->db->query("show tables like '$name'")->row_array();
		
		return $query;
	}
	
	public function select_overtime($date, $np=null)
	{
		$this->db->select('*');
		$this->db->from("ess_lembur_transaksi");
		$this->db->where("tgl_dws",$date);
		$this->db->where("approval_status",'1');
		$this->db->where("waktu_mulai_fix!=",null);
		$this->db->where("waktu_mulai_fix!=",'');
		$this->db->where("waktu_mulai_fix!=",'0000-00-00 00:00:00');
		$this->db->where("waktu_selesai_fix!=",null);
		$this->db->where("waktu_selesai_fix!=",'');
		$this->db->where("waktu_selesai_fix!=",'0000-00-00 00:00:00');
		$this->db->where("is_manual_by_sdm!=",'1');
        if(@$np){
            $this->db->where('no_pokok', $np);
        }
			
		$query = $this->db->get();
		return $query;	
	}
	
	public function select_cico($tahun_bulan,$tanggal_dws,$np_karyawan)
	{
		$tahun_bulan=str_replace("-","_",$tahun_bulan);
		$nama_tabel = 'ess_cico_'.$tahun_bulan;
		
		if(!$this->check_table_exist($nama_tabel))
		{
			$nama_tabel = 'ess_cico';
		}
		
		$this->db->select('*');
		$this->db->from($nama_tabel);
		$this->db->where('dws_tanggal',$tanggal_dws);
		
		$this->db->where('np_karyawan',$np_karyawan);
				
		$this->db->order_by("np_karyawan", "ASC"); 	
		
		$query = $this->db->get();
		return $query;
	}
	
	public function update_overtime($id_overtime)
	{
		return $this->db->set('is_manual_by_sdm', '1')->where('id', $id_overtime)->update('ess_lembur_transaksi');
	}


}

