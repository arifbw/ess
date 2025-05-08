<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Demo_functions extends CI_Controller {
		public function __construct(){
			parent::__construct();
			$this->load->helper('kendaraan');
		}
        
        function nomor_pemesanan(){
            echo generate_nomor_pemesanan();
        }
        
        function uuid_v4(){
            echo $this->uuid->v4();
        }
	}
	
	/* End of file data_kehadiran.php */
	/* Location: ./application/controllers/kehadiran/data_kehadiran.php */