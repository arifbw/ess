<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Test extends CI_Controller {
		
		public function __construct(){
			parent::__construct();
		}

		public function index(){
			/* $tanggal_hari_ini = date("Y-m-d");
			echo $tanggal_hari_ini;
			echo "<br>";
			$date = date("Y-m-d",strtotime(date("Y-m-d", strtotime($tanggal_hari_ini)) . "-2 months"));
			echo $date;
			echo "<br>";
			echo strcmp($date,$tanggal_hari_ini); */
		$this->load->helper("email_helper");
		$subject = "TEST";
		$content = "TEST KIRIM EMAIL";
		$to = "arief.furqon@peruri.co.id";
		echo "$subject | $content | $to";
		kirim_email($subject, $content, $to);
	}

	function info()
	{
		phpinfo();
	}

	function showSession()
	{
		header('Content-type: application/json');
		echo json_encode($_SESSION);
	}

	function deleteLink()
	{
		// exec('rm -f /home/file/kehadiran/test_tujuan/folder/peruri.jpg 2>&1');
		unlink('/home/file/kehadiran/test_tujuan/folder/peruri.jpg');
	}

	public function cetak_all_unit2($tampil_bulan_tahun, $session_group)
	{
		$this->load->helper("cutoff_helper");
		$this->load->helper("tanggal_helper");
		$this->load->helper("karyawan_helper");
		$this->load->helper("perizinan_helper");
		$this->load->helper('form');

		$this->load->library('phpexcel');
		$this->load->model("kehadiran/M_tabel_data_kehadiran");

		// $tampil_bulan_tahun = $this->input->get('bulan');

		if (in_array($session_group, [1, 3])) {
			$this->db->select('kode_unit');
			$this->db->from('mst_karyawan');
			$this->db->group_by('kode_unit');
			$this->db->order_by('kode_unit');
			$query_daftar_unit = $this->db->get();
		} else {
			$query_daftar_unit = $this->m_data_kehadiran->select_daftar_unit();
		}
		$array_daftar_unit = [];
		foreach ($query_daftar_unit->result_array() as $val) {
			$array_daftar_unit[] = $val['kode_unit'];
		}
		$set['kode_unit'] = $array_daftar_unit;

		if ($tampil_bulan_tahun == '')
			$tampil_bulan_tahun = '';
		else {
			$bulan = substr($tampil_bulan_tahun, 0, 2);
			$tahun = substr($tampil_bulan_tahun, 3, 4);
			$tampil_bulan_tahun = $tahun . "_" . $bulan;
		}

		if ($session_group == 4) { //jika Pengadministrasi Unit Kerja		
			$ada_data = 0;
			$var = array();
			$list_pengadministrasi = [
				[
					'id_pengguna' => '2',
					'kode_unit' => '11B00'
				],
				[
					'id_pengguna' => '2',
					'kode_unit' => '51B00'
				],
				[
					'id_pengguna' => '2',
					'kode_unit' => '51B10'
				]
			];
			foreach ($list_pengadministrasi as $data) { //looping list_pengadministrasi
				array_push($var, $data['kode_unit']);
				$ada_data = 1;
			}

			if ($ada_data == 0)
				$var = '';
		} else if ($session_group == 5) //jika Pengguna
			$var 	= "7648";
		else
			$var = 1;
		$get_data = $this->M_tabel_data_kehadiran->_get_excel_per_unit($var, $tampil_bulan_tahun, $set);
		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);

		header("Content-type: application/vnd.ms-excel");
		//nama file
		header("Content-Disposition: attachment; filename=Data_kehadiran.xlsx");
		header('Cache-Control: max-age=0');

		$excel = PHPExcel_IOFactory::createReader('Excel2007');
		$excel = $excel->load('./asset/Template_data_kehadiran.xlsx');

		//anggota
		$excel->setActiveSheetIndex(0);
		$kolom 	= 2;
		$awal 	= 4;
		$no = 1;

		foreach ($get_data as $tampil) {
			$excel->getActiveSheet()->setCellValueExplicit('A' . $awal, $no++, PHPExcel_Cell_DataType::TYPE_STRING);
			$excel->getActiveSheet()->setCellValueExplicit('B' . $awal, strtoupper($tampil->np_karyawan), PHPExcel_Cell_DataType::TYPE_STRING);
			$excel->getActiveSheet()->setCellValueExplicit('C' . $awal, ucwords($tampil->nama), PHPExcel_Cell_DataType::TYPE_STRING);
			$excel->getActiveSheet()->setCellValueExplicit('D' . $awal, date('d-m-Y', strtotime($tampil->dws_tanggal)), PHPExcel_Cell_DataType::TYPE_STRING);

			if ($tampil->dws_name_fix == null || $tampil->dws_name_fix == '')
				$excel->getActiveSheet()->setCellValueExplicit('E' . $awal, strtoupper(nama_dws_by_kode($tampil->dws_name)), PHPExcel_Cell_DataType::TYPE_STRING);
			else
				$excel->getActiveSheet()->setCellValueExplicit('E' . $awal, strtoupper(nama_dws_by_kode($tampil->dws_name_fix)), PHPExcel_Cell_DataType::TYPE_STRING);

			if ($tampil->tapping_fix_1 == null || $tampil->tapping_fix_1 == '') {
				$tapping_1 = $tampil->tapping_time_1;
				if ($tapping_1) {
					$pisah_tapping_1		= explode(' ', $tapping_1);
					$tapping_1_value_date	= $pisah_tapping_1[0];
					$tapping_1_value_time	= $pisah_tapping_1[1];
					$tapping_1_value_time   = substr($tapping_1_value_time, 0, 5);
					//$machine_id_1			= "<br>Machine id : ".$tampil->tapping_terminal_1;
					$machine_id_1			= '';
				} else {
					$tapping_1_value_date	= $tampil->dws_tanggal;
					$tapping_1_value_time	= '';
				}
			} else {
				$tapping_1					= $tampil->tapping_fix_1;
				if ($tapping_1) {
					$pisah_tapping_1		= explode(' ', $tapping_1);
					$tapping_1_value_date	= $pisah_tapping_1[0];
					$tapping_1_value_time	= $pisah_tapping_1[1];
					$tapping_1_value_time   = substr($tapping_1_value_time, 0, 5);
					$machine_id_1			= '';
				} else {
					$tapping_1_value_date	= $tampil->dws_tanggal;
					$tapping_1_value_time	= '';
				}
			}

			if ($tapping_1)
				$excel->getActiveSheet()->setCellValueExplicit('F' . $awal, ucwords(tanggal_indonesia(substr($tapping_1, 0, 10)) . " " . substr($tapping_1, 10, 6) . " " . $machine_id_1), PHPExcel_Cell_DataType::TYPE_STRING);
			else
				$excel->getActiveSheet()->setCellValueExplicit('F' . $awal, ' ', PHPExcel_Cell_DataType::TYPE_STRING);

			if ($tampil->tapping_fix_2 == null || $tampil->tapping_fix_2 == '') {
				$tapping_2  				= $tampil->tapping_time_2;
				if ($tapping_2) {
					$pisah_tapping_2		= explode(' ', $tapping_2);
					$tapping_2_value_date	= $pisah_tapping_2[0];
					$tapping_2_value_time	= $pisah_tapping_2[1];
					$tapping_2_value_time   = substr($tapping_2_value_time, 0, 5);
					//$machine_id_2			= "<br>Machine id : ".$tampil->tapping_terminal_2;
					$machine_id_2			= '';
				} else {
					$tapping_2_value_date	= $tampil->dws_tanggal;
					$tapping_2_value_time	= '';
				}
			} else {
				$tapping_2 					= $tampil->tapping_fix_2;
				if ($tapping_2) {
					$pisah_tapping_2		= explode(' ', $tapping_2);
					$tapping_2_value_date	= $pisah_tapping_2[0];
					$tapping_2_value_time	= $pisah_tapping_2[1];
					$tapping_2_value_time   = substr($tapping_2_value_time, 0, 5);
					$machine_id_2			= "";
				} else {
					$tapping_2_value_date	= $tampil->dws_tanggal;
					$tapping_2_value_time	= '';
				}
			}

			if ($tapping_2)
				$excel->getActiveSheet()->setCellValueExplicit('G' . $awal, ucwords(tanggal_indonesia(substr($tapping_2, 0, 10)) . " " . substr($tapping_2, 10, 6) . "  " . $machine_id_2), PHPExcel_Cell_DataType::TYPE_STRING);
			else
				$excel->getActiveSheet()->setCellValueExplicit('G' . $awal, '', PHPExcel_Cell_DataType::TYPE_STRING);

			$tampil_keterangan 	= '';
			$hari_libur 		= hari_libur_by_tanggal($tampil->dws_tanggal);

			if ($hari_libur) {
				if ($tampil_keterangan == '')
					$tampil_keterangan = $hari_libur;
				else
					$tampil_keterangan = $tampil_keterangan . "<br><br>" . $hari_libur;
			}

			$hari_cuti_bersama = $this->db->query("SELECT * FROM ess_cuti_bersama WHERE tanggal_cuti_bersama='$tampil->dws_tanggal' AND np_karyawan='$tampil->np_karyawan' LIMIT 1")->row_array();

			//7648 Tri Wibowo, 6 Januari 2019 - ketika sudah di pembatalan maka tidak tampil
			$hari_pembatalan =  $this->db->query("SELECT * FROM ess_pembatalan_cuti WHERE is_cuti_bersama='1' AND date='$tampil->dws_tanggal' AND np_karyawan='$tampil->np_karyawan' LIMIT 1")->row_array();
			//jika ada pembatalan
			$id_cuti_bersama = null;
			if ($hari_pembatalan['id'] == null) {
				$id_cuti_bersama = $hari_cuti_bersama['id'];
			}

			if ($tampil->id_cuti) {
				if ($tampil_keterangan == '')
					$tampil_keterangan = 'Cuti';
				else
					$tampil_keterangan = $tampil_keterangan . ". " . 'Cuti';
			} else if ($tampil->id_sppd) {
				if ($tampil_keterangan == '')
					$tampil_keterangan = 'Dinas';
				else
					$tampil_keterangan = $tampil_keterangan . ". " . 'Dinas';
			} else if ($id_cuti_bersama) {
				if ($tampil_keterangan == '')
					$tampil_keterangan = 'Cuti Bersama';
				else
					$tampil_keterangan = $tampil_keterangan . ". " . 'Cuti Bersama';
			} else {
				$id_perizinan = explode(",", $tampil->id_perizinan);
				$isi = '';
				foreach ($id_perizinan as $value) {
					$tahun_bulan = substr($tampil->dws_tanggal, 0, 7);
					$tahun_bulan = str_replace('-', '_', $tahun_bulan);
					$izin = perizinan_by_id($tahun_bulan, $value);
					$kode_erp = $izin['info_type'] . "|" . $izin['absence_type'];
					$nama_perizinan = nama_perizinan_by_kode_erp($kode_erp);
					if ($nama_perizinan)
						$isi = $isi . "" . $nama_perizinan . "<br><br>";
				}

				if (!$hari_libur) {
					if ($tampil_keterangan == '') {
						if ((strpos($isi, 'Izin Dinas Keluar Perusahaan Non-Pendidikan') !== false) || (strpos($isi, 'Izin Dinas Luar Lokasi Pendidikan') !== false) || (strpos($isi, 'Izin Pribadi dengan Potongan') !== false) || (strpos($isi, 'Izin Pribadi Tanpa Potongan') !== false)) {
							$tampil_keterangan = $isi;
						} else {
							if ($tampil->keterangan)
								$tampil_keterangan = $tampil->keterangan . ". " . $isi;
							else
								$tampil_keterangan = $isi;
						}
					} else {
						if ((strpos($isi, 'Izin Dinas Keluar Perusahaan Non-Pendidikan') !== false) || (strpos($isi, 'Izin Dinas Luar Lokasi Pendidikan') !== false) ||
							(strpos($isi, 'Izin Pribadi dengan Potongan') !== false) || (strpos($isi, 'Izin Pribadi Tanpa Potongan') !== false)
						) {
							$tampil_keterangan = $tampil_keterangan . ". " . $isi;
						} else {
							if ($tampil->keterangan)
								$tampil_keterangan = $tampil_keterangan . ". " . $tampil->keterangan . ". " . $isi;
							else
								$tampil_keterangan = $tampil_keterangan . ". " . $isi;
						}
					}
				}
			}

			if ($tampil->wfh == 1) {
				$tampil_keterangan = "Work From Home" . ". " . $tampil_keterangan;
			}

			$excel->getActiveSheet()->setCellValueExplicit('H' . $awal, $tampil_keterangan, PHPExcel_Cell_DataType::TYPE_STRING);
			$awal += 1;
		}

		$objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
		$objWriter->setIncludeCharts(TRUE);
		$objWriter->setPreCalculateFormulas(TRUE);
		PHPExcel_Calculation::getInstance($excel)->clearCalculationCache();
		//$objWriter->save('php://output');
		$objWriter->save(APPPATH . "../test/generate-$tampil_bulan_tahun.xlsx");
	}

	function createJson($table)
	{
		$newQuery = $this->db->get($table)->result_array();

			$this->db->select('TABLE_NAME')
				->from('INFORMATION_SCHEMA.TABLES')
				->where('TABLE_SCHEMA', $databaseName)
				->like('TABLE_NAME', $keyword, 'AFTER')
				->order_by('TABLE_NAME', 'DESC')
				->limit(2);
			$subquery = $this->db->get_compiled_select();

			$this->db->select('TABLE_NAME')
				->from("($subquery) last_2_month")
				->order_by('TABLE_NAME', 'ASC');

			// $this->db->select('TABLE_NAME');
			// $this->db->from('INFORMATION_SCHEMA.TABLES');
			// $this->db->where('TABLE_SCHEMA', $databaseName);
			// $this->db->like('TABLE_NAME', $keyword, 'AFTER');
			$query = $this->db->get();

		$fp = fopen(APPPATH . '../test/' . $table . '.json', 'w');
		fwrite($fp, json_encode($response));
		fclose($fp);
	}

		function test(){
			$this->load->helper("cutoff_helper");
			$this->load->helper("tanggal_helper");
			$this->load->helper("karyawan_helper");
			$this->load->helper("reference_helper");
			$this->load->helper("perizinan_helper");
			$np_karyawan = '7777';
			$kode_unit = array(kode_unit_by_np($np_karyawan));
			$this->load->model('m_approval');
			$list = $this->m_approval->list_atasan_minimal_kaun($kode_unit, $np_karyawan);
			echo json_encode($list);
		}
	}
