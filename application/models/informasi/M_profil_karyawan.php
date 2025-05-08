<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_profil_karyawan extends CI_Model {
	
	private $table_foto = "foto_karyawan";

	public function __construct(){
		parent::__construct();
		//Do your magic here
		
		$this->tabel = "mst_karyawan";
	}
	
	public function ambil_foto_karyawan($no_pokok){
		$data = $this->db->select("IFNULL(nama_file,'default.jpg') foto",false)
						 ->from($this->table_foto)
						 ->where("no_pokok",$no_pokok)
						 ->get()
						 ->result_array()[0]["foto"];//echo $this->db->last_query();die();

		return $data;
	}

	public function profil_karyawan($np){
		$return = $this->db->select("*")
						   ->from($this->tabel)
						   ->where("no_pokok ", $np)
						   ->get()
						   ->result_array()[0];

		return $return;
	}


}

/* End of file M_payslip.php */
/* Location: ./application/models/kehadiran/M_payslip.php */