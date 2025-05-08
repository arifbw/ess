<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_sto extends CI_Model {

	private $table="sys_isi_menu";
	private $table_modul="sys_modul";
	private $table_master_menu="sys_master_menu";
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	private function ambil_id_induk_isi_menu($id_menu,$urutan){
		$data = $this->db->select("urutan_induk")
						 ->from($this->table)
						 ->where("id_master_menu",$id_menu)
						 ->where("urutan",$urutan)
						 ->get()
						 ->result_array();
		return $data[0]["urutan_induk"];
	}
	
	public function ambil_id_modul_isi_menu_urutan_depan($id_menu,$urutan){
		$hasil = $this->db->select("id_modul")
						  ->from($this->table)
						  ->where("id_master_menu",$id_menu)
						  ->like("urutan",$urutan,"after")
						  ->get()
						  ->result_array();
					
		$arr_hasil = array();
		
		for($i=0;$i<count($hasil);$i++){
			array_push($arr_hasil,$hasil[$i]["id_modul"]);
		}
		
		return $arr_hasil;
	}
	
	public function ambil_id_modul_isi_menu_urutan_induk($id_menu,$urutan){
		$hasil = $this->db->select("id_modul")
						  ->from($this->table)
						  ->where("id_master_menu",$id_menu)
						  ->like("urutan_induk",$urutan,"after")
						  ->get()
						  ->result_array();
		$arr_hasil = array();
		
		for($i=0;$i<count($hasil);$i++){
			array_push($arr_hasil,$hasil[$i]["id_modul"]);
		}
		
		return $arr_hasil;
	}
	
	public function ambil_id_modul_isi_menu_urutan($id_menu,$urutan){
		return $this->db->select("id_modul")
					->from($this->table)
					->where("id_master_menu",$id_menu)
					->where("urutan",$urutan)
					->get()
					->result_array()[0]["id_modul"];
	}

	public function ambil_max_urutan_isi_menu($id_master_menu,$urutan_induk){
		$data = $this->db->select_max("urutan","urutan")
						 ->from($this->table)
						 ->where("id_master_menu",$id_master_menu)
						 ->where("urutan_induk",$urutan_induk)
						 ->get();

		if($data->num_rows()==1){
			$return = $data->result_array()[0]["urutan"];
		}
		else{
			$return = "00";
		}
		return $return;
	}
	
	public function cek_isi_menu_digunakan($id_menu,$isian_menu){
		$data = $this->db->from($this->table)
						 ->where('id_master_menu',$id_menu)
						 ->where('id_modul',$isian_menu)
						 ->get();
		
		if($data->num_rows()>0){
			$return = true;
		}
		else{
			$return = false;
		}
		return $return;
	}
	
	public function cek_hasil_simpan_isi_menu($id_menu,$isian_menu,$level,$induk,$urutan){
		$data = $this->db->from($this->table)
						 ->where('id_master_menu',$id_menu)
						 ->where('id_modul',$isian_menu)
						 ->where('level',$level)
						 ->where('urutan_induk',$induk)
						 ->where('urutan',$urutan)
						 ->get();//echo $this->db->last_query();
		
		if($data->num_rows()==1){
			$return = true;
		}
		else{
			$return = false;
		}
		return $return;
	}

	public function daftar_pengaturan_menu($id_menu){
		$data = $this->db->select("i.id, i.level, i.object_id as urutan_induk, i.sequence as urutan, i.object_name, i.object_type, MAX(p.sequence) as sebelum, MIN(n.sequence) as setelah")
						 ->from('ess_sto i')
						 ->join('ess_sto p', "i.object_id=p.object_id AND i.sequence > p.sequence", "left")
						 ->join('ess_sto n', "i.object_id=n.object_id AND i.sequence < n.sequence", "left")
						 ->group_by("i.id")
						 ->order_by("i.sequence asc")
						 ->get()
						 ->result_array();//echo $this->db->last_query();
		return $data;
	}
	
	public function data_isi_menu($id_menu,$urutan){
		$data = $this->db->select("i.*")
						 ->select("m.*")
						 ->from($this->table_master_menu." mm")
						 ->join($this->table." i", "i.id_master_menu = mm.id", "left")
						 ->join($this->table_modul." m", "i.id_modul = m.id AND m.status=1", "left")
						 ->where("i.id_master_menu",$id_menu)
						 ->where("i.urutan",$urutan)
						 ->group_by("i.id_modul")
						 ->order_by("i.urutan asc")
						 ->get()
						 ->result_array()[0];
		return $data;
	}
	
	public function hapus($id_menu,$urutan){
		$urutan_induk = $this->ambil_id_induk_isi_menu($id_menu,$urutan);
		$this->db->where("id_master_menu",$id_menu)
				 ->where("urutan",$urutan)
				 ->where("urutan_induk",$urutan_induk)
				 ->delete($this->table);
		
		$this->db->set("urutan","concat(substr(urutan,1,".(strlen($urutan)-2)."),LPAD(CAST(substr(urutan,".(strlen($urutan)-1).",2) AS UNSIGNED)-1,2,0),substr(urutan,".(strlen($urutan)+1)."))",false)
				 ->where("id_master_menu",$id_menu)
				 ->where("urutan >",$urutan)
				 ->like("urutan_induk",$urutan_induk,"after")
				 ->update($this->table);
				 
		$this->db->set("urutan_induk","concat(substr(urutan_induk,1,".(strlen($urutan)-2)."),LPAD(CAST(substr(urutan_induk,".(strlen($urutan)-1).",2) AS UNSIGNED)-1,2,0))",false)
				 ->where("id_master_menu",$id_menu)
				 ->where("urutan >=",$urutan)
				 ->where("length(urutan_induk) >",strlen($urutan)-2)
				 ->like("urutan_induk",$urutan_induk,"after")
				 ->update($this->table);
	}
	
	public function hapus_isi_menu($data){
		$this->db->delete($this->table,$data);
	}
	
	public function hitung_sub_menu($id_menu,$urutan){
		$data = $this->db->from($this->table)
						 ->where("id_master_menu",$id_menu)
						 ->where("urutan_induk",$urutan)
						 ->get()
						 ->num_rows();//echo $this->db->last_query()."<br>";
		return $data;
	}
	
	public function simpan_isi_menu($data){
		$this->db->insert($this->table,$data);
	}
	
	public function ubah_isi_menu($set,$id_menu,$level,$induk,$isian_menu_lama){
		$this->db->where('id_master_menu',$id_menu)
				 ->where('level',$level)
				 ->where('urutan_induk',$induk)
				 ->where('id_modul',$isian_menu_lama)
				 ->update($this->table,$set);
	}
	
	public function ubah_urutan_isi_menu($set,$id_menu,$id_modul){
		$this->db->where("id_master_menu",$id_menu)
				 ->where("id_modul",$id_modul)
				 ->update($this->table,$set);
	}
	
	public function ubah_urutan_induk_isi_menu($urutan,$id_menu,$arr_id_modul_urutan_induk){
		$this->db->set("urutan_induk","concat('".$urutan."',substr(urutan_induk,".(strlen($urutan)+1)."))",false)
				 ->where("id_master_menu",$id_menu)
				 ->where_in("id_modul",$arr_id_modul_urutan_induk)
				 ->update($this->table);
	}
	
	public function ubah_urutan_isi_menu_masal($id_menu,$urutan,$arr_id_modul_urutan_depan){// belom selesai
		$this->db->set("urutan","concat('".$urutan."',substr(urutan,".(strlen($urutan)+1)."))",false)
					 ->where("id_master_menu",$id_menu)
					 ->where_in("id_modul",$arr_id_modul_urutan_depan)
					 ->update($this->table);
	}
}

/* End of file m_isi_menu.php */
/* Location: ./application/models/administrator/m_isi_menu.php */