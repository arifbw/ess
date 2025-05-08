<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
 
include_once APPPATH."/third_party/fpdf/fpdf.php";

class Fpdf_lib extends FPDF {
    
    /* function fpdf_lib()
    {
        $CI = & get_instance();
        log_message('Debug', 'FPDF class is loaded.');
    } */
 
    function get_width(){
		return $this->w;
	}
 
    function get_height(){
		return $this->h;
	}
	
	function get_margin($position){
		$return = 0;
		if(strcmp($position,"l")==0){
			$return = $this->lMargin;
		}
		if(strcmp($position,"r")==0){
			$return = $this->rMargin;
		}
		if(strcmp($position,"t")==0){
			$return = $this->tMargin;
		}
		if(strcmp($position,"b")==0){
			$return = $this->bMargin;
		}
		return $return;
	}

}
?>