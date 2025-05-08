<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Rekap_data_keterlambatan extends CI_Controller {
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
						
			$this->load->model($this->folder_model."m_data_rekap_keterlambatan");
		
			$this->data["is_with_sidebar"] = true;
								
			$this->data['judul'] = "Rekap Keterlambatan";
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
			$this->data['content'] = $this->folder_view."data_rekap_keterlambatan";
			
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
				$tahun = array();
				$this->data["arr_tahun"] = array();
				for($i=2019; $i<=date('Y'); $i++) {
					$tahun[]=$i;
				}
				$this->data["arr_tahun"]=$tahun;
				
				$js_header_script = "
							<script src='".base_url('asset/sweetalert2')."/sweetalert2.js'></script>
							<script>
								$(document).ready(function() {
									$pilihan_karyawan
									$('.select2').select2();
								});
							</script>";
				
				array_push($this->data["js_header_script"],$js_header_script);
			}
			
			$this->load->view('template',$this->data);
		}
		
		public function tabel_data_rekap_keterlambatan(){
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
				$list[$i] = $this->m_data_rekap_keterlambatan->get_datatable_keterlambatan($kode_unit,$np_karyawan,$periode);
				$recordsFilteredList[$i] = $this->m_data_rekap_keterlambatan->count_filtered($kode_unit,$np_karyawan,$periode);
			}

			$np_list = array();
			$return = array();
			for($i=0; $i<$hitung; $i++) {
		        foreach ($list[$i] as $row => $field) {
					if (!in_array($list[$i][$row]['np_karyawan'], $np_list)) {
						$return[] = $field;
						array_push($np_list, $list[$i][$row]['np_karyawan']);
					} else {
			            $key = array_search($list[$i][$row]['np_karyawan'], $np_list);
						foreach ($field as $column => $value) {
			                if($column=='jml') {
			                	// echo $list[$i][$row]['np_karyawan'].'<br>'.$key.'<br>';
			                	// var_dump($return);exit;
			                	$jml = $list[$i][$row]['jml'];
			                	$return[$key][$column] = $return[$key][$column] + $jml;
			                }
			                if($column=='jml_reset') {
			                	$jml_reset = $list[$i][$row]['jml_reset'];
			                	$return[$key][$column] = $return[$key][$column] + $jml_reset;
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
				$reset = $this->db->where('np_karyawan', $return[$h]['np_karyawan'])->get('ess_penindakan')->num_rows();
				$sisa = $return[$h]['jml'] - ($reset*13);
				$row[] = (($reset > 0) ? $sisa : $return[$h]['jml']).' / '.$return[$h]['jml'].' Kali';
				if ((($reset > 0 && $sisa >= 13) || ($reset == 0 && $return[$h]['jml'] >= 13)) && $this->akses["restart"])
					$row[] = "<a class='btn btn-primary btn-xs' href='".base_url('informasi/data_keterlambatan?np='.$return[$h]['np_karyawan'].'&tahun='.$tahun_periode.'&kode_unit='.$return[$h]['kode_unit'])."'>Detail</a> <a class='btn btn-danger btn-xs restart' data-key='".$return[$h]['np_karyawan']."'>Restart</a>";
				else
					$row[] = "<a class='btn btn-primary btn-xs' href='".base_url('informasi/data_keterlambatan?np='.$return[$h]['np_karyawan'].'&tahun='.$tahun_periode.'&kode_unit='.$return[$h]['kode_unit'])."'>Detail</a>";
				
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

		public function rekap(){
			//	echo __FILE__ . __LINE__;die(var_dump($this->akses));	
			
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."data_rekap_keterlambatan";
			
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
			$tahun_periode = $this->input->post("periode");
			$periode_awal = $this->input->post("periode_awal");
			$periode_akhir = $this->input->post("periode_akhir");

			$bulan = array('01','02','03','04','05','06','07','08','09','10','11','12');
			
			if($tahun_periode==date('Y'))
				$hitung = date('n');
			else
				$hitung = count($bulan);

			for($i=0; $i<$hitung; $i++) {
				$periode = $tahun_periode.'_'.$bulan[$i];
				$list[$i] = $this->m_data_rekap_keterlambatan->export_data_keterlambatan($kode_unit,$np_karyawan,$periode);
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
			$excel->getActiveSheet()->setCellValueExplicit('A2', 'Periode '.substr(tanggal($periode_awal), 2).' s/d '.substr(tanggal($periode_akhir), 2), PHPExcel_Cell_DataType::TYPE_STRING);

	        $kolom 	= 2;
	        $awal 	= 5;
	        $no = 1;
	        $data = array();

			/*for ($h=0; $h<count($return); $h++) {
				$no++;
				$row = array();
				$row[] = $no;
				$row[] = $return[$h]['np_karyawan'];
				$row[] = $return[$h]['nama'];
				$row[] = $return[$h]['nama_unit'];
				$row[] = $return[$h]['jml'];
				
				$data[] = $row;
			}*/
			

			for ($h=0; $h<count($return); $h++) {

					$excel->getActiveSheet()->setCellValueExplicit('A'.$awal, $no++, PHPExcel_Cell_DataType::TYPE_STRING);
		            $excel->getActiveSheet()->setCellValueExplicit('B'.$awal, strtoupper($return[$h]['np_karyawan']), PHPExcel_Cell_DataType::TYPE_STRING);
		            $excel->getActiveSheet()->setCellValueExplicit('C'.$awal, ucwords($return[$h]['nama']), PHPExcel_Cell_DataType::TYPE_STRING);
		            $excel->getActiveSheet()->setCellValueExplicit('D'.$awal, ucwords($return[$h]['nama_unit']), PHPExcel_Cell_DataType::TYPE_STRING);
		            $excel->getActiveSheet()->setCellValueExplicit('E'.$awal, $return[$h]['jml'], PHPExcel_Cell_DataType::TYPE_STRING);
		            /*$excel->getActiveSheet()->getStyle('H'.$awal)->getAlignment()->setWrapText(true);
		            $excel->getActiveSheet()->setCellValueExplicit('I'.$awal, strip_tags($keterangan), PHPExcel_Cell_DataType::TYPE_STRING);
		            $excel->getActiveSheet()->getStyle('I'.$awal)->getAlignment()->setWrapText(true);*/
		            $awal += 1;
			}
			
	        $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
	        $objWriter->setIncludeCharts(TRUE);
	        $objWriter->setPreCalculateFormulas(TRUE);
	        PHPExcel_Calculation::getInstance($excel)->clearCalculationCache();
	        $objWriter->save('php://output');
	        exit();
		}
        
        // heru PDS tambahkan ini, 2021-01-11
        function getUnitByPeriode(){
            $select = '<option value="00000">00000 - Perusahaan Umum Percetakan Uang Republik Indonesia</option>';
            $insert_year = $this->input->post('insert_year',true);
            
            $table = $insert_year==date('Y')?'erp_master_data_'.date('Y_m'):'erp_master_data_'.$insert_year.'_12';
            
            $union = $this->db->distinct()
                ->select("kode_unit,nama_unit")
                ->order_by('kode_unit')
                ->get($table)
                ->result();
            foreach($union as $row){
                $selected = $row->kode_unit==$this->session->userdata("kode_unit") ? 'selected':'';
                $select .= '<option value="'.$row->kode_unit.'" '.$selected.'>'.$row->kode_unit.' - '.$row->nama_unit.'</option>';
            }
            echo $select;
        }
        
        public function daftar_karyawan_revisi(){
			$kode_unit = $_POST["unit_kerja"];
            $insert_year = $this->input->post('insert_year',true);
            
            $table = $insert_year==date('Y')?'erp_master_data_'.date('Y_m'):'erp_master_data_'.$insert_year.'_12';
            
			$hasil["np_pengguna"] = $this->session->userdata("no_pokok");
            $this->db->select('np_karyawan as no_pokok, nama');
            if($kode_unit!='00000')
                $this->db->where('kode_unit',$kode_unit);
            $get = $this->db->group_by('np_karyawan')->get($table);
			$hasil["karyawan"] = $get->result_array();
			echo json_encode($hasil);
		}
        // END heru PDS tambahkan ini, 2021-01-11
	}
	
	/* End of file data_keterlambatan.php */
	/* Location: ./application/controllers/informasi/data_pamlek.php */