<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Home_new extends CI_Controller {
		private $data = array();
		public function __construct(){
			parent::__construct();
			
			$this->load->model("m_setting");
			$this->load->model("M_dashboard","dashboard");
			$this->load->model("master_data/m_hari_libur");
			$this->load->model("informasi/new/m_data_keterlambatan");

			if(empty($this->session->userdata("username"))){
				redirect(base_url($this->m_setting->ambil_url_modul("login")));
			}
			
			$this->load->helper("cutoff_helper");
			$this->load->helper("tanggal_helper");
			$this->load->helper("karyawan_helper");

			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
            
            $this->nama_db = $this->db->database;
			
			$this->data['cutoff_erp_tanggal'] = $this->m_setting->ambil_pengaturan('cutoff_erp_tanggal');
			
			$awal_libur = date_add(date_create(date_add(date_create(date("Y-m-d")),date_interval_create_from_date_string("-1 months"))->format("Y-m-t")),date_interval_create_from_date_string("1 day"))->format("Y-m-d");
			
			if((int)date("j")<=(int)$this->data['cutoff_erp_tanggal']){
				$awal_libur = date_add(date_create($awal_libur),date_interval_create_from_date_string("-1 months"))->format("Y-m-d");
			}
			
			$akhir_libur = date("Y-m-t");
			
			$this->data["hari_libur"] = $this->m_hari_libur->daftar_hari_libur_periode($awal_libur,$akhir_libur);

			$this->data['is_with_sidebar'] = true;
		}

		public function index(){
			coming_soon();
			//$this->output->enable_profiler(TRUE);
			$np = $this->session->userdata('no_pokok');
			$this->session->set_userdata('tampil_np_karyawan', $np);

			//ambil tahun bulan tabel yang tersedia
			$array_tahun_bulan = array();

			$query = $this->db->query("SELECT table_name FROM information_schema.tables WHERE table_schema='$this->nama_db' AND table_name like 'ess_cico_%' group by table_name");
			foreach ($query->result_array() as $data) 
			{					
				$bulan = substr($data['table_name'],-2);
				$tahun = substr($data['table_name'],9,4);
				
				$tahun_bulan = $tahun."-".$bulan;				
				
				$array_tahun_bulan[] = $tahun_bulan;
				$this->session->set_userdata('tampil_tahun_bulan', $tahun_bulan);
			}

			if($_SESSION["grup"]==4) { //jika Pengadministrasi Unit Kerja
				$var=array();
				$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
				foreach ($list_pengadministrasi as $data) { //looping list_pengadministrasi
					array_push($var, $data['kode_unit']);
				}				
				$this->data['np_karyawan'] = $this->dashboard->getKaryawan($var);
			}
			else if($_SESSION["grup"]==5) { //jika Pengguna
				$this->data['np_karyawan'] = $this->dashboard->getKaryawan($np);
			}
			else{
				$this->data['np_karyawan'] = $this->dashboard->getKaryawan();
			}

			$this->data['array_tahun_bulan'] = $array_tahun_bulan;

			$checkDateKehadiran = str_replace('-', '_', $this->session->userdata('tampil_tahun_bulan'));
//			if($this->dashboard->checkDateKehadiran(str_replace('-', '_', $this->session->userdata('tampil_tahun_bulan')))){
//				$checkDateKehadiran = 'tidak ada';
//			}
			
			//$this->data['hari_libur'] = $this->m_hari_libur->getTotalCuti_where('both', $value, $checkDateKehadiran);
			$value = array($this->session->userdata('tampil_np_karyawan'), $this->session->userdata('tampil_tahun_bulan'));

			//REKAP TELAT
			$bulan = array('01','02','03','04','05','06','07','08','09','10','11','12');
			$tahun_periode = $tahun;
			if($tahun_periode==date('Y'))
				$hitung = '9';
				// $hitung = date('n');
			else
				$hitung = count($bulan);

			for($i=0; $i<$hitung; $i++) {
				$periode = $tahun_periode.'_'.$bulan[$i];
				$list[$i] = $this->m_data_keterlambatan->hitung_rekap_keterlambatan($value,$periode);
			}

			$return_telat = 0;
			for($i=0; $i<$hitung; $i++) {
		        foreach ($list[$i] as $row => $field) {
		        	$return_telat = $list[$i][$row]['jml'] + $return_telat;
		        }
			}
			
			$this->data['total_cuti'] = $this->dashboard->getTotalCuti_where('both', $value, $checkDateKehadiran);
			$this->data['total_lembur'] = $this->dashboard->getTotalLembur_where('both', $value, $checkDateKehadiran);
			$this->data['total_izin'] = $this->dashboard->getTotalIzin_where('both', $value, $checkDateKehadiran);
			$this->data['total_dinas'] = $this->dashboard->getTotalDinas_where('both', $value, $checkDateKehadiran);
			$this->data['total_terlambat'] = $return_telat;
			// $this->data['total_terlambat'] = $this->dashboard->getTotalTerlambat_where('both', $value, $checkDateKehadiran);
			$this->data['grafik_kehadiran'] = $this->dashboard->getGrafikKehadiran_where('both', $value, $checkDateKehadiran);
			$this->data['daftar_kehadiran'] = $this->dashboard->getKehadiranNeedApproval_where($this->session->userdata('tampil_np_karyawan'),$checkDateKehadiran)->result_array();
			$this->data['daftar_cuti'] = $this->dashboard->getCutiNeedApproval_where($this->session->userdata('tampil_np_karyawan'))->result_array();
			$this->data['daftar_lembur'] = $this->dashboard->getLemburNeedApproval_where($this->session->userdata('tampil_np_karyawan'))->result_array();
			
			if($_SESSION["grup"]==4) { //jika Pengadministrasi Unit Kerja
				$this->data['list_pengadministrasi'] = $_SESSION["list_pengadministrasi"];
			}	
			
			$this->data['content'] = 'dashboard_new';
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['is_tnav'] = true;
			$this->load->view('template',$this->data);
		}

		public function semua(){
			$np = $this->session->userdata('no_pokok');
			$this->session->unset_userdata('tampil_np_karyawan');
			$this->session->unset_userdata('tampil_tahun_bulan');

			//ambil tahun bulan tabel yang tersedia
			$array_tahun_bulan = array();
			$query = $this->db->query("SELECT table_name FROM information_schema.tables WHERE table_schema='$this->nama_db' AND table_name like '%ess_cico_%';");
			foreach ($query->result_array() as $data) 
			{					
				$bulan = substr($data['table_name'],-2);
				$tahun = substr($data['table_name'],9,4);
				
				$tahun_bulan = $tahun."-".$bulan;				
				
				$array_tahun_bulan[] = $tahun_bulan; 
			}

			if($_SESSION["grup"]==4) { //jika Pengadministrasi Unit Kerja
				$var=array();
				$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
				foreach ($list_pengadministrasi as $data) { //looping list_pengadministrasi
					array_push($var, $data['kode_unit']);
				}				
				$this->data['np_karyawan'] = $this->dashboard->getKaryawan($var);
			}else if($_SESSION["grup"]==5) { //jika Pengguna
				$this->data['np_karyawan'] = $this->dashboard->getKaryawan($np);
			}else{
				$this->data['np_karyawan'] = $this->dashboard->getKaryawan();
			}

			$this->data['array_tahun_bulan'] = $array_tahun_bulan;
			$this->data['total_cuti'] = $this->dashboard->getTotalCuti();
			$this->data['total_lembur'] = $this->dashboard->getTotalLembur();
			$this->data['total_izin'] = $this->dashboard->getTotalIzin();
			$this->data['total_dinas'] = $this->dashboard->getTotalDinas();
			$this->data['daftar_cuti'] = $this->dashboard->getCutiNeedApproval()->result_array();
			$this->data['daftar_lembur'] = $this->dashboard->getLemburNeedApproval()->result_array();
			$this->data['grafik_kehadiran'] = $this->dashboard->getGrafikKehadiran($checkDateIzin);
			$this->data['content'] = 'dashboard';
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['is_tnav'] = true;
			$this->load->view('template',$this->data);
		}

		public function filter_np_karyawan($np_karyawan = null)
		{
			$np = $this->session->userdata('no_pokok');
			if (empty($np_karyawan)) {
				$this->session->unset_userdata('tampil_np_karyawan');
				if(!empty($this->session->userdata('tampil_tahun_bulan'))){
					redirect(base_url('home/filter_tahun_bulan/').$this->session->userdata('tampil_tahun_bulan'));
				}else{
					redirect(base_url('home/semua'));
				}
			}
			$this->session->set_userdata('tampil_np_karyawan', $np_karyawan);

			//ambil tahun bulan tabel yang tersedia
			$array_tahun_bulan = array();
			$query = $this->db->query("SELECT table_name FROM information_schema.tables WHERE table_schema='$this->nama_db' AND table_name like '%ess_cico_%' group by table_name");
			foreach ($query->result_array() as $data) 
			{					
				$bulan = substr($data['table_name'],-2);
				$tahun = substr($data['table_name'],9,4);
				
				$tahun_bulan = $tahun."-".$bulan;				
				
				$array_tahun_bulan[] = $tahun_bulan; 
				if(empty($this->session->userdata('tampil_tahun_bulan'))){
					$this->session->set_userdata('tampil_tahun_bulan', $tahun_bulan);
				}
			}

			if($_SESSION["grup"]==4) { //jika Pengadministrasi Unit Kerja
				$var=array();
				$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
				foreach ($list_pengadministrasi as $data) { //looping list_pengadministrasi
					array_push($var, $data['kode_unit']);
				}				
				$this->data['np_karyawan'] = $this->dashboard->getKaryawan($var);
			}else if($_SESSION["grup"]==5) { //jika Pengguna
				$this->data['np_karyawan'] = $this->dashboard->getKaryawan($np);
			}else{
				$this->data['np_karyawan'] = $this->dashboard->getKaryawan();
			}
			
			$this->data['array_tahun_bulan'] = $array_tahun_bulan;
			$checkDateKehadiran = str_replace('-', '_', $this->session->userdata('tampil_tahun_bulan'));
			if($this->dashboard->checkDateKehadiran(str_replace('-', '_', $this->session->userdata('tampil_tahun_bulan')))){
				$checkDateKehadiran = 'tidak ada';
			}


			//REKAP TELAT
			$value = array($np_karyawan, $this->session->userdata('tampil_tahun_bulan'));
			$bulan = array('01','02','03','04','05','06','07','08','09','10','11','12');
			$tahun_periode = $tahun;
			if($tahun_periode==date('Y'))
				$hitung = '9';
				// $hitung = date('n');
			else
				$hitung = count($bulan);

			for($i=0; $i<$hitung; $i++) {
				$periode = $tahun_periode.'_'.$bulan[$i];
				$list[$i] = $this->m_data_keterlambatan->hitung_rekap_keterlambatan($value,$periode);
			}

			$return_telat = 0;
			for($i=0; $i<$hitung; $i++) {
		        foreach ($list[$i] as $row => $field) {
		        	$return_telat = $list[$i][$row]['jml'] + $return_telat;
		        }
			}

			if(empty($this->session->userdata('tampil_tahun_bulan'))){
				$this->data['total_cuti'] = $this->dashboard->getTotalCuti_where('np', $np_karyawan, $checkDateKehadiran);
				$this->data['total_lembur'] = $this->dashboard->getTotalLembur_where('np', $np_karyawan, $checkDateKehadiran);
				$this->data['total_izin'] = $this->dashboard->getTotalIzin_where('np', $np_karyawan, $checkDateKehadiran);
				$this->data['total_dinas'] = $this->dashboard->getTotalDinas_where('np', $np_karyawan, $checkDateKehadiran);
				$this->data['grafik_kehadiran'] = $this->dashboard->getGrafikKehadiran_where('np', $np_karyawan, $checkDateKehadiran);
			}else{
				$value = array($np_karyawan, $this->session->userdata('tampil_tahun_bulan'));
				$this->data['total_cuti'] = $this->dashboard->getTotalCuti_where('both', $value, $checkDateKehadiran);
				$this->data['total_lembur'] = $this->dashboard->getTotalLembur_where('both', $value, $checkDateKehadiran);
				$this->data['total_izin'] = $this->dashboard->getTotalIzin_where('both', $value, $checkDateKehadiran);
				$this->data['total_dinas'] = $this->dashboard->getTotalDinas_where('both', $value, $checkDateKehadiran);
				$this->data['grafik_kehadiran'] = $this->dashboard->getGrafikKehadiran_where('both', $value, $checkDateKehadiran);
			}
			
			$this->data['total_terlambat'] = $return_telat;
			$this->data['daftar_cuti'] = $this->dashboard->getCutiNeedApproval_where($np_karyawan)->result_array();
			$this->data['daftar_lembur'] = $this->dashboard->getLemburNeedApproval_where($np_karyawan)->result_array();
			$this->data['content'] = 'dashboard_new';
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['is_tnav'] = true;
			$this->load->view('template',$this->data);
		}

		public function filter_tahun_bulan($tahun_bulan = null)
		{
			$np = $this->session->userdata('no_pokok');
			if (empty($tahun_bulan)) {
				$this->session->unset_userdata('tampil_tahun_bulan');
				if(!empty($this->session->userdata('tampil_np_karyawan'))){
					redirect(base_url('home/filter_np_karyawan/').$this->session->userdata('tampil_np_karyawan'));
				}else{
					redirect(base_url('home/semua'));
				}
			}
			$this->session->set_userdata('tampil_tahun_bulan', $tahun_bulan);

			//ambil tahun bulan tabel yang tersedia
			$array_tahun_bulan = array();
			$query = $this->db->query("SELECT table_name FROM information_schema.tables WHERE table_schema='$this->nama_db' AND table_name like '%ess_cico_%';");
			foreach ($query->result_array() as $data) 
			{					
				$bulan = substr($data['table_name'],-2);
				$tahun = substr($data['table_name'],9,4);
				
				$tahunbulan = $tahun."-".$bulan;				
				
				$array_tahun_bulan[] = $tahunbulan; 
				if(empty($this->session->userdata('tampil_tahun_bulan'))){
					$this->session->set_userdata('tampil_tahun_bulan', $tahunbulan);
				}
			}

			if($_SESSION["grup"]==4) { //jika Pengadministrasi Unit Kerja
				$var=array();
				$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
				foreach ($list_pengadministrasi as $data) { //looping list_pengadministrasi
					array_push($var, $data['kode_unit']);
				}				
				$this->data['np_karyawan'] = $this->dashboard->getKaryawan($var);
			}else if($_SESSION["grup"]==5) { //jika Pengguna
				$this->data['np_karyawan'] = $this->dashboard->getKaryawan($np);
			}else{
				$this->data['np_karyawan'] = $this->dashboard->getKaryawan();
			}
			
			$this->data['array_tahun_bulan'] = $array_tahun_bulan;
			$checkDateKehadiran = str_replace('-', '_', $this->session->userdata('tampil_tahun_bulan'));
			if($this->dashboard->checkDateKehadiran(str_replace('-', '_', $this->session->userdata('tampil_tahun_bulan')))){
				$checkDateKehadiran = 'tidak ada';
			}


			//REKAP TELAT
			$value = array($this->session->userdata('tampil_np_karyawan'), $tahun_bulan);
			$bulan = array('01','02','03','04','05','06','07','08','09','10','11','12');
			$tahun_periode = $tahun;
			if($tahun_periode==date('Y'))
				$hitung = '9';
				// $hitung = date('n');
			else
				$hitung = count($bulan);

			for($i=0; $i<$hitung; $i++) {
				$periode = $tahun_periode.'_'.$bulan[$i];
				$list[$i] = $this->m_data_keterlambatan->hitung_rekap_keterlambatan($value,$periode);
			}

			$return_telat = 0;
			for($i=0; $i<$hitung; $i++) {
		        foreach ($list[$i] as $row => $field) {
		        	$return_telat = $list[$i][$row]['jml'] + $return_telat;
		        }
			}

			if(empty($this->session->userdata('tampil_np_karyawan'))){
				$this->data['total_cuti'] = $this->dashboard->getTotalCuti_where('date', $tahun_bulan, $checkDateKehadiran);
				$this->data['total_lembur'] = $this->dashboard->getTotalLembur_where('date', $tahun_bulan, $checkDateKehadiran);
				$this->data['total_izin'] = $this->dashboard->getTotalIzin_where('date', $tahun_bulan, $checkDateKehadiran);
				$this->data['total_dinas'] = $this->dashboard->getTotalDinas_where('date', $tahun_bulan, $checkDateKehadiran);
				$this->data['grafik_kehadiran'] = $this->dashboard->getGrafikKehadiran_where('date', $tahun_bulan, $checkDateKehadiran);
				$this->data['daftar_cuti'] = $this->dashboard->getCutiNeedApproval()->result_array();
				$this->data['daftar_lembur'] = $this->dashboard->getLemburNeedApproval()->result_array();
			}else{
				$value = array($this->session->userdata('tampil_np_karyawan'), $tahun_bulan);
				$this->data['total_cuti'] = $this->dashboard->getTotalCuti_where('both', $value, $checkDateKehadiran);
				$this->data['total_lembur'] = $this->dashboard->getTotalLembur_where('both', $value, $checkDateKehadiran);
				$this->data['total_izin'] = $this->dashboard->getTotalIzin_where('both', $value, $checkDateKehadiran);
				$this->data['total_dinas'] = $this->dashboard->getTotalDinas_where('both', $value, $checkDateKehadiran);
				$this->data['grafik_kehadiran'] = $this->dashboard->getGrafikKehadiran_where('both', $value, $checkDateKehadiran);
				$this->data['daftar_cuti'] = $this->dashboard->getCutiNeedApproval_where($this->session->userdata('tampil_np_karyawan'))->result_array();
				$this->data['daftar_lembur'] = $this->dashboard->getLemburNeedApproval_where($this->session->userdata('tampil_np_karyawan'))->result_array();
			}
			$this->data['total_terlambat'] = $return_telat;

			$this->data['content'] = 'dashboard_new';
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['is_tnav'] = true;
			$this->load->view('template',$this->data);
		}
	}
	