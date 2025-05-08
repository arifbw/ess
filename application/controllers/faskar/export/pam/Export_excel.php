<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Export_excel extends CI_Controller {
	public function __construct(){
		parent::__construct();
		$this->load->helper("tanggal_helper");
	}

	function get_by_header($kode=null){
		if(empty($kode)){
			echo 'Parameter kode tidak ada';
			exit;
		}

		$cek_header = $this->db->where('kode', $kode)->get('ess_faskar_pam_header');
		if($cek_header->num_rows()!=1) {
			echo 'Parameter kode tidak valid';
			exit;
		}

		$header = $cek_header->row();
		$bulan_pemakaian = str_replace(' ','-', Ym_to_MY($header->pemakaian_bulan));
        $filename = "Data-Pemakaian-PAM-{$header->lokasi}-$bulan_pemakaian.xlsx";
        $this->load->library('phpexcel');

        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);

        header("Content-type: application/vnd.ms-excel");
        //nama file
        header("Content-Disposition: attachment; filename=Data-Pemakaian-PAM-{$header->lokasi}-$bulan_pemakaian.xlsx");
        header('Cache-Control: max-age=0');

        $excel = PHPExcel_IOFactory::createReader('Excel2007');
        $excel = $excel->load('./asset/excel-templates-faskar/template-export-faskar-pam.xlsx');

		$this->db->from('ess_faskar_pam_detail');
        $this->db->where('deleted_at is null');
		$this->db->where('faskar_pam_header_id', $header->id);
		$get = $this->db->get();

        $excel->setActiveSheetIndex(0);
        $kolom 	= 1;
        $awal 	= 8;
        $no = 1;

		$excel->getActiveSheet()->setCellValueExplicit('A1', "PEMAKAIAN PAM {$header->lokasi} BULAN ".strtoupper(Ym_to_MY($header->pemakaian_bulan)), PHPExcel_Cell_DataType::TYPE_STRING);
		$excel->getActiveSheet()->setCellValueExplicit('A2', 'PEMBAYARAN BULAN '.strtoupper(Ym_to_MY($header->pembayaran_bulan)), PHPExcel_Cell_DataType::TYPE_STRING);

        foreach($get->result() as $row){
			$excel->getActiveSheet()->getStyle('F'.$awal.':I'.$awal)->getNumberFormat()->setFormatCode("#,##0");

            $excel->getActiveSheet()->setCellValue('A'.$awal, $no);
            $excel->getActiveSheet()->setCellValueExplicit('B'.$awal, $row->np_karyawan, PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('C'.$awal, $row->nama_karyawan, PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('D'.$awal, $row->alamat, PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('E'.$awal, $row->no_rek, PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValue('F'.$awal, $row->pemakaian);
            $excel->getActiveSheet()->setCellValue('G'.$awal, $row->plafon);
            $excel->getActiveSheet()->setCellValue('H'.$awal, $row->beban_pegawai);
            $excel->getActiveSheet()->setCellValue('I'.$awal, $row->beban_perusahaan);
            $excel->getActiveSheet()->setCellValueExplicit('J'.$awal, $row->keterangan, PHPExcel_Cell_DataType::TYPE_STRING);

            $no++;
            $awal++;
        }

        $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $objWriter->setIncludeCharts(TRUE);
        $objWriter->setPreCalculateFormulas(TRUE);
        PHPExcel_Calculation::getInstance($excel)->clearCalculationCache();

        $objWriter->save('php://output');
    }
}