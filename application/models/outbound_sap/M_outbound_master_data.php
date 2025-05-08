<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_outbound_master_data extends CI_Model {		
	private $tabel_master_karyawan = "mst_karyawan";
	private $tabel_poh = "poh";
	function insert_batch_sto_files($data){
		$this->db->insert_batch('erp_master_data_files',$data);
	}
    
    function check_name_then_insert_data($file_name, $data) {
        $cek = $this->db->where('nama_file', $file_name)->get('erp_master_data_files')->num_rows();
        if($cek<1){
            $this->db->insert('erp_master_data_files',$data);
        }
	}
    
    function get_proses_is_nol(){
        return $this->db->select('nama_file, baris_data')->where('proses',0)->get('erp_master_data_files');
    }

	function update_files($nama_file, $data) {
		$this->db->where('nama_file', $nama_file);
		$this->db->update('erp_master_data_files', $data); 
	}
	
	function check_id_then_insert_data($data) { //$np_karyawan, $tanggal_dws, 
//        if(validateDate($data["tanggal_dws"])==true){
		$tahun_bulan = substr(str_replace('-','_',$data["tanggal_dws"]), 0, 7);
		
		$this->db->query("CREATE TABLE IF NOT EXISTS erp_master_data_$tahun_bulan LIKE erp_master_data");

		$where = [
			'np_karyawan'=>$data["np_karyawan"],
			'tanggal_dws'=>$data["tanggal_dws"]
		];
		$cek = $this->db->where($where)->get('erp_master_data_'.$tahun_bulan)->num_rows();
		if($cek<1){
			$proses = $this->db->insert('erp_master_data_'.$tahun_bulan, $data);
		} else{
			$proses = $this->db->where($where)->update('erp_master_data_'.$tahun_bulan, $data);
		}
		echo $this->db->last_query()."\n\n";

		if($proses){
			return true;
		}
		else{
			return false;
		}
//        } else{
//            return false;
//        }
	}
	
	
	function update_master_karyawan($bulan_tahun){
		//$tabel_master_data = "erp_master_data_".date("Y_m");
		//$tabel_master_data = "erp_master_data_".str_replace("-","_",substr($tanggal,0,7));
		$tabel_master_data = "erp_master_data_".$bulan_tahun;
		
		// select max tanggal dws in
		$tanggal_start_dws = $this->max_tanggal_dws($bulan_tahun);
		
		// ambil nama kolom
		$fields_copy = $this->db->query("SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_NAME='".$this->tabel_master_karyawan."' AND COLUMN_NAME NOT LIKE '%poh'")->result_array();
		echo "\n".$this->db->last_query()."\n";
		$fields_copy = array_column($fields_copy,"COLUMN_NAME");
		//$fields_copy = $this->db->list_fields($this->tabel_master_karyawan);
		$fields_copy = implode(", ", $fields_copy);
		$fields_ori = str_replace("no_pokok","np_karyawan",$fields_copy);
		
		// copy ke tabel mst_karyawan
		$this->db->trans_start();
			$this->db->truncate($this->tabel_master_karyawan);
			echo "\n".$this->db->last_query()."\n";
			
			$this->db->query("INSERT INTO ".$this->tabel_master_karyawan." ($fields_copy) SELECT $fields_ori FROM $tabel_master_data WHERE tanggal_start_dws = '$tanggal_start_dws' ORDER BY personnel_number");
			echo "\n".$this->db->last_query()."\n";
			
			$this->update_poh_master_karyawan();
		$this->db->trans_complete();
		
		$this->update_masa_kerja();
	}
	
	function update_poh_master_karyawan(){
		$this->db->query("UPDATE ".$this->tabel_master_karyawan." a JOIN ".$this->tabel_poh." b on a.no_pokok=b.np_poh and b.tanggal_mulai<=now() and b.tanggal_selesai>=now() set a.kode_unit_poh=b.kode_unit, a.nama_unit_poh=b.nama_unit, a.kode_jabatan_poh=b.kode_jabatan, a.nama_jabatan_poh=concat('POH ',b.nama_jabatan)");
		echo "\n".$this->db->last_query()."\n";
	}
	
	function update_masa_kerja(){
		$this->db->trans_start();
			$this->db->query("UPDATE ".$this->tabel_master_karyawan." SET masa_kerja_tahun=year(now())-year(tanggal_masuk), masa_kerja_bulan=month(now())-month(tanggal_masuk), masa_kerja_hari=day(now())-day(tanggal_masuk)");
			$this->db->query("UPDATE ".$this->tabel_master_karyawan." SET masa_kerja_hari=masa_kerja_hari+date_format(date_sub(str_to_date(concat(date_format(tanggal_masuk,'%Y-%m'),'-01'),'%Y-%m-%d'), INTERVAL 1 day),'%d'), masa_kerja_bulan=masa_kerja_bulan-1 WHERE masa_kerja_hari<0");
			$this->db->query("UPDATE ".$this->tabel_master_karyawan." SET masa_kerja_bulan=masa_kerja_bulan+12, masa_kerja_tahun=masa_kerja_tahun-1 WHERE masa_kerja_bulan<0");
		$this->db->trans_complete();
	}
	
	function max_tanggal_dws($bulan_tahun){
		//$tabel_master_data = "erp_master_data_".date("Y_m");
		//$tabel_master_data = "erp_master_data_".str_replace("-","_",substr($tanggal,0,7));
		$tabel_master_data = "erp_master_data_".$bulan_tahun;
		$tabel_master_karyawan = "mst_karyawan";
		
		// select max tanggal dws in
		$result = $this->db->select_max("tanggal_start_dws")
						   ->from($tabel_master_data)
						   ->get()
						   ->row_array();
		return $result["tanggal_start_dws"];
	}
	
	function update_hari_libur_dws_off($bulan_tahun){
		$tabel_master_data = "erp_master_data_".$bulan_tahun;

		$this->db->where("start_time","00:00:00");
		$this->db->where("end_time","00:00:00");
		$this->db->update($tabel_master_data, array("dws"=>"OFF")); 
	}
	
	/* function tambah_cuti_besar(){
		$tabel_master_data = "erp_master_data_".date("Y_m");
		$tabel_master_karyawan = "mst_karyawan";
		
		// select max tanggal dws in
		$result = $this->db->select_max("tanggal_start_dws")
						   ->from($tabel_master_data)
						   ->get()
						   ->row_array();
		return $result["tanggal_start_dws"];
	} */
}
