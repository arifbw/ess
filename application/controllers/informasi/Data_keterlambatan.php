<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Data_keterlambatan extends CI_Controller {
		public function __construct(){
			parent::__construct();
			
			// Report all errors
            error_reporting(E_ALL);

            // Display errors in output
            ini_set('display_errors', 1);
			
			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'informasi/';
			$this->folder_model = 'informasi/';
			$this->folder_controller = 'informasi/';
			
			$this->akses = array();
						
			$this->load->model($this->folder_model."new/m_data_keterlambatan");
		
			$this->data["is_with_sidebar"] = true;
								
			$this->data['judul'] = "Data Keterlambatan";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			$this->load->model("master_data/m_karyawan");
			$this->load->model("master_data/m_satuan_kerja");
			izin($this->akses["akses"]);
		}
		
		public function index(){
			//	echo __FILE__ . __LINE__;die(var_dump($this->akses));	
			
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."data_keterlambatan_new";
			
			if($this->akses["lihat"]){
				array_push($this->data['css_plugin_sources'],"select2/select2.min.css");
				array_push($this->data['js_plugin_sources'],"select2/select2.min.js");

				
				$this->data["daftar_akses_karyawan"] = array(array("no_pokok"=>"","nama"=>""));
				
				$pilihan_karyawan = "";
				if($this->akses["pilih seluruh karyawan"]){
					$pilihan_karyawan = "pilihan_karyawan();";
					$this->data["daftar_akses_unit_kerja"] = $this->m_satuan_kerja->daftar_satuan_kerja();
				}
				else if($this->akses["pilih karyawan diadministrasikan"]){
					$pilihan_karyawan = "pilihan_karyawan();";
					$this->data["daftar_akses_unit_kerja"] = $this->m_satuan_kerja->daftar_satuan_kerja_diadministrasikan();
				}
				else{
					$this->data["daftar_akses_karyawan"] = array(array("no_pokok"=>$this->session->userdata("no_pokok"),"nama"=>$this->session->userdata("nama")));
					$this->data["daftar_akses_unit_kerja"] = array(array("kode_unit"=>$this->session->userdata("kode_unit"), "nama_unit"=>$this->session->userdata("nama_unit")));
				}
			
				$this->data["arr_periode"]=periode();
				
				$js_header_script = "<script>
								$(document).ready(function() {
									$pilihan_karyawan
									$('.select2').select2();
								});
							</script>";
				
				array_push($this->data["js_header_script"],$js_header_script);
			}
			
			$this->load->view('template',$this->data);
		}
		
		public function tabel_data_keterlambatan(){
			$kode_unit = $this->input->post("kode_unit");
			$np_karyawan = $this->input->post("np_karyawan");
			// $periode_awal = '2020-01-01';
			// $periode_akhir = '2020-09-30';
			$periode_awal = $this->input->post("periode_awal");
			$periode_akhir = $this->input->post("periode_akhir");

			if ($periode_awal=="" && $periode_akhir=="") {
				$periode = date('Y_m');
				// $periode = '2020_09';
				$list[0] = $this->m_data_keterlambatan->get_datatable_keterlambatan($kode_unit,$np_karyawan,$periode);
				$recordsFilteredList[0] = $this->m_data_keterlambatan->count_filtered($kode_unit,$np_karyawan,$periode);
				$recordsTotalList[0] = $this->m_data_keterlambatan->count_all($periode);

			} else if (date('Y', strtotime($periode_awal))==date('Y', strtotime($periode_akhir))) {
				$tahun_awal = date('Y', strtotime($periode_awal));
				$bulan_awal = date('n', strtotime($periode_awal));
				$tahun_akhir = date('Y', strtotime($periode_akhir));
				$bulan_akhir = date('n', strtotime($periode_akhir));
				$bulan = array('00', '01','02','03','04','05','06','07','08','09','10','11','12');

				$no=0;
				for($i=($bulan_awal); $i<=$bulan_akhir; $i++) {
					$periode = $tahun_awal."_".$bulan[$i];
					
					$list[$no] = $this->m_data_keterlambatan->get_datatable_keterlambatan($kode_unit,$np_karyawan,$periode);
					$recordsFilteredList[$no] = $this->m_data_keterlambatan->count_filtered($kode_unit,$np_karyawan,$periode);
					$no++;
				}
			}

			$data = array();
			$recordsFiltered = 0;
			$recordsTotal = 0;
			$no = $_POST['start'];
			for($b=0; $b<count($list); $b++) {
				foreach ($list[$b] as $tampil) {
					$no++;
					$row = array();
					$row[] = $no;
					$row[] = $tampil->np_karyawan;
					$row[] = $tampil->nama;
					$row[] = tanggal($tampil->tanggal);
					$row[] = $tampil->jadwal;
					$row[] = tanggal_waktu($tampil->jadwal_masuk);
					$row[] = tanggal_waktu($tampil->datang);
					$arr_keterangan = explode("|",$tampil->keterangan);
					$keterangan = "";
					for($i=0;$i<count($arr_keterangan);$i++){
						if(!empty($keterangan[$i])){
							$keterangan.="<br><br>";
						}
						$keterangan.=$arr_keterangan[$i];
					}
					$row[] = $keterangan;
					
					$data[] = $row;
				}

				$recordsFiltered += $recordsFilteredList[$b];
				$recordsTotal += count($list[$b]);
			}

			$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $recordsTotal,
						"recordsFiltered" => $recordsFiltered,
						"data" => $data
					);
			//output to json format
			echo json_encode($output);
			
			$log = array(
						"id_pengguna" => $this->session->userdata("id_pengguna"),
						"id_modul" => $this->data['id_modul'],
						"deskripsi" => "lihat data keterlambatan <br>Kode unit kerja : ".$kode_unit."<br>NP Karyawan : ".$np_karyawan."<br>Periode : ".$periode_awal." - ".$periode_akhir,
						"alamat_ip" => $this->data["ip_address"],
						"waktu" => date("Y-m-d H:i:s")
					);
				$this->m_log->tambah($log);
		}

		public function rekap(){
			//	echo __FILE__ . __LINE__;die(var_dump($this->akses));	
			
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."data_rekap_keterlambatan_new";
			
			if($this->akses["lihat"]){
				array_push($this->data['css_plugin_sources'],"select2/select2.min.css");
				array_push($this->data['js_plugin_sources'],"select2/select2.min.js");

				
				$this->data["daftar_akses_karyawan"] = array(array("no_pokok"=>"","nama"=>""));
				
				$pilihan_karyawan = "";
				if($this->akses["pilih seluruh karyawan"]){
					$pilihan_karyawan = "pilihan_karyawan();";
					$this->data["daftar_akses_unit_kerja"] = $this->m_satuan_kerja->daftar_satuan_kerja();
				}
				else if($this->akses["pilih karyawan diadministrasikan"]){
					$pilihan_karyawan = "pilihan_karyawan();";
					$this->data["daftar_akses_unit_kerja"] = $this->m_satuan_kerja->daftar_satuan_kerja_diadministrasikan();
				}
				else{
					$this->data["daftar_akses_karyawan"] = array(array("no_pokok"=>$this->session->userdata("no_pokok"),"nama"=>$this->session->userdata("nama")));
					$this->data["daftar_akses_unit_kerja"] = array(array("kode_unit"=>$this->session->userdata("kode_unit"), "nama_unit"=>$this->session->userdata("nama_unit")));
				}
			
				$this->data["arr_periode"]=periode();
				
				$js_header_script = "<script>
								$(document).ready(function() {
									$pilihan_karyawan
									$('.select2').select2();
								});
							</script>";
				
				array_push($this->data["js_header_script"],$js_header_script);
			}
			
			$this->load->view('template',$this->data);
		}
		
		public function tabel_data_rekap_keterlambatan() {
			$kode_unit = $this->input->post("kode_unit");
			$np_karyawan = $this->input->post("np_karyawan");
			$tahun_periode = $this->input->post("periode");

			$bulan = array('01','02','03','04','05','06','07','08','09','10','11','12');
			
			if($tahun_periode==date('Y'))
				$hitung = date('n');
			else
				$hitung = count($bulan);

			for($i=0; $i<$hitung; $i++) {
				$periode = $tahun_periode.'_'.$bulan[$i];
				$list[$i] = $this->m_data_keterlambatan->get_rekap_keterlambatan($kode_unit,$np_karyawan,$periode);
			}

			$np_list = array();
			$return = array();
			for($i=0; $i<$hitung; $i++) {
		        foreach ($list[$i] as $row => $field) {
					if (!in_array($list[$i][$row]['np_karyawan'], $np_list)) {
						$return[] = $field;
						array_push($np_list, $list[$i][$row]['np_karyawan']);
					} else {
						foreach ($field as $column => $value) {
			                if($column=='jml') {
			                	$key = array_search($list[$i][$row]['np_karyawan'], $np_list);
			                	// echo $list[$i][$row]['np_karyawan'].'<br>'.$key.'<br>';
			                	// var_dump($return);exit;
			                	$jml = $list[$i][$row]['jml'];
			                	$return[$key][$column] = $jml + $return[$key][$column];
			                }
			            }
			        }
		        }
			}

			$data = array();
			$recordsFiltered = 0;
			$recordsTotal = 0;
			$no = $_POST['start'];
			for ($h=0; $h<count($return); $h++) {
				$no++;
				$row = array();
				$row[] = $no;
				$row[] = $return[$h]['np_karyawan'];
				$row[] = $return[$h]['nama'];
				$row[] = $return[$h]['nama_unit'];
				$row[] = $return[$h]['jml'];
				
				$data[] = $row;
			}

			$recordsFiltered = 0;
			$recordsTotal = 0;

			$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $recordsTotal,
						"recordsFiltered" => $recordsFiltered,
						"data" => $data
					);
			//output to json format
			echo json_encode($output);
			
			$log = array(
					"id_pengguna" => $this->session->userdata("id_pengguna"),
					"id_modul" => $this->data['id_modul'],
					"deskripsi" => "lihat data keterlambatan <br>Kode unit kerja : ".$kode_unit."<br>NP Karyawan : ".$np_karyawan."<br>Tahun : ".$periode,
					"alamat_ip" => $this->data["ip_address"],
					"waktu" => date("Y-m-d H:i:s")
				);
			$this->m_log->tambah($log);
		}
		
		public function daftar_karyawan(){
			$kode_unit = $_POST["unit_kerja"];
			$hasil["np_pengguna"] = $this->session->userdata("no_pokok");
			$hasil["karyawan"] = $this->m_karyawan->get_karyawan_unit_kerja($kode_unit);
			echo json_encode($hasil);
		}

		public function export() {
			$this->load->library('phpexcel'); 
			
			$kode_unit = $this->input->post("kode_unit");
			$np_karyawan = $this->input->post("np_karyawan");
			$periode_awal = $this->input->post("periode_awal");
			$periode_akhir = $this->input->post("periode_akhir");

			if ($periode_awal=="" && $periode_akhir=="") {
				$periode = date('Y_m');
				$list[0] = $this->m_data_keterlambatan->get_datatable_keterlambatan($kode_unit,$np_karyawan,$periode,'export');

			} else if (date('Y', strtotime($periode_awal))==date('Y', strtotime($periode_akhir))) {
				$tahun_awal = date('Y', strtotime($periode_awal));
				$bulan_awal = date('n', strtotime($periode_awal));
				$tahun_akhir = date('Y', strtotime($periode_akhir));
				$bulan_akhir = date('n', strtotime($periode_akhir));
				$bulan = array('00', '01','02','03','04','05','06','07','08','09','10','11','12');

				$no=0;
				for($i=($bulan_awal); $i<=$bulan_akhir; $i++) {
					$periode = $tahun_awal."_".$bulan[$i];
					$list[$no] = $this->m_data_keterlambatan->get_datatable_keterlambatan($kode_unit,$np_karyawan,$periode,'export');
					$no++;
				}
			}

	        error_reporting(E_ALL);
	        ini_set('display_errors', TRUE);
	        ini_set('display_startup_errors', TRUE);

	        header("Content-type: application/vnd.ms-excel");
	        //nama file
	        header("Content-Disposition: attachment; filename=Data_keterlambatan.xlsx");
	        header('Cache-Control: max-age=0');

	        $excel = PHPExcel_IOFactory::createReader('Excel2007');
	        $excel = $excel->load('./asset/Template_data_keterlambatan.xlsx');

	        //anggota
	        $excel->setActiveSheetIndex(0);
			$excel->getActiveSheet()->setCellValueExplicit('A2', 'Periode '.tanggal($periode_awal).' s/d '.tanggal($periode_akhir), PHPExcel_Cell_DataType::TYPE_STRING);

	        $kolom 	= 2;
	        $awal 	= 5;
	        $no = 1;
	        
			for($b=0; $b<count($list); $b++) {
				foreach ($list[$b] as $tampil) {
					$excel->getActiveSheet()->setCellValueExplicit('A'.$awal, $no++, PHPExcel_Cell_DataType::TYPE_STRING);
		            $excel->getActiveSheet()->setCellValueExplicit('B'.$awal, strtoupper($tampil->np_karyawan), PHPExcel_Cell_DataType::TYPE_STRING);
		            $excel->getActiveSheet()->setCellValueExplicit('C'.$awal, ucwords($tampil->nama), PHPExcel_Cell_DataType::TYPE_STRING);
		            $excel->getActiveSheet()->setCellValueExplicit('D'.$awal, ucwords($tampil->nama_unit), PHPExcel_Cell_DataType::TYPE_STRING);
		            $excel->getActiveSheet()->setCellValueExplicit('E'.$awal, $tampil->tanggal, PHPExcel_Cell_DataType::TYPE_STRING);
		            $excel->getActiveSheet()->setCellValueExplicit('F'.$awal, $tampil->jadwal, PHPExcel_Cell_DataType::TYPE_STRING);
		            $excel->getActiveSheet()->setCellValueExplicit('G'.$awal, $tampil->jadwal_masuk, PHPExcel_Cell_DataType::TYPE_STRING);
		            $excel->getActiveSheet()->setCellValueExplicit('H'.$awal, $tampil->datang, PHPExcel_Cell_DataType::TYPE_STRING);
					$arr_keterangan = explode("|",$tampil->keterangan);
					$keterangan = "";
					for($i=0;$i<count($arr_keterangan);$i++){
						if(!empty($keterangan[$i])){
							$keterangan.="<br><br>";
						}
						$keterangan.=$arr_keterangan[$i];
					}
		            $excel->getActiveSheet()->setCellValueExplicit('I'.$awal, strip_tags($keterangan), PHPExcel_Cell_DataType::TYPE_STRING);
		            $awal += 1;
				}
			}
			
	        $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
	        $objWriter->setIncludeCharts(TRUE);
	        $objWriter->setPreCalculateFormulas(TRUE);
	        PHPExcel_Calculation::getInstance($excel)->clearCalculationCache();
	        $objWriter->save('php://output');
	        exit();
		}
	}
	
	/* End of file data_keterlambatan.php */
	/* Location: ./application/controllers/informasi/data_pamlek.php */