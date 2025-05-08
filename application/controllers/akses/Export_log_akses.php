<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Export_log_akses extends CI_Controller {
	public function __construct(){
		parent::__construct();
		$this->load->helper("tanggal_helper");
	}

    function generate(){
        $start_date = @$this->input->get('start_date') ? date('Y-m-d', strtotime($this->input->get('start_date'))) : date('Y-m-01');
        $end_date = @$this->input->get('end_date') ? date('Y-m-d', strtotime($this->input->get('end_date'))) : date('Y-m-t');

        $filename = "{$start_date}-sampai-{$end_date}";

        $this->load->library('phpexcel');
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);

        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=Data-Log-Akses-$filename.xlsx");
        header('Cache-Control: max-age=0');

        $excel = PHPExcel_IOFactory::createReader('Excel2007');
        $excel = $excel->load('./asset/excel-templates-log-akses/template-export-log-akses.xlsx');

        # proses isi data
        $data = $this->db->from('login_history')
            ->where("DATE_FORMAT(timestamp,'%Y-%m-%d') >=",$start_date)
            ->where("DATE_FORMAT(timestamp,'%Y-%m-%d') <=",$end_date)
            ->order_by('timestamp', 'DESC')
            ->get()->result();
        
        $excel->setActiveSheetIndex(0);
        $kolom 	= 1;
        $awal 	= 6;
        $no = 1;

        $excel->getActiveSheet()->setCellValueExplicit('A3', 'Tanggal: '.tanggal_indonesia($start_date).' sampai '.tanggal_indonesia($end_date), PHPExcel_Cell_DataType::TYPE_STRING);

        foreach($data as $row){
            $excel->getActiveSheet()->setCellValue('A'.$awal, $no);
            $excel->getActiveSheet()->setCellValueExplicit('B'.$awal, $row->np ?: $row->username, PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('C'.$awal, $row->modul, PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('D'.$awal, $row->description, PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('E'.$awal, $row->input_from, PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('F'.$awal, $row->ip_address, PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('G'.$awal, $row->timestamp, PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('H'.$awal, $row->user_agent, PHPExcel_Cell_DataType::TYPE_STRING);

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