<?php
  defined('BASEPATH') OR exit('No direct script access allowed');
  
  class Excel extends CI_Controller {  
    
	//Rekap Lembur
    public function index(){
      // Load plugin PHPExcel nya
      include APPPATH.'third_party/phpexcel/PHPExcel.php';
      
      // Panggil class PHPExcel nya
      $excel = new PHPExcel();
      // Settingan awal fil excel
      $excel->getProperties()->setCreator('PERURI ESS')
                   ->setLastModifiedBy('Admin PERURI ESS')
                   ->setTitle("Rekap Lembur")
                   ->setSubject("Rekap")
                   ->setDescription("Laporan Rekap Lembur")
                   ->setKeywords("Data Lembur");
      // Buat sebuah variabel untuk menampung pengaturan style dari header tabel
      $style_col = array(
        'font' => array('bold' => true), // Set font nya jadi bold
        'alignment' => array(
          'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
          'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
        ),
        'borders' => array(
          'top' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border top dengan garis tipis
          'right' => array('style'  => PHPExcel_Style_Border::BORDER_THIN),  // Set border right dengan garis tipis
          'bottom' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border bottom dengan garis tipis
          'left' => array('style'  => PHPExcel_Style_Border::BORDER_THIN) // Set border left dengan garis tipis
        )
      );
      // Buat sebuah variabel untuk menampung pengaturan style dari isi tabel
      $style_row = array(
        'alignment' => array(
          'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
        ),
        'borders' => array(
          'top' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border top dengan garis tipis
          'right' => array('style'  => PHPExcel_Style_Border::BORDER_THIN),  // Set border right dengan garis tipis
          'bottom' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border bottom dengan garis tipis
          'left' => array('style'  => PHPExcel_Style_Border::BORDER_THIN) // Set border left dengan garis tipis
        )
      );
      
      $excel->setActiveSheetIndex(0)->setCellValue('A1', "FORM REKAP LEMBUR"); // Set kolom A1
      $excel->getActiveSheet()->mergeCells('A1:M1'); // Set Merge Cell pada kolom A1 sampai E1
      $excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(TRUE); // Set bold kolom A1
      $excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(15); // Set font size 15 untuk kolom A1
      $excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); // Set text center untuk kolom A1

      //filter
      $tgl_mulai = date('Y-m-d', strtotime($this->input->post('tgl_mulai')));
      $tgl_selesai = date('Y-m-d', strtotime($this->input->post('tgl_selesai')));
      $lokasi = $this->input->post('lokasi');

      $excel->setActiveSheetIndex(0)->setCellValue('A2', "Tanggal");
      $excel->getActiveSheet()->mergeCells('A2:B2');
      $excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(TRUE);
      $excel->getActiveSheet()->getStyle('A2')->getFont()->setSize(15);

      $excel->setActiveSheetIndex(0)->setCellValue('C2', ":");
      $excel->setActiveSheetIndex(0)->setCellValue('D2', date('d/m/Y', strtotime($tgl_mulai)).' - '.date('d/m/Y', strtotime($tgl_selesai)));

      $excel->setActiveSheetIndex(0)->setCellValue('A3', "Lokasi");
      $excel->getActiveSheet()->mergeCells('A3:B3');
      $excel->getActiveSheet()->getStyle('A3')->getFont()->setBold(TRUE);
      $excel->getActiveSheet()->getStyle('A3')->getFont()->setSize(15);

      $excel->setActiveSheetIndex(0)->setCellValue('C3', ":");
      $excel->setActiveSheetIndex(0)->setCellValue('D3', $lokasi == '1' ? 'Jakarta' : 'Karawang');

      // Buat header tabel nya pada baris ke 3
      $excel->setActiveSheetIndex(0)->setCellValue('A6', "NO");
      $excel->getActiveSheet()->mergeCells('A6:A7');
      $excel->setActiveSheetIndex(0)->setCellValue('B6', "UNIT KERJA");
      $excel->getActiveSheet()->mergeCells('B6:B7');
      $excel->setActiveSheetIndex(0)->setCellValue('C6', "JENIS GILIR");
      $excel->getActiveSheet()->mergeCells('C6:C7');
      $excel->setActiveSheetIndex(0)->setCellValue('D6', "ORDER");
	  $excel->setActiveSheetIndex(0)->setCellValue('K6', "KETERANGAN");
      $excel->getActiveSheet()->mergeCells('K6:K7');

      //get column makanan
      $makanan = $this->db->select('jenis_pesanan')
                    ->from('ess_pemesanan_makan_lembur')
                    ->group_start()
                    ->where(['tanggal_pemesanan >=' => $tgl_mulai, 'tanggal_pemesanan <=' => $tgl_selesai])
                    ->group_end()
                    ->where(['lokasi_lembur' => $lokasi, 'verified' => 3])
                    ->get()->result();

      $all_makanan = [];
      foreach ($makanan as $value) {
            $mkn = json_decode($value->jenis_pesanan, TRUE);
            $mkn = array_column($mkn, 'nama_makanan', 'id_makanan');
            
            foreach ($mkn as $key => $val) {
                if(!in_array($val, $all_makanan)){
                    array_push($all_makanan, $val);
                }
            }
      }
      
      $label = 'D';
      $list_column = [];
      foreach ($all_makanan as $key) {
          $excel->setActiveSheetIndex(0)->setCellValue($label.'7', $key);
          $excel->getActiveSheet()->getStyle($label.'7')->applyFromArray($style_col);

          array_push($list_column, [
            'cell' => $label,
            'nama_makanan' => $key
          ]);
          $label_old = $label;
          ++$label;
      }

      $excel->setActiveSheetIndex(0)->setCellValue($label.'6', "TOTAL");
      $excel->getActiveSheet()->mergeCells($label.'6:'.$label.'7');

      $excel->getActiveSheet()->mergeCells('D6:'.$label_old.'6');

      // Apply style header yang telah kita buat tadi ke masing-masing kolom header
      $excel->getActiveSheet()->getStyle('A6:A7')->applyFromArray($style_col);
      $excel->getActiveSheet()->getStyle('B6:B7')->applyFromArray($style_col);
      $excel->getActiveSheet()->getStyle('C6:C7')->applyFromArray($style_col);
      $excel->getActiveSheet()->getStyle('D6:'.$label_old.'6')->applyFromArray($style_col);
      $excel->getActiveSheet()->getStyle($label.'6:'.$label.'7')->applyFromArray($style_col);
	  $excel->getActiveSheet()->getStyle('K6:K7')->applyFromArray($style_col);
      // Panggil function view yang ada di SiswaModel untuk menampilkan semua data siswanya
      
      $no = 1; // Untuk penomoran tabel, di awal set dengan 1
      $numrow = 8; // Set baris pertama untuk isi tabel adalah baris ke 4

      $unit = $this->db->select('id,kode_unit, nama_unit, jenis_lembur,keterangan')
      			->from('ess_pemesanan_makan_lembur')
      			->group_start()
                ->where(['tanggal_pemesanan >=' => $tgl_mulai, 'tanggal_pemesanan <=' => $tgl_selesai])
                ->group_end()
                ->where(['lokasi_lembur' => $lokasi, 'verified' => 3])
                ///21 01 2022 7648 Tri Wibowo - Permintaan mas eka pak firman
				//->group_by('kode_unit')
                ->get()->result();
      $total_makan = [];
      $jml_total = 0;
      foreach ($unit as $value) {
            $dft = $this->db->select('jenis_pesanan')
                    ->from('ess_pemesanan_makan_lembur')
                    ->group_start()
	                ->where(['tanggal_pemesanan >=' => $tgl_mulai, 'tanggal_pemesanan <=' => $tgl_selesai])
	                ->group_end()
	                ->where(['lokasi_lembur' => $lokasi, 'verified' => 3])
                    ->where('id', $value->id)
                    ->get()->result();

            foreach ($dft as $val) {
                $makan = json_decode($val->jenis_pesanan, TRUE);

                foreach ($makan as $in) {
                    $key = array_search($in['nama_makanan'], array_column($total_makan, 'nama_makanan'));

                    if($key === false){
                        array_push($total_makan, [
                            'id_makanan' => $in['id_makanan'],
                            'nama_makanan' => $in['nama_makanan'],
                            'jumlah' => $in['jumlah']
                        ]);
                    }else{
                        $total_makan[$key]['jumlah'] = $total_makan[$key]['jumlah'] + $in['jumlah'];
                    }

                    $jml_total += $in['jumlah'];
                }
            }

            foreach ($total_makan as $tot) {
                $col = array_search($tot['nama_makanan'], array_column($list_column, 'nama_makanan'));
                
                $excel->setActiveSheetIndex(0)->setCellValue($list_column[$col]['cell'].$numrow, $tot['jumlah']);
            }

            $excel->setActiveSheetIndex(0)->setCellValue($label.$numrow, $jml_total);

            $excel->setActiveSheetIndex(0)->setCellValue('A'.$numrow, $no);
            $excel->setActiveSheetIndex(0)->setCellValue('B'.$numrow, $value->nama_unit);
			$excel->setActiveSheetIndex(0)->setCellValue('C'.$numrow, $value->jenis_lembur);
			///21 01 2022 7648 Tri Wibowo - Permintaan mas eka pak firman
			$excel->setActiveSheetIndex(0)->setCellValue('K'.$numrow, $value->keterangan);
			//$excel->setActiveSheetIndex(0)->setCellValue('G'.$numrow, 'xxx');
			
            $jml_total = 0;
            $total_makan = [];
            $numrow++;
            $no++;
      }

      // Set width kolom
      $excel->getActiveSheet()->getColumnDimension('A')->setWidth(5); // Set width kolom A
      $excel->getActiveSheet()->getColumnDimension('B')->setWidth(15); // Set width kolom B
      $excel->getActiveSheet()->getColumnDimension('C')->setWidth(25); // Set width kolom C
      $excel->getActiveSheet()->getColumnDimension('D')->setWidth(20); // Set width kolom D
      $excel->getActiveSheet()->getColumnDimension('E')->setWidth(30); // Set width kolom E
      
      // Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
      $excel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(-1);
      // Set orientasi kertas jadi LANDSCAPE
      $excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
      // Set judul file excel nya
      $excel->getActiveSheet(0)->setTitle("Rekap Lembur");
      $excel->setActiveSheetIndex(0);
      // Proses file excel
      header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
      header('Content-Disposition: attachment; filename="Rekap Lembur.xlsx"'); // Set nama file excel nya
      header('Cache-Control: max-age=0');
      $write = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
      $write->save('php://output');
    }

    public function monitoring(){
      // Load plugin PHPExcel nya
      include APPPATH.'third_party/phpexcel/PHPExcel.php';
      
      // Panggil class PHPExcel nya
      $excel = new PHPExcel();
      // Settingan awal fil excel
      $excel->getProperties()->setCreator('PERURI ESS')
                   ->setLastModifiedBy('Admin PERURI ESS')
                   ->setTitle("Rekap Lembur")
                   ->setSubject("Rekap")
                   ->setDescription("Rekap Lembur Peruri")
                   ->setKeywords("Rekap Lembur");
      // Buat sebuah variabel untuk menampung pengaturan style dari header tabel
      $style_col = array(
        'font' => array('bold' => true), // Set font nya jadi bold
        'alignment' => array(
          'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
          'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
        ),
        'borders' => array(
          'top' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border top dengan garis tipis
          'right' => array('style'  => PHPExcel_Style_Border::BORDER_THIN),  // Set border right dengan garis tipis
          'bottom' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border bottom dengan garis tipis
          'left' => array('style'  => PHPExcel_Style_Border::BORDER_THIN) // Set border left dengan garis tipis
        )
      );
      // Buat sebuah variabel untuk menampung pengaturan style dari isi tabel
      $style_row = array(
        'alignment' => array(
          'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
        ),
        'borders' => array(
          'top' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border top dengan garis tipis
          'right' => array('style'  => PHPExcel_Style_Border::BORDER_THIN),  // Set border right dengan garis tipis
          'bottom' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border bottom dengan garis tipis
          'left' => array('style'  => PHPExcel_Style_Border::BORDER_THIN) // Set border left dengan garis tipis
        )
      );
      
      $excel->setActiveSheetIndex(0)->setCellValue('A1', "MONITORING PENGIRIMAN NASI LEMBUR PEGAWAI"); // Set kolom A1
      $excel->getActiveSheet()->mergeCells('A1:F1'); // Set Merge Cell pada kolom A1 sampai E1
      $excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(TRUE); // Set bold kolom A1
      $excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(15); // Set font size 15 untuk kolom A1
      $excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); // Set text center untuk kolom A1

      //filter
      $tgl_mulai = date('Y-m-d', strtotime($this->input->post('tgl_mulai')));
      $tgl_selesai = date('Y-m-d', strtotime($this->input->post('tgl_selesai')));
      $lokasi = $this->input->post('lokasi');

       $excel->setActiveSheetIndex(0)->setCellValue('A2', "PERIODE ".date('d / m / Y', strtotime($tgl_mulai)).' - '.date('d / m / Y', strtotime($tgl_selesai))); // Set kolom A1
      $excel->getActiveSheet()->mergeCells('A2:F2'); // Set Merge Cell pada kolom A1 sampai E1
      $excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(TRUE); // Set bold kolom A1
      $excel->getActiveSheet()->getStyle('A2')->getFont()->setSize(15); // Set font size 15 untuk kolom A1
      $excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); // Set text center 

      // Buat header tabel nya pada baris ke 3
      $excel->setActiveSheetIndex(0)->setCellValue('A6', "NO");
      $excel->getActiveSheet()->mergeCells('A6:A7');
      $excel->setActiveSheetIndex(0)->setCellValue('B6', "Tanggal");
      $excel->getActiveSheet()->mergeCells('B6:B7');
      $excel->setActiveSheetIndex(0)->setCellValue('C6', "Jenis Gilir");
      $excel->getActiveSheet()->mergeCells('C6:C7');
      $excel->setActiveSheetIndex(0)->setCellValue('D6', "ORDER");
      $excel->getActiveSheet()->mergeCells('D6:E6');
      $excel->setActiveSheetIndex(0)->setCellValue('D7', "Jml");
      $excel->setActiveSheetIndex(0)->setCellValue('E7', "Harga (Rp)");
    //  $excel->setActiveSheetIndex(0)->setCellValue('F6', "Total (Rp)");
      //$excel->getActiveSheet()->mergeCells('F6:F7');
	  //$excel->setActiveSheetIndex(0)->setCellValue('K6', "keterangan");
     // $excel->getActiveSheet()->mergeCells('K6:K7');
	 // $excel->setActiveSheetIndex(0)->setCellValue('G6', "Keterangan");
      //$excel->getActiveSheet()->mergeCells('G6:G7');

      
      // Apply style header yang telah kita buat tadi ke masing-masing kolom header
      $excel->getActiveSheet()->getStyle('A6:A7')->applyFromArray($style_col);
      $excel->getActiveSheet()->getStyle('B6:B7')->applyFromArray($style_col);
      $excel->getActiveSheet()->getStyle('C6:C7')->applyFromArray($style_col);
      $excel->getActiveSheet()->getStyle('D6:E6')->applyFromArray($style_col);
      //$excel->getActiveSheet()->getStyle('F6:F7')->applyFromArray($style_col);
      $excel->getActiveSheet()->getStyle('D7')->applyFromArray($style_col);
      $excel->getActiveSheet()->getStyle('E7')->applyFromArray($style_col);
	  $excel->getActiveSheet()->getStyle('K6:K7')->applyFromArray($style_col);
	//  $excel->getActiveSheet()->getStyle('G6:G7')->applyFromArray($style_col);
      
      
      $no = 1; // Untuk penomoran tabel, di awal set dengan 1
      $numrow = 8; // Set baris pertama untuk isi tabel adalah baris ke 4

      //get column tanggal
      $tgl = $this->db->select('tanggal_pemesanan')
              ->from('ess_pemesanan_makan_lembur')
              ->group_start()
              ->where(['tanggal_pemesanan >=' => $tgl_mulai, 'tanggal_pemesanan <=' => $tgl_selesai])
              ->group_end()
              ->where(['lokasi_lembur' => $lokasi, 'verified' => 3])
              ->group_by('tanggal_pemesanan')
              ->get()->result();

      foreach ($tgl as $value) {
        $numrow_old = $numrow;

        $gilir = $this->db->select('jenis_lembur')
              ->from('ess_pemesanan_makan_lembur')
			  ->where('verified', '3')
              ->where('tanggal_pemesanan', $value->tanggal_pemesanan)
              ->where('lokasi_lembur', $lokasi)
              ->group_by('jenis_lembur')
              ->get()->result();

          $all_makanan = [];
          $lok = $lokasi == '1' ? 'Jakarta' : 'Karawang';
          foreach ($gilir as $val) {
            $excel->setActiveSheetIndex(0)->setCellValue('C'.$numrow, 'Makan Lembur '.$lok.' '.$val->jenis_lembur);

			//lama
            $makan = $this->db->select('jenis_pesanan')
                ->from('ess_pemesanan_makan_lembur')
				->where('verified', '3')
                ->where('tanggal_pemesanan', $value->tanggal_pemesanan)
                ->where('lokasi_lembur', $lokasi)
                ->where('jenis_lembur', $val->jenis_lembur)
                ->get()->result();
				
            $total=0;
            foreach ($makan as $in) {
              $mkn = json_decode($in->jenis_pesanan, TRUE);
			
              //  $mkn = array_column($mkn, 'nama_makanan', 'id_makanan','harga','jumlah');
                
                foreach ($mkn as $key => $val2) {
                    if(!in_array($val2, $all_makanan)){
                        array_push($all_makanan, $val2);
                    }
					$total = $total + $val2['harga']*$val2['jumlah'];
					//tri wibowo tambahan
					
					
                }
				
				
				
            }
			
			//baru
			$tanggal_pemesanan = $value->tanggal_pemesanan;
			$jenis_lembur = $val->jenis_lembur;
			
			$ambil_jumlah_pemesanan = $this->db->query("SELECT sum(jumlah_pemesanan) as jumlah_pemesanan FROM ess_pemesanan_makan_lembur WHERE verified='3' AND tanggal_pemesanan='$tanggal_pemesanan' AND lokasi_lembur='$lokasi' AND jenis_lembur='$jenis_lembur'")->row_array();
			$jumlah_pemesanan = $ambil_jumlah_pemesanan['jumlah_pemesanan'];

            $harga_makanan = json_decode($makan[0]->jenis_pesanan, TRUE);
            $harga_makanan = $harga_makanan[0]['harga'];
            $excel->setActiveSheetIndex(0)->setCellValue('A'.$numrow, $no);
            $excel->setActiveSheetIndex(0)->setCellValue('D'.$numrow, $jumlah_pemesanan);
            $excel->setActiveSheetIndex(0)->setCellValue('E'.$numrow, $total);
           // $excel->setActiveSheetIndex(0)->setCellValue('F'.$numrow, (sizeof($all_makanan) * $harga_makanan));
	

            $all_makanan = [];
            $no++;
            $numrow++;
          }
          $excel->setActiveSheetIndex(0)->setCellValue('B'.($numrow_old), date('d/m/Y', strtotime($value->tanggal_pemesanan)));
          $excel->getActiveSheet()->mergeCells('B'.$numrow_old.':B'.($numrow-1));
      }

      // Set width kolom
      $excel->getActiveSheet()->getColumnDimension('A')->setWidth(5); // Set width kolom A
      $excel->getActiveSheet()->getColumnDimension('B')->setWidth(15); // Set width kolom B
      $excel->getActiveSheet()->getColumnDimension('C')->setWidth(25); // Set width kolom C
      $excel->getActiveSheet()->getColumnDimension('D')->setWidth(20); // Set width kolom D
      $excel->getActiveSheet()->getColumnDimension('E')->setWidth(30); // Set width kolom E
      
      // Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
      $excel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(-1);
      // Set orientasi kertas jadi LANDSCAPE
      $excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
      // Set judul file excel nya
      $excel->getActiveSheet(0)->setTitle("Rekap Lembur");
      $excel->setActiveSheetIndex(0);
      // Proses file excel
      header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
      header('Content-Disposition: attachment; filename="Monitoring Rekap Lembur.xlsx"'); // Set nama file excel nya
      header('Cache-Control: max-age=0');
      $write = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
      $write->save('php://output');
    }

    public function cekData()
    {
      //filter
      $tgl_mulai = date('Y-m-d', strtotime($this->input->post('tgl_mulai')));
      $tgl_selesai = date('Y-m-d', strtotime($this->input->post('tgl_selesai')));
      $lokasi = $this->input->post('lokasi');

      //get column makanan
      $data = $this->db->select('id')
                ->from('ess_pemesanan_makan_lembur')
                ->group_start()
                ->where(['tanggal_pemesanan >=' => $tgl_mulai, 'tanggal_pemesanan <=' => $tgl_selesai])
                ->group_end()
                ->where(['lokasi_lembur' => $lokasi, 'verified' => 3])
                ->get()->result();
      if(!empty($data)){
        $msg = 'cetak';
      }else{
        $msg = 'kosong';
      }

      header('Content-Type: application/json');
      header("Access-Control-Allow-Origin: *");
      echo json_encode(['status' => 200, 'response' => $msg]);
    }
  }
?>