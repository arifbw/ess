<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_akses extends CI_Model {

	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	public function ambil_akses($id_grup_pengguna){
		$data = $this->db->select("pm.shortcode shortcode_posisi_menu")
						 ->select("mm.id id_master_menu")
						 ->from("posisi_menu pm")
						 ->join("menu_grup_pengguna mgp","pm.id=mgp.id_posisi_menu AND mgp.id_grup_pengguna='$id_grup_pengguna'","left")
						 ->join("master_menu mm","mgp.id_master_menu=mm.id AND mgp.id_grup_pengguna='$id_grup_pengguna'","left")
						 ->get()
						 ->result_array();
		return $data;
	}
}

/* End of file m_akses.php */
/* Location: ./application/models/m_akses.php */