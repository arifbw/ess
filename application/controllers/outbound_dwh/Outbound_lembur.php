<?php
	defined('BASEPATH') OR exit('No direct script access allowed');

	class Outbound_lembur extends CI_Controller {
		 
		function __construct(){
			parent::__construct();
			//$this->load->helper('karyawan');
			$this->load->model("outbound_dss/m_outbound_lembur");
			
		}
		
		/* public function index(){
			redirect(base_url('dashboard'));
		} */
		
		public function generate_lembur($tanggal=date("Y-m-d")){
			echo "IN FUNCTION :".__FUNCTION__ ."\n";
			echo "PARAMETER : \n";
			echo "Tanggal : $tanggal\n";
			//run program selamanya untuk menghindari maximal execution
			//ini_set('MAX_EXECUTION_TIME', -1);
			set_time_limit('0');
			
			//$this->output->enable_profiler(TRUE);		
			
			echo "Mulai ambil data lembur dengan tanggal persetujuan atasan : $tanggal\n";
			echo "mulai ".date('Y-m-d H:i:s')."\n\n";
			
			$data = array();
			
			/*
			jenis = 1 ==> kalender, portal
			jenis = 2 ==> sppd
			*/
			$data = $this->m_outbound_lembur->generate_lembur($tanggal);
			
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
		
		private function create_file($tanggal,$content){
			//include helper file
			$this->load->helper("file");
			
			if ( ! write_file(FCPATH . "outbound_dss/lembur/lembur_$tanggal.txt", $content)){
				echo 'Gagal membuat file txt';
			}
			else{
				echo "File outbound_dss/lembur/lembur_$tanggal.txt"."\n";
			}

			echo "selesai ".date('Y-m-d H:i:s')."\n";
			
			//insert ke tabel 'ess_status_proses_output', id proses = 12
			//$this->db->insert('ess_status_proses_output', ['id_proses'=>12, 'waktu'=>date('Y-m-d H:i:s')]);
		}
	}
