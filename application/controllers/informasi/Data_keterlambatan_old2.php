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
						
			$this->load->model($this->folder_model."m_data_keterlambatan");
		
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
			
			$np = $this->input->get('np');
			$kode_unit = $this->input->get('kode_unit');
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."data_keterlambatan";
			
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

				// dd($this->data);
			
				$this->data["arr_periode"]=periode();
				$this->data["np"]=$np;
				$this->data["kode_unit"]=$kode_unit;
				
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
			$periode_awal = $this->input->post("periode_awal");
			$periode_akhir = $this->input->post("periode_akhir");
			$keterangan = $this->input->post("keterangan");
			// $periode_akhir = '2020-09-30';
			// $periode_awal = '2020-01-01';

			if($_POST['length'] != ""){
				$awal = $_POST['start'];
				$akhir = $_POST['length'];
			} else {
				$awal = 0;
				$akhir = count($list);
			}

			if ($periode_awal=="" && $periode_akhir=="") {
				$periode = date('Y_m');
				// $periode = '2020_09';
				$list[0] = $this->m_data_keterlambatan->get_datatable_keterlambatan($kode_unit,$np_karyawan,$periode,$keterangan);
				$recordsFilteredList[0] = $this->m_data_keterlambatan->count_filtered($kode_unit,$np_karyawan,$periode,$keterangan);
				$recordsTotalList[0] = $this->m_data_keterlambatan->count_all($kode_unit,$np_karyawan,$periode,$keterangan);

			} else {
				$tahun_awal = date('Y', strtotime($periode_awal));
				$bulan_awal = date('n', strtotime($periode_awal));
				$tahun_akhir = date('Y', strtotime($periode_akhir));
				$bulan_akhir = date('n', strtotime($periode_akhir));
				$bulan = array('00', '01','02','03','04','05','06','07','08','09','10','11','12');

				$no=0;
				if ($tahun_awal==$tahun_akhir) {
					for($i=($bulan_awal); $i<=$bulan_akhir; $i++) {
						$periode = $tahun_awal."_".$bulan[$i];
						
						$list[$no] = $this->m_data_keterlambatan->get_datatable_keterlambatan($kode_unit,$np_karyawan,$periode,$keterangan);
						$recordsFilteredList[$no] = $this->m_data_keterlambatan->count_filtered($kode_unit,$np_karyawan,$periode,$keterangan);
						$no++;
					}
				} else {
					for($t=($tahun_awal); $t<=$tahun_akhir; $t++) {
						if ($t==$tahun_akhir) {
							for($i=1; $i<=$bulan_akhir; $i++) {
								$periode = $t."_".$bulan[$i];
								
								$list[$no] = $this->m_data_keterlambatan->get_datatable_keterlambatan($kode_unit,$np_karyawan,$periode,$keterangan);
								$recordsFilteredList[$no] = $this->m_data_keterlambatan->count_filtered($kode_unit,$np_karyawan,$periode,$keterangan);
								$no++;
							}
						} else if($t==$tahun_awal) {
							for($i=($bulan_awal); $i<=12; $i++) {
								$periode = $t."_".$bulan[$i];
								
								$list[$no] = $this->m_data_keterlambatan->get_datatable_keterlambatan($kode_unit,$np_karyawan,$periode,$keterangan);
								$recordsFilteredList[$no] = $this->m_data_keterlambatan->count_filtered($kode_unit,$np_karyawan,$periode,$keterangan);
								$no++;
							}
						} else {
							for($i=1; $i<=12; $i++) {
								$periode = $t."_".$bulan[$i];
								
								$list[$no] = $this->m_data_keterlambatan->get_datatable_keterlambatan($kode_unit,$np_karyawan,$periode,$keterangan);
								$recordsFilteredList[$no] = $this->m_data_keterlambatan->count_filtered($kode_unit,$np_karyawan,$periode,$keterangan);
								$no++;
							}
						}
					}
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
					//MACHINE ID
					$machine_id_1 = '';
					if($tampil->tapping_fix_1==null || $tampil->tapping_fix_1=='') {
						if($tampil->tapping_time_1)
							$machine_id_1 = "<br>Machine id : ".$tampil->tapping_terminal_1;
					} else {
						if($tampil->tapping_fix_1) {
							if(substr($tampil->tapping_time_1,0,16) != substr($tampil->tapping_fix_1,0,16)) //dirubah oleh ess
								$machine_id_1 = "<br>Machine id : ".'ESS';					
							else //tidak dirubah
								$machine_id_1 = "<br>Machine id : ".$tampil->tapping_terminal_1;	
						}			
					}
					$row[] = '<b>'.tanggal_waktu($tampil->datang).'</b>'.$machine_id_1;
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
						"data" => array_slice($data, $awal, $akhir)
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
			$keterangan = $this->input->post("keterangan");

			if ($periode_awal=="" && $periode_akhir=="") {
				$periode = date('Y_m');
				$list[0] = $this->m_data_keterlambatan->export_data_keterlambatan($kode_unit,$np_karyawan,$periode,$keterangan,'export');

			} else if (date('Y', strtotime($periode_awal))==date('Y', strtotime($periode_akhir))) {
				$tahun_awal = date('Y', strtotime($periode_awal));
				$bulan_awal = date('n', strtotime($periode_awal));
				$tahun_akhir = date('Y', strtotime($periode_akhir));
				$bulan_akhir = date('n', strtotime($periode_akhir));
				$bulan = array('00', '01','02','03','04','05','06','07','08','09','10','11','12');

				$no=0;
				for($i=($bulan_awal); $i<=$bulan_akhir; $i++) {
					$periode = $tahun_awal."_".$bulan[$i];
					$list[$no] = $this->m_data_keterlambatan->export_data_keterlambatan($kode_unit,$np_karyawan,$periode,$keterangan,'export');
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
			$excel->getActiveSheet()->setCellValueExplicit('A2', 'Periode '.substr(tanggal($periode_awal), 2).' s/d '.substr(tanggal($periode_akhir), 2), PHPExcel_Cell_DataType::TYPE_STRING);

	        $kolom 	= 2;
	        $awal 	= 5;
	        $no = 1;
	        
			for($b=0; $b<count($list); $b++) {
				foreach ($list[$b] as $tampil) {
					//MACHINE ID
					$machine_id_1 = '';
					if($tampil->tapping_fix_1==null || $tampil->tapping_fix_1=='') {
						if($tampil->tapping_time_1)
							$machine_id_1 = "\nMachine id : ".$tampil->tapping_terminal_1;
					} else {
						if($tampil->tapping_fix_1) {
							if(substr($tampil->tapping_time_1,0,16) != substr($tampil->tapping_fix_1,0,16)) //dirubah oleh ess
								$machine_id_1 = "\nMachine id : ".'ESS';					
							else //tidak dirubah
								$machine_id_1 = "\nMachine id : ".$tampil->tapping_terminal_1;	
						}			
					}
					//KETERANGAN
					$arr_keterangan = explode("|",$tampil->keterangan);
					$keterangan = "";
					for($i=0;$i<count($arr_keterangan);$i++){
						if(!empty($arr_keterangan[$i+1])){
							$keterangan.="\n";
						}
						$keterangan.=$arr_keterangan[$i];
					}

					$excel->getActiveSheet()->setCellValueExplicit('A'.$awal, $no++, PHPExcel_Cell_DataType::TYPE_STRING);
		            $excel->getActiveSheet()->setCellValueExplicit('B'.$awal, strtoupper($tampil->np_karyawan), PHPExcel_Cell_DataType::TYPE_STRING);
		            $excel->getActiveSheet()->setCellValueExplicit('C'.$awal, ucwords($tampil->nama), PHPExcel_Cell_DataType::TYPE_STRING);
		            $excel->getActiveSheet()->setCellValueExplicit('D'.$awal, ucwords($tampil->nama_unit), PHPExcel_Cell_DataType::TYPE_STRING);
		            $excel->getActiveSheet()->setCellValueExplicit('E'.$awal, $tampil->tanggal, PHPExcel_Cell_DataType::TYPE_STRING);
		            $excel->getActiveSheet()->setCellValueExplicit('F'.$awal, $tampil->jadwal, PHPExcel_Cell_DataType::TYPE_STRING);
		            $excel->getActiveSheet()->setCellValueExplicit('G'.$awal, $tampil->jadwal_masuk, PHPExcel_Cell_DataType::TYPE_STRING);
		            $excel->getActiveSheet()->setCellValueExplicit('H'.$awal, $tampil->datang.$machine_id_1, PHPExcel_Cell_DataType::TYPE_STRING);
		            $excel->getActiveSheet()->getStyle('H'.$awal)->getAlignment()->setWrapText(true);
		            $excel->getActiveSheet()->setCellValueExplicit('I'.$awal, strip_tags($keterangan), PHPExcel_Cell_DataType::TYPE_STRING);
		            $excel->getActiveSheet()->getStyle('I'.$awal)->getAlignment()->setWrapText(true);
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
        
        // heru PDS tambahkan ini, 2021-01-09
        function getUnitByPeriode(){
            $select = '<option value="00000">00000 - Perusahaan Umum Percetakan Uang Republik Indonesia</option>';
            $insert_date_awal = $this->input->post('insert_date_awal',true);
            $insert_date_akhir = $this->input->post('insert_date_akhir',true);
            
            $insert_date_awal_convert = str_replace('-','_',$insert_date_awal);
            $insert_date_akhir_convert = str_replace('-','_',$insert_date_akhir);
            
            $table_awal = (check_table_exist("erp_master_data_$insert_date_awal_convert")=='ada' ? "erp_master_data_$insert_date_awal_convert":"erp_master_data_".date('Y_m'));
            $table_akhir = (check_table_exist("erp_master_data_$insert_date_akhir_convert")=='ada' ? "erp_master_data_$insert_date_akhir_convert":"erp_master_data_".date('Y_m'));
            
            $union = $this->db
                ->query("SELECT * 
                        FROM
                        (SELECT DISTINCT kode_unit,nama_unit FROM $table_awal
                        UNION
                        SELECT DISTINCT kode_unit,nama_unit FROM $table_akhir) a
                        ORDER BY kode_unit")
                ->result();
            foreach($union as $row){
                $selected = $row->kode_unit==$this->session->userdata("kode_unit") ? 'selected':'';
                $select .= '<option value="'.$row->kode_unit.'" '.$selected.'>'.$row->kode_unit.' - '.$row->nama_unit.'</option>';
            }
            echo $select;
        }
        // END heru PDS tambahkan ini, 2021-01-09
        
        // heru PDS tambahkan ini, 2021-01-11
        function daftar_karyawan_revisi(){
            $insert_date_awal = $this->input->post('insert_date_awal',true);
            $insert_date_akhir = $this->input->post('insert_date_akhir',true);
            $unit_kerja = $this->input->post('unit_kerja',true);
            
            // $tableFix = 'mst_karyawan';
            
            if( $insert_date_awal==$insert_date_akhir ){
                $date_fix = $insert_date_akhir;
                if( date('Y-m',strtotime($date_fix))>=date('Y-m') )
                    $tableFix = 'erp_master_data_'.date('Y_m');
                else
                    $tableFix = 'erp_master_data_'.date('Y_m',strtotime($date_fix));
            } else{
                if( date('Y',strtotime($insert_date_awal))==date('Y',strtotime($insert_date_akhir)) ){
                    if( date('Y-m',strtotime($insert_date_akhir))>=date('Y-m',strtotime($insert_date_awal)) ){
                        $tableFix = 'erp_master_data_'.date('Y_m',strtotime($insert_date_akhir));
                        $bulan = $insert_date_akhir;
                    } else{
                        $tableFix = 'erp_master_data_'.date('Y_m',strtotime($insert_date_awal));
                        $bulan = $insert_date_awal;
                    }
                    
                    if( date('Y-m',strtotime($bulan))>=date('Y-m') )
                        $tableFix = 'erp_master_data_'.date('Y_m');
                } else{
                    if( date('Y',strtotime($insert_date_akhir))>=date('Y',strtotime($insert_date_awal)) )
                        $tableFix = 'erp_master_data_'.date('Y_m',strtotime($insert_date_akhir));
                    else
                        $tableFix = 'erp_master_data_'.date('Y_m',strtotime($insert_date_awal));
                }
            }
            
            $hasil["np_pengguna"] = $this->session->userdata("no_pokok");
            $this->db->select('np_karyawan as no_pokok, nama');
            if($unit_kerja!='00000')
                $this->db->where('kode_unit',$unit_kerja);
            $get = $this->db->group_by('np_karyawan')->get($tableFix);
			$hasil["karyawan"] = $get->result_array();
			echo json_encode($hasil);
        }
        // END: heru PDS tambahkan ini, 2021-01-11
	}
	
	/* End of file data_keterlambatan.php */
	/* Location: ./application/controllers/informasi/data_pamlek.php */