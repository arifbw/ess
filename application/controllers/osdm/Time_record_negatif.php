<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Time_record_negatif extends CI_Controller {
		public function __construct(){
			parent::__construct();
			
			// Report all errors
            //error_reporting(E_ALL);

            // Display errors in output
            //ini_set('display_errors', 1);
			
			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'osdm/';
			$this->folder_model = 'osdm/';
			$this->folder_controller = 'osdm/';
			
			$this->akses = array();
						
			$this->load->model($this->folder_model."m_time_record_negatif");
		
			$this->data["is_with_sidebar"] = true;
			
			$this->data['judul'] = "Time Record Negatif";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);

			izin($this->akses["akses"]);
		}
		
		public function index(){
			//	echo __FILE__ . __LINE__;die(var_dump($this->akses));
			
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data["content"] = $this->folder_view."time_record_negatif";
			array_push($this->data['js_sources'],"osdm/time_record_negatif");
			
			$this->data["periode_bulan"] = $this->m_time_record_negatif->bulan_periode();
			$banyak_bulan = count($this->data["periode_bulan"]);
			for($i=0;$i<$banyak_bulan;$i++){
				$this->data["periode_bulan"][$i] = substr($this->data["periode_bulan"][$i]["table_name"],9);
			}

			$this->data["tampil_bulan_tahun"]=date("Y-m");
			
			$this->load->view('template',$this->data);
		}
		
		public function daftar_pekan(){
			$tahun_bulan = preg_replace("/_/","-",$_POST["tahun_bulan"]);

			$tanggal_awal = 1;
			$tanggal_akhir = (int)date("t",strtotime($tahun_bulan."-01"));
			
			$arr_periode = array();
			
			for($i=$tanggal_awal;$i<=$tanggal_akhir;$i++){
				
				$periode_awal = date("Y-m-d",strtotime($tahun_bulan."-".str_pad($i,2,"0",STR_PAD_LEFT)));
				$hari_awal = date("w",strtotime($periode_awal));
				if($hari_awal==0){
					$hari_awal=7;
				}
				$i = min($i + 7 - $hari_awal,$tanggal_akhir);

				$periode_akhir = date("Y-m-d",strtotime($tahun_bulan."-".str_pad($i,2,"0",STR_PAD_LEFT)));
				
				if(strcmp($periode_awal,$periode_akhir)==0){
					$periode["text"] = hari_tanggal($periode_awal);
				}
				else{
					$periode["text"] = hari_tanggal($periode_awal)." - ".hari_tanggal($periode_akhir);
				}
				$periode["value"] = $periode_awal."-".$periode_akhir;
				array_push($arr_periode,$periode);
			}
			
			echo json_encode($arr_periode);
		}
		
		public function tampilkan_isian(){
			$periode_awal = substr($_POST["periode"],0,10);
			$periode_akhir = substr($_POST["periode"],11,10);
			
			$tanggal_awal = (int)substr($periode_awal,8,2);
			$tanggal_akhir = (int)substr($periode_akhir,8,2);
			
			$daftar_karyawan_tm_negatif = $this->m_time_record_negatif->daftar_karyawan_tm_negatif($periode_awal,$periode_akhir);
			
			$banyak = count($daftar_karyawan_tm_negatif);
			$arr_karyawan = array();
			
			for($i=0;$i<$banyak;$i++){
				$arr_karyawan[$daftar_karyawan_tm_negatif[$i]["np_karyawan"]]["nama"] = $daftar_karyawan_tm_negatif[$i]["nama"];
				
				if(strcmp($daftar_karyawan_tm_negatif[$i]["dws"],"OFF")==0){
					$arr_karyawan[$daftar_karyawan_tm_negatif[$i]["np_karyawan"]][$daftar_karyawan_tm_negatif[$i]["tanggal_dws"]]["jadwal"] = "Libur";
				}
				else{
					$arr_karyawan[$daftar_karyawan_tm_negatif[$i]["np_karyawan"]][$daftar_karyawan_tm_negatif[$i]["tanggal_dws"]]["jadwal"] = "Masuk";
				}
				
				if((int)$daftar_karyawan_tm_negatif[$i]["wfh"]==1){
					$arr_karyawan[$daftar_karyawan_tm_negatif[$i]["np_karyawan"]][$daftar_karyawan_tm_negatif[$i]["tanggal_dws"]]["wfh"] = "WFH";
				}
				else{
					$arr_karyawan[$daftar_karyawan_tm_negatif[$i]["np_karyawan"]][$daftar_karyawan_tm_negatif[$i]["tanggal_dws"]]["wfh"] = "";
				}
				
				if(empty($daftar_karyawan_tm_negatif[$i]["id_cuti"])){
					$arr_karyawan[$daftar_karyawan_tm_negatif[$i]["np_karyawan"]][$daftar_karyawan_tm_negatif[$i]["tanggal_dws"]]["cuti"] = "";
				}
				else{
					$arr_karyawan[$daftar_karyawan_tm_negatif[$i]["np_karyawan"]][$daftar_karyawan_tm_negatif[$i]["tanggal_dws"]]["cuti"] = "Cuti";
				}
				
				if(empty($daftar_karyawan_tm_negatif[$i]["id_sppd"])){
					$arr_karyawan[$daftar_karyawan_tm_negatif[$i]["np_karyawan"]][$daftar_karyawan_tm_negatif[$i]["tanggal_dws"]]["dinas"] = "";
				}
				else{
					$arr_karyawan[$daftar_karyawan_tm_negatif[$i]["np_karyawan"]][$daftar_karyawan_tm_negatif[$i]["tanggal_dws"]]["dinas"] = "Dinas";
				}
			}
			
			$banyak_karyawan = count($arr_karyawan);
			
			$hasil = "";
			//var_dump($arr_karyawan);
			$hasil .= "<div class='row'>";
				$hasil .= "<table width='100%' class='table table-striped table-bordered table-hover' id='tabel_tm_negatif'>";
					$hasil .= "<thead>";
						$hasil .= "<tr>";
							$hasil .= "<th class='text-center'>No</th>";
							$hasil .= "<th class='text-center'>NP</th>";
							$hasil .= "<th class='text-center'>Nama</th>";
							for($i=$tanggal_awal;$i<=$tanggal_akhir;$i++){
								$hasil .= "<th class='text-center'>".hari_tanggal(substr($periode_awal,0,8).str_pad($i,2,"0",STR_PAD_LEFT))."</th>";
							}
						$hasil .= "</tr>";
					$hasil .= "</thead>";
					$hasil .= "<tbody>";
					for($i=0;$i<$banyak_karyawan;$i++){
						$np = array_keys($arr_karyawan)[$i];
						$hasil .= "<tr>";
							$hasil .= "<td>".($i+1).".</td>";
							$hasil .= "<td>".$np."</td>";
							$hasil .= "<td>".$arr_karyawan[$np]["nama"]."</td>";
								for($j=$tanggal_awal;$j<=$tanggal_akhir;$j++){
									$tanggal = substr($periode_awal,0,8).str_pad($j,2,"0",STR_PAD_LEFT);
									
									$status = "";
									if(isset($arr_karyawan[$np][$tanggal])){
										if(strcmp($arr_karyawan[$np][$tanggal]["jadwal"],"Libur")==0){
											$status = "Libur";
										}
										else if(strcmp($arr_karyawan[$np][$tanggal]["jadwal"],"Cuti")==0){
											$status = "Cuti";
										}
										else if(strcmp($arr_karyawan[$np][$tanggal]["dinas"],"Dinas")==0){
											$status = "Dinas";
										}
										else if(strcmp($arr_karyawan[$np][$tanggal]["wfh"],"WFH")==0){
											$status = "WFH";
										}
										else{
											$status = "Masuk";
										}
									}
									if(in_array($status,array("WFH","Masuk"))){
										
										$tanggal_absen = substr($periode_awal,0,8).str_pad($j,2,"0",STR_PAD_LEFT);
																				
										$dropdown = "<select name='$np/$tanggal_absen' onchange='ubah_wfh(this)')' class='form-control'>";
											$selected = "";
											if(strcmp($status,"Masuk")==0){
												$selected = "selected='selected'";
											}
											$dropdown .= "<option $selected value='Masuk|$np|$tanggal'>Masuk</option>";
											
											$selected = "";
											if(strcmp($status,"WFH")==0){
												$selected = "selected='selected'";
											}
											$dropdown .= "<option $selected value='WFH|$np|$tanggal'>WFH</option>";
										$dropdown .= "</select>";
										
										$status = $dropdown;
									}
									$hasil .= "<td>".$status."</td>";
								}
						$hasil .= "</tr>";
					}
					$hasil .= "</tbody>";
				$hasil .= "</table>";
				//$hasil .= "<!-- /.table-responsive -->";
			$hasil .= "</div>";
			echo json_encode($hasil);
		}
		
		function update()
		{
			$input 						= $this->input->post('vdata_value');
			$np_karyawan 				= $this->input->post('vdata_np');
			$date 						= $this->input->post('vdata_date');
					
			if($input=="WFH")
			{
				$input = '1';
			}
			else
			{
				$input = '0';
			}
			
			$this->m_time_record_negatif->update_wfh($input,$np_karyawan,$date);
				
		}
	}
	
	/* End of file time_record_negatif.php */
	/* Location: ./application/controllers/osdm/time_record_negatif.php */