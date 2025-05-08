<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_provider extends CI_Model {

	private $table="ess_provider_kesehatan";
	
	public function __construct(){
		parent::__construct();
	}

	public function index($kode=null)
	{
		$this->db->from($this->table);
		$this->db->where('aktif', '1');
        
        if(@$kode){
            if($kode!='all'){
                $this->db->where('id_kabupaten', $kode);
            }
        }

		return $this->db->get()->result();
	}

}

?>