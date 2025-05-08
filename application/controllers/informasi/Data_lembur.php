<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Data_lembur extends CI_Controller {
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
						
			$this->load->model($this->folder_model."m_data_lembur");
		
			$this->data["is_with_sidebar"] = true;
								
			$this->data['judul'] = "Data Lembur";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			$this->load->model("master_data/m_karyawan");
			$this->load->model("master_data/m_satuan_kerja");
			izin($this->akses["akses"]);
		}
		
		public function index(){
			//	echo __FILE__ . __LINE__;die(var_dump($this->akses));	
			//var_dump($_SESSION);
			//echo substr($this->session->userdata("kode_jabatan"),-2);
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."data_lembur";
			
			if($this->akses["lihat"]){
				array_push($this->data['css_plugin_sources'],"select2/select2.min.css");
				array_push($this->data['js_plugin_sources'],"select2/select2.min.js");

				$this->data["daftar_akses_karyawan"] = array(array("no_pokok"=>"","nama"=>""));
				
				$script = "pilihan_karyawan();pilihan_bulan();";
				
				$this->data["daftar_akses_unit_kerja"] = array(array("kode_unit"=>$this->session->userdata("kode_unit"), "nama_unit"=>$this->session->userdata("nama_unit")));
				
				if($this->akses["lihat semua"]){
					$this->data["daftar_akses_unit_kerja"] = $this->m_satuan_kerja->daftar_satuan_kerja();
				}
				else if(strcmp(substr($this->session->userdata("kode_jabatan"),-2),"00")==0){
					$this->data["daftar_akses_unit_kerja"] = $this->m_satuan_kerja->daftar_satuan_kerja_bawah($this->session->userdata("kode_unit"));
				}
			
				
				$this->data["arr_tahun"]=periode_tahun();//die(var_dump($this->data["arr_periode"]));
				
				$js_header_script = "<script>
								$(document).ready(function() {
									$script
									$('.select2').select2();
								});
							</script>";
				
				array_push($this->data["js_header_script"],$js_header_script);
			}
			
			$this->load->view('template',$this->data);
		}
		
		public function header_nominal_lembur_karyawan(){
			$kode_unit = $this->input->post("kode_unit");
			$np_karyawan = $this->input->post("np_karyawan");
			$bulan = $this->input->post("bulan");
			$tahun = $this->input->post("tahun");
			$akumulasi = $this->input->post("akumulasi");
			
			echo "<input type='hidden' name='log_data_lembur' id='log_data_pamlek' value='no'>";
			echo "<div class='form-group'>";	
				echo "<div class='row'>";
					echo "<table width='100%' class='table table-striped table-bordered table-hover' id='tabel_nominal_lembur_karyawan'>";
						echo "<thead>";
							echo "<tr>";
								echo "<th class='text-center no-sort'>No</th>";
								if(strcmp($akumulasi,"akumulasi karyawan")!=0){
									echo "<th class='text-center no-sort'>Nama Pembayaran</th>";
								}
								if(empty($np_karyawan) and strcmp($akumulasi,"akumulasi bulan")!=0){
									echo "<th class='text-center no-sort'>Karyawan</th>";
									echo "<th class='text-center no-sort'>Unit Kerja</th>";
								}
								echo "<th class='text-center no-sort'>Uang Lembur</th>";
								echo "<th class='text-center no-sort'>Persentase</th>";
							echo "</tr>";
						echo "</thead>";
						echo "<tbody>";
						echo "</tbody>";
					echo "</table>";
					echo "<!-- /.table-responsive -->";
				echo "</div>";
			echo "</div>";
		}
		
		public function header_peringkat_lembur(){
			$kode_unit = $this->input->post("kode_unit");
			$np_karyawan = $this->input->post("np_karyawan");
			$bulan = $this->input->post("bulan");
			$tahun = $this->input->post("tahun");
			$tab = $this->input->post("tab");
			$akumulasi = $this->input->post("akumulasi");
			
			$count_peringkat = $this->m_satuan_kerja->max_level_satuan_kerja_bawah($kode_unit)+1;
			//var_dump($_POST);
			echo "<input type='hidden' name='log_data_lembur' id='log_data_pamlek' value='no'>";
			echo "<div class='form-group'>";	
				echo "<div class='row'>";
					echo "<table width='100%' class='table table-striped table-bordered table-hover' id='tabel_peringkat_lembur_karyawan'>";
						echo "<thead>";
							echo "<tr>";
								if(strcmp($akumulasi,"akumulasi karyawan")!=0){
									echo "<th class='text-center no-sort' rowspan='2'>Nama Pembayaran</th>";
								}
								if(empty($np_karyawan) and strcmp($akumulasi,"akumulasi bulan")!=0){
									echo "<th class='text-center no-sort' rowspan='2'>Karyawan</th>";
								}
								echo "<th class='text-center no-sort' rowspan='2'>Unit Kerja</th>";
								echo "<th class='text-center no-sort' colspan='$count_peringkat'>".ucwords($tab)." pada</th>";
							echo "</tr>";
							echo "<tr>";
								if(empty($np_karyawan) and strcmp($akumulasi,"akumulasi bulan")==0){
									if($count_peringkat>=6){
										echo "<th class='text-center no-sort'>Seksi</th>";
									}
									if($count_peringkat>=5){
										echo "<th class='text-center no-sort'>Departemen</th>";
									}
									if($count_peringkat>=4){
										echo "<th class='text-center no-sort'>Divisi</th>";
									}
									if($count_peringkat>=3){
										echo "<th class='text-center no-sort'>Direktorat</th>";
									}
								}
								else{
									if($count_peringkat>=6){
										echo "<th class='text-center no-sort'>Unit</th>";
									}
									if($count_peringkat>=5){
										echo "<th class='text-center no-sort'>Seksi</th>";
									}
									if($count_peringkat>=4){
										echo "<th class='text-center no-sort'>Departemen</th>";
									}
									if($count_peringkat>=3){
										echo "<th class='text-center no-sort'>Divisi</th>";
									}
									if($count_peringkat>=2){
										echo "<th class='text-center no-sort'>Direktorat</th>";
									}
								}
								echo "<th class='text-center no-sort'>Perusahaan</th>";
							echo "</tr>";
						echo "</thead>";
						echo "<tbody>";
						echo "</tbody>";
					echo "</table>";
					echo "<!-- /.table-responsive -->";
				echo "</div>";
			echo "</div>";
		}
		
		public function nominal_lembur_karyawan(){//var_dump($_POST);die();
			$kode_unit = $this->input->post("kode_unit");
			$np_karyawan = $this->input->post("np_karyawan");
			$bulan = $this->input->post("bulan");
			$tahun = $this->input->post("tahun");
			$akumulasi = $this->input->post("akumulasi");
			
			$list = $this->m_data_lembur->get_datatable_nominal_lembur_karyawan($kode_unit,$np_karyawan,$tahun,$bulan,$akumulasi);
			$data = array();
			$no = $_POST['start'];

			foreach ($list as $tampil) {
				$no++;
				$row = array();
				$row[] = $no.".";
				if(strcmp($akumulasi,"akumulasi karyawan")!=0){
					$row[] = substr($tampil->nama_payslip,5);
				}
				if(empty($np_karyawan) and strcmp($akumulasi,"akumulasi bulan")!=0){
					$row[] = $tampil->np_karyawan." - ".$tampil->nama;
					$row[] = $tampil->kode_unit." - ".$tampil->nama_unit;
				}
				//$row[] = number_format($tampil->gapok,0,"",".");
				$row[] = number_format($tampil->lembur,0,"",".");
				$row[] = number_format($tampil->persentase,2,",",".")." %";
				
				$data[] = $row;
			}

			$recordsFiltered = $this->m_data_lembur->count_filtered_nominal($kode_unit,$np_karyawan,$tahun,$bulan,$akumulasi);
			$recordsTotal = $this->m_data_lembur->count_all_nominal();

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
						"deskripsi" => "lihat data nominal lembur <br>Kode unit kerja : ".$kode_unit."<br>NP Karyawan : ".$np_karyawan."<br>Tahun : ".$tahun."<br>Bulan : ".$bulan,
						"alamat_ip" => $this->data["ip_address"],
						"waktu" => date("Y-m-d H:i:s")
					);
				$this->m_log->tambah($log);
		}
		
		public function peringkat_lembur_karyawan(){//var_dump($_POST);die();
			$kode_unit = $this->input->post("kode_unit");
			$np_karyawan = $this->input->post("np_karyawan");
			$bulan = $this->input->post("bulan");
			$tahun = $this->input->post("tahun");
			$tab = $this->input->post("tab");
			$akumulasi = $this->input->post("akumulasi");
			
			$count_peringkat = $this->m_satuan_kerja->max_level_satuan_kerja_bawah($kode_unit)+1;
			
			$list = $this->m_data_lembur->get_datatable_peringkat_lembur_karyawan($kode_unit,$np_karyawan,$tahun,$bulan,$akumulasi,$tab);
			$data = array();
			$total_gapok = 0;
			$total_lembur = 0;
			foreach ($list as $tampil) {
				$row = array();
				if(strcmp($akumulasi,"akumulasi karyawan")!=0){
					$row[] = substr($tampil->nama_payslip,5);
				}
				if(empty($np_karyawan) and strcmp($akumulasi,"akumulasi bulan")!=0){
					$row[] = $tampil->np_karyawan." - ".$tampil->nama;
				}
				$row[] = $tampil->kode_unit." - ".$tampil->nama_unit;
				
				if(empty($np_karyawan) and strcmp($akumulasi,"akumulasi bulan")==0){
					$level = (int)substr($tampil->level,0,1);
					if($count_peringkat>=6){
						if(strcmp(substr($tampil->kode_unit,3,1),"0")!=0 and $level>=5){
							$row[] = $tampil->rank_seksi;
						}
						else{
							$row[] = "";
						}
					}
					
					if($count_peringkat>=5){
						if(strcmp(substr($tampil->kode_unit,2,1),"0")!=0 and $level>=4){
							$row[] = $tampil->rank_departemen;
						}
						else{
							$row[] = "";
						}
					}
					
					if($count_peringkat>=4){
						if(strcmp(substr($tampil->kode_unit,1,1),"0")!=0 and $level>=3){
							$row[] = $tampil->rank_divisi;
						}
						else{
							$row[] = "";
						}
					}
					
					if($count_peringkat>=3){
						if(strcmp(substr($tampil->kode_unit,0,1),"0")!=0 and $level>=2){
							$row[] = $tampil->rank_direktorat;
						}
						else{
							$row[] = "";
						}
					}
				}
				else{
					if($count_peringkat>=6){
						if(strcmp(substr($tampil->kode_unit,4,1),"0")!=0){
							$row[] = $tampil->rank_unit;
						}
						else{
							$row[] = "";
						}
					}
					
					if($count_peringkat>=5){
						if(strcmp(substr($tampil->kode_unit,3,1),"0")!=0){
							$row[] = $tampil->rank_seksi;
						}
						else{
							$row[] = "";
						}
					}
					
					if($count_peringkat>=4){
						if(strcmp(substr($tampil->kode_unit,2,1),"0")!=0){
							$row[] = $tampil->rank_departemen;
						}
						else{
							$row[] = "";
						}
					}
					
					if($count_peringkat>=3){
						if(strcmp(substr($tampil->kode_unit,1,1),"0")!=0){
							$row[] = $tampil->rank_divisi;
						}
						else{
							$row[] = "";
						}
					}
					
					if($count_peringkat>=2){
						if(strcmp(substr($tampil->kode_unit,0,1),"0")!=0){
							$row[] = $tampil->rank_direktorat;
						}
						else{
							$row[] = "";
						}
					}
				}
				
				$row[] = $tampil->rank_perusahaan;
				
				$data[] = $row;
			}

			$recordsFiltered = $this->m_data_lembur->count_filtered_peringkat($kode_unit,$np_karyawan,$tahun,$bulan,$akumulasi,$tab);
			$recordsTotal = $this->m_data_lembur->count_all_peringkat();

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
						"deskripsi" => "lihat data peringkat nominal lembur <br>Kode unit kerja : ".$kode_unit."<br>NP Karyawan : ".$np_karyawan."<br>Tahun : ".$tahun."<br>Bulan : ".$bulan,
						"alamat_ip" => $this->data["ip_address"],
						"waktu" => date("Y-m-d H:i:s")
					);
				$this->m_log->tambah($log);
		}
		
		public function daftar_karyawan(){
			$kode_unit = $_POST["unit_kerja"];
			$hasil["np_pengguna"] = $this->session->userdata("no_pokok");
			if(strcmp(substr($this->session->userdata("kode_jabatan"),-2),"00")==0 or $this->akses["lihat semua"]){
				$hasil["karyawan"] = $this->m_karyawan->get_karyawan_unit_kerja($kode_unit);
			}
			else{
				$hasil["karyawan"] = array(array("no_pokok"=>$this->session->userdata("no_pokok"), "nama"=>$this->session->userdata("nama")));
			}
			echo json_encode($hasil);
		}
		
		public function pilih_tahun(){
			$tahun = $_POST["tahun"];
			$hasil = $this->m_data_lembur->get_bulan_gaji($tahun);
			
			$arr_bulan=array("","Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember");
			
			for($i=0;$i<count($hasil);$i++){
				$hasil[$i]["nama_bulan"] = $arr_bulan[(int)$hasil[$i]["bulan"]];
			}
			echo json_encode($hasil);
		}
	}
	
	/* End of file data_lembur.php */
	/* Location: ./application/controllers/informasi/data_lembur.php */
	