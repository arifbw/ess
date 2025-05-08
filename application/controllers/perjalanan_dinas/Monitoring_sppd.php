<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Monitoring_sppd extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		$meta = meta_data();
		foreach ($meta as $key => $value) {
			$this->data[$key] = $value;
		}

		$this->folder_view = 'perjalanan_dinas/';
		$this->folder_model = 'perjalanan_dinas/';
		$this->folder_controller = 'perjalanan_dinas/';

		$this->akses = array();

		$this->load->helper("cutoff_helper");
		$this->load->helper("tanggal_helper");
		$this->load->helper("karyawan_helper");
		$this->load->helper("reference_helper");

		$this->load->model($this->folder_model . "m_monitoring_sppd");
		$this->load->model('M_approval');

		$this->data["is_with_sidebar"] = true;

		$this->data['judul'] = "Monitoring SPPD";
		$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		$this->akses = akses_helper($this->data['id_modul']);
		izin($this->akses["akses"]);
	}

	public function index()
	{
		//$this->output->enable_profiler(TRUE);
		//echo __FILE__ . __LINE__;die(var_dump($this->akses));			

		$this->data["akses"] 					= $this->akses;
		$this->data["navigasi_menu"] 			= menu_helper();
		$this->data['content'] 					= $this->folder_view . "monitoring_sppd";

		$np = $this->session->userdata('no_pokok');

		if ($_SESSION["grup"] == 4) { //jika Pengadministrasi Unit Kerja
			$var = array();
			$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
			foreach ($list_pengadministrasi as $data) { //looping list_pengadministrasi
				array_push($var, $data['kode_unit']);
			}
			$this->data['np_karyawan'] = $this->m_monitoring_sppd->getKaryawan($var);
		} else if ($_SESSION["grup"] == 5) { //jika Pengguna
			$this->data['np_karyawan'] = $this->m_monitoring_sppd->getKaryawan($np);
		} else {
			$this->data['np_karyawan'] = $this->m_monitoring_sppd->getKaryawan();
		}

		$this->load->view('template', $this->data);
	}

	public function tabel_monitoring_sppd()
	{

		$np_karyawan = $this->input->post("np_karyawan");
		$tipe_perjalanan = $this->input->post("tipe_perjalanan");
		$start_date = $this->input->post("start_date");
		$end_date = $this->input->post("end_date");

		$this->load->model($this->folder_model . "M_monitoring_sppd");

		if ($_SESSION["grup"] == 4) //jika Pengadministrasi Unit Kerja
		{
			$ada_data = 0;
			$var = array();
			$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
			foreach ($list_pengadministrasi as $data) //looping list_pengadministrasi
			{
				array_push($var, $data['kode_unit']);
				$ada_data = 1;
			}
			if ($ada_data == 0) {
				$var = '';
			}
		} else
			if ($_SESSION["grup"] == 5) //jika Pengguna
		{
			$var 	= $_SESSION["no_pokok"];
		} else {
			$var = 1;
		}

		$list 	= $this->M_monitoring_sppd->get_datatables($var, $start_date, $end_date, $np_karyawan, $tipe_perjalanan);


		$data = array();
		$no = $_POST['start'];


		foreach ($list as $tampil) {
			$no++;
			$row = array();
			$row[] = $no;

			$row[] = $tampil->np_karyawan;
			$row[] = $tampil->nama;
			$row[] = $tampil->tipe_perjalanan;
			$row[] = $tampil->tujuan;
			$row[] = tanggal_indonesia($tampil->tgl_berangkat);
			$row[] = tanggal_indonesia($tampil->tgl_pulang);
			$row[] = $tampil->jenis_fasilitas;
			$row[] = $tampil->jenis_transportasi;
			$row[] = $tampil->biaya;
			$row[] = $tampil->biayaus;

			$perihal		= $tampil->perihal;
			$tgl_selesai	= datetime_indo($tampil->tgl_selesai);
			$no_surat 		= $tampil->no_surat;
			$hotel 			= $tampil->hotel;
			$nama_jabatan	= $tampil->nama_jabatan;
			$pangkat		= $tampil->pangkat;
			$unit 			= $tampil->unit;
			$kode_unit		= $tampil->kode_unit;

			$btn_text = 'Lihat detail';

			$row[] = "<button class='btn btn-primary btn-xs detail_button' data-toggle='modal' data-target='#modal_detail'
				data-perihal='$perihal'
				data-tgl_selesai='$tgl_selesai'
				data-no_surat='$no_surat'
				data-hotel='$hotel'
				data-nama_jabatan='$nama_jabatan'
				data-pangkat='$pangkat'
				data-unit='$unit'
				data-kode_unit='$kode_unit'
			>$btn_text</button>";

			$data[] = $row;
		}

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->M_monitoring_sppd->count_all($var, $start_date, $end_date, $np_karyawan, $tipe_perjalanan),
			"recordsFiltered" => $this->M_monitoring_sppd->count_filtered($var, $start_date, $end_date, $np_karyawan, $tipe_perjalanan),
			"data" => $data,
		);
		//output to json format
		echo json_encode($output);
	}

	function generateExcel()
	{
		$start_date = @$this->input->get('start_date', true) ? date('Y-m-d', strtotime($this->input->get('start_date', true))) : date('Y-m-01');
		$end_date = @$this->input->get('end_date', true) ? date('Y-m-d', strtotime($this->input->get('end_date', true))) : date('Y-m-t');
		$np_karyawan = @$this->input->get('np_karyawan', true) ? $this->input->get('np_karyawan', true) : '';
		$tipe_perjalanan = @$this->input->get('tipe_perjalanan', true) ? $this->input->get('tipe_perjalanan', true) : '';

		$filename = "{$start_date}-sampai-{$end_date}";

		$this->load->library('phpexcel');
		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);

		header("Content-type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=Data-Monitoring-SPPD-$filename.xlsx");
		header('Cache-Control: max-age=0');

		$excel = PHPExcel_IOFactory::createReader('Excel2007');
		$excel = $excel->load('./asset/excel-templates-monitoring-sppd/template-export-monitoring-sppd.xlsx');

		# proses isi data
		$this->db->select('ess_sppd_monitoring.id');
		$this->db->select('ess_sppd_monitoring.np_karyawan');
		$this->db->select('ess_sppd_monitoring.nama');
		$this->db->select('ess_sppd_monitoring.perihal');
		$this->db->select('ess_sppd_monitoring.tipe_perjalanan');
		$this->db->select('ess_sppd_monitoring.tujuan');
		$this->db->select('ess_sppd_monitoring.tgl_berangkat');
		$this->db->select('ess_sppd_monitoring.tgl_pulang');
		$this->db->select('ess_sppd_monitoring.tgl_selesai');
		$this->db->select('ess_sppd_monitoring.no_surat');
		$this->db->select('ess_sppd_monitoring.hotel');
		$this->db->select('ess_sppd_monitoring.jenis_transportasi');
		$this->db->select('ess_sppd_monitoring.jenis_fasilitas');
		$this->db->select('ess_sppd_monitoring.biaya');
		$this->db->select('ess_sppd_monitoring.biayaus');
		$this->db->select('ess_sppd_monitoring.nama_jabatan');
		$this->db->select('ess_sppd_monitoring.pangkat');
		$this->db->select('ess_sppd_monitoring.unit');
		$this->db->select('b.kode_unit');
		$this->db->from('ess_sppd_monitoring');
		$this->db->join('mst_karyawan b', 'ess_sppd_monitoring.np_karyawan=b.no_pokok', 'LEFT');
		$this->db->where('tgl_berangkat >=', $start_date);
		$this->db->where('tgl_berangkat <=', $end_date);

		if ($_SESSION["grup"] == 4) {
			$var = array();
			$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
			foreach ($list_pengadministrasi as $v) {
				array_push($var, $v['kode_unit']);
			}
			if ($var != []) {
				$this->db->where_in('kode_unit', $var);
			}
		} else if ($_SESSION["grup"] == 5) {
			$var = $_SESSION["no_pokok"];
			$this->db->where('np_karyawan', $var);
		}

		if (!empty($tipe_perjalanan)) {
			$this->db->where('tipe_perjalanan', $tipe_perjalanan);
		}

		if (!empty($np_karyawan) && $_SESSION["grup"] != 5) {
			$this->db->where('np_karyawan', $np_karyawan);
		}

		$data = $this->db->get()->result();

		$excel->setActiveSheetIndex(0);
		$kolom 	= 1;
		$awal 	= 6;
		$no = 1;

		$excel->getActiveSheet()->setCellValueExplicit('A3', 'Tanggal: ' . tanggal_indonesia($start_date) . ' sampai ' . tanggal_indonesia($end_date), PHPExcel_Cell_DataType::TYPE_STRING);

		foreach ($data as $row) {
			$excel->getActiveSheet()->setCellValue('A' . $awal, $no);
			$excel->getActiveSheet()->setCellValueExplicit('B' . $awal, $row->np_karyawan, PHPExcel_Cell_DataType::TYPE_STRING);
			$excel->getActiveSheet()->setCellValueExplicit('C' . $awal, $row->nama, PHPExcel_Cell_DataType::TYPE_STRING);
			$excel->getActiveSheet()->setCellValueExplicit('D' . $awal, $row->perihal, PHPExcel_Cell_DataType::TYPE_STRING);
			$excel->getActiveSheet()->setCellValueExplicit('E' . $awal, $row->tipe_perjalanan, PHPExcel_Cell_DataType::TYPE_STRING);
			$excel->getActiveSheet()->setCellValueExplicit('F' . $awal, $row->tujuan, PHPExcel_Cell_DataType::TYPE_STRING);
			$excel->getActiveSheet()->setCellValueExplicit('G' . $awal, tanggal_indonesia(date('Y-m-d', strtotime($row->tgl_berangkat))), PHPExcel_Cell_DataType::TYPE_STRING);
			$excel->getActiveSheet()->setCellValueExplicit('H' . $awal, tanggal_indonesia(date('Y-m-d', strtotime($row->tgl_pulang))), PHPExcel_Cell_DataType::TYPE_STRING);
			$excel->getActiveSheet()->setCellValueExplicit('I' . $awal, datetime_indo(date('Y-m-d', strtotime($row->tgl_selesai))), PHPExcel_Cell_DataType::TYPE_STRING);
			$excel->getActiveSheet()->setCellValueExplicit('J' . $awal, $row->no_surat, PHPExcel_Cell_DataType::TYPE_STRING);
			$excel->getActiveSheet()->setCellValueExplicit('K' . $awal, $row->hotel, PHPExcel_Cell_DataType::TYPE_STRING);
			$excel->getActiveSheet()->setCellValueExplicit('L' . $awal, $row->jenis_transportasi, PHPExcel_Cell_DataType::TYPE_STRING);
			$excel->getActiveSheet()->setCellValueExplicit('M' . $awal, $row->jenis_fasilitas, PHPExcel_Cell_DataType::TYPE_STRING);
			$excel->getActiveSheet()->setCellValueExplicit('N' . $awal, $row->biaya, PHPExcel_Cell_DataType::TYPE_STRING);
			$excel->getActiveSheet()->setCellValueExplicit('O' . $awal, $row->biayaus, PHPExcel_Cell_DataType::TYPE_STRING);
			$excel->getActiveSheet()->setCellValueExplicit('P' . $awal, $row->nama_jabatan, PHPExcel_Cell_DataType::TYPE_STRING);
			$excel->getActiveSheet()->setCellValueExplicit('Q' . $awal, $row->pangkat, PHPExcel_Cell_DataType::TYPE_STRING);
			$excel->getActiveSheet()->setCellValueExplicit('R' . $awal, $row->unit, PHPExcel_Cell_DataType::TYPE_STRING);
			$excel->getActiveSheet()->setCellValueExplicit('S' . $awal, $row->kode_unit, PHPExcel_Cell_DataType::TYPE_STRING);

			$no++;
			$awal++;
		}
		# END: proses isi data

		$objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
		$objWriter->setIncludeCharts(TRUE);
		$objWriter->setPreCalculateFormulas(TRUE);
		PHPExcel_Calculation::getInstance($excel)->clearCalculationCache();

		$objWriter->save('php://output');
	}
}
