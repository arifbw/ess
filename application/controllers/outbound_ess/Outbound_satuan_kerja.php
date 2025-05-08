<?php
	defined('BASEPATH') OR exit('No direct script access allowed');

	class Outbound_satuan_kerja extends CI_Controller {
		 
		function __construct(){
			parent::__construct();
			//$this->load->helper('satuan_kerja');
			$this->load->model("outbound_ess/m_outbound_satuan_kerja");
			
		}
		
		/* public function index(){
			redirect(base_url('dashboard'));
		} */
		
		public function generate_satuan_kerja($jenis){
			echo "IN FUNCTION :".__FUNCTION__ ."\n";
			echo "PARAMETER : \n";
			echo "Jenis : $jenis\n";
			//run program selamanya untuk menghindari maximal execution
			//ini_set('MAX_EXECUTION_TIME', -1);
			set_time_limit('0');
			
			//$this->output->enable_profiler(TRUE);		
			
			echo "Mulai ambil data satuan_kerja dengan jenis format : $jenis\n";
			echo "mulai ".date('Y-m-d H:i:s')."\n\n";
			
			$data = array();
			
			/*
			jenis = 1 ==> kalender
			jenis = 2 ==> nde
			*/
			$data = $this->m_outbound_satuan_kerja->generate_satuan_kerja($jenis);
			
			$content="";
			
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
			
			if(!empty($content)){
				$this->create_file($jenis,$content);
			}
		}
		
		private function create_file($jenis,$content){
			//include helper file
			$this->load->helper("file");
			
			if ( ! write_file(FCPATH . "outbound_ess/satuan_kerja/$jenis.txt", $content)){
				echo 'Gagal membuat file txt';
			}
			else{
				echo "File outbound_ess/satuan_kerja/$jenis.txt"."\n";
			}

			echo "selesai ".date('Y-m-d H:i:s')."\n";
			
			//insert ke tabel 'ess_status_proses_output', id proses = 12
			//$this->db->insert('ess_status_proses_output', ['id_proses'=>12, 'waktu'=>date('Y-m-d H:i:s')]);
		}
	}
