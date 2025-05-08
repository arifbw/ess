<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Laporan_bantuan_cubes extends CI_Controller {
		public function __construct(){
			parent::__construct();
			
			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'osdm/';
			$this->folder_model = 'osdm/';
			$this->folder_controller = 'osdm/';
			
			$this->akses = array();
			
			$this->load->helper("cutoff_helper");
			$this->load->helper("tanggal_helper");
			$this->load->helper("karyawan_helper");
			$this->load->helper("reference_helper");
        	
        	$this->load->library('phpexcel'); 
						
			$this->load->model($this->folder_model."M_laporan_bantuan_cubes");
			$this->load->model($this->folder_model."M_tabel_laporan_bantuan_cubes");
			
			$this->data["is_with_sidebar"] = true;
			
			$this->data['judul'] = "Laporan Bantuan Cuti Besar";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);	
			izin($this->akses["akses"]);
		}
		
		public function index() {	
			//echo __FILE__ . __LINE__;die(var_dump($this->akses));	
			//$this->session->unset_userdata('tampil_tahun_bulan');
			$this->data["akses"] 					= $this->akses;
			$this->data["navigasi_menu"] 			= menu_helper();
			$this->data['content'] 					= $this->folder_view."laporan_bantuan_cubes";
			
			//$this->data["bulan"] = date('Y-m');
			$this->data["bulan"] = date('Y-m');
			$this->data["month_list"] = $this->M_laporan_bantuan_cubes->get_month_list();
			$this->data["approve"] = '';
			$this->load->view('template',$this->data);
		}	
		
		public function tabel_laporan_bantuan_cubes() {		
			//$approve = $this->input->post('status');
			$tahun_bulan = $this->input->post('tahun_bulan');
			$list 	= $this->M_tabel_laporan_bantuan_cubes->get_datatables($tahun_bulan);	
			$data = array();
			$no = $_POST['start'];
			
			foreach ($list as $tampil) {
				$no++;
				$row = array();
				$row[] = $no;
				$row[] = $tampil->np_karyawan;
				$row[] = $tampil->nama;
				$row[] = $tampil->tahun;
				$row[] = tanggal_indonesia($tampil->start_date);	
				$row[] = tanggal_indonesia($tampil->end_date);
				$pisah = explode(' ',$tampil->approval_sdm_date);
				$approval_sdm_date = $pisah[0];
				$row[] = tanggal_indonesia($approval_sdm_date);
				$row[] = tanggal_indonesia($tampil->bantuan_cuti_besar_tanggal);
				$data[] = $row;
			}

			$output = array(
					"draw" => $_POST['draw'],
					"recordsTotal" => $this->M_tabel_laporan_bantuan_cubes->count_all($tahun_bulan),
					"recordsFiltered" => $this->M_tabel_laporan_bantuan_cubes->count_filtered($tahun_bulan),
					"data" => $data,
			);
			//output to json format
			echo json_encode($output);
		}

		public function cetak($bulan){
	        $data = $this->M_laporan_bantuan_cubes->getDataCetak($bulan);
	        error_reporting(E_ALL);
	        ini_set('display_errors', TRUE);
	        ini_set('display_startup_errors', TRUE);

	        header("Content-type: application/vnd.ms-excel");
	        //nama file
	        header("Content-Disposition: attachment; filename=Laporan_bantuan_cuti_besar.xlsx");
	        header('Cache-Control: max-age=0');

	        $excel = PHPExcel_IOFactory::createReader('Excel2007');
	        $excel = $excel->load('./asset/Template_laporan_bantuan_cubes.xlsx');

	        $border = array (
	        'borders' => array (
	            'allborders' => array (
	                'style' => PHPExcel_Style_Border::BORDER_THIN,
	                ),
	            ),
	        );
	        $valign = array (
	            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
	            'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
	            'rotation'   => 0,
	            'wrap'     => true
	        );
	        $valign2 = array (
	            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
	            'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
	            'rotation'   => 0,
	            'wrap'     => true
	        );

	        //anggota
	        $excel->setActiveSheetIndex(0);
	        $awal 	= 4;
	        $no 	= 1;

	        foreach ($data as $row) {
	            $excel->getActiveSheet()->setCellValueExplicit('A'.$awal, $no++, PHPExcel_Cell_DataType::TYPE_STRING);
	            $excel->getActiveSheet()->setCellValueExplicit('B'.$awal, strtoupper($row['np_karyawan']), PHPExcel_Cell_DataType::TYPE_STRING);
	            $excel->getActiveSheet()->setCellValueExplicit('C'.$awal, strtoupper($row['nama']), PHPExcel_Cell_DataType::TYPE_STRING);
	            $excel->getActiveSheet()->setCellValueExplicit('D'.$awal, $row['tahun'], PHPExcel_Cell_DataType::TYPE_STRING);
	            $excel->getActiveSheet()->setCellValueExplicit('E'.$awal, tanggal_indonesia($row['start_date']), PHPExcel_Cell_DataType::TYPE_STRING);
	            $excel->getActiveSheet()->setCellValueExplicit('F'.$awal, tanggal_indonesia($row['end_date']), PHPExcel_Cell_DataType::TYPE_STRING);
	            $excel->getActiveSheet()->setCellValueExplicit('G'.$awal, tanggal_indonesia($row['bantuan_cuti_besar_tanggal']), PHPExcel_Cell_DataType::TYPE_STRING);
	            //$excel->getActiveSheet()->setCellValueExplicit('I'.$awal, strtoupper($status), PHPExcel_Cell_DataType::TYPE_STRING);
	            $awal += 1;
	        }

	        $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
	        $objWriter->setIncludeCharts(TRUE);
	        $objWriter->setPreCalculateFormulas(TRUE);
	        PHPExcel_Calculation::getInstance($excel)->clearCalculationCache();
	        $objWriter->save('php://output');
	        exit();
	    }
	}
	
	/* End of file persetujuan_lembur_manual.php */
	/* Location: ./application/controllers/lembur/persetujuan_lembur_manual.php */