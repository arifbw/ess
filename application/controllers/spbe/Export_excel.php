<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Export_excel extends CI_Controller {
	public function __construct(){
		parent::__construct();
		$this->load->helper("tanggal_helper");
	}

    function generate(){
        $start_date = @$this->input->get('start_date',true) ? date('Y-m-d', strtotime($this->input->get('start_date',true))) : date('Y-m-01');
        $end_date = @$this->input->get('end_date',true) ? date('Y-m-d', strtotime($this->input->get('end_date',true))) : date('Y-m-t');

        $filename = "{$start_date}-sampai-{$end_date}";

        $this->load->library('phpexcel');
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);

        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=Data-Permohonan-SPBE-$filename.xlsx");
        header('Cache-Control: max-age=0');

        $excel = PHPExcel_IOFactory::createReader('Excel2007');
        $excel = $excel->load('./asset/excel-templates-spbi-spbe/template-export-spbe.xlsx');

        # proses isi data
        if( @$this->input->get('id',true) ) $this->db->where('uuid', $this->input->get('id',true) );
        $data = $this->db->from('ess_permohonan_spbe')
            ->where('deleted_at IS NULL',null,false)
            ->where('canceled_at IS NULL',null,false)
            ->where("DATE_FORMAT(created_at,'%Y-%m-%d') >=",$start_date)
            ->where("DATE_FORMAT(created_at,'%Y-%m-%d') <=",$end_date)
            ->order_by('keluar_tanggal', 'DESC')
            ->order_by('updated_at', 'DESC')
            ->order_by('created_at', 'DESC')
            ->get()->result();
        
        $excel->setActiveSheetIndex(0);
        $kolom 	= 1;
        $awal 	= 6;
        $no = 1;

        $excel->getActiveSheet()->setCellValueExplicit('A3', 'Tanggal: '.tanggal_indonesia($start_date).' sampai '.tanggal_indonesia($end_date), PHPExcel_Cell_DataType::TYPE_STRING);

        foreach($data as $row){
            $excel->getActiveSheet()->setCellValue('A'.$awal, $no);
            $excel->getActiveSheet()->setCellValueExplicit('B'.$awal, $row->nomor_surat, PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('C'.$awal, $row->nama, PHPExcel_Cell_DataType::TYPE_STRING);
            $excel->getActiveSheet()->setCellValueExplicit('D'.$awal, tanggal_indonesia(date('Y-m-d', strtotime($row->created_at))), PHPExcel_Cell_DataType::TYPE_STRING);

            $status = '';
            if( $row->approval_atasan_status == null ){
                $status = 'Menunggu Persetujuan Atasan';
            } else if( $row->approval_atasan_status == '2' ){
                $status = 'Ditolak Atasan';
            } else if( $row->pengecek1_status == null ){
                $status = 'Menunggu Petugas Pamsiknilmat';
            } else if( $row->pengecek1_status == '2' ){
                $status = 'Ditolak Petugas Pamsiknilmat';
            } else if( $row->konfirmasi_pengguna == null ){
                $status = 'Menunggu Konfirmasi Pemohon';
            } else if( $row->konfirmasi_pengguna == '2' ){
                $status = 'Ditolak Pemohon';
            } else if( $row->danposko_status == null ){
                $status = 'Menunggu Persetujuan Komandan Posko';
            } else if( $row->danposko_status == '2' ){
                $status = 'Ditolak Komandan Posko';
            } else if( $row->approval_pengamanan_keluar == null ){
                $status = 'Menunggu Persetujuan Admin Pamsiknilmat';
            } else{
                $status = $row->kondisi_barang_keluar=='2' ? 'Barang Keluar Sebagian' : 'Pengeluaran Selesai';
                if( $row->approval_pengamanan_masuk == null ){
                    $status .= "\n(Menunggu Barang Masuk)";
                } else{
                    $status = 'Barang Telah Selesai Masuk';
                    if( $row->konfirmasi_pembawa_status == null ){
                        $status .= "\n(Pembawa Barang Belum Konfirmasi)";
                    }
                } 
            }
            $excel->getActiveSheet()->setCellValueExplicit('E'.$awal, $status);

            $kembali = '';
            if( $row->barang_kembali=='1' ) $kembali = 'Ya';
            else $kembali = 'Tidak';
            $excel->getActiveSheet()->setCellValueExplicit('F'.$awal, $kembali);

            $proses = '-';
            if( $row->approval_pengamanan_keluar==null ){
                if( $row->approval_atasan_status!='2' ) $proses = 'Proses Persetujuan';
            } else if( $row->approval_pengamanan_keluar!=null ){
                $proses = $row->kondisi_barang_keluar=='2' ? 'Keluar Sebagian' : 'Sudah Keluar Perusahaan';
                if( $row->barang_kembali=='1' ){
                    if( $row->approval_pengamanan_masuk!=null ){
                        $proses = 'Sudah Kembali ke Perusahaan';
                    }
                }
            }
            $excel->getActiveSheet()->setCellValueExplicit('G'.$awal, $proses, PHPExcel_Cell_DataType::TYPE_STRING);

            # pos keluar yg dilewati
            if( !in_array($row->pos_keluar,[null,'null']) ){
                $text_pos_keluar = '';
                $pos_keluar = json_decode($row->pos_keluar);
                foreach ($pos_keluar as $value) {
                    $text_pos_keluar .= "{$value->nama};";
                    $text_pos_keluar .= "\n";
                }
                $excel->getActiveSheet()->setCellValueExplicit('H'.$awal, $text_pos_keluar, PHPExcel_Cell_DataType::TYPE_STRING);
            }
            
            # pos masuk yg dilewati
            if( !in_array($row->pos_masuk,[null,'null']) ){
                $text_pos_masuk = '';
                $pos_masuk = json_decode($row->pos_masuk);
                foreach ($pos_masuk as $value) {
                    $text_pos_masuk .= "{$value->nama};";
                    $text_pos_masuk .= "\n";
                }
                $excel->getActiveSheet()->setCellValueExplicit('I'.$awal, $text_pos_masuk, PHPExcel_Cell_DataType::TYPE_STRING);
            }

            # Atasan
            $excel->getActiveSheet()->setCellValueExplicit('J'.$awal, $row->approval_atasan_np .' - '. $row->approval_atasan_nama, PHPExcel_Cell_DataType::TYPE_STRING);
            if( $row->approval_atasan_status!=null ){
                $excel->getActiveSheet()->setCellValueExplicit('K'.$awal, ($row->approval_atasan_status=='1' ? 'Setuju':'Tolak'), PHPExcel_Cell_DataType::TYPE_STRING);
            }
            if( $row->approval_atasan_updated_at!=null ){
                $excel->getActiveSheet()->setCellValueExplicit('L'.$awal, datetime_indo($row->approval_atasan_updated_at), PHPExcel_Cell_DataType::TYPE_STRING);
            }

            # pengecek
            $excel->getActiveSheet()->setCellValueExplicit('M'.$awal, $row->pengecek1_np .' - '. $row->pengecek1_nama, PHPExcel_Cell_DataType::TYPE_STRING);
            if( $row->pengecek1_status!=null ){
                $excel->getActiveSheet()->setCellValueExplicit('N'.$awal, ($row->pengecek1_status=='1' ? 'Setuju':'Tolak'), PHPExcel_Cell_DataType::TYPE_STRING);
            }
            if( $row->pengecek1_tanggal!=null && $row->pengecek1_jam!=null ){
                $datetime = $row->pengecek1_tanggal.' '.$row->pengecek1_jam;
                $excel->getActiveSheet()->setCellValueExplicit('O'.$awal, datetime_indo($datetime), PHPExcel_Cell_DataType::TYPE_STRING);
            }

            # pengawas
            $excel->getActiveSheet()->setCellValueExplicit('P'.$awal, $row->konfirmasi_pengguna_np .' - '. $row->konfirmasi_pengguna_nama, PHPExcel_Cell_DataType::TYPE_STRING);
            if( $row->konfirmasi_pengguna!=null ){
                $excel->getActiveSheet()->setCellValueExplicit('Q'.$awal, ($row->konfirmasi_pengguna=='1' ? 'Setuju':'Tolak'), PHPExcel_Cell_DataType::TYPE_STRING);
            }
            if( $row->konfirmasi_pengguna_tanggal!=null && $row->konfirmasi_pengguna_jam!=null ){
                $datetime = $row->konfirmasi_pengguna_tanggal.' '.$row->konfirmasi_pengguna_jam;
                $excel->getActiveSheet()->setCellValueExplicit('R'.$awal, datetime_indo($datetime), PHPExcel_Cell_DataType::TYPE_STRING);
            }

            # danposko
            $excel->getActiveSheet()->setCellValueExplicit('S'.$awal, $row->danposko_np .' - '. $row->danposko_nama, PHPExcel_Cell_DataType::TYPE_STRING);
            if( $row->danposko_status!=null ){
                $excel->getActiveSheet()->setCellValueExplicit('T'.$awal, ($row->danposko_status=='1' ? 'Setuju':'Tolak'), PHPExcel_Cell_DataType::TYPE_STRING);
            }
            if( $row->danposko_tanggal!=null && $row->danposko_jam!=null ){
                $datetime = $row->danposko_tanggal.' '.$row->danposko_jam;
                $excel->getActiveSheet()->setCellValueExplicit('U'.$awal, datetime_indo($datetime), PHPExcel_Cell_DataType::TYPE_STRING);
            }

            # pos keluar/masuk
            if( !in_array($row->approval_pengamanan_posisi, [null,'null']) ){
                $approval_pengamanan_posisi = json_decode($row->approval_pengamanan_posisi);
                $text_keluar = '';
                foreach ($approval_pengamanan_posisi as $value) {
                    if( $value->posisi=='keluar' && $value->deleted_at==null ){
                        $tanggal = tanggal_indonesia($value->tanggal);
                        $text_keluar .= "{$value->pos_nama} | Oleh {$value->approval_nama} | Keluar pada {$tanggal}, {$value->jam}";
                        $text_keluar .= "\nKeterangan: {$value->keterangan}";
                        $text_keluar .= "\n\n";
                    }
                }
                $excel->getActiveSheet()->setCellValueExplicit('V'.$awal, $text_keluar, PHPExcel_Cell_DataType::TYPE_STRING);

                $text_masuk = '';
                foreach ($approval_pengamanan_posisi as $value) {
                    if( $value->posisi=='masuk' && $value->deleted_at==null ){
                        $tanggal = tanggal_indonesia($value->tanggal);
                        $text_masuk .= "{$value->pos_nama} | Oleh {$value->approval_nama} | Masuk pada {$tanggal}, {$value->jam}";
                        $text_masuk .= "\nKeterangan: {$value->keterangan}";
                        $text_masuk .= "\n\n";
                    }
                }
                $excel->getActiveSheet()->setCellValueExplicit('W'.$awal, $text_masuk, PHPExcel_Cell_DataType::TYPE_STRING);
            }

            # pembawa
            $excel->getActiveSheet()->setCellValueExplicit('X'.$awal, $row->konfirmasi_pengguna_np .' - '. $row->konfirmasi_pengguna_nama, PHPExcel_Cell_DataType::TYPE_STRING);
            if( $row->konfirmasi_pembawa_status!=null ){
                $excel->getActiveSheet()->setCellValueExplicit('Y'.$awal, ($row->konfirmasi_pembawa_status=='1' ? 'Setuju':'Tolak'), PHPExcel_Cell_DataType::TYPE_STRING);
            }
            if( $row->konfirmasi_pembawa_tanggal!=null && $row->konfirmasi_pembawa_jam!=null ){
                $datetime = $row->konfirmasi_pembawa_tanggal.' '.$row->konfirmasi_pembawa_jam;
                $excel->getActiveSheet()->setCellValueExplicit('Z'.$awal, datetime_indo($datetime), PHPExcel_Cell_DataType::TYPE_STRING);
            }

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