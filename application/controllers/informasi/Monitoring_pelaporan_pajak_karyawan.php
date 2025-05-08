<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Monitoring_pelaporan_pajak_karyawan extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		$meta = meta_data();
		foreach ($meta as $key => $value) {
			$this->data[$key] = $value;
		}

		$this->folder_view = 'informasi/';
		$this->folder_model = 'informasi/';
		$this->folder_controller = 'informasi/';

		$this->akses = array();

		$this->load->helper("cutoff_helper");
		$this->load->helper("tanggal_helper");
		$this->load->helper("karyawan_helper");
		$this->load->helper("reference_helper");
		$this->load->helper("string");

		$this->load->model($this->folder_model . "m_monitoring_pelaporan_pajak");

		$this->data["is_with_sidebar"] = true;

		$this->data['judul'] = "Monitoring Pelaporan Pajak Karyawan";
		$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		$this->akses = akses_helper($this->data['id_modul']);

		izin($this->akses["akses"]);
	}

	public function index()
	{
		$this->data["akses"] = $this->akses;
		$this->data["navigasi_menu"] = menu_helper();
		$this->data['content'] = $this->folder_view . "monitoring_pelaporan_pajak_karyawan";

		$sto = $this->db->select('kode_unit, nama_unit')->group_by('kode_unit, nama_unit')->get('mst_karyawan')->result();
		$this->data['sto'] = $sto;

		$this->load->view('template', $this->data);
	}

	public function tabel_lapor_pajak()
	{
		$list = $this->m_monitoring_pelaporan_pajak->get_datatables();
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $tampil) {
			$no++;
			$row = array();
			$no_tanda_terima_elektronik = trim($tampil->no_tanda_terima_elektronik);
			$row[] = "{$tampil->no_pokok} - {$tampil->nama}";
			$row[] = $tampil->nama_unit;
			$row[] = $tampil->tahun;
			$row[] = $tampil->status_spt;
			$row[] = $no_tanda_terima_elektronik;
			$row[] = $tampil->id != null && $tampil->no_tanda_terima_elektronik != null ? '<span class="text-success">Sudah Lapor</span>' : '<span class="">Belum Lapor</span>';
			$row[] = is_file($tampil->surat_keterangan) ? '<a href="' . base_url($tampil->surat_keterangan) . '" class="btn btn-xs btn-primary" target="_blank">Lihat</a>' : '';

			$data[] = $row;
		}

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->m_monitoring_pelaporan_pajak->count_all(),
			"recordsFiltered" => $this->m_monitoring_pelaporan_pajak->count_filtered(),
			"data" => $data,
		);
		echo json_encode($output);
	}

	function get_rekap_lapor()
	{
		$data = $this->m_monitoring_pelaporan_pajak->get_rekap_lapor();
		$output = array(
			"status" => true,
			"data" => $data
		);
		echo json_encode($output);
	}

	public function export_excel()
	{
		$tahun = $this->input->post('tahun', true);
		$unit = $this->input->post('unit', true);

		$data = $this->m_monitoring_pelaporan_pajak->get_filtered_data($tahun, $unit);

		$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
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
		$sheet->setCellValue('A1', 'Rekap Laporan Daftar Bukti Pajak');
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

		// Buat header tabel nya pada baris ke 5
		$sheet->setCellValue('A5', "No"); // Set kolom A8 dengan tulisan "NO"
		$sheet->setCellValue('B5', "NP"); // Set kolom B8 dengan tulisan "NIS"
		$sheet->setCellValue('C5', "Nama"); // Set kolom C8 dengan tulisan "NAMA"
		$sheet->setCellValue('D5', "Unit Kerja"); // Set kolom D8 dengan tulisan "JENIS KELAMIN"
		$sheet->setCellValue('E5', "Tahun Pajak"); // Set kolom E8 dengan tulisan "ALAMAT"
		$sheet->setCellValue('F5', "Status SPT"); // Set kolom E8 dengan tulisan "ALAMAT"
		$sheet->setCellValue('G5', "No. Tanda Terima Elektronik"); // Set kolom E8 dengan tulisan "ALAMAT"
		$sheet->setCellValue('H5', "Status Lapor"); // Set kolom E8 dengan tulisan "ALAMAT"

		// Apply style header yang telah kita buat tadi ke masing-masing kolom header
		$sheet->getStyle('A5')->applyFromArray($style_col);
		$sheet->getStyle('B5')->applyFromArray($style_col);
		$sheet->getStyle('C5')->applyFromArray($style_col);
		$sheet->getStyle('D5')->applyFromArray($style_col);
		$sheet->getStyle('E5')->applyFromArray($style_col);
		$sheet->getStyle('F5')->applyFromArray($style_col);
		$sheet->getStyle('G5')->applyFromArray($style_col);
		$sheet->getStyle('H5')->applyFromArray($style_col);

		$no = 1; // Untuk penomoran tabel, di awal set dengan 1
		$row_number = 6; // Set baris pertama untuk isi tabel adalah baris ke 6
		foreach ($data as $row) {
			$sheet->setCellValue('A' . $row_number, $no);
			$sheet->setCellValue('B' . $row_number, $row->no_pokok);
			$sheet->setCellValue('C' . $row_number, $row->nama);
			$sheet->setCellValue('D' . $row_number, $row->nama_unit);
			$sheet->setCellValue('E' . $row_number, $row->tahun);
			$sheet->setCellValue('F' . $row_number, $row->status_spt);
			$sheet->setCellValue('G' . $row_number, $row->no_tanda_terima_elektronik);
			$sheet->setCellValue('H' . $row_number, (strlen($row->no_tanda_terima_elektronik) == 21) ? 'Sudah Lapor' : 'Belum Lapor');

			// Apply style row yang telah kita buat tadi ke masing-masing baris (isi tabel)
			$sheet->getStyle('A' . $row_number)->applyFromArray($style_row);
			$sheet->getStyle('B' . $row_number)->applyFromArray($style_row);
			$sheet->getStyle('C' . $row_number)->applyFromArray($style_row);
			$sheet->getStyle('D' . $row_number)->applyFromArray($style_row);
			$sheet->getStyle('E' . $row_number)->applyFromArray($style_row);
			$sheet->getStyle('F' . $row_number)->applyFromArray($style_row);
			$sheet->getStyle('G' . $row_number)->applyFromArray($style_row);
			$sheet->getStyle('H' . $row_number)->applyFromArray($style_row);

			$no++; // Tambah 1 setiap kali looping
			$row_number++; // Tambah 1 setiap kali looping
		}

		// Set width kolom
		$sheet->getColumnDimension('A')->setAutoSize(true);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		$sheet->getColumnDimension('H')->setAutoSize(true);
		$spreadsheet->getActiveSheet()->freezePane('A6');

		// Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
		$sheet->getDefaultRowDimension()->setRowHeight(-1);
		$sheet->setTitle("Daftar Bukti Pajak");
		$filename = "Rekap Laporan Daftar Bukti Pajak Unit " . $sto->object_name . " Tahun " . $tahun;
		$writer = \PhpOffice\PhpSpreadsheet\Writer\Xlsx::class;

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="' . $filename . '.xlsx"'); // Set nama file excel nya
		header('Cache-Control: max-age=0');

		$writer = new $writer($spreadsheet);
		$writer->save('php://output');
	}
}
