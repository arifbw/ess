<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_lembur extends CI_Model {

	private $table_lembur = "ess_lembur_transaksi";
	private $table_karyawan = "mst_karyawan";
	private $table_pengguna = "usr_pengguna";
	private $table_cuti = "ess_cuti";
	private $table_master = "erp_master_data_";
	private $table_gilir = "ess_substitution";
	private $table_jadwal_kerja = "mst_jadwal_kerja";
	
	public function __construct(){
		parent::__construct();
	}
	
	public function get_lembur_belum_persetujuan($tanggal_cutoff){
		$this->table_master.=date("Y_m");
		
		$this->db->select("a.approval_pimpinan_np np_pimpinan");
		$this->db->select("a.tgl_dws");
		$this->db->select("count(*) permohonan_per_hari",false);
		$this->db->select("c.username");
		$this->db->select("b.nama");
		$this->db->select("b.jenis_kelamin");
		$this->db->select("b.nama_jabatan");
		$this->db->select("date_add(last_day(a.tgl_dws),INTERVAL $tanggal_cutoff day) batas");
		$this->db->from($this->table_lembur." a");
		$this->db->join($this->table_karyawan." b","a.approval_pimpinan_np=b.no_pokok");
		$this->db->join($this->table_pengguna." c","a.approval_pimpinan_np=c.no_pokok");
		$this->db->join($this->table_cuti." d","a.approval_pimpinan_np=d.np_karyawan and NOW() between d.start_date and d.end_date","left");
		$this->db->join($this->table_master." e","a.approval_pimpinan_np = e.np_karyawan and e.tanggal_start_dws=curdate()","left");
		$this->db->join($this->table_gilir." f","a.approval_pimpinan_np=f.np_karyawan and e.tanggal_start_dws=f.date","left");
		$this->db->join($this->table_jadwal_kerja." g","ifnull(f.dws,e.dws)=g.dws","left");
		$this->db->where("a.approval_status","0");
		$this->db->where("a.approval_pimpinan_status","0");
		$this->db->where("date_add(last_day(a.tgl_dws),INTERVAL $tanggal_cutoff day) >=","NOW()",false);
		$this->db->where("a.tgl_dws <=","NOW()",false);
		$this->db->where("d.id is","NULL",false);
		$this->db->where("NOW() between ","concat(str_to_date(curdate()+g.lintas_hari_masuk,'%Y%m%d'),' ',g.dws_start_time) and concat(str_to_date(curdate()+g.lintas_hari_pulang,'%Y%m%d'),' ',g.dws_end_time)",false);
		$this->db->group_by(array("a.approval_pimpinan_np","a.tgl_dws"));
		$this->db->order_by("a.approval_pimpinan_np","ASC");
		$this->db->order_by("a.tgl_dws","ASC");
		$result = $this->db->get();
		return $result->result_array();
	}
	
}