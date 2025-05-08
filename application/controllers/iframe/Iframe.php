<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Iframe extends CI_Controller {
		private $data = array();
		public function __construct(){
			parent::__construct();
			
			$this->load->model("m_setting");
			$this->load->model("M_dashboard","dashboard");
			$this->load->model("master_data/m_hari_libur");
						
			$this->load->helper("cutoff_helper");
			$this->load->helper("tanggal_helper");
			$this->load->helper("karyawan_helper");
		
            $this->nama_db = $this->db->database;
			
			$this->data['cutoff_erp_tanggal'] = $this->m_setting->ambil_pengaturan('cutoff_erp_tanggal');
			
			$awal_libur = date_add(date_create(date_add(date_create(date("Y-m-d")),date_interval_create_from_date_string("-1 months"))->format("Y-m-t")),date_interval_create_from_date_string("1 day"))->format("Y-m-d");
			
			if((int)date("j")<=(int)$this->data['cutoff_erp_tanggal']){
				$awal_libur = date_add(date_create($awal_libur),date_interval_create_from_date_string("-1 months"))->format("Y-m-d");
			}
			
			$akhir_libur = date("Y-m-t");
			
			$this->data["hari_libur"] = $this->m_hari_libur->daftar_hari_libur_periode($awal_libur,$akhir_libur);		
		}

		public function index(){
			//$this->output->enable_profiler(TRUE);		 
				
			$username=$this->uri->segment('4');
						
			$ambil_np 		= $this->db->query("SELECT no_pokok FROM usr_pengguna WHERE username='$username'")->row_array();
			$np_karyawan 	= $ambil_np['no_pokok'];
					
			$date_now = date('Y-m-d');
			$pisah_date = explode('-',$date_now);
			$tahun = $pisah_date[0];
			$bulan = $pisah_date[1];
						
			$tahun_bulan = $tahun."-".$bulan;
			
			$checkDateKehadiran = str_replace('-', '_', $tahun_bulan);

			$data_kehadiran = $this->dashboard->getKehadiranNeedApproval_where($np_karyawan,$checkDateKehadiran)->result_array();
			$menunggu_kehadiran = 0;
			foreach($data_kehadiran as $row)
			{
				$menunggu_kehadiran++;				
			} 			
						
			$data_cuti = $this->data['daftar_cuti'] = $this->dashboard->getCutiNeedApproval_where($np_karyawan)->result_array();
			$menunggu_cuti = 0;
			foreach($data_cuti as $row)
			{
				$menunggu_cuti++;				
			} 	
			
			$data_lembur = $this->dashboard->getLemburNeedApproval_where($np_karyawan)->result_array();				
			$menunggu_lembur = 0;
			foreach($data_lembur as $row)
			{
				$menunggu_lembur++;
				
			} 	
			
			$this->data['menunggu_kehadiran'] 	= $menunggu_kehadiran;
			$this->data['menunggu_cuti'] 		= $menunggu_cuti;
			$this->data['menunggu_lembur'] 		= $menunggu_lembur;
			
			$this->load->view('theader');			
			$this->load->view('iframe/iframe_menunggu_persetujuan',$this->data);
		}

	}
	