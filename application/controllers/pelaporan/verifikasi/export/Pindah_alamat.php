<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Pindah_alamat extends CI_Controller {
	public function __construct(){
		parent::__construct();
		$this->load->helper("tanggal_helper");
	}

    function excel(){
        $this->load->library('phpexcel');

        $status = $this->input->get('status');
        $string_status = array(
            'Menunggu Persetujuan Atasan',
            'Disetujui Atasan',
            'Ditolak Atasan',
            'Verifikasi KAUN SDM',
            'Ditolak KAUN SDM',
            'Submit ERP',
            'Ditolak Admin SDM',
        );

        if($status!='all'){
            $this->db->where('approval_status',$status);
        }
        $this->db->where('deleted_at IS NULL');
        $get = $this->db->get('ess_laporan_pindah_alamat')->result();

        $excel = new PHPExcel();
        $excel->setActiveSheetIndex(0);

        $excel->setActiveSheetIndex(0)->mergeCells('A1:R1');
        $excel->getActiveSheet()->setCellValueExplicit('A1', "Verifikasi Laporan Pindah Alamat", PHPExcel_Cell_DataType::TYPE_STRING);
        $excel->getActiveSheet()->getStyle('A1:R1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $excel->setActiveSheetIndex(0)->getStyle('A1')->getFont()->setBold(true);

        $judul = array(
            'No',
            'Pegawai',
            'Unit Kerja',
            'Alamat Lama',
            'Kode Pos Lama',
            'Kelurahan Lama',
            'Kecamatan Lama',
            'Kabupaten Lama',
            'Provinsi Lama',
            'Alamat Baru',
            'Kode Pos Baru',
            'Kelurahan Baru',
            'Kecamatan Baru',
            'Kabupaten Baru',
            'Provinsi Baru',
            'Keterangan',
            'Dibuat Tanggal',
            'Status'
        );
        $alphabet = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R');
        foreach ($alphabet as $key => $value) {
            $excel->setActiveSheetIndex(0)->setCellValue($value.'3', $judul[$key]);
        }
        $excel->setActiveSheetIndex(0)->getStyle('A3:R3')->getFont()->setBold(true);

        $awal = 4;
        $no = 1;

        foreach($get as $row){
            $excel->getActiveSheet()->setCellValue('A'.$awal, $no);
            $excel->getActiveSheet()->setCellValueExplicit('B'.$awal, $row->np_karyawan.' - '.$row->nama_karyawan, PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('C'.$awal, $row->nama_unit, PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('D'.$awal, $row->alamat_lama, PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('E'.$awal, $row->kode_pos_lama, PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('F'.$awal, $row->kelurahan_lama, PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('G'.$awal, $row->kecamatan_lama, PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('H'.$awal, $row->kabupaten_lama, PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('I'.$awal, $row->provinsi_lama, PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('J'.$awal, $row->alamat_baru, PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('K'.$awal, $row->kode_pos_baru, PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('L'.$awal, $row->kelurahan_baru, PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('M'.$awal, $row->kecamatan_baru, PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('N'.$awal, $row->kabupaten_baru, PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('O'.$awal, $row->provinsi_baru, PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('P'.$awal, $row->keterangan, PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('Q'.$awal, datetime_indo($row->created_at), PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('R'.$awal, $string_status[$row->approval_status], PHPExcel_Cell_DataType::TYPE_STRING);

            $no++;
            $awal++;
        }
        $awal--;

        $excel->setActiveSheetIndex(0)->getStyle('A3:R'.$awal)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);        
        foreach ($alphabet as $key => $value) {
            $excel->setActiveSheetIndex(0)->getColumnDimension($value)->setAutoSize(true);
        }

        $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $objWriter->setIncludeCharts(TRUE);
        $objWriter->setPreCalculateFormulas(TRUE);
        PHPExcel_Calculation::getInstance($excel)->clearCalculationCache();

        ob_start();
        $objWriter->save('php://output');
        $xlsData = ob_get_contents();
        ob_end_clean();

        $response =  array(
            'status' => TRUE,
            'file' => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData),
            'name' => "Data Verifikasi Pelaporan Pindah Alamat"
        );
        echo json_encode($response);
    }
}