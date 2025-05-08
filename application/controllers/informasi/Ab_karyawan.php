<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ab_karyawan extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		// Report all errors
		error_reporting(E_ALL);

		// Display errors in output
		ini_set('display_errors', 1);

		$meta = meta_data();
		foreach ($meta as $key => $value) {
			$this->data[$key] = $value;
		}

		$this->folder_view = 'informasi/';
		$this->folder_model = 'informasi/';
		$this->folder_controller = 'informasi/';

		$this->akses = array();

		$this->load->model($this->folder_model . "m_ab_karyawan");
		$this->load->model($this->folder_model . "m_ab_karyawan_detail");

		$this->data["is_with_sidebar"] = true;

		$this->data['judul'] = "AB Karyawan";
		$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		$this->akses = akses_helper($this->data['id_modul']);

		$this->load->model("master_data/m_karyawan");
		$this->load->model("master_data/m_satuan_kerja");
		izin($this->akses["akses"]);
	}

	public function index()
	{
		//	echo __FILE__ . __LINE__;die(var_dump($this->akses));	

		$np = $this->input->get('np');
		$kode_unit = $this->input->get('kode_unit');

		$this->data["akses"] = $this->akses;
		$this->data["navigasi_menu"] = menu_helper();
		$this->data['content'] = $this->folder_view . "ab_karyawan";

		if ($this->akses["lihat"]) {
			array_push($this->data['css_plugin_sources'], "select2/select2.min.css");
			array_push($this->data['js_plugin_sources'], "select2/select2.min.js");


			$this->data["daftar_akses_karyawan"] = array(array("no_pokok" => "", "nama" => ""));

			$pilihan_karyawan = "";
			// if ($this->akses["pilih seluruh karyawan"]) {
			// 	$pilihan_karyawan = "pilihan_karyawan();";
			// 	$this->data["daftar_akses_unit_kerja"] = $this->m_satuan_kerja->daftar_satuan_kerja();
			// } else if ($this->akses["pilih karyawan diadministrasikan"]) {
			// 	$pilihan_karyawan = "pilihan_karyawan();";
			// 	$this->data["daftar_akses_unit_kerja"] = $this->m_satuan_kerja->daftar_satuan_kerja_diadministrasikan();
			// } else {
			$this->data["daftar_akses_karyawan"] = array(array("no_pokok" => $this->session->userdata("no_pokok"), "nama" => $this->session->userdata("nama")));
			$this->data["daftar_akses_unit_kerja"] = array(array("kode_unit" => $this->session->userdata("kode_unit"), "nama_unit" => $this->session->userdata("nama_unit")));
			// }

			$this->data["arr_periode"] = periode();
			$this->data["np"] = $np;
			$this->data["kode_unit"] = $kode_unit;

			$js_header_script = "<script>
								$(document).ready(function() {
									$pilihan_karyawan
									$('.select2').select2();
								});
							</script>";

			array_push($this->data["js_header_script"], $js_header_script);
		}

		$this->load->view('template', $this->data);
	}

	public function detail()
	{
		$kode_unit = $this->input->get('kode_unit');
		$np_karyawan = $this->input->get('np_karyawan');
		$periode_awal = $this->input->get('periode_awal');
		$periode_akhir = $this->input->get('periode_akhir');

		$this->data["akses"] = $this->akses;
		$this->data["navigasi_menu"] = menu_helper();
		$this->data['content'] = $this->folder_view . "ab_karyawan_detail";

		array_push($this->data['css_plugin_sources'], "select2/select2.min.css");
		array_push($this->data['js_plugin_sources'], "select2/select2.min.js");

		$this->data["arr_periode"] = array($periode_awal, $periode_akhir);
		$this->data["np"] = $np_karyawan;
		$this->data["kode_unit"] = $kode_unit;


		$this->load->view('template', $this->data);
	}

	public function data_detail()
	{

		$kode_unit = $this->decrypt($this->input->post('kode_unit'));
		$np_karyawan = $this->decrypt($this->input->post('np_karyawan'));
		$periode_awal = $this->input->post('periode_awal');
		$periode_akhir = $this->input->post('periode_akhir');

		if ($_POST['length'] != "") {
			$awal = $_POST['start'];
			$akhir = $_POST['length'];
		} else {
			$awal = 0;
			$akhir = count($list);
		}

		$list = $this->m_ab_karyawan_detail->get_datatable_ab($kode_unit, $np_karyawan, $periode_awal, $periode_akhir);
		$recordsFiltered = $this->m_ab_karyawan_detail->count_filtered($kode_unit, $np_karyawan, $periode_awal, $periode_akhir);
		$recordsTotal = $this->m_ab_karyawan_detail->count_all($kode_unit, $np_karyawan, $periode_awal, $periode_akhir);


		$data = array();
		$no = 0;

		foreach ($list as $tampil) {
			$no++;

			$array_of_dws = json_decode($tampil->array_of_dws);
			$array_tapping_fix_1 = json_decode($tampil->array_tapping_fix_1);
			$array_tapping_fix_2 = json_decode($tampil->array_tapping_fix_2);

			$row = array();
			$row[] = $no;
			$row[] = $tampil->np_karyawan;
			$row[] = $tampil->nama;
			$row[] = tanggal($array_of_dws[0]);
			$row[] = tanggal(end($array_of_dws));

			$keterangan = "";
			for ($i = 0; $i < count($array_tapping_fix_1); $i++) {
				if ($array_tapping_fix_1[$i] != null) {
					$keterangan .=  tanggal_waktu($array_tapping_fix_1[$i]) . ' - ' . tanggal_waktu($array_tapping_fix_2[$i]);
					$keterangan .= "<br>";
				}
			}

			$row[] = $keterangan .  (empty($keterangan) ? '' : ' <br><br> <b> Diubah di ' . '  ESS </b>');

			$data[] = $row;
		}


		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $recordsTotal,
			"recordsFiltered" => $recordsFiltered,
			"data" => array_slice($data, $awal, $akhir)
		);

		//output to json format
		echo json_encode($output);

		$log = array(
			"id_pengguna" => $this->session->userdata("id_pengguna"),
			"id_modul" => $this->data['id_modul'],
			"deskripsi" => "lihat AB Karyawan <br>Kode unit kerja : " . $kode_unit . "<br>NP Karyawan : " . $np_karyawan . "<br>Periode : " . $periode_awal . " - " . $periode_akhir,
			"alamat_ip" => $this->data["ip_address"],
			"waktu" => date("Y-m-d H:i:s")
		);
		$this->m_log->tambah($log);
	}

	public function tabel_ab_karyawan()
	{
		$kode_unit = $this->input->post("kode_unit");
		$np_karyawan = $this->input->post("np_karyawan");
		$periode_awal = $this->input->post("periode_awal");
		$periode_akhir = $this->input->post("periode_akhir");



		if ($_POST['length'] != "") {
			$awal = $_POST['start'];
			$akhir = $_POST['length'];
		} else {
			$awal = 0;
			$akhir = count($list);
		}


		$list = $this->m_ab_karyawan->get_datatable_ab($kode_unit, $np_karyawan, $periode_awal, $periode_akhir);
		$recordsFiltered = $this->m_ab_karyawan->count_filtered($kode_unit, $np_karyawan, $periode_awal, $periode_akhir);
		$recordsTotal = $this->m_ab_karyawan->count_all($kode_unit, $np_karyawan, $periode_awal, $periode_akhir);


		$data = array();

		$no = 0;

		foreach ($list as $tampil) {

			$buttonValue = http_build_query([
				'kode_unit' => $this->encrypt($tampil->kode_unit),
				'np_karyawan' => $this->encrypt($tampil->np_karyawan),
				'periode_awal' => $periode_awal,
				'periode_akhir' => $periode_akhir
			]);

			$no++;
			$row = array();
			$row[] = $no;
			$row[] = $tampil->np_karyawan;
			$row[] = $tampil->nama;
			$row[] = $tampil->jumlah_ab;
			$row[] = '<button class="btn btn-default center-block" onclick="window.location.href=\'' . base_url('/informasi/ab_karyawan/detail') . '?' . $buttonValue . '\'">Lihat Detail</button>';

			$data[] = $row;
		}


		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $recordsTotal,
			"recordsFiltered" => $recordsFiltered,
			"data" => array_slice($data, $awal, $akhir),
		);

		//output to json format
		echo json_encode($output);

		$log = array(
			"id_pengguna" => $this->session->userdata("id_pengguna"),
			"id_modul" => $this->data['id_modul'],
			"deskripsi" => "lihat AB Karyawan <br>Kode unit kerja : " . $kode_unit . "<br>NP Karyawan : " . $np_karyawan . "<br>Periode : " . $periode_awal . " - " . $periode_akhir,
			"alamat_ip" => $this->data["ip_address"],
			"waktu" => date("Y-m-d H:i:s")
		);
		$this->m_log->tambah($log);
	}

	public function daftar_karyawan()
	{
		$kode_unit = $_POST["unit_kerja"];
		$hasil["np_pengguna"] = $this->session->userdata("no_pokok");
		$hasil["karyawan"] = $this->m_karyawan->get_karyawan_unit_kerja($kode_unit);
		echo json_encode($hasil);
	}

	function daftar_karyawan_revisi()
	{
		$insert_date_awal = $this->input->post('insert_date_awal', true);
		$insert_date_akhir = $this->input->post('insert_date_akhir', true);
		$unit_kerja = $this->input->post('unit_kerja', true);

		// $tableFix = 'mst_karyawan';

		if ($insert_date_awal == $insert_date_akhir) {
			$date_fix = $insert_date_akhir;
			if (date('Y-m', strtotime($date_fix)) >= date('Y-m'))
				$tableFix = 'erp_master_data_' . date('Y_m');
			else
				$tableFix = 'erp_master_data_' . date('Y_m', strtotime($date_fix));
		} else {
			if (date('Y', strtotime($insert_date_awal)) == date('Y', strtotime($insert_date_akhir))) {
				if (date('Y-m', strtotime($insert_date_akhir)) >= date('Y-m', strtotime($insert_date_awal))) {
					$tableFix = 'erp_master_data_' . date('Y_m', strtotime($insert_date_akhir));
					$bulan = $insert_date_akhir;
				} else {
					$tableFix = 'erp_master_data_' . date('Y_m', strtotime($insert_date_awal));
					$bulan = $insert_date_awal;
				}

				if (date('Y-m', strtotime($bulan)) >= date('Y-m'))
					$tableFix = 'erp_master_data_' . date('Y_m');
			} else {
				if (date('Y', strtotime($insert_date_akhir)) >= date('Y', strtotime($insert_date_awal)))
					$tableFix = 'erp_master_data_' . date('Y_m', strtotime($insert_date_akhir));
				else
					$tableFix = 'erp_master_data_' . date('Y_m', strtotime($insert_date_awal));
			}
		}

		$hasil["np_pengguna"] = $this->session->userdata("no_pokok");
		$this->db->select('np_karyawan as no_pokok, nama');
		if ($unit_kerja != '00000')
			$this->db->where('kode_unit', $unit_kerja);
		$get = $this->db->group_by('np_karyawan')->get($tableFix);
		$hasil["karyawan"] = $get->result_array();
		echo json_encode($hasil);
	}

	function getUnitByPeriode()
	{
		$select = '<option value="00000">00000 - Perusahaan Umum Percetakan Uang Republik Indonesia</option>';
		$insert_date_awal = $this->input->post('insert_date_awal', true);
		$insert_date_akhir = $this->input->post('insert_date_akhir', true);

		$insert_date_awal_convert = str_replace('-', '_', $insert_date_awal);
		$insert_date_akhir_convert = str_replace('-', '_', $insert_date_akhir);

		$table_awal = (check_table_exist("erp_master_data_$insert_date_awal_convert") == 'ada' ? "erp_master_data_$insert_date_awal_convert" : "erp_master_data_" . date('Y_m'));
		$table_akhir = (check_table_exist("erp_master_data_$insert_date_akhir_convert") == 'ada' ? "erp_master_data_$insert_date_akhir_convert" : "erp_master_data_" . date('Y_m'));

		$union = $this->db
			->query("SELECT * 
                        FROM
                        (SELECT DISTINCT kode_unit,nama_unit FROM $table_awal
                        UNION
                        SELECT DISTINCT kode_unit,nama_unit FROM $table_akhir) a
                        ORDER BY kode_unit")
			->result();
		foreach ($union as $row) {
			$selected = $row->kode_unit == $this->session->userdata("kode_unit") ? 'selected' : '';
			$select .= '<option value="' . $row->kode_unit . '" ' . $selected . '>' . $row->kode_unit . ' - ' . $row->nama_unit . '</option>';
		}
		echo $select;
	}

	public function export()
	{
		$this->load->library('phpexcel');

		$kode_unit = $this->input->post("kode_unit");
		$np_karyawan = $this->input->post("np_karyawan");
		$periode_awal = $this->input->post("periode_awal");
		$periode_akhir = $this->input->post("periode_akhir");

		if ($periode_awal == "" && $periode_akhir == "") {
			$periode = date('Y_m');
			$list[0] = $this->m_ab_karyawan->export_data_ab_karyawan($kode_unit, $np_karyawan, $periode, $periode);
		} else if (date('Y', strtotime($periode_awal)) == date('Y', strtotime($periode_akhir))) {
			$tahun_awal = date('Y', strtotime($periode_awal));
			$bulan_awal = date('n', strtotime($periode_awal));
			$tahun_akhir = date('Y', strtotime($periode_akhir));
			$bulan_akhir = date('n', strtotime($periode_akhir));
			$bulan = array('00', '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12');

			$no = 0;
			for ($i = ($bulan_awal); $i <= $bulan_akhir; $i++) {
				$periode = $tahun_awal . "_" . $bulan[$i];
				$periode_end = $tahun_akhir . "_" . $bulan[$i];
				$list[$no] = $this->m_ab_karyawan->export_data_ab_karyawan($kode_unit, $np_karyawan, $periode, $periode_end);
				$no++;
			}
		}

		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);

		header("Content-type: application/vnd.ms-excel");
		//nama file
		header("Content-Disposition: attachment; filename=Data_AB_Karyawan.xlsx");
		header('Cache-Control: max-age=0');

		$excel = PHPExcel_IOFactory::createReader('Excel2007');
		$excel = $excel->load('./asset/Template_data_ab_karyawan.xlsx');

		//anggota
		$excel->setActiveSheetIndex(0);
		$excel->getActiveSheet()->setCellValueExplicit('A2', 'Periode ' . substr(tanggal($periode_awal), 2) . ' s/d ' . substr(tanggal($periode_akhir), 2), PHPExcel_Cell_DataType::TYPE_STRING);

		$kolom 	= 2;
		$awal 	= 5;
		$no = 1;

		for ($b = 0; $b < count($list); $b++) {
			foreach ($list[$b] as $tampil) {
				$array_of_dws = json_decode($tampil->array_of_dws);
				$array_tapping_fix_1 = json_decode($tampil->array_tapping_fix_1);
				$array_tapping_fix_2 = json_decode($tampil->array_tapping_fix_2);

				$keterangan = "";
				for ($i = 0; $i < count($array_tapping_fix_1); $i++) {
					if ($array_tapping_fix_1[$i] != null) {
						$keterangan .=  tanggal_waktu($array_tapping_fix_1[$i]);
						$keterangan .= "\n";
					}
				}

				for ($i = 0; $i < count($array_tapping_fix_2); $i++) {
					if ($array_tapping_fix_2[$i] != null) {
						$keterangan .=  tanggal_waktu($array_tapping_fix_2[$i]);
						$keterangan .= "\n";
					}
				}


				$excel->getActiveSheet()->setCellValueExplicit('A' . $awal, $no++, PHPExcel_Cell_DataType::TYPE_STRING);
				$excel->getActiveSheet()->setCellValueExplicit('B' . $awal, strtoupper($tampil->np_karyawan), PHPExcel_Cell_DataType::TYPE_STRING);
				$excel->getActiveSheet()->setCellValueExplicit('C' . $awal, ucwords($tampil->nama), PHPExcel_Cell_DataType::TYPE_STRING);
				$excel->getActiveSheet()->setCellValueExplicit('D' . $awal, tanggal($array_of_dws[0]), PHPExcel_Cell_DataType::TYPE_STRING);
				$excel->getActiveSheet()->setCellValueExplicit('E' . $awal, tanggal(end($array_of_dws)), PHPExcel_Cell_DataType::TYPE_STRING);
				$excel->getActiveSheet()->setCellValueExplicit('F' . $awal, $keterangan . (empty($keterangan) ? '' : ' Diubah di ' . '  ESS'), PHPExcel_Cell_DataType::TYPE_STRING);
				$excel->getActiveSheet()->getStyle('F' . $awal)->getAlignment()->setWrapText(true);
				$awal += 1;
			}
		}

		$objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
		$objWriter->setIncludeCharts(TRUE);
		$objWriter->setPreCalculateFormulas(TRUE);
		PHPExcel_Calculation::getInstance($excel)->clearCalculationCache();
		$objWriter->save('php://output');
		exit();
	}


	function encrypt($data)
	{
		$key = "KunciRahasia12345";

		$ivSize = openssl_cipher_iv_length('aes-256-cbc');
		$iv = openssl_random_pseudo_bytes($ivSize);
		$encryptedData = openssl_encrypt($data, 'aes-256-cbc', $key, 0, $iv);
		return base64_encode($iv . $encryptedData);
	}

	// Fungsi untuk mendekripsi data
	function decrypt($encryptedData)
	{
		$key = "KunciRahasia12345";

		$encryptedData = base64_decode($encryptedData);
		$ivSize = openssl_cipher_iv_length('aes-256-cbc');
		$iv = substr($encryptedData, 0, $ivSize);
		$data = openssl_decrypt(substr($encryptedData, $ivSize), 'aes-256-cbc', $key, 0, $iv);
		return $data;
	}
}
