<?php
	$this->load->view('theader');
	if($is_with_sidebar){
		$this->load->view('tnav');
	}
	$this->load->view($content);
	$this->load->view('tfooter');
?>