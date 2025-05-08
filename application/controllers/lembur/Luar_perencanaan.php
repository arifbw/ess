<?php
defined('BASEPATH') or exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Luar_perencanaan extends CI_Controller {
	public function __construct() {
		parent::__construct();

		$meta = meta_data();
		foreach ($meta as $key => $value) {
			$this->data[$key] = $value;
		}

		$this->folder_view = 'lembur/';
		$this->folder_model = 'lembur/';
		$this->folder_ajax_view = $this->folder_view . 'ajax/';

		$this->akses = array();

		$this->load->helper("karyawan_helper");
		$this->load->helper("tanggal_helper");
		$this->load->helper("cutoff_helper");
		$this->load->helper("string");

		$this->load->model($this->folder_model . "/M_lembur_luar_perencanaan");

		$this->data['success'] = "";
		$this->data['warning'] = "";

		$this->data["is_with_sidebar"] = true;
	}

    public function index() {
		$this->data['judul'] = "Lembur di Luar Perencanaan";
		$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		$this->akses = akses_helper($this->data['id_modul']);

		izin($this->akses["akses"]);

		$this->data["akses"] = $this->akses;
		$this->data["navigasi_menu"] = menu_helper();
		$this->data['content'] = $this->folder_view . "luar_perencanaan/index";
        
        $this->load->view('template', $this->data);
	}

    public function get_data() {
		$list 	= $this->M_lembur_luar_perencanaan->get_datatables();
		$no = $_POST['start'];

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->M_lembur_luar_perencanaan->count_all(),
			"recordsFiltered" => $this->M_lembur_luar_perencanaan->count_filtered(),
			"data" => $list,
		);
		echo json_encode($output);
	}

    function import_excel(){
        if (isset($_FILES['file_lembur']['tmp_name'])) {
			$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
			$spreadsheet = $reader->load($_FILES['file_lembur']['tmp_name']);
			$sheet = $spreadsheet->getSheetByName('Input Lembur Manual');
			$sheetdata = $sheet->toArray(null, true, true, true);

			$processed = [];
			$array_of_np = [];
			foreach ($sheetdata as $key => $value) {
				if($key>7){
					$array_of_np[] = trim($value['A']);
					$processed[] = [
						'np'=>$value['A'],
						// 'nama'=>$value['B'],
						// 'nama_unit'=>$value['C'],
						'tanggal'=>trim($value['B'])!='' ? date('Y-m-d', strtotime(trim($value['B']))) : null,
						'jumlah_jam_lembur'=>$value['C'],
						'jenis_lembur'=>$value['D'],
						// 'mulai_lembur_ke'=>$value['G'],
						'premi_lembur'=>$value['E']
					];
				}
			}

			if($processed!=[]){
				$transformed = array_map(function ($item) {
					$item['created_at'] = date('Y-m-d H:i:s');
					return $item;
				}, $processed);
				$this->M_lembur_luar_perencanaan->insert_multiple($transformed);
				$this->M_lembur_luar_perencanaan->update_detail();
			}

			return $this->output
				->set_content_type('application/json')
				->set_status_header(200)
				->set_output(json_encode([
					'status'=>true,
					'data'=>$processed,
				]));
        } else {
			return $this->output
				->set_content_type('application/json')
				->set_status_header(200)
				->set_output(json_encode([
					'status'=>false,
                	'data'=>[]
				]));
        }
    }

	function hapus(){
		$id = $this->input->post('id', true);
		$this->db->trans_start();
		$this->db->where('id', $id);
		$this->db->update($this->M_lembur_luar_perencanaan->table, ['deleted_at'=>date('Y-m-d H:i:s')]);
		$this->db->trans_complete();
		
		echo json_encode([
			'status'=>true,
			'message'=>'Sudah dihapus'
		]);
	}
	
	function export_excel($start_date, $end_date)
	{
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
		$sheet->setCellValue('A1', 'LEMBUR DI LUAR PERENCANAAN');
		$sheet->mergeCells('A1:G1');
		$sheet->getStyle('A1')->getFont()->setBold(true);
		$sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
		// $sheet->setCellValue('A2', '');
		// $sheet->setCellValue('B2', '');
		// $sheet->setCellValue('C2', '');
		// $sheet->getStyle('A2')->getFont()->setBold(true);
		// $sheet->getStyle('B2')->getAlignment()->setHorizontal('right');
		$sheet->setCellValue('A3', 'TANGGAL');
		$sheet->setCellValue('B3', ':');
		$sheet->setCellValue('C3', tanggal_indonesia($start_date).' - '.tanggal_indonesia($end_date));
		$sheet->getStyle('A3')->getFont()->setBold(true);
		$sheet->getStyle('B3')->getAlignment()->setHorizontal('right');
		$sheet->getStyle('C3')->getAlignment()->setHorizontal('left');

		// Buat header tabel nya pada baris ke 3
		$sheet->setCellValue('A5', "NP"); // Set kolom A5 dengan tulisan "NO"
		$sheet->setCellValue('B5', "Nama"); // Set kolom B5 dengan tulisan "NIS"
		$sheet->setCellValue('C5', "Unit Kerja"); // Set kolom C5 dengan tulisan "NAMA"
		$sheet->setCellValue('D5', "Tanggal"); // Set kolom D5 dengan tulisan "JENIS KELAMIN"
		$sheet->setCellValue('E5', "Jumlah Jam Lembur"); // Set kolom E5 dengan tulisan "ALAMAT"
		$sheet->setCellValue('F5', "Jenis Lembur"); // Set kolom E5 dengan tulisan "ALAMAT"
		$sheet->setCellValue('G5', "Premi Lembur (Rp)"); // Set kolom E5 dengan tulisan "ALAMAT"
		// Apply style header yang telah kita buat tadi ke masing-masing kolom header
		$sheet->getStyle('A5')->applyFromArray($style_col);
		$sheet->getStyle('B5')->applyFromArray($style_col);
		$sheet->getStyle('C5')->applyFromArray($style_col);
		$sheet->getStyle('D5')->applyFromArray($style_col);
		$sheet->getStyle('E5')->applyFromArray($style_col);
		$sheet->getStyle('F5')->applyFromArray($style_col);
		$sheet->getStyle('G5')->applyFromArray($style_col);
		// Panggil function view yang ada di SiswaModel untuk menampilkan semua data siswanya
		$dataPerencanaan = $this->db->where('deleted_at', null)->get('ess_lembur_luar_perencanaan')->result();

		$no = 1; // Untuk penomoran tabel, di awal set dengan 1
		$numrow = 6; // Set baris pertama untuk isi tabel adalah baris ke 4
		foreach ($dataPerencanaan as $data) { // Lakukan looping pada variabel siswa
			$sheet->setCellValue('A' . $numrow, $data->np);
			$sheet->setCellValue('B' . $numrow, $data->nama);
			$sheet->setCellValue('C' . $numrow, $data->nama_unit);
			$sheet->setCellValue('D' . $numrow, tanggal_indonesia($data->tanggal));
			$sheet->setCellValue('E' . $numrow, $data->jumlah_jam_lembur);
			$sheet->setCellValue('F' . $numrow, $data->jenis_lembur);
			$formattedNumber = number_format($data->premi_lembur, 0, ',', '.');
			$formattedCurrency = "Rp " . $formattedNumber;
			$sheet->setCellValue('G' . $numrow, $formattedCurrency);

			// Apply style row yang telah kita buat tadi ke masing-masing baris (isi tabel)
			$sheet->getStyle('A' . $numrow)->applyFromArray($style_row);
			$sheet->getStyle('B' . $numrow)->applyFromArray($style_row);
			$sheet->getStyle('C' . $numrow)->applyFromArray($style_row);
			$sheet->getStyle('D' . $numrow)->applyFromArray($style_row);
			$sheet->getStyle('E' . $numrow)->applyFromArray($style_row);
			$sheet->getStyle('F' . $numrow)->applyFromArray($style_row);
			$sheet->getStyle('G' . $numrow)->applyFromArray($style_row);

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
		$sheet->getColumnDimension('G')->setAutoSize(true);
		$spreadsheet->getActiveSheet()->freezePane('A6');

		// Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
		$sheet->getDefaultRowDimension()->setRowHeight(-1);
		// Set orientasi kertas jadi LANDSCAPE
		$sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
		// Set judul file excel nya
		$sheet->setTitle("Data");
		$filename = "Laporan Lembur di Luar Perencanaan ". tanggal_indonesia($start_date).' - '.tanggal_indonesia($end_date);
		// Proses file excel
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="' . $filename . '.xlsx"'); // Set nama file excel nya
		header('Cache-Control: max-age=0');
		$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');
	}
}