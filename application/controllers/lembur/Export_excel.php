<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Export_excel extends CI_Controller {
    public function __construct(){
        parent::__construct();
        $this->load->helper("karyawan_helper");
        $this->load->helper("tanggal_helper");
        $this->load->helper("cutoff_helper");
    }

    function all_per_bulan($start_date, $end_date){
        // if($month) $bln = $month;
        // else $bln = date('Y-m');

        $bln = "{$start_date}-{$end_date}";
        
        $filename = "Pengajuan-Lembur-Seluruh-Unit-Bulan-$bln.xlsx";

        $this->load->library('phpexcel'); 

        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);

        $excel = PHPExcel_IOFactory::createReader('Excel2007');
        $excel = $excel->load('./asset/excel-templates-lembur/Export-Pengajuan-Lembur-All-per-Bulan.xlsx');
        
        $excel->setActiveSheetIndex(1);
        $excel->getActiveSheet()->setCellValue('A3', 'PERIODE: '.$bln);
        
        $start_date_ymd = date('Y-m-d', strtotime($start_date));
        $end_date_ymd = date('Y-m-d', strtotime($end_date));
        if($_SESSION["grup"]==4) {
            $list_kode_unit = array();
            $list_pengadministrasi = $_SESSION["list_pengadministrasi"];
            foreach ($list_pengadministrasi as $row) {	
                array_push($list_kode_unit,$row['kode_unit']);								
            }	
            
            if($list_kode_unit!=[]) $this->db->where_in('kode_unit', $list_kode_unit);
            else $this->db->where_in('kode_unit', [null]);
        }
        $get_lembur = $this->db->where("(tgl_dws BETWEEN '{$start_date_ymd}' AND '{$end_date_ymd}')",null,false)->get('ess_lembur_transaksi')->result();
        // $get_lembur = $this->db->where("date_format(tgl_dws, \"%Y-%m\")=\"$bln\"")->get('ess_lembur_transaksi')->result();

        $awal = 8;
        $no = 1;

        foreach($get_lembur as $row){
            $excel->getActiveSheet()->insertNewRowBefore($awal,1);
            $excel->getActiveSheet()->setCellValue('A'.$awal, $no);
            $excel->getActiveSheet()->setCellValue('B'.$awal, $row->no_pokok);
            $excel->getActiveSheet()->setCellValue('C'.$awal, $row->nama);
            $excel->getActiveSheet()->setCellValue('D'.$awal, tanggal_indonesia($row->tgl_dws));
            $excel->getActiveSheet()->setCellValue('E'.$awal, tanggal_indonesia($row->tgl_mulai).' '.date('H:i', strtotime($row->jam_mulai)));
            $excel->getActiveSheet()->setCellValue('F'.$awal, tanggal_indonesia($row->tgl_selesai).' '.date('H:i', strtotime($row->jam_selesai)));
            $excel->getActiveSheet()->setCellValue('G'.$awal, $row->waktu_mulai_fix==null || $row->waktu_selesai_fix==null || $row->waktu_mulai_fix=='' || $row->waktu_selesai_fix=='' ? '-' : datetime_indo($row->waktu_mulai_fix).' s/d '.datetime_indo($row->waktu_selesai_fix));
            $excel->getActiveSheet()->setCellValue('H'.$awal, $row->alasan);

            // total jam
            // $start = new DateTime("{$row->tgl_mulai} {$row->jam_mulai}");
            // $end = new DateTime("{$row->tgl_selesai} {$row->jam_selesai}");
            
            // total jam => ganti jadi fix
            if( $row->waktu_mulai_fix==null || $row->waktu_selesai_fix==null || $row->waktu_mulai_fix=='' || $row->waktu_selesai_fix=='' ){
                $start = new DateTime("{$row->tgl_mulai} {$row->jam_mulai}");
                $end = new DateTime("{$row->tgl_selesai} {$row->jam_selesai}");
            } else{
                $start = new DateTime($row->waktu_mulai_fix);
                $end = new DateTime("$row->waktu_selesai_fix");
            }

            $interval = $end->diff($start);

            $hours = $interval->h;
            $hours += $interval->days * 24;

            $minutes = $interval->i;
            $minutes += $hours * 60;

            $count_hours = floor($minutes / 60);
            $count_minutes = $minutes % 60;

            if($count_hours > 0){
                if($count_minutes>=45) $count_hours+=1;
            }

            $excel->getActiveSheet()->setCellValue('I'.$awal, $count_hours);

            // status
            $status = '';
            if($row->waktu_mulai_fix==null || $row->waktu_selesai_fix==null || $row->waktu_mulai_fix=='' || $row->waktu_selesai_fix=='' || $row->waktu_mulai_fix=='00:00:00' || $row->waktu_selesai_fix=='00:00:00') {
                $status = 'Tidak Diakui';
            } else if($row->approval_status=='1') {
                $status = 'Disetujui SDM';
            } else if($row->approval_status=='2') {
                $status = 'Ditolak SDM';
            } else if($row->approval_status=='0' || $row->approval_status==null || $row->approval_status=='') {
                if($row->approval_pimpinan_status=='1') {
                    $status = 'Disetujui Atasan';
                } else if($row->approval_pimpinan_status=='2') {
                    $status = 'Ditolak Atasan';
                } else if($row->approval_pimpinan_status=='0' || $row->approval_pimpinan_status==null || $row->approval_pimpinan_status=='') {
                    $status = 'Menunggu Persetujuan';
                }
            } else {
                $status = 'Tidak Valid';
            }
            $excel->getActiveSheet()->setCellValue('J'.$awal, $status);

            $awal++;
            $no++;
        }

        $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $objWriter->setIncludeCharts(TRUE);
        $objWriter->setPreCalculateFormulas(TRUE);
        PHPExcel_Calculation::getInstance($excel)->clearCalculationCache();

        header("Content-type: application/vnd.ms-excel");
        //nama file
        header("Content-Disposition: attachment; filename=Pengajuan-Lembur-Seluruh-Unit-Bulan-$bln.xlsx");
        header('Cache-Control: max-age=0');

        $objWriter->save('php://output');
        // $objWriter->save(APPPATH.'../uploads/rekap/transportasi/'.$filename, 'F');
    }
}