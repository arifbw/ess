<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Kebutuhan_pelatihan extends CI_Controller {
		public function __construct(){
			parent::__construct();
			
			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'pelatihan/';
			$this->folder_model = 'pelatihan/';
			$this->folder_controller = 'pelatihan/';
			
			$this->akses = array();
			
			$this->load->helper("cutoff_helper");
			$this->load->helper("tanggal_helper");
			$this->load->helper("karyawan_helper");
			$this->load->helper("reference_helper");
						
			$this->load->model($this->folder_model."m_kebutuhan_pelatihan");
			
			$this->data["is_with_sidebar"] = true;
			
			$this->data['judul'] = "Rekap Kebutuhan Pelatihan";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);				
			//izin($this->akses["akses"]);
		}
		
		public function index()
		{					
			//echo __FILE__ . __LINE__;die(var_dump($this->akses));			
			$this->data["akses"] 					= $this->akses;
			$this->data["navigasi_menu"] 			= menu_helper();
			$this->data['content'] 					= $this->folder_view."kebutuhan_pelatihan";

			array_push($this->data['css_plugin_sources'],"select2/select2.min.css");
			array_push($this->data['js_plugin_sources'],"select2/select2.min.js");
			$script = "pilihan_karyawan();";
			$this->data['np_karyawan'] = $this->db->get('mst_karyawan')->result();
			$this->data['kategori_pelatihan'] = $this->db->get('mst_diklat_kategori_pelatihan')->result();
			$this->data['pelatihan'] = $this->db->get('mst_diklat_pelatihan')->result();

			$this->load->view('template',$this->data);
		}	
		
		public function tabel_ess_pelatihan($bulan_tahun=null,$filter='all',$np_karyawan=null,$id_kategori_pelatihan=null,$id_pelatihan=null)
		{	
			$np_karyawan = $this->input->post("np_karyawan");
			$id_kategori_pelatihan = $this->input->post("id_kategori_pelatihan");
			$id_pelatihan = $this->input->post("id_pelatihan");

			$this->load->model($this->folder_model."M_tabel_kebutuhan_pelatihan");
			
			$list 	= $this->M_tabel_kebutuhan_pelatihan->get_datatables($month,$filter,$np_karyawan,$id_kategori_pelatihan,$id_pelatihan);	
						
			$data = array();
			$no = $_POST['start'];
			
			foreach ($list as $tampil) {
				$no++;
				$row = array();
				$row[] = $no;
				$row[] = $tampil->np_karyawan;
				$row[] = $tampil->nama;
				$row[] = $tampil->nama_unit;
				$row[] = $tampil->nama_kategori_pelatihan;
				$row[] = $tampil->kode_pelatihan;
				$row[] = $tampil->pelatihan;
				$row[] = $tampil->skala_prioritas;
				$row[] = $tampil->vendor;

				
			
				
				
				// //cutoff ERP
				// $sudah_cutoff = sudah_cutoff($tampil->start_date);
				
				// if($sudah_cutoff) //jika sudah lewat masa cutoff
				// {
				// 	$row[] = "<button class='btn btn-primary btn-xs persetujuan_button'   data-toggle='tooltip' title='Tidak bisa melakukan aksi setelah Cutoff data di ERP pada $sudah_cutoff' disabled>Submit ERP</button>";
				// }else
				// {			
				// 	if(!$approval_1)
				// 	{
				// 		$approval_1="xxxx";
				// 	}
					
				// 	if(!$approval_2)
				// 	{
				// 		$approval_2="xxxx";
				// 	}
					
					
				// 	$row[] = "<button class='btn btn-primary btn-xs persetujuan_button' data-toggle='modal'  data-target='#modal_persetujuan'
				// 	data-id='$id'
				// 	data-np-karyawan='$np_karyawan'
				// 	data-nama='$nama'
				// 	data-created-at='$created_at'
				// 	data-created-by='$created_by'
				// 	data-approval-1-nama='$approval_1_nama'
				// 	data-approval-2-nama='$approval_2_nama'
				// 	data-approval-1-status='$approval_1_status'
				// 	data-approval-2-status='$approval_2_status'	
				// 	data-approval-1='$approval_1'
				// 	data-approval-2='$approval_2'
				// 	data-status-1='$status_1'
				// 	data-status-2='$status_2'	
				// 	$btn_disabled>Persetujuan</button>";					
				// }
				
				$data[] = $row;
				
				
			}

			$output = array(
							"draw" => $_POST['draw'],
							"recordsTotal" => $this->M_tabel_kebutuhan_pelatihan->count_all($month,$filter,$np_karyawan,$id_kategori_pelatihan,$id_pelatihan),
							"recordsFiltered" => $this->M_tabel_kebutuhan_pelatihan->count_filtered($month,$filter,$np_karyawan,$id_kategori_pelatihan,$id_pelatihan),
							"data" => $data,
					);
			//output to json format
			echo json_encode($output);
		}

		public function daftar_pelatihan(){
			$id_kategori_pelatihan	= $this->input->post('id_kategori_pelatihan');	
			$data = $this->m_kebutuhan_pelatihan->get_pelatihan($id_kategori_pelatihan);	

			if ($data) {
				$return = [
					'status'=>true,
					'data'=> $data
				];
			}
			else{				
				$return = [
                    'status'=>false,
                    'data'=> $data
                ];
			}	
            echo json_encode($return);
		}
		
		function generateExcel()
		{
			$np_karyawan = @$this->input->get('np_karyawan', true) ? $this->input->get('np_karyawan', true) : '';
			$id_kategori_pelatihan = @$this->input->get('id_kategori_pelatihan', true) ? $this->input->get('id_kategori_pelatihan', true) : '';
			$id_pelatihan = @$this->input->get('id_pelatihan', true) ? $this->input->get('id_pelatihan', true) : '';

			$filename = date("d-m-Y");
	
			$this->load->library('phpexcel');
			error_reporting(E_ALL);
			ini_set('display_errors', TRUE);
			ini_set('display_startup_errors', TRUE);
	
			header("Content-type: application/vnd.ms-excel");
			header("Content-Disposition: attachment; filename=Rekap-Kebutuhan-Pelatihan-$filename.xlsx");
			header('Cache-Control: max-age=0');
	
			$excel = PHPExcel_IOFactory::createReader('Excel2007');
			$excel = $excel->load('./asset/excel-templates-pelatihan/template-export-kebutuhan-pelatihan.xlsx');
	
			# proses isi data
			$this->db->select('ess_diklat_kebutuhan_pelatihan.id');
			$this->db->select('ess_diklat_kebutuhan_pelatihan.np_karyawan');
			$this->db->select('ess_diklat_kebutuhan_pelatihan.nama');
			$this->db->select('ess_diklat_kebutuhan_pelatihan.nama_jabatan');
			$this->db->select('ess_diklat_kebutuhan_pelatihan.nama_unit');
			$this->db->select('ess_diklat_kebutuhan_pelatihan.id_kategori_pelatihan');
			$this->db->select('ess_diklat_kebutuhan_pelatihan.nama_kategori_pelatihan');
			$this->db->select('ess_diklat_kebutuhan_pelatihan.id_pelatihan');
			$this->db->select('ess_diklat_kebutuhan_pelatihan.kode_pelatihan');
			$this->db->select('ess_diklat_kebutuhan_pelatihan.pelatihan');
			$this->db->select('ess_diklat_kebutuhan_pelatihan.skala_prioritas');
			$this->db->select('ess_diklat_kebutuhan_pelatihan.vendor');
			$this->db->from('ess_diklat_kebutuhan_pelatihan');
			$this->db->where("ess_diklat_kebutuhan_pelatihan.status_1='1' AND ess_diklat_kebutuhan_pelatihan.status_2='1'");
			$this->db->order_by('id_kategori_pelatihan ASC', 'id_pelatihan ASC', 'created_at DESC');
	
			// if ($_SESSION["grup"] == 4) {
			// 	$var = array();
			// 	$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
			// 	foreach ($list_pengadministrasi as $v) {
			// 		array_push($var, $v['kode_unit']);
			// 	}
			// 	if ($var != []) {
			// 		$this->db->where_in('kode_unit', $var);
			// 	}
			// } else if ($_SESSION["grup"] == 5) {
			// 	$var = $_SESSION["no_pokok"];
			// 	$this->db->where('np_karyawan', $var);
			// }
	
			// if (!empty($tipe_perjalanan)) {
			// 	$this->db->where('tipe_perjalanan', $tipe_perjalanan);
			// }
	
			if (!empty($np_karyawan)) {
				$this->db->where('np_karyawan', $np_karyawan);
			}

			if (!empty($id_kategori_pelatihan)) {
				$this->db->where('id_kategori_pelatihan', $id_kategori_pelatihan);
			}

			if (!empty($id_pelatihan)) {
				$this->db->where('id_pelatihan', $id_pelatihan);
			}
	
			$data = $this->db->get()->result();
	
			$excel->setActiveSheetIndex(0);
			$kolom 	= 1;
			$awal 	= 6;
			$no = 1;
	
			$excel->getActiveSheet()->setCellValueExplicit('A1', 'FORM. IDENTIFIKASI KEBUTUHAN PELATIHAN TAHUN ' . date("Y"), PHPExcel_Cell_DataType::TYPE_STRING);
			
			$temp_id_kategori = NULL;

			foreach ($data as $row) {
				// Jika kategori pelatihan berubah
				if ($row->id_kategori_pelatihan != $temp_id_kategori) {
						// Merge cell dari kolom A hingga J pada baris saat ini
						$excel->getActiveSheet()->mergeCells("A$awal:J$awal");
				
						// Set warna background menjadi biru muda
						$excel->getActiveSheet()->getStyle("A$awal:J$awal")->applyFromArray([
							'fill' => [
								'fillType' => PHPExcel_Style_Fill::FILL_SOLID,
								'startColor' => ['rgb' => 'FFFF00'] 
							],
							'font' => [
      				  	        'bold' => true // Teks tebal
        					],
							'alignment' => [
                				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT, // Teks rata tengah
                				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
           					]
						]);
				
						// Tulis nilai id_kategori_pelatihan di dalam merge cell
						$excel->getActiveSheet()->setCellValue("A$awal", $row->nama_kategori_pelatihan);
				
						// Update kategori sebelumnya
						$temp_id_kategori = $row->id_kategori_pelatihan;
				
						// Pindah ke baris berikutnya setelah merge
						$awal++;
				}

				$excel->getActiveSheet()->setCellValue('A' . $awal, $no);
				$excel->getActiveSheet()->setCellValueExplicit('B' . $awal, $row->nama_kategori_pelatihan, PHPExcel_Cell_DataType::TYPE_STRING);
				$excel->getActiveSheet()->setCellValueExplicit('C' . $awal, $row->kode_pelatihan, PHPExcel_Cell_DataType::TYPE_STRING);
				$excel->getActiveSheet()->setCellValueExplicit('D' . $awal, $row->pelatihan, PHPExcel_Cell_DataType::TYPE_STRING);
				$excel->getActiveSheet()->setCellValueExplicit('E' . $awal, $row->np_karyawan, PHPExcel_Cell_DataType::TYPE_STRING);
				$excel->getActiveSheet()->setCellValueExplicit('F' . $awal, $row->nama, PHPExcel_Cell_DataType::TYPE_STRING);
				$excel->getActiveSheet()->setCellValueExplicit('G' . $awal, $row->nama_jabatan, PHPExcel_Cell_DataType::TYPE_STRING);
				$excel->getActiveSheet()->setCellValueExplicit('H' . $awal, $row->nama_unit, PHPExcel_Cell_DataType::TYPE_STRING);
				$excel->getActiveSheet()->setCellValueExplicit('I' . $awal, $row->skala_prioritas, PHPExcel_Cell_DataType::TYPE_STRING);
				$excel->getActiveSheet()->setCellValueExplicit('J' . $awal, $row->vendor, PHPExcel_Cell_DataType::TYPE_STRING);
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