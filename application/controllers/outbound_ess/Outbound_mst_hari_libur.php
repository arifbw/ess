<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Outbound_mst_hari_libur extends CI_Controller {
		
	function __construct(){
		parent::__construct();
		$this->load->model("outbound_ess/M_outbound_mst_hari_libur", 'libur');
	}
	
	public function generate(){
		$this->load->helper('file');
		$data = $this->libur->all()->result_array();
		$content = "";
		
		$num_rows = count($data);
		$num_cols = count($data[0]);
		
		for($i=0;$i<$num_rows;$i++){
			$j=0;
			foreach($data[$i] as $key=>$value){
				$content .= preg_replace("/\s+/"," ",$value);
				$j++;
				if($j<$num_cols){
					$content .= "\t";
				}
				else{
					$content .= "\r\n";
				}
			}
		}
		
		if( $content!='' ){
			if( !is_dir(FCPATH . "outbound_ess/mst_hari_libur") ) mkdir("outbound_ess/mst_hari_libur", 0775);
			if( write_file(FCPATH . "outbound_ess/mst_hari_libur/mst_hari_libur.txt", $content) ) echo 'OK';
			else echo 'Not created';
		} else echo 'Empty $content';
	}
}
