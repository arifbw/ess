<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard_realisasi extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		$meta = meta_data();
		foreach ($meta as $key => $value) {
			$this->data[$key] = $value;
		}

		$this->dwh = $this->load->database('dwh', true);
		$this->folder_view = 'lembur/dashboard_realisasi/';
		$this->folder_model = 'lembur/';
		$this->folder_controller = 'lembur/';

		$this->akses = array();

		$this->load->helper("cutoff_helper");
		$this->load->helper("tanggal_helper");
		$this->load->helper("karyawan_helper");
		$this->load->helper("reference_helper");
		$this->load->helper("string");

		$this->data["is_with_sidebar"] = true;

		$this->data['judul'] = "Dashboard Monitoring Realisasi Lembur";
		$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		$this->akses = akses_helper($this->data['id_modul']);
		izin($this->akses["akses"]);

		$this->load->model('lembur/M_dashboard_realisasi', 'm_dashboard_realisasi');
		$this->load->model('lembur/M_plafon_lembur', 'm_plafon_lembur');
	}

	public function index()
	{
		$this->data["akses"] = $this->akses;
		$this->data["navigasi_menu"] = menu_helper();
		$this->data['content'] = $this->folder_view . "index";

		$sess_grup = $this->session->userdata('grup');
		if($sess_grup==3){ // Admin SDM - Remunerasi
			$this->db->group_start();
				$this->db->where("SUBSTR(object_abbreviation,1,1) <>", '0');
				$this->db->where("SUBSTR(object_abbreviation,2,4)", '0000');
			$this->db->group_end();
			$this->db->or_group_start();
				$this->db->where("SUBSTR(object_abbreviation,2,1) <>", '0');
				$this->db->where("SUBSTR(object_abbreviation,3,3)", '000');
			$this->db->group_end();
		} else if($sess_grup==31){ // Admin - Perencanaan Lembur Divisi
			$sess_kode_unit = $this->session->userdata('kode_unit');
			$kode_divisi = substr($sess_kode_unit, 0, 2). '000';
			$this->db->where("object_abbreviation", $kode_divisi);
		}
		$sto = $this->db->where('object_type', 'O')->get('ess_sto')->result();
		$this->data['sto'] = $sto;

		$bulan = $this->db->get('bulan')->result();
		$this->data['bulan'] = $bulan;

		$this->load->view('template', $this->data);
	}

	function get_realisasi_tahunan()
	{
		$tahun = $this->input->post('tahun', true);
		$unit = $this->input->post('unit', true);

		$data = $this->m_dashboard_realisasi->get_realisasi_tahunan($tahun, $unit);
		echo json_encode([
			'status' => true,
			'data' => $data
		]);
	}

	function get_plafon()
	{
		$tahun = $this->input->post('tahun', true);
		$unit = $this->input->post('unit', true);
		$divisi = substr($unit, 0, 2) . '000';

		$plafon = $this->m_plafon_lembur->get_by_tahun_sto($tahun, $divisi);
		$realisasi_divisi = $this->m_dashboard_realisasi->get_realisasi_tahunan_divisi($tahun, substr($unit, 0, 2));
		echo json_encode([
			'status' => true,
			'data' => [
				'plafon' => $plafon,
				'realisasi' => $realisasi_divisi
			]
		]);
	}

	function get_data_karyawan()
	{
		$list 	= $this->m_dashboard_realisasi->get_datatables();
		$no = $_POST['start'];

		foreach ($list as $key => $val) {
			$no++;
			$list[$key]->no = $no;
		}

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->m_dashboard_realisasi->count_all(),
			"recordsFiltered" => $this->m_dashboard_realisasi->count_filtered(),
			"data" => $list
		);
		echo json_encode($output);
	}

	function export_excel()
	{
		$tahun = $this->input->get('tahun', true);
		$unit = $this->input->get('unit', true);
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		// Buat sebuah variabel untuk menampung pengaturan style dari header tabel
		$style_col = [
			'font' => ['bold' => true], // Set font nya jadi bold
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
				'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
			],
			'borders' => [
				'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
				'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
				'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
				'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
			]
		];
		// Buat sebuah variabel untuk menampung pengaturan style dari isi tabel
		$style_row = [
			'alignment' => [
				'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
			],
			'borders' => [
				'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
				'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
				'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
				'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
			]
		];
		$sto = $this->db->where(['object_type' => 'O', 'object_abbreviation' => $unit])->get('ess_sto')->row();
		$title = strtoupper("Daftar Lembur Unit " . $sto->object_name . " Tahun " . $tahun);
		$sheet->setCellValue('A1', 'DAFTAR LEMBUR');
		$sheet->mergeCells('A1:F1');
		$sheet->getStyle('A1')->getFont()->setBold(true);
		$sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
		$sheet->setCellValue('A2', 'UNIT');
		$sheet->setCellValue('C2', ':');
		$sheet->setCellValue('D2', strtoupper($sto->object_name));
		$sheet->getStyle('A2')->getFont()->setBold(true);
		$sheet->getStyle('C2')->getAlignment()->setHorizontal('right');
		$sheet->setCellValue('A3', 'TAHUN');
		$sheet->setCellValue('C3', ':');
		$sheet->setCellValue('D3', strtoupper($tahun));
		$sheet->getStyle('A3')->getFont()->setBold(true);
		$sheet->getStyle('C3')->getAlignment()->setHorizontal('right');
		$sheet->getStyle('D3')->getAlignment()->setHorizontal('left');
		$sheet->setCellValue('A4', 'PLAFON');
		$sheet->setCellValue('C4', ':');
		$sheet->setCellValue('D4', '');
		$sheet->getStyle('A4')->getFont()->setBold(true);
		$sheet->getStyle('C4')->getAlignment()->setHorizontal('right');
		$sheet->setCellValue('A5', 'REALISASI');
		$sheet->setCellValue('C5', ':');
		$sheet->setCellValue('D5', '');
		$sheet->getStyle('A5')->getFont()->setBold(true);
		$sheet->getStyle('C5')->getAlignment()->setHorizontal('right');
		$sheet->setCellValue('A6', 'SISA');
		$sheet->setCellValue('C6', ':');
		$sheet->setCellValue('D6', '');
		$sheet->getStyle('A6')->getFont()->setBold(true);
		$sheet->getStyle('C6')->getAlignment()->setHorizontal('right');

		// Buat header tabel nya pada baris ke 3
		$sheet->setCellValue('A8', "No"); // Set kolom A8 dengan tulisan "NO"
		$sheet->setCellValue('B8', "NP"); // Set kolom B8 dengan tulisan "NIS"
		$sheet->setCellValue('C8', "Nama"); // Set kolom C8 dengan tulisan "NAMA"
		$sheet->setCellValue('D8', "Unit Kerja"); // Set kolom D8 dengan tulisan "JENIS KELAMIN"
		$sheet->setCellValue('E8', "Lembur (Rp)"); // Set kolom E8 dengan tulisan "ALAMAT"
		$sheet->setCellValue('F8', "Lembur (Jam)"); // Set kolom E8 dengan tulisan "ALAMAT"
		// Apply style header yang telah kita buat tadi ke masing-masing kolom header
		$sheet->getStyle('A8')->applyFromArray($style_col);
		$sheet->getStyle('B8')->applyFromArray($style_col);
		$sheet->getStyle('C8')->applyFromArray($style_col);
		$sheet->getStyle('D8')->applyFromArray($style_col);
		$sheet->getStyle('E8')->applyFromArray($style_col);
		$sheet->getStyle('F8')->applyFromArray($style_col);
		// Panggil function view yang ada di SiswaModel untuk menampilkan semua data siswanya

		$this->dwh->select('
			rekap.np_karyawan,
			rekap.nama,
			rekap.abbr_unit_kerja,
			rekap.unit_kerja,
			SUM(rekap.total_jam_lembur) as total_jam,
			SUM(CASE
				WHEN RIGHT(rekap.3001_uang_lembur_15, 1) = "-" THEN
					CAST(CONCAT("-", LEFT(rekap.3001_uang_lembur_15, LENGTH(rekap.3001_uang_lembur_15) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3001_uang_lembur_15 AS SIGNED)
			END) AS total_3001_uang_lembur_15,
			SUM(CASE
				WHEN RIGHT(rekap.3002_uang_lembur_2, 1) = "-" THEN
					CAST(CONCAT("-", LEFT(rekap.3002_uang_lembur_2, LENGTH(rekap.3002_uang_lembur_2) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3002_uang_lembur_2 AS SIGNED)
			END) AS total_3002_uang_lembur_2,
			SUM(CASE
				WHEN RIGHT(rekap.3003_uang_lembur_3, 1) = "-" THEN
					CAST(CONCAT("-", LEFT(rekap.3003_uang_lembur_3, LENGTH(rekap.3003_uang_lembur_3) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3003_uang_lembur_3 AS SIGNED)
			END) AS total_3003_uang_lembur_3,
			SUM(CASE
				WHEN RIGHT(rekap.3004_uang_lembur_4, 1) = "-" THEN
					CAST(CONCAT("-", LEFT(rekap.3004_uang_lembur_4, LENGTH(rekap.3004_uang_lembur_4) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3004_uang_lembur_4 AS SIGNED)
			END) AS total_3004_uang_lembur_4,
			SUM(CASE
				WHEN RIGHT(rekap.3005_uang_lembur_5, 1) = "-" THEN
					CAST(CONCAT("-", LEFT(rekap.3005_uang_lembur_5, LENGTH(rekap.3005_uang_lembur_5) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3005_uang_lembur_5 AS SIGNED)
			END) AS total_3005_uang_lembur_5,
			SUM(CASE
				WHEN RIGHT(rekap.3006_uang_lembur_6, 1) = "-" THEN
					CAST(CONCAT("-", LEFT(rekap.3006_uang_lembur_6, LENGTH(rekap.3006_uang_lembur_6) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3006_uang_lembur_6 AS SIGNED)
			END) AS total_3006_uang_lembur_6,
			SUM(CASE
				WHEN RIGHT(rekap.3007_uang_lembur_7, 1) = "-" THEN
					CAST(CONCAT("-", LEFT(rekap.3007_uang_lembur_7, LENGTH(rekap.3007_uang_lembur_7) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3007_uang_lembur_7 AS SIGNED)
			END) AS total_3007_uang_lembur_7,
			SUM(CASE
				WHEN RIGHT(rekap.3100_uang_lembur_manual, 1) = "-" THEN
					CAST(CONCAT("-", LEFT(rekap.3100_uang_lembur_manual, LENGTH(rekap.3100_uang_lembur_manual) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3100_uang_lembur_manual AS SIGNED)
			END) AS total_3100_uang_lembur_manual,
			SUM(CASE
				WHEN RIGHT(rekap.3110_insentif_lembur, 1) = "-" THEN
					CAST(CONCAT("-", LEFT(rekap.3110_insentif_lembur, LENGTH(rekap.3110_insentif_lembur) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3110_insentif_lembur AS SIGNED)
			END) AS total_3110_insentif_lembur,
			SUM(CASE
				WHEN RIGHT(rekap.3400_uang_lembur_susulan, 1) = "-" THEN
					CAST(CONCAT("-", LEFT(rekap.3400_uang_lembur_susulan, LENGTH(rekap.3400_uang_lembur_susulan) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3400_uang_lembur_susulan AS SIGNED)
			END) AS total_3400_uang_lembur_susulan
		');
		$this->dwh->from('sap_hcm_rekap_lembur rekap');

		// $this->db->join("(SELECT x.np_karyawan, MAX(x.id) as max_id
		// FROM sap_hcm_rekap_lembur x WHERE x.periode_tahun='{$tahun}' AND x.abbr_unit_kerja LIKE '".rtrim($unit, '0')."%'
		// GROUP BY x.np_karyawan) t2", 'rekap.np_karyawan = t2.np_karyawan AND rekap.id = t2.max_id','LEFT');

		$this->dwh->join("(SELECT x.np_karyawan, x.periode_tahun, x.periode_bulan, MAX(x.id) as max_id
		FROM sap_hcm_rekap_lembur x WHERE x.periode_tahun='{$tahun}' AND x.abbr_unit_kerja LIKE '".rtrim($unit, '0')."%'
		GROUP BY x.np_karyawan, x.periode_tahun, x.periode_bulan) t2", 'rekap.np_karyawan = t2.np_karyawan AND rekap.periode_tahun = t2.periode_tahun AND rekap.periode_bulan = t2.periode_bulan AND rekap.id = t2.max_id','INNER');

		$this->dwh->where('rekap.periode_tahun', $tahun);
		$this->dwh->where('rekap.rate_lembur !=', '');
		if ($unit != '00000') {
			$this->dwh->like('rekap.abbr_unit_kerja', rtrim($unit, '0'), 'AFTER');
		}
		$this->dwh->having('SUM(rekap.total_jam_lembur) >', 0);
		$this->dwh->group_by('rekap.np_karyawan');
		$this->dwh->order_by('rekap.np_karyawan, rekap.nama, rekap.abbr_unit_kerja, rekap.unit_kerja');
		$query = $this->dwh->get();
		$daftarLembur = $query->result_array();
		$kolom_hitung = ['total_3001_uang_lembur_15', 'total_3002_uang_lembur_2', 'total_3003_uang_lembur_3', 'total_3004_uang_lembur_4', 'total_3005_uang_lembur_5', 'total_3006_uang_lembur_6', 'total_3007_uang_lembur_7', 'total_3100_uang_lembur_manual', 'total_3110_insentif_lembur', 'total_3400_uang_lembur_susulan'];

		foreach ($daftarLembur as $k => $v) {
			$sum = 0;
			foreach ($kolom_hitung as $value) {
				$sum += floatval($v[$value]);
				unset($daftarLembur[$k][$value]);
			}
			$formattedNumber = number_format($sum, 0, ',', '.');
			$formattedCurrency = "Rp " . $formattedNumber;
			$daftarLembur[$k]['total_perhitungan'] = $formattedCurrency;
		}

		// PHP's NumberFormatter class is used to format the currency.
		$no = 1; // Untuk penomoran tabel, di awal set dengan 1
		$numrow = 9; // Set baris pertama untuk isi tabel adalah baris ke 4
		foreach ($daftarLembur as $data) { // Lakukan looping pada variabel siswa
			$sheet->setCellValue('A' . $numrow, $no);
			$sheet->setCellValue('B' . $numrow, $data['np_karyawan']);
			$sheet->setCellValue('C' . $numrow, $data['nama']);
			$sheet->setCellValue('D' . $numrow, $data['unit_kerja']);
			$sheet->setCellValue('E' . $numrow, $data['total_perhitungan']);
			$sheet->setCellValue('F' . $numrow, $data['total_jam']);

			// Apply style row yang telah kita buat tadi ke masing-masing baris (isi tabel)
			$sheet->getStyle('A' . $numrow)->applyFromArray($style_row);
			$sheet->getStyle('B' . $numrow)->applyFromArray($style_row);
			$sheet->getStyle('C' . $numrow)->applyFromArray($style_row);
			$sheet->getStyle('D' . $numrow)->applyFromArray($style_row);
			$sheet->getStyle('E' . $numrow)->applyFromArray($style_row);
			$sheet->getStyle('F' . $numrow)->applyFromArray($style_row);

			$no++; // Tambah 1 setiap kali looping
			$numrow++; // Tambah 1 setiap kali looping
		}
		// Set width kolom
		$sheet->getColumnDimension('A')->setAutoSize(true);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		$spreadsheet->getActiveSheet()->freezePane('A9');

		// Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
		$sheet->getDefaultRowDimension()->setRowHeight(-1);
		// Set orientasi kertas jadi LANDSCAPE
		$sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
		// Set judul file excel nya
		$sheet->setTitle("Daftar Lembur");
		$filename = "Laporan Daftar Lembur Unit " . $sto->object_name . " Tahun " . $tahun. " - ". date('YmdHis');
		// Proses file excel
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="' . $filename . '.xlsx"'); // Set nama file excel nya
		header('Cache-Control: max-age=0');
		$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');
	}

	function export_excel_rincian()
	{
		$tahun = $this->input->get('tahun', true);
		$unit = $this->input->get('unit', true);
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		// Buat sebuah variabel untuk menampung pengaturan style dari header tabel
		$style_col = [
			'font' => ['bold' => true], // Set font nya jadi bold
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
				'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
			],
			'borders' => [
				'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
				'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
				'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
				'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
			]
		];
		// Buat sebuah variabel untuk menampung pengaturan style dari isi tabel
		$style_row = [
			'alignment' => [
				'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
			],
			'borders' => [
				'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
				'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
				'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
				'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
			]
		];
		$sto = $this->db->where(['object_type' => 'O', 'object_abbreviation' => $unit])->get('ess_sto')->row();
		$title = strtoupper("Daftar Lembur Unit " . $sto->object_name . " Tahun " . $tahun);
		$sheet->setCellValue('A1', 'DAFTAR RINCIAN LEMBUR');
		$sheet->mergeCells('A1:G1');
		$sheet->getStyle('A1')->getFont()->setBold(true);
		$sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
		$sheet->setCellValue('A2', 'UNIT');
		$sheet->setCellValue('C2', ':');
		$sheet->setCellValue('D2', strtoupper($sto->object_name));
		$sheet->getStyle('A2')->getFont()->setBold(true);
		$sheet->getStyle('C2')->getAlignment()->setHorizontal('right');
		$sheet->setCellValue('A3', 'TAHUN');
		$sheet->setCellValue('C3', ':');
		$sheet->setCellValue('D3', strtoupper($tahun));
		$sheet->getStyle('A3')->getFont()->setBold(true);
		$sheet->getStyle('C3')->getAlignment()->setHorizontal('right');
		$sheet->getStyle('D3')->getAlignment()->setHorizontal('left');
		// $sheet->setCellValue('A4', '');
		// $sheet->setCellValue('C4', '');
		// $sheet->setCellValue('D4', '');
		// $sheet->getStyle('A4')->getFont()->setBold(true);
		// $sheet->getStyle('C4')->getAlignment()->setHorizontal('right');
		// $sheet->setCellValue('A5', '');
		// $sheet->setCellValue('C5', '');
		// $sheet->setCellValue('D5', '');
		// $sheet->getStyle('A5')->getFont()->setBold(true);
		// $sheet->getStyle('C5')->getAlignment()->setHorizontal('right');
		// $sheet->setCellValue('A6', '');
		// $sheet->setCellValue('C6', '');
		// $sheet->setCellValue('D6', '');
		// $sheet->getStyle('A6')->getFont()->setBold(true);
		// $sheet->getStyle('C6')->getAlignment()->setHorizontal('right');

		// Buat header tabel nya
		$sheet->setCellValue('A5', "No");
		$sheet->setCellValue('B5', "NP");
		$sheet->setCellValue('C5', "Nama");
		$sheet->setCellValue('D5', "Unit Kerja");
		$sheet->setCellValue('E5', "Bulan");
		$sheet->setCellValue('F5', "Jam Lembur");
		$sheet->setCellValue('G5', "Premi Lembur");
		// Apply style header yang telah kita buat tadi ke masing-masing kolom header
		$sheet->getStyle('A5')->applyFromArray($style_col);
		$sheet->getStyle('B5')->applyFromArray($style_col);
		$sheet->getStyle('C5')->applyFromArray($style_col);
		$sheet->getStyle('D5')->applyFromArray($style_col);
		$sheet->getStyle('E5')->applyFromArray($style_col);
		$sheet->getStyle('F5')->applyFromArray($style_col);
		$sheet->getStyle('G5')->applyFromArray($style_col);

		$this->dwh->select('
			rekap.np_karyawan,
			rekap.nama,
			rekap.abbr_unit_kerja,
			rekap.unit_kerja,
			rekap.periode_tahun, 
			rekap.periode_bulan,
			SUM(rekap.rate_lembur) as rate_lembur,
			SUM(rekap.total_jam_lembur) as total_jam,
			SUM(CASE
				WHEN RIGHT(rekap.3001_uang_lembur_15, 1) = "-" THEN
					CAST(CONCAT("-", LEFT(rekap.3001_uang_lembur_15, LENGTH(rekap.3001_uang_lembur_15) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3001_uang_lembur_15 AS SIGNED)
			END) AS total_3001_uang_lembur_15,
			SUM(CASE
				WHEN RIGHT(rekap.3002_uang_lembur_2, 1) = "-" THEN
					CAST(CONCAT("-", LEFT(rekap.3002_uang_lembur_2, LENGTH(rekap.3002_uang_lembur_2) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3002_uang_lembur_2 AS SIGNED)
			END) AS total_3002_uang_lembur_2,
			SUM(CASE
				WHEN RIGHT(rekap.3003_uang_lembur_3, 1) = "-" THEN
					CAST(CONCAT("-", LEFT(rekap.3003_uang_lembur_3, LENGTH(rekap.3003_uang_lembur_3) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3003_uang_lembur_3 AS SIGNED)
			END) AS total_3003_uang_lembur_3,
			SUM(CASE
				WHEN RIGHT(rekap.3004_uang_lembur_4, 1) = "-" THEN
					CAST(CONCAT("-", LEFT(rekap.3004_uang_lembur_4, LENGTH(rekap.3004_uang_lembur_4) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3004_uang_lembur_4 AS SIGNED)
			END) AS total_3004_uang_lembur_4,
			SUM(CASE
				WHEN RIGHT(rekap.3005_uang_lembur_5, 1) = "-" THEN
					CAST(CONCAT("-", LEFT(rekap.3005_uang_lembur_5, LENGTH(rekap.3005_uang_lembur_5) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3005_uang_lembur_5 AS SIGNED)
			END) AS total_3005_uang_lembur_5,
			SUM(CASE
				WHEN RIGHT(rekap.3006_uang_lembur_6, 1) = "-" THEN
					CAST(CONCAT("-", LEFT(rekap.3006_uang_lembur_6, LENGTH(rekap.3006_uang_lembur_6) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3006_uang_lembur_6 AS SIGNED)
			END) AS total_3006_uang_lembur_6,
			SUM(CASE
				WHEN RIGHT(rekap.3007_uang_lembur_7, 1) = "-" THEN
					CAST(CONCAT("-", LEFT(rekap.3007_uang_lembur_7, LENGTH(rekap.3007_uang_lembur_7) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3007_uang_lembur_7 AS SIGNED)
			END) AS total_3007_uang_lembur_7,
			SUM(CASE
				WHEN RIGHT(rekap.3100_uang_lembur_manual, 1) = "-" THEN
					CAST(CONCAT("-", LEFT(rekap.3100_uang_lembur_manual, LENGTH(rekap.3100_uang_lembur_manual) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3100_uang_lembur_manual AS SIGNED)
			END) AS total_3100_uang_lembur_manual,
			SUM(CASE
				WHEN RIGHT(rekap.3110_insentif_lembur, 1) = "-" THEN
					CAST(CONCAT("-", LEFT(rekap.3110_insentif_lembur, LENGTH(rekap.3110_insentif_lembur) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3110_insentif_lembur AS SIGNED)
			END) AS total_3110_insentif_lembur,
			SUM(CASE
				WHEN RIGHT(rekap.3400_uang_lembur_susulan, 1) = "-" THEN
					CAST(CONCAT("-", LEFT(rekap.3400_uang_lembur_susulan, LENGTH(rekap.3400_uang_lembur_susulan) - 1)) AS SIGNED)
				ELSE
					CAST(rekap.3400_uang_lembur_susulan AS SIGNED)
			END) AS total_3400_uang_lembur_susulan
		');
		$this->dwh->from('sap_hcm_rekap_lembur rekap');

		$this->dwh->join("(SELECT x.np_karyawan, x.periode_tahun, x.periode_bulan, MAX(x.id) as max_id
		FROM sap_hcm_rekap_lembur x WHERE x.periode_tahun='{$tahun}' AND x.abbr_unit_kerja LIKE '".rtrim($unit, '0')."%'
		GROUP BY x.np_karyawan, x.periode_tahun, x.periode_bulan) t2", 'rekap.np_karyawan = t2.np_karyawan AND rekap.periode_tahun = t2.periode_tahun AND rekap.periode_bulan = t2.periode_bulan AND rekap.id = t2.max_id','INNER');

		$this->dwh->where('rekap.periode_tahun', $tahun);
		$this->dwh->where('rekap.rate_lembur !=', '');
		if ($unit != '00000') {
			$this->dwh->like('rekap.abbr_unit_kerja', rtrim($unit, '0'), 'AFTER');
		}
		// BUG nya disini, jadi karena yang lembur susulan, tidak terekap di total_jam_lembur jadi datanya ga masuk
		// $this->dwh->where('rekap.total_jam_lembur >', 0);
		$this->dwh->group_by('rekap.np_karyawan, rekap.periode_tahun, rekap.periode_bulan');
		$this->dwh->order_by('rekap.np_karyawan, rekap.nama, rekap.abbr_unit_kerja, rekap.unit_kerja, rekap.periode_tahun, rekap.periode_bulan');
		$query = $this->dwh->get();
		$daftarLembur = $query->result_array();
		$kolom_hitung = ['total_3001_uang_lembur_15', 'total_3002_uang_lembur_2', 'total_3003_uang_lembur_3', 'total_3004_uang_lembur_4', 'total_3005_uang_lembur_5', 'total_3006_uang_lembur_6', 'total_3007_uang_lembur_7', 'total_3100_uang_lembur_manual', 'total_3110_insentif_lembur', 'total_3400_uang_lembur_susulan'];

		foreach ($daftarLembur as $k => $v) {
			$sum = 0;
			foreach ($kolom_hitung as $value) {
				$sum += floatval($v[$value]);
				unset($daftarLembur[$k][$value]);
			}
			$formattedNumber = number_format($sum, 0, ',', '.');
			$formattedCurrency = "Rp " . $formattedNumber;
			$daftarLembur[$k]['total_perhitungan'] = $formattedCurrency;
		}

		$no = 1;
		$numrow = 6;
		setlocale(LC_TIME, 'id_ID');
		foreach ($daftarLembur as $data) {
			$bulan = "{$data['periode_tahun']}-{$data['periode_bulan']}";
			$date = DateTime::createFromFormat('Y-m', $bulan);
			$formattedDate = strftime('%b %Y', $date->getTimestamp());

			$sheet->setCellValue('A' . $numrow, $no);
			$sheet->setCellValue('B' . $numrow, $data['np_karyawan']);
			$sheet->setCellValue('C' . $numrow, $data['nama']);
			$sheet->setCellValue('D' . $numrow, $data['unit_kerja']);
			$sheet->setCellValue('E' . $numrow, $formattedDate);
			$sheet->setCellValue('F' . $numrow, $data['total_jam']);
			$sheet->setCellValue('G' . $numrow, $data['total_perhitungan']);

			// Apply style row yang telah kita buat tadi ke masing-masing baris (isi tabel)
			$sheet->getStyle('A' . $numrow)->applyFromArray($style_row);
			$sheet->getStyle('B' . $numrow)->applyFromArray($style_row);
			$sheet->getStyle('C' . $numrow)->applyFromArray($style_row);
			$sheet->getStyle('D' . $numrow)->applyFromArray($style_row);
			$sheet->getStyle('E' . $numrow)->applyFromArray($style_row);
			$sheet->getStyle('F' . $numrow)->applyFromArray($style_row);
			$sheet->getStyle('G' . $numrow)->applyFromArray($style_row);

			$no++;
			$numrow++;
		}
		// Set width kolom
		$sheet->getColumnDimension('A')->setAutoSize(true);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		$spreadsheet->getActiveSheet()->freezePane('A6');

		// Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
		$sheet->getDefaultRowDimension()->setRowHeight(-1);
		// Set orientasi kertas jadi LANDSCAPE
		$sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
		// Set judul file excel nya
		$sheet->setTitle("Daftar Rincian Lembur");
		$filename = "Laporan Daftar Rincian Lembur Unit " . $sto->object_name . " Tahun " . $tahun. " - ". date('YmdHis');
		// Proses file excel
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="' . $filename . '.xlsx"'); // Set nama file excel nya
		header('Cache-Control: max-age=0');
		$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');
	}
}
