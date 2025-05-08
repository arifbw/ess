<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_outbound_sto extends CI_Model {
	private $tabel = "ess_sto";
	private $tabel_satuan_kerja = "mst_satuan_kerja";
	private $tabel_satuan_kerja_temp = "mst_satuan_kerja_temp";
	private $tabel_jabatan = "mst_jabatan";
	private $tabel_jabatan_temp = "mst_jabatan_temp";
	
	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	function insert_batch_sto_files($data){
		$this->db->insert_batch('ess_sto_files',$data);
	}
    
    function check_name_then_insert_data($file_name, $data) {
        $cek = $this->db->where('nama_file', $file_name)->get('ess_sto_files')->num_rows();
        if($cek<1){
            $this->db->insert('ess_sto_files',$data);
        }
	}
    
    function get_proses_is_nol(){
        return $this->db->select('nama_file, baris_data')->where('proses',0)->get('ess_sto_files');
    }

	function update_files($nama_file, $data) {
		$this->db->where('nama_file', $nama_file);
		$this->db->update('ess_sto_files', $data); 
	}
	
	function check_id_then_insert_data($data) {
        $cek = $this->db->where("object_type", $data["object_type"])->where("object_id", $data["object_id"])->get($this->tabel)->num_rows();
        if($cek<1){
            $proses = $this->db->insert($this->tabel,$data);
        } else{
            $proses = $this->db->where('object_id', $id)->update('ess_sto',$data);
        }
		
		echo "<br>".$this->db->last_query();
		
		if($proses){
			echo "<br>".__LINE__;
            return true;
        }
		else{
			echo "<br>".__LINE__;
            return false;
        }
	}
	
	function update_satuan_kerja(){
		//Jika ingin Masukin Temp
		$this->db->truncate($this->tabel_satuan_kerja_temp);echo $this->db->last_query()."<br>";
		
		$fields = $this->db->list_fields($this->tabel_satuan_kerja_temp);
		$fields = implode(", ", $fields);
		
		//$this->db->query("INSERT INTO ".$this->tabel_satuan_kerja_temp." ($fields) SELECT object_abbreviation, object_name_lengkap, NOW() FROM ".$this->tabel." WHERE object_type='O' ORDER BY object_abbreviation");echo $this->db->last_query()."<br>";
		//zanna 01-07-2021
		$this->db->query("INSERT INTO ".$this->tabel_satuan_kerja_temp." ($fields) SELECT object_abbreviation, object_name_lengkap FROM ".$this->tabel." WHERE object_type='O' ORDER BY object_abbreviation");echo $this->db->last_query()."<br>";
		

		$count = $this->db->count_all_results($this->tabel_satuan_kerja_temp);echo $this->db->last_query()."<br>";echo $count."<br>";
		
		if($count>0){
			$this->db->truncate($this->tabel_satuan_kerja);echo $this->db->last_query()."<br>";
			
			$fields = $this->db->list_fields($this->tabel_satuan_kerja);
			$fields = implode(", ", $fields);
			
			$this->db->query("INSERT INTO ".$this->tabel_satuan_kerja." ($fields) SELECT $fields FROM ".$this->tabel_satuan_kerja_temp);echo $this->db->last_query()."<br>";
		}
		
		/*
		//jika ingin tanpa temp
		$this->db->truncate($this->tabel_satuan_kerja);echo $this->db->last_query()."<br>";
		
		$fields = $this->db->list_fields($this->tabel_satuan_kerja);
		$fields = implode(", ", $fields);
		
		//$this->db->query("INSERT INTO ".$this->tabel_satuan_kerja." ($fields) SELECT object_abbreviation, object_name_lengkap, NOW() FROM ".$this->tabel." WHERE object_type='O' ORDER BY object_abbreviation");echo $this->db->last_query()."<br>";
		$this->db->query("INSERT INTO ".$this->tabel_satuan_kerja." ($fields) SELECT object_abbreviation, object_name_lengkap FROM ".$this->tabel." WHERE object_type='O' ORDER BY object_abbreviation");echo $this->db->last_query()."<br>";
		

		$count = $this->db->count_all_results($this->tabel_satuan_kerja);echo $this->db->last_query()."<br>";echo $count."<br>";
		*/
		
	}

	function update_jabatan(){
		$this->db->truncate($this->tabel_jabatan_temp);echo $this->db->last_query()."<br>";
		
		$this->db->query("INSERT INTO ".$this->tabel_jabatan_temp." SELECT DISTINCT o.object_abbreviation, s.object_abbreviation, s.object_name_lengkap, CASE WHEN s.object_abbreviation LIKE '%00' THEN '1' ELSE '0' END, NOW() FROM ".$this->tabel." s LEFT JOIN ".$this->tabel." o ON s.up=o.id WHERE s.object_type='S' ORDER BY o.object_abbreviation, s.object_abbreviation ");echo $this->db->last_query()."<br>";

		$count = $this->db->count_all_results($this->tabel_jabatan_temp);echo $this->db->last_query()."<br>";echo $count."<br>";
		
		if($count>0){
			$this->db->truncate($this->tabel_jabatan);echo $this->db->last_query()."<br>";
			
			$this->db->query("INSERT INTO ".$this->tabel_jabatan." SELECT * FROM ".$this->tabel_jabatan_temp);echo $this->db->last_query()."<br>";
		}
		
	}
}
